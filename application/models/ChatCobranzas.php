<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\PusherException;

defined('BASEPATH') OR exit('No direct script access allowed');

class ChatCobranzas extends Orm_model
{
    public $provider;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('dispatcher');
        $this->tableName = 'new_chats';
        $this->columns   = [
            'id',
            'id_operador',
            'from',
            'nombres',
            'apellidos',
            'documento',
            'email',
            'to',
            'medio',
            'proveedor',
            'fecha_inicio',
            'iniciado_por',
            'type',
            'sin_leer',
            'abierto',
            'fecha_ultima_recepcion',
            'fecha_ultimo_envio',
            'ultimo_mensaje',
            'status_chat',
            'account_sid'
        ];
        $this->dbName    = 'chat';
        $this->db        = $this->load->database(
            'chat',
            true
        );
		$this->db_solicitudes = $this->load->database('solicitudes',TRUE);
    }

    /**
     * Columns in the table are:
     * id
     * id_operador
     * from
     * nombres
     * apellidos
     * documento
     * email
     * to
     * medio
     * proveedor
     * fecha_inicio
     * iniciado_por
     * type
     * sin_leer
     * abierto
     * fecha_ultima_recepcion
     * fecha_ultimo_envio
     * ultimo_mensaje
     * status_chat
     * account_sid
     */

    /**
     * Create or Update a new record in the DB
     *
     * @param stdClass $object
     * @param bool $regMsg
     *
     * @param bool|null $returnID
     * @return mixed
     * @throws GuzzleException
     * @throws PusherException
     */
    public function modelDbAction(
        stdClass $object,
        bool $regMsg = true,
        bool $returnID = null
    )
    {
        /**
         * Fecha de inicio. Puede estar asignada al momento de la creación y si no lo está, la crea.
         */
        $startDateDB = isset($object->timestamp)
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i:s\Z',
                $object->timestamp
            )
                    ->format('Y-m-d H:i:s')
            : Carbon::now()
                    ->format('Y-m-d H:i:s');
        $type        = 'general';
        /**
         * Obtener los números de teléfono en el formato adecuado.
         */
        $fromNumber  = TwilioCobranzas::getMobileNumber($object->From);
        $toNumber    = TwilioCobranzas::getMobileNumber($object->To);

        /**
         * Carga el modelo de Solicitud
         * application/models/SolicitudWS.php
         */
        $this->load->model('solicitudWS', '', true);
        /** @var SolicitudWS $requestModel */
        $requestModel    = $this->solicitudWS;
        /**
         * Buscar una solicitud asociada al telefono del cliente
         */
        $solicitudEntity = $requestModel->findRequestByTelefono($fromNumber);

        if ($solicitudEntity) {
            $type = 'solicitante';
        }

        /**
         * Al enviar un template de iniciar conversación se establece previamente el id del agente para levantar
         * un nuevo chat.
         *
         * De otra manera, se creará con el agente en Null
         */
        if (isset($object->agent_id)) {
            $this->load->model(
                'operatorRelation',
                '',
                true
            );
            /** @var OperatorRelation $relationModel */
            $mobileNumber  = TwilioCobranzas::getMobileNumber($object->From);
            $relationModel = $this->operatorRelation;
            $relation      = $relationModel->findByMobile($mobileNumber);

            if ($relation === null) {
                $data = [
                    'id_agente'   => $object->agent_id,
                    'num_celular' => $mobileNumber
                ];
                $relationModel->dbCreate($data);
            }
        }

        /**
         * Data suministrada para la creación del nuevo chat.
         */
        $data = [
            'id_operador'            => $object->agent_id ?? 192,
            'from'                   => $fromNumber,
            'nombres'                => null,
            'apellidos'              => null,
            'documento'              => null,
            'email'                  => null,
            'to'                     => $toNumber,
            'medio'                  => 'WhatsApp',
            'proveedor'              => $this->provider ?? -1,
            'fecha_inicio'           => $startDateDB,
            'iniciado_por'           => $object->started_by ?? 'visitor',
            'type'                   => $type,
            'sin_leer'               => $object->sin_leer ?? 1,
            'abierto'                => 0,
            'fecha_ultima_recepcion' => $object->fecha_ultima_recepcion ?? $startDateDB,
            'fecha_ultimo_envio'     => $object->fecha_ultimo_envio ?? null,
            'ultimo_mensaje'         => strlen($object->Body) > 150
                ? substr($object->Body, 0, 147) . '...' : $object->Body,
            'status_chat'            => $object->status_chat ?? 'activo',
            'account_sid'            => $object->AccountSid
        ];

        if ($solicitudEntity) {
            $data['nombres']   = $solicitudEntity['nombres'];
            $data['apellidos'] = $solicitudEntity['apellidos'];
            $data['documento'] = $solicitudEntity['documento'];
            $data['email']     = $solicitudEntity['email'];
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert(
            'chat.' . $this->tableName,
            $data
        );

        $lastID = $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        if (isset($lastID)) {
            /**
             * Normalmente el sistema creará un registro por nuevo mensaje recibido pues $regMsg por defecto es TRUE,
             * para que no lo haga, debe llamarse a esta función con FALSE en $regMsg
             */
            if ($regMsg) {
                $this->registerReceivedMessage(
                    ['id' => $lastID],
                    $object,
                    true
                );
            }

            /**
             * Crea un registro de cambio de status en la tabla auxiliar de chat.relacion_estado_chat
             */
            $this->updateRelationStateChat($data['status_chat'], $lastID);

            /**
             * $returnID por defecto es NULL, si se establece en TRUE, la función retornará el último ID creado en
             * el sistema
             */
            if ($returnID) {
                return $lastID;
            }
        }

        return true;
    }

    /**
     * Actualizada un chat con la data suministrada de acuerdo al ID suministrado
     *
     * @param $id
     * @param $data
     * @return bool
     */
    public function doUpdate(
        $id,
        $data
    ): bool
    {
        if (isset($data['ultimo_mensaje'])) {
            $data['ultimo_mensaje'] = strlen($data['ultimo_mensaje']) > 150
                ? substr($data['ultimo_mensaje'], 0, 147) . '...' : $data['ultimo_mensaje'];
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->update(
            'chat.' . $this->tableName,
            $data,
            ['id' => $id]
        );

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    //Specific Tools

    /**
     * @param stdClass $object
     *
     * @return array|null
     * @throws GuzzleException
     */
    private function getVisitorObject(stdClass $object): ?array
    {
        $this->load->model(
            'visitor',
            '',
            true
        );
        /** @var Visitor $model */
        $model = $this->visitor;
        $model->setProvider($this->provider);

        if (isset($object->visitor)) {
            if ($this->provider) {
                $visitor         = (object)$model->getVisitorFromProviderApi($object->visitor->id);
                $object->visitor = $model->normalizeObject($visitor);
                $visitor         = $model->findBy(
                    $object->visitor->id,
                    $object->visitor
                );

                return $visitor ? ['id' => $visitor->id, 'visitor' => $visitor] : null;
            }

            $visitor = $model->findBy(
                $object->visitor->id,
                $object->visitor,
                true
            );

            return $visitor ? ['id' => $visitor->id, 'visitor' => $visitor] : null;
        }

        //TODO optimize
        $visitor         = $model->findByNumber($object->From);
        $requestByNumber = $model->findInRequest($object->From);

        if ($visitor === null) {
            //NewVisitor
            $visitorToDB = $model->normalizeObject($object);
            if ($requestByNumber !== null) {
                $visitorToDB = $model->relateRequestData(
                    $requestByNumber,
                    $visitorToDB
                );
            }
            //Check Visitors on DB
            if ($visitorToDB !== null) {
                return [
                    'id'         => $model->modelDbAction(
                        $visitorToDB,
                        false,
                        true
                    ), 'visitor' => $visitorToDB
                ];
            }
        } else {
            if ($requestByNumber !== null) {
                $carbonDate = null;

                if ($requestByNumber->fecha_expedicion !== null && !empty($requestByNumber->fecha_expedicion) && $requestByNumber->fecha_expedicion !== '0000-00-00') {
                    $carbonDate = Carbon::createFromFormat(
                        'Y-m-d',
                        $requestByNumber->fecha_expedicion
                    )
                                        ->format('Y-m-d H:i:s');
                }

                $model->updateByID(
                    $visitor->id,
                    [
                        'solicitud_id' => $requestByNumber->id,
                        'nombres'      => $requestByNumber->nombres,
                        'apellidos'    => $requestByNumber->apellidos,
                        'cedula'       => $requestByNumber->documento,
                        'fexpedicion'  => $carbonDate,
                        'tipo_doc'     => $requestByNumber->id_tipo_documento,
                        'telefono'     => $requestByNumber->telefono,
                        'email'        => $requestByNumber->email
                    ]
                );
            }

            return ['id' => $visitor->id, 'visitor' => $model->findByNumber($object->From)];
        }

        return null;
    }

    /**
     * @param stdClass $object
     *
     * @return stdClass|null
     */
    private function getSessionID(stdClass $object): ?\stdClass
    {
        if (isset($object->session)) {
            $this->load->model(
                'session',
                '',
                true
            );
            /** @var Session $model */
            $model = $this->session;
            $model->setProvider($this->provider);
            $object->session = $model->normalizeObject($object->session);

            if ($this->provider) {
                return $model->findBy(
                    $object->session->id,
                    $object->session
                );
            }

            return $model->findBy(
                $object->session->id,
                $object->session,
                true
            );
        }

        return null;
    }

    /**
     * @param stdClass $object
     *
     * @return stdClass|null
     */
    private function getCustomerID(stdClass $object): ?\stdClass
    {
        if ($object->id_cliente === null) {
            return null;
        }
//        $this->load->model('session', '', true);
//        /** @var Session $model */
//        $model = $this->session;
//        $model->setProvider($this->provider);
//        $object->session = $model->normalizeObject($object->session);
//
//        if ($this->provider) {
//            return $model->findBy($object->session->id, $object->session);
//        }
//
//        return $model->findBy($object->session->id, $object->session, true);
    }

    /**
     * @param array $chatEntity
     * @param stdClass $receivedMessage
     * @param bool $chatCreation
     * @throws GuzzleException
     * @throws PusherException
     */
    public function registerReceivedMessage(array $chatEntity, stdClass $receivedMessage,
                                            bool $chatCreation = false): void
    {
        /**
         * Registrar el nuevo mensaje en la DB
         */
        $this->load->model('receivewhatsapp', '', true);
        /** @var Receivewhatsapp $modelReceiveWhatsApp */
        $modelReceiveWhatsApp = $this->receivewhatsapp;
        $modelReceiveWhatsApp->setProvider($this->provider);

        $creationDate = $modelReceiveWhatsApp->modelCreateDb(
            $receivedMessage,
            $chatEntity['id'],
            true
        );

        /**
         * Actualizar la entidad Chat en los datos del último mensaje recibido.
         */
        if (!is_bool($creationDate) && $chatCreation === false) {
            $this->updateChatEntity($chatEntity, $receivedMessage, $creationDate);

            newMessageNotification(
                $receivedMessage,
                $chatEntity['id'],
                $chatEntity['id_operador']
            );
        }
    }

    /**
     * @param int $chatID
     * @param stdClass $receivedMessage
     * @throws GuzzleException
     */
    public function registerReceivedMessageSimple(int $chatID, stdClass $receivedMessage): void
    {
        //1. Registrar el nuevo mensaje en la DB
        $this->load->model('receivewhatsapp', '', true);
        /** @var Receivewhatsapp $modelReceiveWhatsApp */
        $modelReceiveWhatsApp = $this->receivewhatsapp;
        $modelReceiveWhatsApp->setProvider($this->provider);

        $creationDate = $modelReceiveWhatsApp->modelCreateDb(
            $receivedMessage,
            $chatID,
            true,
            false
        );
        $this->updateChatEntitySimple($chatID, $receivedMessage, $creationDate);
    }

    /**
     * @param array $chatEntity
     * @param stdClass $newMessage
     * @param string $lastReceivedDate
     * @return bool
     */
    private function updateChatEntity(array $chatEntity, stdClass $newMessage, string $lastReceivedDate): bool
    {
        $chatData = [
            'fecha_ultima_recepcion' => $lastReceivedDate,
            'ultimo_mensaje'         => strlen($newMessage->Body) > 150
                ? substr($newMessage->Body, 0, 147) . '...' : $newMessage->Body,
            'account_sid'            => $newMessage->AccountSid
        ];

        if ($chatEntity['abierto'] !== '1') {
            $chatData['sin_leer'] = 1;
        }

        if ($chatEntity['status_chat'] !== 'activo') {
            $chatData['status_chat'] = 'activo';
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->update(
            'chat.new_chats',
            $chatData,
            ['id' => (int)$chatEntity['id']]
        );

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        if (isset($chatData['status_chat'])) {
            $this->recordChatStatusChange(
                [
                    'id_chat'      => $chatEntity['id'],
                    'fecha_cambio' => Carbon::now()
                                            ->format('Y-m-d H:i:s'),
                    'estado'       => $chatData['status_chat']
                ]
            );
        }

        return true;
    }

    /**
     * @param int $chatID
     * @param stdClass $newMessage
     * @param string $lastReceivedDate
     * @return bool
     */
    private function updateChatEntitySimple(int $chatID, stdClass $newMessage, string $lastReceivedDate): bool
    {
        $chatData = [
            'fecha_ultima_recepcion' => $lastReceivedDate,
            'ultimo_mensaje'         => strlen($newMessage->Body) > 150
                ? substr($newMessage->Body, 0, 147) . '...' : $newMessage->Body,
            'account_sid'            => $newMessage->AccountSid,
            'sin_leer'               => 0,
            'status_chat'            => 'vencido'
        ];

        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->update(
            'chat.new_chats',
            $chatData,
            ['id' => $chatID]
        );

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    /**
     * @param int $chatID
     * @param stdClass $newMessage
     * @param string $lastSentDate
     * @return bool
     */
    public function updateChatSentMessage(int $chatID, stdClass $newMessage, string $lastSentDate): bool
    {
        $chatData = [
            'fecha_ultimo_envio' => $lastSentDate,
            'ultimo_mensaje'     => strlen($newMessage->body) > 150
                ? substr($newMessage->body, 0, 147) . '...' : $newMessage->body,
            'account_sid'        => $newMessage->accountSid
        ];

        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->update(
            'chat.new_chats',
            $chatData,
            ['id' => (int)$chatID]
        );

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    /**
     * @param array $params
     * @return bool
     */
    private function recordChatStatusChange(array $params): bool
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert(
            'chat.relacion_estado_chat',
            $params
        );

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    //Tools

    /**
     * Get a single agent associated to this Chat
     * @return array
     */
    public function getAgent(): array
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where(
            'agentes',
            ['id' => $this->id_agente]
        );

        return $query->row();
    }

    /**
     * Get a single agent associated to this Chat
     * @return array
     */
    public function getVisitor(): array
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where(
            'visitantes',
            ['id' => $this->id_visitante]
        );

        return $query->row();
    }

    /**
     * Get a single agent associated to this Chat
     * @return array
     */
    public function getSession(): array
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where(
            'sesiones',
            ['id' => $this->id_sesion]
        );

        return $query->row();
    }

    /**
     * Check if agent exist by Third Party parameter
     *
     * @param      $id
     * @param bool $local
     *
     * @return stdClass|null|bool
     */
    public function findBy(
        $id,
        bool $local = false
    )
    {
        /** @var CI_DB_result $query */
        if ($local) {
            $query = $this->db->get_where(
                'chat.' . $this->tableName,
                ['id' => $id]
            );
        } else {
            $query = $this->db->get_where(
                'chat.' . $this->tableName,
                ['third_party_id' => $id]
            );
        }

        if (!is_bool($query)) {
            return $query->row();
        } else {
            return $query;
        }
    }

    /**
     * Encuentra un Chat por el número asociado. Retorna un array o null y false en error.
     * @param string $number
     *
     * @return mixed
     */
    // public function findBySenderNumber(string $number)
    // {
    //     $sql   = 'select chats.* from chat.new_chats as chats' .
    //         ' where chats.from like "' . $number . '"';
    //     $query = $this->db->query($sql);
    //     $i     = 0;

    //     while (is_bool($query) && $i < 3) {
    //         $query = $this->db->get();
    //         ++$i;
    //         //Espera 150 milisegundos entre cada intento
    //         sleep(0.15);
    //     }

    //     if ($i < 3) {
    //         return $query->row_array();
    //     }

    //     return false;
    // }

    /**
     * Encuentra un Chat por el número asociado. Retorna un array o null y false en error.
     * @param string $number
     *
     * @return mixed
     */
    public function findBySenderNumber(string $number, string $number_to)
    {
        $sql   = 'select chats.* from chat.new_chats as chats' .
            ' where chats.to like "' . $number_to . '" AND chats.from like "' . $number . '"';
        
        $query = $this->db->query($sql);
        // $i     = 0;

        // while (is_bool($query) && $i < 3) {
        //     $query = $this->db->get();
        //     ++$i;
        //     //Espera 150 milisegundos entre cada intento
        //     sleep(0.15);
        // }

        // if ($i < 3) {
        //     return $query->row_array();
        // }

        // return false;

        $result = ($query!==false && $query->num_rows() > 0) ? $query->row_array() : false;

        return $result;

    }
    
    //obtengo status del chat
    public function getchatStatus(string $chat_id)
    {
        $sql =  "SELECT chats.status_chat FROM chat.new_chats AS chats
                    WHERE id  =  $chat_id ";
     
        $query = $this->db->query($sql);  
  
        $result = ($query!==false && $query->num_rows() > 0) ? $query->row_array() : false;

        return $result['status_chat'];
    }

    /**
     * Normalize Chat data into a strClass before interaction with DB
     * @param stdClass|null $object
     *
     * @return stdClass|null
     */
    public function normalizeObject($object): ?stdClass
    {
        if ($object === null) {
            return null;
        }

        if ($this->provider === 'ZenDesk') {
            $object->id_cliente = null;

            return $object;
        }

        if ($this->provider === 'Twilio_WS') {
            $object->proveedor              = 'Twilio_WS';
            $object->id_agente              = '42';
            $object->id_visitante           = null;
            $object->id_sesion              = null;
            $object->comentario             = null;
            $object->rating                 = null;
            $object->duracion               = null;
            $object->id_departamento        = null;
            $object->sin_leer               = true;
            $object->third_party_id         = null;
            $object->third_party_agent_id   = null;
            $object->third_party_visitor_id = null;
            $object->third_party_session_id = null;
            $object->offline_message        = null;
            $object->id_cliente             = null;
            $object->type                   = 'chat';

            return $object;
        }

        //Logic for normalize other providers is here.

        return null;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider): void
    {
        $this->provider = $provider;
    }

    /**
     * Remove keys with null values.
     *
     * @param array $data
     *
     * @return array
     */
    private function cleanNullValues(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    public function relationAgent()
    {
        return ['operatorWS', ['id_operador' => 'idoperador'], 'type' => 'one'];
    }

    public function relationVisitor()
    {
        return ['visitor', ['id_visitante' => 'id'], 'type' => 'one'];
    }

    public function relationCustomer()
    {
        return ['customer', ['id_cliente' => 'id'], 'type' => 'one'];
    }

    public function relationReceiveWhatsApp()
    {
        return ['receivewhatsapp', ['id' => 'id_chat'], 'type' => 'many'];
    }

    public function relationSentWhatsApp()
    {
        return ['sentwhatsapp', ['id' => 'id_chat'], 'type' => 'many'];
    }

    /**
     * @param int $id
     * @param bool $missed
     * @param bool $onLogin
     * @return bool
     * @throws PusherException
     */
    public function closeByID(
        int $id,
        $missed = false,
        $onLogin = false
    ): bool
    {
        $chat = $this->findByID($id);
        if ($chat !== null) {
            $this->db->trans_begin();
            $this->db->trans_strict(false);

            $data                       = [];
            $data['status_chat']        = $missed !== false ? 'perdido' : 'cerrado';
            $data['fecha_finalizacion'] = Carbon::now()
                                                ->format('Y-m-d H:i:s');
            $lastMessageDate            = Carbon::parse($chat['chats']['fecha_inicio']);
            $now                        = Carbon::now();
            $data['duracion']           = $now->diffInMinutes($lastMessageDate);

            $this->db->update(
                'chat.' . $this->tableName,
                $data,
                array('id' => $id)
            );

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();

                return false;
            }

            $this->db->trans_commit();

            if ($chat['chats']['id_agente'] !== null && $onLogin === false) {
                /** @var OperatorWS $opModel */
                $this->load->model(
                    'operatorWS',
                    '',
                    true
                );
                $opModel = $this->operatorWS;
                $opData  = $opModel->findByAnchor(
                    $chat['chats']['id_agente'],
                    'idoperador',
                    [
                        $opModel->relationChatActive(),
                        $opModel->relationStatus(),
                        $opModel->relationHours(),
                        $opModel->relationAbsences()
                    ]
                );

                if (!empty($opData) && $opModel->getStatus($opData[(int)$chat['chats']['id_agente']]) === 'Online') {
                    $opData          = $opData[(int)$chat['chats']['id_agente']];
                    $manyActiveChats = $opData['chats'] !== null ? count($opData['chats']) : 0;
                    $limit           = (int)$opData['operadores']['cantidad_asignar'];

                    if ($manyActiveChats < $limit) {
                        $limit -= $manyActiveChats;
                        checkofflinechats(
                            $chat['chats']['id_agente'],
                            $limit,
                            $chat['chats']['type']
                        );
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param int|array $id
     * @param int $operatorID
     * @return bool
     */
    public function setOperatorByID(
        $id,
        int $operatorID
    ): bool
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $data              = [];
        $data['id_agente'] = $operatorID;
        if (is_array($id)) {
            $this->db->update(
                'chat.' . $this->tableName,
                $data,
                'id in (' . implode(
                    ',',
                    $id
                ) . ')'
            );
        } else {
            $this->db->update(
                'chat.' . $this->tableName,
                $data,
                ['id' => $id]
            );
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }
        $this->db->trans_commit();

//        file_put_contents(FCPATH . 'ign-logs/setOpLog', 'Query' . PHP_EOL, FILE_APPEND | LOCK_EX);

        return true;
    }

    /**
     * @param array $array
     * @return mixed
     */
    public function organizeMessages(array $array)
    {
        if (isset($array['chats'])) {
            //TODO single Result
        } else {
            foreach ($array as $key => $chat) {
                $messagesChat = [];
                if (isset($chat['received_messages']) && !empty($chat['received_messages'])) {
                    foreach ($chat['received_messages'] as $receivedMessage) {
                        $receivedMessage['received'] = true;
                        $receivedMessage['sent']     = false;

                        $messagesChat[] = $receivedMessage;
                    }
                }

                if (isset($chat['sent_messages']) && !empty($chat['sent_messages'])) {
                    foreach ($chat['sent_messages'] as $sentMessage) {
                        $sentMessage['received'] = false;
                        $sentMessage['sent']     = true;

                        $messagesChat[] = $sentMessage;
                    }
                }

                usort(
                    $messagesChat,
                    static function (
                        $a,
                        $b
                    ) {
                        return $a['fecha_creacion'] <=> $b['fecha_creacion'];
                    }
                );

                $chat['messages'] = $messagesChat;
                unset($chat['received_messages'], $chat['sent_messages']);
                $array[$key] = $chat;
            }
        }

        return $array;
    }

    /**
     * @param array $chatArray
     * @return array
     * @deprecated
     */
    public function findPrevByID(array $chatArray): array
    {
        $manyOpActiveChats = $this->db->query(
            'select chat.new_chats.id, ' .
            'chat.new_chats.id_operador, ' .
            'chat.new_chats.fecha_inicio, ' .
            'gestion.operadores.nombre_apellido as operator_names ' .
            'from chat.new_chats ' .
            'left join gestion.operadores on idoperador = id_operador ' .
            'where fecha_inicio < "' . $chatArray['new_chats']['fecha_inicio'] . '"' .
            ' order by fecha_inicio DESC limit 3'
        );

        return $manyOpActiveChats->result();
    }

    /**
     * @param stdClass $chat
     * @return bool
     * @throws GuzzleException
     */
    public function verifyType(stdClass $chat): bool
    {
        $this->setProvider($chat->proveedor);

        $type          = 'general';
        $visitorObject = $this->getVisitorObject($chat);

        if ($visitorObject !== null && $visitorObject['visitor']->solicitud_id !== null && $visitorObject['visitor']->solicitud_id !== '') {
            $type = 'cliente';
        }

        if ($type === 'cliente') {
            $data = [
                'type' => 'cliente'
            ];

            $this->doUpdate(
                $chat->id,
                $data
            );

            return true;
        }

        return false;
    }

    /**
     * This function close all outdated chats
     * @deprecated
     */
    public function closeOutdatedChats(): bool
    {
        $sql   = 'SELECT new_chats.id, new_chats.fecha_inicio, rw.max_fecha_creacion as last_received_date
FROM chat.new_chats
         LEFT JOIN (
    SELECT MAX(chat.received_messages.fecha_creacion) as max_fecha_creacion,
           chat.received_messages.id_chat
    FROM chat.received_messages
    GROUP BY id_chat
) as rw ON new_chats.id = rw.id_chat
WHERE new_chats.status_chat = ' . $this->db->escape('activo') . '
GROUP BY id';
        $query = $this->db->query($sql);

        if (!is_bool($query)) {
            $activeChats    = $query->result_array();
            $chatIDsToClose = [];

            foreach ($activeChats as $chat) {
                if ($chat['last_received_date'] !== null) {
                    $lastMessageDate = Carbon::parse($chat['last_received_date']);
                    $now             = Carbon::now();
                    $differenceDates = $now->diffInHours($lastMessageDate);

                    if ($differenceDates >= 24) {
                        $chatIDsToClose[] = $chat;
                    }
                } else {
                    $startDate       = Carbon::parse($chat['fecha_inicio']);
                    $now             = Carbon::now();
                    $differenceDates = $now->diffInHours($startDate);


                    if ($differenceDates >= 24) {
                        $chatIDsToClose[] = $chat;
                    }
                }
            }

            if (!empty($chatIDsToClose)) {
                return $this->closeChats($chatIDsToClose);
            }

            return true;
        }

        return false;
    }

    /**
     * @param array $chats
     * @return bool
     */
    private function closeChats(array $chats): bool
    {
        $this->db->trans_begin();

        foreach ($chats as $chat) {
            $data              = [
                'status_chat' => 'vencido',
                'abierto' => 0
            ];
            $relationStateChat = [
                'id_chat'      => $chat['id'],
                'fecha_cambio' => Carbon::now()
                                        ->format('Y-m-d H:i:s'),
                'estado'       => 'vencido',
            ];

            $this->db->update(
                'chat.' . $this->tableName,
                $data,
                array('id' => $chat['id'])
            );

            $this->db->insert(
                'chat.relacion_estado_chat',
                $relationStateChat
            );
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    /**
     * Obtiene los chats que están en la cola. Cierra los que están fuera de ventana y retorna sólo los que deben ser
     * asignados.
     * @return array
     */
    public function getChatsQueue(): array
    {
        if (ENVIRONMENT == 'production') {
            $numero = TWILIO_PROD_COBRANZAS;
        }else{
            $numero = TWILIO_TEST_COBRANZAS;
        }
        $sql   = 'select new_chats.id, new_chats.fecha_inicio,'
            . ' new_chats.fecha_ultima_recepcion as last_received_date,'
            . ' new_chats.ultimo_mensaje, s.operador_asignado as operador_relacion,'
            . ' new_chats.nombres, new_chats.apellidos, new_chats.from as num_celular,'
            . ' new_chats.status_chat'
            . ' from chat.new_chats'
            . ' left join solicitudes.solicitud s on s.telefono = new_chats.from and operador_asignado != 0'
            . ' where (new_chats.id_operador is null or new_chats.id_operador = 42) and new_chats.to = "'.$numero.'" and new_chats.status_chat = "activo"'
            . ' GROUP by new_chats.id order by new_chats.fecha_inicio';
        $query = $this->db->query($sql);

        if (!is_bool($query)) {
            $queueChats = $query->result_array();

            //1. Cerrar los que no están en ventana pero si en la cola.
            $chatIDsToClose = [];

            foreach ($queueChats as $cKey => $chat) {
                if ($chat['last_received_date'] !== null) {
                    $lastMessageDate = Carbon::parse($chat['last_received_date']);
                    $now             = Carbon::now();
                    $differenceDates = $now->diffInHours($lastMessageDate);
                    if ($differenceDates >= 24) {
                        $chatIDsToClose[] = $chat;
                        unset($queueChats[$cKey]);
                    }
                } else {
                    $startDate       = Carbon::parse($chat['fecha_inicio']);
                    $now             = Carbon::now();
                    $differenceDates = $now->diffInHours($startDate);


                    if ($differenceDates >= 24) {
                        $chatIDsToClose[] = $chat;
                        unset($queueChats[$cKey]);
                    }
                }
            }

            if (!empty($chatIDsToClose)) {
                //Ejecuta el cierre de los chats
                $this->closeChats($chatIDsToClose);
            }

            return $queueChats;
        }
    }

    /**
     * Obtiene los chats que están en la cola. Cierra los que están fuera de ventana y retorna sólo los que deben ser
     * asignados.
     * @param int $idOp
     * @return bool|array
     */
    public function getChatsActiveByOp(int $idOp)
    {
        $sql   = 'select new_chats.id as id, new_chats.id as chat_id, new_chats.status_chat,'
            . ' new_chats.nombres, new_chats.apellidos, new_chats.sin_leer, new_chats.from as num_celular,'
            . ' new_chats.documento, new_chats.email,'
            . ' new_chats.ultimo_mensaje, new_chats.fecha_ultima_recepcion, new_chats.fecha_ultimo_envio'
            . ' from chat.new_chats'
            . " where (new_chats.status_chat = 'activo' and new_chats.id_operador = " . $idOp . ')'
            // . " or (new_chats.status_chat = 'vencido' and new_chats.sin_leer = 1 and new_chats.id_operador = " . $idOp . ')'
            . " or (new_chats.status_chat = 'pendiente' and new_chats.id_operador = " . $idOp . ')'
            . " or (new_chats.status_chat = 'revision' and new_chats.id_operador = " . $idOp . ')'
            . ' order by new_chats.fecha_inicio';
        $query = $this->db->query($sql);

        if (!is_bool($query)) {
            $activeChats = $query->result_array();

            //1. Cerrar los que no están en ventana pero si en la cola.
            $chatIDsToClose = [];

            foreach ($activeChats as $cKey => $chat) {
                if ($chat['fecha_ultima_recepcion'] !== null) {
                    $lastMessageDate = Carbon::parse($chat['fecha_ultima_recepcion']);
                    $lastSentDate    = Carbon::parse($chat['fecha_ultimo_envio']);
                    $now             = Carbon::now();
                    $differenceDates = $now->diffInHours($lastMessageDate);

                    if ($chat['status_chat'] === 'pendiente') {
                        $differenceSentDates = $now->diffInHours($lastSentDate);
                        if ($differenceSentDates >= 12) {
                            $chatIDsToClose[] = $chat;
                            unset($activeChats[$cKey]);
                        }
                    }

                    if ($chat['status_chat'] === 'revision' && $differenceDates >= 36) {
                        $chatIDsToClose[] = $chat;
                        unset($activeChats[$cKey]);
                    }

                    if ($chat['status_chat'] === 'activo' && $differenceDates >= 24) {
                        $chatIDsToClose[] = $chat;
                        unset($activeChats[$cKey]);
                    }

                    // if ($chat['status_chat'] === 'vencido' && $differenceDates >= 24) {
                    //     $chatIDsToClose[] = $chat;
                    //     unset($activeChats[$cKey]);
                    // }
                } else {
                    $startDate       = Carbon::parse($chat['fecha_inicio']);
                    $now             = Carbon::now();
                    $differenceDates = $now->diffInHours($startDate);


                    if ($differenceDates >= 24) {
                        $chatIDsToClose[] = $chat;
                        unset($activeChats[$cKey]);
                    }
                }
            }

            if (!empty($chatIDsToClose)) {
                //Ejecuta el cierre de los chats
                $this->closeChats($chatIDsToClose);
            }

            return $activeChats;
        }

        return false;
    }


    /**
     * Por cada chat en espera ($queueChats) verifica para los operadores disponibles y le asigna uno según sea el caso.
     * @param array $queueChats
     * @throws PusherException
     */
    public function assignOperators(array $queueChats): void
    {
        $this->load->helper('dispatcher_helper');
        $chatsAssignations   = [];
        /**
         * Obtener operadores con posibilidad de asignación.
         */
        $onlineOperatorsList = getOnlineOperatorsList2();

        foreach ($onlineOperatorsList as $oKey => $operator) {
            $operator['many_chats'] = $operator['new_chats'] !== null ? count($operator['new_chats']) : 0;
            unset($operator['new_chats']);
            $onlineOperatorsList[$oKey] = $operator;
        }

        foreach ($queueChats as $chat) {
            /**
             * Obtiene la data relacionada a la asignación del chat.
             */
            $assignation = getFreeOperatorID2(
                $chat,
                $onlineOperatorsList
            );
            if ($assignation['id'] !== null) {
                $chatsAssignations[] = [
                    'chatID'     => $chat['id'],
                    'operatorID' => $assignation['id'],
                    'chat'       => $chat
                ];
            }
        }

        $this->assignOperatorsBatch($chatsAssignations);
    }

    /**
     * Crea la transacción para la asignación masiva de operadores a chats.
     *
     * @param array $chatsAssignations
     * @return bool|null
     * @throws PusherException
     */
    private function assignOperatorsBatch(array $chatsAssignations): ?bool
    {
        $this->db->trans_begin();

        foreach ($chatsAssignations as $assignation) {
            $data = [
                'id_operador' => $assignation['operatorID']
            ];

            $this->db->update(
                'chat.' . $this->tableName,
                $data,
                array('id' => $assignation['chatID'])
            );
            $this->updateRelationOpChat((int)$assignation['operatorID'], (int)$assignation['chatID']);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return null;
    }

    /**
     * @param string $estado
     * @param int $chatID
     */
    public function updateRelationStateChat(string $estado, int $chatID): void
    {
        $this->db->trans_begin();

        $relationStateChat = [
            'id_chat'      => $chatID,
            'fecha_cambio' => Carbon::now()
                                    ->format('Y-m-d H:i:s'),
            'estado'       => $estado,
        ];

        $this->db->insert(
            'chat.relacion_estado_chat',
            $relationStateChat
        );

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    /**
     * Crea un nuevo registro en la tabla auxiliar chat.relacion_operador_chat por el nuevo operador asignado.
     *
     * @param int $opID
     * @param int $chatID
     */
    public function updateRelationOpChat(int $opID, int $chatID): void
    {
        $this->db->trans_begin();

        $relationOpChat = [
            'id_chat'      => $chatID,
            'id_operador'  => $opID,
            'fecha_cambio' => Carbon::now()
                                    ->format('Y-m-d H:i:s')
        ];

        $this->db->insert(
            'chat.relacion_operador_chat',
            $relationOpChat
        );

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function getAuthentication(int $userID)
    {
        $Auth = $this->db->query('SELECT idoperador, nombre_apellido FROM gestion.operadores WHERE estado = 1 AND documento ="'.$userID.'"');

        return $Auth->row();
    }

    public function updateChatbotTest($senderNumber, $receiverNumber){
        $data = ["id_operador" => 192];

        $this->db->where('from', (string)$senderNumber);
        $this->db->where('to', (string)$receiverNumber);
        $this->db->update('new_chats', $data);
    }

    public function checkChat($senderNumber, $receiverNumber){
        $this->db->select('id');
        $this->db->from('new_chats');
        $this->db->where('from', (string)$senderNumber);
        $this->db->where('to', (string)$receiverNumber);
        $this->db->where_not_in('status_chat', ['activo', 'pendiente']);
        $query = $this->db->get();
        // var_dump($this->db->last_query());die;
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function checkChatOp192($senderNumber, $receiverNumber){
        $this->db->select('id');
        $this->db->from('new_chats');
        $this->db->where('from', (string)$senderNumber);
        $this->db->where('to', (string)$receiverNumber);
        $this->db->where('id_operador', 192);
        $query = $this->db->get();
        // var_dump($this->db->last_query());die;
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function getSentMessages($parametros)
    {
        $this->db->select(`nc`.`status_chat`,'sm.*, op.nombre_apellido');
        $this->db->from('sent_messages sm');
        $this->db->where('sm.id_chat', $parametros['id_chat']);
        $this->db->join('chat.new_chats nc', 'sm.id_chat = nc.id', 'left');
        $this->db->join('gestion.operadores op', 'op.idoperador = sm.id_operador', 'left');

        if (isset($parametros['fecha_inicio']) && isset($parametros['fecha_fin'])) {
            $this->db->where('sm.fecha_creacion > "' . $parametros['fecha_inicio'] . ' 23:59:59" and sm.fecha_creacion <= "' . $parametros['fecha_fin'] . ' 23:59:59"');
        }
        if(isset($parametros['id_sent']) ) { $this->db->where('sm.id', $parametros['id_sent']);}        
        $query = $this->db->get();
        // var_dump($this->db->last_query());die;      
        return $query->result_array();
    }

    public function updateOperador($id_chat, $id_operador)
    {
        $this->db->trans_begin();

        $data = array('id_operador' => $id_operador);
        $this->db->where('id', $id_chat);
        $this->db->update('new_chats', $data);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
        }

        return true;
        
    }

	public function get_solicitud(array $data): object
	{
		$this->db_solicitudes->select('id,nombres,apellidos,documento,email,telefono');
		$this->db_solicitudes->from('solicitud');
		$this->db_solicitudes->where($data);
		$this->db_solicitudes->order_by('id', 'DESC');
		$query = $this->db_solicitudes->get();
        //print_r ($this->db_solicitudes->affected_rows());die;
		if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->row();
        }else{
            return new StdClass();

        }
	}

}

<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\PusherException;

defined('BASEPATH') or exit('No direct script access allowed');

class Chat extends Orm_model
{
    public $provider;

	const CHAT_TEMPLATE_TYPE_WHATSAPP = 'WAPP';
	const CHAT_TEMPLATE_TYPE_SMS = 'SMS';
	const CHAT_TEMPLATE_TYPE_IVR = 'IVR';
	
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
        $this->db_parametria = $this->load->database('parametria',TRUE);
        $this->db_gestion = $this->load->database('gestion',TRUE);
        $this->db_maestro = $this->load->database('maestro',TRUE);
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
    ) {
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
        $fromNumber  = Twilio::getMobileNumber($object->From);
        $toNumber    = Twilio::getMobileNumber($object->To);

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
            $mobileNumber  = Twilio::getMobileNumber($object->From);
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
    ): bool {
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
    public function registerReceivedMessage(
        array $chatEntity,
        stdClass $receivedMessage,
        bool $chatCreation = false
    ): void {
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
    ) {
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

        $result = ($query !== false && $query->num_rows() > 0) ? $query->row_array() : false;

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
            $object->id_agente              = null;
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
    ): bool {
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
    ): bool {
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
            $numero = TWILIO_PROD_GESTION;
        }else{
            $numero = TWILIO_TEST_GESTION;
        }
        $sql   = 'select new_chats.id, new_chats.fecha_inicio,'
            . ' new_chats.fecha_ultima_recepcion as last_received_date,'
            . ' new_chats.ultimo_mensaje, s.operador_asignado as operador_relacion,'
            . ' new_chats.nombres, new_chats.apellidos, new_chats.from as num_celular,'
            . ' new_chats.status_chat'
            . ' from chat.new_chats'
            . ' left join solicitudes.solicitud s on s.telefono = new_chats.from and operador_asignado != 0'
            . ' where (new_chats.id_operador is null or new_chats.id_operador IN (70)) and new_chats.to = '.$numero.' and new_chats.status_chat = "activo"'
            . ' order by new_chats.fecha_inicio';
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
        $onlineOperatorsList = getOnlineOperatorsList();

        foreach ($onlineOperatorsList as $oKey => $operator) {
            $operator['many_chats'] = $operator['new_chats'] !== null ? count($operator['new_chats']) : 0;
            unset($operator['new_chats']);
            $onlineOperatorsList[$oKey] = $operator;
        }

        foreach ($queueChats as $chat) {
            /**
             * Obtiene la data relacionada a la asignación del chat.
             */
            $assignation = getFreeOperatorID(
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
            if ($assignation['chat']['operador_relacion'] == 0) {
                $data = [
                    'id_operador' => $assignation['operatorID']
                ];
            } else {
                $data = [
                    'id_operador' => $assignation['chat']['operador_relacion']
                ];
            }

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
        $Auth = $this->db->query('SELECT idoperador, nombre_apellido FROM gestion.operadores WHERE estado = 1 AND documento ="' . $userID .'"');

        return $Auth->row();
    }

 
    /**
     * betza
     * mensajes para el componente boxWhatsapp
     */


    public function getChat($param)
    {
        $this->db->select('nc.*, op.nombre_apellido');
        $this->db->from('new_chats nc');
        $this->db->join('gestion.operadores op', 'op.idoperador = nc.id_operador', 'left');

        if(isset($param['numero'])) {   $this->db->where('nc.`from`', (string)$param['numero']);}
        if(isset($param['to'])) {   $this->db->where('nc.`to`', (string)$param['to']);}
        if(isset($param['id_chat'])) {   $this->db->where('nc.`id`', $param['id_chat']);}
        
        $this->db->order_by('fecha_inicio', 'desc');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;      
        return $query->result_array();
    }

    public function getReceivedMessages($parametros)
    {
        $this->db->select('*');
        $this->db->from('received_messages rm');
        $this->db->where('rm.id_chat', $parametros['id_chat']);
        if (isset($parametros['fecha_inicio']) && isset($parametros['fecha_fin'])) {
            $this->db->where('rm.fecha_creacion > "' . $parametros['fecha_inicio'] . ' 23:59:59" and rm.fecha_creacion <= "' . $parametros['fecha_fin'] . ' 23:59:59"');
        }
        //if(isset($parametros['inicio']) && isset($parametros['fin'])) { $this->db->limit($parametros['fin'], $parametros['inicio']);}
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;      
        return $query->result_array();
    }

    public function getSentMessages($parametros)
    {
        $this->db->select(`nc`.`status_chat`, 'sm.*, op.nombre_apellido');
        $this->db->from('sent_messages sm');

        $this->db->where('sm.id_chat', $parametros['id_chat']);
        $this->db->join('chat.new_chats nc', 'sm.id_chat = nc.id', 'left');
        $this->db->join('gestion.operadores op', 'op.idoperador = sm.id_operador', 'left');

        if (isset($parametros['id_template'])) {
			$this->db->where('sm.id_template', $parametros['id_template']);
		}
		
        if (isset($parametros['fecha_inicio']) && isset($parametros['fecha_fin'])) {
            $this->db->where('sm.fecha_creacion > "' . $parametros['fecha_inicio'] . ' 23:59:59" and sm.fecha_creacion <= "' . $parametros['fecha_fin'] . ' 23:59:59"');
        }
        if(isset($parametros['id_sent']) ) { $this->db->where('sm.id', $parametros['id_sent']);}        
        $query = $this->db->get();

//         var_dump($this->db->last_query());die;      
        return $query->result_array();
    }

    public function countMessages($parametros) {
        $sql = "";
        if($parametros['received']){
            $sql .= "SELECT count(*) AS quantity_messages_received FROM chat.received_messages sm WHERE sm.id_chat = {$parametros['id_chat']}";
        } else {
            $sql .= "SELECT count(*) AS quantity_messages_sent FROM chat.sent_messages sm WHERE sm.id_chat = {$parametros['id_chat']}";
        }     

        $resultado = $this->db->query($sql);
        return $resultado->result_array();
    }

    public function get_all_messages($parametros)
    {
        $sql = "SELECT * FROM 
                    (SELECT 
                            rm.id_chat, 
                            rm.id, 
                            rm.fecha_creacion, 
                            rm.sms_status, 
                            '-1' AS id_operador, true AS received, 
                            false AS sent, 
                            rm.body, 
                            rm.media_content_type0, 
                            rm.media_url0, 
                            NULL AS nombre_apellido_operador, 
                            NULL as sms_message_sid ";
        $sql .= "FROM received_messages rm 
                    WHERE rm.id_chat = '" . $parametros['id_chat'];
        $sql .= "' UNION ALL 
                        SELECT 
                            sm.id_chat, 
                            sm.id, 
                            sm.fecha_creacion, 
                            sm.sms_status, 
                            sm.id_operador, 
                            false AS received, 
                            true AS sent,
                            sm.body, 
                            sm.media_content_type0, 
                            sm.media_url0, 
                            op.nombre_apellido AS nombre_apellido_operador,
                            sm.sms_message_sid ";
        $sql .= "FROM sent_messages sm 
                    INNER JOIN gestion.operadores op ON sm.id_operador = op.idoperador 
                        WHERE sm.id_chat = '" . $parametros['id_chat'] . "') chat ";
        $sql .= "ORDER BY chat.fecha_creacion 
                    DESC LIMIT " . $parametros['inicio'] . "," . $parametros['fin'];
                    
        $query = $this->db->query($sql);
        //var_dump($this->db->last_query());die;      
        return $query->result_array();
    }

    ///get all chat no vencidos sin atender por operador
    public function get_chat_sin_atender($id_operador)
    {
        $this->db->select('count(id) cantidad');
        $this->db->from('new_chats');
        $this->db->where('id_operador', $id_operador);
        $this->db->where('status_chat <> "vencido"');
        $this->db->where('fecha_ultima_recepcion > fecha_ultimo_envio');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        if($this->db->affected_rows() > 0){
            return $query->result();
        }else{
            return 0;
        }
    }

    public function get_chat_iniciados($id_operador)
    {
        $this->db->select('count(id) cantidad');
        $this->db->from('new_chats');
        $this->db->where('id_operador', $id_operador);
        $this->db->where('status_chat <> "vencido"');
        $this->db->where('iniciado_por = "operator"');
        $query = $this->db->get();

        if($this->db->affected_rows() > 0){
            return $query->result();
        }else{
            return 0;
        }
    }

    public function get_chats_agenda($documento){
        $this->db->select('nc.id');
        $this->db->from('new_chats nc, solicitudes.solicitante_agenda_telefonica ag');
        $this->db->where("nc.from = ag.numero and ag.documento = '$documento'");
        $query = $this->db->get();

        if($this->db->affected_rows() > 0){
            return $query->result();
        }else{
            return [];
        }

    }

    public function get_template($param){
        $this->db->select('templates.*, cet.tipo_envio, cet.id_operador');
        $this->db->from('templates');
        $this->db->join('chat.configutacion_envio_templates cet', 'cet.tipo_template = chat.templates.tipo_template', 'left');

        $this->db->where('estado', '1');
        if (isset($param["canal"])){
            $this->db->like('canal', $param["canal"]); //donde canal contenga viene en param canal .like.
        }
        if (isset($param["tipo_template"])){            
            $this->db->where('templates.tipo_template', $param["tipo_template"]); //donde tipo_template = WAPP, SMS, IVR.
        }        
        $query = $this->db->get();
        // var_dump($this->db->last_query());die;
        return $query->result_array();
    }

    public function updateAgendaProveedor($id_template, $data){
        $this->db->where('id', $id_template);
        $update = $this->db->update('templates', $data);
        return $update;
    }
    public function updateEstadoServicio($numero, $data){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('numero', $numero);
        $update = $this->db->update('solicitante_agenda_telefonica', $data);
        return $update;
    }

    /**
     * @param array $array
     * @return mixed
     */
    public function organizeMessagess(array $chat)
    {
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
            static function ($a, $b) {
                return $a['fecha_creacion'] <=> $b['fecha_creacion'];
            }
        );

        $chat['messages'] = $messagesChat;
        unset($chat['received_messages'], $chat['sent_messages']);
        $array = $chat;

        return $array;
    }

    /**
     * get template message
     */

    public function get_template_details_by($param){
        $this->db->select('*');
        $this->db->from('templates as template');
        $this->db->join('variables_template as variable', 'variable.id_template = template.id', 'left');
        //$this->db->where('variable.id_template = templates.id');
        
        if(isset($param['id_template']))    {   $this->db->where('template.id', $param['id_template']);     }
        if(isset($param['id_template_twilio']))    {   $this->db->where('template.id_template_twilio', $param['id_template_twilio']);     }
        
        
        $query = $this->db->get();
       // var_dump($this->db->last_query());die;
        return $query->result();

    }

    public function get_query_result($query){

        $query = $this->db->query($query);
        $row = $this->db->affected_rows();
        //var_dump($this->db->last_query());die;
        if ($row >0) {
                return $query->result_array();
        } else {
                return [];
        }
    }
    /***********************/
    /*** Canales de Chat ***/
    /***********************/
    public function getCanalChat() {
        if (ENVIRONMENT == 'production') {
            $twilio = TWILIO_PROD_GESTION;
        }else{
            $twilio = TWILIO_TEST_GESTION;
        }

        $sql = "select DISTINCT IF(nc.to = '" . $twilio . "', 'Ventas', 'Cobranzas') AS canal, nc.to AS tlf
                FROM chat.new_chats nc
                ORDER BY nc.to;";
        
        $resultado = $this->db->query($sql);
        return $resultado->result_array();
    }
    /**************************************************************/
    /*** Se obtienen los chats activos por dni de cliente ***/
    /**************************************************************/

    public function getChatsActiveByCliente($documento)
    {
        $sql   = 'select * from chat.new_chats where (new_chats.status_chat = "activo" and new_chats.documento ="'. $documento. '")';
        $query = $this->db->query($sql);        

        return $query->result_array();
    }
	
	
	/**
	 * Obtiene el ultimo chat del cliente atravez del documento
	 * 
	 * @param $documento
	 *
	 * @return mixed
	 */
	public function getLastChatByClienteDocument($documento)
	{
		$result = $this->db->select('*')
			->from('chat.new_chats')
			->where('documento', $documento)
			->order_by('id', 'DESC')
			->get()->result_array();
		
		return $result;
	}
	
    /**************************************************************/
    /*** Se obtienen los operadores según el canal seleccionado ***/
    /**************************************************************/
    public function getOperadoresCanal($canal, $id_operador = null) {
        $where = "";

        if($id_operador){
            $where = " AND id_operador = {$id_operador}";
        }
        
        $sql = "select A.*, ifnull(B.activo, 0) activo, ifnull(C.sin_leer, 0) sin_leer, ifnull(D.vencido, 0) vencido, ifnull(E.ultimo, 0) ultimo, ifnull(F.pendiente, 0) pendiente
                FROM (
                /*** Buscar los operadores según el canal seleccionado ***/
                SELECT nc.`to`, nc.id_operador, op.nombre_apellido, count(1) AS total
                FROM chat.new_chats nc
                    INNER JOIN gestion.operadores op ON nc.id_operador = op.idoperador
                WHERE id_operador IS NOT NULL
                    AND nc.`to` = $canal
                    /***AND nc.documento IS NOT NULL***/
                    AND op.estado = 1
                    $where
                GROUP BY nc.`to`, nc.id_operador) AS A
                    LEFT JOIN (
                    /*** status ACTIVO ***/
                    SELECT nc.id_operador, count(1) AS activo
                    FROM chat.new_chats nc
                    WHERE id_operador IS NOT NULL
                        AND nc.`to` = $canal
                        AND nc.status_chat = 'activo'
                        /***AND nc.documento IS NOT NULL***/
                    GROUP BY nc.`to`, nc.id_operador) AS B ON A.id_operador = B.id_operador
                    LEFT JOIN (
                    /*** status SIN LEER ***/
                    SELECT nc.id_operador, count(1) AS sin_leer
                    FROM chat.new_chats nc
                    WHERE id_operador IS NOT NULL
                        AND nc.`to` = $canal
                        AND nc.status_chat = 'activo'
                        AND nc.sin_leer = 1
                        /***AND nc.documento IS NOT NULL***/
                    GROUP BY nc.`to`, nc.id_operador) AS C ON A.id_operador = C.id_operador
                    LEFT JOIN (
                    /*** status VENCIDO ***/
                    SELECT nc.id_operador, count(1) AS vencido
                    FROM chat.new_chats nc
                    WHERE id_operador IS NOT NULL
                        AND nc.`to` = $canal
                        AND nc.status_chat = 'vencido'
                        AND nc.sin_leer = 1
                        /***AND nc.documento IS NOT NULL***/
                    GROUP BY nc.`to`, nc.id_operador) AS D ON A.id_operador = D.id_operador
                    LEFT JOIN (
                    /*** status SIN RESPONDER ***/
                    SELECT nc.id_operador, count(1) AS ultimo
                    FROM chat.new_chats nc
                    WHERE id_operador IS NOT NULL
                        AND nc.`to` = $canal
                        AND nc.fecha_ultima_recepcion > nc.fecha_ultimo_envio
                        /***AND nc.documento IS NOT NULL***/
                    GROUP BY nc.`to`, nc.id_operador) AS E ON A.id_operador = E.id_operador
                    LEFT JOIN (
                        /*** status SIN LEER ***/
                        SELECT nc.id_operador, count(1) AS pendiente
                        FROM chat.new_chats nc
                        WHERE id_operador IS NOT NULL
                            AND nc.`to` = $canal
                            AND nc.status_chat = 'pendiente'
                            AND nc.sin_leer = 1
                            /***AND nc.documento IS NOT NULL***/
                        GROUP BY nc.`to`, nc.id_operador) AS F ON A.id_operador = F.id_operador;";
        $resultado = $this->db->query($sql);
        return $resultado->result_array();
    }
    /***************************************************************/
    /*** Se obtienen los clientes según el operador seleccionado ***/
    /***************************************************************/
    public function getOperadorCliente($canal, $id_operador, $filtro, $status) {
        
        $sql = "SELECT  nc.id,
                        nc.id_operador,
                        IFNULL(op.nombre_apellido, ' ') nombre_operador, 
                        IFNULL(nc.documento, ' ') documento, 
                        IFNULL(nc.nombres, 'Visitante') nombres, 
                        IFNULL(nc.apellidos, ' ') apellidos,
                        nc.to canal,
                        nc.sin_leer,
                        nc.abierto,
                        nc.status_chat,
                        nc.ultimo_mensaje,
                        IF ((nc.sin_leer = 0 AND nc.abierto = 0), DATE_FORMAT(nc.fecha_ultimo_envio, '%d/%m/%Y %H:%i'), DATE_FORMAT(nc.fecha_ultima_recepcion, '%d/%m/%Y %H:%i')) ultima_hora
                FROM chat.new_chats nc left join gestion.operadores op on nc.id_operador = op.idoperador
                WHERE nc.id_operador = $id_operador
                    AND nc.`to` = $canal ";
                    

        switch ($filtro) {
            case "activo":
                $sql = $sql . " and nc.status_chat = 'activo' ";
            break;
            case "sin_leer":
                $sql = $sql . " AND nc.sin_leer = 1 and nc.status_chat = 'activo' ";
            break;
            case "vencido":
                $sql = $sql . " and nc.status_chat = 'vencido' AND nc.sin_leer = 1 ";
            break;
            case "pendiente":
                $sql = $sql . " and nc.status_chat = 'pendiente' AND nc.sin_leer = 1";
            break;
            case "sin_responder":
                $sql = $sql . " and nc.fecha_ultima_recepcion > nc.fecha_ultimo_envio and nc.status_chat = 'activo' ";
            break;
            default:
                $sql = $sql . " And nc.status_chat = '$status'";
            break;
        }

        $sql = $sql . " ORDER BY nc.fecha_ultima_recepcion DESC;"; 

        $resultado = $this->db->query($sql);

        return $resultado->result_array();
    }
    /***************************************************************/
    /*** Se obtienen los Chats según el cliente seleccionado ***/ 
    /***************************************************************/
    public function getOperadorChat($id_chat, $inicio, $fin) {
        $sql = "select id,
                    body,
                    DATE_FORMAT(fecha_creacion, '%d/%m/%Y %H:%i:%s') fecha_creacion,
                    media_content_type0,
                    media_url0,
                    recibido,
                    nombre_apellido,
                    documento,
                    tlf_cliente,
                    iniciado_por,
                    ultimo_mensaje,
                    status_chat,
                    sin_leer,
                    nombre_operador,
                    sms_status,
                    sms_message_sid  
                FROM (SELECT rm.id,
                            rm.body,
                            rm.fecha_creacion,
                            rm.media_content_type0,
                            rm.media_url0,
                            1 AS recibido,
                            concat(cn.nombres, ' ', cn.apellidos) as nombre_apellido,
                            cn.documento,
                            cn.`from` as tlf_cliente,
                            cn.iniciado_por,
                            cn.ultimo_mensaje,
                            cn.status_chat,
                            cn.sin_leer,
                            null as nombre_operador,
                            
                            rm.sms_status,
                            NULL AS sms_message_sid  
                    FROM chat.new_chats cn
                        INNER JOIN chat.received_messages rm ON cn.id = rm.id_chat
                    WHERE rm.id_chat = $id_chat
                    UNION ALL
                    SELECT sm.id,
                            sm.body,
                            sm.fecha_creacion,
                            sm.media_content_type0,
                            sm.media_url0,
                            0 AS recibido,
                            concat(cn.nombres, ' ', cn.apellidos) as nombre_apellido,
                            cn.documento,
                            cn.`from` as tlf_cliente,
                            cn.iniciado_por,
                            cn.ultimo_mensaje,
                            cn.status_chat,
                            cn.sin_leer,
                            op.nombre_apellido as nombre_operador,
                
                            sm.sms_status,
                            sm.sms_message_sid  
                    FROM chat.new_chats cn
                        INNER JOIN chat.sent_messages sm ON cn.id = sm.id_chat
                        INNER JOIN gestion.operadores op ON sm.id_operador = op.idoperador
                    WHERE sm.id_chat = $id_chat) AS todo
                ORDER BY todo.fecha_creacion DESC, todo.recibido ASC
                LIMIT $inicio, $fin;";

        $resultado = $this->db->query($sql);
        return $resultado->result_array();
    }
    /************************************************************/
    /*** Obtiene los totales de mensajes enviados y recibidos ***/
    /************************************************************/
    public function getTotalesRecEnv($id_chat) {
        $sql = "Select 1 AS recibido,
                    DATE_FORMAT(min(rm.fecha_creacion), '%d/%m/%Y %H:%i:%s') AS primero,
                    DATE_FORMAT(max(rm.fecha_creacion), '%d/%m/%Y %H:%i:%s') AS ultimo,
                    count(1) AS cantidad
            FROM chat.received_messages rm
            WHERE rm.id_chat = $id_chat
            UNION ALL
            SELECT 0 AS recibido,
                    DATE_FORMAT(min(sm.fecha_creacion), '%d/%m/%Y %H:%i:%s') AS primero,
                    DATE_FORMAT(max(sm.fecha_creacion), '%d/%m/%Y %H:%i:%s') AS ultimo,
                    count(1) AS cantidad
            FROM chat.sent_messages sm
            WHERE sm.id_chat = $id_chat;";

        $totales = $this->db->query($sql);
        return $totales->result_array();
    }    
    /************************************************/
    /*** Se busca por documento, teléfono o texto ***/
    /************************************************/
    public function getTelefonoTextoDocumento($filtro, $txtBuscar, $operador) {
        $sql = "Select nc.id,
            nc.id_operador,
            op.nombre_apellido,
            IFNULL(nc.documento, ' ') documento, 
            IFNULL(nc.nombres, 'Visitante') nombres, 
            IFNULL(nc.apellidos, ' ') apellidos,
            nc.sin_leer,
            nc.abierto,
            nc.status_chat,
            nc.ultimo_mensaje,
            nc.to canal,
            IF((nc.sin_leer = 0 AND nc.abierto = 0),
                DATE_FORMAT(nc.fecha_ultimo_envio, '%d/%m/%Y %H:%i'),
                DATE_FORMAT(nc.fecha_ultima_recepcion, '%d/%m/%Y %H:%i'))
                AS ultima_hora
        FROM chat.new_chats nc
            INNER JOIN gestion.operadores op ON nc.id_operador = op.idoperador ";

        switch ($filtro) {
            case "telefono":
                $sql = $sql . "WHERE nc.id_operador IS NOT NULL
                    AND op.estado = 1
                    AND nc.`from` like '%$txtBuscar%' ";
            break;
            case "documento":
                $sql = $sql . "WHERE nc.id_operador IS NOT NULL
                    AND op.estado = 1
                    AND nc.documento like '%$txtBuscar%' ";
            break;
            case "texto":
                $sql = $sql . "INNER JOIN (SELECT rm.id_chat
                                            FROM chat.received_messages rm
                                            WHERE rm.body LIKE '%$txtBuscar%'
                                            UNION
                                            SELECT sm.id_chat
                                            FROM chat.sent_messages sm
                                            WHERE sm.body LIKE '%$txtBuscar%') AS todo
                                ON nc.id = todo.id_chat
                                WHERE nc.id_operador IS NOT NULL
                                AND op.estado = 1 ";
            break;
        }
        if ($operador != "todos") {
            $sql = $sql . "And nc.id_operador = $operador ";
        }
        $sql = $sql . "ORDER BY nc.fecha_ultima_recepcion DESC;";

        $resultado = $this->db->query($sql);
        return $resultado->result_array();
    }    
    /*************************************************/
    /*** Obtiene los operadores activo de new_chat ***/
    /*************************************************/
    public function getOperadores() {
        $sql = "Select nc.id_operador, op.nombre_apellido
            FROM chat.new_chats nc
                INNER JOIN gestion.operadores op ON nc.id_operador = op.idoperador
            WHERE id_operador IS NOT NULL
                AND op.estado = 1
            GROUP BY nc.id_operador;";
        $operadores = $this->db->query($sql);
        return $operadores->result_array();
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
    
    public function checkCredito($documento)
    {
        $sql = "SELECT 
                cre.id, 
                cre.estado
            FROM 
                maestro.clientes AS c 
                JOIN maestro.creditos AS cre ON cre.id_cliente = c.id 
                JOIN maestro.credito_detalle AS cre_d ON cre_d.id_credito = cre.id 
            WHERE 
                cre.estado IN ('vigente','mora') AND
                c.documento = '$documento'";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function checkRetanqueo($documento)
    {
        $sql = "SELECT IF( s.fecha_alta > cre.fecha_otorgamiento, 'retanqueo', '' ) AS tipo_solicitud 
        FROM solicitudes.solicitud s 
        JOIN maestro.clientes c ON c.documento = s.documento 
        JOIN maestro.creditos cre ON cre.id_cliente = c.id 
        WHERE s.documento = '$documento'  
        ORDER BY `s`.`id`  DESC";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function checkChatPendiente($senderNumber, $receiverNumber)
    {
        $this->db->select('id,status_chat,documento,id_operador,fecha_ultimo_envio');
        $this->db->from('new_chats');
        $this->db->where('from', (string)$senderNumber);
        $this->db->where('to', (string)$receiverNumber);
        $this->db->where('status_chat', 'pendiente');
        $query = $this->db->get();
        $result = $query->row();
        if ($this->db->affected_rows() > 0) {

            if ( !empty($result->documento)) {
                $estado_credito =  $this->checkCredito($result->documento);
                $retanqueo = $this->checkRetanqueo($result->documento);
                if (!empty($estado_credito)) {
                    return true;
                } elseif (isset($retanqueo->tipo_solicitud) && $retanqueo->tipo_solicitud == 'retanqueo') {
                    return  true;
                } else {
                    return  false;
                }
            }else{
                return false;
            }   
            
        } else {
            return false;
        }
    }
	
	
	/**
	 * Obtiene los templates para whatsapp
	 * 
	 * @return array
	 */
	public function getWhatsappTemplates()
	{
		return $this->getTemplates(self::CHAT_TEMPLATE_TYPE_WHATSAPP);
	}
	
	
	/**
	 * Obtiene los templates para SMS
	 * 
	 * @return array
	 */
	public function getSMSTemplates()
	{
		return $this->getTemplates(self::CHAT_TEMPLATE_TYPE_SMS);
	}
	
	
	/**
	 * Obtiene los template para emails
	 * 
	 * @return array
	 */
	public function getEmailTemplates()
	{
		$dbCampanias = $this->load->database('campanias',true);
		
		$dbCampanias->select('*')
		->from('campanias_mail_templates')
		->where('estado', 1)
		->where("canal like '%2%'");
		$query = $dbCampanias->get();
		$result = $query->result_array();
		
		return $result;
	}
	
	/**
	 * Obtiene los templates de la base de datos
	 * 
	 * @param null $tipo
	 *
	 * @return array
	 */
	private function getTemplates($tipo = null)
	{
		$this->db->select('*')
			->from('templates')
			->where('estado', 1)
			->where("canal like '%2%'");
		
		if (!is_null($tipo)) {
			$this->db->where('tipo_template', $tipo);
		}
		$this->db->order_by('grupo');
		$this->db->order_by('id');
		
		$query = $this->db->get();
		$result = $query->result_array();
		
		return $result;
	}
	
	/**
	 * Obtiene un tempalte de whatsapp por Id
	 * 
	 * @param $idTemplate
	 *
	 * @return mixed
	 */
	public function getWhatsappTemplate($idTemplate)
	{
		return $this->getTemplateById($idTemplate);
	}
	
	/**
	 * Obtiene un tempalte de SMS por Id
	 * 
	 * @param $idTemplate
	 *
	 * @return mixed
	 */
	public function getSmsTemplate($idTemplate)
	{
		return $this->getTemplateById($idTemplate);
	}
	
	/**
	 * Obtiene un template de email por id
	 * 
	 * @param $idTemplate
	 *
	 * @return mixed
	 */
	public function getEmailTempalte($idTemplate)
	{
		$dbCampanias = $this->load->database('campanias',true);
		
		$dbCampanias->select('*')
			->from('campanias_mail_templates')
			->where('id', $idTemplate);
		
		$query = $dbCampanias->get();
		$result = $query->result_array();
		
		return $result;
	}
	
	/**
	 * Obtiene un template por id
	 * 
	 * @param $idTemplate
	 *
	 * @return mixed
	 */
	private function getTemplateById($idTemplate)
	{
		$this->db->select('*')
			->from('templates')
			->where('id', $idTemplate);
		
		$query = $this->db->get();
		$result = $query->result_array();
		
		return $result;
	}

    

    public function consultar_info_grupo($grupos,$origen)
    {   
        

        $this->db_parametria->select('*');
        $this->db_parametria->from('grupos_filtros_notificaciones');
        $this->db_parametria->where('id_grupo_notificacion IN ('.$grupos.")");
        $this->db_parametria->where('origen',$origen);
        // var_dump( $this->db_parametria->get_compiled_select());die;

        $query = $this->db_parametria->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    public function insert_track_notificaciones($data_track)
    {
        $this->db_gestion->insert('track_notificaciones', $data_track);
        // print_r ($this->db_gestion->last_query());die;
        if ($this->db_gestion->affected_rows() > 0) {
            return true;
            
        }
        else{
            return false;
        }
    }

    public function search_data_chat($id_chat,$id_operador)
    {
        $this->db->select('`c`.`from` AS `numero_client`, CONCAT(c.nombres, c.apellidos) AS nombre_cliente, `c`.`documento`, (select nombre_apellido from gestion.operadores where idoperador = '.$id_operador.') AS `operador`');
        $this->db->from('chat.new_chats c');
        $this->db->where("c.id", $id_chat);

        //  var_dump( $this->db->get_compiled_select());die;

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    public function consult_operadores($id_operador)
    {
        $this->db->select('idoperador, chatfilter  ');
        $this->db->from('gestion.users_servicios');
        $this->db->where("idoperador", $id_operador);
        $this->db->where("chatfilter", 1);

        //  var_dump( $this->db->get_compiled_select());die;

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else{
            return 0;

        }
    }
    public function search_data_agenda_num($from)
    {

        $this->db_maestro->select('A.contacto, C.documento');
        $this->db_maestro->from('maestro.agenda_telefonica A');
        $this->db_maestro->join("maestro.clientes C", "A.id_cliente = C.id");
        $this->db_maestro->where("A.numero", $from);
        $this->db_maestro->where("A.fuente", "PERSONAL");

        //  var_dump( $this->db_maestro->get_compiled_select());die;

        $query = $this->db_maestro->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else{
            return $arrayData=array(["contacto"=>"Visitante","documento"=>"No disponible"]);

        }
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
	
	public function getChatInfoByIdSolicitud($idSolicitud, $canal, $idTemplate)
	{
		$rtrn = ['idSolicitud' => $idSolicitud];
		
		$querySolicitud = $this->db_solicitudes->select('*')
			->from('solicitud')
			->where('id', $idSolicitud)
			->get()->row_array();
		
		if (!empty($querySolicitud)) {
			$chats = $this->getLastChatByClienteDocument($querySolicitud['documento']);

			$idChat = 0;
			foreach ($chats as $chat) {
				if ($chat['to'] == $canal) {
					$idChat = $chat['id'];
					break;
				}
			}
			if ($idChat !== 0) {
				$enviados = $this->getSentMessages([
					'id_chat' => $idChat,
					'id_template' => $idTemplate
				]);
				
				if (!empty($enviados)) {
					$rtrn = [
						'idSolicitud' => $idSolicitud,
						'documento' => $querySolicitud['documento'],
						'sms_status' => $enviados[0]['sms_status'],
						'template' => $enviados[0]['body'],
						'telefono' => $enviados[0]['from'],
						'fecha' => $enviados[0]['fecha_creacion'],
					];
				}
			}
		}
		
		return $rtrn;
		
	}

    public function search_chats_asign_by_ope($id_opeador)
    {
        $this->db_gestion->select('id_operador');
        $this->db_gestion->from('gestion.operadores_gestion_chat');
        $subQuery= $this->db_gestion->get_compiled_select();

        $this->db->select('CR.id id_credito,C.id as id_cliente,CR.estado as estado_credito,S.id id_solicitud,S.paso,S.estado as estado_solicitud, T.tiempo_espera, T.tiempo_respuesta, N.*');
        $this->db->from('chat.new_chats N');
        $this->db->join('maestro.clientes C' , "N.documento = C.documento", 'left');
        $this->db->join('solicitudes.solicitud S' , "C.id = S.id_cliente", 'left');
        $this->db->join('maestro.creditos CR' , "C.id = CR.id_cliente", 'left');
        $this->db->join('chat.tiempos_gestion T', "N.prioridad_gestion = T.id", 'left');
        $this->db->where("N.id_operador IN ($subQuery)", null, false);
        $this->db->where("N.status_chat", "activo");
        $this->db->where("N.id_operador", $id_opeador);
        $this->db->where("N.prioridad_gestion > 0");
        $this->db->where("N.documento IS NOT NULL");
        $this->db->where("N.id NOT in (select id_chat from chat.track_chatuac_abierto)");
        $this->db->where("N.fecha_ultima_recepcion > N.fecha_ultimo_envio");

        
        $this->db->order_by("N.prioridad_gestion, N.fecha_ultima_recepcion", "DESC"); 
        $this->db->group_by("N.documento");
        $this->db->limit(1);
        $query = $this->db->get();
        // echo "<pre>";
        // print_r($this->db->last_query());     
        // echo "</pre>";
        // die;
        return $query->result_array();

    }

    public function insert_temp_chat($dataArray)
    {

        $this->db->select('id_operador');
        $this->db->from('chat.track_chatuac_abierto');
        $this->db->where("id_chat", $dataArray['id_chat']);

        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            $this->db->insert('chat.track_chatuac_abierto', $dataArray);
            $this->db->insert_id();

            if($this->db->affected_rows() > 0){
                return true;
            } else{
                return false;
            }
        }
        return false;
       
    }

    public function delete_temp_chat($id_chat)
    {
        $this->db->delete('chat.track_chatuac_abierto', "fecha_registro <= '". date('Y-m-d H:i:s' , strtotime ( "-60 minutes" , strtotime(date("Y-m-d H:i:s"))))."'" );
        $this->db->where("id_chat", $id_chat);
        $this->db->delete('chat.track_chatuac_abierto');

        if($this->db->affected_rows() > 0){
            return true;
        } else{
            return false;
        }
    }

    public function update_prioridad($id_chat){
        $data = ["prioridad_gestion" => 0];

        $this->db->where('id', (int)$id_chat);
        $this->db->update('new_chats', $data);
    }

    public function getChatwithSolicitud($id_chat)
    {
       
        $this->db_gestion->select('id_operador');
        $this->db_gestion->from('gestion.operadores_gestion_chat');
        $subQuery= $this->db_gestion->get_compiled_select();

        $this->db->select('CR.id id_credito,C.id as id_cliente,CR.estado as estado_credito,S.id id_solicitud,S.paso,S.estado as estado_solicitud, T.tiempo_espera, T.tiempo_respuesta, N.*');
        $this->db->from('chat.new_chats N');
        $this->db->join('maestro.clientes C' , "N.documento = C.documento", 'left');
        $this->db->join('solicitudes.solicitud S' , "C.id = S.id_cliente", 'left');
        $this->db->join('maestro.creditos CR' , "C.id = CR.id_cliente", 'left');
        $this->db->join('chat.tiempos_gestion T', "N.prioridad_gestion = T.id", 'left');
        $this->db->where("N.id_operador IN ($subQuery)", null, false);
        $this->db->where("N.status_chat", "activo");
        $this->db->where("N.id", $id_chat);
        $this->db->where("N.prioridad_gestion > 0");
        $this->db->where("N.documento IS NOT NULL");
        
        $this->db->order_by("N.prioridad_gestion, N.fecha_ultima_recepcion", "DESC"); 
        $this->db->group_by("N.documento");
        // echo "<pre>";
        // print_r( $this->db->get_compiled_select());die;
        // echo "</pre>";
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;      
        return $query->result_array();
    }

}

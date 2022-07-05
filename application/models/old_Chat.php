<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\PusherException;

defined('BASEPATH') OR exit('No direct script access allowed');

class old_Chat extends Orm_model
{
    public $provider;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'chats';
        $this->columns   = [
            'id',
            'proveedor',
            'id_agente',
            'id_visitante',
            'id_sesion',
            'comentario',
            'rating',
            'duracion',
            'id_departamento',
            'fecha_inicio',
            'fecha_finalizacion',
            'perdido',
            'iniciado_por',
            'sin_leer',
            'third_party_id',
            'third_party_agent_id',
            'third_party_visitor_id',
            'third_party_session_id',
            'offline_message',
            'status_chat',
            'id_cliente',
            'type'
        ];
        $this->load->helper('dispatcher');
        $this->dbName = 'chat';
        $this->db     = $this->load->database('chat', true);
    }
    /**
     * Columns in the table are:
     * id
     * proveedor
     * id_agente
     * id_visitante
     * id_sesion
     * comentario
     * rating
     * duracion
     * id_departamento
     * fecha_inicio
     * fecha_finalizacion
     * perdido
     * iniciado_por
     * sin_leer
     * third_party_id
     * third_party_agent_id
     * third_party_visitor_id
     * third_party_session_id
     * offline_message
     * status_chat
     * id_cliente
     * type
     */

    /**
     * Get agents from provider API
     *
     * @return stdClass
     * @throws GuzzleException
     */
    public function getChatsFromProviderApi(): \stdClass
    {
        if ($this->provider === 'ZenDesk') {
            $dotEnv = Dotenv\Dotenv::create(FCPATH);
            $dotEnv->load();

            $client   = new Client();
            $response = $client->request(
                'GET',
                'https://www.zopim.com/api/v2/chats',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' .
                            getenv('ZEN_ATOKEN')
                    ]
                ]
            );

            return (object)json_decode($response->getBody()->getContents(), false)->chats;
        }

        return (object)['error' => 'Provider not found'];
    }

    /**
     * Create or Update a new record in the DB
     *
     * @param stdClass $object
     * @param bool $update
     * @param bool $regMsg
     *
     * @return bool
     * @throws GuzzleException
     * @throws PusherException
     */
    public function modelDbAction(stdClass $object, bool $update = false, bool $regMsg = true): bool
    {
        $startDateDB   = isset($object->timestamp) ? Carbon::createFromFormat(
            'Y-m-d\TH:i:s\Z',
            $object->timestamp
        )->format('Y-m-d H:i:s') : Carbon::now()->format('Y-m-d H:i:s');
        $endDateDB     = isset($object->end_timestamp) ? Carbon::createFromFormat(
            'Y-m-d\TH:i:s\Z',
            $object->end_timestamp
        )->format('Y-m-d H:i:s') : null;
        $visitorID     = null;
        $type          = 'general';
        $visitorObject = $this->getVisitorObject($object);

        if (isset($object->visitor_id)) {
            /**
             * Si ya viene definido un visitor_id, ya se estableció el visitante, se obtiene la ID y se cambia el
             * tipo de chat a Cliente.
             */
            $visitorID = $object->visitor_id;

            if ($visitorObject !== null && $visitorObject['visitor']->solicitud_id !== null && $visitorObject['visitor']->solicitud_id !== '') {
                $type = 'cliente';
            }
        } else if ($visitorObject !== null) {
            /**
             * Si no esta previamente definido, se deben hacer una validaciones extras.
             */
            $visitorID = $visitorObject['id'];

            if (($visitorObject['visitor']->solicitud_id !== null && $visitorObject['visitor']->solicitud_id !== '') ||
                (isset($visitorObject['visitor']->request_id) && $visitorObject['visitor']->request_id !== '')) {
                $type = 'cliente';
            }
        }

        /**
         * Al enviar un template de iniciar conversación se establece previamente el id del agente para levantar
         * un nuevo chat.
         */
        if (isset($object->agent_id)) {
            $this->load->model('operatorRelation', '', true);
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

        $data = [
            'proveedor'              => $this->provider ?? -1,
            'id_agente'              => $object->agent_id ?? null,
            'id_visitante'           => $visitorID,
            /**
             * id_cliente nunca se usó, el sistema terminó migrando a id_visitante, esa tabla almacena datos mirrors
             * de los clientes en solicitudes.
             **/
            'id_cliente'             => null,
            'id_sesion'              => null,
            'comentario'             => $object->comment ?? '',
            'rating'                 => $object->rating ?? 0,
            'duracion'               => 0,
            'id_departamento'        => $object->department_id ?? -1,
            'fecha_inicio'           => $startDateDB,
            'fecha_finalizacion'     => $endDateDB,
            'status_chat'            => $endDateDB ? 'cerrado' : 'activo',
            'perdido'                => $object->missed ?? false,
            'iniciado_por'           => $object->started_by ?? 'visitor',
            'sin_leer'               => $object->unread ?? false,
            'third_party_id'         => $object->id ?? null,
            'third_party_agent_id'   => $object->agent_ids[0] ?? null,
            'third_party_visitor_id' => $object->visitor->id ?? null,
            'third_party_session_id' => $object->session->id ?? null,
            'type'                   => $object->type !== 'chat' ? $object->type : $type
        ];

        if ($object->type === 'offline_msg') {
            $data['offline_message'] = $object->message;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert('chat.' . $this->tableName, $data);

        $lastID = $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        if (isset($lastID) && $object->type === 'chat') {
            /**
             * Si se envia un template, ya hay el operador asignado, se envia la notificación de inmediato.
             */
            if (isset($object->agent_id)) {
                assignNewChat($visitorID, $lastID, $object->agent_id, $visitorObject['visitor'], $object->Body);
            }

            if ($regMsg) {
                $this->registerMessages($lastID, $object);
            }
        }

        return true;
    }

    /**
     * @param $id
     * @param $data
     * @return bool
     */
    public function doUpdate($id, $data): bool
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->update('chat.' . $this->tableName, $data, ['id' => $id]);

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
        $this->load->model('visitor', '', true);
        /** @var Visitor $model */
        $model = $this->visitor;
        $model->setProvider($this->provider);

        if (isset($object->visitor)) {
            if ($this->provider) {
                $visitor         = (object)$model->getVisitorFromProviderApi($object->visitor->id);
                $object->visitor = $model->normalizeObject($visitor);
                $visitor         = $model->findBy($object->visitor->id, $object->visitor);

                return $visitor ? ['id' => $visitor->id, 'visitor' => $visitor] : null;
            }

            $visitor = $model->findBy($object->visitor->id, $object->visitor, true);

            return $visitor ? ['id' => $visitor->id, 'visitor' => $visitor] : null;
        }

        //TODO optimize
        $visitor         = $model->findByNumber($object->From);
        $requestByNumber = $model->findInRequest($object->From);

        if ($visitor === null) {
            //NewVisitor
            $visitorToDB = $model->normalizeObject($object);
            if ($requestByNumber !== null) {
                $visitorToDB = $model->relateRequestData($requestByNumber, $visitorToDB);
            }
            //Check Visitors on DB
            if ($visitorToDB !== null) {
                return ['id' => $model->modelDbAction($visitorToDB, false, true), 'visitor' => $visitorToDB];
            }
        } else {
            if ($requestByNumber !== null) {
                $carbonDate = null;

                if ($requestByNumber->fecha_expedicion !== null && !empty($requestByNumber->fecha_expedicion) && $requestByNumber->fecha_expedicion !== '0000-00-00') {
                    $carbonDate = Carbon::createFromFormat('Y-m-d', $requestByNumber->fecha_expedicion)->format('Y-m-d H:i:s');
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
            $this->load->model('session', '', true);
            /** @var Session $model */
            $model = $this->session;
            $model->setProvider($this->provider);
            $object->session = $model->normalizeObject($object->session);

            if ($this->provider) {
                return $model->findBy($object->session->id, $object->session);
            }

            return $model->findBy($object->session->id, $object->session, true);
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
     * @param int $chatID
     * @param stdClass $object
     *
     * @param stdClass|null $activeChatData
     * @throws GuzzleException
     * @throws PusherException
     */
    public function registerMessages(int $chatID, stdClass $object, stdClass $activeChatData = null): void
    {
        if ($this->provider === 'ZenDesk') {
            $this->load->model('message', '', true);
            foreach ($object->history as $message) {
                if ($message->type === 'chat.msg') {
                    /** @var Message $model */
                    $model = $this->message;
                    $model->setProvider($this->provider);
                    $model->fingerprint = $model->getFingerprint($message);

                    if ($this->provider) {
                        $model->findBy($model->fingerprint, $message, $chatID);
                    }

                    $model->findBy($model->fingerprint, $message, $chatID, true);
                }
            }
        }

        if ($this->provider === 'Twilio_WS') {
            $this->load->model('receivewhatsapp', '', true);
            /** @var Receivewhatsapp $modelReceiveWhatsApp */
            $modelReceiveWhatsApp = $this->receivewhatsapp;
            $modelReceiveWhatsApp->setProvider($this->provider);
            $modelReceiveWhatsApp->modelCreateDb($object, $chatID);

            $query = $this->db->get_where(
                $this->dbName . '.' . $this->tableName, ['id' => $chatID]
            );

            $i = 0;
            while (is_bool($query) && $i < 3) {
                $query = $this->db->get_where(
                    $this->dbName . '.' . $this->tableName, ['id' => $chatID]
                );
                $i     = $i + 1;
                //Espera 150 milisegundos entre cada intento
                sleep(0.15);
            }

            if ($i < 3) {
                $chat = $query->row();

                if ($chat->id_agente !== null) {
                    $operadorID = $chat->id_agente;
                    newMessageNotification($object, $chatID, $operadorID, $activeChatData);
                }
            }
        }
    }

    //Tools

    /**
     * Get a single agent associated to this Chat
     * @return array
     */
    public function getAgent(): array
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where('agentes', ['id' => $this->id_agente]);

        return $query->row();
    }

    /**
     * Get a single agent associated to this Chat
     * @return array
     */
    public function getVisitor(): array
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where('visitantes', ['id' => $this->id_visitante]);

        return $query->row();
    }

    /**
     * Get a single agent associated to this Chat
     * @return array
     */
    public function getSession(): array
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where('sesiones', ['id' => $this->id_sesion]);

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
    public function findBy($id, bool $local = false)
    {
        /** @var CI_DB_result $query */
        if ($local) {
            $query = $this->db->get_where('chat.' . $this->tableName, ['id' => $id]);
        } else {
            $query = $this->db->get_where('chat.' . $this->tableName, ['third_party_id' => $id]);
        }

        if (!is_bool($query)) {
            return $query->row();
        } else {
            return $query;
        }
    }

    /**
     * @param string $number
     *
     * @return mixed
     */
    public function findBySenderNumber(string $number)
    {
        //TODO optimize
        $this->db->select('chats.*, operadores.*, CONCAT(visitantes.nombres, " ", visitantes.apellidos) as nombre_completo, visitantes.num_celular');
        $this->db->from('chat.chats');
        $this->db->join('chat.visitantes', 'chats.id_visitante=visitantes.id');
        $this->db->join('gestion.operadores', 'chats.id_agente=operadores.idoperador', 'left outer');
        $this->db->where('visitantes.num_celular', $number);
        $this->db->where('chats.status_chat = "activo"');
        $query = $this->db->get();

        $i = 0;
        while (is_bool($query) && $i < 3) {
            $query = $this->db->get();
            $i     = $i + 1;
            //Espera 150 milisegundos entre cada intento
            sleep(0.15);
        }

        if ($i < 3) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * Normalize Chat data into a strClass before interaction with DB
     *
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
        return ['operatorWS', ['id_agente' => 'idoperador'], 'type' => 'one'];
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
        return ['receivewhatsapp', ['id' => 'chat_id'], 'type' => 'many'];
    }

    public function relationSentWhatsApp()
    {
        return ['sendwhatsapp', ['id' => 'chat_id'], 'type' => 'many'];
    }

    /**
     * @param int $id
     * @param bool $missed
     * @param bool $onLogin
     * @return bool
     * @throws PusherException
     */
    public function closeByID(int $id, $missed = false, $onLogin = false): bool
    {
        $chat = $this->findByID($id);
        if ($chat !== null) {
            $this->db->trans_begin();
            $this->db->trans_strict(false);

            $data                       = [];
            $data['status_chat']        = $missed !== false ? 'perdido' : 'cerrado';
            $data['fecha_finalizacion'] = Carbon::now()->format('Y-m-d H:i:s');
            $lastMessageDate            = Carbon::parse($chat['chats']['fecha_inicio']);
            $now                        = Carbon::now();
            $data['duracion']           = $now->diffInMinutes($lastMessageDate);

            $this->db->update('chat.' . $this->tableName, $data, array('id' => $id));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();

                return false;
            }

            $this->db->trans_commit();

            if ($chat['chats']['id_agente'] !== null && $onLogin === false) {
                /** @var OperatorWS $opModel */
                $this->load->model('operatorWS', '', true);
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
                        checkofflinechats($chat['chats']['id_agente'], $limit, $chat['chats']['type']);
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
    public function setOperatorByID($id, int $operatorID): bool
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $data              = [];
        $data['id_agente'] = $operatorID;
        if (is_array($id)) {
            $this->db->update('chat.' . $this->tableName, $data, 'id in (' . implode(',', $id) . ')');
        } else {
            $this->db->update('chat.' . $this->tableName, $data, ['id' => $id]);
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
                if (isset($chat['receive_whatsapp']) && !empty($chat['receive_whatsapp'])) {
                    foreach ($chat['receive_whatsapp'] as $receivedMessage) {
                        $receivedMessage['received'] = true;
                        $receivedMessage['sent']     = false;

                        $messagesChat[] = $receivedMessage;
                    }
                }

                if (isset($chat['send_whatsapp']) && !empty($chat['send_whatsapp'])) {
                    foreach ($chat['send_whatsapp'] as $sentMessage) {
                        $sentMessage['received'] = false;
                        $sentMessage['sent']     = true;

                        $messagesChat[] = $sentMessage;
                    }
                }

                usort($messagesChat, static function ($a, $b) {
                    return $a['fecha_creacion'] <=> $b['fecha_creacion'];
                });

                $chat['messages'] = $messagesChat;
                $array[$key]      = $chat;
            }
        }

        return $array;
    }

    /**
     * @param array $chatArray
     * @return array
     */
    public function findPrevByID(array $chatArray): array
    {
        $manyOpActiveChats = $this->db->query('select chat.chats.id, ' .
                                              'chat.chats.id_agente, ' .
                                              'chat.chats.fecha_inicio, ' .
                                              'chat.chats.fecha_finalizacion, ' .
                                              'gestion.operadores.nombre_apellido as operator_names ' .
                                              'from chat.chats ' .
                                              'left join gestion.operadores on idoperador = id_agente ' .
                                              'where fecha_inicio < "' . $chatArray['chats']['fecha_inicio'] . '"' .
                                              ' and id_visitante = ' . $chatArray['chats']['id_visitante'] .
                                              ' order by fecha_inicio DESC limit 3');

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

            $this->doUpdate($chat->id, $data);

            return true;
        }

        return false;
    }

    /**
     * This function close all outdated chats
     */
    public function closeOutdatedChats(): bool
    {
        $sql   = 'SELECT chats.id, chats.fecha_inicio, rw.max_fecha_creacion as last_received_date
FROM chat.chats
         LEFT JOIN (
    SELECT MAX(chat.receive_whatsapp.fecha_creacion) as max_fecha_creacion,
           chat.receive_whatsapp.chat_id
    FROM chat.receive_whatsapp
    GROUP BY chat_id
) as rw ON chats.id = rw.chat_id
WHERE chats.status_chat = ' . $this->db->escape('activo') . '
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
            $data                       = [
                'status_chat' => 'cerrado'
            ];
            $data['fecha_finalizacion'] = Carbon::now()->format('Y-m-d H:i:s');
            $lastMessageDate            = Carbon::parse($chat['fecha_inicio']);
            $now                        = Carbon::now();
            $data['duracion']           = $now->diffInMinutes($lastMessageDate);

            $this->db->update('chat.' . $this->tableName, $data, array('id' => $chat['id']));
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
        $sql   = 'select chats.id, chats.fecha_inicio, v.num_celular, rw.max_fecha_creacion as last_received_date,'
            . ' rw.body, s.operador_asignado as operador_relacion, v.id as id_visitante,'
            . ' s.nombres, s.apellidos, s.telefono as num_celular'
            . ' from chat.chats'
            . ' left join chat.visitantes v on chats.id_visitante = v.id'
            . ' left join ('
            . ' select MAX(chat.receive_whatsapp.fecha_creacion) as max_fecha_creacion, chat.receive_whatsapp.chat_id,'
            . ' chat.receive_whatsapp.body'
            . ' from chat.receive_whatsapp group by chat_id'
            . ' ) as rw on chats.id = rw.chat_id'
            . ' left join solicitudes.solicitud s on s.telefono = v.num_celular and operador_asignado != 0'
            . " where chats.status_chat = 'activo' and chats.id_agente is null"
            . ' order by chats.fecha_inicio';
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
     * @return array
     */
    public function getChatsActiveByOp(int $idOp): array
    {
        $sql   = 'select chats.id as id, chats.id as chat_id,'
            . ' v.nombres, v.apellidos, v.telefono as num_celular'
            . ' from chat.chats'
            . ' left join chat.visitantes v on chats.id_visitante = v.id'
            . " where chats.status_chat = 'activo' and chats.id_agente = " . $idOp
            . ' order by chats.fecha_inicio';
        $query = $this->db->query($sql);

        if (!is_bool($query)) {
            return $query->result_array();
        }


        return [];
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
        $onlineOperatorsList = getOnlineOperatorsList();

        foreach ($queueChats as $chat) {
            $assignation = getFreeOperatorID($chat, $onlineOperatorsList);

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
                'id_agente' => $assignation['operatorID']
            ];

            $this->db->update('chat.' . $this->tableName, $data, array('id' => $assignation['chatID']));
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        foreach ($chatsAssignations as $assignation) {
            $visitor = [
                'nombres'     => $assignation['chat']['nombres'],
                'apellidos'   => $assignation['chat']['apellidos'],
                'num_celular' => $assignation['chat']['num_celular']
            ];
            assignNewChat(
                $assignation['chat']['id_visitante'],
                $assignation['chatID'],
                $assignation['operatorID'],
                $visitor,
                $assignation['chat']['body']
            );
        }

        return null;
    }

}

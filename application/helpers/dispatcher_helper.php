<?php
/**
 * @author   Ignacio Salcedo <ignacio.salcedo@solventa.com>
 */

use Carbon\Carbon;
use Pusher\PusherException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @param string $idVisitor
 * @param $chatID
 * @param $operatorID
 * @param $visitor
 * @param string $msg
 *
 * @throws PusherException
 */
function assignNewChat(string $idVisitor, $chatID, $operatorID, $visitor, string $msg)
{
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    $pusher = new Pusher\Pusher(
        getenv('PUSHER_KEY'),
        getenv('PUSHER_SECRET'),
        getenv('PUSHER_APP_ID'),
        ['cluster' => getenv('PUSHER_CLUSTER')]
    );

    if (is_array($visitor)) {
        $nombreCompleto = $visitor['nombres'] . ' ' . $visitor['apellidos'];
        $numCelular     = $visitor['num_celular'];
    } else {
        $nombreCompleto = $visitor->nombres . ' ' . $visitor->apellidos;
        $numCelular     = $visitor->num_celular;
    }

    $pusher->trigger(
        'operator-' . $operatorID . '-channel',
        'assignations',
        [
            'message'         => 'Ha sido asignado un nuevo Chat',
            'chat_id'         => $chatID,
            'contactID'       => $idVisitor,
            'last_message'    => $msg,
            'nombre_completo' => $nombreCompleto,
            'num_celular'     => $numCelular,
            'def_date'        => Carbon::now()
                                       ->format('Y-m-d H:i:s')
        ]
    );
}

/**
 * @param $chatData
 * @param $chatID
 * @param $operatorID
 * @param stdClass|null $activeChatData
 * @throws PusherException
 */
function newMessageNotification($chatData, $chatID, $operatorID, stdClass $activeChatData = null)
{
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    $pusher = new Pusher\Pusher(
        getenv('PUSHER_KEY'),
        getenv('PUSHER_SECRET'),
        getenv('PUSHER_APP_ID'),
        ['cluster' => getenv('PUSHER_CLUSTER')]
    );

    $pusher->trigger(
        'operator-' . $operatorID . '-channel',
        'notification-chat-' . $chatID,
        [
            'message' => 'Nuevo Mensaje',
            'chatID'  => $chatID
        ]
    );

    if ($activeChatData !== null) {
        $pusher->trigger(
            'operator-' . $operatorID . '-channel',
            'new_message',
            [
                'message'         => 'Nuevo Mensaje',
                'contactID'       => $activeChatData->id_visitante,
                'last_message'    => $chatData->Body,
                'chat_id'         => $chatID,
                'nombre_completo' => $activeChatData->nombre_completo,
                'num_celular'     => $activeChatData->num_celular,
                'def_date'        => Carbon::now()
                                           ->format('Y-m-d H:i:s')
            ]
        );
    }
}

/**
 * @param $chatID
 * @param $operatorID
 *
 * @throws PusherException
 */
function newMessageMonitorNotification($chatID, $operatorID)
{
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    $pusher = new Pusher\Pusher(
        getenv('PUSHER_KEY'),
        getenv('PUSHER_SECRET'),
        getenv('PUSHER_APP_ID'),
        ['cluster' => getenv('PUSHER_CLUSTER')]
    );

    $pusher->trigger(
        'operator-' . $operatorID . '-channel',
        'notification-monitor-chat-' . $chatID,
        [
            'message' => 'Nuevo Mensaje envíado',
            'chatID'  => $chatID
        ]
    );
}

/**
 * Obtiene el operado a asignar para un chat, sea por relación o por aleatoriedad entre los operadores
 * más libres.
 *
 * @param array $chatData
 * @param array|null $onlineOperatorsList
 *
 * @return array
 */
function getFreeOperatorID(
    array $chatData,
    array $onlineOperatorsList = null
): array
{
    $onlineOperators = $onlineOperatorsList;

    if ($onlineOperatorsList === null) {
        $ci =& get_instance();
        //1. Get online operators
        /** @var CI_DB_result $query */
        $ci->load->database();

        $onlineOperators = getOnlineOperatorsList();
    }

    $operatorsIDs = [];

    if (empty($onlineOperators)) {
        return ['id' => null, 'flag' => 'queue'];
    }

    foreach ($onlineOperators as $operator) {
        if ($chatData['operador_relacion'] !== null && (int)$chatData['operador_relacion'] === (int)$operator['operadores']['idoperador']) {
            return ['id' => $operator['operadores']['idoperador'], 'flag' => 'relation'];
        }

        if ($chatData['operador_relacion'] !== null && (int)$chatData['operador_relacion'] !== (int)$operator['operadores']['idoperador']) {
            foreach ($onlineOperators as $innerOperator) {
                if ((int)$chatData['operador_relacion'] === (int)$innerOperator['operadores']['idoperador']) {
                    continue 2;
                }
            }
        }

        $operatorToAr   = [
            'id'         => (int)$operator['operadores']['idoperador'],
            'many_chats' => (int)$operator['many_chats']
        ];
        $operatorsIDs[] = $operatorToAr;
    }

    //Le asigna el chat al último operador en la lista de many
    usort($operatorsIDs, static function ($a, $b) {
        return $a['many_chats'] <=> $b['many_chats'];
    });

    return ['id' => $operatorsIDs[0]['id'], 'flag' => 'assignation'];
}

/**
 * Obtiene el operado a asignar para un chat, sea por relación o por aleatoriedad entre los operadores COBRANZAS
 * más libres.
 *
 * @param array $chatData
 * @param array|null $onlineOperatorsList
 *
 * @return array
 */
function getFreeOperatorID2(
    array $chatData,
    array $onlineOperatorsList = null
): array
{
    $onlineOperators = $onlineOperatorsList;

    if ($onlineOperatorsList === null) {
        $ci =& get_instance();
        //1. Get online operators
        /** @var CI_DB_result $query */
        $ci->load->database();

        $onlineOperators = getOnlineOperatorsList2();
    }

    $operatorsIDs = [];

    if (empty($onlineOperators)) {
        return ['id' => null, 'flag' => 'queue'];
    }

    foreach ($onlineOperators as $operator) {
        if ($chatData['operador_relacion'] !== null && (int)$chatData['operador_relacion'] === (int)$operator['operadores']['idoperador']) {
            return ['id' => $operator['operadores']['idoperador'], 'flag' => 'relation'];
        }

        if ($chatData['operador_relacion'] !== null && (int)$chatData['operador_relacion'] !== (int)$operator['operadores']['idoperador']) {
            foreach ($onlineOperators as $innerOperator) {
                if ((int)$chatData['operador_relacion'] === (int)$innerOperator['operadores']['idoperador']) {
                    continue 2;
                }
            }
        }

        $operatorToAr   = [
            'id'         => (int)$operator['operadores']['idoperador'],
            'many_chats' => (int)$operator['many_chats']
        ];
        $operatorsIDs[] = $operatorToAr;
    }

    //Le asigna el chat al último operador en la lista de many
    usort($operatorsIDs, static function ($a, $b) {
        return $a['many_chats'] <=> $b['many_chats'];
    });

    return ['id' => $operatorsIDs[0]['id'], 'flag' => 'assignation'];
}

/**
 * Retorna una array con la lista de operadores disponibles para asignaciones.
 *
 * @return array
 */
function getOnlineOperatorsList(): array
{
    $ci =& get_instance();
    $ci->load->model('operatorWS', '', true);
    /** @var Chat $model */
    /** @var OperatorWS $opModel */
    $opModel = $ci->operatorWS;
    $opData  = $opModel->findByQueryAnchor(
        '{{prefix}}tipo_operador = 1 and {{prefix}}estado = "1"',
        [
            $opModel->relationAbsences(),
            $opModel->relationChatActive()
        ],
        'idoperador'
    );

    /**
     * Elimina operadores de la lista si están ausentes. Ausencia programada.
     * geastion.ausencias_operadores
     */
    foreach ($opData as $kOp => $operator) {
        if (!empty($operator['ausencias_operadores'])) {
            foreach ($operator['ausencias_operadores'] as $absence) {
                $aStartDate = Carbon::parse($absence['fecha_inicio']);
                $aEndDate   = Carbon::parse($absence['fecha_final']);
                $now        = Carbon::now();
                if ($now->greaterThanOrEqualTo($aStartDate) && $now->lessThanOrEqualTo($aEndDate)) {
                    unset($opData[$kOp]);
                }
            }
        }
    }

    return $opData;
}

/**
 * Retorna una array con la lista de operadores disponibles para asignaciones de operadores de COBRANZAS.
 *
 * @return array
 */
function getOnlineOperatorsList2(): array
{
    $ci =& get_instance();
    $ci->load->model('operatorWS', '', true);
    /** @var Chat $model */
    /** @var OperatorWS $opModel */
    $opModel = $ci->operatorWS;
    $opData  = $opModel->findByQueryAnchor(
        '{{prefix}}tipo_operador IN (5,6) and {{prefix}}estado = "1"',
        [
            $opModel->relationAbsences(),
            $opModel->relationChatActive()
        ],
        'idoperador'
    );

    /**
     * Elimina operadores de la lista si están ausentes. Ausencia programada.
     * geastion.ausencias_operadores
     */
    foreach ($opData as $kOp => $operator) {
        if (!empty($operator['ausencias_operadores'])) {
            foreach ($operator['ausencias_operadores'] as $absence) {
                $aStartDate = Carbon::parse($absence['fecha_inicio']);
                $aEndDate   = Carbon::parse($absence['fecha_final']);
                $now        = Carbon::now();
                if ($now->greaterThanOrEqualTo($aStartDate) && $now->lessThanOrEqualTo($aEndDate)) {
                    unset($opData[$kOp]);
                }
            }
        }
    }

    return $opData;
}

/**
 * @param int $operatorID
 * @param int $limit
 * @param string $queueType
 * @return bool
 * @throws PusherException
 * @deprecated
 */
function checkOfflineChats(int $operatorID, int $limit = 5, string $queueType = '')
{
    /** @var CI_DB_result $query */
    $ci =& get_instance();
    $ci->load->database('chat');
    $ci->load->model('chat', '', true);

    $sql = 'SELECT new_chats.*, visitantes.num_celular, relaciones_agentes_dispatcher.id_agente as relation_id_agente
            FROM chat.new_chats
                LEFT JOIN chat.visitantes 
                    ON new_chats.from = visitantes.num_celular
                LEFT JOIN chat.relaciones_agentes_dispatcher 
                    ON relaciones_agentes_dispatcher.num_celular = visitantes.num_celular
            WHERE new_chats.status_chat = ' . $ci->db->escape('activo') . '
            AND new_chats.id_operador IS NULL 
            GROUP BY new_chats.id';

//    if ($queueType !== null && $queueType !== '') {
//        $sql .= ' AND type = ' . $ci->db->escape($queueType);
//    }

    $sql .= ' ORDER BY chats.fecha_inicio LIMIT ' . $ci->db->escape($limit);
    /** @var CI_DB_mysqli_result $query */
    $offlineChatsQuery = $ci->db->query($sql);

    if (!is_bool($offlineChatsQuery)) {
        $offlineChats = $offlineChatsQuery->result();

        foreach ($offlineChats as $cKey => $chat) {
            if ($chat->relation_id_agente !== null && (int)$chat->relation_id_agente !== $operatorID) {
                unset($offlineChats[$cKey]);
            }
        }

        $initialOfflineChatsCount = count($offlineChats);

        /**
         * Verify the chat type. Some times the type will change from 'general' to 'customer'.
         * Only is possible that a chat can mutate into 'customer' if the previous type was 'general'
         **/
//        if ($queueType === 'general' && $initialOfflineChatsCount !== 0) {
//            foreach ($offlineChats as $oKey => $offlineChat) {
//                $offlineChat->From = $offlineChat->num_celular;
//                $mutation          = $ci->chat->verifyType($offlineChat);
//                if ($mutation === true) {
//                    unset($offlineChats[$oKey]);
//                }
//            }
//        }

        if (!(empty($offlineChats) && $initialOfflineChatsCount === 0)) {
            $chatIDsArr = [];

            foreach ($offlineChats as $cKey => $chat) {
                $chatIDsArr[] = (int)$chat->id;
            }

            $chatIDsStr             = implode(',', $chatIDsArr);
            $lastMessagesDatesQuery = $ci
                ->db
                ->query('SELECT fecha_creacion, id_chat from chat.received_messages WHERE id_chat in (' . $chatIDsStr . ') ORDER BY fecha_creacion DESC');

            if (!is_bool($lastMessagesDatesQuery)) {
                $lastMessagesDatesArr = $lastMessagesDatesQuery->result_array();

                if (!empty($lastMessagesDatesArr)) {
                    $lastMessagesDatesArrByID = [];

                    foreach ($lastMessagesDatesArr as $entry) {
                        $lastMessagesDatesArrByID[$entry['chat_id']] = $entry['fecha_creacion'];
                    }

                    foreach ($offlineChats as $cKey => $chat) {
                        $lastMessageDate = $lastMessagesDatesArrByID[$chat->id] ?? null;

                        if ($lastMessageDate !== null) {
                            $lastMessageDate = Carbon::parse($lastMessageDate);
                            $now             = Carbon::now();
                            $differenceDates = $now->diffInHours($lastMessageDate);

                            if ($differenceDates >= 24) {
                                unset($offlineChats[$cKey]);
                                $ci->chat->closeByID($chat->id, true, true);
                            } else {
                                $ci->chat->setOperatorByID($chat->id, $operatorID);
                                //TODO FIX
                                assignNewChat([], $chat->id, $operatorID);
                            }
                        } else {
                            unset($offlineChats[$cKey]);
                            $ci->chat->closeByID($chat->id, true, true);
                        }
                    }

                    if (count($offlineChats) < $limit) {
                        $newLimit = $limit - count($offlineChats);
                        checkOfflineChats($operatorID, $newLimit, $queueType);
                    }
                }
            }
        }
    } else {
        return false;
    }

    return true;
}

/**
 * Return an empty array or an array of chats IDs
 * @param int $operatorID
 * @param int $limit
 * @param string $queueType
 * @return array
 * @throws PusherException
 */
function checkCustomersChatsFromQueue(int $operatorID, int $limit = 5): ?array
{
    /** @var CI_DB_mysqli_result $offlineChatsQuery */
    /** @var Chat $chatModel */
    $ci =& get_instance();
    $ci->load->database('chat');
    $ci->load->model('chat', '', true);
    $chatModel = $ci->chat;

    $ci->db->trans_start();

    $sql               = 'SELECT new_chats.*, visitantes.num_celular FROM chat.new_chats JOIN chat.visitantes ON new_chats.from = visitantes.num_celular'
        . ' WHERE new_chats.status_chat = ' . $ci->db->escape('activo')
        . ' AND new_chats.id_operador IS NULL AND type = ' . $ci->db->escape('cliente')
        . ' ORDER BY new_chats.fecha_inicio LIMIT ' . $ci->db->escape($limit);
    $offlineChatsQuery = $ci->db->query($sql);

    if (!is_bool($offlineChatsQuery)) {
        $offlineChats = $offlineChatsQuery->result();
        $chatIDsArr   = [];

        if (!empty($offlineChats)) {
            foreach ($offlineChats as $cKey => $chat) {
                $chatIDsArr[] = (int)$chat->id;
            }
            $chatModel->setOperatorByID($chatIDsArr, $operatorID);
        }

        return $chatIDsArr;
    }

    $ci->db->trans_complete();

    //Null is error
    return null;
}

function checkGeneralChatsFromQueue(int $operatorID, int $limit = 5): ?array
{
    /** @var CI_DB_mysqli_result $offlineChatsQuery */
    /** @var Chat $chatModel */
    $ci =& get_instance();
    $ci->load->database('chat');
    $ci->load->model('chat', '', true);
    $chatModel = $ci->chat;

    $ci->db->trans_start();

    $sql               = 'SELECT new_chats.*, visitantes.num_celular FROM chat.new_chats JOIN chat.visitantes ON new_chats.from = visitantes.num_celular'
        . ' WHERE new_chats.status_chat = ' . $ci->db->escape('activo')
        . ' AND new_chats.id_operador IS NULL AND type = ' . $ci->db->escape('general')
        . ' ORDER BY new_chats.fecha_inicio LIMIT ' . $ci->db->escape($limit);
    $offlineChatsQuery = $ci->db->query($sql);

    if (!is_bool($offlineChatsQuery)) {
        $offlineChats = $offlineChatsQuery->result();
        $chatIDsArr   = [];

        if (!empty($offlineChats)) {
            foreach ($offlineChats as $cKey => $chat) {
                $chatIDsArr[] = (int)$chat->id;
            }
            $chatModel->setOperatorByID($chatIDsArr, $operatorID);
        }

        return $chatIDsArr;
    }

    $ci->db->trans_complete();

    //Null is error
    return null;
}

function checkAllChatsFromQueue(int $operatorID, int $limit = 5): ?array
{
    /** @var CI_DB_mysqli_result $offlineChatsQuery */
    /** @var Chat $chatModel */
    $ci =& get_instance();
    $ci->load->database('chat');
    $ci->load->model('chat', '', true);
    $chatModel = $ci->chat;

    $ci->db->trans_start();

    $sql = 'SELECT new_chats.*, visitantes.num_celular, relaciones_agentes_dispatcher.id_agente as relation_id_agente
            FROM chat.new_chats
                LEFT JOIN chat.visitantes 
                    ON new_chats.from = visitantes.num_celular
                LEFT JOIN chat.relaciones_agentes_dispatcher 
                    ON relaciones_agentes_dispatcher.num_celular = visitantes.num_celular
            WHERE new_chats.status_chat = ' . $ci->db->escape('activo') . '
            AND new_chats.id_operador IS NULL 
            GROUP BY new_chats.id 
            ORDER BY new_chats.fecha_inicio LIMIT ' . $ci->db->escape($limit);

    $offlineChatsQuery = $ci->db->query($sql);

    if (!is_bool($offlineChatsQuery)) {
        $offlineChats = $offlineChatsQuery->result();
        $chatIDsArr   = [];

        if (!empty($offlineChats)) {
            foreach ($offlineChats as $cKey => $chat) {
                if ($chat->relation_id_agente === null || (int)$chat->relation_id_agente === $operatorID) {
                    $chatIDsArr[] = (int)$chat->id;
                }
            }
            $chatModel->setOperatorByID($chatIDsArr, $operatorID);
        }

        return $chatIDsArr;
    }

    $ci->db->trans_complete();

    //Null is error
    return null;
}

/**
 * @return array
 * @deprecated
 */
function getTemplatesList()
{
    return [
        '1' => 'Hola $receiverName, soy $senderName de Solventa Prestamos. Tu CREDITO esta APROBADO, escríbenos ahora a este WhatsApp o llámanos al Teléfono: (300) 929-4761 o por SMS al 350-5016 986 así desembolsamos en tu cuenta bancaria. Por tu seguridad queremos validar unos datos y acreditarte el dinero en el dia. Feliz día y Saludos!'
    ];
}

/**
 * @param string $templateID
 * @return string|bool
 */
function getTemplatesByTwilioID(string $templateID)
{
    /**
     * @var CI_DB_mysqli_driver $db
     * @var CI_DB_mysqli_result $query
     */
    $ci =& get_instance();
    $ci->load->database();
    $db    = $ci->db;
    $query = $db->query(
        'SELECT msg_string from chat.templates WHERE id_template_twilio = "' . $templateID . '"'
    );

    if (!is_bool($query)) {
        return $query->row();
    }

    return false;
}

/**
 * Organiza el template, la data suministrada y en ultimas, envía el mensaje desde la SDK de twilio.
 *
 * @param string $template
 * @param stdClass $request
 * @param int|null $chatID
 * @param int|null $operatorID
 * @return bool
 * @throws ConfigurationException
 * @throws TwilioException
 */
function sendTemplate(string $template, stdClass $request, int $chatID = null, $operatorID = null): bool
{
    $ci =& get_instance();
    $ci->load->database();
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    //Process Template
    $ci->load->helper('cookie');
    $templateString = getTemplatesByTwilioID($template);
    // $sessionCookie  = get_cookie('__data_operator');
    if ($operatorID !== null && $operatorID !== false && $templateString !== null) {
    // if ($sessionCookie !== null && $templateString !== false && $templateString !== null) {
        // $sessionCookie = explode(',', $sessionCookie);
        // $operatorID    = (int)$sessionCookie[0];
        $operator      = $ci->db->get_where('gestion.operadores', ['idoperador' => $operatorID]);
        $operator      = $operator->row();

        //Fix space before coma (,)
        $receiverName = explode(' ', trim($request->nombres))[0];

        if ($receiverName === '') {
            $receiverName = '👋';
        }
        //END fix

        $templateData = [
            '{{1}}' => $receiverName,
            '{{2}}' => explode(' ', trim($operator->nombre_apellido))[0],
            '{{3}}' => '(1)2193093'
        ];
        $message      = strtr($templateString->msg_string, $templateData);
        $sid          = getenv('TWILIO_SID');
        $token        = getenv('TWILIO_AUTH');
        $twilio       = new Client($sid, $token);
        $sendData     = [
            'from' => 'whatsapp:' . getenv('WS_NUMBER'),
            'body' => $message
        ];

        if (strpos($request->telefono, '+') === 0) {
            $whatsAppNumber = 'whatsapp:' . $request->telefono;
        } else {
            $whatsAppNumber = 'whatsapp:+57' . $request->telefono;
        }

        $message = $twilio->messages
            ->create($whatsAppNumber, $sendData);

        if ($chatID) {
            $ci->load->model('sentwhatsapp', '', true);
            /** @var Sentwhatsapp $modelSentWhatsapp */
            $modelSentWhatsapp = $ci->sentwhatsapp;
            $modelSentWhatsapp->modelCreateDb((object)$message->toArray(), $chatID, $operatorID);
        } else {
            $ci->load->model('sentwhatsapp', '', true);
            /** @var Sentwhatsapp $modelSentWhatsapp */
            $modelSentWhatsapp = $ci->sentwhatsapp;
            $modelSentWhatsapp->modelCreateDb((object)$message->toArray());
        }

        return true;
    }

    return false;
}



/**
 * Organiza el template, la data suministrada y en ultimas, envía el mensaje desde la SDK de twilio.
 *
 * @param stdClass $template
 * @param stdClass $request
 * @param int|null $chatID
 * @param int|null $operatorID
 * @return bool
 * @throws ConfigurationException
 * @throws TwilioException
 */
function sendTemplateNew($template,  $request, int $chatID = null, $operatorID = null): bool
{   
    $ci =& get_instance();
    $ci->load->database();
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    //Process Template
    $ci->load->helper('cookie');    
    // $sessionCookie  = get_cookie('__data_operator');

    if ($operatorID !== null && $operatorID !== false && $template !== null) {
        // print("entro");
        $sid          = getenv('TWILIO_SID');
        $token        = getenv('TWILIO_AUTH');
        $twilio       = new Client($sid, $token);
        $sendData     = [
            'from' => 'whatsapp:' . getenv('WS_NUMBER'),
            'body' => $template['template']
        ];
    
        if (strpos($request, '+') === 0) {
            $whatsAppNumber = 'whatsapp:' . $request;
        } else {
            $whatsAppNumber = 'whatsapp:+57' . $request;
        }

        $message = $twilio->messages->create($whatsAppNumber, $sendData);

        $x = (object)$message->toArray();
        $x->id_template = $template['id_template'];

        if ($chatID) {
            $ci->load->model('sentwhatsapp', '', true);
            /** @var Sentwhatsapp $modelSentWhatsapp */
            $modelSentWhatsapp = $ci->sentwhatsapp;
            $sentMessageEntity = $modelSentWhatsapp->modelCreateDb((object)$x, $chatID, $operatorID, true, true);
        } else {
            $ci->load->model('sentwhatsapp', '', true);
            /** @var Sentwhatsapp $modelSentWhatsapp */
            $modelSentWhatsapp = $ci->sentwhatsapp;
            $sentMessageEntity = $modelSentWhatsapp->modelCreateDb((object)$x, null, null, true,true);
        }

        $ci->load->model('chat', '', true);
        $model = $ci->chat;

        $sentMensaje = [];
        if(!empty($sentMessageEntity))
            $sentMensaje = $model->getSentMessages(['id_chat' => $chatID, 'id_sent' => $sentMessageEntity['lastID']]);
  
        if (!empty($sentMensaje)) {
            
            //pusher
            $pusher = new Pusher\Pusher(
                getenv('PUSHER_KEY'),
                getenv('PUSHER_SECRET'),
                getenv('PUSHER_APP_ID'),
                ['cluster' => getenv('PUSHER_CLUSTER')]
            );
            
            $res = $pusher->trigger(
                'channel-chat-'.$chatID,
                'sent-message-component',
                [
                    'body' => $sentMensaje[0]['body'],
                    'received' => 0,
                    'sent' => 1,
                    'media_url0' => $sentMensaje[0]['media_url0'],
                    'media_content_type0' => $sentMensaje[0]['media_content_type0'],
                    'fecha_creacion' =>  $sentMensaje[0]['fecha_creacion'],
                    'nombre_apellido_operador' => $sentMensaje[0]['nombre_apellido'],
                    'id_chat' => $chatID,
                    'sms_status' => $sentMensaje[0]['sms_status'],
                    'sms_sid' => $sentMensaje[0]['sms_message_sid'],
                    'new_status_chat' => $sentMensaje[0]['status_chat'], 
                ]);
        }
        
        return true;
    }

    return false;
}




/**
 * Organiza el template, la data suministrada y en ultimas, envía el mensaje desde la SDK de twilio.
 * Solo para cobranzas
 * @param string $template
 * @param stdClass $request
 * @param int|null $chatID
 * @param int|null $operatorID
 * @return bool
 * @throws ConfigurationException
 * @throws TwilioException
 */
function sendTemplate2(string $template, stdClass $request, int $chatID = null, $operatorID = null): bool
{

    $ci =& get_instance();
    $ci->load->database();
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    //Process Template
    $ci->load->helper('cookie');
    $templateString = getTemplatesByTwilioID($template);
    if ($operatorID !== null && $operatorID !== false && $templateString !== null) {

        $operator      = $ci->db->get_where('gestion.operadores', ['idoperador' => $operatorID]);
        $operator      = $operator->row();

        //Fix space before coma (,)
        $receiverName = explode(' ', trim($request->nombres))[0];

        if ($receiverName === '') {
            $receiverName = '👋';
        }
        //END fix

        $templateData = [
            '{{1}}' => $receiverName,
            '{{2}}' => explode(' ', trim($operator->nombre_apellido))[0],
            '{{3}}' => '(1)7945528'
        ];
        $message      = strtr($templateString->msg_string, $templateData);
        $sid          = getenv('TWILIO_SID');
        $token        = getenv('TWILIO_AUTH');
        $twilio       = new Client($sid, $token);
        $sendData     = [
            'from' => 'whatsapp:' . getenv('WS_NUMBER2'),
            'body' => $message
        ];

        if (strpos($request->telefono, '+') === 0) {
            $whatsAppNumber = 'whatsapp:' . $request->telefono;
        } else {
            $whatsAppNumber = 'whatsapp:+57' . $request->telefono;
        }

        $message = $twilio->messages
            ->create($whatsAppNumber, $sendData);

        if ($chatID) {
            $ci->load->model('sentwhatsapp', '', true);
            /** @var Sentwhatsapp $modelSentWhatsapp */
            $modelSentWhatsapp = $ci->sentwhatsapp;
            $modelSentWhatsapp->modelCreateDb((object)$message->toArray(), $chatID, $operatorID);
        } else {
            $ci->load->model('sentwhatsapp', '', true);
            /** @var Sentwhatsapp $modelSentWhatsapp */
            $modelSentWhatsapp = $ci->sentwhatsapp;
            $modelSentWhatsapp->modelCreateDb((object)$message->toArray());
        }

        return true;
    }

    return false;
}


/**
 * Organiza el template, la data suministrada y en ultimas, envía el mensaje desde la SDK de twilio.
 * Solo para cobranzas
 * @param stdClass $template
 * @param stdClass $request
 * @param int|null $chatID
 * @param int|null $operatorID
 * @return bool
 * @throws ConfigurationException
 * @throws TwilioException
 */
function sendTemplate2New($template, $request, int $chatID = null, $operatorID = null): bool
{

    $ci =& get_instance();
    $ci->load->database();
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    //Process Template
    $ci->load->helper('cookie');

    if ($operatorID !== null && $operatorID !== false && $template !== null) {

        $sid          = getenv('TWILIO_SID');
        $token        = getenv('TWILIO_AUTH');
        $twilio       = new Client($sid, $token);
        $sendData     = [
            'from' => 'whatsapp:' . getenv('WS_NUMBER2'),
            'body' => $template['template']
        ];

        if (strpos($request, '+') === 0) {
            $whatsAppNumber = 'whatsapp:' . $request;
        } else {
            $whatsAppNumber = 'whatsapp:+57' . $request;
        }

        $message = $twilio->messages->create($whatsAppNumber, $sendData);

        $x = (object)$message->toArray();
        $x->id_template = $template['id_template'];

        if ($chatID) {
            $ci->load->model('sentwhatsapp', '', true);
            /** @var Sentwhatsapp $modelSentWhatsapp */
            $modelSentWhatsapp = $ci->sentwhatsapp;
            $sentMessageEntity = $modelSentWhatsapp->modelCreateDb((object)$x, $chatID, $operatorID, true, true);
        } else {
            $ci->load->model('sentwhatsapp', '', true);
            /** @var Sentwhatsapp $modelSentWhatsapp */
            $modelSentWhatsapp = $ci->sentwhatsapp;
            $sentMessageEntity = $modelSentWhatsapp->modelCreateDb((object)$x, null, null, true, true);
        }

        $sentMensaje = [];
        if(!empty($sentMessageEntity))

            $model = new ChatCobranzas;
            $sentMensaje = $model->getSentMessages(['id_chat' => $chatID, 'id_sent' => $sentMessageEntity['lastID']]);

        if (!empty($sentMensaje)) {
            
            $pusher = new Pusher\Pusher(
                getenv('PUSHER_KEY'),
                getenv('PUSHER_SECRET'),
                getenv('PUSHER_APP_ID'),
                ['cluster' => getenv('PUSHER_CLUSTER')]
            );

            $res = $pusher->trigger(
                'channel-chat-'.$chatID,
                'sent-message-component',
                [
                    'body' => $sentMensaje[0]['body'],
                    'received' => 0,
                    'sent' => 1,
                    'media_url0' => $sentMensaje[0]['media_url0'],
                    'media_content_type0' => $sentMensaje[0]['media_content_type0'],
                    'fecha_creacion' =>  $sentMensaje[0]['fecha_creacion'],
                    'nombre_apellido_operador' => $sentMensaje[0]['nombre_apellido'],
                    'id_chat' => $chatID,
                    'sms_status' => $sentMensaje[0]['sms_status'],
                    'sms_sid' => $sentMensaje[0]['sms_message_sid'],
                    'new_status_chat' => $sentMensaje[0]['status_chat'], 
                ]);
        }

        return true;
    }

    return false;
}


/**
 * Send queue message to provided number
 *
 * @param string $mobileNumber
 * @param int $chatID
 *
 * @throws ConfigurationException
 * @throws TwilioException
 */
function sendQueueMessage(string $mobileNumber, int $chatID): void
{
    $ci =& get_instance();
    $ci->load->database();
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();

    $sid      = getenv('TWILIO_SID');
    $token    = getenv('TWILIO_AUTH');
    $twilio   = new Client($sid, $token);
    $sendData = [
        'from' => 'whatsapp:' . getenv('WS_NUMBER'),
        'body' => 'Recuerda que nuestro horario de atención es en días hábiles de 7:00 am a 7:00 pm. Tan pronto podamos, nos pondremos en contacto contigo.'
    ];

    $message = $twilio->messages
        ->create($mobileNumber, $sendData);

    $ci->load->model('sentwhatsapp', '', true);
    /** @var Sentwhatsapp $modelSentWhatsapp */
    $modelSentWhatsapp = $ci->sentwhatsapp;
    $modelSentWhatsapp->modelCreateDb((object)$message->toArray(), $chatID);
}

/**
 * @param int $idOperator
 * @param string $queueType
 * @throws PusherException
 * @deprecated
 */
function checkTransferActiveChat(int $idOperator, string $queueType)
{
    /** @var CI_DB_result $query */
    $ci =& get_instance();
    $ci->load->database();
    $sql = 'SELECT * FROM chat.new_chats
            WHERE new_chats.status_chat = \'activo\'
            AND new_chats.id_operador = ' . $idOperator;
    /** @var CI_DB_mysqli_result $query */
    $activeChats = $ci->db->query($sql);
    $activeChats = $activeChats->result();

    if (!empty($activeChats)) {
        distributeBetweenOps($activeChats, $idOperator, $queueType);
    }
}

/**
 * @param array $activeChats
 * @param int|array $idOperator
 * @param string $queueType
 * @throws PusherException
 * @deprecated
 */
function distributeBetweenOps(array $activeChats, $idOperator, string $queueType)
{
    $ci =& get_instance();
    $ci->load->database();
    $ci->load->model('chat');
    /** @var Chat $modelChat */
    $modelChat = $ci->chat;
    $agentID   = getFreeOperatorID($idOperator, null, $queueType);

    //If there's an available operator.
    if ($agentID['id'] !== null) {
        $manyOpActiveChats = $ci->db->query('select count(id) as manyChats from chat.new_chats where id_operador = ' . $agentID['id'] .
                                            ' and status_chat = "activo"');
        $manyOpActiveChats = (int)$manyOpActiveChats->row()->manyChats;
        $operatorData      = $ci->db->query('select operadores.cantidad_asignar from gestion.operadores where idoperador = ' . $agentID['id']);
        $operatorData      = $operatorData->row();

        /**
         * If the quantity of $activeChats is < or = than five (limit) minus the quantity of active chat in the selected operator
         * assign this chats to this operator
         **/
        $freePoll = (int)$operatorData->cantidad_asignar - (int)$manyOpActiveChats;

        if (count($activeChats) <= $freePoll) {
            foreach ($activeChats as $chat) {
                $data = [
                    'id_agente' => $agentID['id']
                ];

                $modelChat->doUpdate($chat->id, $data);
                assignNewChat([], $chat->id, $agentID['id']);
            }
        }

        /**
         * If the quantity of $activeChats is > than five (limit) minus the quantity of active chat in the selected operator
         * complex assignation
         **/
        if (count($activeChats) > $freePoll) {
            $chatsToAssign = [];
            for ($i = 0; $i < $freePoll; $i++) {
                $chatsToAssign[] = $activeChats[$i];
                unset($activeChats[$i]);
            }

            foreach ($chatsToAssign as $chat) {
                $data = [
                    'id_agente' => $agentID['id']
                ];

                $modelChat->doUpdate($chat->id, $data);
                assignNewChat([], $chat->id, $agentID['id']);
            }

            $activeChats = array_values($activeChats);
            if (is_array($idOperator)) {
                $idOperator[] = (int)$agentID['id'];
            } else {
                $idOperator = [
                    (int)$idOperator,
                    (int)$agentID['id']
                ];
            }

            distributeBetweenOps($activeChats, $idOperator, $queueType);
        }
    } else {
        foreach ($activeChats as $chat) {
            $data = [
                'id_agente' => null
            ];

            $modelChat->doUpdate($chat->id, $data);
        }
    }
}

/**
 * @param string $smsMessageSid
 * @param int $operatorID
 * @param string $status
 * @throws PusherException
 */
function announceMessageStatus(string $smsMessageSid, int $operatorID, string $status, int $id_chat = null)
{
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    $pusher = new Pusher\Pusher(
        getenv('PUSHER_KEY'),
        getenv('PUSHER_SECRET'),
        getenv('PUSHER_APP_ID'),
        ['cluster' => getenv('PUSHER_CLUSTER')]
    );

    $pusher->trigger(
        'operator-' . $operatorID . '-channel',
        'message-status',
        [
            'status'    => $status,
            'messageID' => $smsMessageSid
        ]
    );

    $pusher->trigger(
        'channel-chat-' . $id_chat,
        'message-status',
        [
            'status'    => $status,
            'messageID' => $smsMessageSid
        ]
    );
}

/**
 * Envía una notificación pusher al sistema para indicar que se ha iniciado un nuevo chat.
 *
 * @param string $chatID
 * @param int $operatorID
 * @throws PusherException
 */
function addChatFromTemplate(string $chatID, int $operatorID)
{
    $dotEnv = Dotenv\Dotenv::create(FCPATH);
    $dotEnv->load();
    $pusher = new Pusher\Pusher(
        getenv('PUSHER_KEY'),
        getenv('PUSHER_SECRET'),
        getenv('PUSHER_APP_ID'),
        ['cluster' => getenv('PUSHER_CLUSTER')]
    );

    $pusher->trigger(
        'operator-' . $operatorID . '-channel',
        'add-chat-from-template',
        [
            'chatID' => $chatID
        ]
    );
}

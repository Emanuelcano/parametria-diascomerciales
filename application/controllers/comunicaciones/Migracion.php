<?php

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\PusherException;

defined('BASEPATH') OR exit('No direct script access allowed');

class Migracion extends CI_Controller
{
    /**
     * @return bool
     * @throws GuzzleException
     * @throws PusherException
     */
    public function one_time_run_migration()
    {
        $this->load->database();
        //Registro de mensajes recibidos y creación de entidades de chat.
        $pageSize        = 250;
        $currentPage     = 0;
        $dbRecividosData = 'SELECT * FROM chat.receive_whatsapp LIMIT ' . $currentPage * $pageSize . ',' . $pageSize;
        /** @var CI_DB_mysqli_result $query */
        $query           = $this->db->query($dbRecividosData);
        $dbRecividosData = $query->result_array();
        $dbEnviadosData  = 'SELECT * FROM chat.send_whatsapp LIMIT ' . $currentPage * $pageSize . ',' . $pageSize;
        /** @var CI_DB_mysqli_result $query */
        $query          = $this->db->query($dbEnviadosData);
        $dbEnviadosData = $query->result_array();

        /**
         * Debugging
         */
//        $this->print_mem();

//        Registrar mensajes recibidos.
        if ($dbRecividosData !== false) {
            while (!empty($dbRecividosData)) {
                //Do Stuff
                $newChatsNumbersArray = $this->db->query('SELECT new_chats.from as phoneNumber, id FROM chat.new_chats')
                                                 ->result_array();
                $newChatsNumbersArray = $this->simpleArrayOfNumbers($newChatsNumbersArray);
                $arrayGroupedByFrom   = $this->arrayGroupedByFrom($dbRecividosData);

                $this->db->trans_start();
                $this->db->trans_strict(FALSE);
                foreach ($arrayGroupedByFrom as $phoneNumber => $messages) {
                    $batchMessages = [];
                    $chatID        = 0;
                    foreach ($messages as $message) {
                        if (in_array((string)$phoneNumber, $newChatsNumbersArray, true)) {
                            $chatID   = array_search((string)$phoneNumber, $newChatsNumbersArray, true);
                            $msgArray = [
                                'id_chat'             => $chatID,
                                'sms_message_sid'     => $message['sms_message_sid'],
                                'num_media'           => $message['num_media'],
                                'sms_status'          => $message['sms_status'],
                                'body'                => $message['body'],
                                'num_segments'        => $message['num_segments'],
                                'media_content_type0' => $message['media_content_type0'],
                                'media_url0'          => $message['media_url0'],
                                'api_version'         => $message['api_version'],
                                'fecha_creacion'      => $message['fecha_creacion']
                            ];

                            $batchMessages[] = $msgArray;
                        } else {
                            /**
                             * No existe el chat, lo creamos como ocurre cuando se recibe un mensaje desde Twilio.
                             * Éste método automáticamente crea el mensaje recibido.
                             *
                             * ¡SOLO CON EL PRIMER MENSAJE!
                             */
                            $msgArray = (object)[
                                'ApiVersion'        => $message['api_version'],
                                'SmsSid'            => $message['sms_sid'],
                                'SmsStatus'         => $message['sms_status'],
                                'SmsMessageSid'     => $message['sms_message_sid'],
                                'NumSegments'       => $message['num_segments'],
                                'From'              => $message['froms'],
                                'To'                => $message['tos'],
                                'MessageSid'        => $message['message_sid'],
                                'Body'              => $message['body'],
                                'AccountSid'        => $message['account_sid'],
                                'NumMedia'          => 0,
                                'MediaUrl0'         => $message['media_url0'],
                                'MediaContentType0' => $message['media_content_type0']
                            ];

                            $newObject                              = $this->normalizeObject($msgArray);
                            $lastChatID                             = $this->modelDbAction($newObject, true, true);
                            $newChatsNumbersArray[(int)$lastChatID] = (string)$phoneNumber;
                        }
                    }

                    if (!empty($batchMessages)) {
                        $this->db->insert_batch('chat.received_messages', $batchMessages);
                        $lastMessageReceived = end($batchMessages);
                        $chatData            = [
                            'fecha_ultima_recepcion' => $lastMessageReceived['fecha_creacion'],
                            'ultimo_mensaje'         => strlen($lastMessageReceived['body']) > 150
                                ? substr($lastMessageReceived['body'], 0, 147) . '...' : $lastMessageReceived['body'],
                            'account_sid'            => 'AC6b46e7311003df1b23ce538a408a054c',
                            'sin_leer'               => 0,
                            'status_chat'            => 'vencido'
                        ];
                        $this->db->update(
                            'chat.new_chats',
                            $chatData,
                            ['id' => $chatID]
                        );
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    return false;
                }

                $this->db->trans_commit();
                //End Do Stuff

                $currentPage++;
                /**
                 * Debugging
                 */
//                echo $currentPage . '<br>';
//                echo $currentPage * $pageSize . '<br>';
//                $this->print_mem();
                $dbRecividosData = 'SELECT * FROM chat.receive_whatsapp LIMIT ' . $currentPage * $pageSize . ',' . $pageSize;
                $query           = $this->db->query($dbRecividosData);
                $dbRecividosData = $query->result_array();
            }
        }

        //Registrar mensajes enviados.
        if ($dbEnviadosData !== false) {
            while (!empty($dbEnviadosData)) {
                //Do Stuff
                $newChatsNumbersArray = $this->db->query('SELECT new_chats.from as phoneNumber, id FROM chat.new_chats')
                                                 ->result_array();
                $newChatsNumbersArray = $this->simpleArrayOfNumbers($newChatsNumbersArray);
                $arrayGroupedByFrom   = $this->arrayGroupedByTo($dbEnviadosData);

                $this->db->trans_start();
                $this->db->trans_strict(FALSE);
                foreach ($arrayGroupedByFrom as $phoneNumber => $messages) {
                    $batchMessages = [];
                    $chatID        = 0;
                    foreach ($messages as $message) {
                        if (in_array((string)$phoneNumber, $newChatsNumbersArray, true)) {
                            $chatID   = array_search((string)$phoneNumber, $newChatsNumbersArray, true);
                            $msgArray = [
                                'id_chat'             => $chatID,
                                'id_operador'         => -1,
                                'sms_message_sid'     => $message['sms_message_sid'],
                                'num_media'           => $message['num_media'],
                                'sms_status'          => $message['sms_status'],
                                'body'                => $message['body'],
                                'num_segments'        => $message['num_segments'],
                                'media_content_type0' => $message['media_content_type0'],
                                'media_url0'          => $message['media_url0'],
                                'api_version'         => $message['api_version'],
                                'fecha_creacion'      => $message['fecha_creacion']
                            ];

                            $batchMessages[] = $msgArray;
                        }
                    }

                    if (!empty($batchMessages)) {
                        $this->db->insert_batch('chat.sent_messages', $batchMessages);
                        $lastMessageSent = end($batchMessages);
                        $chatData        = [
                            'fecha_ultimo_envio' => $lastMessageSent['fecha_creacion'],
                            'ultimo_mensaje'     => strlen($lastMessageSent['body']) > 150
                                ? substr($lastMessageSent['body'], 0, 147) . '...' : $lastMessageSent['body']
                        ];
                        $this->db->update(
                            'chat.new_chats',
                            $chatData,
                            ['id' => $chatID]
                        );
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    return false;
                }

                $this->db->trans_commit();
                //End Do Stuff

                $currentPage++;
                /**
                 * Debugging
                 */
//                echo $currentPage . '<br>';
//                echo $currentPage * $pageSize . '<br>';
//                $this->print_mem();
                $dbEnviadosData = 'SELECT * FROM chat.send_whatsapp LIMIT ' . $currentPage * $pageSize . ',' . $pageSize;
                $query          = $this->db->query($dbEnviadosData);
                $dbEnviadosData = $query->result_array();
            }
        }

        /**
         * Debugging
         */
//        $this->print_mem();
    }

    /**
     * @param array $numbersData
     * @return array
     */
    private function simpleArrayOfNumbers(array $numbersData): array
    {
        $simpleArray = [];
        foreach ($numbersData as $datum) {
            $simpleArray[$datum['id']] = $datum['phoneNumber'];
        }

        return $simpleArray;
    }

    /**
     * @param array $messagesData
     * @return array
     */
    private function arrayGroupedByFrom(array $messagesData): array
    {
        require_once('Twilio.php');
        $mainArray = [];

        foreach ($messagesData as $message) {
            $phoneNumber               = $this->getMobileNumber($message['froms']);
            $mainArray[$phoneNumber][] = $message;
        }

        return $mainArray;
    }

    /**
     * @param array $messagesData
     * @return array
     */
    private function arrayGroupedByTo(array $messagesData): array
    {
        require_once('Twilio.php');
        $mainArray = [];

        foreach ($messagesData as $message) {
            $phoneNumber               = $this->getMobileNumber($message['tos']);
            $mainArray[$phoneNumber][] = $message;
        }

        return $mainArray;
    }


    /**
     * Normalize Chat data into a strClass before interaction with DB
     *
     * @param stdClass|null $object
     *
     * @return stdClass|null
     */
    private function normalizeObject($object): ?stdClass
    {
        if ($object === null) {
            return null;
        }

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
        $startDateDB     = isset($object->timestamp)
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i:s\Z',
                $object->timestamp
            )
                    ->format('Y-m-d H:i:s')
            : Carbon::now()
                    ->format('Y-m-d H:i:s');
        $type            = 'general';
        $fromNumber      = $this->getMobileNumber($object->From);
        $toNumber        = $this->getMobileNumber($object->To);
        $solicitudEntity = $this->findRequestByTelefono($fromNumber);

        if ($solicitudEntity) {
            $type = 'solicitante';
        }

        $data = [
            'id_operador'            => $object->agent_id ?? null,
            'from'                   => $fromNumber,
            'nombres'                => null,
            'apellidos'              => null,
            'documento'              => null,
            'email'                  => null,
            'to'                     => $toNumber,
            'medio'                  => 'WhatsApp',
            'proveedor'              => 'Twilio_WS',
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
            'chat.new_chats',
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
             * Si se envia un template, ya hay el operador asignado, se envia la notificación de inmediato.
             */
            if ($regMsg) {
                $this->registerReceivedMessage(
                    ['id' => $lastID],
                    $object
                );
            }

            if ($returnID) {
                return $lastID;
            }
        }

        return true;
    }


    /**
     * Return the clean phone number of the sender.
     *
     * @param string $number
     * @return string
     */
    private function getMobileNumber(string $number): string
    {
        $numberArray = explode(':', $number);
        if (isset($numberArray[1])) {
            //If is colombian number return only the string containing the number
            if (strpos($numberArray[1], '+57') !== false) {
                return str_replace('+57', '', $numberArray[1]);
            }

            //If the number is from another country, return the full number
            return $numberArray[1];
        }

        //Rare case in the future when the number is not whatsapp related
        return $number;
    }


    /**
     * @param string $phoneNumber
     * @return array|bool
     */
    public function findRequestByTelefono(string $phoneNumber)
    {
        //Check by Absences
        if ($phoneNumber !== '') {
            $sql = 'SELECT solicitud.nombres, solicitud.apellidos, solicitud.documento, solicitud.email'
                . ' from solicitudes.solicitud'
                . " where solicitud.telefono = '" . $phoneNumber . "'"
                . ' order by id DESC limit 1';

            $query = $this->db->query($sql);

            if (!is_bool($query)) {
                return $query->row_array();
            }

            return false;
        }
    }

    /**
     * @param array $chatEntity
     * @param stdClass $receivedMessage
     * @param bool $chatCreation
     * @return mixed
     * @throws GuzzleException
     * @throws PusherException
     */
    public function registerReceivedMessage(array $chatEntity, stdClass $receivedMessage)
    {
        //1. Registrar el nuevo mensaje en la DB
        $creationDate = Carbon::now()
                              ->format('Y-m-d H:i:s');
        $data         = [
            'id_chat'             => $chatEntity['id'],
            'sms_message_sid'     => $receivedMessage->SmsMessageSid,
            'num_media'           => $receivedMessage->NumMedia,
            'sms_status'          => $receivedMessage->SmsStatus,
            'body'                => $receivedMessage->Body,
            'num_segments'        => $receivedMessage->NumSegments,
            'media_content_type0' => isset($receivedMessage->MediaContentType0) ?
                str_replace('\\', '', $receivedMessage->MediaContentType0) : null,
            'media_url0'          => isset($receivedMessage->MediaUrl0) ? str_replace('\\', '',
                                                                                      $receivedMessage->MediaUrl0) : null,
            'api_version'         => $receivedMessage->ApiVersion,
            'fecha_creacion'      => $creationDate
        ];

        $this->db->trans_start();
        $this->db->trans_strict(FALSE);
        $this->db->insert('chat.received_messages', $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();
    }

    private function print_mem()
    {
        /* Currently used memory */
        $mem_usage = memory_get_usage();

        /* Peak memory usage */
        $mem_peak = memory_get_peak_usage();

        echo 'The script is now using: <strong>' . round($mem_usage / 1024) . 'KB</strong> of memory.<br>';
        echo 'Peak usage: <strong>' . round($mem_peak / 1024) . 'KB</strong> of memory.<br><br>';
    }
}
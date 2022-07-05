<?php

use Carbon\Carbon;
use Pusher\PusherException;

class old_Sendwhatsapp extends Orm_model
{
    public $provider;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'send_whatsapp';
        $this->columns   = [
            'id',
            'sms_message_sid',
            'num_media',
            'sms_sid',
            'sms_status',
            'body',
            'tos',
            'num_segments',
            'message_sid',
            'account_sid',
            'froms',
            'media_content_type0',
            'media_url0',
            'api_version',
            'chat_id',
            'fecha_creacion'
        ];
        $this->dbName    = 'chat';
    }

    /**
     * Columns in the table are:
     * sms_message_sid
     * num_media
     * sms_sid
     * sms_status
     * body
     * tos
     * num_segments
     * message_sid
     * account_sid
     * froms
     * media_content_type0
     * media_url0
     * api_version
     */

    /**
     * @param stdClass $object
     * @param int $chatID
     * @param bool $returnID
     * @return bool|int
     */
    public function modelCreateDb(stdClass $object, int $chatID = null, bool $returnID = false)
    {
        $data = [
            'sms_message_sid'     => $object->sid ?? null,
            'num_media'           => $object->numMedia ?? null,
            'sms_sid'             => $object->sid ?? null,
            'sms_status'          => $object->status,
            'body'                => $object->body,
            'tos'                 => $object->to,
            'num_segments'        => $object->numSegments,
            'message_sid'         => $object->sid,
            'account_sid'         => $object->accountSid,
            'froms'               => $object->from,
            'media_content_type0' => isset($object->MediaContentType0) ? str_replace('\\', '', $object->MediaContentType0) : null,
            'media_url0'          => isset($object->MediaUrl0) ? str_replace('\\', '', $object->MediaUrl0) : null,
            'api_version'         => $object->apiVersion,
            'chat_id'             => $chatID ?? -1,
            'fecha_creacion'      => Carbon::now()->format('Y-m-d H:i:s')
        ];

        $this->db->trans_start();
        $this->db->trans_strict(FALSE);
        $this->db->insert('chat.' . $this->tableName, $data);
        $lastID = $this->db->insert_id();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        if ($returnID) {
            return $lastID;
        }

        return true;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @param int $lastID
     * @param string $fileUrl
     * @return void|bool
     */
    public function registerMedias(int $lastID, string $fileUrl)
    {
        $this->load->model('subresourceuris');
        /** @var Subresourceuris $mediaResourceModel */
        $mediaResourceModel = $this->subresourceuris;
        $mediaResourceModel->modelCreateDb(
            [
                'cod_message_sid' => $lastID,
                'media'           => $fileUrl
            ]
        );

        $mediaData = [
            'media_content_type0' => mime_content_type('.' . $fileUrl),
            'media_url0'          => $fileUrl
        ];

        $this->db->trans_start();
        $this->db->trans_strict(FALSE);
        $this->db->update('chat.' . $this->tableName, $mediaData, ['id' => $lastID]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();
    }

    /**
     * @param string $smsMessageSid
     * @param string $status
     * @return bool
     * @throws PusherException
     */
    public function updateStatus(string $smsMessageSid, string $status): bool
    {
        $dbData = [
            'sms_status' => $status
        ];

        $this->db->trans_start();
        $this->db->trans_strict(FALSE);
        $this->db->update('chat.' . $this->tableName, $dbData, ['sms_message_sid' => $smsMessageSid]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        $messageData = $this->db->query('select chat.send_whatsapp.chat_id, ' .
                                        'chat.chats.id_agente, ' .
                                        'chat.chats.id ' .
                                        'from chat.send_whatsapp ' .
                                        'left join chat.chats on chats.id = send_whatsapp.chat_id ' .
                                        'where send_whatsapp.sms_message_sid = "' . $smsMessageSid . '" limit 1');
        $messageData = $messageData->row_array();

        if ($messageData['id_agente'] !== null) {
            $this->load->helper('dispatcher');
            announceMessageStatus($smsMessageSid, $messageData['id_agente'], $status);
        }

        return true;
    }

}

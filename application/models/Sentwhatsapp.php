<?php

use Carbon\Carbon;
use Pusher\PusherException;

class Sentwhatsapp extends Orm_model
{
    public $provider;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'sent_messages';
        $this->columns   = [
            'id',
            'id_chat',
            'id_operador',
            'sms_message_sid',
            'num_media',
            'sms_status',
            'body',
            'num_segments',
            'media_content_type0',
            'media_url0',
            'api_version',
            'fecha_creacion',
            'id_template'
        ];
        $this->dbName    = 'chat';
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
    }

    /**
     * Columns in the table are:
     * id
     * id_chat
     * id_operador
     * sms_message_sid
     * num_media
     * sms_status
     * body
     * num_segments
     * media_content_type0
     * media_url0
     * api_version
     * fecha_creacion
     * id_template
     */

    /**
     * @param stdClass $object
     * @param int $chatID
     * @param int|null $opID
     * @param bool $returnID
     * @param bool $returnDate
     * @return bool|array
     */
    public function modelCreateDb(stdClass $object, int $chatID = null,
                                  int $opID = null, bool $returnID = false,
                                  bool $returnDate = false)
    {
        $creationDate = Carbon::now()
                              ->format('Y-m-d H:i:s.u');
        $returnData   = [];
        $opid = -1;
        if (isset($object->id_template) && $object->id_template == 102) {
            $opid = 192;
        }
        $data         = [
            'id_chat'             => $chatID ?? -1,
            'id_operador'         => $opID ?? $opid,
            'sms_message_sid'     => $object->sid ?? null,
            'num_media'           => $object->numMedia ?? null,
            'sms_status'          => $object->status,
            'body'                => $object->body,
            'num_segments'        => $object->numSegments,
            'media_content_type0' => isset($object->MediaContentType0) ?
                str_replace('\\', '', $object->MediaContentType0) : null,
            'media_url0'          => isset($object->MediaUrl0) ?
                str_replace('\\', '', $object->MediaUrl0) : null,
            'api_version'         => $object->apiVersion,
            'fecha_creacion'      => $creationDate,
            'id_template'         => $object->id_template ?? 0
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
            $returnData['lastID'] = $lastID;
        }

        if ($returnDate) {
            $returnData['creationDate'] = $creationDate;
        }

        if (!empty($returnData)) {
            return $returnData;
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
     * Registra archivos enviados por medio del chat en la DB y se asocian al ChatID
     *
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

        $ext = substr($fileUrl, -3);

        switch (strtolower($ext)) {
            case 'jpg':
                $mime = 'image/jpeg';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'pdf':
                $mime = 'application/pdf';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'jpeg':
                $mime = 'image/jpeg';
                break;
            default:
                $mime = 0;
                break;

        }

        $mediaData = [
            #'media_content_type0' => mime_content_type('.' . $fileUrl),
            'media_content_type0' => $mime,
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

        $messageData = $this->db->query('select chat.new_chats.id_operador, chat.sent_messages.id_chat ' .
                                        'from chat.sent_messages ' .
                                        'left join chat.new_chats on new_chats.id = sent_messages.id_chat ' .
                                        'where sent_messages.sms_message_sid = "' . $smsMessageSid . '" limit 1');
        $messageData = $messageData->row_array();

        if ($messageData['id_operador'] !== null) {
            $this->load->helper('dispatcher');
            announceMessageStatus($smsMessageSid, $messageData['id_operador'], $status, $messageData['id_chat']);
        }

        return true;
    }
    /*
     * @param string $phone
     * @return bool
    */
    public function updateSolicitudFailed(string $phone)
    {
      $idSol = $this->db_solicitudes->query("SELECT MAX(id) AS id FROM solicitud WHERE telefono =". $phone . " AND paso <= 6");
      $idSol = $idSol->row('id');
      
      if (!is_null($idSol)) {
        $this->db_solicitudes->query("UPDATE solicitud SET respuesta_analisis = 'RECHAZADO', paso = '18' WHERE id = ".$idSol);    
        $this->db_solicitudes->query("UPDATE solicitud_analisis SET respuesta = 'RECHAZADO', regla = '1003' WHERE id_solicitud = ".$idSol);    
        return true;
      }
      
      return false;
    }

}

<?php

use Carbon\Carbon;
use Pusher\PusherException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @deprecated
 * Class OperatorRelation
 */
class OperatorRelation extends Orm_model
{
    public $provider;

    public function __construct()
    {

        $this->db = $this->load->database('chat', TRUE);
        parent::__construct();

        $this->tableName = 'relaciones_agentes_dispatcher';
        $this->columns   = [
            'id',
            'id_agente',
            'num_celular',
            'fecha_creacion',
            'ultima_actualizacion'
        ];
        $this->dbName    = 'chat';
    }
    /**
     * Columns in the table are:
     * id
     * id_agente
     * num_celular
     * fecha_creacion
     * ultima_actualizacion
     */

    /**
     * Create a new record in the DB
     *
     * @param array $data
     * @return bool
     */
    public function dbCreate(array $data): bool
    {
        $data['fecha_creacion']       = Carbon::now()->format('Y-m-d H:i:s');
        $data['ultima_actualizacion'] = Carbon::now()->format('Y-m-d H:i:s');

        $this->db->trans_start();
        $this->db->trans_strict(FALSE);
        $this->db->insert('chat.' . $this->tableName, $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    /**
     * Update a record in the DB
     *
     * @param array $data
     * @param int $id
     * @param string $anchor
     * @return bool
     */
    public function dbUpdate(array $data, int $id, string $anchor = 'id'): bool
    {
        $this->db->trans_start();
        $this->db->trans_strict(FALSE);
        $this->db->update('chat.' . $this->tableName, $data, [$anchor => $id]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    /**
     * Get a single agent associated to this Chat
     * @param int $agentID
     * @return stdClass|null
     */
    public function findByOperatorID(int $agentID): ?stdClass
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where('chat.' . $this->tableName, ['id_agente' => $agentID]);

        return $query->row();
    }

    /**
     * Get a single agent associated to this Chat
     * @param string $mobileNumber
     * @return stdClass|null
     */
    public function findByMobile(string $mobileNumber): ?stdClass
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where('chat.' . $this->tableName, ['num_celular' => $mobileNumber]);

        return $query->row();
    }

    /**
     * @param int $agentID
     * @param int $page
     * @return bool|stdClass
     */
    public function findByOpIdOrderDirectory(int $agentID, int $page)
    {
        $sql   = "SELECT visitante.id as id, 
                         chat.relaciones_agentes_dispatcher.num_celular      as num_celular,
                         CONCAT(visitante.nombres, ' ', visitante.apellidos) as nombre_completo,
                         IF(IF(last_r_date.fecha_creacion is null, 0, last_r_date.fecha_creacion)  > IF(last_s_date.fecha_creacion is null, 0, last_s_date.fecha_creacion),
                             last_r_date.fecha_creacion,
                         last_s_date.fecha_creacion)                      as def_date,
                         IF(IF(last_r_date.fecha_creacion is null, 0, last_r_date.fecha_creacion)  > IF(last_s_date.fecha_creacion is null, 0, last_s_date.fecha_creacion),
                             last_r_date.body,
                         last_s_date.body)                                as last_message,
                         IF(IF(last_r_date.fecha_creacion is null, 0, last_r_date.fecha_creacion)  > IF(last_s_date.fecha_creacion is null, 0, last_s_date.fecha_creacion),
                             last_r_date.chat_id,
                         last_s_date.chat_id)                                as chat_id
                  FROM chat.relaciones_agentes_dispatcher
                  LEFT JOIN (SELECT a.froms, chat.receive_whatsapp.fecha_creacion, chat.receive_whatsapp.body, chat.receive_whatsapp.chat_id
                            FROM (SELECT receive_whatsapp.froms, max(receive_whatsapp.id) as id
                                  FROM chat.receive_whatsapp
                                  GROUP BY receive_whatsapp.froms) as a
                                     LEFT JOIN chat.receive_whatsapp on a.id = chat.receive_whatsapp.id
                            GROUP BY froms) AS last_r_date
                           ON last_r_date.froms LIKE CONCAT('%', chat.relaciones_agentes_dispatcher.num_celular, '%')
                  LEFT JOIN (SELECT a.tos, chat.send_whatsapp.fecha_creacion, chat.send_whatsapp.body, chat.send_whatsapp.chat_id
                            FROM (SELECT send_whatsapp.tos, max(send_whatsapp.id) as id
                                  FROM chat.send_whatsapp
                                  GROUP BY send_whatsapp.tos) as a
                                     LEFT JOIN chat.send_whatsapp on a.id = chat.send_whatsapp.id
                            GROUP BY tos) AS last_s_date
                           ON last_s_date.tos LIKE CONCAT('%', chat.relaciones_agentes_dispatcher.num_celular, '%')
                  LEFT JOIN chat.visitantes as visitante
                           on visitante.num_celular = chat.relaciones_agentes_dispatcher.num_celular
                WHERE id_agente = " . $agentID . '
                GROUP BY num_celular
                ORDER BY def_date DESC
                LIMIT 14
                OFFSET ' . $page * 14;
        $query = $this->db->query($sql);

        if (!is_bool($query)) {
            return $query->result();
        }

        return false;
    }


    public function findByKeyword(int $agentID, string $keyword)
    {
        $sql = "SELECT visitante.id as id,
                visitante.num_celular                               as num_celular,
                CONCAT(visitante.nombres, ' ', visitante.apellidos) as nombre_completo,
                IF(IF(last_r_date.fecha_creacion is null, 0, last_r_date.fecha_creacion)  > IF(last_s_date.fecha_creacion is null, 0, last_s_date.fecha_creacion),
                   last_r_date.fecha_creacion,
                   last_s_date.fecha_creacion)                      as def_date,
                IF(IF(last_r_date.fecha_creacion is null, 0, last_r_date.fecha_creacion)  > IF(last_s_date.fecha_creacion is null, 0, last_s_date.fecha_creacion),
                   last_r_date.body,
                   last_s_date.body)                                as last_message,
                IF(IF(last_r_date.fecha_creacion is null, 0, last_r_date.fecha_creacion)  > IF(last_s_date.fecha_creacion is null, 0, last_s_date.fecha_creacion),
                   last_r_date.chat_id,
                   last_s_date.chat_id)                             as chat_id,
                true                                                as temporal
                FROM chat.relaciones_agentes_dispatcher
                LEFT JOIN (SELECT a.froms, chat.receive_whatsapp.fecha_creacion, chat.receive_whatsapp.body, chat.receive_whatsapp.chat_id
                           FROM (SELECT receive_whatsapp.froms, max(receive_whatsapp.id) as id
                                 FROM chat.receive_whatsapp
                                 GROUP BY receive_whatsapp.froms) as a
                                    LEFT JOIN chat.receive_whatsapp on a.id = chat.receive_whatsapp.id
                           GROUP BY froms) AS last_r_date
                          ON last_r_date.froms LIKE CONCAT('%', num_celular, '%')
                LEFT JOIN (SELECT a.tos, chat.send_whatsapp.fecha_creacion, chat.send_whatsapp.body, chat.send_whatsapp.chat_id
                           FROM (SELECT send_whatsapp.tos, max(send_whatsapp.id) as id
                                 FROM chat.send_whatsapp
                                 GROUP BY send_whatsapp.tos) as a
                                    LEFT JOIN chat.send_whatsapp on a.id = chat.send_whatsapp.id
                           GROUP BY tos) AS last_s_date
                          ON last_s_date.tos LIKE CONCAT('%', num_celular, '%')
                LEFT JOIN chat.visitantes as visitante
                          ON visitante.num_celular = relaciones_agentes_dispatcher.num_celular 
                         WHERE id_agente = " . $agentID . "
                         AND ('%" . $keyword . "%' OR visitante.nombres LIKE '%" . $keyword . "%' OR visitante.apellidos LIKE '%" . $keyword . "%')
                         GROUP BY id";

        $query = $this->db->query($sql);

        if (!is_bool($query)) {
            return $query->result();
        }

        return false;
    }

    /**
     * Relation
     *
     * @return array
     */
    public function relationOperator(): array
    {
        return ['operatorWS', ['id_agente' => 'idoperador'], 'type' => 'one'];
    }

}

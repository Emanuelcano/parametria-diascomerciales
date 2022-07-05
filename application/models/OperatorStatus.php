<?php

use Carbon\Carbon;
use Pusher\PusherException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

defined('BASEPATH') OR exit('No direct script access allowed');

class OperatorStatus extends Orm_model
{
    public $provider;

    public function __construct()
    {

        $this->db = $this->load->database('chat', TRUE);
        parent::__construct();

        $this->tableName = 'estados_operadores';
        $this->dbName    = 'chat';
        $this->columns   = [
            'id',
            'operador_id',
            'ultimo_login',
            'ultimo_logout',
            'cantidad_logins',
            'is_idle',
            'is_logged_in',
            'is_alive_timestamp',
            'is_idle_timestamp'
        ];
    }
    /**
     * Columns in the table are:
     * id
     * operador_id
     * ultimo_login
     * ultimo_logout
     * cantidad_logins
     * is_idle
     * is_logged_in
     * is_alive_timestamp
     * is_idle_timestamp
     */

    /**
     * Create or Update a new record in the DB for Login
     *
     * @param int $operatorID
     * @return bool
     */
    public function loginByID(int $operatorID): bool
    {

        /**
         * @var CI_DB_mysqli_driver $db
         * @var CI_DB_mysqli_result $query
         */
        $db        = $this->db;
        $query     = $db->query(
            'SELECT id,cantidad_logins from chat.estados_operadores WHERE operador_id = ' . $operatorID
        );
        $idsArray  = $query->row(); //TODO
        $lastLogin = Carbon::now()->format('Y-m-d H:i:s');
        $data      = [
            'operador_id'        => $operatorID,
            'ultimo_login'       => $lastLogin,
            'is_idle'            => null,
            'is_logged_in'       => true,
            'is_alive_timestamp' => $lastLogin,
            'is_idle_timestamp'  => null,
        ];

        if ($idsArray === null) {
            $data['cantidad_logins'] = 1;

            return $this->dbCreate($data);
        }

        $data['cantidad_logins'] = $idsArray->cantidad_logins + 1;

        return $this->dbUpdate($data, (int)$idsArray->id);
    }

    /**
     * Create or Update a new record in the DB for Logout
     *
     * @param int $operatorID
     * @return bool
     */
    public function logoutByID(int $operatorID): bool
    {

        /**
         * @var CI_DB_mysqli_driver $db
         * @var CI_DB_mysqli_result $query
         */
        $db         = $this->db;
        $query      = $db->query(
            'SELECT id,ultimo_login from chat.estados_operadores WHERE operador_id = ' . $operatorID
        );
        $stateArray = $query->row();
        $data       = [
            'operador_id'        => $operatorID,
            'ultimo_login'       => $stateArray->ultimo_login,
            'ultimo_logout'      => Carbon::now()->format('Y-m-d H:i:s'),
            'is_idle'            => null,
            'is_logged_in'       => false,
            'is_alive_timestamp' => null,
            'is_idle_timestamp'  => null,
        ];

        return $this->dbUpdate($data, (int)$stateArray->id);
    }

    /**
     * Create a new record in the DB
     *
     * @param array $data
     * @return bool
     */
    public function dbCreate(array $data): bool
    {
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

    //Tools

    /**
     * Mark Im alive timestamp
     *
     * @param int $operatorID
     * @return bool
     * @throws PusherException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function putImAlive(int $operatorID): bool
    {
        $operatorLastStatus = $this->findByOpID($operatorID);
        if ($operatorLastStatus !== false) {
            if ($operatorLastStatus === null) {
                $lastLogin = Carbon::now()->format('Y-m-d H:i:s');
                $data      = [
                    'operador_id'        => $operatorID,
                    'ultimo_login'       => $lastLogin,
                    'is_idle'            => null,
                    'is_logged_in'       => true,
                    'is_alive_timestamp' => $lastLogin,
                    'is_idle_timestamp'  => null,
                    'cantidad_logins'    => 1
                ];

                $this->dbCreate($data);
            }

            $data = [
                'is_alive_timestamp' => Carbon::now()->format('Y-m-d H:i:s'),
                'is_idle'            => null,
                'is_idle_timestamp'  => null
            ];

            $this->db->trans_start();
            $this->db->trans_strict(FALSE);
            $this->db->update('chat.' . $this->tableName, $data, ['operador_id' => $operatorID]);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                return false;
            }

            $this->db->trans_commit();
        }

//        $queueType = 'cliente';
//        if ($operatorLastStatus->tipo_operador === 4) {
//            $queueType = 'general';
//        }
//
//        $this->load->helper('dispatcher');
//        $this->closeOutdatedChats($operatorID, true);
//        if ($operatorLastStatus->is_idle === '1') {
//            $sql             = 'SELECT operadores.cantidad_asignar from gestion.operadores WHERE idoperador = ' . $operatorID;
//            $query           = $this->db->query($sql);
//            $limit           = (int)$query->row()->cantidad_asignar;
//            $manyActiveChats = $this->countActiveChats($operatorID);
//            $limit           -= $manyActiveChats;
//
//            checkOfflineChats($operatorID, $limit, $queueType);
//        }

        return true;
    }

    /**
     * Flag the user as Idle
     *
     * @deprecated
     * @param int $operatorID
     * @return bool
     * @throws PusherException
     */
    public function putIdle(int $operatorID): bool
    {
        $data = [
            'is_idle'           => true,
            'is_idle_timestamp' => Carbon::now()->format('Y-m-d H:i:s')
        ];

        $this->db->trans_start();
        $this->db->trans_strict(FALSE);
        $this->db->update('chat.' . $this->tableName, $data, ['operador_id' => $operatorID]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        $this->load->helper('dispatcher');
        checkTransferActiveChat($operatorID);

        return true;
    }

    /**
     * Return all the online operators
     *
     * @return array
     */
    public function getOnlineOperators(): array
    {
        /**
         * @var CI_DB_mysqli_driver $db
         * @var CI_DB_mysqli_result $query
         */
        $db    = $this->db;
        $query = $db->query(
            'SELECT * from chat.estados_operadores WHERE is_logged_in = 1'
        );

        return $query->result();
    }

    /**
     * Return status by operator ID
     *
     * @param int $opID
     * @return array|stdClass
     */
    public function findByOpID(int $opID)
    {
        /**
         * @var CI_DB_mysqli_driver $db
         * @var CI_DB_mysqli_result $query
         */
        $db    = $this->db;
        $query = $db->query(
            'SELECT chat.estados_operadores.*, gestion.operadores.tipo_operador as tipo_operador from chat.estados_operadores LEFT JOIN gestion.operadores on gestion.operadores.idoperador =' . $opID . ' WHERE estados_operadores.operador_id =' . $opID
        );

        if (!is_bool($query)) {
            return $query->row();
        }

        return false;
    }

    /**
     * Close active chats out of the time windows
     *
     * @param int $opID
     * @param bool $noEmpty
     * @param bool $onLogin
     */
    public function closeOutdatedChats(int $opID, $noEmpty = false, $onLogin = false): void
    {
        /**
         * @var CI_DB_mysqli_driver $db
         * @var CI_DB_mysqli_result $query
         */
        $db      = $this->db;
        $sql     = 'SELECT chats.* FROM chat.chats WHERE chats.status_chat = \'activo\' AND chats.id_agente = ' . $opID;
        $opChats = $db->query($sql);
        $opChats = $opChats->result();
        $this->load->model('chat', '', true);

        if (empty($opChats)) {
            return;
        }

        foreach ($opChats as $cKey => $chat) {
            $lastMessageDate = $db
                ->query('SELECT fecha_creacion from chat.received_messages WHERE id_chat = ' . $chat->id . ' ORDER BY fecha_creacion DESC');
            $lastMessageDate = $lastMessageDate->row();

            if ($lastMessageDate !== null) {
                $lastMessageDate = Carbon::parse($lastMessageDate->fecha_creacion);
                $now             = Carbon::now();
                $differenceDates = $now->diffInHours($lastMessageDate);

                if ($differenceDates >= 24) {
                    $this->chat->closeByID($chat->id, false, $onLogin);
                }
            } else if ($noEmpty === false) {
                $this->chat->closeByID($chat->id, false, $onLogin);
            }
        }
    }

    /**
     * @param int $opID
     * @return int
     */
    public function countActiveChats(int $opID): int
    {
        $db  = $this->db;
        $sql = 'SELECT COUNT(chats.id) as manyChats FROM chat.chats WHERE chats.status_chat = "activo" AND chats.id_agente = ' . $opID;
        $sql = $db->query($sql);
        $row = $sql->row();

        return $row->manyChats;
    }
}

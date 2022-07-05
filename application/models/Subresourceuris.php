<?php

class Subresourceuris extends CI_Model
{
    public $provider;
    public $tableName;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'subresource_uris';
    }
    /**
     * Columns in the table are:
     * cod_message_sid
     * media
     */

    /**
     * @param array $data
     * @return bool
     */
    public function modelCreateDb(array $data): bool
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
     * Normalize Chat data into a strClass before interaction with DB
     *
     * @param stdClass|null $object
     * @return stdClass|null
     */
    public function normalizeObject($object): ?stdClass
    {
        if ($object === null) {
            return null;
        }

        return $object;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider): void
    {
        $this->provider = $provider;
    }

}
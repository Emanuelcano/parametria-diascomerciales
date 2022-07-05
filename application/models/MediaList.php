<?php

class MediaList extends CI_Model
{
    public $provider;
    public $tableName;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'media_list';
    }
    /**
     * Columns in the table are:
     * sid
     * account_sid
     * parent_sid
     * content_type
     * date_created
     * date_updated
     * uri
     */

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
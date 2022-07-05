<?php

class MessageSid extends CI_Model
{
    public $provider;
    public $tableName;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'message_id';
    }
    /**
     * Columns in the table are:
     * sid
     * date_created
     * date_updated
     * date_sent
     * account_sid
     * tos
     * froms
     * messaging_service_sid
     * body
     * status
     * num_segments
     * num_media
     * direction
     * api_version
     * price
     * price_unit
     * error_code
     * error_message
     * uri
     * assigned
     * date_registry
     * situation
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
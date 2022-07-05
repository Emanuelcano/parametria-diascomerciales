<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

defined('BASEPATH') OR exit('No direct script access allowed');

class Visitor extends Orm_model
{
    public $provider;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'visitantes';
        $this->columns   = [
            'id',
            'solicitud_id',
            'proveedor',
            'nombres',
            'apellidos',
            'num_celular',
            'plan_movil',
            'cedula',
            'fexpedicion',
            'tipo_doc',
            'telefono',
            'notas',
            'email',
            'fecha_creacion',
            'ultima_actualizacion',
            'actualizado_por',
            'third_party_id'
        ];
        $this->dbName    = 'chat';
    }
    /**
     * Columns in the table are:
     * id
     * solicitud_id
     * proveedor
     * nombres
     * apellidos
     * num_celular
     * plan_movil
     * cedula
     * fexpedicion
     * tipo_doc
     * telefono
     * notas
     * email
     * fecha_creacion
     * ultima_actualizacion
     * actualizado_por
     * third_party_id
     */

    /**
     * Get agents from provider API
     *
     * @param string $id
     * @return stdClass
     * @throws GuzzleException
     */
    public function getVisitorFromProviderApi(string $id): \stdClass
    {
        if ($this->provider === 'ZenDesk') {
            $dotEnv = Dotenv\Dotenv::create(FCPATH);
            $dotEnv->load();

            $client   = new Client();
            $response = $client->request(
                'GET',
                'https://www.zopim.com/api/v2/visitors/' . $id,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' .
                            getenv('ZEN_ATOKEN')
                    ]
                ]
            );

            return json_decode($response->getBody()->getContents(), false);
        }

        return (object)['error' => 'Provider not found'];
    }

    /**
     * Create or Update a new record in the DB
     *
     * @param stdClass $object
     * @param bool $update
     * @param bool $returnID
     * @return bool|int
     */
    public function modelDbAction(stdClass $object, bool $update = false, bool $returnID = false)
    {
        $data = [
            'solicitud_id'         => $object->request_id ?? null,
            'proveedor'            => $this->provider ?? -1,
            'nombres'              => $object->names ?? null,
            'apellidos'            => $object->last_names ?? null,
            'num_celular'          => $object->mobile ?? null,
            'plan_movil'           => $object->mobile_plan ?? null,
            'cedula'               => $object->id_number ?? null,
            'fexpedicion'          => $object->id_number_exp_date ?? null,
            'tipo_doc'             => $object->id_type ?? null,
            'telefono'             => $object->phone ?? null,
            'notas'                => $object->notes ?? null,
            'email'                => $object->email ?? null,
            'fecha_creacion'       => $object->created ?? Carbon::now()->format('Y-m-d H:i:s'),
            'ultima_actualizacion' => Carbon::now()->format('Y-m-d H:i:s'),
            'third_party_id'       => $object->id ?? null,
        ];

        $this->db->trans_start();
        $this->db->trans_strict(FALSE);

        if ($update) {
            $data['actualizado_por'] = -1; //-1 means it was updated by the CRON
            $data                    = $this->cleanNullValues($data);
            if ($this->provider) {
                $this->db->update($this->tableName, $data, array('third_party_id' => $object->id));
            } else {
                $this->db->update($this->tableName, $data, array('id' => $object->id));
            }
        } else {
            $data['actualizado_por'] = null;
            $this->db->insert('chat.' . $this->tableName, $data);
            $lastID = $this->db->insert_id();
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        if ($returnID) {
            return $lastID ?? null;
        }

        return true;
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateByID(int $id, array $data): bool
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $this->db->trans_start();
        $this->db->trans_strict(FALSE);

        $data['actualizado_por']      = $_SESSION['id_usuario'] ?? -1;
        $data['ultima_actualizacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $this->db->update('chat.' . $this->tableName, $data, array('id' => $id));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    //Tools

    /**
     * Get all chats associated to this Visitor
     * @return array
     */
    public function getChats(): array
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where('chats', ['id_visitante' => $this->id]);

        return $query->result();
    }

    /**
     * Get a single chat associated to this Visitor
     * @param string $id
     * @return array
     */
    public function getChat(string $id): array
    {
        /** @var CI_DB_result $query */
        $query = $this->db->get_where('chats', ['id' => $id, 'id_visitante' => $this->id]);

        return $query->row();
    }

    /**
     * Check if agent exist by Third Party parameter
     *
     * @param $id
     * @param stdClass $data
     * @param bool $local
     * @return stdClass||null
     */
    public function findBy($id, stdClass $data = null, bool $local = false): ?stdClass
    {
        /** @var CI_DB_result $query */
        if ($local) {
            $query = $this->db->get_where('chat.' . $this->tableName, ['id' => $id]);
        } else {
            $query = $this->db->get_where('chat.' . $this->tableName, ['third_party_id' => $id]);
        }

        if ($this->provider === 'ZenDesk' && $data) {
            $result = $query->row();

            //If exist, update it and return a new query request
            if ($result !== null) {
                $this->modelDbAction($data, true);
                if ($local) {
                    $query = $this->db->get_where('chat.' . $this->tableName, ['id' => $id]);
                } else {
                    $query = $this->db->get_where('chat.' . $this->tableName, ['third_party_id' => $id]);
                }

                return $query->row();
            }

            //If doesn't exist, create it and return a new query request
            $this->modelDbAction($data);
            if ($local) {
                $query = $this->db->get_where('chat.' . $this->tableName, ['id' => $id]);
            } else {
                $query = $this->db->get_where('chat.' . $this->tableName, ['third_party_id' => $id]);
            }

            return $query->row();
        }

        return $query->row();
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

        if ($this->provider === 'ZenDesk') {
            $object->names   = $object->name;
            $object->created = Carbon::createFromTimestamp($object->created)->format('Y-m-d H:i:s');
            unset($object->name);

            return $object;
        }

        if ($this->provider === 'Twilio_WS') {
            $object->solicitud_id = null;
            $object->proveedor    = 'Twilio_WS';
            $object->mobile       = Twilio::getMobileNumber($object->From);

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

    /**
     * @param string $fromNumber
     * @return stdClass|null
     */
    public function findByNumber(string $fromNumber): ?stdClass
    {
        $fromNumber = self::getMobileNumber($fromNumber);
        $query      = $this->db->get_where('chat.' . $this->tableName, ['num_celular' => $fromNumber]);

        return $query->row();
    }

    /**
     * @param string $fromNumber
     * @return stdClass|null
     */
    public function findInRequest(string $fromNumber): ?stdClass
    {
        $fromNumber = self::getMobileNumber($fromNumber);
        $query      = $this
            ->db
            ->query('select * from solicitudes.solicitud where telefono = "' . $fromNumber . '" order by id DESC limit 1');

        return $query->row();
    }

    /**
     * @param stdClass $requestData
     * @param stdClass $visitorData
     * @return stdClass|null
     */
    public function relateRequestData(stdClass $requestData, stdClass $visitorData): ?stdClass
    {
        $visitorData->request_id = $requestData->id;
        $visitorData->names      = $requestData->nombres;
        $visitorData->last_names = $requestData->apellidos;
        $visitorData->id_number  = $requestData->documento;
        $carbonDate              = null;
        if ($requestData->fecha_expedicion !== null && $requestData->fecha_expedicion !== '') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $requestData->fecha_expedicion)
                ->format('Y-m-d H:i:s');
        }
        $visitorData->id_number_exp_date = $carbonDate;
        $visitorData->id_type            = $requestData->id_tipo_documento;
        $visitorData->phone              = $requestData->telefono;
        $visitorData->email              = $requestData->email;

        return $visitorData;
    }

    /**
     * Return the clean phone number of the sender.
     *
     * @param string $number
     * @return string
     */
    public static function getMobileNumber(string $number): string
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

    public function relationChat()
    {
        return ['chat', ['id' => 'id_visitante'], 'type' => 'many'];
    }
}

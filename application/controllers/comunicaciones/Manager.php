<?php

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\PusherException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

defined('BASEPATH') OR exit('No direct script access allowed');

class Manager extends CI_Controller
{

    /**
     * Update visitor Info
     *
     * @return bool
     */
    public function update_visitor(): bool
    {
        $visitorData = $this->input->get();
        $this->load->model('visitor', '', true);
        /** @var Visitor $visitorModel */
        $visitorModel = $this->visitor;

        return $visitorModel->updateByID($visitorData['id'], $visitorData);
    }

    /**
     * @return bool
     * @throws PusherException
     */
    public function close_chat()
    {
        $chatID = $this->input->get('id');
        $this->load->model('chat', '', true);
        /** @var Chat $chatModel */
        $chatModel = $this->chat;

        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode(
                    [
                        'estado' => $chatModel->closeByID($chatID)
                    ]
                )
            );
    }

    /**
     * @return CI_Output|null
     */
    public function get_conversations(): ?CI_Output
    {
        $mobileNumber = $this->input->get()['mobile'];
        $this->load->model('visitor', '', true);
        /** @var Visitor $modelVisitor */
        $modelVisitor = $this->visitor;
        $visitor      = $modelVisitor->findByNumber($mobileNumber);

        if ($visitor !== null) {
            $sql   = 'SELECT * from chat.new_chats WHERE id_operador = ' . $visitor->id . ' LIMIT 6';
            $query = $this->db->query($sql);

            /** @var CI_DB_mysqli_result $query */
            return $this->output
                ->set_content_type('application/json')
                ->set_output(
                    json_encode(
                        $query->result()
                    )
                );
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function get_chat()
    {
        $this->load->model('chat', '', true);
        /** @var Chat $chatModel */
        $chatModel            = $this->chat;
        $response             = [];
        $chatId               = $this->input->get()['id'];
        $response['chat']     = $chatModel->findByID(
            $chatId,
            [
                $chatModel->relationVisitor(),
            ]
        );
        $response['mensajes'] = $this->getMessages($response['chat']['chats']['id']);
        $response['operador'] = null;
        $query                = $this->db->get_where(
            'gestion.operadores',
            ['idoperador' => $response['chat']['chats']['id_agente']]
        );
        $operator             = $query->row();
        if ($operator !== null) {
            $response['operador'] = $operator;
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode($response)
            );
    }

    /**
     * @param $id
     * @return array
     */
    public function getMessages($id): array
    {

        $this->load->model('receivewhatsapp', '', true);
        $this->load->model('sentwhatsapp', '', true);

        $sentMessages     = $this->db->get_where('chat.sent_messages', ['id_chat' => $id])
                                     ->result_array();
        $receivedMessages = $this->db->get_where('chat.received_messages', ['id_chat' => $id])
                                     ->result_array();
        foreach ($sentMessages as $key => $message) {
            $message['sent']     = true;
            $message['received'] = false;

            $sentMessages[$key] = $message;
        }
        foreach ($receivedMessages as $key => $message) {
            $message['sent']     = false;
            $message['received'] = true;

            $receivedMessages[$key] = $message;
        }

        $messages = array_merge($sentMessages, $receivedMessages);
        usort($messages, static function ($message1, $message2) {
            return $message1['fecha_creacion'] <=> $message2['fecha_creacion'];
        });

        return $messages;
    }

    /**
     * Update is_alive_timestamp in DB
     *
     * @return bool
     * @throws PusherException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function im_alive(): bool
    {
        $operatorID = $this->input->get()['op'];
        $this->load->model('operatorStatus', '', true);
        /** @var OperatorStatus $opStatusModel */
        $opStatusModel = $this->operatorStatus;

        return $opStatusModel->putImAlive($operatorID);
    }

    /**
     * Check for IDLE operators, will flag they as is_idle so they can not receive anymore chat and if they have
     * active chats, will transfer those chats to another operators.
     *
     * @return bool
     * @throws PusherException
     *
     * php /var/www/backend/index.php comunicaciones manager check_for_idle_op
     * @deprecated
     */
    public function check_for_idle_op(): bool
    {
        if (!is_cli()) {
            show_404();
        }

//        $this->load->model('operatorStatus', '', true);
//        /** @var OperatorStatus $opStatusModel */
//        $opStatusModel       = $this->operatorStatus;
//        $onlineOperatorsList = $opStatusModel->getOnlineOperators();
//
//        foreach ($onlineOperatorsList as $operator) {
//            $lastAlivePing   = Carbon::parse($operator->is_alive_timestamp);
//            $now             = Carbon::now();
//            $differenceDates = $now->diffInMinutes($lastAlivePing);
//
//            if ($differenceDates >= 10) {
//                $opStatusModel->putIdle($operator->operador_id);
//            }
//        }

        echo 'Done!' . PHP_EOL;
        echo 'Deprecated' . PHP_EOL;

        return true;
    }

    /**
     * Check updated visitor info
     */
    public function chat_visitor_info()
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode(['response' => 'Exito'])
            );
    }

    /**
     * Obtains a list of contacts by certain parameters.
     * @return mixed
     * @deprecated
     */
    public function get_contacts()
    {
        $this->load->model('operatorRelation', '', true);
        /** @var OperatorRelation $model */
        $model       = $this->operatorRelation;
        $get         = $this->input->get();
        $page        = $get['page'] ?? 0;
        $contactsArr = $model->findByOpIdOrderDirectory($get['user'], $page);

        if ($contactsArr !== false) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(
                    json_encode($contactsArr)
                );
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode(['error' => true, 'description' => 'SQL bool error'])
            );
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function search_contact()
    {
        $this->load->model('operatorRelation', '', true);
        /** @var OperatorRelation $model */
        $model       = $this->operatorRelation;
        $get         = $this->input->get();
        $contactsArr = $model->findByKeyword($get['user'], $get['keyword']);

        if ($contactsArr !== false) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(
                    json_encode($contactsArr)
                );
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode(['error' => true, 'description' => 'SQL bool error'])
            );
    }

    /**
     * Actualiza un chat para cambiar los flags de leído y de abierto.
     * Está visible en pantalla y leído.
     *
     * @return void
     */
    public function chat_on_display(): void
    {
        $this->load->model('chat', '', true);
        /** @var Chat $chatModel */
        $chatModel  = $this->chat;
        $get        = $this->input->get();
        $chatID     = $get['chat_id'];
        $updateData = [
            'sin_leer' => 0,
            'abierto'  => 1
        ];

        if ($get['chat_status'] === 'vencido' && $get['chat_read'] === '1') {
            $updateData['status_chat'] = 'revision';
            $chatModel->updateRelationStateChat('revision', $chatID);
        }

        $chatModel->doUpdate($chatID, $updateData);
    }


    /**
     * Actualiza un chat para cambiar el flag de abierto a cero. No está visible en pantalla.
     *
     * @return void
     */
    public function chat_out_display(): void
    {
        $this->load->model('chat', '', true);
        /** @var Chat $chatModel */
        $get        = $this->input->get();
        $chatID     = $get['chat_id'];
        $updateData = [
            'abierto' => 0
        ];
        $chatModel  = $this->chat;
        $chatModel->doUpdate($chatID, $updateData);
    }
}
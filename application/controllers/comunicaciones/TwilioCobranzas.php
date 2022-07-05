<?php

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\PusherException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
 

defined('BASEPATH') OR exit('No direct script access allowed');

class TwilioCobranzas extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        
       $this->load->model('Chatbot_model','',TRUE);
       $this->Cobranzas = array(5,6); //15185188
    }
    
    /**
     * @return void
     * @throws GuzzleException
     * @throws PusherException
     * SOLO PARA COBRANZAS PRODUCCION +5715185188
     * SOLO PARA COBRANZAS TESTING +5713289049
     */
    public function get_new_message(): void
    {
        $this->load->model('chatbot_model', '', true);
        $chatbot = $this->chatbot_model;
        $msgArray = (object)$this->input->post();
        $sinString = explode("whatsapp:",$msgArray->From);
        $msgArray->origen = "COBRANZAS"; //'ORIGINACION','COBRANZAS'
        $msgArray->telfNum = $sinString[1];
        // var_dump($msgArray);die;
        // if($sinString[1] == "+5491170366167" || $sinString[1] == "+5491161331406" || $sinString[1] == "+541170366167" || $sinString[1] == "+541161331406"){
           
            
            $msgArray->metodo_notificacion = 2;
            // var_dump($msgArray);die;
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_BACKEND.'api/ApiFilterWhatsApp/get_new_message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $msgArray,
                
            ));
            
            $response = curl_exec($curl);
            //  var_dump($response);die;
            curl_close($curl);
            $consulta_word = json_decode($response);


        $chatAct = $chatbot->activedChatbot(2);
        
        $msgArray = (object)$this->input->post();
        $this->load->model('ChatCobranzas', '', true);
        /** @var Chat $model */
        $model = $this->ChatCobranzas;
        $model->setProvider('Twilio_WS');


        $dotEnv = Dotenv\Dotenv::create(FCPATH);
        $dotEnv->load();
       
        if ($msgArray->From !== null && $msgArray->From !== '') {
            //Retorna el nuevo número como un integer plano.
            $senderNumber = self::getMobileNumber($msgArray->From);
            if (ENVIRONMENT == 'production') {
                $receiverNumber = TWILIO_PROD_COBRANZAS;
            }else{
                $receiverNumber = TWILIO_TEST_COBRANZAS;
            }
            //Busca y retorna (si existe) un chat activo usando como ancla el número del mensaje recibido.
            // $chatEntity = $model->findBySenderNumber($senderNumber);
            $chatEntity = $model->findBySenderNumber($senderNumber, $receiverNumber);

            if ($chatEntity) {
                //De existir un chat activo, sólo registra el nuevo mensaje.
                $model->registerReceivedMessage($chatEntity, $msgArray);

                # pusher send message
                //$pusher = new Pusher\Pusher('665335991888e0778882','f6a64704f1d7a264aef9','894798', ['cluster' => 'mt1']);
                $pusher = new Pusher\Pusher(
                    getenv('PUSHER_KEY'),
                    getenv('PUSHER_SECRET'),
                    getenv('PUSHER_APP_ID'),
                    ['cluster' => getenv('PUSHER_CLUSTER')]
                );
                $res = $pusher->trigger(
                    'channel-chat-'.$chatEntity["id"],
                    'received-message-component',
                    [
                        'body' => $msgArray->Body,
                        'received' => 1,
                        'sent' => 0,
                        'media_url0' => $msgArray->MediaUrl0,
                        'media_content_type0' => $msgArray->MediaContentType0,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'nombre_apellido_operador' => '',
                        'id_chat' => $chatEntity["id"],
                        'status_chat' => $chatEntity["status_chat"]
                    ]
                    );
                    
            } elseif ($chatEntity == false) {
                //Al no existir chat activo, debe crear uno nuevo.
                //Normaliza la data antes de proceder a registrar el nuevo chat.
                $newObject = $model->normalizeObject($msgArray);

                if ($newObject !== null) {
                    //Método para crear un nuevo chat.
                    $model->modelDbAction($newObject);
                }
            }
            # SI CHATBOT ESTA HABILITADO
            //if ($chatAct == true) {
                // if (in_array($senderNumber, ['+5491127083804','+5491126306639','+5491160302616','+5491173634911','+5491128884597','3022529113'])){
                    $checkChat = $model->checkChat($senderNumber, $receiverNumber);
                    $checkChatOp192 = $model->checkChatOp192($senderNumber, $receiverNumber);
                    // var_dump($checkChat); die;
                    
                    if ($checkChat) {
                        // $fp = fopen("fichero.txt", "a+");
                        // fputs($fp, $checkChat);
                        // fclose($fp);
                        $model->updateChatbotTest($senderNumber, $receiverNumber);
                        ## ENDPOINT CHATBOT
                        $url = CHATBOT_URL.'chatbot/chatbot_live_cobranza/cliente/';

                        $params = array(
                          'telefono_cliente' => $senderNumber,
                          'mensaje'  => $msgArray->Body,
                        );

                        if ($msgArray->NumMedia == 1) {                          
                          $params = array(
                              'telefono_cliente' => $senderNumber,
                              'mensaje'  => $msgArray->Body,
                              'NumMedia'  => $msgArray->NumMedia,
                              'MediaContentType'  => $msgArray->MediaContentType0,
                              'MediaUrl'  => $msgArray->MediaUrl0,
                          );
                        }
                        $curl = curl_init($url);      
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt_array($curl, array(
                           CURLOPT_CUSTOMREQUEST => "POST",
                           CURLOPT_POSTFIELDS => $params,
                       ));        
                        curl_setopt($curl, CURLINFO_HEADER_OUT, true);                                
                        $results = curl_exec($curl); 
                        // $response = str_replace("\xEF\xBB\xBF", '', $results);

                        curl_close($curl);
                        
                    }elseif ($checkChatOp192) {
                        // $fp = fopen("fichero.txt", "a+");
                        // fputs($fp, $checkChatOp192);
                        // fclose($fp);
                        $model->updateChatbotTest($senderNumber, $receiverNumber);
                        ## ENDPOINT CHATBOT
                        $url = CHATBOT_URL.'chatbot/chatbot_live_cobranza/cliente/';    
                        $params = array(
                          'telefono_cliente' => $senderNumber,
                          'mensaje'  => $msgArray->Body,
                        );

                        if ($msgArray->NumMedia == 1) {                          
                          $params = array(
                            'telefono_cliente' => $senderNumber,
                            'mensaje'  => $msgArray->Body,
                            'NumMedia'  => $msgArray->NumMedia,
                            'MediaContentType'  => $msgArray->MediaContentType0,
                            'MediaUrl'  => $msgArray->MediaUrl0,
                          );
                        }
                        $curl = curl_init($url);      
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt_array($curl, array(
                           CURLOPT_CUSTOMREQUEST => "POST",
                           CURLOPT_POSTFIELDS => $params,
                       ));        
                        curl_setopt($curl, CURLINFO_HEADER_OUT, true);                                
                        $results = curl_exec($curl); 
                        // $response = str_replace("\xEF\xBB\xBF", '', $results);

                        curl_close($curl);
                    }
                // }

            //}  
        }   
    }

    /**
     * Verifica chats existentes con el id_agente = null, los organiza del más antiguo al más reciente y les asigna
     * el operador correspondiente.
     *
     * @return bool
     * @throws PusherException
     *
     * php /var/www/backend/index.php comunicaciones twilio dispatcher_cron
     */
    public function dispatcher_cron(): bool
    {
        // if (!is_cli()) {
        //     show_404();
        // }

        $this->load->model('ChatCobranzas', '', true);
        /** @var Chat $chatModel */
        $chatModel  = $this->ChatCobranzas;
        /**
         * Recibir el listado de chats en espera de asignación.
         */
        $queueChats = $chatModel->getChatsQueue();
        /**
         * Asigna operadores a los chats de la lista anterior.
         */
        foreach ($queueChats as $key => $chat) {
            $idOperador = $this->AsignacionOp($chat['id']);
            $updateOp = $chatModel->updateOperador($chat['id'],$idOperador);

            if ($updateOp) {
               echo "asignado al Operador: $idOperador el Idchat ".$chat['id']."\n"; 
            }else{
                echo "no asignado";
            }
        
        }
        
        echo 'Done!' . PHP_EOL;

        return true;
    }

    /**
     * Enviar un nuevo mensaje desde el front.
     *
     * @return mixed
     * @throws ConfigurationException
     * @throws TwilioException
     * @throws PusherException
     */
    
    public function send_new_message2()
    {
        /** @var array $msgData
         * [
         *  'chatID' => int,
         *  'message' => string
         * ]
         */
        $msgData = $this->input->post();
        $fileUrl = null;
        $dotEnv  = Dotenv\Dotenv::create(FCPATH);
        $dotEnv->load();

        //If media, media upload Logic
        if (isset($_FILES['media'])) {
            $config['upload_path']   = FCPATH . 'media/chat_medias';
            $config['allowed_types'] = 'gif|jpg|png|pdf';

            $this->load->library('upload', $config);
            /** @var CI_Upload $uploadInfo */
            $uploadInfo = $this->upload;
            if (!$uploadInfo->do_upload('media')) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(
                            [
                                'error'       => 500,
                                'description' => 'No se ha podido subir el archivo, intente nuevamente.'
                            ]
                        )
                    );
            }

            //URL Dymamic
            $fileUrl = getenv('MEDIA_ORIGIN_URL') . 'media/chat_medias/' . $uploadInfo->data()['file_name'];
        }
        //End Upload Logic

        $this->load->model('ChatCobranzas', '', true);
        /** @var Chat $model */
        $model = $this->ChatCobranzas;
        $model->setProvider('Twilio_WS');
        $chat = $model->findByID($msgData['chatID']);

        //Caso error 3 reintentos
        $i = 0;
        while ($chat === null && $i < 3) {
            $chat = $model->findByID($msgData['chatID']);
            ++$i;
            //Espera 150 milisegundos entre cada intento
            sleep(0.15);
        }

        if ($i < 3) {
            $whatsAppNumber = $chat['new_chats']['from'];
            $sid            = getenv('TWILIO_SID');
            $token          = getenv('TWILIO_AUTH');
            $twilio         = new Client($sid, $token);
            $sendData       = [
                'from' => 'whatsapp:' . getenv('WS_NUMBER2'),
                'body' => $msgData['message']
            ];

            if ($fileUrl !== null) {
                $sendData['mediaUrl'] = [
                    $fileUrl
                ];
            }

            if (strpos($whatsAppNumber, '+') === 0) {
                $whatsAppNumber = 'whatsapp:' . $whatsAppNumber;
            } else {
                $whatsAppNumber = 'whatsapp:+57' . $whatsAppNumber;
            }

            try {
                $message = $twilio->messages->create($whatsAppNumber, $sendData);
                $message = (object)$message->toArray();

                $this->load->model('sentwhatsapp', '', true);
                /** @var Sentwhatsapp $modelSentWhatsApp */
                $modelSentWhatsApp = $this->sentwhatsapp;
                //Si no se ha envíado algún archivo
                if ($fileUrl === null) {
                    $sentMessageEntity = $modelSentWhatsApp->modelCreateDb(
                        $message,
                        $chat['new_chats']['id'],
                        $msgData['operatorID'],
                        true,
                        true
                    );

                    $model->updateChatSentMessage($chat['new_chats']['id'], $message,
                                                  $sentMessageEntity['creationDate']);
                } elseif (isset($uploadInfo)) {
                    //Si se ha envíado algún archivo
                    $sentMessageEntity = $modelSentWhatsApp->modelCreateDb(
                        $message,
                        $chat['new_chats']['id'],
                        $msgData['operatorID'],
                        true,
                        true
                    );
                    if (is_array($sentMessageEntity)) {
                        $modelSentWhatsApp->registerMedias(
                            $sentMessageEntity['lastID'],
                            $fileUrl
                        );
                    }

                    $model->updateChatSentMessage($chat['new_chats']['id'], $message,
                                                  $sentMessageEntity['creationDate']);
                }


                //consultamos el mensaje enviado
                $sentMensaje = $model->getSentMessages(['id_chat' => $chat['new_chats']['id'], 'id_sent' => $sentMessageEntity['lastID']]);
                if (!empty($sentMensaje)) {
                    //pusher
                    $pusher = new Pusher\Pusher(
                        getenv('PUSHER_KEY'),
                        getenv('PUSHER_SECRET'),
                        getenv('PUSHER_APP_ID'),
                        ['cluster' => getenv('PUSHER_CLUSTER')]
                    );
                    $res = $pusher->trigger(
                        'channel-chat-'.$chat['new_chats']['id'],
                        'sent-message-component',
                        [
                            'body' => $sentMensaje[0]['body'],
                            'received' => 0,
                            'sent' => 1,
                            'media_url0' => $sentMensaje[0]['media_url0'],
                            'media_content_type0' => $sentMensaje[0]['media_content_type0'],
                            'fecha_creacion' =>  $sentMensaje[0]['fecha_creacion'],
                            'nombre_apellido_operador' => $sentMensaje[0]['nombre_apellido'],
                            'id_chat' => $chat['new_chats']['id'],
                            'sms_status' => $sentMensaje[0]['sms_status'],
                            'sms_sid' => $sentMensaje[0]['sms_message_sid']
                        ]
                        );
                }


                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(
                            [
                                'messages' => $this->getMessages($msgData['chatID']),
                                'user'     => ''
                            ]
                        )
                    );
            } catch (TwilioException $e) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(
                            [
                                'error'       => 500,
                                'description' => 'Error al enviar el mensaje. Trata de nuevo.'
                            ]
                        )
                    );
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode(
                    [
                        'error'       => 404,
                        'description' => 'El chat solicitado no existe'
                    ]
                )
            );
    }

    /**
     * Endpoint que recibe la solicitud de envío de un nuevo template desde el frontend del operador SOLO DE COBRANZAS.
     *
     * @return mixed
     * @throws ConfigurationException
     * @throws GuzzleException
     * @throws TwilioException
     * @throws PusherException
     */
    public function send_template_message()
    {
        /** @var array $msgData
         * [
         *  'chatID' => int,
         *  'message' => string
         * ]
         */

        //Load models
        $this->load->helper('dispatcher');
        $this->load->helper('cookie');
        $this->load->model('ChatCobranzas', '', true);
        $chatModel  = $this->ChatCobranzas;

        if ($this->input->post('userID')) {
          $sessionCookie = $this->ChatCobranzas->getAuthentication($this->input->post('userID'));
          $operatorID    = (int)$sessionCookie->idoperador;
          $msgData['id_cliente']    = $this->input->post('idCliente');
          $msgData['template'] = $this->input->post('Template');
          $request    = $this->db->select('id_cliente, numero AS telefono, contacto AS nombres')
                                 ->get_where('maestro.agenda_telefonica', ['id_cliente' => $msgData['id_cliente']])
                                 ->row();
        }else{
          $sessionCookie = get_cookie('__data_operator');
          $sessionCookie = explode(',', $sessionCookie);
          $operatorID    = (int)$sessionCookie[0];
          $msgData    = $this->input->get();
          $solID      = $msgData['solID']; //Get Solicitud
          $request    = $this->db->select('telefono, nombres')
                                 ->get_where('solicitudes.solicitud', ['id' => $solID])
                                 ->row();
        }

        /**
         * Verificación de la cookie de sesión que nos indicará el tipo y la información del agente.
         */
        if ($sessionCookie !== null) {
            /**
             * Obtiene el chat por su relación con el número telefónico
             */
            if (ENVIRONMENT == 'production') {
                $receiverNumber = TWILIO_PROD_COBRANZAS;
            }else{
                $receiverNumber = TWILIO_TEST_COBRANZAS;
            }
            $chatEntity = $chatModel->findBySenderNumber($request->telefono, $receiverNumber);

            if ($chatEntity === null || $chatEntity === false) {
                /**
                 * Por lógica, si el chat es Null, nunca ha habido comunicación por WhatsApp, por ende se envía template
                 * y nada más.
                 */
                /**
                 * Los valores de conexión de Twilio están almacenados en el .env del root del proyecto.
                 */
                $dotEnv = Dotenv\Dotenv::create(FCPATH);
                $dotEnv->load();
                $nowDate = Carbon::now()
                                 ->format('Y-m-d H:i:s');
                /**
                 * Se obtiene el template desde la DB
                 */
                $body    = getTemplatesByTwilioID($msgData['template']);
                $body    = $body !== false && $body !== null ? $body->msg_string : '';
                /**
                 * Data para crear el nuevo chat relacionado al número del template
                 */
                $data    = [
                    'agent_id'               => $operatorID,
                    'From'                   => $request->telefono,
                    'To'                     => str_replace('+57', '', getenv('WS_NUMBER2')),
                    'Body'                   => $body,
                    'started_by'             => 'operator',
                    'type'                   => 'solicitante',
                    'sin_leer'               => 0,
                    'fecha_ultima_recepcion' => $nowDate,
                    'fecha_ultimo_envio'     => $nowDate,
                    'status_chat'            => 'pendiente',
                    'AccountSid'             => 'AC6b46e7311003df1b23ce538a408a054c'
                ];
                /**
                 * El proveedor del mensaje es Twilio_WS
                 */

                $chatModel->setProvider('Twilio_WS');
                $lastID = $chatModel->modelDbAction((object)$data, false, true);

                sendTemplate2($msgData['template'], $request, (int)$lastID, $operatorID);
                addChatFromTemplate($lastID, $operatorID);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(['template' => true])
                    );
            }

            /**
             * Si el chat esta en estatus de vencido o en revisión.
             */
            if ($chatEntity['status_chat'] === 'vencido' || $chatEntity['status_chat'] === 'revision' || $chatEntity['status_chat'] === 'pendiente') {
                $chatID     = $chatEntity['id'];
                $nowDate    = Carbon::now()
                                    ->format('Y-m-d H:i:s');
                $body       = getTemplatesByTwilioID($msgData['template']);
                $body       = $body !== false && $body !== null ? $body->msg_string : '';
                $updateData = [
                    'ultimo_mensaje'     => $body,
                    'status_chat'        => 'pendiente',
                    'fecha_ultimo_envio' => $nowDate,
                ];

                /**
                 * Si el chat no posee operador asignado.
                 */

                $updateData['id_operador'] = $operatorID;
                $chatModel->updateRelationOpChat($operatorID, $chatID);


                /**
                 * Se actualiza la entidad Chat
                 */
                $chatModel->doUpdate($chatID, $updateData);
                /**
                 * Se crea un nuevo registro de cambio de estado para el Chat
                 */
                $chatModel->updateRelationStateChat('pendiente', $chatID);
                sendTemplate2($msgData['template'], $request, (int)$chatEntity['id'], $operatorID);
                addChatFromTemplate($chatEntity['id'], $operatorID);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(['template' => true])
                    );
            }

            /**
             * Si no esta vencido.
             * No envía template (cuesta dinero) en lugar de eso envía notificación para levantar el chat.
             */
            addChatFromTemplate($chatEntity['id'], $operatorID);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(
                    json_encode(
                        [
                            'chat' => true
                        ]
                    )
                );
        }
    }





     /**
     * Endpoint que recibe la solicitud de envío de un nuevo template desde el frontend del operador SOLO DE COBRANZAS.
     *
     * @return mixed
     * @throws ConfigurationException
     * @throws GuzzleException
     * @throws TwilioException
     * @throws PusherException
     */
    public function send_template_message_new()
    {
        /** @var array $msgData
         * [
         *  'chatID' => int,
         *  'message' => string
         * ]
         */

        //Load models
        $this->load->helper('dispatcher');
        $this->load->helper('cookie');
        $this->load->model('ChatCobranzas', '', true);
        $chatModel  = $this->ChatCobranzas;

        if ($this->input->post('userID')) {
          $sessionCookie = $this->ChatCobranzas->getAuthentication($this->input->post('userID'));
          $operatorID    = (int)$sessionCookie->idoperador;
          $msgData['template'] = $this->input->post('Template');
          $msgData['id_template'] = $this->input->post('id_template');
          $request    = $this->input->post('phoneN');
        }else{
          $sessionCookie = get_cookie('__data_operator');
          $sessionCookie = explode(',', $sessionCookie);
          $operatorID    = (int)$sessionCookie[0];
	
			if ($operatorID == 0) {
				$operatorID = 192;
			}

          $msgData['template'] = $this->input->post('Template');
          $msgData['id_template'] = $this->input->post('id_template');
          $request    = $this->input->post('phoneN');
        }

        $this->load->model('operadores/Operadores_model', '', true);
        $tipoOperador = $this->Operadores_model->get_lista_operadores_by(['idoperador'=>$operatorID]);

        /**
         * Verificación de la cookie de sesión que nos indicará el tipo y la información del agente.
         */
		if($msgData['id_template'] == '124'){

			$url = CHATBOT_URL.'chatbot/chatbot_live/preventiva/';
		
			$params = [
				'telefono' => $request
			];
			
			$curl = curl_init($url);      
			curl_setopt($curl, CURLOPT_VERBOSE, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt_array($curl, array(
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $params,
			));        
			curl_setopt($curl, CURLINFO_HEADER_OUT, true);                                
			$results = curl_exec($curl); 
			// $response = str_replace("\xEF\xBB\xBF", '', $results);
			curl_close($curl);

		}
		
        if ($sessionCookie !== null) {
            /**
             * Obtiene el chat por su relación con el número telefónico
             */
            if (ENVIRONMENT == 'production') {
                $receiverNumber = TWILIO_PROD_COBRANZAS;
            }else{
                $receiverNumber = TWILIO_TEST_COBRANZAS;
            }
            $chatEntity = $chatModel->findBySenderNumber($request, $receiverNumber);

            if ($chatEntity === null || $chatEntity === false) {
                /**
                 * Por lógica, si el chat es Null, nunca ha habido comunicación por WhatsApp, por ende se envía template
                 * y nada más.
                 */
                if ( empty($tipoOperador) || (!in_array($tipoOperador[0]['tipo_operador'], OPERADORES_COBRANZAS))) {
                    $operatorID = 42;
                }
                /**
                 * Los valores de conexión de Twilio están almacenados en el .env del root del proyecto.
                 */
                $dotEnv = Dotenv\Dotenv::create(FCPATH);
                $dotEnv->load();
                $nowDate = Carbon::now()
                                 ->format('Y-m-d H:i:s');
                /**
                 * Se obtiene el template desde la DB
                 */
                $body    = $msgData['template'];
                //$body    = $body !== false && $body !== null ? $body->msg_string : '';
                /**
                 * Data para crear el nuevo chat relacionado al número del template
                 */
                $data    = [
                    'agent_id'               => $operatorID,
                    'From'                   => $request,
                    'To'                     => str_replace('+57', '', getenv('WS_NUMBER2')),
                    'Body'                   => $body,
                    'started_by'             => 'operator',
                    'type'                   => 'solicitante',
                    'sin_leer'               => 0,
                    'fecha_ultima_recepcion' => $nowDate,
                    'fecha_ultimo_envio'     => $nowDate,
                    'status_chat'            => 'pendiente',
                    'AccountSid'             => 'AC6b46e7311003df1b23ce538a408a054c'
                ];

				if(empty($chatEntity['documento'])){

                    $solicitud = $chatModel->get_solicitud([
                        'telefono' => $request
                    ]);
                
                    if(count((array)$solicitud) > 0){
                        $data['nombres']   = $solicitud->nombres;
                        $data['apellidos'] = $solicitud->apellidos;
                        $data['email'] 	   = $solicitud->email;
                        $data['documento'] = $solicitud->documento;
                        $data['type'] = 'solicitante';
                    }
                }
               

                /**
                 * El proveedor del mensaje es Twilio_WS
                 */

                $chatModel->setProvider('Twilio_WS');
                $lastID = $chatModel->modelDbAction((object)$data, false, true);

                sendTemplate2New($msgData, $request, (int)$lastID, $operatorID);
                addChatFromTemplate($lastID, $operatorID);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(['template' => true])
                    );
            }

            /**
             * Si el chat esta en estatus de vencido o en revisión.
             */
            if ($chatEntity['status_chat'] === 'vencido' || $chatEntity['status_chat'] === 'revision' || $this->input->post('test_template') == true || $chatEntity['status_chat'] === 'pendiente') {
                if (!empty($tipoOperador) && !in_array($tipoOperador[0]['tipo_operador'], OPERADORES_COBRANZAS)) {
                    $operatorID = (int)$chatEntity['id_operador'];
                }
                $chatID     = $chatEntity['id'];
                $nowDate    = Carbon::now()
                                    ->format('Y-m-d H:i:s');
                $body    = $msgData['template'];
                //$body       = $body !== false && $body !== null ? $body->msg_string : '';
                $updateData = [
                    'ultimo_mensaje'     => $body,
                    'status_chat'        => 'pendiente',
                    'fecha_ultimo_envio' => $nowDate
                ];

				if(empty($chatEntity['documento'])){

                    $solicitud = $chatModel->get_solicitud([
                        'telefono' => $request
                    ]);
                
                    if(count((array)$solicitud) > 0){
                        $data['nombres']   = $solicitud->nombres;
                        $data['apellidos'] = $solicitud->apellidos;
                        $data['email'] 	   = $solicitud->email;
                        $data['documento'] = $solicitud->documento;
                        $data['type'] = 'solicitante';
                    }
                }
               

				
                /**
                 * Si el chat no posee operador asignado.
                 */

                $updateData['id_operador'] = $operatorID;
                $chatModel->updateRelationOpChat($operatorID, $chatID);


                /**
                 * Se actualiza la entidad Chat
                 */
                $chatModel->doUpdate($chatID, $updateData);
                /**
                 * Se crea un nuevo registro de cambio de estado para el Chat
                 */
                $chatModel->updateRelationStateChat('pendiente', $chatID);
                sendTemplate2New($msgData, $request, (int)$chatEntity['id'], $operatorID);
                addChatFromTemplate($chatEntity['id'], $operatorID);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(['template' => true])
                    );
            }

            /**
             * Si no esta vencido.
             * No envía template (cuesta dinero) en lugar de eso envía notificación para levantar el chat.
             */
            addChatFromTemplate($chatEntity['id'], $operatorID);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(
                    json_encode(
                        [
                            'chat' => true
                        ]
                    )
                );
        }
    }


    /**
     * @return mixed
     */
    public function get_chat()
    {
        $get = $this->input->get();
        $this->load->model('ChatCobranzas', '', true);
        /** @var Chat $model */
        $model = $this->ChatCobranzas;
        $chat  = $model->findByID(
            (int)$get['id']
        );

        //In Miliseconds
        $lastReceptionDate     = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $chat['new_chats']['fecha_ultima_recepcion']
        );
        $endWindowsDate        = $lastReceptionDate->clone()
                                                   ->addHours(24);
        $nowTime               = Carbon::now();
        $diffInMilliseconds    = $nowTime->diffInMilliseconds($endWindowsDate, false);
        $endTimeInMilliseconds = $diffInMilliseconds < 0 ? 0 : $diffInMilliseconds;

    

        $chatData              = [
            'id'                           => $chat['new_chats']['id'],
            'num_celular'                  => $chat['new_chats']['from'],
            'nombre_completo'              => ucwords(mb_strtolower($chat['new_chats']['nombres'], 'UTF-8') ) . ' ' . ucwords( mb_strtolower( $chat['new_chats']['apellidos'], 'UTF-8') ),
            'documento'                    => $chat['new_chats']['documento'],
            'email'                        => $chat['new_chats']['email'],
            'sin_leer'                     => $chat['new_chats']['sin_leer'],
            'status_chat'                  => $chat['new_chats']['status_chat'],
            'def_date'                     => $chat['new_chats']['fecha_ultima_recepcion'] > $chat['new_chats']['fecha_ultimo_envio'] ?
                $chat['new_chats']['fecha_ultima_recepcion'] : $chat['new_chats']['fecha_ultimo_envio'],
            'fecha_ultima_recepcion'       => $chat['new_chats']['fecha_ultima_recepcion'],
            'fecha_ultimo_envio'           => $chat['new_chats']['fecha_ultimo_envio'],
            'timeWindowsEndInMilliseconds' => $endTimeInMilliseconds,
            'last_message'                 => $chat['new_chats']['ultimo_mensaje'],
            'chat_id'                      => $chat['new_chats']['id']
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode(
                    $chatData
                )
            );
    }

    /**
     * @return mixed
     * @throws PusherException
     */
    public function get_chats()
    {
        $this->load->model('ChatCobranzas', '', true);
        $this->load->model('operatorWS', '', true);
        $this->load->model('Solicitud_m', '', true);
        /** @var Chat $model */
        /** @var OperatorWS $opModel */
        $model   = $this->ChatCobranzas;
        $opModel = $this->operatorWS;
        $Solicitud_m    = $this->Solicitud_m;
        $get     = $this->input->get();
        $opData  = $model->getChatsActiveByOp((int)$get['user']);

        if ($opData === false) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(
                    json_encode(
                        [
                            'error'       => 500,
                            'description' => 'Error al obtener los chats, por favor, refresca el navegador.'
                        ]
                    )
                );
        }

        if (!empty($opData) && $opData !== false) {
            $chatsData = [];
            foreach ($opData as $chat) {
                //In Miliseconds
                $lastReceptionDate     = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $chat['fecha_ultima_recepcion']
                );
                $endWindowsDate        = $lastReceptionDate->clone()
                                                           ->addHours(24);
                $nowTime               = Carbon::now();
                $diffInMilliseconds    = $nowTime->diffInMilliseconds($endWindowsDate, false);
                $endTimeInMilliseconds = $diffInMilliseconds < 0 ? 0 : $diffInMilliseconds;

                //get solicitud del cliente si el documento existe 
                $expediente = 0;
                if(!is_null($chat['documento'])){
                    //$this->Solicitud_m = $this->load->model('Solicitud_m','', true);
                    $solicitud = $Solicitud_m->getSolicitudesBy(['documento'=> $chat['documento']]);
                    $expediente = (empty($solicitud))? 0:$solicitud[0]->id_credito;
                }
        
                $chatData    = [
                    'id'                           => $chat['id'],
                    'num_celular'                  => $chat['num_celular'],
                    'nombre_completo'              => ucwords(mb_strtolower($chat['nombres'], 'UTF-8') ) . ' ' . ucwords( mb_strtolower( $chat['apellidos'], 'UTF-8') ),
                    'documento'                    => $chat['documento'],
                    'expediente'                   => $expediente,
                    'email'                        => $chat['email'],
                    'sin_leer'                     => $chat['sin_leer'],
                    'status_chat'                  => $chat['status_chat'],
                    'def_date'                     => $chat['fecha_ultima_recepcion'] > $chat['fecha_ultimo_envio'] ?
                        $chat['fecha_ultima_recepcion'] : $chat['fecha_ultimo_envio'],
                    'fecha_ultima_recepcion'       => $chat['fecha_ultima_recepcion'],
                    'fecha_ultimo_envio'           => $chat['fecha_ultimo_envio'],
                    'timeWindowsEndInMilliseconds' => $endTimeInMilliseconds,
                    'last_message'                 => $chat['ultimo_mensaje'],
                    'chat_id'                      => $chat['chat_id']
                ];
                $chatsData[] = $chatData;
            }

            usort($chatsData, static function ($a, $b) {
                return $b['def_date'] <=> $a['def_date'];
            });

            return $this->output
                ->set_content_type('application/json')
                ->set_output(
                    json_encode(
                        array_values($chatsData)
                    )
                );
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode(
                    array_values([])
                )
            );
    }

    public function chat_messages()
    {
        $get = $this->input->get();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode(
                    [
                        'messages' => $this->getMessages($get['id'], $get['documento'], $get['fromNumber']),
                        'user'     => ''
                    ]
                )
            );
    }

    /**
     * @return CI_Output|null
     */
    public function get_earliest_chats(): ?CI_Output
    {
        $get = $this->input->get();
        $this->load->model('ChatCobranzas', '', true);
        /** @var Chat $chatModel */
        $chatModel             = $this->ChatCobranzas;
        $chatArray             = [
            'chats' => [
                'fecha_inicio' => $get['date'],
                'id_visitante' => $get['visitorID']
            ]
        ];
        $previousChats         = $chatModel->findPrevByID($chatArray);
        $previousChatsMessages = [];

        foreach ($previousChats as $chat) {
            $sentMessages     = $this->db->get_where('chat.sent_messages', ['id_chat' => $chat->id])
                                         ->result_array();
            $receivedMessages = $this->db->get_where('chat.received_messages', ['id_chat' => $chat->id])
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

            $prevMessages = array_merge($sentMessages, $receivedMessages);
            usort($prevMessages, static function ($message1, $message2) {
                return $message1['fecha_creacion'] <=> $message2['fecha_creacion'];
            });

            $previousChatsMessages[] = [
                'chatID'             => $chat->id,
                'fecha_inicio'       => $chat->fecha_inicio,
                'fecha_finalizacion' => $chat->fecha_finalizacion,
                'agente'             => $chat->operator_names,
                'messages'           => $prevMessages
            ];
        }

        return !empty($previousChatsMessages) ? $this->output
            ->set_content_type('application/json')
            ->set_output(
                json_encode($previousChatsMessages)
            ) : null;
    }

    /**
     * @param $id
     * @param null $documento
     * @param null $fromNumber
     * @return array
     */
    public function getMessages($id, $documento = null, $fromNumber = null): array
    {
        $this->load->model('receivewhatsapp', '', true);
        $this->load->model('sentwhatsapp', '', true);
        $this->load->model('chat', '', true);
        if ($documento === 'null') {
            $this->load->model('solicitudWS', '', true);
            /** @var SolicitudWS $requestModel */
            $requestModel    = $this->solicitudWS;
            $solicitudEntity = $requestModel->findRequestByTelefono($fromNumber);
            if ($solicitudEntity !== null && $solicitudEntity !== false) {
                /** @var Chat $chatModel */
                $updateData = [
                    'nombres'   => $solicitudEntity['nombres'],
                    'apellidos' => $solicitudEntity['apellidos'],
                    'documento' => $solicitudEntity['documento'],
                    'email'     => $solicitudEntity['email'],
                    'type'      => 'solicitante'
                ];
                $chatModel = $this->chat;
                $chatModel->doUpdate($id, $updateData);
            }
        }
        /*$sentMessages       = $this->db->select('sent_messages.*, operadores.nombre_apellido as nombre_operador')
                                       ->join(
                                           'gestion.operadores',
                                           'idoperador = chat.sent_messages.id_operador',
                                           'left'
                                       )
                                       ->order_by('id', 'DESC')
                                       ->limit(5)
                                       ->get_where('chat.sent_messages', ['id_chat' => $id])
                                       ->result_array();
        $receivedMessages   = $this->db->order_by('id', 'DESC')
                                        ->limit(5)
                                        ->get_where('chat.received_messages', ['id_chat' => $id])
                                        ->result_array();*/
        $sentMessagesID     = [];
        $receivedMessagesID = [];
        $this->chatdb = $this->load->database('chat', TRUE);
        $sql = "select * from ( SELECT rm.id_chat, rm.sms_message_sid, rm.id, rm.fecha_creacion, rm.sms_status, '-1' AS id_operador, true AS received, false AS sent, rm.body, rm.media_content_type0, rm.media_url0, NULL AS nombre_apellido_operador ";
        $sql .= "FROM received_messages rm WHERE rm.id_chat = '" . $id;
        $sql .= "' union all SELECT sm.id_chat, sm.sms_message_sid, sm.id, sm.fecha_creacion, sm.sms_status, sm.id_operador, false AS received, true AS sent, sm.body, sm.media_content_type0, sm.media_url0, op.nombre_apellido AS nombre_apellido_operador ";
        $sql .= "FROM sent_messages sm INNER JOIN gestion.operadores op ON sm.id_operador = op.idoperador WHERE sm.id_chat = '" . $id . "') chat ";
        $sql .= "ORDER BY chat.fecha_creacion DESC LIMIT 20";
        $query = $this->chatdb->query($sql);
        $messages = $query->result_array();
        $messages = array_reverse($messages);
        /*foreach ($sentMessages as $key => $message) {
            $message['sent']     = true;
            $message['received'] = false;
            $sentMessagesID[$message['sms_message_sid']] = $message;
        }
        foreach ($receivedMessages as $key => $message) {
            $message['sent']     = false;
            $message['received'] = true;
            $receivedMessagesID[$message['sms_message_sid']] = $message;
        }
        $messages = array_merge($sentMessagesID, $receivedMessagesID);
        usort($messages, static function ($message1, $message2) {
            return $message1['fecha_creacion'] <=> $message2['fecha_creacion'];
        });*/
        return [
            'messages' => $messages
        ];
    }

//    public function get_chats()
//    {
//        $get = $this->input->get();
//        $this->load->model('ChatCobranzas', '', true);
//        /** @var Chat $model */
//        $model = $this->ChatCobranzas;
//
//        return $this->output
//            ->set_content_type('application/json')
//            ->set_output(
//                json_encode(
//                    $model->findByID(
//                        $get['id'],
//                        [
//                            $model->relationVisitor(),
//                            $model->relationCustomer(),
//                            $model->relationReceiveWhatsApp(),
//                            $model->relationSentWhatsApp()
//                        ]
//                    )
//                )
//            );
//    }

    /**
     * @throws PusherException
     */
    public function get_message_status()
    {
        $twillioStatus = $this->input->post();
        $this->load->model('sentwhatsapp', '', true);
        /** @var Sentwhatsapp $sentWsModel */
        $sentWsModel = $this->sentwhatsapp;
        $sentWsModel->updateStatus($twillioStatus['SmsSid'], $twillioStatus['MessageStatus']);
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

    public function AsignacionOp($id_chat)
    {         
            $operador = $this->getOperator($this->Cobranzas, $id_chat);
            return $operador;
    }

    private function getOperator($arrayType, $id_chat)
    {
        $fecha_actual = date('Y-m-d');

        # buscar operadores disponibles para asignar
        $data = $this->Chatbot_model->SeleccionarOperadores($fecha_actual, $arrayType);

        # buscar de los operadores quien tiene menos chats asignados
        $minChat = $this->Chatbot_model->MinChat($data, $id_chat);
        return $minChat;
    }

    public function send_new_message()
    {
        /** @var array $msgData
         * [
         *  'chatID' => int,
         *  'message' => string
         * ]
         */
        $msgData = $this->input->post();
        $msgData['origen'] = "COBRANZAS";
        $msgData['metodo_notificacion'] = 1;
                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => URL_BACKEND.'api/ApiFilterWhatsApp/send_new_message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $msgData,
                
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                $consulta_word = json_decode($response);
                //    var_dump($response);die;
                //  var_dump($consulta_word->code);
                // die;

        if($consulta_word->code == 200 || $consulta_word->code == 404)
        {

                $fileUrl = null;
                $dotEnv  = Dotenv\Dotenv::create(FCPATH);
                $dotEnv->load();

                //If media, media upload Logic
                if (isset($_FILES['media'])) {
                    $config['upload_path']   = FCPATH . 'media/chat_medias';
                    $config['allowed_types'] = 'gif|jpg|png|pdf';

                    $this->load->library('upload', $config);
                    /** @var CI_Upload $uploadInfo */
                    $uploadInfo = $this->upload;
                    if (!$uploadInfo->do_upload('media')) {
                        return $this->output
                            ->set_content_type('application/json')
                            ->set_output(
                                json_encode(
                                    [
                                        'error'       => 500,
                                        'description' => 'No se ha podido subir el archivo, intente nuevamente.'
                                    ]
                                )
                            );
                    }

                    //URL Dymamic
                    $fileUrl = getenv('MEDIA_ORIGIN_URL') . 'media/chat_medias/' . $uploadInfo->data()['file_name'];
                }
                //End Upload Logic

                $this->load->model('ChatCobranzas', '', true);
                /** @var Chat $model */
                $model = $this->ChatCobranzas;
                $model->setProvider('Twilio_WS');
                $chat = $model->findByID($msgData['chatID']);

                //Caso error 3 reintentos
                $i = 0;
                while ($chat === null && $i < 3) {
                    $chat = $model->findByID($msgData['chatID']);
                    ++$i;
                    //Espera 150 milisegundos entre cada intento
                    sleep(0.15);
                }

                if ($i < 3) {
                    $whatsAppNumber = $chat['new_chats']['from'];
                    $sid            = getenv('TWILIO_SID');
                    $token          = getenv('TWILIO_AUTH');
                    $twilio         = new Client($sid, $token);
                    $sendData       = [
                        'from' => 'whatsapp:' . getenv('WS_NUMBER2'),
                        'body' => $msgData['message']
                    ];

                    if ($fileUrl !== null) {
                        $sendData['mediaUrl'] = [
                            $fileUrl
                        ];
                    }

                    if (strpos($whatsAppNumber, '+') === 0) {
                        $whatsAppNumber = 'whatsapp:' . $whatsAppNumber;
                    } else {
                        $whatsAppNumber = 'whatsapp:+57' . $whatsAppNumber;
                    }

                    try {
                        $message = $twilio->messages->create($whatsAppNumber, $sendData);
                        $message = (object)$message->toArray();

                        $this->load->model('sentwhatsapp', '', true);
                        /** @var Sentwhatsapp $modelSentWhatsApp */
                        $modelSentWhatsApp = $this->sentwhatsapp;
                        //Si no se ha envíado algún archivo
                        if ($fileUrl === null) {
                            $sentMessageEntity = $modelSentWhatsApp->modelCreateDb(
                                $message,
                                $chat['new_chats']['id'],
                                $msgData['operatorID'],
                                true,
                                true
                            );

                            $model->updateChatSentMessage($chat['new_chats']['id'], $message,
                                                        $sentMessageEntity['creationDate']);
                        } elseif (isset($uploadInfo)) {
                            //Si se ha envíado algún archivo
                            $sentMessageEntity = $modelSentWhatsApp->modelCreateDb(
                                $message,
                                $chat['new_chats']['id'],
                                $msgData['operatorID'],
                                true,
                                true
                            );
                            if (is_array($sentMessageEntity)) {
                                $modelSentWhatsApp->registerMedias(
                                    $sentMessageEntity['lastID'],
                                    $fileUrl
                                );
                            }

                            $model->updateChatSentMessage($chat['new_chats']['id'], $message,
                                                        $sentMessageEntity['creationDate']);
                        }


                        //consultamos el mensaje enviado
                        $sentMensaje = $model->getSentMessages(['id_chat' => $chat['new_chats']['id'], 'id_sent' => $sentMessageEntity['lastID']]);
                        if (!empty($sentMensaje)) {
                            //pusher
                            $pusher = new Pusher\Pusher(
                                getenv('PUSHER_KEY'),
                                getenv('PUSHER_SECRET'),
                                getenv('PUSHER_APP_ID'),
                                ['cluster' => getenv('PUSHER_CLUSTER')]
                            );
                            $res = $pusher->trigger(
                                'channel-chat-'.$chat['new_chats']['id'],
                                'sent-message-component',
                                [
                                    'body' => $sentMensaje[0]['body'],
                                    'received' => 0,
                                    'sent' => 1,
                                    'media_url0' => $sentMensaje[0]['media_url0'],
                                    'media_content_type0' => $sentMensaje[0]['media_content_type0'],
                                    'fecha_creacion' =>  $sentMensaje[0]['fecha_creacion'],
                                    'nombre_apellido_operador' => $sentMensaje[0]['nombre_apellido'],
                                    'id_chat' => $chat['new_chats']['id'],
                                    'sms_status' => $sentMensaje[0]['sms_status'],
                                    'sms_sid' => $sentMensaje[0]['sms_message_sid']
                                ]
                                );
                        }


                        return $this->output
                            ->set_content_type('application/json')
                            ->set_output(
                                json_encode(
                                    [
                                        'messages' => $this->getMessages($msgData['chatID']),
                                        'user'     => ''
                                    ]
                                )
                            );
                    } catch (TwilioException $e) {
                        return $this->output
                            ->set_content_type('application/json')
                            ->set_output(
                                json_encode(
                                    [
                                        'error'       => 500,
                                        'description' => 'Error al enviar el mensaje. Trata de nuevo.'
                                    ]
                                )
                            );
                    }
                }

                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(
                            [
                                'error'       => 404,
                                'description' => 'El chat solicitado no existe'
                            ]
                        )
                    );

        }else{
                
                return $this->output->set_content_type('application/json')
                ->set_output(
                    json_encode(
                        [
                            'error'       => 404,
                            'description' => 'El mensaje enviado no esta permitido'
                        ]
                    )
                );
            
        }
    }
}

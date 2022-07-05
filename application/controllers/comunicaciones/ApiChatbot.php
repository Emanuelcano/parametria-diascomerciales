<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'third_party/REST_Controller.php';
require APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class ApiChatbot extends REST_Controller {
    
    public function __construct() {
        parent::__construct();
        
       $this->load->model('Chatbot_model','',TRUE);

       # Tipo de Operador que realiza la Gestion
       $this->Comerciales = array(1); //15140334
       $this->SAC = array(4,5); //15140334
       $this->Cobranzas = array(5,6); //15185188
    }

    public function AsignacionOp_post()
    {
        /*
        * SE VERIFICA QUE POST ES ENVIADO
        */
        $id_chat = $this->input->post('IdChat');
        $id_operador = $this->input->post('IdOperador');
		$id_gestion = $this->input->post('IdGestion');

		if(!empty($id_gestion)){
			$this->Comerciales = array(5); //15140334
			$this->SAC = array(5); //15140334
			$this->Cobranzas = array(5); //15185188
		}

        if (ENVIRONMENT == 'production') {
            $receiverNumberGes = TWILIO_PROD_GESTION;
            $receiverNumberCob = TWILIO_PROD_COBRANZAS;
        }else{
            $receiverNumberGes = TWILIO_TEST_GESTION;
            $receiverNumberCob = TWILIO_TEST_COBRANZAS;
        }
        
        $response = array('success' => false, 'operador' => 70, 'message' => 'Verificar número asignado a la conversacion Twilio (To). Sin Operadores');

        if ($id_chat) {
            /*|1
            * SE VERIFICA SI EL OPERADOR_ASIGNADO EN LA SOLICITUD ESTA ACTIVO Y NO AUSENTE, SE LE ASIGNA EL MISMO AL CHAT
            */
            
            $numGestion = $this->Chatbot_model->NumGestion($id_chat);
            $validateCliente = $this->Chatbot_model->ValidateCliente($numGestion->documento);
            if ($numGestion->to == $receiverNumberGes && !empty($validateCliente)) {
                $stringComerciales = implode(",", $this->SAC);
            }else{
                $stringComerciales = implode(",", $this->Comerciales);
            }

            $activeOperador = $this->Chatbot_model->VeriffOperador($id_operador, $stringComerciales);
        
            if ($activeOperador) {
                $response = array('success' => true, 'operador' => $activeOperador, 'message' => 'Operador de Gestión Asignado Chat = Gestion');
            }else{                
                switch ($numGestion->to) {
                    case $receiverNumberGes:
                        if (!empty($validateCliente)) {
                            $operador = $this->getOperator($this->SAC, $id_chat);
                        }else{
                            $operador = $this->getOperator($this->Comerciales, $id_chat);
                        }
                        $response = array('success' => true, 'operador' => $operador, 'message' => 'Operador de Gestión Asignado');
                    break;
                    
                    case $receiverNumberCob:
                        $operador = $this->getOperator($this->Cobranzas, $id_chat);
                        $response = array('success' => true, 'operador' => $operador, 'message' => 'Operador de Cobranzas Asignado');
                    break;
                }
            }
        }

        // REST_Controller provide this method to send responses
        $this->response($response, self::HTTP_OK);
    }

    private function getOperator($arrayType, $id_chat)
    {
        $fecha_actual = date('Y-m-d');

        # buscar operadores disponibles para asignar
        $data = $this->Chatbot_model->SeleccionarOperadores($fecha_actual, $arrayType);
        if(empty($data) && $arrayType == $this->SAC){
            $data = $this->Chatbot_model->SeleccionarOperadores($fecha_actual, $this->Comerciales);
        }

        # buscar de los operadores quien tiene menos chats asignados
        $minChat = $this->Chatbot_model->MinChat($data, $id_chat);
        return $minChat;
    }
    /*
    * METODO PARA VENCER CHATS DE GESTION Y COBRABZAS MAYOR A 24 HORAS Y SER REASGINADOS AL CHATBOT 192
    */
    public function vencerChats_get()
    {
        $data = $this->Chatbot_model->vencerChats();
        
        if ($data > 0) {
            $this->response(["Total Chats Vencidos de Gestion y Cobranzas: " => $data], self::HTTP_OK);            
        }
        else{
            $this->response(["message" => "No Hay Chats para Vencer mayor a 24 Horas!!"], self::HTTP_OK); 
        }
    }

    /*
    * SE REASIGNAN LOS CHAT AUTOMATICOS 108 VENCIDOS AL CHATBOT 192 Y LOS ACTIVOS A LOS OPERADORES
    */
    public function asignarOp108Reales_get()
    {
        /*
        * SE OBTIENEN LOS CHAT ACTIVOS DEL OPERADOR 108 Y SE ASIGNAN A OPERADORES REALES
        */
        $dataGestion = $this->Chatbot_model->reasignarGestion108();

        $this->reasignar108($dataGestion);

        $this->response(["message" => "Ejecutado Satisfactoriamente!!!"], self::HTTP_OK); 
    }

    private function reasignar108($dataGestion)
    {
        $fecha_actual = date('Y-m-d');
        /*
        * BUSCAR OPERADORES ACTIVOS CON MENOS CHATS
        */
        # buscar operadores disponibles para asignar
        $data = $this->Chatbot_model->SeleccionarOperadores($fecha_actual, $this->Comerciales);

        # buscar de los operadores 
        $minChat108 = $this->Chatbot_model->MinChat108($data);
        $y = 0;

        if ($dataGestion != false) {
            foreach ($dataGestion as $key => $value) {
                $this->Chatbot_model->UpdateOperator($value['id'], $minChat108[$y]['id_operador']);
                $y++;
            }               
        }

    }


}

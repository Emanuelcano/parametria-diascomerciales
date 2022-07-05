<?php

class ChatUAC extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // if ($this->session->userdata('is_logged_in')) {
            // MODELS
            $this->load->model('chat');
            $this->load->model('Solicitud_m','solicitud_model',TRUE);
            // LIBRARIES
            $this->load->library('form_validation');
            $this->load->library('Twilio_library');
            // HELPERS
            // $this->load->helper('date');
        // } else {
        //     redirect(base_url('login'));
        // }  
        // header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        // header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    public function index() {
        $link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/', $link);
       

            $title['title'] = 'Chat UAC';
            $this->load->view('layouts/adminLTE', $title);

            //$canalChat = $this->chat->getCanalChat();
            //$operadores = [];//$this->chat->getOperadores();
            //$casos_dispacher = $this->chat->search_chats_asign_by_ope($this->session->userdata("idoperador"));
            $data = array(
                'canalChat'     => [],
                'operadores'    => [],
                'canalChat'     => [],
                'id_operador'   => $this->session->userdata("id_operador"),
                'data'          => ["canal"=>1]
            );

            $this->load->view('chatuac/chatuac_view', $data);
            return $this;
       
    }

    public function render_new_chat() {
        $canal = $this->input->get('canal');
        if ($this->session->userdata('is_logged_in')) {
            $id_operador = $this->session->userdata('idoperador');
        } else {
            $id_operador = $this->input->post('id_operador');
        }
        
        $chat = $this->chat->search_chats_asign_by_ope($id_operador);
        
        //validar que vengan chats
        if (!empty($chat)) {
            $dataArray = 
            [
                'id_operador' => $id_operador,
                'id_chat' => $chat[0]['id'], 
                'fecha_registro' => date('Y-m-d H:i:s'),
            ];
            
            $inset_temp = $this->chat->insert_temp_chat($dataArray);
            
            // var_dump($inset_temp);die;

                if($inset_temp){
                    $data = array(
                        'canalChat'    => $chat,
                        'canal'        => $canal,
                    );
                } else {
                    $data = array();
                }
        }else {
            $data = array();
        }
        
        

        //insert tabla track UAC

        echo  $this->load->view('chatuac/chatUacComponent', $data, true); 
        // $this->load->view('template/test', $data,"TRUE"); 
    }

    function borrar_chat()
    {
        $id_chat = $this->input->post('id_chat');
        $btn_finalizar_chat = $this->input->post('btn_finalizar_chat');
        if ($btn_finalizar_chat) { //finalizar chat
            // ACTUALIZO EL ESTADO DEL CHAT PRIORIDAD 0
            $update_prioridad = $this->chat->update_prioridad($id_chat);
        }

       
        $delete_chats_temp = $this->chat->delete_temp_chat($id_chat);
        echo $delete_chats_temp;
    }


    public function render_whasapp_chat() {
       $id_chat =  $this->input->post("id_chat");
        if ($this->session->userdata('is_logged_in')) {
            
            $id_operador = $this->session->userdata('idoperador');
        } else {
            $id_operador = $this->input->post('id_operador');
        }


        $data = array(
            'id_chat'  => $id_chat
        );

        $dataArray = 
        [
            'id_operador' => $id_operador,
            'id_chat' => $id_chat, 
            'fecha_hora' => date('Y-m-d H:i:s'),
        ];

        $inset_temp = $this->chat->insert_temp_chat($dataArray);
        // var_dump($inset_temp);die;
        // $this->load->view('layouts/adminLTE__header', $data);
        $this->load->view('whatsapp/whatsapp_component', $data); 
        // $this->load->view('layouts/adminLTE__footer', $data);
    }
    // al cerrar el chat se debe hacer el delete de la tabla track uac

    public function send_pagare_whatsapp()
    {
        $file = [];
        $end_folder = FCPATH.'public/';
        if(ENVIRONMENT == 'development')
        {
            $pdf = $end_folder.'/nNrUjlzVlFGZWJHVEgwV0hJVmJ1OUxFTWRtd0thSDYzRkxQM1RQWTVwbz0=.pdf';
            $urlpdf = "https://testbackend.solventa.co:10443/public/IMAGENES_SOLICITANTES/nNrUjlzVlFGZWJHVEgwV0hJVmJ1OUxFTWRtd0thSDYzRkxQM1RQWTVwbz0=.pdf";
        }else{
            $pdf = $this->input->post("path_doc");
            $urlpdf = URL_BACKEND.$pdf;
        }
        $this->_end_folder($end_folder);
        
        $telefono = $this->input->post("telefono");
        $canal = $this->input->post("canal");
        file_put_contents($pdf, file_get_contents($urlpdf));
        $file = curl_file_create($pdf, 'pdf', $pdf);

        $url = URL_BACKEND."comunicaciones/twilio/send_new_message";
        $idoperador = $this->session->userdata('idoperador');
        if($canal == "49")
        {
            $canal= "13289049";
        } else if($canal == "34"){
            $canal= "15140334";
        } else if($canal == "88"){
            $canal= "15185188";

        }

        $new_chat = $this->solicitud_model->find_lastchat($telefono,$canal,$idoperador);
        $params = array(
            'chatID'  => $new_chat->id,
            'operatorID' => $this->session->userdata('idoperador'),
        );
        if ( is_object($file)  ) {
            $params['media'] = $file;
        }else{
            $params['message'] = $file;
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $params,
        ));
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $results = curl_exec($curl);
        $response = str_replace("\xEF\xBB\xBF", '', $results);
        //Borrar en caso de ser archivo
        (isset($file->name) ? unlink($file->name):'');
        return $response;

    }
    private function _end_folder($end_folder)
    {
        // Valida que la carpeta de destino exista, si no existe la crea.
        if(!file_exists($end_folder) && !empty($end_folder))
        {
            // Si no puede crear el directorio.
            if(!mkdir($end_folder, 0777, true))
            {
                return FALSE;
            }
        }
        return $end_folder;
    }
}

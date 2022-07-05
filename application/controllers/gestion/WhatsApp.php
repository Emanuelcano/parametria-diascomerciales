<?php

defined('BASEPATH') or exit('No direct script access allowed');

class WhatsApp extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        //if ($this->session->userdata("is_logged_in")) {
            $this->load->model('chat', 'chat', TRUE);
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
            $this->load->model('Credito_model', 'credito_model', TRUE);
            $this->load->model('galery_model', '', TRUE);
            $this->load->helper('formato');

            
        /*} else {
            die('Usuario no logueado');
        }*/
    }
/***************************************************************************/
// VISTAS
/***************************************************************************/
    public function index($id_solicitud)
    {
        $data = [];
        $solicitud_desembolso = [];
        
        if (isset($id_solicitud)) 
        {
            // Datos propios de la solicitud
            $solicitud = $this->_get_solicitude($id_solicitud);
             
           //Creditos del cliente
           $data['credits'] = [];
           
            $recalculable = true;
            //SE VERIFICA SI EL CREDITO YA TUVO SU PRIMER MOVIMIENTO - PAGO DE FORMA QUE NO SE PUEDA RECALCULAR LAS CONDICIONES.
            if(!empty($data['credits'][0]['quotas'])){
                foreach($data['credits'][0]['quotas'] as $key => $quotas){
                    if(strtolower($quotas['estado']) == "pagado" ){
                        $recalculable = false;
                        break;
                    }
                }
            }
            $params = ['id'=>$solicitud[0]['id_credito'], 'id_cliente'=>$solicitud[0]['id_cliente']];
            //busca en la tabla CREDITO
            $credito = $this->credito_model->search($params);

            //SET RESULT DE BD CON EL RESULTADO DE LA VERIFICACION DE SI ES RECALCULABLE.
            $credito[0]['recalculable'] = $recalculable;
            $data['credito_general'] = $credito;

            
            //TODO
            $chats     = [];
            $chatModel = $this->chat;

            $chats = $chatModel->findAllByAnchor(
                $solicitud[0]['telefono'],
                'from',
                [
                    $chatModel->relationAgent(),
                    $chatModel->relationReceiveWhatsApp(),
                    $chatModel->relationSentWhatsApp()
                ]
            );
            if (!empty($chats)) {
                $chats = $chatModel->organizeMessages($chats);
            }

            $data['chats'] = $chats;

            $this->load->view('gestion/box_whatsapp',['chats' => $data['chats'], 'solicitude' => $solicitud[0], 'solicitud_desembolso' => $solicitud_desembolso]); 
        }
        
    }

    private function _get_solicitude($id_solicitude)
    {
        return $this->solicitud_model->getSolicitudes(['id' => $id_solicitude]);
    }

    //por fecha
    public function whatsapp($id_solicitud, $paginacion = 0)
    {
        $data = [];
        $solicitud_desembolso = [];
        $hoy = date('Y-m-d');

        $pagina = $paginacion;


        $cant_dias = 30; //cantidad de dias que se le restan a la fecha para buscar los chats
        $cantidad_mensajes = 20; //cantidad minima de mensajes a recuperar

        if (isset($id_solicitud) && $this->session->userdata('tipo_operador') !=ID_OPERADOR_EXTERNO) 
        {
            // Datos propios de la solicitud
            $solicitud = $this->_get_solicitude($id_solicitud);
             
           //Creditos del cliente
           $data['credits'] = [];
           
            $recalculable = true;
            //SE VERIFICA SI EL CREDITO YA TUVO SU PRIMER MOVIMIENTO - PAGO DE FORMA QUE NO SE PUEDA RECALCULAR LAS CONDICIONES.
            if(!empty($data['credits'][0]['quotas'])){
                foreach($data['credits'][0]['quotas'] as $key => $quotas){
                    if(strtolower($quotas['estado']) == "pagado" ){
                        $recalculable = false;
                        break;
                    }
                }
            }
            $params = ['id'=>$solicitud[0]['id_credito'], 'id_cliente'=>$solicitud[0]['id_cliente']];
            //busca en la tabla CREDITO
            $credito = $this->credito_model->search($params);

            //SET RESULT DE BD CON EL RESULTADO DE LA VERIFICACION DE SI ES RECALCULABLE.
            $credito[0]['recalculable'] = $recalculable;
            $data['credito_general'] = $credito;

            
            //TODO
            $chats     = [];
            $data['chats']     = [];
            $chatModel = $this->chat;

            
            $chats_aux = $chatModel->getChat($solicitud[0]['telefono']);
            $chats_aux = array_reverse($chats_aux);

            foreach ($chats_aux as $key => $value) {
                $chats['new_chats'] = $value;
                $chats["received_messages"]= [];
                $chats["sent_messages"] = [];
                $diff_days = 0;
            

                if(!empty($chats['new_chats'])){
                    //do {    
                            $pagina += 1;

                            $fecha_tope = $value["fecha_inicio"]; //fecha tope para la busquieda de mensajes
                            
                            $inicio = strtotime ( '-'.$pagina * $cant_dias.' day' , strtotime ( $hoy ) ) ;//fecha menor
                            $inicio = date( 'Y-m-d' , $inicio );

                            $fin = strtotime ( '-'.(($pagina-1) * $cant_dias) .' day' , strtotime ( $hoy ) ) ;//fecha mayor
                            $fin = date( 'Y-m-d' , $fin );

                            $diff_days = date_diff_days($fecha_tope, $inicio);
                            
                            $chats["received_messages"] = array_merge($chats["received_messages"] , $chatModel->getReceivedMessages(['id_chat' => $value['id']/* , 'fecha_inicio' => $inicio, 'fecha_fin' => $fin*/]));
                            $chats["sent_messages"] = array_merge($chats["sent_messages"] , $chatModel->getSentMessages(['id_chat' => $value['id'] /*, 'fecha_inicio' => $inicio, 'fecha_fin' => $fin*/]));
                            
                    // mientras no he terminado los mensajes de chat y la cantidad de mensajes es < cantidad_mensajes            
                    //} while ($diff_days >= 0 && (count($chats["received_messages"]) + count($chats["sent_messages"])) < $cantidad_mensajes);

                }

                $chats = $chatModel->organizeMessagess($chats);
                array_push($data['chats'],$chats);

                    
                //if(isset($data['chats'][0]['messages']) && count($data['chats'][0]['messages']) >= $cantidad_mensajes){
                //    break;
                //}
                        
            }
    
            /*
                        var_dump('<pre>');
                        var_dump($data['chats'] );
                        var_dump($pagina );
                        var_dump('</pre><hr />');die;
                    
            */
            $this->load->view('gestion/box_whatsapp',['chats' => $data['chats'], 'solicitude' => $solicitud[0], 'solicitud_desembolso' => $solicitud_desembolso, 'paginacion' => $paginacion ]); 
        }
        
    }


    //por limite de mensaje
    public function whatsapp_paginado($documento, $pagina = 0,$canal)
    {
        // Datos propios de la solicitud
        //$data['solicitude'] = $this->_get_solicitude($id_solicitud,$pagina =0);
        //$solicitud = $data['solicitude'];
        $aux = $this->solicitud_model->get_numeros_chat($documento);
        $solicitud = $this->solicitud_model->getSolicitudesBy(['documento' => $documento ])[0];
        

        $ventas = [];
        $cobranza = [];
        $data = [];

        if(!empty($aux)){
            foreach ($aux as $key => $value) {
                $value["contacto"] = explode(' ', $value["nombres"])[0].' '.explode(' ', $value["apellidos"])[0];
                $value["Nombre_Parentesco"] = (!is_null($value["Nombre_Parentesco"]))? substr($value["Nombre_Parentesco"], 0, 3):null ;
                
                if($value["to"] == TWILIO_TEST_GESTION || $value["to"] == TWILIO_TEST_COBRANZAS) {
                    // array_push($cobranza, $value);
                    // array_push($ventas, $value);
                    if($value["to"] == TWILIO_TEST_GESTION)
                        array_push($ventas, $value);

                    if($value["to"] == TWILIO_TEST_COBRANZAS)
                        array_push($cobranza, $value);
                    
                } else{
                    if($value["to"] == TWILIO_PROD_GESTION)
                        array_push($ventas, $value);

                    if($value["to"] == TWILIO_PROD_COBRANZAS)
                        array_push($cobranza, $value);
                }
                
            }
        }
        $data['chats_334'] = $ventas;
        $data['chats_188'] = $cobranza;
        // print_r($data);die;
        $data['permisos']['operador']  = $this->solicitud_model->get_permisos_servicios([
            'idoperador' => $this->session->userdata('idoperador'),
            'action' => 'biometriaws'
        ]);
        $whatsapp_scans  = $this->galery_model->get_whatsapp_scans(['id_solicitud' => $solicitud->id]); 

        if (isset($whatsapp_scans)) {
            if ($whatsapp_scans->status == 'activo') 
                $data['permisos']['ws_scan'] = 1;
            else
                $data['permisos']['ws_scan'] = 0;
        } else {
            $data['permisos']['ws_scan'] = 0;
        }

        // if (isset($whatsapp_scans)) {
        //     if ($whatsapp_scans->front == 1 && $whatsapp_scans->back == 1 && $whatsapp_scans->video == 1 )
        //         $data['permisos']['ws_scan'] = 1;
        //     else
        //         $data['permisos']['ws_scan'] = 0;
        // } else {
        //     $data['permisos']['ws_scan'] = 0;
        // }
        
        $this->load->view('gestion/box_whatsapp',['datos'=>$data]);    
    }

    public function cargarChatComponent($id_chat) {
       
         
        $datos = $this->chat->getChatwithSolicitud($id_chat);
        $data = array(
            'id_chat'  => $id_chat,
            'id_solicitud'  => (!empty($datos[0]['id_solicitud']))?$datos[0]['id_solicitud']:0,
            'documento'  => (!empty($datos[0]['documento']))?$datos[0]['documento']:0
        );

        $this->load->view('whatsapp/whatsapp_component', $data); 
    }
}
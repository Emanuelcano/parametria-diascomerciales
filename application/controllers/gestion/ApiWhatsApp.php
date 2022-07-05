<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiWhatsApp extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
        // Models
        $this->load->model('chat', 'chat', TRUE);
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
            $this->load->model('Credito_model', 'credito_model', TRUE);
            $this->load->model('galery_model', '', TRUE); //CI_model
            $this->load->model('Jumio_model', 'jumio_model', TRUE); //CI_model
		
	}
       
    public function get_mensajes_paginados_post()
	{
        $cantidad_mensajes = 20; //cantidad minima de mensajes a recuperar
        //control de paginacion
        $paginacion['pagina'] = $this->post('pagina');
        //334 originacion
        //188 cobranza
        $paginacion['chat'] = $this->post('chat');
        
        if($paginacion['pagina'] > -1){
            
            $chats     = [];
            $data['chats']     = [];
            $chatModel = $this->chat;
            
            $chats['new_chats'] = $chatModel->getChat(['id_chat' => $paginacion['chat']]);
            
            if(!empty($chats['new_chats'])){
                $chats['new_chats'] = $chats['new_chats'][0];
                $inicio = $paginacion['pagina'] * $cantidad_mensajes; //a partir de aqui se empezaran a consultar los registros de chat
                $chats["messages"] = $chatModel->get_all_messages(['id_chat' => $chats['new_chats']['id'] , 'inicio' => $inicio, 'fin' => $cantidad_mensajes]);
                $chats["messages"] = array_reverse($chats["messages"]);
            }
            
            array_push($data['chats'],$chats);
            $data['chats'] = array_reverse($data['chats']);
            
            if(isset($chats["messages"]) && count($chats["messages"]) >= $cantidad_mensajes) {
                $paginacion['pagina'] = intval($paginacion['pagina'])+1;
            } else {
                $paginacion['pagina'] = -1;
            }
            
            if(!empty($chats['new_chats']))
            {
                $response['status']['code'] = parent::HTTP_OK;
                $response['status']['ok'] = TRUE;
                $response['chat'] = $data['chats'];
                $response['paginacion'] = $paginacion['pagina'];
            }else{
                $response['status']['code'] = parent::HTTP_OK;
                $response['status']['ok'] = FALSE;
            }

        } else {
            $response['status']['code'] = parent::HTTP_OK;
			$response['status']['ok'] = FALSE;
        }
		
		$this->response($response, parent::HTTP_OK);
    }   
    
    public function mensaje_maker_get($template, $solID){
        
        $this->load->helper('formato');

        $response = mensaje_whatapp_maker($template, $solID);
        $this->response($response, parent::HTTP_OK);
    }
    /*******************************************************/
    /*** Obtiene los operadores de una canal seleccinado ***/
    /*******************************************************/
    public function getOperadoresCanal_post() {

        if ($this->input->is_ajax_request()) {
            $canal = $this->input->post('canal');

            $operadores = $this->chat->getOperadoresCanal($canal);

            $status = parent::HTTP_OK;
            $response['status']['ok']   = TRUE;
            $response['status']['code'] = $status;
            $response['operadores']     = $operadores;

            return $this->response($response, $status);
        } else {
            show_404();
        }
    }
    /*******************************************************/
    /*** Obtiene los clientes de un operador seleccinado ***/
    /*******************************************************/
    public function getOperadorCliente_post() {

        if ($this->input->is_ajax_request()) {
            $canal       = $this->input->post('canal');
            $id_operador = $this->input->post('id_operador');
            $filtro      = $this->input->post('filtro');
            $status      = $this->input->post('status');
          
            if(empty($filtro)){
                $filtro ='activo';
            }

            $chatOperador = $this->chat->getOperadorCliente($canal, $id_operador, $filtro, $status);
            $count_chats_by_status = $this->chat->getOperadoresCanal($canal,$id_operador);

            $status = parent::HTTP_OK;
            $response['status']['ok']   = TRUE;
            $response['status']['code'] = $status;
            $response['chatOperador']   = $chatOperador;
            $response['count_chats_by_status']   = $count_chats_by_status;

            return $this->response($response, $status);
        } else {
            show_404();
        }
    }
    
    /****************************************************************/
    /*** Obtiene los mensajes de un cliente seleccinado paginados ***/
    /****************************************************************/
    public function getOperadorChat_post() {
        if ($this->input->is_ajax_request()) {
            $cantidad_mensajes = 20; //cantidad minima de mensajes a recuperar
            $id_chat = $this->input->post('id_chat');
            $pagina  = $this->input->post('pagina');
            $documento  = $this->input->post('documento');
            $solicitud  = $this->input->post('solicitud');
           
            if($pagina > -1) {
                $inicio = $pagina * $cantidad_mensajes;
                $fin = $cantidad_mensajes;
                $chatCliente = $this->chat->getOperadorChat($id_chat, $inicio, $fin);
                $chatCliente = array_reverse($chatCliente);
                $pagares = $this->get_images_documentos($solicitud,$documento);
                $totalRecibidosEnviados = $this->chat->getTotalesRecEnv($id_chat);
                $totalRecibidosEnviados = array_reverse($totalRecibidosEnviados);

                if(isset($chatCliente) && count($chatCliente) >= $cantidad_mensajes) {
                    $pagina++;
                } else {
                    $pagina = -1;
                }

                $totales = $this->chat->getTotalesRecEnv($id_chat);

                $status = parent::HTTP_OK;
                $response['status']['ok']   = TRUE;
                $response['status']['code'] = $status;
                $response['chatCliente']    = $chatCliente ? $chatCliente : $chatCliente['chatCliente'] = []  ;
                $response['pagares']    = $pagares ? $pagares :  []  ;
                $response['totalRecibidosEnviados']    = $totalRecibidosEnviados ? $totalRecibidosEnviados : $response['totalRecibidosEnviados'] = []  ;
                $response['id_chat']         = $id_chat;
                $response['pagina']         = $pagina;
                $response['totales']        = $totales;
            } else {
                $response['status']['code'] = parent::HTTP_OK;
                $response['status']['ok']   = FALSE;    
            }
            return $this->response($response, parent::HTTP_OK);
        } else {
            show_404();
        }
    }
    /************************************************/
    /*** Se busca por documento, teléfono o texto ***/
    /************************************************/
    public function getTelefonoTextoDocumento_post() {
        if ($this->input->is_ajax_request()) {
            $filtro    = $this->input->post('filtro');
            $txtBuscar = $this->input->post('txtBuscar');
            $operador = $this->input->post('operador');

            $chatOperador = $this->chat->getTelefonoTextoDocumento($filtro, $txtBuscar, $operador);

            $status = parent::HTTP_OK;
            $response['status']['ok']   = TRUE;
            $response['status']['code'] = $status;
            $response['chatOperador']   = $chatOperador;

            return $this->response($response, $status);
        } else {
            show_404();
        }
    }


    private function get_images_documentos($id_solicitude,$documento)
    {
        $data = $this->_get_imagenes($id_solicitude, $documento)['images'];
        $pagares = [];
        return $data['data'];
    }

    private function _get_imagenes($id_solicitude, $documento)
    {
        $data['images']['options'] = $this->galery_model->search_required(['origen' => 'BACK', 'estado' => 1]);
        $data['images']['data']    = $this->galery_model->search_imagenes(['id_solicitud' => $id_solicitude, 'documento' => $documento]);
        // Obtengo informacion si las imagenes fueron analizadas por Jumio.
        $data['images']['origin']  = array_merge($this->_get_jumio($id_solicitude), $this->_get_eid($id_solicitude));

        //var_dump($data['images']);die;
        return $data;
    }

    private function _get_jumio($id_solicitude)
    {
        $aux = [];
        $params['id_solicitud'] = $id_solicitude;
        $params['order'] = [['id', 'desc']];
        
        $response = $this->jumio_model->search($params);
        foreach ($response as $key => $value) 
        {
            $aux[] = $value;
            foreach ($value as $index => $res) 
            {
                if(isset($this->_status_jumio[$index]))
                {
                    $aux[$key][$index] = $this->_status_jumio[$index][$res];
                }
            }
             
        }

        return $aux;
    }

    private function _get_eid($id_solicitude)
    {
        $aux = [];
        $params['id_solicitud'] = $id_solicitude;
        $params['order'] = [['id', 'desc']];
        
        $response = $this->jumio_model->search_eid($params);
        
        foreach ($response as $key => $value) 
        {
            $aux[] = $value;
            
            foreach ($value as $index => $res) 
            {   
                if(isset($this->_status_jumio[$index]))
                {
                    if(is_null($res)) { 
                        $res = "0";
                    } else { 
                        $res = "1";
                    }
                    $aux[$key][$index] = $this->_status_eid[$index][$res];
                }
            }
        }
        //var_dump($aux);die;
        return $aux;
    }
    private function _get_solicitude($id_solicitude)
    {
        return $this->Solicitud_m->getSolicitudes(['id' => $id_solicitude]);
    }


    
}
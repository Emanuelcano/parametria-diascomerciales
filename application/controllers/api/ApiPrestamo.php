<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';

use payvalida\payvalidaRegisterRequest;
use payvalida\payvalidaUpdateRequest;
use Restserver\Libraries\REST_Controller;

require_once APPPATH . 'libraries/payvalida/payvalidaRegisterRequest.php';
require_once APPPATH . 'libraries/payvalida/payvalidaUpdateRequest.php';

class ApiPrestamo extends REST_Controller
{
    protected $_solicitud;
    CONST template_83 = 59;
    public function __construct()
    {
        
        parent::__construct();
        $this->load->library('User_library');
        $auth = $this->user_library->check_token();

        if($auth->status == parent::HTTP_OK)
        {
            // MODELS
            $this->load->model('prestamos/Prestamo_model', 'prestamo', TRUE);
            $this->load->model('Solicitud_m','solicitud',TRUE);
            $this->load->model('Credito_model','credito',TRUE);
            $this->load->model('cliente_model','cliente',TRUE);
            $this->load->model('BankEntidades_model','bankEntidades',TRUE);
            // LIBRARIES
            $this->load->library('form_validation');
	
			$this->load->helper('formato_helper');
        }else{
            $this->session->sess_destroy();
            $this->response(['redirect'=>base_url('login')],$auth->status);
        }
    }
    
    public function tablePrestamosPagar_post(){
        $data = $this->solicitud->findSolicitudes();
        foreach($data as $prestamo){

            $prestamo->nombre_comp = substr($prestamo->nombre_comp, 40); // rafael le tenia substrpara guardar el nombre de archivo, pero no se muestra la imagen en comprobantes
            $prestamo->nombres = $prestamo->nombres . ' ' . $prestamo->apellidos;
        }
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);
    }

    public function PagarPrestamo_post()
    {
        $response = array_base();
        if($this->_validate_save_input())
        {
            $id = $this->input->post('id');
            $solicitud = $this->solicitud->getSolicitudesBy(['id_solicitud'=>$id]);
            $this->_solicitud = $solicitud[0]; //Set propierty $_solcitud, para usarlo en la validacion de inputs. 
            //se valida que esta solicitud no contenga ningun credito asociado.
            if(!empty($solicitud)){
                //Valida si la solicitud tiene los campos completos para realizar el pago.
                if($this->_validate_solicitud_input()){
                    //Verifica si la solicitud esta asociada a un credito.
                    if($this->_solicitud->id_credito == null || $this->_solicitud->id_credito == 0 ){

                        $pagarPrestamo = $this->prestamo->PagarPrestamo($this->_solicitud);

                        if($pagarPrestamo['respuesta']){
                            if (!is_null($this->_solicitud->utm_source) && $this->_solicitud->utm_source == "crezu") {
                                $end_point = URL_MEDIOS_PAGOS."ApiSeoCrezu";
                                $params = [
                                    "id_solicitud" => $id, 
                                    "click_id" => $this->_solicitud->tracking_id,
                                    "goal" => 'CPS'
                                ];
                                $this->curl($end_point, 'POST', $params);
                            }

                            $response['success'] = true;
                            $response['title_response'] =  "Conéxión establecida.";
                            $response['text_response'] = "";
                            $response['response']['mensaje'] = "El pago se realizo correctamente." ;
                            $response['response']['respuesta'] = true;
                            if( $this->_solicitud->tipo_solicitud === "PRIMARIA" ){

                                $chat = $this->prestamo->checkStatusChat($this->_solicitud->documento);
                                if(!empty($chat)){

                                    if( $chat->status_chat == "activo"){

                                        $message  = "Hola {{nombreCompleto}} Queremos contarte que realizamos el desembolso de tu crédito. El tiempo que pueda tardar tu entidad financiera en reflejarte el dinero en tu cuenta bancaria, dependerá de cuanto demore en gestionar y aceptar la transferencia. Muchas gracias por confiar en Solventa.";

                                        $nombreCompleto = $this->_solicitud->nombres." ".$this->_solicitud->apellidos;
                                        $configMessage = [
                                            'procedimiento'=>'confirmacionDesembolso',
                                            'replace' => ['nombreCompleto'=>$nombreCompleto],
                                            'via' => 'whatsapp',
                                            'message' => $message,
                                            'template' => false
                                        ];

                                    }else{

                                        $configMessage = [
                                            'procedimiento'=>'confirmacionDesembolso',
                                            'template' => self::template_83, 
                                            'id_solicitud'=>$this->_solicitud->id, 
                                            'via' => "whatsapp"
                                        ];

                                    }

                                    $message = $this->getMessage($configMessage);

                                    $dataWhatsapp = [
                                        'message' => $message,
                                        'operatorID' => 108,
                                        'chatID' => $chat->id
                                    ];
                                    if(ENVIRONMENT === "production"){
                                        $pagarPrestamo['send_whatsapp'] = $this->send_whatsapp($dataWhatsapp);
                                    }

                                }else{
                                    $pagarPrestamo['send_whatsapp'] = "No enviado.";
                                }

                                $response['data']['send_whatsapp'] = (isset($pagarPrestamo['send_whatsapp'])) ? $pagarPrestamo['send_whatsapp'] : "";

                            }else{

                                $response['data']['sendmail'] = (isset($pagarPrestamo['sendmail'])) ?  $pagarPrestamo['sendmail'] : "";

                            }

                                $response['data']['trackGestion'] = (isset($pagarPrestamo['trackGestion'])) ? $pagarPrestamo['trackGestion'] : "";

                        }else{

                            $response['title_response'] =  "Conéxión establecida.";
                            $response['text_response'] = "";
                            $response['response']['mensaje'] = "No se pudo realizar el pago: " . $pagarPrestamo;
                            $response['response']['respuesta'] = false;
                            $response['response']['error'] = ["No se pudo realizar el pago: " . $pagarPrestamo];
                        }

                    }else{

                        $response['title_response'] =  "Conéxión establecida.";
                        $response['text_response'] = "";
                        $response['response']['mensaje'] = "Ya existe un credito pagado para esta solicitud." ;
                        $response['response']['respuesta'] = false;
                        $response['response']['error'] = ["Ya existe un credito pagado para esta solicitud."];
                    }
                }else{
                    
                    $response['response']['mensaje'] = "No se pudo realizar el pago." ;
                    $response['response']['respuesta'] = false;
                    $validationErr = $this->form_validation->error_array();
                    $response['response']['error'] = $validationErr;
                }
            }else{
                $response['response']['respuesta'] = false;
                $response['response']['mensaje'] = "La solicitud no existe." ;
                $response['response']['error'] = ["La solicitud no existe."];
            }
        }else{

            $response['title_response'] =  "Conéxión establecida.";
            $response['text_response'] = "";
            $response['response']['mensaje'] = "No se pudo realizar el pago." ;
            $response['response']['respuesta'] = false;
            $validationErr = $this->form_validation->error_array();
            $response['response']['error'] = $validationErr;

        }

        $this->response($response);
    }
    
    public function Procesar_post()
    {
        if($this->_validate_save_input())
        {
            $id     = $this->input->post('id');
            $procesado = $this->prestamo->Procesando($id);
            $response = array_base();
            if(!$procesado['error']){
                $status = parent::HTTP_OK;
                $message = "Registro actualizado correctamente.";
                $response['response']['respuesta'] = true;
                $response['response']['status'] = $status;
                $response['response']['error'] = $procesado['error'];
                $response['response']['mensaje'] = $message;
            }else{
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $message = 'No se pudo actualizar el registro.';

                $response['response']['respuesta'] = false;
                $response['response']['status'] = $status;
                $response['response']['error'] = $procesado['error'];
                $response['response']['mensaje'] = $message;
            }

        }else{
            $status = parent::HTTP_BAD_REQUEST;
            $validationErr = $this->form_validation->error_array();
            $response['status'] = $status;
            $response['error'] = $validationErr;
        }
        $this->response($response);
        
    }
    
    public function Rechazar_post()
    {
        if($this->_validate_save_input())
        {
            $id     = $this->input->post('id');
            $rechazar = $this->prestamo->Rechazar($id);
            $response = array_base();
            if(!$rechazar['error']){
                $message = "El prestamo se rechazo correctamente.";
                $status = parent::HTTP_OK;
                $response['response']['respuesta'] = true;
                $response['response']['status'] = $status;
                $response['response']['error'] = $rechazar['error'];
                $response['response']['mensaje'] = $message;
            }else{
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $message = 'No se pudo rechazar el prestamo.';
                $response['response']['respuesta'] = false;
                $response['response']['status'] = $status;
                $response['response']['error'] = $rechazar['error'];
                $response['response']['mensaje'] = $message;
            }

        }else{
            $status = parent::HTTP_BAD_REQUEST;
            $validationErr = $this->form_validation->error_array();
            $response['status'] = $status;
            $response['error'] = $validationErr;
        }

        $this->response($response);
    }
    
    public function PosponerPago_post()
    {
        if($this->_validate_save_input())
        {
            $id     = $this->input->post('id');
            $posponer = $this->prestamo->PosponerPago($id);
            $response = array_base();
            if(!$posponer['error']){
                $message = "Transferencia rechazada correctamente.";
                $status = parent::HTTP_OK;
                $response['response']['respuesta'] = true;
                $response['response']['status'] = $status;
                $response['response']['error'] = $posponer['error'];
                $response['response']['mensaje'] = $message;

                //track
                $dataTrackGestion = [
                    'id_solicitud'=>(int)$id,
                    'observaciones'=>'[TRANSFERENCIA RECHAZADA BBVA]<br>Operador: '.$this->session->userdata("idoperador"), 
                    'id_tipo_gestion' => 9,
                    'id_operador' => $this->session->userdata("idoperador")
                ];
                $endPoint =  base_url('api/track_gestion');
                $this->curl($endPoint, 'POST', $dataTrackGestion);
            

            }else{
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $message = 'No se pudo rechazar la transferencia.';
                $response['response']['respuesta'] = false;
                $response['response']['status'] = $status;
                $response['response']['error'] = $posponer['error'];
                $response['response']['mensaje'] = $message;
            }

        }else{
            $status = parent::HTTP_BAD_REQUEST;
            $validationErr = $this->form_validation->error_array();
            $response['status'] = $status;
            $response['error'] = $validationErr;
        }
        $this->response($response);
        
    }
    
    public function _validate_save_input()
    {
        $this->form_validation->set_rules('id', 'id_solicitud', 'required');

        $this->form_validation->set_message('required', 'El campo %s es obligatorio');

        if($this->form_validation->run())
        {
            return TRUE;
        }else
        {
            return FALSE;
        }
    }

    public function _validate_solicitud_input(){

        $config =  array(
            [
                'field' => 'requieresSolicitud',
                'rules' => [
                    'callback_requiresSolicitud'
                ]
            ]
        );
        
        $this->form_validation->set_rules($config);
        
        if($this->form_validation->run())
        {
            return TRUE;
        }else
        {
            return FALSE;
        }
    }

    //CALLBACK DE FORM VALIDATION, VERIFICA SI LA SOLICITUD CONTIENE LOS CAMPOS REQUERIDOS PARA CREAR EL CLIENTE.
    public function requiresSolicitud(){

        $sol = $this->_solicitud;
        if($sol->documento == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo documento es obligatorio" );
            return false;
        }elseif($sol->id_tipo_documento == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo id_tipo_documento es obligatorio");
            return false;
        }/*elseif($sol->fecha_expedicion == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo fecha_expedicion es obligatorio");
            return false;
        }*/elseif($sol->id_departamento == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo id_departamento es obligatorio");
            return false;
        }elseif($sol->id_localidad == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo id_localidad es obligatorio");
            return false;
        }elseif($sol->nombres == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo nombres es obligatorio");
            return false;
        }elseif($sol->apellidos == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo apellidos es obligatorio");
            return false;
        }elseif($sol->id_usuario == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo id_usuario es obligatorio");
            return false;
        }elseif($sol->fecha_alta == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo fecha_alta es obligatorio");
            return false;
        }elseif($sol->telefono == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo telefono es obligatorio");
            return false;
        }elseif($sol->email == null){
            $this->form_validation->set_message("requiresSolicitud", "El Campo email es obligatorio");
        }else{
            return true;
        }

    }
    //Metodo temporal para migrar las referencias de solicitud
    public function migrar_referencias_post(){

        $this->db->select('*');
        $this->db->from('maestro.clientes');
        //$this->db->limit(20);
        $clientes = $this->db->get();

        foreach ($clientes->result() as $key => $clientes) {

            $this->db->where(['id_cliente' => $clientes->id]);
            $this->db->select('*');
            $this->db->from('maestro.referencias');
            $refe = $this->db->get();

            foreach($refe->result() as $key => $referencia){

                $id_tipo_documento=$referencia->id_tipo_documento;
                $documento=$referencia->documento;
                $nombres_apellidos=$referencia->nombres_apellidos;
                $telefono=$referencia->telefono;
                $id_parentesco=$referencia->id_parentesco;
                $email=$referencia->email;
                

                /**
                * crear agenda telefonica por cada referencia
                */
                $this->db->select('id');
                $existe_num_ref = $this->db->get_where('maestro.agenda_telefonica',['id_cliente' => $clientes->id, 'numero' => $telefono]);

                if(empty($existe_num_ref->result())){
                    $dataAgendaTelefonica = array(
                        'id_cliente' => $clientes->id,
                        'numero' => $telefono,
                        'tipo' => 1,
                        'fuente' => 3,
                        'contacto'=>$nombres_apellidos,
                        'estado' => 1
                    );
                    $dataAgendaTelefonica['id_parentesco'] = ($id_parentesco !== null ) ? $id_parentesco : "";
                    $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);

                    checkDbError($this->db);
                }


            }

            print_r ( "Termino: ".$clientes->id." ". $clientes->nombres."</br>");
        }
    }


    //Metodo temporal para migrar las referencias de solicitud
    public function migrar_telefonos_datacredito_post(){
        $this->db->select('*');
        $this->db->from('maestro.clientes');
        //$this->db->limit(5);
        $clientes = $this->db->get();
        foreach ($clientes->result() as $key => $clientes) {

            //busco la consulta en DATACREDITO
            $this->db->where('identificacion = "'.$clientes->documento.'"');
            $this->db->select('*');
            $this->db->from('api_buros.datacredito2_reconocer_naturalnacional');
            $consulta = $this->db->get();
            if(!empty($consulta->result())){

                foreach ($consulta->result() as $key => $consulta) {
                    # code...
                    $id_consulta=$consulta->id;

                    //BUSCO EN DATACREDITO CELULARES
                    $this->db->where(['IdConsulta' => $id_consulta]);
                    $this->db->select('*');
                    $this->db->from('api_buros.datacredito2_reconocer_celular');
                    $consulta_celular = $this->db->get();

                    if(!empty($consulta_celular->result())){

                        foreach ($consulta_celular->result() as $key => $consulta_celular) {

                            $this->db->where(['ciudad_tel' => $consulta->ciudad]);
                            $this->db->select('areaCode, ciudad_tel, departamento_tel');
                            $this->db->from('parametria.tel_codigoarea');
                            $this->db->limit(1);
                            $query = $this->db->get();
                            $codigo_area = $query->row();

                            $areaCode = "";
                            $ciudad_tel = "";
                            $departamento_tel = "";

                            if(!empty($codigo_area)){
                                $areaCode = (string)$codigo_area->areaCode;
                                $ciudad_tel = $codigo_area->ciudad_tel;
                                $departamento_tel = $codigo_area->departamento_tel;
                            }

                            # code...

                            $nombres_apellidos='';
                            $telefono=$consulta_celular->celular;
                            $id_parentesco="";

                            /**
                            * crear agenda telefonica por cada referencia
                            */
                            $this->db->select('id');
                            $existe_num_ref = $this->db->get_where('maestro.agenda_telefonica',['id_cliente' => $clientes->id, 'numero' => $telefono]);
                            if(empty($existe_num_ref->result())){
                                $dataAgendaTelefonica = array(
                                    'id_cliente' => $clientes->id,
                                    'indicativo_ciudad' => $areaCode,
                                    'numero' => $telefono,
                                    'tipo' => 1,
                                    'fuente' => 4,
                                    'contacto'=>$nombres_apellidos,
                                    'estado' => 1,
                                    'ciudad' => $ciudad_tel,
                                    'departamento' => $departamento_tel
                                );
                                $dataAgendaTelefonica['id_parentesco'] = ($id_parentesco !== null ) ? $id_parentesco : "";
                                $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);
                                checkDbError($this->db);
                            }

                        }

                    }

                    //BUSCO EN DATACREDITO CELULARES
                    $this->db->where(['IdConsulta' => $id_consulta]);
                    $this->db->select('*');
                    $this->db->from('api_buros.datacredito2_reconocer_telefono');
                    $consulta_telefono = $this->db->get();

                    if(!empty($consulta_telefono->result())){

                        foreach ($consulta_telefono->result() as $key => $consulta_telefono) {
                            # code...
                            if($consulta_telefono->nombreCiudad === "BOGOTA, D.C."){
                                $consulta_telefono->nombreCiudad = substr($consulta_telefono->nombreCiudad, 0,6);
                            }
                            $this->db->where(['ciudad_tel' => $consulta_telefono->nombreCiudad]);
                            $this->db->select('areaCode, ciudad_tel, departamento_tel');
                            $this->db->from('parametria.tel_codigoarea');
                            $this->db->limit(1);
                            $query = $this->db->get();
                            $codigo_area = $query->row();

                            $areaCode = "";
                            $ciudad_tel = "";
                            $departamento_tel = "";

                            if(!empty($codigo_area)){
                                $areaCode = (string)$codigo_area->areaCode;
                                $ciudad_tel = $codigo_area->ciudad_tel;
                                $departamento_tel = $codigo_area->departamento_tel;
                            }

                            $nombres_apellidos='';
                            $telefono=$consulta_telefono->telefono;

                            $tipo = 2;
                            $fuente = 5;
                            if($consulta_telefono->tipo != 'R'){
                                $fuente = 6;
                            }

                            $this->db->select('id');
                            $existe_num_ref = $this->db->get_where('maestro.agenda_telefonica',['id_cliente' => $clientes->id, 'numero' => $telefono]);
                            if(empty($existe_num_ref->result())){
                                $dataAgendaTelefonica = array(
                                    'id_cliente' => $clientes->id,
                                    'indicativo_ciudad' => $areaCode,
                                    'numero' => $telefono,
                                    'tipo' => $tipo,
                                    'fuente' => $fuente,
                                    'contacto'=>$nombres_apellidos,
                                    'estado' => 1,
                                    'ciudad' => $ciudad_tel,
                                    'departamento' => $departamento_tel
                                );
                                $dataAgendaTelefonica['id_parentesco'] = ($id_parentesco !== null ) ? $id_parentesco : "";
                                $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);
                                checkDbError($this->db);
                            }

                        }

                    }

                }

                print_r ( "Termino: ".$clientes->id." ". $clientes->nombres."</br>");
        }
        }


    }
    public function migrar_telefonos_transunion_post(){
        $this->db->select('*');
        $this->db->from('maestro.clientes');
        //$this->db->limit(5);
        $clientes = $this->db->get();
        foreach ($clientes->result() as $key => $clientes) {
            //busco la consulta en DATACREDITO
            $this->db->where('NumeroIdentificacion ="'.$clientes->documento.'"');
            $this->db->select('*');
            $this->db->from('api_buros.dataconsulta');
            $consulta = $this->db->get();
            if(!empty($consulta->result())){
                foreach ($consulta->result() as $key => $consulta) {
                    # code...
                    $id_consulta=$consulta->id;
                    //BUSCO EN DATACREDITO CELULARES
                    $this->db->where(['IdConsulta' => $id_consulta]);
                    $this->db->select('*');
                    $this->db->from('api_buros.pecoriginacion_celulares');
                    $consulta_celular = $this->db->get();
                    if(!empty($consulta_celular->result())){
                        foreach ($consulta_celular->result() as $key => $consulta_celular) {
                            $this->db->where(['codigo' => $consulta->CodigoDepartamento]);
                            $this->db->select('id_departamento, nombre_departamento');
                            $this->db->from('parametria.geo_departamento');
                            $this->db->limit(1);
                            $query = $this->db->get();
                            $departamento = $query->row();

                            $this->db->where(['codigo' => $consulta->CodigoMunicipio, 'codigo_departamento' => $consulta->CodigoDepartamento]);
                            $this->db->select('id_municipio, nombre_municipio');
                            $this->db->from('parametria.geo_municipio');
                            $this->db->limit(1);
                            $query = $this->db->get();
                            $municipio = $query->row();

                            $this->db->where(['departamento_tel' => $departamento->nombre_departamento]);
                            $this->db->select('areaCode, ciudad_tel, departamento_tel');
                            $this->db->from('parametria.tel_codigoarea');
                            $this->db->limit(1);
                            $query = $this->db->get();
                            $codigo_area = $query->row();

                            $areaCode = "";
                            $ciudad_tel = "";
                            $departamento_tel = "";

                            if(!empty($codigo_area)){
                                $areaCode = (string)$codigo_area->areaCode;
                                $ciudad_tel = $codigo_area->ciudad_tel;
                                $departamento_tel = $codigo_area->departamento_tel;
                            }
                            # code...
                            $nombres_apellidos='';
                            $telefono=$consulta_celular->Celular;
                            $id_parentesco="";
                            /**
                            * crear agenda telefonica por cada referencia
                            */
                            $this->db->select('id');
                            $existe_num_ref = $this->db->get_where('maestro.agenda_telefonica',['id_cliente' => $clientes->id, 'numero' => $telefono]);
                            if(empty($existe_num_ref->result())){
                                $dataAgendaTelefonica = array(
                                    'id_cliente' => $clientes->id,
                                    'indicativo_ciudad' => $areaCode,
                                    'numero' => $telefono,
                                    'tipo' => 1,
                                    'fuente' => 7,
                                    'contacto'=>$nombres_apellidos,
                                    'estado' => 1,
                                    'ciudad' => $ciudad_tel,
                                    'departamento' => $departamento_tel
                                );
                                $dataAgendaTelefonica['id_parentesco'] = ($id_parentesco !== null ) ? $id_parentesco : "";
                                $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);
                                checkDbError($this->db);
                            }
                        }
                    }
                }
                print_r ( "Termino: ".$clientes->id." ". $clientes->nombres."</br>");
            }
        }
    }


    //consulta de bancos de desembolso
    public function consultar_bancos_desembolso_get($id_banco, $limit){
        
        $response['status']['ok'] = FALSE;
        $response['message'] = 'No hay registros';
        //consultamos los bancos disponibles
        if($id_banco > 0){
            $bancosDesembolso = $this->bankEntidades->search(['aplica_desembolso' => 1, 'id_banco' => $id_banco]);
        } else {
            $bancosDesembolso = $this->bankEntidades->search(['aplica_desembolso' => 1]);
        }
        $datos = $bancosDesembolso;
        foreach ($bancosDesembolso as $key => $value) {
            //consulto indicadores
            if($limit > 0){
                $reenviados = $this->bankEntidades->desembolso_cantidades_reenviados(['desembolsa_a' => $value['desembolsa_a'], 'limit' => $limit]);
                
                $limit = $limit - intval($reenviados[0]->unidades);
                
                $desembolso_cantidades = $this->bankEntidades->desembolso_cantidades(['desembolsa_a' => $value['desembolsa_a'], 'limit' => $limit]);
            }else {
                $reenviados = $this->bankEntidades->desembolso_cantidades_reenviados(['desembolsa_a' => $value['desembolsa_a']]);
                $desembolso_cantidades = $this->bankEntidades->desembolso_cantidades(['desembolsa_a' => $value['desembolsa_a']]);
            }

            $desembolso = [
                'unidades' =>  intval($reenviados[0]->unidades) + intval($desembolso_cantidades[0]->unidades),
                'monto'  =>  floatval($reenviados[0]->monto) + floatval($desembolso_cantidades[0]->monto)
            ];

            $datos[$key]['valores']= $desembolso;
        }
        $status = parent::HTTP_OK;
        if (!empty($bancosDesembolso)) {
            $response['message'] = 'consulta realizada con exito';
            $response['status']['ok'] = TRUE;
        }
        $response['status']['code'] = $status;
        $response['data'] = $datos;
        $this->response($response, $status);
    }

    private function send_whatsapp($data){

        $url_send_template_message_new = base_url().'comunicaciones/Twilio/send_new_message';   //Produccion

        $response = Requests::post($url_send_template_message_new,[], $data);

        return $response;
    }

    private function getMessage($config){

        switch ($config['procedimiento']) {

            case 'confirmacionDesembolso':
                if($config['via'] === "whatsapp" && $config['template'] === false){

                    $message  = $config['message'];

                    foreach ($config['replace'] as $key => $value) {
                        $message = str_replace("{{".$key."}}",$value, $message);
                    }

                }elseif($config['via'] === "whatsapp" && $config['template'] !== false){

                    $message = mensaje_whatapp_maker($config['template'], $config['id_solicitud']);
                    $message = $message['message'];

                }

                return $message;
                break;
            
            default:
                # code...
                break;
        }
    }
    /**
     * Method de prueba para enviar notificaciones al whatsapp.
     */
    public function send_ws_confirmacion_desembolso_test_post($documento, $id_solicitud){
        //1020800830
        $response = array_base();
        $chat = $this->prestamo->checkStatusChat($documento);
        if(!empty($chat)){
            if( $chat->status_chat == "activo"){
                $nombreCompleto = $chat->nombres;
                $message  = "Hola {{nombreCompleto}} Queremos contarte que realizamos del desembolso de tu crédito. El tiempo que pueda tardar tu entidad financiera en reflejarte el dinero en tu cuenta bancaria, dependerá de cuanto demore en gestionar y aceptar la transferencia. Muchas gracias por confiar en Solventa";
                $configMessage = ['procedimiento'=>'confirmacionDesembolso','replace' => ['nombreCompleto'=> $nombreCompleto], 'via' => 'whatsapp', 'message' => $message, 'template' => false];
                $message = $this->getMessage($configMessage);
            }else{

                $configMessage = ['procedimiento'=>'confirmacionDesembolso','template' => self::template_83, 'id_solicitud'=>$id_solicitud, 'via' => "whatsapp"];
                $message = $this->getMessage($configMessage);
            }
           
            $dataWhatsapp = [
                'message' => $message,
                'operatorID' => 108,
                'chatID' => 136051 //PRUEBA
            ];

            $response['send_whatsapp'] = $this->send_whatsapp($dataWhatsapp);
            $response['title_response'] = "send_ws_confirmacion_desembolso_test";
            $response['success'] = true;
            $response['data']['send_whatsapp'] = $dataWhatsapp;

        }else{

            $response['success'] = true;
            $response['data']['send_whatsapp'] = "No enviado.";
        }

        $this->response($response);

    }
    //generar archivos de desembolso
    public function generar_archivos_desembolso_post(){
        $response = $this->curl($this->post('url'), 'GET',[]);
        $status = parent::HTTP_OK;
        $this->response($response, $status);
    }

    private function curl($endPoint, $method = 'POST',  $params=[] ){
        //PENDIENTE REEMPLAZAR POR LA LIBRERIA REQUEST.
        $token = $this->session->userdata('token');
        $curl = curl_init();
        $options[CURLOPT_HTTPHEADER] = ['Authorization:'.$token];
        $options[CURLOPT_POSTFIELDS] = $params;
        $options[CURLOPT_URL] = $endPoint;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

        if(ENVIRONMENT == 'development')
        {
            $options[CURLOPT_CERTINFO] = 1;
            $options[CURLOPT_SSL_VERIFYPEER] = 0;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        
        curl_setopt_array($curl,$options);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
        {
          $response['error'] = 'cURL Error #:' . $err;
        }

        return $response;
    }
	
	/**
	 * Registra en payvalida una orden
	 *
	 * @throws Exception
	 */
	public function registrarPayValida_post()
	{
		if (ENVIRONMENT === "production") {
			$url = 'https://api.payvalida.com/api/v3/porders';
		} else {
			$url = 'https://api-test.payvalida.com/api/v3/porders';
		}
		
		$id = $this->input->post('id');
		$tipo = $this->input->post('tipo');
		$metodoDePago = $this->input->post('metodoDePago');

		if (empty($id) or empty($tipo) or empty($metodoDePago) ) {
			$response = [
				'success'=>false,
				'error'=>'Parametros no validos'
			];
			return $this->response($response, parent::HTTP_BAD_REQUEST);
		}
		
		$tiposDisponibles = ['C', 'A', 'T'];
		if (!in_array($tipo, $tiposDisponibles)) {
			$response = [
				'success' => false,
				'error' => 'Tipo no valido'
			];
			return $this->response($response, parent::HTTP_BAD_REQUEST);
		}
		
		$movimiento = $this->getPayvalidaMovimiento($id, $metodoDePago);
		
		if (!empty($movimiento)) {
			$response = $movimiento;
		} else {
			$payvalidaRegisterRequest = new PayvalidaRegisterRequest($id, $metodoDePago, $tipo);
			$insertedId = $payvalidaRegisterRequest->savePayvalidaMovimientos();
			
			$curlResponse = $this->curlPOSTPayValida($url, $payvalidaRegisterRequest->toArray());
			
			$response = [
				'referencia' => '',
				'vencimiento' => $payvalidaRegisterRequest->expiration
			];
			
			if (isset($curlResponse->CODE)) {
				$response = $this->getPayvalidaResponse($curlResponse, $response, $insertedId);
			} else {
				throw new Exception('Error en la API de Payvalida:' . $curlResponse->errorMessage);
			}
		}
		
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Obtiene el movimiento de payvalida existente
	 *
	 * @param $idReferencia
	 * @param $medioDePago
	 *
	 * @return array
	 */
	private function getPayvalidaMovimiento($idReferencia, $medioDePago)
	{
		$movimiento = $this->credito->getPayvalidaMovimientoByIdReferencia($idReferencia, $medioDePago);
		
		$response = [];
		if (!empty($movimiento)) {
			$response = [
				'referencia' => $movimiento[0]['referencia'],
				'vencimiento' => date('d/m/Y', strtotime($movimiento[0]['expiracion']))
			];
		}

		return $response;
	}
	
	/**
	 * Consulta una orden en payvalida usando el orderId que nosotros generamos
	 *
	 * @return mixed|void
	 */
	public function consultarPayValida_post()
	{
		$orderId = $this->input->post('orderId');
		
		if (ENVIRONMENT === "production") {
			$urlBase = 'https://api.payvalida.com/api/v3/porders/' . $orderId;
		} else {
			$urlBase = 'https://api-test.payvalida.com/api/v3/porders/' . $orderId;
		}
		
		$data = [
			'merchant' => PAYVALIDA_MERCHANT,
			'checksum' => hash('SHA512', $orderId . PAYVALIDA_MERCHANT . PAYVALIDA_FIXED_HASH),
		];
		
		$url = $urlBase . "?" . http_build_query($data);
		
		return $this->curlGETPayValida($url, []);
	}
	
	/**
	 * Actualiza una orden en payvalida
	 *
	 * @return mixed|void
	 * @throws Exception
	 */
	public function actualizarPayValida_post()
	{
		$data = $this->input->post('data');
		if (empty($data)) {
			$data = [];
		} else {
			$data = json_decode($data, true)[0];
		}
		
		$orderId = $this->input->post('orderId');
		$metodoDePago = $this->input->post('metodoDePago');
		
		if (ENVIRONMENT === "production") {
			$url = 'https://api.payvalida.com/api/v3/porders';
		} else {
			$url = 'https://api-test.payvalida.com/api/v3/porders';
		}
		
		$payvalidaUpdateRequest = new PayvalidaUpdateRequest($orderId, $metodoDePago);
		
		//actualizo los valores
		$payvalidaUpdateRequest->updateByArray($data);
		
		$insertedId = $payvalidaUpdateRequest->savePayvalidaMovimientos();
		
		$curlResponse = $this->curlPATCHPayValida($url, $payvalidaUpdateRequest->toArray());
		
		$response = [
			'referencia' => '',
//			'link' => ''
		];
		
		if (isset($curlResponse->CODE)) {
			$response = $this->getPayvalidaResponse($curlResponse, $response, $insertedId);
		} else {
			throw new Exception('Error en la API de Payvalida:' . $curlResponse->errorMessage);
		}
		
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Borra una orden en payvalida usando el orderId que nosotros generamos
	 *
	 * @return mixed|void
	 */
	public function eliminarPayValida_post()
	{
		$orderId = $this->input->post('orderId');
		
		if (ENVIRONMENT === "production") {
			$urlBase = 'https://api.payvalida.com/api/v3/porders/' . $orderId;
		} else {
			$urlBase = 'https://api-test.payvalida.com/api/v3/porders/' . $orderId;
		}
		
		$data = [
			'merchant' => PAYVALIDA_MERCHANT,
			'checksum' => hash('SHA512', $orderId . PAYVALIDA_MERCHANT . PAYVALIDA_FIXED_HASH),
		];
		
		$url = $urlBase . "?" . http_build_query($data);
		return $this->curlDELETEPayValida($url, []);
	}
	
	/**
	 * Metodo Delete de payvalida
	 *
	 * @param $url
	 * @param $data
	 *
	 * @return mixed|void
	 */
	private function curlDELETEPayValida($url, $data)
	{
		return $this->curlPayValida("DELETE", $url, $data);
	}
	
	/**
	 * Metodo Patch de payvalida
	 *
	 * @param $url
	 * @param $data
	 *
	 * @return mixed|void
	 */
	private function curlPATCHPayValida($url, $data)
	{
		return $this->curlPayValida("PATCH", $url, $data);
	}
	
	/**
	 * Metodo Post de Payvalida
	 *
	 * @param $url
	 * @param $data
	 *
	 * @return mixed|void
	 */
	private function curlPOSTPayValida($url, $data)
	{
		return $this->curlPayValida("POST", $url, $data);
	}
	
	/**
	 * Metodo Get de Payvalida
	 *
	 * @param $url
	 * @param $data
	 *
	 * @return mixed|void
	 */
	private function curlGETPayValida($url, $data)
	{
		return $this->curlPayValida("GET", $url, $data);
	}
	
	/**
	 * Curl a payvalida
	 *
	 * @param $method
	 * @param $url
	 * @param $data
	 *
	 * @return mixed|void
	 */
	private function curlPayValida($method, $url, $data)
	{
		$a = json_encode($data, true);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $a,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json"
			),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo 'cURL Error #:' . $err;
			die;
		}
		
		return json_decode($response);
	}
	
	/**
	 * @param $curlResponse
	 * @param array $response
	 * @param $insertedId
	 *
	 * @return array
	 * @throws Exception
	 */
	private function getPayvalidaResponse($curlResponse, array $response, $insertedId): array
	{
		if ($curlResponse->CODE == '0000') {
			$response['referencia'] = $curlResponse->DATA->Referencia;
//			$response['link'] = $curlResponse->DATA->checkout;
			
			$this->credito->savePayvalidaRegisterResponse(
				$insertedId,
				$curlResponse->DATA->PVordenID,
				$curlResponse->DATA->Referencia,
				$curlResponse->DATA->checkout
			);
		} else {
			$this->credito->savePayvalidaRegisterError(
				$insertedId,
				$curlResponse->CODE . ' - ' . $curlResponse->DESC
			);
			throw new Exception('Ocurrio un error al registrar en payvalida');
		}
		return $response;
	}
	
	/**
	 * Registra e imputa u pago de payvalida
	 * 
	 * @return void
	 */
	public function registrarPayvalidaPago_post()
	{
		$pv_po_id = $this->input->post('pv_po_id');
		
		$exist = $this->prestamo->checkIfExistPayvalidaPayment($pv_po_id);
		
		if ($exist) {
			$response = [
				'Error' => "La referencia ya posee un pago"
			];
			return $this->response($response, parent::HTTP_BAD_REQUEST);
		}
		
		$paymentInfo = $this->prestamo->getPayvalidaPaymentInfo($pv_po_id);
		
		$po_id = $paymentInfo['po_id'];
		if (strpos($po_id, "C") !== false) {
			list($idCreditoDetalle, $random) = explode('C', $po_id);
			$tipoPago = 'Cuota';
			$this->registrarPagoPayvalidaCuota($idCreditoDetalle, $paymentInfo, $tipoPago);
		}
		
		if (strpos($po_id, "A") !== false) {
			list($idAcuerdo, $random) = explode('A', $po_id);
			$tipoPago = 'Acuerdo';
			$this->registrarPagoPayvalidaAcuerdo($idAcuerdo, $paymentInfo, $tipoPago);
		}
		
		if (strpos($po_id, "T") !== false) {
			list($idCliente, $random) = explode('T', $po_id);
			$tipoPago = 'Total';
			$this->registrarPagoPayvalidaTotal($idCliente, $paymentInfo, $tipoPago);
		}
	}
	
	private function registrarPagoPayvalidaAcuerdo($idAcuerdo, $paymentInfo, $tipoPago)
	{
		$acuerdo = $this->credito->getAcuerdoCuotaIdById($idAcuerdo)[0];
		$idCreditoDetalle = $acuerdo['id'];
		
		return $this->registrarPagoPayvalidaCuota($idCreditoDetalle, $paymentInfo, $tipoPago);
	}
	
	private function registrarPagoPayvalidaTotal($idCliente, $paymentInfo, $tipoPago)
	{
		$creditosDetalles = $this->credito->getCreditosDetalleVigMora($idCliente);

		if (!empty($creditosDetalles)) {
			return $this->registrarPagoPayvalidaCuota($creditosDetalles[0]->id, $paymentInfo, $tipoPago);
		}
	}
	
	private function registrarPagoPayvalidaCuota($idCreditoDetalle, $paymentInfo, $tipoPago)
	{
		if (!empty($paymentInfo)) {
			$monto = $paymentInfo['amount'];
			$fechaPago = $paymentInfo['expiracion'];
			$medioPago = $paymentInfo['pv_payment'];
			
			$data = [
				'id_credito_detalle' => $idCreditoDetalle,
				'monto' => $monto,
				'fecha_pago' => $paymentInfo['fecha_pago'],
				'referencia' => 'payvalida ' . $paymentInfo['pv_payment'],
				'medio_pago' => 'payvalida',
				'paymentMethod' => $paymentInfo['pv_payment'],
				'referencia_externa' => $paymentInfo['pv_po_id'],
				'referencia_interna' => $paymentInfo['po_id'],
				'payvalida' => true,
				'estado_razon' => 'Aceptada',
				'tipo_pago' => $tipoPago,
			];

			$response = $this->registrar_pago($data);
			
			$jsonResponse = json_decode($response);

			if (isset($jsonResponse->success) and $jsonResponse->success) {
				$cliente = $this->credito->getClientByIdCreditoDetalle($idCreditoDetalle);

				$data = [
					'id_cliente' => $cliente[0]['id'],
					'monto' => $monto,
					'fecha_pago' => $fechaPago,
					'medio_pago' => $medioPago,
					'id_pago_credito' => $jsonResponse->response,
				];
				
				$responseImputacion = $this->imputar_pago($data);
				
				$this->credito->markPayvalidaAsProcessed($paymentInfo['pv_po_id']);
				
				echo $responseImputacion;
			} else {
				$response = [
					'Error' => 'Error al registrar el pago: ' . $response
				];
				return $this->response($response, parent::HTTP_INTERNAL_SERVER_ERROR);
			}
		} else {
			$response = [
				'Error' => 'No se encontro referencia de pago en payvalida '
			];
			return $this->response($response, parent::HTTP_BAD_REQUEST);
		}
	}
	
	
	
	/**
	 * curl a medios de pago para registrar un pago
	 * 
	 * @param $data
	 *
	 * @return string
	 */
	private function registrar_pago($data)
	{
		if (ENVIRONMENT == 'development') {
			$end_point = "http://localhost/medios-de-pago/transaccion/RegistrarPago/registrar_pago";
		} else {
			$end_point = URL_MEDIOS_PAGOS . "transaccion/RegistrarPago/registrar_pago";
		}
		
		return $this->curlMediosDePago($end_point, $data);
	}
	
	/**
	 * curl a medios de pago  para imputar un pago
	 * 
	 * @param $data
	 *
	 * @return string
	 */
	private function imputar_pago($data)
	{
		if (ENVIRONMENT == 'development') {
			$end_point = "http://localhost/medios-de-pago/transaccion/RegistrarPago/imputacion";
		} else {
			$end_point = URL_MEDIOS_PAGOS . "transaccion/RegistrarPago/imputacion";
		}
		
		return $this->curlMediosDePago($end_point, $data);
	}
	
	/**
	 * Curl a endpoint de medios de pago
	 * 
	 * @param $endpoint
	 * @param $data
	 *
	 * @return string
	 */
	private function curlMediosDePago($endpoint, $data)
	{
		$hooks = new Requests_Hooks();
		$hooks->register('curl.before_send', function ($fp) {
			curl_setopt($fp, CURLOPT_TIMEOUT, 300);
		});
		$headers = array('Accept' => 'application/json');
		
		$request = Requests::post($endpoint, $headers, $data, array('hooks' => $hooks));
		return $request->body;
	}
}

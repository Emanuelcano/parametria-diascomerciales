<?php
defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);
require_once APPPATH . 'third_party/Format.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
class Tesoreria extends CI_Controller
{

    protected $end_folder = 'public/imputacion_pago/comprobantes';
    protected $file_config = [];
    protected $file_field = '';
    CONST template_82 = 58;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('formato_helper');  

        $this->load->library('form_validation');
        $this->load->helper('file');
        $this->load->helper('requests_helper');    

        if(ENVIRONMENT == 'development')
        {
            $this->load->model("CuentasBancarias_model",'cuentasBancarias');
            $this->load->model('operaciones/Gastos_model', 'operaciones', TRUE);
            $this->load->model('Solicitud_m','solicitud',TRUE);  
            $this->load->model('Credito_model','credito',TRUE);  
            $this->load->model('Cliente_model','cliente',TRUE);  
            $this->load->model('BankEntidades_model','bankEntidades',TRUE);  
            $this->load->model('ImputacionCredito_model','imputacionCredito',TRUE); 
            $this->load->model('prestamos/Prestamo_model', 'prestamo', TRUE);
            $this->load->model('DebitosAutomaticos_m','debitosautomaticos', TRUE);
			$this->load->model('Devolucion_model', 'devolucion_model', TRUE);
            $this->load->model('CreditoDetalle_model', 'creditoDetalle_model', TRUE);

            // LIBRARIES
            $this->load->library('form_validation');
            $this->load->helper('file');
            $this->load->helper('requests_helper');    

        }else{

            if ($this->session->userdata("is_logged_in")) {
                $this->load->model("CuentasBancarias_model",'cuentasBancarias');
                $this->load->model('operaciones/Gastos_model', 'operaciones', TRUE);
                $this->load->model('Solicitud_m','solicitud',TRUE);  
                $this->load->model('Credito_model','credito',TRUE);  
                $this->load->model('Cliente_model','cliente',TRUE);  
                $this->load->model('BankEntidades_model','bankEntidades',TRUE);  
                $this->load->model('ImputacionCredito_model','imputacionCredito',TRUE);  
                $this->load->model('prestamos/Prestamo_model', 'prestamo', TRUE);
                $this->load->model('DebitosAutomaticos_m','debitosautomaticos', TRUE);
                $this->load->model('Devolucion_model', 'devolucion_model', TRUE);
                $this->load->model('CreditoDetalle_model', 'creditoDetalle_model', TRUE);

                // LIBRARIES
                $this->load->library('form_validation');
                $this->load->helper('file');
                $this->load->helper('requests_helper');

            }else{
                redirect(base_url()."login/logout");
            }

        }

    }

    public function index()
    {
        if(ENVIRONMENT == 'development')
        {
            $cantidad_procesar_gasto = count($this->operaciones->get_gastos_pendientes_aprobados());
            $cantDesembolsoValidar = $this->solicitud->getCantDesembolsos();
            $cantidad_devoluciones = $this->devolucion_model->cantidad_solicitudes_devoluciones(['estado'=>0]);
            $data = array(
                'title' => 'Tesorería',
                'cantidad_procesar_gasto'=> $cantidad_procesar_gasto,
                'cantDesembolsoValidar' => $cantDesembolsoValidar[0]['cantidad'],
                'total_devoluciones' => $cantidad_devoluciones[0]->cantidad
            );
            $this->load->view('layouts/adminLTE', $data);
            $this->load->view('tesoreria/tesoreria',$data);
        }else{

            if ($this->session->userdata("is_logged_in")){
                $cantidad_procesar_gasto = count($this->operaciones->get_gastos_pendientes_aprobados());
                $cantidad_devoluciones = $this->devolucion_model->cantidad_solicitudes_devoluciones(['estado'=>0]);

                $data = array(
                    'title' => 'Tesorería',
                    'cantidad_procesar_gasto'=>$cantidad_procesar_gasto,
                    'total_devoluciones' => $cantidad_devoluciones[0]->cantidad
                );

                    $this->load->view('layouts/adminLTE', $data);
                    $this->load->view('tesoreria/tesoreria',$data);

            }else{
                redirect(base_url()."auth/logout");
            }
        }
    }

    public function vistaDevolucion(){
        $data = array(
            'title' => "Devoluciones",
        );
        $this->load->view('tesoreria/devoluciones',$data);
    }
    
    public function vistaPrestamos(){
        $cuentasBancarias = $this->cuentasBancarias->getCuentasBancarias(); 

            //ARMA LA DATA PARA LA VISTA
        $data = array(
            'title' => 'Tesorería',
            'tesoreria'=>[
                'cuentasBancarias' => $cuentasBancarias,
            ]
        );
        $this->load->view('tesoreria/vista_prestamos',$data);
    }

    public function vistaImputarPago(){
        $data = array(
            'cliente'
        );
        $this->load->view('tesoreria/vista_imputar_pago',$data);
    }

    public function vistaImputarPagoEfecty(){
        $data = array(
            'cliente'
        );
        $this->load->view('tesoreria/vista_imputar_pago_efecty',$data);
    }

    public function vistaRespuestaBanco(){
        $data = array(
            'title' => "Respuesta del BBVA",
            'banco' => "bbva"
        );
        $this->load->view('tesoreria/vista_respuesta_banco',$data);
    }

    public function vistaRespuestaBancoSantanter(){
        $data = array(
            'title' => "Respuesta del Santander",
            'banco' => 'santander'
        );
        $this->load->view('tesoreria/vista_respuesta_banco',$data);
    }
    public function vistaRespuestaProcesarGasto(){
        $cuentasBancarias = $this->cuentasBancarias->getCuentasBancarias(); 
        $gastos_pendientes = $this->operaciones->get_gastos_pendientes_aprobados(); 

        $data = array(
            'title' => 'Procesar Gastos',
            'gastos_pendientes' => $gastos_pendientes,
            'cuentasBancarias' => $cuentasBancarias
        );
        // var_dump($data['cuentasBancarias']);die;
        $this->load->view('tesoreria/vista_procesar_gasto',$data);
    }

    public function vistaRespuestaBanColombia(){
        $data = array(
            'title' => "Respuesta del BanColombia",
            'banco' => 'bancolombia'
        );
        $this->load->view('tesoreria/vista_respuesta_banco',$data);
    }

    public function vistaRespuestaBancobogota(){
        $data = array(
            'title' => "Respuesta de BancoBogota",
            'banco' => 'Bancobogota'
        );
        $this->load->view('tesoreria/vista_respuesta_banco',$data);
        
    }

    /**
     * Vista para inicial de Imputaciones Automaticas de Bancolombia
     * @return view
     */
    public function vistaImputacionAutomaticaBancolombia(){
        $data = array(
            'title' => "Imputación Automática",
            'banco' => "bancolombia"
        );
        $this->load->view('tesoreria/vista_imputacion_automatica_bancolombia',$data);
    }

    /**
     * Vista para inicial de Imputaciones Recaudo de Bancolombia
     * @return view
     */
    public function vistaImputacionRecaudoBancolombia(){
        $data = array(
            'title' => "Imputación Recaudo",
            'banco' => "bancolombia"
        );
        $this->load->view('tesoreria/vista_imputacion_recaudo_bancolombia',$data);
    }

    /**
     * Vista para inicial de Imputaciones Automaticas de Bancolombia
     * @return view
     */
    public function vistaDebitoAutomaticoBancolombia(){
        $data = array(
            'title' => "Debito Automático",
            'banco' => "bancolombia"
        );
        $this->load->view('tesoreria/vista_debito_automatico_bancolombia',$data);
    }

    /**
     * Vista inicial de Generacion Debitos
     * @return view
     */
    public function vistaGeneracionDebitos(){
        $data = array(
            'title' => "Generacion Debitos",
            'banco' => "bancolombia"
        );
        $this->load->view('tesoreria/vista_generacion_debitos',$data);
    }

    /**
     * Vista para inicial de Imputaciones Automaticas de Bancolombia
     * @return view
     */
/*      public function vistaDebitoAutomaticoBancolombiaInformeEnvios(){
        $data = array(
            'title' => "Debito Automático RCGA",
            'banco' => "bancolombia"
        );
        $this->load->view('tesoreria/vista_debito_automatico_bancolombia_informe_envios',$data);
    } */





    public function buscarCreditosClientes(){
        $data = [];
        $param = $this->input->post('paramBusqueda');
        if($param != null){
            $data = $this->credito->getCreditosByCliente($param);
        }

        echo json_encode(['data'=>$data]);

    }

    public function buscarEntidadesBancarias(){
        $data = $this->bankEntidades->search();
        echo json_encode(['data'=>$data]);
    }

    public function buscarCuentaBancaria(){
        $cuentasBancarias = $this->cuentasBancarias->findAllCuentasBancarias();
        $ctasAux = [];
        foreach($cuentasBancarias as $key => $cta ){
            $ctasAux[$key]['id'] = $cta->id;
            $ctasAux[$key]['numero_cuenta'] = $cta->Nombre_Banco . "-" . $cta->numero_cuenta;
        }
        echo json_encode(['data'=>$ctasAux]);
    }

    public function guardarImputacion(){
        $resultado = 'REVISION';
        $procesado = 0;

        $post= $this->input->post();
        $config['form'] = "imputacionPago";
        
        if($post['id_solicitud_imputacion']) {
            $config['resultado'] = true;
            if (array_key_exists('comprobante', $post)) {
                $config['comprobanteNuevo'] = false;
            } else {
                $config['comprobanteNuevo'] = true;
            }
            if ($post['medio_pago'] == 'Efecty' || $post['medio_pago'] == 'ePayco') {
                $config['bancoOrigen'] = false;
                $bancoOrigen = 0;
            } else {
                $config['bancoOrigen'] = true;
                $bancoOrigen = $post['id_banco_origen'];
            }
        } else {
            $config['resultado'] = false;
            $config['comprobanteNuevo'] = true;
            $config['bancoOrigen'] = true;
            $bancoOrigen = $post['id_banco_origen'];
        }

        
        //*Config para guardar el file
        $this->config_file['upload_path'] = $this->get_end_folder();
        $this->config_file['allowed_types'] = 'jpg|png|pdf|jpeg|xlxs';
        $this->config_file['overwrite'] = FALSE;
        $this->config_file['file_name'] = "imputar"."_".$post['documento'].'_'.$post['id_creditos_detalle']."_".date('Y-m-d');

        //El comprobante se sube a traves de un custom_rule de form_validation
        //ver _validate_save_input / rule callback_uploadFile - method uploadFile
        if($this->_validate_save_input($config))
        {
            $comprobante = '';
            if ($config['comprobanteNuevo']) {
                $file = $this->upload->data();
                $comprobante = base_url($this->get_end_folder().$file['file_name']);
            } else {
                $comprobante = $post['ruta_comprobante'];
            }
            $data = [
                'id_cliente' => $post['id_cliente'],
                'id_credito' => $post['id_credito'],
                'id_creditos_detalle' => $post['id_creditos_detalle'],
                'fecha_transferencia' => $post['fecha_transferencia'],
                'monto_transferencia' => $post['monto_transferencia'],
                'id_banco_origen' => $bancoOrigen,
                'id_cuenta_destino' => $post['id_cuenta_destino'],
                'referencia' => $post['referencia'],
                'comprobante' => $comprobante,
                'fecha_creacion' => date('Y-m-d H:i:s')
            ];
            $insert_id = $this->imputacionCredito->insert($data); 

            if(  $insert_id > 0){
                //Armado de data para servicio de registrar_pago
                $data_registro_pago = [
                    'id_credito_detalle' => $post['id_creditos_detalle'],
                    'monto' => $post['monto_transferencia'],
                    'fecha_pago' => $post['fecha_transferencia'],
                    'referencia' => $post['referencia'],
                    'id_archivo_adjunto' => $insert_id
                ];
                $registro_pago = $this->registrar_pago($data_registro_pago);
                
                if(!empty($registro_pago->response->pago)){
                    $registro_pago = json_decode($registro_pago);
                    $respuesta = $registro_pago->response->respuesta;
                    $id_pago_credito = $registro_pago->response->pago;
                }else{
                    $respuesta_array = explode("{", $registro_pago);
                    $respuesta_array = explode(",", $respuesta_array[2]);
                    $pago_array = $respuesta_array[0];
                    $pago = explode(":", $pago_array);
                    $pago = str_replace('"','',$pago[1]);
                    $respuesta = explode(":", $respuesta_array[2]);
                    $respuesta = str_replace('}}','',$respuesta);
                    $respuesta = $respuesta[1];
                    $id_pago_credito = $pago;
                }

                if($respuesta){

                    $data_imputar_pago = [
                        'id_cliente' => $post['id_cliente'],
                        'monto'      => $post['monto_transferencia'],
                        'fecha_pago' => $post['fecha_transferencia'],
                        'medio_pago' => "TRANSFERENCIA",
                        'id_pago_credito' => $id_pago_credito
                    ];
                    $imputar_pago = $this->imputar_pago($data_imputar_pago);
                    if(!empty($imputar_pago->response->respuesta)) {
                        $imputar_pago = json_decode($imputar_pago);
                        $respuesta_pago = $imputar_pago->response->respuesta;
                    }else{
                        $response_array = explode("{", $imputar_pago);
                        $response_array = explode(",", $response_array[2]);
                        $response_array = explode(":", $response_array[1]);
                        $respuesta_pago= $response_array; 
                    }
                    //var_dump($imputar_pago);die;
                    if($respuesta_pago){

                        $resultado = 'Imputado';
                        $procesado = 1;
                        $response = $imputar_pago;

                        /*** Se usa para el Track ***/
                        $id_solicitud = $this->imputacionCredito->getIdSolicitudCredito($post['id_credito']);
                        $arrTrack = array_base();
                        $arrTrack['success'] = true;
                        $arrTrack['response']['respuesta'] = true;
                        $arrTrack['response']['id_operador'] = $this->session->userdata("idoperador");
                        $arrTrack['response']['fecha_operacion'] = date("d-m-Y H:i:s");
                        $arrTrack['response']['id_solicitud'] = $id_solicitud[0]->id;

                        if($comprobante){
                            $arrTrack['response']['comprobante'] = $comprobante;
                            $arrTrack['response']['nombre_comprobante'] = substr($comprobante,41);
                        }
                        $response = $arrTrack;
                    }else{
                        $response = $imputar_pago;
                        $response['response']['errors'] = ['Error al realizar la imputación.'];
                        $log  = "Method: guardarImputacion - imputar_pago".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "ERROR: El pago fue procesado pero no pudo ser imputado".PHP_EOL.
                        "DATA: ".json_encode($imputar_pago).PHP_EOL.
                        "-------------------------".PHP_EOL;
                        //Save string to log, use FILE_APPEND to append.
                        file_put_contents('./application/logs/imputacion_manual_'.date("j.n.Y").'.log', $log, FILE_APPEND);
                        
                    }
                }else{
                    
                    unlink($this->get_end_folder().$file['file_name']);
                    $this->db->where(['id' => $insert_id ]);
                    $this->db->delete('maestro.imputacion_credito');
                    $response['response']['errors'] = ['Error al realizar la imputación.'];
                    $log  = "Method: guardarImputacion - registrar_pago".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "ERROR: No se registro el pago en la tabla pago_credito pero si en imputacion_credito".PHP_EOL.
                        "DATA: ".json_encode($registro_pago).PHP_EOL.
                        "-------------------------".PHP_EOL;
                    //Save string to log, use FILE_APPEND to append.
                    file_put_contents('./application/logs/imputacion_manual_'.date("j.n.Y").'.log', $log, FILE_APPEND);
                }

            }else{
                unlink($this->get_end_folder().$file['file_name']);
                $response['response']['respuesta'] = false;
                $response['response']['errors'] = ['Error al realizar al registrar el pago.'];
                $log  = "Method: guardarImputacion - imputacionCredito->insert".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "ERROR: No se inserto el registro en la tabla imputacion_credito".PHP_EOL.
                        "DATA: ".json_encode($data_registro_pago).PHP_EOL.
                        "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('./application/logs/imputacion_manual_'.date("j.n.Y").'.log', $log, FILE_APPEND);
            }
           
        }else{
            $validationErr = $this->form_validation->error_array();
            $response['response']['errors']  = $validationErr;
        }

        
        /**
         * actualizamos el registro de la solicitud de imputacion si corresponde
         *  Si viene el id de la solicitud de imputación se actualiza a procesado
         */

        if ($post['id_solicitud_imputacion']) {
            $id = $post['id_solicitud_imputacion'];
            $data = [
                'resultado' => $resultado,
                'por_procesar' => $procesado,
                'fecha_proceso' => date('Y-m-d H:i:s'),
                'comentario' => $post['comentario'],
                'id_operador_procesa' => $this->session->userdata("idoperador")
            ];
            $actualizo = $this->credito->updateSolicitudImputacion($data, $id); 
        }
        echo json_encode($response);
    }

    public function getvalidacioncliente() {
        $post= $this->input->post();
        $count = count($this->cliente->getrenovacionCliente($post['doc']));
        $cliente = $this->cliente->getClienteBy(['documento' => $post['doc']]);
        $nombre = explode(' ', $cliente[0]->nombres);

        if ($count > 0):
            $msj['sms'] = 'Hola '.$nombre[0].', tu pago ya fue imputado. Ya puedes solicitar tu renovación ingresando en https://solventa.co/ingresar';
            $msj['ws']  = $nombre[0].', tu pago ya fue imputado. Ya puedes solicitar tu renovación ingresando en https://solventa.co/ingresar';
        elseif ($count == 0):
            $msj['sms'] = 'Hola '.$nombre[0].', tu pago ya fue imputado.';
            $msj['ws']  = $nombre[0].', tu pago ya fue imputado.';
        endif;
        $response = $this->sendmsj($post['doc'], $msj);
        echo json_encode($response);
    }

    private function sendmsj($doc, $msj){        
        $sms = $this->solicitud->get_agenda_personal_solicitud(["documento" => $doc,"fuentes" => "'PERSONAL DECLARADO'","estado" => 1]);
        if (count($sms) >0 ) {
            $whatsapp = $this->solicitud->get_agenda_whatsapp(["documento" => $doc,"status_chat" => 'activo', "from" => $sms[0]['numero']]);
        }
        $response['sms']    = [];
        $response['ws']     = [];
        if (count($sms) > 0) {
            $endPoint = URL_CAMPANIAS."ApiEnvioComuGeneral";
            $response['sms'] = Requests::post($endPoint, [], ["tipo_envio" => 2,"servicio" => 2,"text" => $msj['sms'],"numero" => "+57".$sms[0]['numero']]); 
        }
        if (count($whatsapp) > 0) {
            $endPoint = base_url()."comunicaciones/twilio/send_new_message";
            $response['ws'] = Requests::post($endPoint, [], ['chatID'  => $whatsapp[0]['id'],'message' => $msj['ws'],'operatorID' => 192 ]); 
        }
        return $response;
    }

    /*
    * Procesamiento de archivo xls
    */
    public function procesarRespuestaBbva(){

        $this->end_folder = 'public/respuesta_banco/bbva';
        $config['form'] = "respuestaBanco";
        $this->config_file['upload_path'] =  $this->get_end_folder();
        $this->config_file['allowed_types'] = 'xlsx|xls';
        $this->config_file['overwrite'] = FALSE;
        //$this->config_file['file_name'] = "respuesta_bbva_".date('Y-m-d');

        $response = array_base();
        if($this->_validate_save_input($config)){
            
            if(ENVIRONMENT == 'development')
            {
                //Consume metodo local de prueba.
                $upload_xls_bbva = json_decode($this->test_response());
                $response['ambiente_prueba'] = 'usando metodo test_response en ambiente de desarrollo';

            }else{
                //Consume Servicio de producción.
                $full_path_file = $this->upload->data()['full_path'];
                $upload_xls_bbva = $this->upload_file_bbva($full_path_file);
                $upload_xls_bbva = json_decode($upload_xls_bbva);
            }
            if($upload_xls_bbva->success){
                //Formateado de la respuesta para la vista 
                $solicitudes_pagadas = [];
                $solicitud_ya_pagados = [];
                $solicitud_no_pagadas = [];
                $solicitud_pago_con_error = [];
               
                foreach($upload_xls_bbva->response->solicitud_pagadas as $key => $pagadas){
                    if(!empty($pagadas)){
                        foreach($pagadas as $key => $pagadasAux){
                            $pagadasAux->respuesta = "solicitud_pagada";
                            $pagadasAux->orden = 3;

                            $fecha_proceso=date_create($pagadasAux->fecha_proceso);
                            $fecha_cobro=date_create($pagadasAux->fecha_cobro);
                            $pagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $pagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitudes_pagadas, (array)$pagadasAux);
                        }
                    }

                }

                foreach($upload_xls_bbva->response->solicitud_ya_pagados as $key => $yaPagadas){
                    if(!empty($yaPagadas)){
                        foreach($yaPagadas as $key => $yaPagadasAux){
                            $yaPagadasAux->respuesta = "solicitud_ya_pagados";
                            $yaPagadasAux->orden = 4;

                            $fecha_proceso=date_create($yaPagadasAux->fecha_proceso);
                            $fecha_cobro=date_create($yaPagadasAux->fecha_cobro);
                            $yaPagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $yaPagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitud_ya_pagados, (array)$yaPagadasAux);
                        }
                    }

                }

                if(!empty($upload_xls_bbva->response->solicitud_no_pagadas)){
                    foreach($upload_xls_bbva->response->solicitud_no_pagadas as $key => $noPagadas){
                        if(!empty($noPagadas)){
                            foreach($noPagadas as $key => $noPagadasAux){
                                $params['documento'] = $noPagadasAux->documento;
                                $params['limite'] = 1;
                                $solicitud=$this->solicitud->getSolicitudesBy($params);
                                if(ENVIRONMENT != 'development'){

                                    $this->notificacionDesembolsoFallido($solicitud);
                                }
                                $noPagadasAux->respuesta = "solicitud_no_pagadas";
                                $noPagadasAux->orden = 1;

                                $fecha_proceso=date_create($noPagadasAux->fecha_proceso);
                                $fecha_cobro=date_create($noPagadasAux->fecha_cobro);
                                $noPagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                                $noPagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                                array_push($solicitud_no_pagadas, (array)$noPagadasAux);
                            }
                            
                        }

                    }
                }

                foreach($upload_xls_bbva->response->solicitud_pago_con_error as $key => $pagoError){
                    if(!empty($pagoError)){
                        foreach($pagoError as $key => $pagoErrorAux){
                            $pagoErrorAux->respuesta = "solicitud_pago_con_error";
                            $pagoErrorAux->orden = 2;

                            $fecha_proceso=date_create($pagoErrorAux->fecha_proceso);
                            $fecha_cobro=date_create($pagoErrorAux->fecha_cobro);
                            $pagoErrorAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $pagoErrorAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitud_pago_con_error, (array)$pagoErrorAux);
                        }
                    }

                }

                $solicitudes = array_merge($solicitud_no_pagadas, $solicitud_pago_con_error);
                
                $solicitudes = array_merge($solicitudes, $solicitudes_pagadas);
                
                $solicitudes = array_merge($solicitudes, $solicitud_ya_pagados);



                if(empty($solicitudes)){
                    $response["success"] = true;
                    $response["title_response"] = "Conéxión establecida.";
                    $response["data"] = $solicitudes;
                    $response["response"]['respuesta'] = false ;
                    $response["response"]['mensaje'] = "El proceso no retorno resultados." ;
                    $response["response"]['errors'] = [];

                }else{
                    $response["success"] = true;
                    $response["title_response"] = "Conéxión establecida.";
                    $response["data"] = $solicitudes;
                    $response["response"]['respuesta'] = true ;
                    $response["response"]['mensaje'] = "El archivo se proceso con exito." ;
                }

            }else{

                $response["response"]['respuesta'] = false ;
                $response["response"]['mensaje'] = "No se pudo completar la carga del archivo.";
                $response["response"]['errors'] = ["No se pudo completar la carga del archivo."];

            }
        }else{
            $validationErr = $this->form_validation->error_array();
            $response['response']['errors']  = $validationErr;
        }

        echo json_encode($response);

    }


    /*
    * Procesamiento de archivo xls Banco Santander
    */
    public function procesarRespuestaSantander(){
        $this->end_folder = 'public/respuesta_banco/santander';
        $config['form'] = "respuestaBancoSantander"; //Para realizar validacion del form de subida del lado del servidor
        $this->config_file['upload_path'] =  $this->get_end_folder();
        $this->config_file['allowed_types'] = 'xlsx';
        $this->config_file['overwrite'] = FALSE;
        //$this->config_file['file_name'] = "respuesta_santander_".date('Y-m-d');

        $response = array_base();
        if($this->_validate_save_input($config)){

            if(ENVIRONMENT == 'development')
            {
                //Consume metodo local de prueba.
                $upload_xls_santander = json_decode($this->test_response_santander());
                $response['ambiente_prueba'] = 'usando metodo test_response en ambiente de desarrollo';

            }else{
                //Consume Servicio de producción.
                $full_path_file = $this->upload->data()['full_path'];
                $upload_xls_santander = $this->upload_file_santander($full_path_file);
                $upload_xls_santander = json_decode($upload_xls_santander);
            }
            if($upload_xls_santander->success){
                //Formateado de la respuesta para la vista 
                $solicitudes_pagadas = [];
                $solicitud_ya_pagados = [];
                $solicitud_no_pagadas = [];
                $solicitud_pago_con_error = [];

                foreach($upload_xls_santander->response->solicitud_pagadas as $key => $pagadas){
                    if(!empty($pagadas)){
                        foreach($pagadas as $key => $pagadasAux){
                            $pagadasAux->respuesta = "solicitud_pagada";
                            $pagadasAux->orden = 3;

                            $fecha_proceso=date_create($pagadasAux->fecha_proceso);
                            $fecha_cobro=date_create($pagadasAux->fecha_cobro);
                            $pagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $pagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitudes_pagadas, (array)$pagadasAux);
                        }
                    }

                }

                foreach($upload_xls_santander->response->solicitud_ya_pagados as $key => $yaPagadas){
                    if(!empty($yaPagadas)){
                        foreach($yaPagadas as $key => $yaPagadasAux){
                            $yaPagadasAux->respuesta = "solicitud_ya_pagados";
                            $yaPagadasAux->orden = 4;

                            $fecha_proceso=date_create($yaPagadasAux->fecha_proceso);
                            $fecha_cobro=date_create($yaPagadasAux->fecha_cobro);
                            $yaPagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $yaPagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitud_ya_pagados, (array)$yaPagadasAux);
                        }
                    }

                }

                if(!empty($upload_xls_santander->response->solicitud_no_pagadas)){
                    foreach($upload_xls_santander->response->solicitud_no_pagadas as $key => $noPagadas){
                        if(!empty($noPagadas)){
                            foreach($noPagadas as $key => $noPagadasAux){
                                $params['documento'] = $noPagadasAux->documento;
                                $params['limite'] = 1;
                                $solicitud=$this->solicitud->getSolicitudesBy($params);
                                if(ENVIRONMENT != 'development'){
                                    $this->notificacionDesembolsoFallido($solicitud);
                                }
                                $noPagadasAux->respuesta = "solicitud_no_pagadas";
                                $noPagadasAux->orden = 1;

                                $fecha_proceso=date_create($noPagadasAux->fecha_proceso);
                                $fecha_cobro=date_create($noPagadasAux->fecha_cobro);
                                $noPagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                                $noPagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                                array_push($solicitud_no_pagadas, (array)$noPagadasAux);
                            }
                            
                        }

                    }
                }

                foreach($upload_xls_santander->response->solicitud_pago_con_error as $key => $pagoError){
                    if(!empty($pagoError)){
                        foreach($pagoError as $key => $pagoErrorAux){
                            $pagoErrorAux->respuesta = "solicitud_pago_con_error";
                            $pagoErrorAux->orden = 2;

                            $fecha_proceso=date_create($pagoErrorAux->fecha_proceso);
                            $fecha_cobro=date_create($pagoErrorAux->fecha_cobro);
                            $pagoErrorAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $pagoErrorAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitud_pago_con_error, (array)$pagoErrorAux);
                        }
                    }

                }

                $solicitudes = array_merge($solicitud_no_pagadas, $solicitud_pago_con_error);
                
                $solicitudes = array_merge($solicitudes, $solicitudes_pagadas);
                
                $solicitudes = array_merge($solicitudes, $solicitud_ya_pagados);



                if(empty($solicitudes)){
                    $response["success"] = true;
                    $response["title_response"] = "Conéxión establecida.";
                    $response["data"] = $solicitudes;
                    $response["response"]['respuesta'] = false ;
                    $response["response"]['mensaje'] = "El proceso no retorno resultados." ;
                    $response["response"]['errors'] = [];

                }else{
                    $response["success"] = true;
                    $response["title_response"] = "Conéxión establecida.";
                    $response["data"] = $solicitudes;
                    $response["response"]['respuesta'] = true ;
                    $response["response"]['mensaje'] = "El archivo se proceso con exito." ;
                }

            }else{

                $log  = "Method: procesarRespuestaSantander - upload_xls_santander_1".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA ENDPOINT: ".json_encode($upload_xls_santander).PHP_EOL.
                        "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('./application/logs/respuesta_santander_'.date("j.n.Y").'.log', $log, FILE_APPEND);

                $response["response"]['respuesta'] = false ;
                $response["response"]['mensaje'] = "No se pudo completar la carga del archivo.";
                $response["response"]['errors'] = ["No se pudo completar la carga del archivo."];

            }
        }else{

            $validationErr = $this->form_validation->error_array();

            $log  = "Method: procesarRespuestaSantander - _validate_save_input".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA: ".json_encode($validationErr).PHP_EOL.
                        "-------------------------".PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents('./application/logs/respuesta_santander_'.date("j.n.Y").'.log', $log, FILE_APPEND);
            $response['response']['errors']  = $validationErr;
        }

        echo json_encode($response);

    }

    /*
    * Procesamiento de archivo xls Banco Bancolombia
    */
    public function procesarRespuestaBancolombia(){

        $this->end_folder = 'public/respuesta_banco/bancolombia';
        $config['form'] = "respuestaBanColombia"; //Para realizar validacion del form de subida del lado del servidor
        $this->config_file['upload_path'] =  $this->get_end_folder();
        $this->config_file['allowed_types'] = 'xlsx|xls';
        $this->config_file['overwrite'] = FALSE;
        //$this->config_file['file_name'] = "respuesta_bancolombia_".date('Y-m-d');

        $response = array_base();

        if($this->_validate_save_input($config)){

            if(ENVIRONMENT == 'development')
            {
                //Consume metodo local de prueba.
                $upload_xls_bancolombia = json_decode($this->test_response_bancolombia());
                $full_path_file = $this->upload->data()['full_path'];                
                //$upload_xls_bancolombia = $this->upload_file_bancolombia($full_path_file);
                //$upload_xls_bancolombia = json_decode($upload_xls_bancolombia);
                $response['ambiente_prueba'] = 'usando metodo test_response en ambiente de desarrollo';
            }else{
                //Consume Servicio de producción.
                $full_path_file = $this->upload->data()['full_path'];
                $upload_xls_bancolombia = $this->upload_file_bancolombia($full_path_file);
                $upload_xls_bancolombia = json_decode($upload_xls_bancolombia);
            }
            // var_dump($upload_xls_bancolombia); die;
            if($upload_xls_bancolombia->success){
                //Formateado de la respuesta para la vista 
                $solicitudes_pagadas = [];
                $solicitud_ya_pagados = [];
                $solicitud_no_pagadas = [];
                $solicitud_pago_con_error = [];
               
                foreach($upload_xls_bancolombia->response->solicitud_pagadas as $key => $pagadas){
                    if(!empty($pagadas)){
                        foreach($pagadas as $key => $pagadasAux){
                            $pagadasAux->respuesta = "solicitud_pagada";
                            $pagadasAux->orden = 3;

                            $fecha_proceso=date_create($pagadasAux->fecha_proceso);
                            $fecha_cobro=date_create($pagadasAux->fecha_cobro);
                            $pagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $pagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitudes_pagadas, (array)$pagadasAux);
                        }
                    }

                }

                foreach($upload_xls_bancolombia->response->solicitud_ya_pagados as $key => $yaPagadas){
                    if(!empty($yaPagadas)){
                        foreach($yaPagadas as $key => $yaPagadasAux){
                            $yaPagadasAux->respuesta = "solicitud_ya_pagados";
                            $yaPagadasAux->orden = 4;

                            $fecha_proceso=date_create($yaPagadasAux->fecha_proceso);
                            $fecha_cobro=date_create($yaPagadasAux->fecha_cobro);
                            $yaPagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $yaPagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitud_ya_pagados, (array)$yaPagadasAux);
                        }
                    }

                }

                if(!empty($upload_xls_bancolombia->response->solicitud_no_pagadas)){
                    foreach($upload_xls_bancolombia->response->solicitud_no_pagadas as $key => $noPagadas){
                        if(!empty($noPagadas)){
                            foreach($noPagadas as $key => $noPagadasAux){
                                $params['documento'] = $noPagadasAux->documento;
                                $params['limite'] = 1;
                                $solicitud=$this->solicitud->getSolicitudesBy($params);
                                if(ENVIRONMENT != 'development'){
                                    $this->notificacionDesembolsoFallido($solicitud);
                                }
                                $noPagadasAux->respuesta = "solicitud_no_pagadas";
                                $noPagadasAux->orden = 1;

                                $fecha_proceso=date_create($noPagadasAux->fecha_proceso);
                                $fecha_cobro=date_create($noPagadasAux->fecha_cobro);
                                $noPagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                                $noPagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                                array_push($solicitud_no_pagadas, (array)$noPagadasAux);
                            }
                            
                        }

                    }
                }

                foreach($upload_xls_bancolombia->response->solicitud_pago_con_error as $key => $pagoError){
                    if(!empty($pagoError)){
                        foreach($pagoError as $key => $pagoErrorAux){
                            $pagoErrorAux->respuesta = "solicitud_pago_con_error";
                            $pagoErrorAux->orden = 2;

                            $fecha_proceso=date_create($pagoErrorAux->fecha_proceso);
                            $fecha_cobro=date_create($pagoErrorAux->fecha_cobro);
                            $pagoErrorAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $pagoErrorAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitud_pago_con_error, (array)$pagoErrorAux);
                        }
                    }

                }

                $solicitudes = array_merge($solicitud_no_pagadas, $solicitud_pago_con_error);
                
                $solicitudes = array_merge($solicitudes, $solicitudes_pagadas);
                
                $solicitudes = array_merge($solicitudes, $solicitud_ya_pagados);



                if(empty($solicitudes)){
                    $response["success"] = true;
                    $response["title_response"] = "Conéxión establecida.";
                    $response["data"] = $solicitudes;
                    $response["response"]['respuesta'] = false ;
                    $response["response"]['mensaje'] = "El proceso no retorno resultados." ;
                    $response["response"]['errors'] = [];

                }else{
                    $response["success"] = true;
                    $response["title_response"] = "Conéxión establecida.";
                    $response["data"] = $solicitudes;
                    $response["response"]['respuesta'] = true ;
                    $response["response"]['mensaje'] = "El archivo se proceso con exito." ;
                }

            }else{

                $log  = "Method: procesarRespuestaBanColombia - upload_xls_bancolombia_1".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA ENDPOINT: ".json_encode($upload_xls_bancolombia).PHP_EOL.
                        "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('./application/logs/respuesta_bancolombia_'.date("j.n.Y").'.log', $log, FILE_APPEND);

                $response["response"]['respuesta'] = false ;
                $response["response"]['mensaje'] = "No se pudo completar la carga del archivo.";
                $response["response"]['errors'] = ["No se pudo completar la carga del archivo."];

            }
        }else{

            $validationErr = $this->form_validation->error_array();

            $log  = "Method: procesarRespuestaBancolombia - _validate_save_input".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA: ".json_encode($validationErr).PHP_EOL.
                        "-------------------------".PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents('./application/logs/respuesta_bancolombia_'.date("j.n.Y").'.log', $log, FILE_APPEND);
            $response['response']['errors']  = $validationErr;
        }

        echo json_encode($response);

    }

    public function procesarRespuestaBancobogota(){

        $this->end_folder = 'public/respuesta_banco/bancobogota';
        $config['form'] = "respuestaBancoBogota"; //Para realizar validacion del form de subida del lado del servidor
        $this->config_file['upload_path'] =  $this->get_end_folder();
        $this->config_file['allowed_types'] = 'xlsx|xls';
        $this->config_file['overwrite'] = FALSE;
        //$this->config_file['file_name'] = "respuesta_bancolombia_".date('Y-m-d');

        $response = array_base();

        if($this->_validate_save_input($config)){

            if(ENVIRONMENT == 'development'){
                //Consume metodo local de prueba.
                $upload_xls_bancobogota = json_decode($this->test_response_bancolombia());
                $full_path_file = $this->upload->data()['full_path'];
                //$upload_xls_bancobogota = $this->upload_file_bancobogota($full_path_file);
                //$upload_xls_bancobogota = json_decode($upload_xls_bancobogota);
                $response['ambiente_prueba'] = 'usando metodo test_response en ambiente de desarrollo';
            }else{
                //Consume Servicio de producción.
                $full_path_file = $this->upload->data()['full_path'];
                $upload_xls_bancobogota = $this->upload_file_bancobogota($full_path_file);
                $upload_xls_bancobogota = json_decode($upload_xls_bancobogota);
            }

            if($upload_xls_bancobogota->success){
                //Formateado de la respuesta para la vista 
                $solicitudes_pagadas = [];
                $solicitud_ya_pagados = [];
                $solicitud_no_pagadas = [];
                $solicitud_pago_con_error = [];
               
                foreach($upload_xls_bancobogota->response->solicitud_pagadas as $key => $pagadas){
                    if(!empty($pagadas)){
                        foreach($pagadas as $key => $pagadasAux){
                            $pagadasAux->respuesta = "solicitud_pagada";
                            $pagadasAux->orden = 3;

                            $fecha_proceso=date_create($pagadasAux->fecha_proceso);
                            $fecha_cobro=date_create($pagadasAux->fecha_cobro);
                            $pagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $pagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitudes_pagadas, (array)$pagadasAux);
                        }
                    }

                }
                foreach($upload_xls_bancobogota->response->solicitud_ya_pagados as $key => $yaPagadas){
                    if(!empty($yaPagadas)){
                        foreach($yaPagadas as $key => $yaPagadasAux){
                            $yaPagadasAux->respuesta = "solicitud_ya_pagados";
                            $yaPagadasAux->orden = 4;

                            $fecha_proceso=date_create($yaPagadasAux->fecha_proceso);
                            $fecha_cobro=date_create($yaPagadasAux->fecha_cobro);
                            $yaPagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $yaPagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitud_ya_pagados, (array)$yaPagadasAux);
                        }
                    }

                }

                if(!empty($upload_xls_bancobogota->response->solicitud_no_pagadas)){
                    foreach($upload_xls_bancobogota->response->solicitud_no_pagadas as $key => $noPagadas){
                        if(!empty($noPagadas)){
                            foreach($noPagadas as $key => $noPagadasAux){
                                $params['documento'] = $noPagadasAux->documento;
                                $params['limite'] = 1;
                                $solicitud=$this->solicitud->getSolicitudesBy($params);
                                if(ENVIRONMENT != 'development'){
                                    $this->notificacionDesembolsoFallido($solicitud);
                                }
                                $noPagadasAux->respuesta = "solicitud_no_pagadas";
                                $noPagadasAux->orden = 1;

                                $fecha_proceso=date_create($noPagadasAux->fecha_proceso);
                                $fecha_cobro=date_create($noPagadasAux->fecha_cobro);
                                $noPagadasAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                                $noPagadasAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                                array_push($solicitud_no_pagadas, (array)$noPagadasAux);
                            }
                            
                        }

                    }
                }

                foreach($upload_xls_bancobogota->response->solicitud_pago_con_error as $key => $pagoError){
                    if(!empty($pagoError)){
                        foreach($pagoError as $key => $pagoErrorAux){
                            $pagoErrorAux->respuesta = "solicitud_pago_con_error";
                            $pagoErrorAux->orden = 2;

                            $fecha_proceso=date_create($pagoErrorAux->fecha_proceso);
                            $fecha_cobro=date_create($pagoErrorAux->fecha_cobro);
                            $pagoErrorAux->fecha_proceso = date_format($fecha_proceso,"d-m-Y");
                            $pagoErrorAux->fecha_cobro = date_format($fecha_cobro,"d-m-Y");

                            array_push($solicitud_pago_con_error, (array)$pagoErrorAux);
                        }
                    }

                }

                $solicitudes = array_merge($solicitud_no_pagadas, $solicitud_pago_con_error);
                
                $solicitudes = array_merge($solicitudes, $solicitudes_pagadas);
                
                $solicitudes = array_merge($solicitudes, $solicitud_ya_pagados);

                if(empty($solicitudes)){
                    $response["success"] = true;
                    $response["title_response"] = "Conéxión establecida.";
                    $response["data"] = $solicitudes;
                    $response["response"]['respuesta'] = false ;
                    $response["response"]['mensaje'] = "El proceso no retorno resultados." ;
                    $response["response"]['errors'] = [];

                }else{
                    $response["success"] = true;
                    $response["title_response"] = "Conéxión establecida.";
                    $response["data"] = $solicitudes;
                    $response["response"]['respuesta'] = true ;
                    $response["response"]['mensaje'] = "El archivo se proceso con exito." ;
                }

            }else{

                $log  = "Method: procesarRespuestaBancobogota - upload_xls_bancobogota_1".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA ENDPOINT: ".json_encode($upload_xls_bancobogota).PHP_EOL.
                        "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('./application/logs/respuesta_bancobogota_'.date("j.n.Y").'.log', $log, FILE_APPEND);

                $response["response"]['respuesta'] = false ;
                $response["response"]['mensaje'] = "No se pudo completar la carga del archivo.";
                $response["response"]['errors'] = ["No se pudo completar la carga del archivo."];

            }
        }else{

            $validationErr = $this->form_validation->error_array();

            $log  = "Method: procesarRespuestaBancobogota - _validate_save_input".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA: ".json_encode($validationErr).PHP_EOL.
                        "-------------------------".PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents('./application/logs/respuesta_bancobogota_'.date("j.n.Y").'.log', $log, FILE_APPEND);
            $response['response']['errors']  = $validationErr;
        }

        echo json_encode($response);

    }

    ///Mothod test de procesamiento de archivo de respuesta de BBVA
    private function test_response(){
        /*$response = [
            "success" => true,
            "title_response" =>"Conéxión establecida.",
            "text_response" => "",
            "response"=> [
                "solicitud_pagadas" => [
                    
                ],
                "solicitud_ya_pagados" => [
                    
                ],
                "solicitud_no_pagadas" => [
                    "78646879" => [
                        "documento" => "78646879",
                        "monto" => "150000",
                        "fecha_proceso" => "2020-04-08",
                        "fecha_cobro" => "2020-04-08",
                        "estado" => "R16 CUENTA ORDENANTE O BENEF. INACTI"
                    ]
                ],
                "solicitud_pago_con_error" => [
                    
                ]
            ]
        ];*/
        //PRUEBA
        $response = '{"success":true,"title_response":"Conéxión establecida.","text_response":"","response":{"solicitud_pagadas":[],"solicitud_pago_con_error":[] ,"solicitud_ya_pagados":[],"solicitud_no_pagadas":[{"78646879":{"documento":"78646879","monto":"150000","fecha_proceso":"2020-04-08","fecha_cobro":"2020-04-08","estado":"AUTORIZADO"}}]}}';
        return $response;
    }

    ///Mothod test de procesamiento de archivo de respuesta de Santander
    private function test_response_santander(){
        //PRUEBA
        $response = '{"success":true,
            "title_response":"Conéxión establecida.",
            "text_response":"",
            "response":{
               "solicitud_pagadas":[
                  {
                     "1045698139":{
                        "documento":1045698139,
                        "monto":"300000.00",
                        "fecha_proceso":"2020-04-03",
                        "fecha_cobro":"2020-04-03",
                        "estado":"Confirmada",
                        "causal":""
                     },
                     "1128398614":{
                        "documento":1128398614,
                        "monto":"446880.00",
                        "fecha_proceso":"2020-04-03",
                        "fecha_cobro":"2020-04-03",
                        "estado":"Confirmada",
                        "causal":""
                     }
                  }
               ],
               "solicitud_ya_pagados":[
                  [
         
                  ]
               ],
               "solicitud_no_pagadas":[
                  {
                     "78646879":{
                        "documento":78646879,
                        "monto":"250500.00",
                        "fecha_proceso":"2020-04-03",
                        "fecha_cobro":"2020-04-03",
                        "estado":"Devuelta",
                        "causal":"Cuenta Invalida"
                     }
                  }
               ],
               "solicitud_pago_con_error":[
                  [
         
                  ]
               ]
            }
         }';
        return $response;
    }

    ///Mothod test de procesamiento de archivo de respuesta de Santander
    private function test_response_bancolombia(){
        //PRUEBA
        $response = '{
                "success":true,
                "title_response":"Conéxión establecida.",
                "text_response":"",
                "response":{
                   "solicitud_pagadas":[
                      {
                         "1045698139":{
                            "documento":1045698139,
                            "monto":"300000.00",
                            "fecha_proceso":"2020-04-03",
                            "fecha_cobro":"2020-04-03",
                            "estado":"Confirmada",
                            "causal":""
                         },
                         "1128398614":{
                            "documento":1128398614,
                            "monto":"446880.00",
                            "fecha_proceso":"2020-04-03",
                            "fecha_cobro":"2020-04-03",
                            "estado":"Confirmada",
                            "causal":""
                         }
                      }
                   ],
                   "solicitud_ya_pagados":[
                      [
             
                      ]
                   ],
                   "solicitud_no_pagadas":[
                      {
                         "78646879":{
                            "documento":78646879,
                            "monto":"250500.00",
                            "fecha_proceso":"2020-04-03",
                            "fecha_cobro":"2020-04-03",
                            "estado":"Devuelta",
                            "causal":"Cuenta Invalida"
                         }
                      }
                   ],
                   "solicitud_pago_con_error":[
                      [
             
                      ]
                   ]
                }
             }';
        return $response;
    }

    /*
    * armado de directorio donde se guardan los archivos cargados.
    */
    public function get_end_folder()
    {
        $end_folder = $this->end_folder.'/'.date('Y').'/'.date('m').'/'.date('d').'/';
            // Carpeta de destino.
        // $this->file_full_path= $end_folder.$this->file_name;

        // Valida que la carpeta de destino exista, si no existe la crea.
        if(!file_exists($end_folder))
        {
            // Si no puede crear el directorio.
            if(!mkdir($end_folder, 0777, true))
            {
                $this->response['status']['ok'] = FALSE;
                $this->response['errors'] = 'No fué posible crear el directorio en .' . $end_folder;
                return FALSE;
            }
        }
        return $end_folder;
    }
    
    
    /*
    * Mando sms y mail para avisar que no se pagaron
    */
    public function send_sms_mail($id_solicitud)
    {
        $data = [ 'idSolicitud' => $id_solicitud ];
        //Enviar dos endpoint
        $headers = array('Accept' => 'application/json');
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp){
            curl_setopt($fp, CURLOPT_TIMEOUT, 300);
        });

        $end_point = URL_CAMPANIAS."CampaniasDesembolso/EnviarSmsNoPudoPagar";
        Requests::post($end_point, $headers, $data, array('hooks' => $hooks));

        $end_point = URL_CAMPANIAS."CampaniasDesembolso/EnviarMailNoPudoPagar";
        Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
        
    }

    /**
    * @param Solicitud object []
    */
    private function notificacionDesembolsoFallido($solicitud)
    {
        $solicitud = $solicitud[0];
        $id_solicitud = $solicitud->id;
        $tipo_solicitud = $solicitud->tipo_solicitud;
        $data = [ 'idSolicitud' => $id_solicitud ];
        //Enviar dos endpoint
        $headers = array('Accept' => 'application/json');
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp){
            curl_setopt($fp, CURLOPT_TIMEOUT, 300);
        });

        $end_point = URL_CAMPANIAS."CampaniasDesembolso/EnviarSmsNoPudoPagar";
        Requests::post($end_point, $headers, $data, array('hooks' => $hooks));

        switch ($tipo_solicitud) {
            case 'RETANQUEO':
                $end_point = URL_CAMPANIAS."CampaniasDesembolso/EnviarMailNoPudoPagar";
                Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
                break;

            case 'PRIMARIA':
                //Verify status of chat
                $chat = $this->prestamo->checkStatusChat($solicitud->documento);

                if(!empty($chat)){
                    if( $chat->status_chat == "activo"){
                        $nombreCompleto = $chat->nombres;
                        $datosBancarios = $this->getDatosBancarios($id_solicitud);
                        if($datosBancarios !== false ){
                            $Nombre_Banco = $datosBancarios->Nombre_Banco;
                            $Nombre_TipoCuenta = $datosBancarios->Nombre_TipoCuenta;
                            $numero_cuenta = $datosBancarios->numero_cuenta;
                        }else{
                            $Nombre_Banco = "";
                            $Nombre_TipoCuenta = "";
                            $numero_cuenta = "";          
                        }

                        $message  = "Hola ".$nombreCompleto.". No hemos podido desembolsar el dinero en tu cuenta bancaria personal del Banco: ". $Nombre_Banco ." ".$Nombre_TipoCuenta." Numero: ".  $numero_cuenta.". 
                        Necesito que me envies una certificación bancaria para confirmar que todos los datos esten correctos y reenviar a desembolsar tu crédito.";

                    }else{

                        $message = mensaje_whatapp_maker(self::template_82, $id_solicitud);
                        $message = $message['message'];
                    }
                   
                    $dataWhatsapp = [
                        'message' => $message,
                        'operatorID' => 108,
                        'chatID' => $chat->id
                    ];

                    $this->send_whatsapp($dataWhatsapp);
                }
                break;
        }
    }
    
    /*
    * Set validations rules.
    */
    private function getRulesConfig($config){

        if($config['form'] == "imputacionPago"){
            $comprobanteNuevo = $config['comprobanteNuevo'];
            $bancoOrigen = $config['bancoOrigen'];
            $resultado = $config['resultado'];
            //Imputacion de Pago
            $this->file_field = "comprobante";
            $config = array(
                array(
                        'field' => 'id_cliente',
                        'label' => 'Cliente',
                        'rules' => 'required',
                        'errors' => array(
                                'required' => 'El campo %s es obligatorio',
                        )
                ),
                array(
                        'field' => 'id_credito',
                        'label' => 'Credito',
                        'rules' => 'required',
                        'errors' => array(
                                'required' => 'El campo %s es obligatorio',
                        )
                ),
                array(
                        'field' => 'id_creditos_detalle',
                        'label' => 'Cuota',
                        'rules' => 'required',
                        'errors' => array(
                                'required' => 'El campo %s es obligatorio',
                        )
                ),
                array(
                        'field' => 'fecha_transferencia',
                        'label' => 'Fecha Transferencia',
                        'rules' => 'required',
                        'errors' => array(
                                'required' => 'El campo %s es obligatorio',
                        )
                ),
                array(
                        'field' => 'monto_transferencia',
                        'label' => 'Monto Transferencia',
                        'rules' => 'required',
                        'errors' => array(
                                'required' => 'El campo %s es obligatorio',
                        )
                ),
                array(
                        'field' => 'id_cuenta_destino',
                        'label' => 'Cuenta Destino',
                        'rules' => 'required',
                        'errors' => array(
                                'required' => 'El campo %s es obligatorio',
                        )
                ),
                array(
                    'field' => 'comprobante_exist',
                    'rules' => 'callback_validExistComprobante'
                )
                
            );
            if($bancoOrigen) {
                $config[] = array(
                    'field' => 'id_banco_origen',
                    'label' => 'Entidad Bancaría',
                    'rules' => 'required',
                    'errors' => array(
                            'required' => 'El campo %s es obligatorio',
                    )

                );
            }
            if($comprobanteNuevo) {
                $config[] = array(
                    'field' => 'comprobante',
                    'rules' => [
                        'callback_uploadFile'
                    ]
                );
            }
            if($resultado) {
                $config[] = array(
                    'field' => 'resultado',
                    'label' => 'Resultado',
                    'rules' => 'required',
                    'errors' => array(
                        'required' => 'El campo %s es obligatorio',
                    )
                );
            }
        }else if($config['form']=== "respuestaBanco"){
            //BancoBBVA
            $this->file_field = "fileRespuestaBanco";
            $config =  array(
                [
                    'field' => 'fileRespuestaBanco',
                    'rules' => [
                        'callback_uploadFile'
                    ]
                ]
            );
        }elseif($config['form']=== "respuestaBancoSantander"){
            //BancoSantander
            $this->file_field = "fileRespuestaBanco";
            $config =  array(
                [
                    'field' => 'fileRespuestaBanco',
                    'rules' => [
                        'callback_uploadFile'
                    ]
                ]
            );
        }elseif($config['form']=== "respuestaBanColombia"){
            //BanColombia
            $this->file_field = "fileRespuestaBanco";
            $config =  array(
                [
                    'field' => 'fileRespuestaBanco',
                    'rules' => [
                        'callback_uploadFile'
                    ]
                ]
            );
        }elseif($config['form']=== "respuestaBancoBogota"){
                //BancoBogota
                $this->file_field = "fileRespuestaBanco";
                $config =  array(
                    [
                        'field' => 'fileRespuestaBanco',
                        'rules' => [
                            'callback_uploadFile'
                        ]
                    ]
                );
        }elseif($config['form']=== "imputacionAutomaticaBancolombia"){
            $this->file_field = "fileImputacionAutomaticaBancolombia";
            $config =  array(
                [
                    'field' => 'fileImputacionAutomaticaBancolombia',
                    'rules' => [
                        'callback_uploadFile'
                    ]
                ]
            );
        }elseif($config['form']=== "imputacionRecaudoBancolombia"){
            $this->file_field = "fileImputacionRecaudoBancolombia";
            $config =  array(
                [
                    'field' => 'fileImputacionRecaudoBancolombia',
                    'rules' => [
                        'callback_uploadFile'
                    ]
                ]
            );
        }

        return $config;
    }

    public function _validate_save_input($config)
    {

        $fieldsConfig = $this->getRulesConfig($config);
        $this->form_validation->set_rules($fieldsConfig);
        
        if($this->form_validation->run())
        {
            return TRUE;
        }else
        {
            return FALSE;
        }
    }
    /**
    *   Consume el service registrar pago de api mediospagos.solventa.co
    *
    **/
    private function registrar_pago($data){
        if(ENVIRONMENT == 'development')
        {
            //Consume metodo local de prueba.
            /*$response = array_base();
            $response['response']['respuesta'] = true;
            $response['success'] = true;
            $response = json_encode($response);*/
            $headers = array('Accept' => 'application/json');
            $hooks = new Requests_Hooks();

            $hooks->register('curl.before_send', function($fp){
                curl_setopt($fp, CURLOPT_TIMEOUT, 300);
            });

            $end_point = "https://testmediospagos.solventa.co/transaccion/RegistrarPago/registrar_pago";
            $request = Requests::post($end_point, $headers, $data,array('hooks' => $hooks));
            $response = $request->body;

        }else{
            $hooks = new Requests_Hooks();

            $hooks->register('curl.before_send', function($fp){
                curl_setopt($fp, CURLOPT_TIMEOUT, 300);
            });
            $headers = array('Accept' => 'application/json');
            $end_point = URL_MEDIOS_PAGOS."transaccion/RegistrarPago/registrar_pago";
            $request = Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
            $response = $request->body;
        }
        return $response;
    }

    /**
    *   Consume el service imputar pago de api mediospagos.solventa.co
    *
    **/
    private function imputar_pago($data){
        //var_dump("http://localhost/medios-de-pago-2.0/test/imputacion?id_cliente=$cliente&monto=$monto&fecha_pago=$fecha&medio_pago=$medio&id_pago_credito=$pago");die;
        if(ENVIRONMENT == 'development')
        {
            //Consume metodo local de prueba.
            /*$response = array_base();
            $response['success'] = true;
            $response['response']['respuesta'] = true;
            $response = json_encode($response);*/
            $headers = array('Accept' => 'application/json');
            $headers = array('Accept' => 'application/json');
            $hooks = new Requests_Hooks();

            $hooks->register('curl.before_send', function($fp){
                curl_setopt($fp, CURLOPT_TIMEOUT, 3000);
            });
            $end_point = "https://testmediospagos.solventa.co/transaccion/RegistrarPago/imputacion";
            $request = Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
            $response = $request->body;

        }else{
            $hooks = new Requests_Hooks();

            $hooks->register('curl.before_send', function($fp){
                curl_setopt($fp, CURLOPT_TIMEOUT, 300);
            });
            $headers = array('Accept' => 'application/json');
            //$end_point = "https://mediospagos.solventa.co/transaccion/RegistrarPago/imputar_pago";
            $end_point = URL_MEDIOS_PAGOS."transaccion/RegistrarPago/imputacion";

            $request = Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
            $response = $request->body;
        }
        return  $response;
    }

    /**
    *   Consume el service https://mediospagos.solventa.co/bbva/Cashout/upload_xls_bbva
    *
    **/
    private function upload_file_bbva($full_path_file){

        $url_api_bbva_upload_bbva = URL_MEDIOS_PAGOS.'bbva/Cashout/upload_xls_bbva';   //Produccion
        $body = array();
        if(file_exists($full_path_file)){
            $files = array();
            $files['file'] = $full_path_file;

            array_walk($files, function($filePath, $key) use(&$body) {
                $body[$key] = curl_file_create($filePath);
            });
        }
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_TIMEOUT, 500);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $headers = array('Content-Type' => 'multipart/form-data');

        $response = Requests::post($url_api_bbva_upload_bbva, $headers, array(), array('hooks' => $hooks));
        return ($response->body);
    }

    /**
    *   Consume el service https://mediospagos.solventa.co/santander/SantanderCashOut/upload_xls_santander
    *
    **/
    private function upload_file_santander($full_path_file){

        $url_api_bbva_upload_bbva = URL_MEDIOS_PAGOS.'santander/SantanderCashOut/upload_xls_santander';   //Produccion
        $body = array();
        if(file_exists($full_path_file)){
            $files = array();
            $files['file'] = $full_path_file;

            array_walk($files, function($filePath, $key) use(&$body) {
                $body[$key] = curl_file_create($filePath);
            });
        }
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_TIMEOUT, 8000);
            curl_setopt($fp, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $headers = array('Accept' => 'application/json');

        $response = Requests::post($url_api_bbva_upload_bbva, $headers, array(), array('hooks' => $hooks));
        $log  = "Method: upload_file_santander".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA ENDPOINT: ".$response->body.PHP_EOL.
                        "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('./application/logs/respuesta_santander_'.date("j.n.Y").'.log', $log, FILE_APPEND);
        return ($response->body);
    }

    /**
    *   Consume el service https://mediospagos.solventa.co/bancolombia/BancolombiaCashOut/upload_xls_bancolombia
    *
    **/
    private function upload_file_bancolombia($full_path_file){

        // $url_api_upload_bcolombia = 'http://mediospagos.solventa.local/bancolombia/BancolombiaCashOut/upload_xls_bancolombia';   //Testing
        $url_api_upload_bcolombia = URL_MEDIOS_PAGOS.'bancolombia/BancolombiaCashOut/upload_xls_bancolombia';   //Produccion
        $body = array();
        if(file_exists($full_path_file)){
            $files = array();
            $files['file'] = $full_path_file;

            array_walk($files, function($filePath, $key) use(&$body) {
                $body[$key] = curl_file_create($filePath);
            });
        }
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_TIMEOUT, 500);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $headers = array('Accept' => 'application/json');

        $response = Requests::post($url_api_upload_bcolombia, $headers, array(), array('hooks' => $hooks));
        $log  = "Method: upload_file_bancolombia".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA ENDPOINT: ".$response->body.PHP_EOL.
                        "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('./application/logs/respuesta_bancolombia_'.date("j.n.Y").'.log', $log, FILE_APPEND);
        return ($response->body);
    }

    private function upload_file_bancobogota($full_path_file){
                                     
        //$url_api_upload_bcobogota = 'https://testmediospagos.solventa.co:10443/bcobogota/BcobogotaCashOut/upload_xls_bcobogota';//Testing
        $url_api_upload_bcobogota = URL_MEDIOS_PAGOS.'bcobogota/BcobogotaCashOut/upload_xls_bcobogota';   //Produccion

        $body = array();
        if(file_exists($full_path_file)){
            $files = array();
            $files['file'] = $full_path_file;

            array_walk($files, function($filePath, $key) use(&$body) {
                $body[$key] = curl_file_create($filePath);
            });
        }
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_TIMEOUT, 500);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $headers = array('Accept' => 'application/json');

        $response = Requests::post($url_api_upload_bcobogota, $headers, array(), array('hooks' => $hooks));
        $log  = "Method: upload_file_bancobogota".' - '.date("F j, Y, g:i a").PHP_EOL.
                        "RESPUESTA ENDPOINT: ".$response->body.PHP_EOL.
                        "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('./application/logs/respuesta_bancobogota_'.date("j.n.Y").'.log', $log, FILE_APPEND);
        return ($response->body);
    }

    /*
    * valida si el comprobante de imputacion fue subido anteriormente
    *
    */
    public function validExistComprobante(){
        $post = $this->input->post();
        if (array_key_exists('id_banco_origen', $post)) {
            $bancoOrigen = $post['id_banco_origen'];
        } else {
            $bancoOrigen = 0;
        }
        $data = [
            'id_cliente' => $post['id_cliente'],
            'id_credito' => $post['id_credito'],
            'id_creditos_detalle' => $post['id_creditos_detalle'],
            'id_banco_origen' => $bancoOrigen,
            'referencia' => $post['referencia'],
        ];
        $result = $this->imputacionCredito->validIfExist($data);
        $exist = false;

        if(empty($result)){
            $exist = true;
        }else{
            $this->form_validation->set_message('validExistComprobante', 'El comprobante que intenta subir ya existe.');
        }
        return $exist;
    }

    public function uploadFile(){
        $config = $this->config_file;
        $fileName = $this->file_field;
        $this->load->library('upload');
        $this->upload->initialize($config);

        if ($this->upload->do_upload($fileName)){
            return true;

        }else{

            $uploadError = str_replace(["<p>", "</p>"], "", $this->upload->display_errors());
            $this->form_validation->set_message("uploadFile", $uploadError);
            return false;
        }

    }

    public function vistaValidarDesembolsos() {
        $desembolsos = $this->solicitud->getDesembolsos();
        // dump($desembolso); die;

        $data = array(
            'desembolsos' => $desembolsos
        );
        $this->load->view('tesoreria/validarDesembolso_view', $data);
    }
    /**************************************************/
    /*** Se actualiza la Solicitud de la Imputación ***/
    /**************************************************/
    public function actualizarSolicitudImputacion(){
        $post= $this->input->post();
        if (array_key_exists('id_banco_origen', $post)) {
            $bancoOrigen = $post['id_banco_origen'];
        } else {
            $bancoOrigen = 0;
        }
        $id = $post['id_solicitud_imputacion'];
        $data = [
            'referencia'    => $post['referencia'],
            'fecha_pago'    => date_format(date_create($post['fecha_transferencia']),"Y/m/d"),
            'monto_pago'    => $post['monto_transferencia'],
            'banco_origen'  => $bancoOrigen,
            'banco_destino' => $post['id_cuenta_destino'],
            'resultado'     => $post['resultado'],
            'comentario'     => $post['comentario'],
            'id_operador_procesa' => $this->session->userdata("idoperador")
        ];
        $actualizo = $this->credito->updateSolicitudImputacion($data, $id);
        if ($actualizo > 0) {
            $response['success'] = true;
            $response['response']['respuesta'] = true;
            $response["response"]['mensaje'] = "Actualizado con éxito.";
            /*** Se usa para el Track ***/
            $id_solicitud = $this->imputacionCredito->getIdSolicitudCredito($post['id_credito']);
            $response["response"]['id_operador'] = $this->session->userdata("idoperador");
            $response["response"]['fecha_operacion'] = date("d-m-Y H:i:s");            
            $response['response']['id_solicitud'] = $id_solicitud[0]->id;
        } else {
            $response["response"]['respuesta'] = false ;
            $response["response"]['mensaje'] = "No se pudo completar la actualización del registro.";
            $response["response"]['errors'] = ["No se pudo completar la actualización del registro."];
        }
        echo json_encode($response);
    }
    /*************************************************/
    /*** Se obtienen las solicitudes de imputación ***/
    /*************************************************/
    public function buscarSolicitudImputacion(){
        $data = [];
        $data = $this->credito->getSolicitudImputacion();
        echo json_encode(['data'=>$data]);
    }

    /**
     * Consume servicio de envio de whatsapp
     * @param object[(int)message, (int)operatorID, (int)chatID] 
     * @return if send whatsapp servicio : base_url().'comunicaciones/Twilio/send_new_message'
     */
    private function send_whatsapp($data){

        $url_send_template_message_new = base_url().'comunicaciones/Twilio/send_new_message';   //Produccion
        Requests::post($url_send_template_message_new,[], $data);
    }

    public function getDatosBancarios($id_solicitud){

        $datosBancarios = $this->solicitud->getDatosBancarios($id_solicitud);
        $datos = new stdClass();
        if(!empty($datosBancarios)){

            foreach ($datosBancarios as $key => $datoBancario) {

                $datos->numero_cuenta = $datoBancario['numero_cuenta'];
                $datos->Nombre_Banco =  $datoBancario['Nombre_Banco'];
                $datos->Nombre_TipoCuenta = $datoBancario['Nombre_TipoCuenta'];
            }
        }else{

            $datos = false;
        }

        return $datos;
    }

    public function generarVistaDebitoAutomatico()
    {

        switch (ENVIRONMENT) {
			case 'development':
				$endpoint = URL_MEDIOS_PAGOS_LOCAL .'bancolombia/GeneracionDebitoAutomatico/generar_vista_debito_automatico';   //Testing
				break;
			case 'testing':
				$endpoint = URL_MEDIOS_PAGOS_LOCAL .'bancolombia/GeneracionDebitoAutomatico/generar_vista_debito_automatico';   //LOCAL
				break;
			case 'production':
				$endpoint = URL_MEDIOS_PAGOS.'bancolombia/GeneracionDebitoAutomatico/generar_vista_debito_automatico';   //Produccion
                break;
            default:
                $endpoint = URL_MEDIOS_PAGOS_LOCAL .'bancolombia/GeneracionDebitoAutomatico/generar_vista_debito_automatico';   //LOCAL
        }

        $post = $this->input->post();
        
        $body = array();

        $body['fechaInicio']                    = $post['fechaInicio'];
        $body['fechaFinalizacion']              = $post['fechaFinalizacion'];
        $body['auth']                           = $post['auth'];
        $body['estado']                         = $post['estado'];
        $body['fechaVencimiento']               = $post['fechaVencimiento'];
        $body['fechaVencimientoInicio']         = $post['fechaVencimientoInicio'];
        $body['fechaVencimientoFinal']          = $post['fechaVencimientoFinal'];
        $body['conDebitoMultiple']              = $post['conDebitoMultiple'];
        $body['tope']                           = $post['tope'];
        $body['atraso']                         = $post['atraso'];
        $body['bancotipo']                      = $post['bancotipo'];
        $body['tipoempleado']                   = $post['tipoempleado'];
        $body['sqlClientes']                    = $post['sqlClientes'];
        $body['sqlMonto']                       = $post['sqlMonto'];
        
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_TIMEOUT, 600);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $headers = array('Accept' => 'application/json');

        $response = Requests::post($endpoint, $headers, array(), array('hooks' => $hooks));

        echo $response->body;
    }

    public function enviarArchivosDebitoAutomatico()
    {

        switch (ENVIRONMENT) 
        {
			case 'development':
				$endpoint = URL_MEDIOS_PAGOS_LOCAL . 'bancolombia/GeneracionDebitoAutomatico/generar_archivos_debito_automatico';   // Local
                break;
			case 'testing':
				$endpoint = URL_MEDIOS_PAGOS_LOCAL . 'bancolombia/GeneracionDebitoAutomatico/generar_archivos_debito_automatico';   // Testing
				break;
			case 'production':
				$endpoint = URL_MEDIOS_PAGOS . 'bancolombia/GeneracionDebitoAutomatico/generar_archivos_debito_automatico';   // Produccion
                break;
            default:
                $endpoint = URL_MEDIOS_PAGOS_LOCAL . 'bancolombia/GeneracionDebitoAutomatico/generar_archivos_debito_automatico';   // Local
        }

        $post = $this->input->post();
        
        $body = array();

        $body['fechaInicio']                    = $post['fechaInicio'];
        $body['fechaFinalizacion']              = $post['fechaFinalizacion'];
        $body['auth']                           = $post['auth'];
        $body['estado']                         = $post['estado'];
        $body['fechaVencimiento']               = $post['fechaVencimiento'];
        //$body['conFechaVencimiento']            = $post['conFechaVencimiento'];
        //$body['filtroFechaVencimiento']         = $post['filtroFechaVencimiento'];
        $body['fechaVencimientoInicio']         = $post['fechaVencimientoInicio'];
        $body['fechaVencimientoFinal']          = $post['fechaVencimientoFinal'];
        $body['conDebitoMultiple']              = $post['conDebitoMultiple'];
        $body['tope']                           = $post['tope'];
        $body['atraso']                         = $post['atraso'];
        $body['bancotipo']                      = $post['bancotipo'];
        $body['tipoempleado']                   = $post['tipoempleado'];
        $body['sqlClientes']                    = $post['sqlClientes'];
        $body['sqlMonto']                       = $post['sqlMonto'];
        
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_TIMEOUT, 600);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $headers = array('Accept' => 'application/json');

        $response = Requests::post($endpoint, $headers, array(), array('hooks' => $hooks));

        echo json_encode($response->body);
    }

    public function procesarRespuestaImputacionRecaudo()
    {

        header('Content-Type: text/html; charset=ISO-8859-1');
        set_time_limit(0);

        $this->end_folder                   = 'uploads/bancolombia/recaudo';
        $config['form']                     = "imputacionRecaudoBancolombia";
        $this->config_file['upload_path']   = $this->get_end_folder();
        $this->config_file['allowed_types'] = 'txt';
        $this->config_file['overwrite']     = FALSE;
        $nombre_origen                      = $_FILES['fileImputacionRecaudoBancolombia']['name'];
        $rutaToday                          = $this->get_end_folder() . $nombre_origen;
        $response                           = array_base(); 

        $response['response']['respuesta'] = false;
        $response['response']['errors'] = [];
        $response['response']['mensaje'] = "Tipo de archivo erroneo. Solo archivos REC con extension .txt y Convenio 090652.";

        if((substr($nombre_origen, 0, 3) == "REC") && (substr($nombre_origen, 17, 6) == "090652"))
        {

            $response['response']['respuesta'] = false;
            $response['response']['errors'] = [];
            $response['response']['mensaje'] = "Archivo: ". $nombre_origen.", procesado anteriormente.";

            if(empty($this->fileExistInDB(str_replace(".txt", "",$nombre_origen))) && !file_exists($rutaToday)  )
            {       

                if($this->_validate_save_input($config))
                {

                    //Consume metodo local de prueba.
                    $full_path_file = $this->upload->data()['full_path'];
                    $file_name = $this->upload->data()['file_name'];
    
                    $monto_total = 0;
                    $file = new SplFileObject($full_path_file);
    
                    $debitos_cabecera_id = 0;
                    $count = 0; 
                    $i = 0;
                    $full_data = [];
                    $data = [];
    
                    $file_ok1_name          = str_replace(".txt", "",$file_name) . "_OK1" . ".txt" ;
                    $file_ok                = $this->get_end_folder() . $file_ok1_name;
                    $file_ok1               = fopen($file_ok, "w") or die("Unable to open file!"); 

                    foreach ($file as $k => $line) 
                    {

                        set_time_limit(0);

                        $line = utf8_decode($line);
                        $line = preg_replace('/[-?]/', 'X', $line);
                        $line = cleanString($line);

                        if(substr($line,0,1) == 1)
                        {
                            $monto_total = sprintf("%.2f", substr($line, 66, 17) / 100);
                            $headLine['ruta_back_txt']  = $full_path_file;
                            $headLine['nombre_archivo'] = str_replace(".txt", "",$file_name);
                            $headLine['fecha_recaudo']  = substr($line, 49, 8);
                            $headLine['origen_pago']    = "Bancolombia";
                            $debitos_cabecera_id = $this->debitosautomaticos->insert("solicitudes.debitos_cabecera_txt", $headLine);
                        } 
    
                        if(substr($line,0,1) == 6)
                        {

                            // OK1 | OK2 | OK4
                            if (((substr($line, 171, 3)) === 'OK1') || ((substr($line, 171, 3)) === 'OK2') || ((substr($line, 171, 3)) === 'OK4'))
                            {
    
                                $i++;
                                $count++;
                                
                                $documento = trim(preg_replace("/[^0-9]/", "",substr($line, 80,  30)));
                                $id_credito_detalle = $this->creditoDetalle_model->get_ultima_cuota_por_documento($documento);

                                if($id_credito_detalle != false)
                                {

                                    $bodyLine['id_cabecera_txt']    = $debitos_cabecera_id;
                                    $bodyLine['id_credito_detalle'] = $id_credito_detalle->id;
                                    $bodyLine['monto']              = $monto_total;
                                    $bodyLine['fecha_creado']       = (substr($line, 174, 8));
                                    $bodyLine['monto']              = sprintf("%.2f", (substr($line, 62,  17)) / 100);
        
                                    $data[$i]['nombre_completo_pagador']    = (substr($line, 14,  20));
                                    $data[$i]['cuenta_pagador']             = (substr($line, 34,  9));
                                    $data[$i]['monto']                      = sprintf("%.2f", (substr($line, 62,  17)) / 100);
                                    $data[$i]['id_credito_detalle']         = rtrim($id_credito_detalle->id);
                                    $data[$i]['status']                     = (substr($line, 171, 3));
                                    $data[$i]['fecha_recaudo']              = (substr($line, 174, 8)); 

                                    $nombre_cliente             = str_pad(substr($line, 14,  20), 20, ' ', STR_PAD_RIGHT);
                                    $monto                      = str_pad(substr($line, 62,  17), 17, 0, STR_PAD_LEFT);
                                    $numero_cuenta_cliente      = str_pad(substr($line, 80,  30), 9, ' ', STR_PAD_RIGHT);
                                    $ref1_cliente               = str_pad($id_credito_detalle->id, 30, ' ', STR_PAD_RIGHT);
                                    $ref2_cliente               = str_pad("", 30,   ' ', STR_PAD_RIGHT);
                                    $final1                     = substr($line, 140,  44);
                                    $final2                     = str_pad("", 3,   ' ', STR_PAD_RIGHT);
                                    $final3                     = substr($line, 187,  23);
    
                                    fwrite($file_ok1, "6" . '             ' . $nombre_cliente . "005600078" . "00000000000000000" . "67" . $monto . "S" . $ref1_cliente . $ref2_cliente . $final1 . $final2 . $final3 . "\n");
                                
                                }else{

                                    $rexc['monto_total']    = sprintf("%.2f", (substr($line, 62,  17)) / 100);
                                    $rexc['ruta_back_txt']  = $full_path_file;
                                    $rexc['nombre_archivo'] = str_replace(".txt", "",$file_name);
                                    $rexc['fecha_recaudo']  = (substr($line, 174, 8));
                                    $rexc['origen_pago']    = "RECAUDO";
                                    $rexc['imputado']       = 0;
                                    $rexc['documento']      = substr($line, 80,  30);
                                    $this->debitosautomaticos->insert("maestro.recaudo_sin_imputar", $rexc);

                                }
                                
                            }
    
                        }

                    }

                    $response['response']['respuesta'] = true;
                    $response['success'] = true;

                    $log  = date("Y-m-d h:i:s") . " SALIDA FOREACH "  . PHP_EOL;
                    file_put_contents('./application/logs/respuesta_process_da_bancolombia_'.date("j.n.Y").'.log', $log, FILE_APPEND);
    
                    fclose($file_ok1);
    
                    $dataUpdate['total_txt'] = $count;
                    $where['id'] = $debitos_cabecera_id;
    
                    $this->debitosautomaticos->update("solicitudes.debitos_cabecera_txt", $where, $dataUpdate);
                    $this->lectura_debito_automatico($file_ok, str_replace(".txt", "",$file_ok1_name ), 'CONV_090652');
    
                    $detalle['archivo'] = str_replace(".txt", "",$file_name);
                    $detalle['total_txt'] = $count;
                    $detalle['origen_pago'] = "Bancolombia";
                    $detalle['fecha_subida'] = date("d-m-Y h:i:s");
                    $detalle['monto_total'] = $monto_total;
                    $detalle['full_path_file'] = $full_path_file;
                    $response['detalle'] = $detalle;
                    $full_data = array_merge($full_data, $data);
                    $response["data"] = $full_data;
                    $response['ambiente_prueba'] = 'usando metodo test_response en ambiente de desarrollo';
                    $response['full_path_file'] = $full_path_file;
                    $response['monto_total'] = $monto_total;
    
                    $response['response']['respuesta'] = true;
                    $response['success'] = true;
    

                }else{

                    $response = $this->form_validation->error_array();
                }

            }

        }

        echo json_encode($response);
    }

    public function procesarRespuestaImputacionAutomatica()
    {
        header('Content-Type: text/html; charset=ISO-8859-1');
        set_time_limit(0);
        
        $rango_monto_imputacion             = $this->input->post('rango_monto_imputacion');
        $this->end_folder                   = 'uploads/bancolombia/debitoautomatico';
        $config['form']                     = "imputacionAutomaticaBancolombia";
        $this->config_file['upload_path']   = $this->get_end_folder();
        $this->config_file['allowed_types'] = 'txt';
        $this->config_file['overwrite']     = FALSE;
        $nombre_origen                      = $_FILES['fileImputacionAutomaticaBancolombia']['name'];
        $rutaToday                          = $this->get_end_folder() . $nombre_origen;
        $response                           = array_base();
        
        $response['response']['respuesta'] = false;
        $response['response']['errors'] = [];
        $response['response']['mensaje'] = "Tipo de archivo erroneo. Solo archivos REC con extension .txt y Convenio 010232.";

        if((substr($nombre_origen, 0, 3) == "REC") && (substr($nombre_origen, 17, 6) == "010232"))
        {

            $response['response']['respuesta']  = false;
            $response['response']['errors']     = [];
            $response['response']['mensaje']    = "Archivo: " . $nombre_origen . ", procesado anteriormente.";

            if(empty($this->fileExistInDB(str_replace(".txt", "",$nombre_origen))) && !file_exists($rutaToday)  )
            {

                if($this->_validate_save_input($config))
                {
    
                    //Consume metodo local de prueba.
                    $full_path_file = $this->upload->data()['full_path'];
                    $file_name = $this->upload->data()['file_name'];
    
                    $monto_total = 0;
                    $file = new SplFileObject($full_path_file);
    
                    $debitos_cabecera_id = 0;
                    $count = 0; 
                    $i = 0;
                    $full_data = [];
                    $data = [];
    
                    $file_ok1_name          = str_replace(".txt", "",$file_name) . "_OK1" . ".txt" ;
                    $file_ok                = $this->get_end_folder() . $file_ok1_name;
                    $file_ok1               = fopen($file_ok, "w") or die("Unable to open file!"); 

                    $file_ok2_name          = str_replace(".txt", "",$file_name) . "_B_OK1" . ".txt" ;
                    $file_ok2               = fopen($this->get_end_folder() . $file_ok2_name, "w") or die("Unable to open file!"); 
                    $file_bancolombia       = $this->get_end_folder() . $file_ok2_name;

                    $flag = 1;

                    foreach ($file as $k => $line) 
                    {

                        set_time_limit(0);

                        $line = utf8_decode($line);
                        $line = preg_replace('/[-?]/', 'X', $line);
                        $line = cleanString($line);
    
                        if(substr($line,0,1) == 1)
                        {
                            $monto_total = sprintf("%.2f", substr($line, 66, 17) / 100);
                            $headLine['ruta_back_txt']  = $full_path_file;
                            $headLine['nombre_archivo'] = str_replace(".txt", "",$file_name);
                            $headLine['fecha_recaudo']  = substr($line, 49, 8);
                            $headLine['origen_pago']    = "Bancolombia";
                            $title                      = substr($line, 0, 210); 
                            $debitos_cabecera_id = $this->debitosautomaticos->insert("solicitudes.debitos_cabecera_txt", $headLine);
                        } 
                        
                        if(substr($line,0,1) == 6)
                        {

                            // OK1 | OK2 | OK4
                            if (((substr($line, 171, 3)) === 'OK1') || ((substr($line, 171, 3)) === 'OK2') || ((substr($line, 171, 3)) === 'OK4'))
                            {
                                
                                $i++;
                                $count++;
    
                                $bodyLine['id_cabecera_txt']    = $debitos_cabecera_id;
                                $bodyLine['id_credito_detalle'] = (substr($line, 80,  30));
                                $bodyLine['monto']              = $monto_total;
                                $bodyLine['fecha_creado']       = (substr($line, 174, 8));
                                $bodyLine['monto']              = sprintf("%.2f", (substr($line, 62,  17)) / 100);
    
                                $data[$i]['nombre_completo_pagador']    = (substr($line, 14,  20));
                                $data[$i]['cuenta_pagador']             = (substr($line, 34,  9));
                                $data[$i]['monto']                      = sprintf("%.2f", (substr($line, 62,  17)) / 100);
                                $data[$i]['id_credito_detalle']         = rtrim((substr($line, 80,  30)));
                                $data[$i]['status']                     = (substr($line, 171, 3));
                                $data[$i]['fecha_recaudo']              = (substr($line, 174, 8)); 
    
                                $nombre_cliente             = str_pad(substr($line, 14,  20), 20, ' ', STR_PAD_RIGHT);
                                $monto                      = str_pad(substr($line, 62,  17), 17, 0, STR_PAD_LEFT);
                                $ref1_cliente               = str_pad(substr($line, 80,  30), 9, ' ', STR_PAD_RIGHT);
                                $ref2_cliente               = str_pad("", 30,   ' ', STR_PAD_RIGHT);
                                $final1                     = substr($line, 140,  44);
                                $final2                     = str_pad("", 3,   ' ', STR_PAD_RIGHT);
                                $final3                     = substr($line, 187,  23);
    
                                switch ($rango_monto_imputacion) 
                                {

                                    case 'si':
                                        if($flag == 1)
                                        {
                                            fwrite($file_ok2, $title . "\n");
                                            $flag++;
                                        }
                                        if ($monto < 3000000) 
                                        {
                                            $log  = " A " . PHP_EOL;
                                            file_put_contents('./application/logs/respuesta_process_da_bancolombia_'.date("j.n.Y").'.log', $log, FILE_APPEND);

                                            fwrite($file_ok2, "6" . '             ' . $nombre_cliente . "005600078" . "00000000000000000" . "67" . $monto . "S" . $ref1_cliente . $ref2_cliente . $final1 . $final2 . $final3 . "\n");
                                        }
                                        if ($monto >= 3000000) 
                                        {
                                            $log  = " B " . PHP_EOL;
                                            file_put_contents('./application/logs/respuesta_process_da_bancolombia_'.date("j.n.Y").'.log', $log, FILE_APPEND);
                                            
                                            fwrite($file_ok1, "6" . '             ' . $nombre_cliente . "005600078" . "00000000000000000" . "67" . $monto . "S" . $ref1_cliente . $ref2_cliente . $final1 . $final2 . $final3 . "\n");
                                        }
                                        break;
                                    
                                    case 'no':
                                        if ($monto < 3000000) 
                                        {
                                            $log  = " C " . PHP_EOL;
                                            file_put_contents('./application/logs/respuesta_process_da_bancolombia_'.date("j.n.Y").'.log', $log, FILE_APPEND);

                                            fwrite($file_ok1, "6" . '             ' . $nombre_cliente . "005600078" . "00000000000000000" . "67" . $monto . "S" . $ref1_cliente . $ref2_cliente . $final1 . $final2 . $final3 . "\n");
                                        }
                                        break;

                                    default:
                                        break;

                                }

                            }
    
                        }
    
                    }  
    
                    $log  = date("Y-m-d h:i:s") . " SALIDA FOREACH "  . PHP_EOL;
                    file_put_contents('./application/logs/respuesta_process_da_bancolombia_'.date("j.n.Y").'.log', $log, FILE_APPEND);
                    
                    fclose($file_ok1);
                    fclose($file_ok2);
                    
                    $dataUpdate['total_txt'] = $count;
                    $where['id'] = $debitos_cabecera_id;

                    if ($rango_monto_imputacion == "si")
                    {
                        $from           = 'hola@solventa.com';
                        $to             = 'alejo.abella@solventa.com.ar';
                        $from_name      = 'Solventa Colombia';
                        $subject        = 'Debito Automatico BANCOLOMBIA MONTO MENOR '   ;
                        $message        = 'BANCOLOMBIA MONTO MENOR Archivo' ;
                        $cc             = 'operaciones@solventa.com.ar';
                        $cco            = '';
    
                        $fileName       = FCPATH . $file_bancolombia;
                        $this->sendemailok($from, $to, $from_name, $subject, $message, $cc, $cco, $fileName);
                    }

                    $this->lectura_debito_automatico($file_ok, str_replace(".txt", "",$file_ok1_name ), 'DEBITO AUTOMATICO');

                    $detalle['archivo'] = str_replace(".txt", "",$file_name);
                    $detalle['total_txt'] = $count;
                    $detalle['origen_pago'] = "Bancolombia";
                    $detalle['fecha_subida'] = date("d-m-Y h:i:s");
                    $detalle['monto_total'] = $monto_total;
                    $detalle['full_path_file'] = $full_path_file;
                    $response['detalle'] = $detalle;
                    $full_data = array_merge($full_data, $data);
                    $response["data"] = $full_data;
                    $response['ambiente_prueba'] = 'usando metodo test_response en ambiente de desarrollo';
                    $response['full_path_file'] = $full_path_file;
                    $response['monto_total'] = $monto_total;
    
                    $response['response']['respuesta'] = true;
                    $response['success'] = true;
    
                }else{
    
                    $response = $this->form_validation->error_array();
                }
    
            }

        }

        //unlink($fileName);

        echo json_encode($response);
    }

    private function lectura_debito_automatico($full_path_file, $file_name, $medio_pago = 'DEBITO AUTOMATICO')
    {
        set_time_limit(0);
        //echo  $file_name;
        $endpoint = URL_MEDIOS_PAGOS_LOCAL . 'bancolombia/LecturaDebitoAutomatico/lectura_debito_automatico';   //Testing

        if(ENVIRONMENT == 'production')
        {
            $endpoint = URL_MEDIOS_PAGOS.'bancolombia/LecturaDebitoAutomatico/lectura_debito_automatico';   //Produccion
        }

        $body = array();

        if(file_exists($full_path_file))
        {
            $files = array();
            $files['file'] = $full_path_file;

            array_walk($files, function($filePath, $key) use(&$body) 
            {
                $body[$key] = curl_file_create($filePath);
            });

            $body['filename'] =$file_name;
            $body['medio_pago'] =$medio_pago;
        }


        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body)
        {
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_TIMEOUT, 3000);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $headers = array('Accept' => 'application/json');

        $response = Requests::post($endpoint, $headers, array(), array('hooks' => $hooks));

        $log  = "Method: upload_file_debito_automatico" . ' - ' . date("Y-m-d h:i:s") . PHP_EOL .
                        "RESPUESTA ENDPOINT: " . $response->body . PHP_EOL .
                        "-------------------------" . PHP_EOL; 

        return $response->body;
    }

    public function sendemailok($from=null, $to=null, $from_name=null, $subject=null, $message=null, $cc=null, $cco=null,$fileName=null)
    {
    	$filename2 = realpath($fileName);
	    $finfo = new \finfo(FILEINFO_MIME_TYPE);
	    $mimetype = $finfo->file($filename2);
	    $file = new CURLFILE($filename2, $mimetype);

	    if(file_exists($filename2))
        {

	        $data = array(
	            'from' => $from,
	            'to' => $to,
	            'from_name' => $from_name,
	            'subject' => $subject,
	            'message' => $message,
	            'cc' => $cc,
	            'cco' => $cco,
	            'file' => $file
	        );

	        $request_headers = ['Authorization:eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzE0MzEzNDQsImV4cCI6MTU3MTQzNDk0NCwiZGF0YSI6eyJpZCI6IjEiLCJhZG1pbiI6dHJ1ZSwidGltZSI6MTU3MTQzMTM0NCwidGltZVRvbGl2ZSI6bnVsbH19.yOaR-uR1qjjGS_Z6VbTyBKN_zs-Xxx5Y_Xt2_dMZEa0'];
	        
	        $ch = curl_init();
	        curl_setopt_array($ch, array(
	            CURLOPT_URL => URL_SEND_MAIL . 'api/sendmail',
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_ENCODING => '',
	            CURLOPT_MAXREDIRS => 10,
	            CURLOPT_TIMEOUT => 0,
	            CURLOPT_FOLLOWLOCATION => true,
	            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	            CURLOPT_CUSTOMREQUEST => 'POST',
	            CURLOPT_POSTFIELDS => $data,
	            CURLOPT_HTTPHEADER => $request_headers
	        ));
	        curl_exec($ch);
	        curl_close($ch);
	    }

    }

    private function fileExistInDB($fileName)
    {

        $where = [
            'nombre_archivo' => $fileName
        ];
        return $this->debitosautomaticos->select("solicitudes.debitos_cabecera_txt", $where);
    }

    public function procesarImputacionEfecty(){

        $post = $this->input->post();        

		$archivo = $this->imputacionCredito->get_archivo_efecty(["fileName" =>  $post['fileName']]);

		if(!empty($archivo)){
			$response['status']['ok'] = FALSE;
			$response['message'] = "El archivo ya fue procesado";
            echo json_encode($response);		
            die;
        }

		    $fichero_anio = dirname(BASEPATH) . '/public/imputaciones_efecty/' . date('Y');
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/imputaciones_efecty/' . date('Y') . '/' . date('m');
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700, true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700, true);
            }
            $fecha_creacion_archivo = date('Ymd_His');
            $nombre_archivo = $post['fileName'];

            $fileNameCmps = explode(".", $nombre_archivo);
            $fileExtension = strtolower(end($fileNameCmps));
            if ($fileExtension != 'xlsx') {
                $response['status']['ok'] = FALSE;
                $response['message'] = "Tipo de archivo incorrecto<br>Tipos de archivos admitidos: <b>xlsx</b>";
                echo json_encode($response);		
                die;
            }

            $config['upload_path'] = $ruta_guardar_archivo; 
            $config['file_name'] = $nombre_archivo;
            $config['allowed_types'] = 'xlsx';
            $config['overwrite'] = FALSE;
			
			$this->load->library('upload');
            $this->upload->initialize($config);
			
            if ($this->upload->do_upload('file')) {
				
				$file = $this->upload->data();
				$filename = $file['file_name'];
                
                $archivo_ruta = 'public/imputaciones_efecty/' . date('Y') . '/' . date('m') . '/' .$filename;
				
				
				
				$element['patch_imagen'] = $archivo_ruta;
				$element['extension'] = $config['allowed_types'];
				$element['is_image'] = $file['is_image'];
				$element['fecha_carga'] = date('Y-m-d H:i:s');
				
				$reader = new Xlsx();
				$reader->setReadDataOnly(true);
				$spreadsheet = $reader->load($element['patch_imagen']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                
				if(count($sheetData) > 0){
                    $registros = count($sheetData);
					$registros_procesados = 0; 
                    
                    for ($i=2 ; $i <= count($sheetData); $i++) { 
                        
                        set_time_limit(0);
						$registros = $registros+1;
						
                        $monto              = $sheetData[$i]['E'];      //VALOR_MOVILIZADO
                        $monto              = trim(str_replace('$','',str_replace(',','',$monto)));
						$referencia_externa = $sheetData[$i]['A'];      //NUMERO_ORDEN
						$referencia_interna = $sheetData[$i]['M'];      //NUMCUOTA

						$fecha_pago      = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheetData[$i]['G']));
					
						$detalle_credito    = $sheetData[$i]['M'];      // NUMCUOTA
						$id_detalle_credito = trim(explode('-', $detalle_credito)[0]);
						
                        //consultamos el credito detalle
                        $credito_detalle = $this->credito->get_creditos_cliente(['id_cuota'=>$id_detalle_credito]);

                        if (!empty($credito_detalle)) {
                            $data_registro_pago = [
                                'id_credito_detalle' => $id_detalle_credito,
                                'monto' => $monto,
                                'referencia_interna' => $referencia_interna,
                                'referencia_externa' => $referencia_externa,
                                'fecha_pago' => $fecha_pago,
                                'efecty_manual' => true
                            ];
                            $registro_pago = $this->registrar_pago($data_registro_pago);
                            
                            set_time_limit(0);

                            if(!empty($registro_pago->response->pago)){
                                $registro_pago = json_decode($registro_pago);
                                $respuesta = $registro_pago->response->respuesta;
                                $id_pago_credito = $registro_pago->response->pago;
                            }else{
                                $respuesta_array = explode("{", $registro_pago);
                                $respuesta_array = explode(",", $respuesta_array[2]);
                                $pago_array = $respuesta_array[0];
                                $pago = explode(":", $pago_array);
                                $pago = str_replace('"','',$pago[1]);
                                $respuesta = explode(":", $respuesta_array[2]);
                                $respuesta = str_replace('}}','',$respuesta);
                                $respuesta = $respuesta[1];
                                $id_pago_credito = $pago;
                            }


                            if($respuesta){
            
                                $data_imputar_pago = [
                                    'id_cliente' => $credito_detalle[0]['id_cliente'],
                                    'monto'      => $monto,
                                    'fecha_pago' => $fecha_pago,
                                    'medio_pago' => "efecty",
                                    'id_pago_credito' => $id_pago_credito
                                ];
                                $imputar_pago = $this->imputar_pago($data_imputar_pago);
                                set_time_limit(0);
                                
                                if(!empty($imputar_pago->response->respuesta)) {
                                    $imputar_pago = json_decode($imputar_pago);
                                    $respuesta_pago = $imputar_pago->response->respuesta;
                                }else{
                                    $response_array = explode("{", $imputar_pago);
                                    $response_array = explode(",", $response_array[2]);
                                    $response_array = explode(":", $response_array[1]);
                                    $respuesta_pago= $response_array; 
                                }

                                if($respuesta_pago){
                                    $registros_procesados = $registros_procesados +1;

                                   // trackeamos                                    
                                   $sol = $this->solicitud->getSolicitudesBy(['id_credito' => $credito_detalle[0]['id_credito']])[0]->id;
                                   $dataTrackGestion = [
                                       'id_solicitud'=>(int)$sol,
                                       'observaciones'=>'<b>[IMPUTACION MANUAL EFECTY]</b><br>Fecha Proceso: '.date('d-m-Y').
                                                        '<br>Monto: $'.number_format($monto,2, ',', '.').
                                                        '<br>Archivo: '. $nombre_archivo.
                                                        '<br>Respuesta: PAGO IMPUTADO', 
                                       'id_tipo_gestion' => 11,
                                       'id_operador' => $this->session->userdata("idoperador")
                                   ];
                                   $endPoint =  base_url('api/track_gestion');
                                   $response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);


                                }else{
                                   // trackeamos                                    
                                   $sol = $this->solicitud->getSolicitudesBy(['id_credito' => $credito_detalle[0]['id_credito']]);
                                   $dataTrackGestion = [
                                       'id_solicitud'=>(int)$sol,
                                       'observaciones'=>'<b>[IMPUTACION MANUAL EFECTY]</b><br>Fecha Proceso: '.date('d-m-Y').
                                                        '<br>Monto: $'.number_format($monto,2, ',', '.').
                                                        '<br>Archivo: '. $nombre_archivo.
                                                        '<br>Respuesta: PAGO NO IMPUTADO', 

                                       'id_tipo_gestion' => 11,
                                       'id_operador' => $this->session->userdata("idoperador")
                                   ];
                                   $endPoint =  base_url('api/track_gestion');
                                   $response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
                                }
                            }
                        }

                        
        

					}
                    
                    
					if($registros_procesados > 0){
                        /**
                         * insertamos el registro de archivo procesado si se proceso algun registro
						 */
                        $data = [
                            'url' => $archivo_ruta,
							'nombre_archivo' => $filename,
							'fecha_proceso' => date('Y-m-d H:i:s'),
							'id_operador' => $this->session->userdata("idoperador")
						];
						$insert = $this->imputacionCredito->insertarArchivoEfecty($data);
                        
                        $response['status']['ok'] = TRUE;
                        $response['message'] = "Archivo procesado.";
	
					} else{
						$response['status']['ok'] = FALSE;
						$response['message'] = "Ningun registro procesado";
					}

				} else{
					$response['status']['ok'] = FALSE;
					$response['message'] = "Archivo sin registros";
				}

				
            } else {
                $response['status']['ok'] = FALSE;
                $response['message'] = "no fue posible cargar el archivo";
            }
        

            echo json_encode($response);		
    }

    public function procesarImputacionPSE(){

    
            $post = $this->input->post();        
    
            //$archivo = $this->imputacionCredito->get_archivo_efecty(["fileName" =>  $post['fileName']]);
    
            /*if(!empty($archivo)){
                $response['status']['ok'] = FALSE;
                $response['message'] = "El archivo ya fue procesado";
                echo json_encode($response);		
                die;
            }*/

            
            $fichero_anio = dirname(BASEPATH) . '/public/imputaciones_efecty/' . date('Y');
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/imputaciones_efecty/' . date('Y') . '/' . date('m');
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700, true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700, true);
            }
            $fecha_creacion_archivo = date('Ymd_His');
            $nombre_archivo = $post['fileName'];

            $fileNameCmps = explode(".", $nombre_archivo);
            $fileExtension = strtolower(end($fileNameCmps));
            if ($fileExtension != 'xlsx') {
                $response['status']['ok'] = FALSE;
                $response['message'] = "Tipo de archivo incorrecto<br>Tipos de archivos admitidos: <b>xlsx</b>";
                echo json_encode($response);		
                die;
            }

            $config['upload_path'] = $ruta_guardar_archivo; 
            $config['file_name'] = $nombre_archivo;
            $config['allowed_types'] = 'xlsx';
            $config['overwrite'] = FALSE;
            
            $this->load->library('upload');
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('file')) {
                
                $file = $this->upload->data();
                $filename = $file['file_name'];
                $archivo_ruta = 'public/imputaciones_efecty/' . date('Y') . '/' . date('m') . '/' .$filename;
                
                
                
                $element['patch_imagen'] = $archivo_ruta;
                $element['extension'] = $config['allowed_types'];
                $element['is_image'] = $file['is_image'];
                $element['fecha_carga'] = date('Y-m-d H:i:s');
                
                $reader = new Xlsx();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($element['patch_imagen']);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                
                //var_dump("prueba 1");die;
                
                if(count($sheetData) > 0){
                    $registros = count($sheetData);
                    $registros_procesados = 0; 
                    
                    for ($i=3 ; $i <= count($sheetData); $i++) { 
                        set_time_limit(0);
                        $registros = $registros+1;
                        
                        $monto              = $sheetData[$i]['E'];      //VALOR_MOVILIZADO
                        $monto              = trim(str_replace('$','',str_replace(',','',$monto)));
                        $referencia_externa = $sheetData[$i]['C'];      //NUMERO_ORDEN
                        $referencia_interna = $sheetData[$i]['D'];      //NUMCUOTA
                        
                        $fecha_pago      = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheetData[$i]['B']));
                        
                        $detalle_credito    = $sheetData[$i]['D'];      // NUMCUOTA
                        if(trim(explode('-', $detalle_credito)[0]) == "A")
                        {
                            $id_acuerdo = trim(explode('-', $detalle_credito)[1]);
                            $aux = $this->credito->get_acuerdo_info(['estado_mora_null' => "1", 'id_acuerdo' =>$id_acuerdo, 'dir'=>'ASC']);
                            if (empty($aux)) {
                                $aux = $this->credito->get_acuerdo_info(['estado_cuota' => "pagado", 'id_acuerdo' =>$id_acuerdo, 'dir'=>'DESC']);
                            }
                            $id_detalle_credito = $aux[0]->id_cuota;
                            
                        } else{
                            $id_detalle_credito = trim(explode('-', $detalle_credito)[1]);
                            
                        }
                        
                            
                            //validar referencia externa
                            $pagos_referencia = $this->credito->get_pagos(['referencia_externa'=>$referencia_externa]);
                            
                            if(!empty($pagos_referencia))
                                continue;
                            
                            //consultamos el credito detalle
                            $credito_detalle = $this->credito->get_creditos_cliente(['id_cuota'=>$id_detalle_credito]);
                            
                            
                            if (!empty($credito_detalle)) {
                                $data_registro_pago = [
                                    'id_credito_detalle' => $id_detalle_credito,
                                    'monto' => $monto,
                                    'referencia_interna' => $referencia_interna,
                                    'referencia_externa' => $referencia_externa,
                                    'fecha_pago' => $fecha_pago,
                                    'PSE_manual' => true,
                                    'referencia' => "Cron epayco pse"
                                ];
                                
                                $registro_pago = $this->registrar_pago($data_registro_pago);
                                set_time_limit(0);
                                
                                if(!empty($registro_pago->response->pago)){
                                    $registro_pago = json_decode($registro_pago);
                                    $respuesta = $registro_pago->response->respuesta;
                                    $id_pago_credito = $registro_pago->response->pago;
                                }else{
                                    $respuesta_array = explode("{", $registro_pago);
                                        $respuesta_array = explode(",", $respuesta_array[2]);
                                        $pago_array = $respuesta_array[0];
                                        $pago = explode(":", $pago_array);
                                        $pago = str_replace('"','',$pago[1]);
                                        $respuesta = explode(":", $respuesta_array[2]);
                                        $respuesta = str_replace('}}','',$respuesta);
                                        $respuesta = $respuesta[1];
                                        $id_pago_credito = $pago;
                                    }
                                    
                                    
                                    if($respuesta){
                                        
                                        $data_imputar_pago = [
                                            'id_cliente' => $credito_detalle[0]['id_cliente'],
                                            'monto'      => $monto,
                                            'fecha_pago' => $fecha_pago,
                                            'medio_pago' => "epayco",
                                            'id_pago_credito' => $id_pago_credito
                                        ];
                                        $imputar_pago = $this->imputar_pago($data_imputar_pago);
                                        set_time_limit(0);
                                        
                                        if(!empty($imputar_pago->response->respuesta)) {
                                            $imputar_pago = json_decode($imputar_pago);
                                            $respuesta_pago = $imputar_pago->response->respuesta;
                                        }else{
                                            $response_array = explode("{", $imputar_pago);
                                                $response_array = explode(",", $response_array[2]);
                                                $response_array = explode(":", $response_array[1]);
                                                $respuesta_pago= $response_array; 
                                            }
                                            
                                            if($respuesta_pago){
                                                $registros_procesados = $registros_procesados +1;
                                                
                                                // trackeamos                                    
                                                $sol = $this->solicitud->getSolicitudesBy(['id_credito' => $credito_detalle[0]['id_credito']])[0]->id;
                                                $dataTrackGestion = [
                                                    'id_solicitud'=>(int)$sol,
                                                    'observaciones'=>'<b>[IMPUTACION MANUAL EPAYCO]</b><br>Fecha Proceso: '.date('d-m-Y').
                                                    '<br>Monto: $'.number_format($monto,2, ',', '.').
                                                    '<br>Archivo: '. $nombre_archivo.
                                                    '<br>Respuesta: PAGO IMPUTADO', 
                                                    'id_tipo_gestion' => 11,
                                                    'id_operador' => $this->session->userdata("idoperador")
                                                ];
                                                $endPoint =  base_url('api/track_gestion');
                                                $response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
                                                
                                                
                                    }else{
                                        // trackeamos                                    
                                        $sol = $this->solicitud->getSolicitudesBy(['id_credito' => $credito_detalle[0]['id_credito']]);
                                        $dataTrackGestion = [
                                            'id_solicitud'=>(int)$sol,
                                            'observaciones'=>'<b>[IMPUTACION MANUAL EPAYCO]</b><br>Fecha Proceso: '.date('d-m-Y').
                                            '<br>Monto: $'.number_format($monto,2, ',', '.').
                                            '<br>Archivo: '. $nombre_archivo.
                                            '<br>Respuesta: PAGO NO IMPUTADO', 
                                            
                                            'id_tipo_gestion' => 11,
                                            'id_operador' => $this->session->userdata("idoperador")
                                        ];
                                        $endPoint =  base_url('api/track_gestion');
                                        $response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
                                    }
                                }
                            }
                        }
                        
                        //var_dump(trim(explode('-', $detalle_credito)[1]));die;
                        
                        if($registros_procesados > 0){
                            /**
                             * insertamos el registro de archivo procesado si se proceso algun registro
                             */
                            $data = [
                                'url' => $archivo_ruta,
                                'nombre_archivo' => $filename,
                                'fecha_proceso' => date('Y-m-d H:i:s'),
                                'id_operador' => $this->session->userdata("idoperador")
                            ];
                            //$insert = $this->imputacionCredito->insertarArchivoEfecty($data);
                            
                            $response['status']['ok'] = TRUE;
                            $response['message'] = "Archivo procesado.";
                            
                        } else{
                            $response['status']['ok'] = FALSE;
                            $response['message'] = "Ningun registro procesado";
                        }
                        
                    } else{
                        $response['status']['ok'] = FALSE;
                        $response['message'] = "Archivo sin registros";
                    }
                    
                    
                } else {
                    $response['status']['ok'] = FALSE;
                    $response['message'] = "no fue posible cargar el archivo";
                }
                
                echo json_encode($response);		
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
}

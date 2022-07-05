<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';

use Restserver\Libraries\REST_Controller;

class ApiGastos extends REST_Controller {

    // protected $end_folder = 'assets/gastos';
    protected $end_folder = 'public/gastos';

    public function __construct($config = 'rest') {
        parent::__construct();
        $this->load->model('operaciones/Gastos_model', 'operaciones', TRUE);
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('upload');
    }

    public function tablaGastos_post() {
        $data = $this->operaciones->get_gastos();
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);

    }
    
    public function tablaGastosPendientes_post() {

        $data = $this->operaciones->get_gastos_pendientes();
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);
    }
    public function tablaGastosSearch_post() {
        $search_gasto_param=$this->input->post('search');
        $gastos = $this->operaciones->get_gastos_search( $search_gasto_param);
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'gastos' => $gastos];
        $this->response($response, $status);
}    
    public function tablaEstados_post() {
        $id_gasto = $this->input->post('id_gasto');
        $data = $this->operaciones->get_estados_gasto($id_gasto);
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);
    }  
   
     public function comprobanteProcesarGasto_post() {
        $id_gasto= $this->post('id_gasto');
        // var_dump($id_gasto);die();
        $fichero_anio = dirname(BASEPATH) . '/public/gastos/' . date('Y');
        $ruta_guardar_archivo = dirname(BASEPATH) . '/public/gastos/' . date('Y') . '/' . date('m');

        if (!file_exists($fichero_anio)) {
            mkdir($fichero_anio, 0700,true);
        }
        if (!file_exists($ruta_guardar_archivo)) {
            mkdir($ruta_guardar_archivo, 0700,true);
        }

        $fecha_creacion_archivo = date('Ymd_Hi');
        $nombre_archivo = $fecha_creacion_archivo . '_ComprobantePago';
        $config['upload_path'] = $this->get_end_folder();
        $config['file_name'] = $nombre_archivo;
        $config['allowed_types'] = 'jpg|png|jpeg|pdf';
        $config['overwrite'] = TRUE;
        $config['max_size'] = "320000";

        $this->load->library('upload');
        $this->upload->initialize($config);
        if ($this->upload->do_upload('file'))
        {
            $file = $this->upload->data();
            $file['uri'] = base_url($config['upload_path'].$file['file_name']);
            $data  = array("upload_data" => $this->upload->data());
            $archivo_ruta = 'public/gastos/' . date('Y') . '/' . date('m') . '/' .$nombre_archivo.$data['upload_data']['file_ext'];
            $this->operaciones->actualizarComprobantePagoGastos($id_gasto, $archivo_ruta);
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],'message' => "Comprobante pago guardado"];
        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->upload->display_errors();
        }
    
        $this->response($response,$status);

    }

    public function registroGastos_post() {
            // var_dump($this->session->userdata['id_usuario']);die;
            $fecha_creacion = date('Y-m-d H:i:s');
            $exen = $this->input->post('exento');
            $exento = str_replace(",", ".", str_replace(".", "", $exen));

            $sub = $this->input->post('sub_total');
            $sub_total = str_replace(",", ".", str_replace(".", "", $sub));

            $des = $this->input->post('descuento');
            $descuento = str_replace(",", ".", str_replace(".", "", $des));

            $imp = $this->input->post('impuesto');
            $impuesto = str_replace(",", ".", str_replace(".", "", $imp));

            $refuen = $this->input->post('retefuente');
            $retefuente = str_replace(",", ".", str_replace(".", "", $refuen));

            $rete = $this->input->post('reteica');
            $reteica = str_replace(",", ".", str_replace(".", "", $rete));

            $impc = $this->input->post('impuesto_consumo');
            $impuesto_consumo = str_replace(",", ".", str_replace(".", "", $impc));

            $total = $this->input->post('total_pagar');
            $total_pagar = str_replace(",", ".", str_replace(".", "", $total));            
            
            $fichero_anio = dirname(BASEPATH) . '/public/gastos/' . date('Y');
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/gastos/' . date('Y') . '/' . date('m');
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700,true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700,true);
            }
            $fecha_creacion_archivo = date('Ymd_Hi');
            $nombre_archivo = $fecha_creacion_archivo . '_Comprobante';
                
            $config['upload_path'] = $this->get_end_folder();
            $config['allowed_types'] = 'jpg|png|jpeg|pdf';
            $config['overwrite'] = FALSE;
            $config['file_name'] = $nombre_archivo;
            $this->upload->initialize($config);

            $this->load->library('upload', $config);

            $subir = $this->upload->do_upload('file');
            
            $data  = array("upload_data" => $this->upload->data());
            $archivo_ruta = 'public/gastos/' . date('Y') . '/' . date('m') . '/' .$nombre_archivo.$data['upload_data']['file_ext'];
            $fecha_emision =  date('Y-m-d H:i:s', strtotime($this->input->post('fecha_factura')));
            $fecha_vencimiento =  date('Y-m-d H:i:s', strtotime($this->input->post('fecha_vencimiento_factura')));
            $nro_factura=$this->input->post('nro_factura');
            $concepto=$this->input->post('concepto');
            $parametros = array(
                'id_tipo' => $this->input->post('id_tipo'),
                'id_tipo_moneda' => $this->input->post('id_tipo_moneda'),
                'id_empresa' => $this->input->post('id_empresa'),                
                'id_clase' => $this->input->post('clase_gasto'),
                'id_descripcion' => $this->input->post('descripcion'),
                'id_beneficiario' => $this->input->post('id_beneficiario'),
                'concepto' => $this->input->post('concepto'),
                'nro_factura' => $this->input->post('nro_factura'),
                'fecha_emision' => $fecha_emision,
                'fecha_vencimiento' => $fecha_vencimiento,
                'id_forma_pago' => $this->input->post('forma_pago'),
                'exento' => $exento,
                'sub_total' => $sub_total,
                'descuento' => $descuento,
                'impuesto' => $impuesto,
                'impuesto_consumo'=> $impuesto_consumo,
                'retefuente' => $retefuente,
                'reteica' => $reteica,
                'total_pagar' => $total_pagar,
                'url_archivo' => $archivo_ruta,
                'usuario_creador' => $this->session->userdata['id_usuario'],
                'fecha_creacion' => $fecha_creacion,
                'usuario_ultima_moficacion' => $this->session->userdata['id_usuario'],
                'fecha_ultima_modificacion' => $fecha_creacion,
                'estado' => 1,
            );
            $insertar_gasto = false;
            $validation_gasto = $this->operaciones->validation_gasto($nro_factura,$concepto);
            if(empty($validation_gasto)){
                if ($subir) {
                    $insertar_gasto = $this->operaciones->registroGastos($parametros);
                    // $parametros['id_gasto'] = $insertar_gasto;
                    $data=array(
                    'observaciones'=>'[Registro Gasto] <br> Se Registro el gasto con id: '.$insertar_gasto,
                    'id_operador' => $this->session->userdata['idoperador'],
                    'fecha' => date('Y-m-d'),
                    'hora'=> date('H:i:s'),
                    'tipo_gestion'=> '161',
                    'id_gasto'=>$insertar_gasto
                    );
                    $insertar_estado_gasto = $this->operaciones->trackGasto($data);
                }            
                if ($insertar_gasto && $insertar_estado_gasto) {
                    $status = parent::HTTP_OK;
                    $response = ['status' => ['code' => $status, 'ok' => TRUE],
                        'message' => "Gasto guardado"
                    ];
                } else {
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar el gasto"];
                }
            }else{
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => TRUE],'message' => "Ya existe Gasto"];
            }
            $this->response($response, $status);
    }

    public function actualizarGasto_post() {
            $fecha_ultima_modificacion = date('Y-m-d H:i:s');
            
            $exento = $this->input->post('exento');

            $sub_total = $this->input->post('sub_total');

            $descuento = $this->input->post('descuento');

            $impuesto = $this->input->post('impuesto');

            $impuesto_consumo=$this->input->post('impuesto_consumo');
            
            $reteica = $this->input->post('reteica');
            
            $retefuente = $this->input->post('retefuente');

            $total_pagar = $this->input->post('total_pagar');           
            
            $fichero_anio = dirname(BASEPATH) . '/public/gastos/' . date('Y');
            
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/gastos/' . date('Y') . '/' . date('m');
            
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700,true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700,true);
            }
            $fecha_ultima_modificacion_archivo = date('Ymd_Hi');
            $nombre_archivo = $fecha_ultima_modificacion_archivo . '_Comprobante';
            $config['upload_path'] = $this->get_end_folder();
            $config['allowed_types'] = 'jpg|png|jpeg|pdf';
            $config['overwrite'] = FALSE;
            $config['file_name'] = $nombre_archivo;
            $this->upload->initialize($config);
            $this->load->library('upload', $config);
            $subir = $this->upload->do_upload('file');
            $data  = array("upload_data" => $this->upload->data());
            $nombre_viejo = $this->input->post('url_archivo');
            $ficheroArchivo = dirname(BASEPATH).$nombre_viejo;
            if ($subir){
                if (file_exists($nombre_viejo)) {
                    unlink($nombre_viejo);
                }   
                $archivo_ruta = 'public/gastos/' . date('Y') . '/' . date('m') . '/' .$nombre_archivo.$data['upload_data']['file_ext'];
            } else {
                $archivo_ruta = $nombre_viejo;
            }
            $fecha_emision =  date('Y-m-d H:i:s', strtotime($this->input->post('fecha_factura')));
            $fecha_vencimiento =  date('Y-m-d H:i:s', strtotime($this->input->post('fecha_vencimiento_factura')));
            $parametros_new = array(
                'id_tipo' => $this->input->post('id_tipo'),
                'id_empresa' => $this->input->post('id_empresa'),
                'id_tipo_moneda' => $this->input->post('id_tipo_moneda'),
                'id_gasto' => $this->input->post('id_gasto'),
                'id_clase' => $this->input->post('clase_gasto'),
                'id_descripcion' => $this->input->post('descripcion'),
                'id_beneficiario' => $this->input->post('id_beneficiario'),
                'concepto' => $this->input->post('concepto'),
                'nro_factura' => $this->input->post('nro_factura'),
                'fecha_emision' => $fecha_emision,
                'fecha_vencimiento' => $fecha_vencimiento,
                'id_forma_pago' => $this->input->post('forma_pago'),
                'exento' => $exento,
                'sub_total' => $sub_total,
                'descuento' => $descuento,
                'impuesto' => $impuesto,
                'impuesto_consumo' => $impuesto_consumo,
                'retefuente' => $retefuente,
                'reteica' => $reteica,
                'total_pagar' => $total_pagar,
                'url_archivo' => $archivo_ruta,
                'usuario_ultima_moficacion' => $this->session->userdata['id_usuario'],
                'fecha_ultima_modificacion' => $fecha_ultima_modificacion
            );
            // $parametros_old = $this->operaciones->cargarGastoComparacion($this->input->post('id_gasto')) ;
            // $observaciones="";
            // foreach ($parametros_new as $key => $value){
            //     foreach ($parametros_old[0] as $key_old=> $value_old){
            //         if($key_old == $key && $value_old != $value){
            //         $observaciones = $observaciones . $key_old.':'.$value_old. '->'.$value. " ";
            //         }
            //     }
            // }
            $parametro_update= array(
            'observaciones'=> "[Actualización de Datos]<br>",
            'id_operador' => $this->session->userdata['idoperador'],
            'fecha' => date('Y-m-d'),
            'hora'=> date('H:i:s'),
            'tipo_gestion'=> '162',
            'id_gasto'=>$this->input->post('id_gasto')
            );
            $actualizar_gasto = $this->operaciones->actualizarGasto($parametros_new);   
            $actualizar_track = $this->operaciones->trackGasto($parametro_update);   
            if ($actualizar_gasto) {
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => TRUE],'message' => "Gasto actualizado"];
            } else {
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al actualizar gasto"];
            }
        $this->response($response, $status);
    }
   
    public function actualizaEstadoGasto_post() {
        // var_dump($this->input->post('id_estado'));die;
        $id_estado=$this->input->post('id_estado');
        $fecha_modificacion = date('Y-m-d H:i:s');
        $id_gasto = $this->input->post('id_gasto');
        $consulta_estado_old= $this->operaciones->get_gastos($id_gasto);
        $letra_estado_old=$consulta_estado_old[0]['descripcion_estado'];
        $id_estado_new= $this->input->post('id_estado');

        $parametros = array(
            'estado' => $id_estado,
            'usuario_ultima_moficacion' =>$this->session->userdata['id_usuario'],
            'fecha_ultima_modificacion' => $fecha_modificacion,
            'id_gasto' => $id_gasto,
        );
        $actualizar_gasto = $this->operaciones->actualizaEstadoGasto($parametros);
        $consulta_estado_actual = $this->operaciones->get_tipo_gasto($id_estado);
         $letra_estado=$consulta_estado_actual[0]->denominacion;
        // var_dump($letra_estado);die;
        $data=array(
        'observaciones'=> "[Actualización de Estado]<br>Id: ".$id_gasto. "<br>Cambio estado de gasto ".$letra_estado_old." -> ".$letra_estado,
        'fecha' => date('Y-m-d'),
        'hora'=> date('H:i:s'),
        'id_operador'=>$this->session->userdata['id_usuario'],
        'tipo_gestion'=>'163',
        'id_gasto'=>$id_gasto
        );

        //Crear registro en gastos trackeo con el nuevo estado
        $gastos_trackeo = $this->operaciones->trackGasto($data);    
        if ($actualizar_gasto && $gastos_trackeo) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Se Actualizo el Estado"
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Fallo"];
        }
        $this->response($response, $status);
    }    
  
   
    public function cargarGasto_post($flag=FALSE) {
        $id_gasto          = $this->input->post('id_gasto');        
        $vista             = $this->input->post('vista');
        $id_empresa        = $this->operaciones->get_empresa();
        $tipo_gasto        = $this->operaciones->get_tipo_gasto();
        $clase_gasto       = $this->operaciones->get_clase_gasto();
        $descripcion_gasto = $this->operaciones->get_descripcion_gasto();
        $beneficiarios     = $this->operaciones->get_beneficiarios();
        $forma_pago        = $this->operaciones->get_forma_pago();
        $moneda            = $this->operaciones->get_moneda();
        $cargar_gasto      = $this->operaciones->get_gastos($id_gasto);
        $beneficiarios_edit= $this->operaciones->get_beneficiarios($cargar_gasto[0]['id_beneficiario']);
        $tipo_documento    = $this->operaciones->get_documento($beneficiarios_edit[0]['id_tipo_documento']);
        $tipo_moneda       = $this->operaciones->get_tipo_moneda($beneficiarios_edit[0]['id_tipo_moneda']);
        $parametros = array('tipo_documento' => $tipo_documento,'tipo_moneda' => $tipo_moneda,'id_gasto' => $id_gasto,'id_empresa' => $id_empresa,'moneda' => $moneda, 'vista' => $vista, 'tipo_gasto' => $tipo_gasto, 'clase_gasto' => $clase_gasto, 'descripcion_gasto' => $descripcion_gasto, 'beneficiarios' => $beneficiarios, 'forma_pago' => $forma_pago, 'cargar_gasto' => $cargar_gasto);
        $this->load->view('operaciones/editarGasto', ['data' => $parametros]);
    }
    
    public function cargarGastoPendiente_post() {
        $id_gasto          = $this->input->post('id_gasto');        
        $vista             = $this->input->post('vista');
        $id_empresa        = $this->operaciones->get_empresa();
        $tipo_gasto        = $this->operaciones->get_tipo_gasto();
        $clase_gasto       = $this->operaciones->get_clase_gasto();
        $descripcion_gasto = $this->operaciones->get_descripcion_gasto();
        $beneficiarios     = $this->operaciones->get_beneficiarios();
        $forma_pago        = $this->operaciones->get_forma_pago();
        $moneda            = $this->operaciones->get_moneda();
        $cargar_gasto      = $this->operaciones->get_gastos($id_gasto);
        $beneficiarios_edit= $this->operaciones->get_beneficiarios($cargar_gasto[0]['id_beneficiario']);
        $tipo_documento    = $this->operaciones->get_documento($beneficiarios_edit[0]['id_tipo_documento']);
        $tipo_moneda       = $this->operaciones->get_tipo_moneda($beneficiarios_edit[0]['id_tipo_moneda']);
        $parametros = array('tipo_documento' => $tipo_documento,'tipo_moneda' => $tipo_moneda,'id_gasto' => $id_gasto,'id_empresa' => $id_empresa,'moneda' => $moneda, 'vista' => $vista, 'tipo_gasto' => $tipo_gasto, 'clase_gasto' => $clase_gasto, 'descripcion_gasto' => $descripcion_gasto, 'beneficiarios' => $beneficiarios, 'forma_pago' => $forma_pago, 'cargar_gasto' => $cargar_gasto);
        $this->load->view('administracion/editarGastoPendiente', ['data' => $parametros]);
    }

    public function get_end_folder() {
        $end_folder = $this->end_folder . '/' . date('Y') . '/' . date('m') . '/';
        if (!file_exists($end_folder)) {
            if (!mkdir($end_folder, 0777, true)) {
                $this->response['status']['ok'] = FALSE;
                $this->add_errors('No fué posible crear el directorio en .' . $end_folder);
                $this->response['errors'] = 'No fué posible crear el directorio en .' . $end_folder;
                return FALSE;
            }
        }
        return $end_folder;
    }

    public function guardarTipoGasto_post() {
        $parametros = array(
            'denominacion' => $this->input->post('denominacion'),
            'estado' => 1
        );
        $insertar_tipo_gasto = $this->operaciones->guardarTipoGasto($parametros);
        if ($insertar_tipo_gasto) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Tipo Gasto Guardado",
                'id' => $insertar_tipo_gasto
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar el tipo de gasto"];
        }
        $this->response($response, $status);
    }

    public function guardarClaseGasto_post() {
        $parametros = array(
            'denominacion' => $this->input->post('denominacion'),
            'estado' => 1
        );
        $insertar_clase_gasto = $this->operaciones->guardarClaseGasto($parametros);
        if ($insertar_clase_gasto) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Clase Gasto Guardada",
                'id' => $insertar_clase_gasto
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar la clase de gasto"];
        }
        $this->response($response, $status);
    }

    public function guardarDescripcionGasto_post() {
        $parametros = array(
            'denominacion' => $this->input->post('denominacion'),
            'id_clase_gasto' => $this->input->post('id_clase_gasto'),
            'estado' => 1
        );
        $insertar_descripcion_gasto = $this->operaciones->guardarDescripcionGasto($parametros);
        if ($insertar_descripcion_gasto) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Descripcion Gasto Guardada",
                'id' => $insertar_descripcion_gasto
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar la descripcion de gasto"];
        }
        $this->response($response, $status);
    }
    
    public function detalleBeneficiario_post() {
        $parametros = array(
            'id_beneficiario' => $this->input->post('id_beneficiario')
        );
        
        $datos = $this->operaciones->detalleBeneficiario($parametros);
        echo json_encode($datos);
    }
    
    public function verificarFactura_post() {
        $id_beneficiario = $this->input->post('id_beneficiario');
        $parametros = array(
            'nro_factura'     => $this->input->post('nro_factura'),
            'id_beneficiario' => $id_beneficiario
        );
        $datos = $this->operaciones->existeFactura($parametros);
        if (empty($id_beneficiario)){
             $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Debe cargar el beneficiario antes."];
        } else {
            if(empty($datos))
            {
                $status = parent::HTTP_OK;
                $response = ['status' =>['code' => $status, 'ok' => TRUE], 'data' => $datos];
            }else{
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Ya existe un beneficiario con este número de factura."];
            }
        }
        $this->response($response, $status);
    }
    
    public function rellenarDescripcion_post() {
        $parametros = array(
            'id_clase' => $this->input->post('id_clase'),
        );
        $datos = $this->operaciones->completarDescripcion($parametros);
        echo json_encode($datos);
    }
   
     public function tableProcesarGasto_post(){
        $data = $this->operaciones->get_gastos_pendientes_aprobados();
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);
    }
    /********************************************************************/
    /*** Se Obtiene la cantidad de Desembolsos pendientes por validar ***/
    /********************************************************************/
    public function getCantValidarPendientes_get() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Solicitud_m','solicitud',TRUE);
            $data = $this->solicitud->getCantDesembolsos();
            if ($data){
                $response['status']['ok']  = TRUE;
                $response['cantPendiente'] = $data[0]['cantidad'];
            }else{
                $response['status']['ok'] = FALSE;
                $response['message'] = "Error al consultar la Cantidad";
            }

            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;

            $this->response($response, $status);
        }else{
            show_404();
        }
    }    
    /*******************************************/
    /*** Se actualiza el desembolso validado ***/
    /*******************************************/
    public function actualizaDesembolsoValidado_post($id_desembolso) {
        if ($this->input->is_ajax_request()) {
            $respuesta = $this->input->post('respuesta');

            $data = array(
                'respuesta'            => $respuesta,
                'fecha_hora_respuesta' => date("Y-m-d H:i:s")
            );
            $respuesta = explode("-", $respuesta);
            if($respuesta[0] == 'RECHAZADA'){
                $dato = array('pagado' => 2);
                $id_solicitud = $this->input->post('id_solicitud');
                $pagado = $this->operaciones->consulta_pagado_solicitud_txt($id_solicitud);
                if($pagado[0]['pagado'] == 2){
                    $actualizo = 1;
                }else{
                    $actualizo = $this->operaciones->actualizarsolicitud_txt($id_solicitud, $dato);
                }
            }
            $actualizo = $this->operaciones->setActualizarValidacion($id_desembolso, $data);
            if ($actualizo > 0 && $actualizo > 0){
                $response['status']['ok'] = TRUE;
                $response['fecha'] = date("d-m-Y H:i:s");
                $response['id_operador'] = $this->session->userdata['idoperador'];
            }else{
                $response['status']['ok'] = FALSE;
                $response['message'] = "Error al actualizar validación";
            }

            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;

            $this->response($response, $status);
        }else{
            show_404();
        }
    }    
    /********************************************************************************/
    /*** Se guarda y se actualiza la ruta del comprobante del desembolso validado ***/
    /********************************************************************************/
    public function uploadComprobanteValidado_post($id_desembolso) {
        if ($this->input->is_ajax_request()) {

            $fichero_anio = dirname(BASEPATH) . '/public/tesoreria/comprobantes/' . date('Y');
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/tesoreria/comprobantes/' . date('Y') . '/' . date('m');
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700, true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700, true);
            }
            $fecha_creacion_archivo = date('Ymd_Hi');
            $nombre_archivo = $fecha_creacion_archivo . '_ComprobantePago';
            $config['upload_path'] = $ruta_guardar_archivo; //$this->get_end_folder();
            $config['file_name'] = $nombre_archivo;
            $config['allowed_types'] = 'jpg|png|jpeg|pdf';
            $config['overwrite'] = TRUE;
            $config['max_size'] = "320000";
            $this->load->library('upload');
            $this->upload->initialize($config);
            if ($this->upload->do_upload('file')) {
                $file = $this->upload->data();
                $file['uri'] = base_url($config['upload_path'].$file['file_name']);
                $data  = array("upload_data" => $this->upload->data());
                $archivo_ruta = 'public/tesoreria/comprobantes/' . date('Y') . '/' . date('m') . '/' .$nombre_archivo.$data['upload_data']['file_ext'];
                $status = parent::HTTP_OK;
                $response = [
                    'status'  => ['code' => $status, 'ok' => TRUE],
                    'message' => "Comprobante pago guardado",
                    'comprobante' => $nombre_archivo

                ];
                $datos = array(
                    'comprobante' => $archivo_ruta
                );
                $actualizo = $this->operaciones->setActualizarValidacion($id_desembolso, $datos);
                if ($actualizo > 0){
                    $response['status']['ok'] = TRUE;
                }else{
                    $response['status']['ok'] = FALSE;
                    $response['message'] = "Error al actualizar ruta del archivo";
                }
            } else {
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->upload->display_errors();
            }

            return $this->response($response, $status);
        }else{
            show_404();
        }
    }
    /***************************************************************/
    /*** Se busca el desembolso a validar por el N° de Solicitud ***/
    /***************************************************************/
    public function getSolicitudSearch_get($id_solicitud) {
        if ($this->input->is_ajax_request()) {
            $data = $this->operaciones->getDesembolso($id_solicitud);
            $response['status']['ok']  = TRUE;
            $response['data'] = $data;

            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;

            $this->response($response, $status);
        }else{
            show_404();
        }
    }
    /************************************************************************/
    /*** Se Obtiene la cantidad de Solicitudes de Imputaciones pendientes ***/
    /************************************************************************/
    public function getCantImputadasPendientes_get() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Solicitud_m','solicitud',TRUE);
            $data = $this->solicitud->getCantImputacionesPendientes();
            if ($data){
                $response['status']['ok']  = TRUE;
                $response['cantPendiente'] = $data[0]['cantidad'];
            }else{
                $response['status']['ok'] = FALSE;
                $response['message'] = "Error al consultar la Cantidad";
            }

            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;

            $this->response($response, $status);
        }else{
            show_404();
        }
    }    
}

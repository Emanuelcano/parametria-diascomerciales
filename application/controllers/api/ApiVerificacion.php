<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';

use Restserver\Libraries\REST_Controller;

class ApiVerificacion extends REST_Controller {

    protected $end_folder = 'assets/gastos';

    public function __construct($config = 'rest') {
        parent::__construct();
        $this->load->model('solicitudes/SolicitudVerificacion_model', 'verificacion', TRUE);
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('upload');
    }
    
    public function guardarReferenciaFamiliar_post() {
        $id_solicitud = $this->input->post('id_solicitud');
        $id_tipo_verificacion = 2;
        $parametros = array(
            'id_solicitud'         => $id_solicitud,
            'id_tipo_verificacion' => $id_tipo_verificacion,
            'pregunta1'            => $this->input->post('nombre_familiar'),
            'pregunta2'            => $this->input->post('empresa_familiar'),
            'pregunta3'            => $this->input->post('domicilio_familiar'),
            'pregunta4'            => $this->input->post('barrio_familiar'),
            'pregunta5'            => $this->input->post('tipo_trabajo_familiar'),
            'pregunta6'            => "",
            'usuario_creador'      => $this->session->userdata['id_usuario'],
            'fecha_creacion'       => date('Y-m-d H:i:s')
        );
        
        
        $cantidad = $this->verificacion->cantidadGuardada($id_solicitud,$id_tipo_verificacion);
        if ($cantidad[0]['cantidad'] <=3){            
            $insertar_referencia_familiar = $this->verificacion->guardarReferencia($parametros);
        } else {
            $insertar_referencia_familiar = false;
        }       
        if ($insertar_referencia_familiar) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Referencia Familiar Guardada",
                'id' => $insertar_referencia_familiar,
                'data' => $parametros,
                'cantidad' => $cantidad[0]
            ];
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar referencia familiar"];
        }
        $this->response($response, $status);
    }
    
    public function guardarReferenciaTitular_post() {
        $id_solicitud = $this->input->post('id_solicitud');
        $id_tipo_verificacion = 1;
        $parametros = array(
            'id_solicitud'         => $id_solicitud,
            'id_tipo_verificacion' => $id_tipo_verificacion,
            'pregunta1'            => $this->input->post('empresa'),
            'pregunta2'            => $this->input->post('domicilio'),
            'pregunta3'            => $this->input->post('barrio'),
            'pregunta4'            => $this->input->post('tipo_trabajo'),
            'pregunta5'            => $this->input->post('signo'),
            'pregunta6'            => "",
            'usuario_creador'      => $this->session->userdata['id_usuario'],
            'fecha_creacion'       => date('Y-m-d H:i:s')
        );
        
        $cantidad = $this->verificacion->cantidadGuardada($id_solicitud,$id_tipo_verificacion);
        if ($cantidad[0]['cantidad'] <=1){            
            $insertar_referencia_titular = $this->verificacion->guardarReferencia($parametros);
        } else {
            $insertar_referencia_titular = false;
        }      
        if ($insertar_referencia_titular) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Referencia Titular Guardada",
                'id' => $insertar_referencia_titular,
                'data' => $parametros,
                'cantidad' => $cantidad[0]
            ];
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar referencia titular"];
        }
        $this->response($response, $status);
    }    
    
    public function guardarReferenciaPersonal_post() {
        $id_solicitud = $this->input->post('id_solicitud');
        $id_tipo_verificacion = 3;
        $parametros = array(
            'id_solicitud'         => $id_solicitud,
            'id_tipo_verificacion' => $id_tipo_verificacion,
            'pregunta1'            => $this->input->post('nombre_personal'),
            'pregunta2'            => $this->input->post('empresa_personal'),
            'pregunta3'            => $this->input->post('domicilio_personal'),
            'pregunta4'            => $this->input->post('barrio_personal'),
            'pregunta5'            => $this->input->post('tipo_trabajo_personal'),
            'pregunta6'            => "",
            'usuario_creador'      => $this->session->userdata['id_usuario'],
            'fecha_creacion'       => date('Y-m-d H:i:s')
        );
        
        $cantidad = $this->verificacion->cantidadGuardada($id_solicitud,$id_tipo_verificacion);
         if ($cantidad[0]['cantidad'] <=3){            
            $insertar_referencia_personal = $this->verificacion->guardarReferencia($parametros);
        } else {
            $insertar_referencia_personal = false;
        }       
        if ($insertar_referencia_personal) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Referencia Personal Guardada",
                'id' => $insertar_referencia_personal,
                'data' => $parametros,
                'cantidad' => $cantidad[0]
            ];
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar referencia personal"];
        }
        $this->response($response, $status);
    }
    
    public function guardarReferenciaLaboral_post() {
        $id_solicitud = $this->input->post('id_solicitud');
        $id_tipo_verificacion = 4;
        $parametros = array(
            'id_solicitud'         => $id_solicitud,
            'id_tipo_verificacion' => $id_tipo_verificacion,
            'pregunta1'            => $this->input->post('empresa_laboral'),
            'pregunta2'            => $this->input->post('tipo_trabajo_laboral'),
            'pregunta3'            => $this->input->post('puesto'),
            'pregunta4'            => $this->input->post('caja'),
            'pregunta5'            => "",
            'pregunta6'            => "",
            'usuario_creador'      => $this->session->userdata['id_usuario'],
            'fecha_creacion'       => date('Y-m-d H:i:s')
        );
        $cantidad = $this->verificacion->cantidadGuardada($id_solicitud,$id_tipo_verificacion);
        if ($cantidad[0]['cantidad'] <=3){            
            $insertar_referencia_laboral = $this->verificacion->guardarReferencia($parametros);
        } else {
            $insertar_referencia_laboral = false;
        }       
        
        if ($insertar_referencia_laboral) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Referencia Laboral Guardada",
                'id' => $insertar_referencia_laboral,
                'data' => $parametros,
                'cantidad' => $cantidad[0]
            ];
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                'errors' => "Falló al guardar referencia laboral"];
        }
        $this->response($response, $status);
    }
    
    public function guardarReferenciaTitularInd_post() {
        $id_solicitud = $this->input->post('id_solicitud');
        $id_tipo_verificacion = 5;
          $parametros = array(
            'id_solicitud'         => $id_solicitud,
            'id_tipo_verificacion' => $id_tipo_verificacion,
            'pregunta1'            => $this->input->post('ocupacion'),
            'pregunta2'            => $this->input->post('domicilio_negocio'),
            'pregunta3'            => $this->input->post('barrio_negocio'),
            'pregunta4'            => $this->input->post('servicios'),
            'pregunta5'            => $this->input->post('tiempo_trabajo_ind'),
            'pregunta6'            => $this->input->post('nit'),
            'usuario_creador'      => $this->session->userdata['id_usuario'],
            'fecha_creacion'       => date('Y-m-d H:i:s')
        );
        
        $cantidad = $this->verificacion->cantidadGuardada($id_solicitud,$id_tipo_verificacion);
        if ($cantidad[0]['cantidad'] <=1){            
            $insertar_referencia_ind = $this->verificacion->guardarReferencia($parametros);
        } else {
            $insertar_referencia_ind = false;
        }       
        if ($insertar_referencia_ind) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Referencia Titular Independiente",
                'id' => $insertar_referencia_ind,
                'data' => $parametros,
                'cantidad' => $cantidad[0]
            ];
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar referencia Titular Independiente"];
        }
        $this->response($response, $status);
    }
    
    public function guardarReferenciaProveedor1_post() {
        $id_solicitud = $this->input->post('id_solicitud');
        $id_tipo_verificacion = 6;
        $parametros = array(
            'id_solicitud'         => $id_solicitud,
            'id_tipo_verificacion' => $id_tipo_verificacion,
            'pregunta1'            => $this->input->post('ocupacion_proveedor1'),
            'pregunta2'            => $this->input->post('domicilio_negocio_prov1'),
            'pregunta3'            => $this->input->post('barrio_negocio_prov1'),
            'pregunta4'            => $this->input->post('servicios_prov1'),
            'pregunta5'            => $this->input->post('tiempo_trabajo_prov1'),
            'pregunta6'            => $this->input->post('producto_prov1'),
            'usuario_creador'      => $this->session->userdata['id_usuario'],
            'fecha_creacion'       => date('Y-m-d H:i:s')
        );
        
        $cantidad = $this->verificacion->cantidadGuardada($id_solicitud,$id_tipo_verificacion);
         if ($cantidad[0]['cantidad'] <=3){            
            $insertar_referencia_ind = $this->verificacion->guardarReferencia($parametros);
        } else {
            $insertar_referencia_ind = false;
        }       
        
        if ($insertar_referencia_ind) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Referencia Proveedor 1",
                'id' => $insertar_referencia_ind,
                'data' => $parametros,
                'cantidad' => $cantidad[0]
            ];
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar referencia proveedor 1"];
        }
        $this->response($response, $status);
    }
    
    public function guardarReferenciaProveedor2_post() {
        $id_solicitud = $this->input->post('id_solicitud');
        $id_tipo_verificacion = 7;
        $parametros = array(
            'id_solicitud'         => $id_solicitud,
            'id_tipo_verificacion' => $id_tipo_verificacion,
            'pregunta1'            => $this->input->post('ocupacion_proveedor2'),
            'pregunta2'            => $this->input->post('domicilio_negocio_prov2'),
            'pregunta3'            => $this->input->post('barrio_negocio_prov2'),
            'pregunta4'            => $this->input->post('servicios_prov2'),
            'pregunta5'            => $this->input->post('tiempo_trabajo_prov2'),
            'pregunta6'            => $this->input->post('producto_prov2'),
            'usuario_creador'      => $this->session->userdata['id_usuario'],
            'fecha_creacion'       => date('Y-m-d H:i:s')
        );
        
        $cantidad = $this->verificacion->cantidadGuardada($id_solicitud,$id_tipo_verificacion);
          if ($cantidad[0]['cantidad'] <=3){            
            $insertar_referencia_prov2 = $this->verificacion->guardarReferencia($parametros);
        } else {
            $insertar_referencia_prov2 = false;
        }      
        if ($insertar_referencia_prov2) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Referencia Proveedor 2 o Cliente",
                'id' => $insertar_referencia_prov2,
                'data' => $parametros,
                'cantidad' => $cantidad[0]
            ];
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar referencia proveedor 2 o Cliente"];
        }
        $this->response($response, $status);
    }
    
    
    
}

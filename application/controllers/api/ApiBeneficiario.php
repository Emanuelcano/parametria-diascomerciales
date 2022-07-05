<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';

use Restserver\Libraries\REST_Controller;

class ApiBeneficiario extends REST_Controller {


    public function __construct($config = 'rest') {
        parent::__construct();
        $this->load->model('operaciones/Beneficiarios_model', 'operaciones', TRUE);
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('upload');
    }

    public function tablaBeneficiarios_post() {
        $data = $this->operaciones->get_beneficiarios();
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);
    }

    public function registroBeneficiario_post() {
        $fecha_creacion = date('Y-m-d H:i:s');
        $document= $this->input->post('nroDocumento');
        $tipo_documento=$this->input->post('tipoDocumento');
        $nit_beneficiario= $this->operaciones->get_nit_beneficiario($document,$tipo_documento);
        $parametros = array(
            'id_tipo' => $this->input->post('tipoBeneficiario'),
            'id_rubro' => $this->input->post('rubroBeneficiario'),
            'id_forma_pago' => $this->input->post('formaPago'),
            'id_tipo_moneda' => $this->input->post('moneda'),
            'id_tipo_documento' => $this->input->post('tipoDocumento'),
            'nro_documento' => $this->input->post('nroDocumento'),
            'denominacion' => $this->input->post('denominacion'),
            'direccion' => $this->input->post('direccion'),
            'localidad' => $this->input->post('localidad'),
            'cp' => $this->input->post('cp'),
            'id_provincia' => $this->input->post('id_provincia'),
            'telefono' => $this->input->post('telefono'),
            'telefono_alt' => $this->input->post('telefonoAlt'),
            'email' => $this->input->post('email'),
            'id_banco' => $this->input->post('id_banco'),
            'id_tipo_cuenta' => $this->input->post('tipoCuenta'),
            'nro_cuenta1' => $this->input->post('nro_cuenta1'),
            'nro_cuenta2' => $this->input->post('nro_cuenta2'),
            'usuario_creador' => $this->session->userdata['id_usuario'],
            'fecha_creacion' => $fecha_creacion,
            'usuario_ultima_moficacion' => "",
            'fecha_ultima_modificacion' => "",
            'estado' => 1,
        );
        // $insertar_beneficiario = $this->operaciones->registroBeneficiario($parametros);
        if($nit_beneficiario == 0){
                $this->operaciones->registroBeneficiario($parametros);
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => TRUE],
                    'message' => "Se creo Registro"
                ];
            }else{
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Nro documento utilizado por otro beneficiario"];
            }
        $this->response($response, $status);
        }

    public function actualizarBeneficiario_post() {
        $fecha_ultima_modificacion = date('Y-m-d H:i:s');
        $document= $this->input->post('nroDocumento');
        $tipo_documento=$this->input->post('tipoDocumento');
        $nit_beneficiario= $this->operaciones->get_nit_beneficiario($document,$tipo_documento);
        // var_dump($nit_beneficiario);die;
        $param = array(
                    'id_beneficiario' => $this->input->post('id_beneficiario'),
                    'id_tipo' => $this->input->post('tipoBeneficiario'),
                    'id_rubro' => $this->input->post('rubroBeneficiario'),
                    'id_forma_pago' => $this->input->post('formaPago'),
                    'id_tipo_moneda' => $this->input->post('moneda'),
                    'denominacion' => $this->input->post('denominacion'),
                    'direccion' => $this->input->post('direccion'),
                    'localidad' => $this->input->post('localidad'),
                    'cp' => $this->input->post('cp'),
                    'id_provincia' => $this->input->post('id_provincia'),
                    'telefono' => $this->input->post('telefono'),
                    'telefono_alt' => $this->input->post('telefonoAlt'),
                    'email' => $this->input->post('email'),
                    'id_banco' => $this->input->post('id_banco'),
                    'id_tipo_cuenta' => $this->input->post('tipoCuenta'),
                    'nro_cuenta1' => $this->input->post('nro_cuenta1'),
                    'nro_cuenta2' => $this->input->post('nro_cuenta2'),
                    'usuario_ultima_moficacion' => $this->session->userdata['id_usuario'],
                    'fecha_ultima_modificacion' => $fecha_ultima_modificacion,
                    'estado' => 1
                );

        $parametros = array(
            'id_beneficiario' => $this->input->post('id_beneficiario'),
            'id_tipo' => $this->input->post('tipoBeneficiario'),
            'id_rubro' => $this->input->post('rubroBeneficiario'),
            'id_forma_pago' => $this->input->post('formaPago'),
            'id_tipo_moneda' => $this->input->post('moneda'),
            'id_tipo_documento' => $this->input->post('tipoDocumento'),
            'nro_documento' => $this->input->post('nroDocumento'),
            'denominacion' => $this->input->post('denominacion'),
            'direccion' => $this->input->post('direccion'),
            'localidad' => $this->input->post('localidad'),
            'cp' => $this->input->post('cp'),
            'id_provincia' => $this->input->post('id_provincia'),
            'telefono' => $this->input->post('telefono'),
            'telefono_alt' => $this->input->post('telefonoAlt'),
            'email' => $this->input->post('email'),
            'id_banco' => $this->input->post('id_banco'),
            'id_tipo_cuenta' => $this->input->post('tipoCuenta'),
            'nro_cuenta1' => $this->input->post('nro_cuenta1'),
            'nro_cuenta2' => $this->input->post('nro_cuenta2'),
            'usuario_ultima_moficacion' => $this->session->userdata['id_usuario'],
            'fecha_ultima_modificacion' => $fecha_ultima_modificacion,
            'estado' => 1
        );
            // $this->operaciones->actualizarBeneficiario($parametros);

            if($nit_beneficiario == 0){
                $this->operaciones->actualizarBeneficiario($parametros);
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => TRUE],
                    'message' => "Registro actualizado"
                ];
            }else{
                $this->operaciones->actualizarBeneficiario($param);
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => TRUE],
                    'message' => "Registro actualizado"
                ];
            }
        
        $this->response($response, $status);
    }

    public function cambioEstado_post() {
        $fecha_modificacion = date('Y-m-d H:i:s');
        $parametros = array(
            'estado' => $this->input->post('cambioEstado'),
            'usuario_ultima_moficacion' => $this->session->userdata['id_usuario'],
            'fecha_ultima_modificacion' => $fecha_modificacion,
            'id_beneficiario' => $this->input->post('id_beneficiario'),
        );
        $actualizar_beneficiario = $this->operaciones->cambioEstado($parametros);
        if ($actualizar_beneficiario) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Estado Modificado"
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Fallo al modificar estado"];
        }
        $this->response($response, $status);
    }
    
    public function cargarBeneficiario_post() {
        $id_beneficiario = $this->input->post('id_beneficiario');
        $vista = $this->input->post('vista');
        $cargar_beneficiario = $this->operaciones->get_beneficiarios($id_beneficiario);
        $parametros = array(
            'tipo_benef'     => $this->operaciones->get_tipo_beneficiario(),
            'lista_rubro'    => $this->operaciones->get_lista_rubro(),
            'forma_pago'     => $this->operaciones->get_forma_pago(),
            'moneda'         => $this->operaciones->get_moneda(),
            'tipo_documento' => $this->operaciones->get_tipo_documento(),
            'provincia'      => $this->operaciones->get_provincia(),
            'banco'          => $this->operaciones->get_banco(),
            'tipo_cuenta'    => $this->operaciones->get_tipo_cuenta(),
            'beneficiarios'  => $this->operaciones->get_beneficiarios(),
            'datos_beneficiario' => $cargar_beneficiario,
            'vista' => $vista,
        );

        $this->load->view('operaciones/editarBeneficiario', ['data' => $parametros]);
    }

    public function guardarTipoBeneficiario_post() {
        $parametros = array(
            'denominacion' => $this->input->post('denominacion'),
            'estado' => 1
        );
        $insertar_tipo_benef = $this->operaciones->guardarTipoBeneficiario($parametros);
        if ($insertar_tipo_benef) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Tipo Beneficiario Guardado",
                'id' => $insertar_tipo_benef
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar el tipo de beneficiario"];
        }
        $this->response($response, $status);
    }

    public function guardarFormaPago_post() {
        $parametros = array(
            'denominacion' => $this->input->post('denominacion'),
            'estado' => 1
        );
        $insertar_fp = $this->operaciones->guardarFormaPago($parametros);
        if ($insertar_fp) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Forma de Pago Guardada",
                'id' => $insertar_fp
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar forma de pago"];
        }
        $this->response($response, $status);
    }

    public function guardarMoneda_post() {
        $parametros = array(
            'denominacion' => $this->input->post('denominacion'),
            'estado' => 1
        );
        $insertar_moneda = $this->operaciones->guardarMoneda($parametros);
        if ($insertar_moneda) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Moneda Guardada",
                'id' => $insertar_moneda
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar moneda"];
        }
        $this->response($response, $status);
    }

    public function guardarTipoDocumento_post() {
        $parametros = array(
            'nombre_tipoDocumento' => $this->input->post('nombre_tipoDocumento'),
            'convencion_tipoDocumento' => $this->input->post('convencion_tipoDocumento'),
            'codigo' => $this->input->post('codigo'),
            'id_estado_tipoDocumento' => 1
        );
        $insertar_doc = $this->operaciones->guardarTipoDocumento($parametros);
        if ($insertar_doc) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Tipo de Documento Guardado",
                'id' => $insertar_doc
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar tipo de documento"];
        }
        $this->response($response, $status);
    }

    public function guardarRubroBeneficiario_post() {
        $parametros = array(
            'denominacion' => $this->input->post('denominacion'),
            'estado' => 1
        );
        $insertar_tipo_benef = $this->operaciones->guardarRubroBeneficiario($parametros);
        if ($insertar_tipo_benef) {
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                'message' => "Rubro Beneficiario Guardado",
                'id' => $insertar_tipo_benef
            ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar el rubro de beneficiario"];
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
    

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiAuditoriaInterna extends REST_Controller
{      
	public function __construct()
	{
		parent::__construct();

		$this->load->library('User_library');
		$auth = $this->user_library->check_token();

		if($auth->status == parent::HTTP_OK)
		{
			// MODELS
			$this->load->model('Credito_model','credito_model',TRUE);
			$this->load->model('Cliente_model','cliente_model',TRUE);
			$this->load->model('Solicitud_m', 'solicitud_model', TRUE);
			$this->load->model('AuditoriaInterna_model','auditoria_interna_model',TRUE);
			

			// LIBRARIES
			$this->load->library('form_validation');
			$this->load->library('Infobip_library');
		}else{
			$this->session->sess_destroy();
	       	$this->response(['redirect'=>base_url('login')],$auth->status);
		}
	}

    public function search_post()
	{
		$params = [];
		if($this->session->userdata('tipo_operador') == ID_OPERADOR_EXTERNO)
		{
			//metodo que busca creditos solo con el id del cliente
			$creditos = $this->credito_model->simple_list_externo(['search' => $this->input->post('search')]);
		}else {
			//metodo que busca los creditos comparando documento, telefono id_credito y nombre del cliente
			$creditos = $this->credito_model->simple_list(['search' => $this->input->post('search')]);
		}
        
        foreach ($creditos as $key => $credito) {
            $date = date_create_from_format('Y-m-d H:i:s',$credito['fecha_otorgamiento']);
            
			$creditos[$key]['fecha_otorgamiento'] = ($date) ? $date->format('d-m-Y'):'';
			$creditos[$key]['hours_ultima_actividad'] = ($date) ? $date->format('H:i:s'):'';
		}

		$status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$response['status']['ok']	 = TRUE;
		$response['creditos'] 	 = $creditos;

		$this->response($response, $status);
	}

	public function searchSolicitudesPosterior_post(){
		$solicitudes=[];
		/*if($this->session->userdata('tipo_operador') == ID_AUDITOR_VENTAS)*/
		$fechas = explode('|', $this->input->post('fecha'));
		$fechaIni = date_format(date_create(trim($fechas[0])), 'Y-m-d') . ' 00:00:00.000000';
		$fechaFin = date_format(date_create(trim($fechas[1])), 'Y-m-d') . ' 23:59:59.000000';

			
		//buscamos la informacion de la silicitudes y los numeros de telefono asociados a estas solicitudes
		$solicitudes = $this->auditoria_interna_model->search_solicitudes_por_auditar($fechaIni, $fechaFin);


		$status = parent::HTTP_OK;
		$response['status']['code']  = $status;
		$response['status']['ok']	 = TRUE;
		$response['solicitudes'] 	 = $solicitudes;

		$this->response($response, $status);
	}


	public function AuditarOperador_post()
	{
		if ($this->input->is_ajax_request()) {
            $buscar = $this->input->post("sl_operadores");
            //var_dump($buscar);die;
            $datos = $this->auditoria_interna_model->auditoria_por_operador($buscar);
            echo json_encode($datos);
            
        }else{

            show_404();
        }
	}

	public function get_llamadas_por_auditar_get($id_solicitud)
	{
		$agenda_cliente = [];
		$agenda_solicitud = [];
		$solicitud = $this->solicitud_model->getSolicitudes(['id' => $id_solicitud]);

		if (!is_null($solicitud[0]["id_cliente"]) && $solicitud[0]["id_cliente"] > 0) {
			$agenda_personal = $this->cliente_model->get_agenda_personal(['id_cliente' => $solicitud[0]["id_cliente"]]);
			$agenda_referencia = $this->cliente_model->get_agenda_referencia(['id_cliente' => $solicitud[0]["id_cliente"]]);
			
			$agenda_cliente = array_merge($agenda_personal,$agenda_referencia);
		}

		$agenda_referencia = $this->solicitud_model->getSolicitudReferencia($id_solicitud);
		$agenda_solicitud =  array_merge($solicitud,$agenda_referencia);
		

		//var_dump($agenda_solicitud);die;

		$status = parent::HTTP_OK;
		$response['status']['code']  = $status;
		$response['status']['ok']	 = TRUE;
		$response['solicitudes'] 	 = $solicitud;

		$this->response($response, $status);
		
	}
	public function BuscarNumerosCliente_post()
	{
		if ($this->input->is_ajax_request()) {
			$buscar = array (
				"id_cliente" => $this->input->post("id_cliente"),
				"operacion" => $this->input->post("operacion"),
				"id_solicitud" => $this->input->post("id_solicitud")
			);
			

            //var_dump($buscar);die;
            $datos = $this->auditoria_interna_model->getTlfClientes($buscar);
            echo json_encode($datos);
            
        }else{

            show_404();
        }
	}

	/****************************************************/
	/*** Se guarda la auditoría realizada al operador ***/
	/****************************************************/
	public function GuardarAuditoria_post() {
		if ($this->input->is_ajax_request()) {
			/*** Reglas de validación para el formulario ***/
			$this->form_validation->set_rules('sl_tlfcliente', 'Teléfono', 'required');
			$this->form_validation->set_rules('txt_observaciones', 'Observaciones', 'required');
			$this->form_validation->set_rules('rd_califica', 'Calificación', 'required');
			/*** Mensajes de error para cada tipo de regla ***/
			$this->form_validation->set_message('required', 'el campo {field} es obligatorio.');

			if ($this->form_validation->run() == FALSE) {
				$response['status']['ok'] = FALSE;
				$response['message'] = validation_errors();
			} else {
				if ($this->post("tipo_auditoria")) {
					$tipo_auditoria = $this->post("tipo_auditoria");
				} else {
					$tipo_auditoria = "ONLINE";
				}
				if ($this->post("txt_hd_track")) {
					$id_track_auditoria = $this->post("txt_hd_track");
				} else {
					$id_track_auditoria = 0;
				}
				if ($this->post("id_audio")) {
					$id_audio = $this->post("id_audio");
				} else {
					$id_audio = 0;
				}
				$data = array(
					'gestion' 			 => $this->input->post("rd_califica"),
					'tlf_cliente' 		 => $this->post("sl_tlfcliente"),
					'id_track_auditoria' => $id_track_auditoria,
					'tipo_auditoria' 	 => $tipo_auditoria,
					'proceso' 			 => $this->post("txt_hd_operacion"),
					'id_solicitud' 		 => $this->post("txt_hd_solicitud"),
					'id_auditor' 		 => $this->session->userdata('idoperador'),
					'fecha_auditado' 	 => date("Y-m-d H:i:s"),
					'observaciones' 	 => $this->security->xss_clean(strip_tags($this->input->post("txt_observaciones"))),
					'id_audio' 			 => $id_audio
				);

				$id_auditoria = $this->auditoria_interna_model->setGuardaAuditoria($data);

				if ($id_auditoria > 0) {
					$auditoria = $this->auditoria_interna_model->get_auditoria(['id_auditoria' => $id_auditoria]);
					$response['status']['ok'] = TRUE;
					$response['auditoria'] = $auditoria;
					//echo json_encode($jornada);
				}else{
					$response['status']['ok'] = FALSE;
					$response['message'] = "Error al guardar la auditoria";
				}
			}
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$this->response($response, $status);	
		} else {
            show_404();
        }
	}
	/**************************************************************************************/
	/*** Se obtienen las auditorías ya realizadas a un operador por parte de un auditor ***/
	/**************************************************************************************/
	public function getAuditoriasRealizadas_get($id_operador) {
		/*** Se obtiene el auditor logueado de la sesión ***/		
		$id_auditor = $this->session->idoperador;
		$auditorias = $this->auditoria_interna_model->getAuditoriasRealizadas($id_auditor, $id_operador);

		if($auditorias) {
			$response['status']['ok'] = true;
		} else {
			$response['status']['ok'] = false;
		}

		$status = parent::HTTP_OK;
		$response['status']['code']  = $status;
		$response['auditorias'] 	 = $auditorias;
		//$x['data'] 	 = $auditorias;

		$this->response($response, $status);
	}
	
	/**************************************************************************************/
					/*** Se actualiza la auditoría seleccionada ***/
	/**************************************************************************************/
	public function ActualizarAuditoria_post($id_auditoria) {
		if ($this->input->is_ajax_request()) {
			if ($this->post("tipo_auditoria")) {
				$tipo_auditoria = $this->post("tipo_auditoria");
			} else {
				$tipo_auditoria = "ONLINE";
			}
			if ($this->post("txt_hd_track")) {
				$id_track_auditoria = $this->post("txt_hd_track");
			} else {
				$id_track_auditoria = 0;
			}
			if ($this->post("id_audio")) {
				$id_audio = $this->post("id_audio");
			} else {
				$id_audio = 0;
			}
			$data = array(
				'gestion' 			 => $this->input->post("rd_califica"),
				'tlf_cliente' 		 => $this->post("sl_tlfcliente"),
				'id_track_auditoria' => $id_track_auditoria,
				'tipo_auditoria' 	 => $tipo_auditoria,
				'proceso' 			 => $this->post("txt_hd_operacion"),
				'id_solicitud' 		 => $this->post("txt_hd_solicitud"),
				'id_auditor' 		 => $this->session->userdata('idoperador'),
				'fecha_auditado' 	 => date("Y-m-d H:i:s"),
				'observaciones' 	 =>  $this->input->post("txt_observaciones"),
				'id_audio' 			 => $id_audio
			);

			$actualizo = $this->auditoria_interna_model->setActualizarAuditoria($data, $id_auditoria);

			if ($actualizo > 0){
				$auditoria = $this->auditoria_interna_model->get_auditoria(['id_auditoria' => $id_auditoria]);
				$response['status']['ok']	 = TRUE;
				$response['auditoria'] 	 = $auditoria;
				//echo json_encode($jornada);
			}else{
				$response['status']['ok']	 = FALSE;
				$response['message'] = "Error al guardar la auditoria";
			}

			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
	
			$this->response($response, $status);	
        }else{
            show_404();
        }
	}	
	/******************************************/
	/*** Se obtienen los audios por auditar ***/
	/******************************************/
	public function getLlamadasPorAuditar_get($id_audio) {
		$audios = $this->auditoria_interna_model->getLlamadasPorAuditar($id_audio);

		if($audios) {
			$response['status']['ok'] = true;
		} else {
			$response['status']['ok'] = false;
		}

		$status = parent::HTTP_OK;
		$response['status']['code']  = $status;
		$response['audios'] = $audios;

		$this->response($response, $status);
	}
	/**************************************************************************************/
	/*** Se obtienen las auditorías ya realizadas a un operador por parte de un auditor ***/
	/**************************************************************************************/
	public function getAuditoriaAudioPosterior_get($id_audio) {
		/*** Se obtiene el auditor logueado de la sesión ***/		
		$id_auditor = $this->session->idoperador;
		$auditorias = $this->auditoria_interna_model->getAuditoriasRealizadas($id_auditor, $id_audio);

		if($auditorias) {
			$response['status']['ok'] = true;
		} else {
			$response['status']['ok'] = false;
		}

		$status = parent::HTTP_OK;
		$response['status']['code']  = $status;
		$response['auditorias'] 	 = $auditorias;

		$this->response($response, $status);
	}
}
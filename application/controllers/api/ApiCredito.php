<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiCredito extends REST_Controller
{      
	public function __construct()
	{
		parent::__construct();

		$this->load->library('User_library');
		$auth = $this->user_library->check_token();

		if($auth->status == parent::HTTP_OK)
		{
			// MODELS
			$this->load->model('credito_model','credito_model',TRUE);
			$this->load->model('CreditoDetalle_model','credito_detalle_model',TRUE);
			$this->load->model('CreditoCondicion_model','credito_condicion_model',TRUE);
			$this->load->model('Cliente_model','cliente_model',TRUE);
			$this->load->model('operaciones/Beneficiarios_model','beneficiarios_model',TRUE);
            $this->load->model('tablero/Tablero_model', 'tablero_model', TRUE);
			$this->load->model('supervisores/Supervisores_model', 'supervisores', TRUE);
			$this->load->model('PagoCredito_model', 'PagoCredito', TRUE);
			$this->load->model('Solicitud_m', 'solicitud_m', TRUE);



			// LIBRARIES
			$this->load->library('form_validation');
			$this->load->library('Infobip_library');
			$this->load->helper(['jwt', 'authorization']);
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
			//si viene la consulta por periodo de vencimiento
			if(!is_null($this->input->post('search')) &&  $this->input->post('fecha') != "null"){
				$periodo = $this->input->post('fecha');
				$consultor = (($this->input->post('operador') == "null" )? $this->session->userdata('idoperador') : $this->input->post('operador'));
				$aux = strtotime ( '-4 day' , strtotime($periodo));
				$fecha_inicio = date ( 'Y-m-d' , $aux );
				$aux = strtotime ( '+4 day' , strtotime($periodo));
				$fecha_fin = date ( 'Y-m-d' , $aux );

				if($fecha_inicio >= date('Y-m-d') && $fecha_fin >= date('Y-m-d')){
					$estado = "null";
				} else {
					$estado = "mora";
				}

				$param=[
					'fecha_inicio' => $fecha_inicio,
					'fecha_fin' => $fecha_fin,
					'operador' => $consultor,
					'estado_cuota' => $estado,
					'tipo_solicitud' => 'PRIMARIA'
				];

				$creditos = $this->credito_model->simple_list($param);
			}else{

				$creditos = $this->credito_model->simple_list(['search' => $this->input->post('search',TRUE), 'criterio'=>$this->input->post('criterio',TRUE)]);
			}
		}
        
		$status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$response['status']['ok']	 = TRUE;
		$response['creditos'] 	 = $creditos;

		$this->response($response, $status);
	}

	public function generar_promesa_post()
	{
		$hoy = date("Y-m-d H:i:s");
		$cliente = $this->post("id_cliente");
		$diferencia=0;
		
		$fecha_calculo = date_format(date_create($this->post('fecha_calculo')),"Y/m/d");

		//consultamos los dias de atraso del cliente
		$mora = $this->credito_model->mora_al_dia_cliente($cliente);
		
		$atraso = (!empty($mora))? $mora[0]['dias_atraso']:0;

		if($this->post('tipo') =="simple")
		{
				if($this->_validate_promesa($this->post('tipo'))) {
				
						$monto_promesa = floatval($this->post('monto'));
						$new_acuerdo = 0;
						$cuotas = array_unique(explode(",",$this->post('cuotas')));
						
						//consultamos si existe una promesa de pago enestado pendiente para este cliente y cambiamos su estado
						$parametros = [
							'id_cliente' => $cliente,
							'estado' => 'pendiente'
						];
						$data = [
							'estado' => 'anulado'
						];
						$result = $this->credito_model->update_promesa($parametros,$data);
						//insertamos el nuevo acuerdo
						$data =[
							"fecha" => $this->post('fecha'),
							"id_cliente" => $this->post('id_cliente'),
							"medio" => $this->post('medio'),
							"monto" => $monto_promesa,
							"estado" => 'pendiente',
							'id_operador' => $this->session->userdata("idoperador"),
							'fecha_hora' => $hoy,
							'monto_descuento' => $this->post('monto_descuento'),
							'id_planes_descuentos' => $this->post('id_plan'),
							'tipo' => $this->post('tipo'),
							'dias_atraso' => $atraso
						];
						
						$new_acuerdo = $this->credito_model->insert_promesa($data);
			
						//generamos el detalle de la promesa
						foreach ($cuotas as $key => $cuota):

							//calculamos el valor de mora a cancelar de la Cuota para la fecha de la promesa
							$mora = $this->_calcular_deuda_cron($cuota, $this->post("fecha"));

							//consultamos el plan aplicado
							$plan = $this->credito_model->get_planes_descuento(['id_plan' =>$this->post('id_plan')]);
							$descuento_cuota = 0;
							
							//calculamos el descuento correspondiente
							if(!empty($plan)){
								$plan = $plan[0];
								$campos = explode('-',$plan['aplica_sobre']);
								
								foreach ($campos as $keys => $value) {
									if(!empty($mora['mora_multa'])){
										$descuento_cuota += ($mora['mora_multa'][$value])*($plan['porcentaje'])/100;
									} else {
										$descuento_cuota += 0;
									}
										
								}

							}
							

							$monto = ($mora["total_a_pagar"]-$descuento_cuota);

						
							if($monto_promesa >= $monto && $key < count($cuotas)-1 ){
								$data =[
									"id_acuerdo" => $new_acuerdo,
									"id_credito_detalle" => $cuota,
									"monto_acuerdo" => $monto,
									"monto_descuento" => $descuento_cuota
								];
								$monto_promesa = $monto_promesa - $monto;
							

					

							} else {
								$data =[
									"id_acuerdo" => $new_acuerdo,
									"id_credito_detalle" => $cuota,
									"monto_acuerdo" => $monto_promesa,
									"monto_descuento" => $descuento_cuota
								];
							
							}
							$this->credito_model->insert_promesa_detalle($data);
						
						endforeach;
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = TRUE;
						$response['message'] = "Las promesas fueron generadas con éxito.";

						$this->infobip_library->send_smsAcuerdo($cliente);

				} else 
				{
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = FALSE;
						$response['errors'] = $this->form_validation->error_array();
				}
		} else{
				$acuerdos = explode(",",$this->post('plan_detalle'));
				$cuotas = $this->credito_model->get_creditos_cliente(['id_cliente' => $cliente, 'estado_cuota' => 'mora']);
				
				//calculamos la deuda de cada cuota
				$aux = $cuotas;
				foreach ($cuotas as $key => $cuota):

					
					$mora = $this->_calcular_deuda_cron($cuota["id"], $fecha_calculo)["total_a_pagar"];
					
					$aux[$key]["monto_cobrar"] = $mora;

				endforeach;
				$cuotas = $aux;
				//consultamos si existe una promesa de pago enestado pendiente para este cliente y cambiamos su estado
				$parametros = [
					'id_cliente' => $cliente,
					'estado' => 'pendiente'
				];
				$data = [
					'estado' => 'anulado'
				];
				$result = $this->credito_model->update_promesa($parametros,$data);
				
				$plan_acuerdo = [];
				//ciclo para las cuotas del plan de pago
				foreach ($acuerdos as $key1 => $acuerdo) {
					$datos = explode("&",$acuerdo);

					$monto = $datos[3] + $diferencia;
					$fecha = date_format(date_create($datos[2]),"Y/m/d");
					
					$id_detalle = $datos[0];
					$diferencia = 0;
					
					array_push($plan_acuerdo,[
						'cuota' => $key1 +1,
						'porcentaje' => $datos[1],
						'monto' => $datos[3],
						'fecha' => $fecha
					]);
					//insertamos el nuevo acuerdo
					$data =[
						"fecha" => $fecha,
						"id_cliente" => $this->post('id_cliente'),
						"medio" => $this->post('medio'),
						"monto" => $monto,
						"estado" => 'pendiente',
						'id_operador' => $this->session->userdata("idoperador"),
						'fecha_hora' => $hoy,
						'tipo' => $this->post('tipo'),
						'id_plan_detalle' => $id_detalle
					];
					$new_acuerdo = $this->credito_model->insert_promesa($data);
					$aux = $cuotas;
						
					//generamos el detalle de la promesa
					foreach ($cuotas as $key => $cuota):
						if ($monto == $aux[$key]["monto_cobrar"] && $monto > 0)
						{
							$data =[
								"id_acuerdo" => $new_acuerdo,
								"id_credito_detalle" => $cuota["id"],
								"monto_acuerdo" => $aux[$key]["monto_cobrar"]
							];

							$aux[$key]["monto_cobrar"] =0;
							$monto = 0;
							unset($aux[$key]);
							$this->credito_model->insert_promesa_detalle($data);
							break;
						} else {

							if($monto > $aux[$key]["monto_cobrar"] && $monto > 0){
								$data =[
									"id_acuerdo" => $new_acuerdo,
									"id_credito_detalle" => $cuota["id"],
									"monto_acuerdo" => $aux[$key]["monto_cobrar"]
								];
								$aux[$key]["monto_cobrar"] = 0;
								$monto = $monto - $aux[$key]["monto_cobrar"];
								unset($aux[$key]);
								$this->credito_model->insert_promesa_detalle($data);
								continue;
							}

							if ($monto < $aux[$key]["monto_cobrar"] && $monto > 0 ){
								$data =[
									"id_acuerdo" => $new_acuerdo,
									"id_credito_detalle" => $cuota["id"],
									"monto_acuerdo" => $monto
								];
								$aux[$key]["monto_cobrar"] = $aux[$key]["monto_cobrar"] - $monto;
								$monto = 0;
								$this->credito_model->insert_promesa_detalle($data);
								break;
							}
						}
						
					endforeach;
				
					$cuotas = $aux;
				}
				
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['message'] = "Las promesas fueron generadas con éxito.";
				$response['data'] = $plan_acuerdo;
				
				$this->infobip_library->send_smsAcuerdo($cliente);

		}

        $this->response($response,$status);
	}

	public function ajustar_descuento_acuerdo_post()
	{
						$monto_promesa = floatval($this->post('monto'));
						$acuerdo = $this->post('acuerdo');
						
						//actualizamos la promesa seleccionada 
						$parametros = [
							'id_acuerdo' => $acuerdo,
						];
						$data = [
							"tipo" => ($this->post('id_plan') > 0)? 'plan':'simple',
							"monto" => $monto_promesa,
							'monto_descuento' => $this->post('monto_descuento'),
							'id_planes_descuentos' => $this->post('id_plan'),
						];
						$result = $this->credito_model->update_promesa($parametros,$data);

						//consultamos los detalles del acuerdo modificado
						if($result > 0)
						{

							$data = $this->credito_model->acuerdos_pago_detalle(['id_acuerdo' => $acuerdo]);

							if (!empty($data)) {
								
								//por cada detalle
								foreach ($data as $key => $cuota):
									
									//calculamos el valor de mora a cancelar de la Cuota para la fecha de la promesa
									$mora = $this->_calcular_deuda_cron($cuota["id_credito_detalle"], date($cuota["fecha"]));

									//consultamos el plan aplicado
									$plan = $this->credito_model->get_planes_descuento(['id_plan' =>$this->post('id_plan')]);
									$descuento_cuota = 0;
									
									//calculamos el descuento correspondiente
									if(!empty($plan)){
										$plan = $plan[0];
										$campos = explode('-',$plan['aplica_sobre']);
										

										foreach ($campos as $key => $value) {
											if(!empty($mora['mora_multa'])){
												$descuento_cuota += ($mora['mora_multa'][$value])*($plan['porcentaje'])/100;
											} else{
												$descuento_cuota += 0;
											}
										}

									}


									//modificamos el detalle del acuerdo
									$param =[
										"id_detalle" => $cuota["id_detalle"]
									];
									if($monto_promesa >= $mora["total_a_pagar"] && $key < count($data)-1 ){
										$data_update =[
											"monto_acuerdo" => $mora["total_a_pagar"],
											"monto_descuento" => $descuento_cuota
										];
										$monto_promesa = $monto_promesa - $mora["total_a_pagar"];
									} else {
										$data_update =[
											"monto_acuerdo" => $monto_promesa,
											"monto_descuento" => $descuento_cuota
										];
									}
									
					
									$this->credito_model->update_promesa_detalle($param,$data_update);

									

								endforeach;

							}
						}

						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = TRUE;
						$response['message'] = "Las promesas fueron ajustadas con éxito.";

						$this->response($response);
						
	}

	public function consultar_promesas_get($cliente)
	{
		if($this->session->userdata('tipo_operador') == ID_OPERADOR_EXTERNO)
		{
			//si el cosnultor es externo consultamos los acuerdos por cliente y consultor
			$data = $this->credito_model->acuerdos_pago(['id_cliente' => $cliente, 'id_operador' => $this->session->userdata('idoperador')]);
		} else {
			$data = $this->credito_model->acuerdos_pago(['id_cliente' => $cliente]);

			
			foreach ($data as $key => $value) {
				$operadores = explode('-',$value['ajustado_por']);
           
				if($value['id_planes_descuentos']  > 0 && in_array($this->session->userdata('tipo_operador'), $operadores) && $value['estado']== 'pendiente')
				 {
					 $data[$key]['editable'] = true;
				 } else {
					 $data[$key]['editable'] = false;
				 }
			 }
		}

		if ( !empty($data)) 
        {
            // Set HTTP status code
            $status = parent::HTTP_OK;
            // Prepare the response
            $response = ['status' => $status, 'data' => $data];  
        }else
        {
            $status = '200';
            $response = ['status' => $status, 'message' => 'No hay resgistros '];
        }
		
        $this->response($response);
		
	}

	public function consultar_promesa_detalle_get($acuerdo)
	{
		$data = $this->credito_model->acuerdos_pago_detalle(['id_acuerdo' => $acuerdo]);
		//consultamos los acuerdos de pago
		if ( !empty($data)) 
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data];  
        }else
        {
            $status = '200';
            $response = ['status' => $status, 'message' => 'No hay resgistros '];
        }
		
        $this->response($response);
		
	}

	public function consultar_credito_get($credito)
	{
		$data = $this->credito_model->get_creditos_cliente(['id_credito' => $credito]);
		$condicion = $this->credito_model->get_credito_condicion_by(['id_credito' => $credito]);
		$aux=$data;
		$hoy = date("Y-m-d");

		foreach ($data as $key => $value) {
				$aux[$key]['detalle_pagos'] = $this->credito_model->get_all_pagos_cuota([ 'id_cuota' => $value["id"] ]);
				
				if(!empty($condicion) && $condicion[0]->idcondicion_simulador == 1){
					$gastos_mora =  $value["tecnologia_mora"] + $value["multa_mora"];
					$aux[$key]['honorarios'] = [
						'gastos_mora' => $gastos_mora,
						'sms_ivr_email' => $gastos_mora * 6.75 / 100,
						'rastreo' => $gastos_mora  * 24.25 / 100,
						'prejuridico' => $gastos_mora * 16.80 / 100,
						'bpo' => $gastos_mora * 52.20 / 100,
						'aval' => $condicion[0]->aval
					];
				}elseif(!empty($condicion) && $condicion[0]->idcondicion_simulador > 1){
					$gastos_mora =  $value["tecnologia_mora"] + $value["multa_mora"];
					$aux[$key]['honorarios'] = [
						'gastos_mora' => $gastos_mora,
						'sms_ivr_email' => $gastos_mora * 6.75 / 100,
						'rastreo' => $gastos_mora  * 24.25 / 100,
						'prejuridico' => $gastos_mora * 16.80 / 100,
						'bpo' => $gastos_mora * 52.20 / 100,
						'aval' => $condicion[0]->aval
					];
				}
				
		}

		$data = $aux;

		if ( !empty($data)) 
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data];  
        }else
        {
            $status = '200';
            $response = ['status' => $status, 'message' => 'No hay resgistros '];
        }
		
        $this->response($response);
	}

	public function get_creditos_cliente_get($cliente)
	{
		$data = $this->credito_model->get_creditos_cliente(['id_cliente' => $cliente]);
		
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$response['status']['ok'] = TRUE;
		$response['creditos'] = $data;

        $this->response($response);
	}


	public function recalcular_deuda_post()
	{
		$fecha_promesa = $this->input->post('fecha');
		$id_cuota = $this->input->post('cuota');
		$id_credito = $this->credito_detalle_model->search(['id' =>$id_cuota])[0]->id_credito;
		$id_descuento = $this->input->post('id_descuento');
		$response['descuento'] = [];

		if($this->_validate_recalcular()) 
		{
			$response = $this->_calcular_deuda_cron($id_cuota, $fecha_promesa);
			if($id_descuento != 'undefined' && $id_descuento != null){
				$plan = $this->credito_model->get_planes_descuento(['id_plan' => $id_descuento]);
				
				if(!empty($plan)){
					$plan = $plan[0];
					$campos = explode('-',$plan['aplica_sobre']);
					$descuento = 0;

					foreach ($campos as $key => $value) {
						if(!empty($response['mora_multa'])){
							$descuento += ($response['mora_multa'][$value])*($plan['porcentaje'])/100;
						} else {
							$descuento += 0;
						}
					}

					$response['descuento'] = array(
						'monto_descuento' => $descuento,
						'monto_con_descuento' => ($response['total_a_pagar']) - $descuento,
						'monto_sin_descuento' => $response['total_a_pagar'],
					);

				}
				
			}
				
		}else 
        {
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->form_validation->error_array();
		}
		
		$this->response($response);
	}

	public function agendar_telefono_mail_post()
	{
		$result = 0;
			if($this->post('cuenta') != null)
			{
				if($this->_validate_agenda('mail'))
				{
					$data =[
						'id_cliente' => $this->post('id_cliente'),
						'cuenta' => $this->post('cuenta'),
						'fuente' => 'PERSONAL',
						'contacto' => $this->post('contacto'),
						'estado' => '1'
					];
					
					$result = $this->cliente_model->agregar_mail($data);
					
					if ($result > 0) {
						$data['agenda_mail'] = $this->cliente_model->get_agenda_mail(["id_cliente" => $this->post('id_cliente')]);
						$status = parent::HTTP_OK;
						$response = ['status' => $status, 'message' => "Correo agregado a la agenda", 'agenda_mail' =>$data['agenda_mail']];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El correo no pudo ser agregado a la agenda"];
					}
				}else 
				{
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = FALSE;
						$response['errors'] = $this->form_validation->error_array();
				}

			} else {
				if($this->_validate_agenda('tel'))
				{
					$data =[
						'id_cliente' => $this->post('id_cliente'),
						'numero' => $this->post('numero'),
						'tipo' => strtoupper($this->post('tipo')),
						'fuente' => strtoupper($this->post('fuente')),
						'contacto' => $this->post('contacto'),
						'id_parentesco' => $this->post('id_parentesco'),
						'estado' => $this->post('estado'),
						'ciudad' => $this->post('municipio'),
						'departamento' => $this->post('departamento'),
						'estado_codigo' => 0
					];
					
					$result = $this->cliente_model->agregar_telefono($data);
					
					if ($result > 0) {

						$aux = $this->cliente_model->get_agenda_personal(["id_cliente" => $this->post('id_cliente')]);
						if(!empty($aux)){
							foreach ($aux as $key => $value) {
								$municipio = $this->beneficiarios_model->get_municipio(["nombre_municipio" => $value["ciudad"]]);

								if (!empty($municipio)) {
									$municipio = $municipio[0]->Codigo;
								} else{
									$municipio = "";
								}

								$data["agenda_telefonica"][$key] = [
									'id' => $value["id"],
									'id_cliente' => $value["id_cliente"],
									'numero' => $value["numero"],
									'contacto' => $value["contacto"],
									'fuente' => $value["fuente"],
									'id_parentesco' => $value["id_parentesco"],
									'parentesco' => $value["Nombre_Parentesco"],
									'estado' => $value["estado"],
									'tipo' => $value["tipo"],
									'codigo' => $municipio,
									'departamento' => $value["departamento"],
									'estado_codigo' => $value["estado_codigo"]
								];
							}
						}
						//consultamos los acuerdos de pago
						$data['lista_parentesco'] = $this->cliente_model->get_lista_parentesco();

						$status = parent::HTTP_OK;
						$response = ['status' => $status, 'message' => "Número agregado a la agenda", 'agenda_telefonica' => $data["agenda_telefonica"], 'lista_parentesco' => $data['lista_parentesco']];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El número no pudo ser agregado a la agenda"];
					}
				
				}else 
				{
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = FALSE;
						$response['errors'] = $this->form_validation->error_array();
				}		
			}
       
		$this->response($response);
	}

	public function actualizar_agenda_post()
	{
		$result = 0;
		$data=[];
		$departamento="";
		$ciudad="";
			if($this->post('agenda') == 'mail')
			{
				if($this->_validate_actualizar_agenda($this->post('agenda')))
				{
					$data =['estado' => $this->post('estado')];
					$result = $this->cliente_model->update_mail($this->post('id'), $data);

					if ($result > 0) {
						$status = parent::HTTP_OK;
						$response = ['status' => $status, 'message' => "Correo modificado"];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El correo no pudo ser modificado"];
					}
				}else 
				{
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = FALSE;
						$response['errors'] = $this->form_validation->error_array();
				}
			} else {

				if(($this->_validate_actualizar_agenda($this->post('agenda')) && $this->post('verificar') != '1') || ($this->post('verificar') == '1') || $this->post('departamento')!= null)
				{
					
					if($this->post('verificar') == '1'){
						$data =[
							'estado_codigo' => '1'
						];
					} else {
						if($this->post('departamento')!= null && $this->post('ciudad')!=null)
						{
							$departamento = $this->post('departamento');
							$ciudad = $this->post('ciudad');
							$data =[
								'tipo' => strtoupper($this->post('tipo')),
								'fuente' => strtoupper($this->post('fuente')),
								'contacto' => $this->post('contacto'),
								'id_parentesco' => $this->post('id_parentesco'),
								'estado' => $this->post('estado'),
								'departamento' => $this->post('departamento'),
								'ciudad' => $this->post('ciudad')
							];
						}else {
							$data =[
								'tipo' => strtoupper($this->post('tipo')),
								'fuente' => strtoupper($this->post('fuente')),
								'contacto' => $this->post('contacto'),
								'id_parentesco' => $this->post('id_parentesco'),
								'estado' => $this->post('estado')
							];
						}
						
					}
					$result = $this->cliente_model->update_telefono($this->post('id'), $data);
					if ($result > 0) 
					{
						if($this->post('departamento')!= null && $this->post('ciudad')!=null)
							$municipio = $this->beneficiarios_model->get_municipio(["nombre_municipio" => $ciudad]);
						
						if(!empty($municipio))
							$ciudad = $municipio[0]->Codigo;

						$status = parent::HTTP_OK;
						$response = ['status' => $status, 'message' => "Número actualizado", 'departamento' => $departamento, 'codigo' => $ciudad];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El número no pudo ser actualizado"];
					}

				}else 
				{
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = FALSE;
						$response['message'] = $this->form_validation->error_array();
				}
		

			}	
		
		$this->response($response);
	}

	public function actualizar_agenda_solicitudes_post()
	{
		$result = 0;
		$data=[];
		$departamento="";
		$ciudad="";
			if($this->post('agenda') == 'mail')
			{
				if($this->_validate_actualizar_agenda($this->post('agenda')))
				{
					$data =['fuente' => $this->post('fuente'),'contacto' => $this->post('contacto'),'estado' => $this->post('estado')];
					$result = $this->cliente_model->update_mail_solicitudes($this->post('id'), $data);
					// var_dump($result);die;

					if ($result > 0) {
						$status = parent::HTTP_OK;
						$response = ['status' => $status, 'message' => "Correo modificado"];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El correo no pudo ser modificado"];
					}
				}else 
				{
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = FALSE;
						$response['errors'] = $this->form_validation->error_array();
				}
			} else {

				if(($this->_validate_actualizar_agenda($this->post('agenda')) && $this->post('verificar') != '1') || ($this->post('verificar') == '1') || $this->post('departamento')!= null)
				{
					
					if($this->post('verificar') == '1'){
						$data =[
							'estado_codigo' => '1'
						];
					} else {
						if($this->post('departamento')!= null && $this->post('ciudad')!=null)
						{
							$departamento = $this->post('departamento');
							$ciudad = $this->post('ciudad');
							$data =[
								'tipo' => strtoupper($this->post('tipo')),
								'fuente' => strtoupper($this->post('fuente')),
								'contacto' => $this->post('contacto'),
								'id_parentesco' => $this->post('id_parentesco'),
								'estado' => $this->post('estado'),
								'departamento' => $this->post('departamento'),
								'ciudad' => $this->post('ciudad')
							];
						}else {
							$data =[
								'tipo' => strtoupper($this->post('tipo')),
								'fuente' => strtoupper($this->post('fuente')),
								'contacto' => $this->post('contacto'),
								'id_parentesco' => $this->post('id_parentesco'),
								'estado' => $this->post('estado')
							];
						}
						
					}
					$result = $this->cliente_model->update_telefono_solicitudes($this->post('id'), $data);
					if ($result > 0) 
					{
						if($this->post('departamento')!= null && $this->post('ciudad')!=null)
							$municipio = $this->beneficiarios_model->get_municipio(["nombre_municipio" => $ciudad]);
						
						if(!empty($municipio))
							$ciudad = $municipio[0]->Codigo;

						$status = parent::HTTP_OK;
						$response = ['status' => $status, 'message' => "Número actualizado", 'departamento' => $departamento, 'codigo' => $ciudad];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El número no pudo ser actualizado"];
					}

				}else 
				{
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = FALSE;
						$response['message'] = $this->form_validation->error_array();
				}
		

			}	
		
		$this->response($response);
	}

	public function enviar_mensaje_post()
	{
		
		if($this->_validate_enviar_mensaje())
		{
			$numeros = explode(',',$this->post('telefonos'));
			$status = parent::HTTP_OK;
			$response = ['status' => $status, 'message' => 'Mensajes Enviados'];

			if($numeros[0] == "")
			{
				//BUSCAMOS EL NUMERO PERSONAL DE ESTADO 1
				$aux = $this->cliente_model->get_agenda_personal(["id_cliente" => $this->post('id_cliente'), "fuente" => 'PERSONAL', "estado" => '1']);
			
				if (!empty($aux)) 
				{
					
					foreach ($aux as $key => $value) :
						$respuesta = $this->infobip_library->send_smsCobranza($this->post('id_cliente'), $this->post('id_mensaje'), $value['numero']);
						if($respuesta->data =='No se puede enviar mensaje')
						{
							$status = parent::HTTP_NOT_ACCEPTABLE;
							if($this->post('id_mensaje') == 2) {
								$response = ['status' => $status, 'message' => 'El cliente no tiene acuerdos de pago pendientes.'];
							}else{
								$response = ['status' => $status, 'message' => 'El cliente no tiene deuda.'];
							}
						}

					endforeach;
				} else 
				{
					$status = parent::HTTP_PRECONDITION_REQUIRED;
					$response = ['status' => $status, 'message' => 'Es necesario seleccionar los números de teléfono a los cuales se enviará el mensaje'];
				}
			} else 
			{

				foreach ($numeros as $key => $value) :
					$respuesta = $this->infobip_library->send_smsCobranza($this->post('id_cliente'), $this->post('id_mensaje'), $value);
					//var_dump($respuesta);die;
					if($respuesta->data =='No se puede enviar mensaje'){
						$status = parent::HTTP_NOT_ACCEPTABLE;
						if($this->post('id_mensaje') == 2) {
							$response = ['status' => $status, 'message' => 'El cliente no tiene acuerdos de pago pendientes.'];
						}else{
							$response = ['status' => $status, 'message' => 'El cliente no tiene deuda pendiente.'];
						}
					}
				endforeach;
			} 			
		}else 
		{
				$status = parent::HTTP_NOT_ACCEPTABLE;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['errors'] = $this->form_validation->error_array();
		}
		// $status = parent::HTTP_NOT_ACCEPTABLE;
		// $response['status']['code'] = $status;
		// $response['status']['ok'] = FALSE;
		// $response = ['status' => $status, 'message' => 'El servicio se encuentra actualmente desactivado.'];
		$this->response($response);
		
	}

	public function enviar_mail_post()
	{
		if($this->_validate_enviar_mail())
		{
			$cuentas = explode(',',$this->post('cuentas'));
			$status = parent::HTTP_OK;
			$response = ['status' => $status, 'message' => 'Correos Enviados'];
			if($cuentas[0] != "")
			{
				foreach ($cuentas as $key => $value) :
					$respuesta = $this->infobip_library->send_mailCobranza($this->post('id_cliente'), $this->post('id_mail'), $value);
					
					if($respuesta->data =='La persona no cumple las condiciones para enviar el mail'){
						$status = parent::HTTP_NOT_ACCEPTABLE;
						
						if($this->post('id_mail') == 21272) {
							$response = ['status' => $status, 'message' => 'El cliente no tiene acuerdos de pago pendientes.'];
						}else{
							$response = ['status' => $status, 'message' => 'El cliente no tiene deuda pendiente.'];
						}
					} else {
						$status = parent::HTTP_OK;
						$response = ['status' => $status, 'message' => 'Correos Enviados.'];
					}
				endforeach;
			} else 
			{
				$status = parent::HTTP_PRECONDITION_REQUIRED;
				$response = ['status' => $status, 'message' => 'Es necesario seleccionar las cuentas de correo a las cuales se enviará el mensaje'];
			} 			
		}else 
		{
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['message'] = 'Debe seleccionar la cuenta de correo a la cual se enviará el mensaje';
		}
		$this->response($response);
	}

	public function enviar_mail_desglose_post()
	{
		$cuentas=[];
		if($this->post('cuentas') != null)
		{
			$cuentas = explode(',',$this->post('cuentas'));
		}else 
		{
			//armamos la agenda de mail
			$aux = $this->cliente_model->get_agenda_mail(["id_cliente" => $this->post("id_cliente"), "fuente" => 'PERSONAL']);
			foreach ($aux as $key => $value) {
				array_push($cuentas, $value['cuenta']);
			}
		}

		
		if(!empty($cuentas) && $cuentas[0] != "")
		{
			foreach ($cuentas as $key => $value) :
				$respuesta = $this->infobip_library->send_mailCobranzaDesglose($this->post('id_credito'), $value);
				//var_dump($cuentas);
				//var_dump($respuesta);die;
				if($respuesta->message =='Success'){
					
					$status = parent::HTTP_OK;
					$response = ['status' => $status, 'message' => 'Correos Enviados.'];
					
				} else {
					$status = parent::HTTP_NOT_ACCEPTABLE;
					$response = ['status' => $status, 'message' => 'El correo no fue enviado.'];
				}
			endforeach;
		} else 
		{
			$status = parent::HTTP_PRECONDITION_REQUIRED;
			$response = ['status' => $status, 'message' => 'No existe una cuenta valida para enviar el email'];
		} 			
		
		$this->response($response);
	}
	
	public function detalle_plan_pago_post()
	{
			$data["deuda"] = 0;
			$data["plan_detalle"] = $this->credito_model->get_plan_detalle(['estado' => 'activo', 'id_plan' => $this->input->post('plan')]);
			$cuotas = $this->credito_model->get_creditos_cliente(['id_cliente' => $this->input->post('id_cliente'), 'estado_cuota' => 'mora']);
			if($this->input->post('fecha') == null){
				$fecha = date('Y-m-j');
				$nuevafecha = strtotime ( '+'.$data["plan_detalle"][count($data["plan_detalle"])-1]["extension_dias"].' day' , strtotime ( $fecha ) ) ;
				$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
				
			} else {
				$nuevafecha = $this->input->post('fecha');
				$dFechaR   = $this->input->post('fecha');
		            $dia       = substr($dFechaR, 0, 2); //29/07/2017
		            $mes       = substr($dFechaR, 3, 2);
		            $anio      = substr($dFechaR, 6, 4);
		            $nuevafecha = $anio . "-" . $mes . "-" . $dia;
			}

			foreach ($cuotas as $key => $value) {
				
					$data["deuda"] += $this->_calcular_deuda_cron($value["id"], $nuevafecha)['total_a_pagar'];
				
			}

			if(!empty($data["plan_detalle"]))
			{
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['data'] = $data;
			} else {
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['message'] = "No hay planes disponibles";
			}
		
		$this->response($response);
	}

	public function get_departamentos_get()
	{
		$departamentos = $this->beneficiarios_model->get_provincia();
		if(!empty($departamentos)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
		   	$response['departamentos'] 	 = $departamentos;
		} else {
			$status = parent::HTTP_NOT_FOUND;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		}
		$this->response($response, $status);
	}

	public function get_municipios_get($dep)
	{
		$municipios = $this->beneficiarios_model->get_municipio(['cod_departamento' => $dep]);
		if(!empty($municipios)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
		   	$response['municipios'] 	 = $municipios;
		} else {
			$status = parent::HTTP_NOT_FOUND;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		}
		$this->response($response, $status);
	}
	
	public function get_situacion_laboral_get($cliente){

		$data = [];
		if($this->session->userdata('tipo_operador') != ID_OPERADOR_EXTERNO)
		{
			$data = $this->cliente_model->get_situacion_laboral(['id_cliente' => $cliente]);
		}

		if(!empty($data)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
		   	$response['data'] 	 = $data;
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		}
		$this->response($response, $status);
	}
	/**
	 * Actualiza la informacion laboral del cliente a partir de los servicios disponibles en ApiBuros, alternando los servicios.
	 * @param cliente -> id_cliente
	 * @return response json
	 */
	public function update_situacion_laboral_get($sol){ 
		$config_curl = [
			/*'arus'=>[
				'url' => (ENVIRONMENT === "production") 
				? APIBURO_URL."api/ArusServices/consulta_actualiza_laboral_arus?id_cliente=".$cliente 
				:"http://buro:8080/api/ArusServices/consulta_actualiza_laboral_arus?id_cliente=".$cliente, //Modificar y poner el ambiente de testing (Preproduccion)
				'order' => 1
			],'incomevalidator' => [
				'url' => (ENVIRONMENT === "production") 
				? APIBURO_URL."api/TransUnion/consulta_actualiza_laboral_transunion?id_cliente=".$cliente 
				: "http://buro:8080/api/TransUnion/consulta_actualiza_laboral_transunion?id_cliente=".$cliente, //Modificar y poner el ambiente de testing (Preproduccion)
				'order' => 2
			]*/
			'incomevalidator' => [
				'url' => (ENVIRONMENT === "production") 
				? APIBURO_URL."api/mareigua/consulta/".$sol
				: APIBURO_URL."api/mareigua/consulta/".$sol,
				//? APIBURO_URL."api/TransUnion/consulta_actualiza_laboral_transunion?id_cliente=".$cliente 
				//: "http://buro:8080/api/TransUnion/consulta_actualiza_laboral_transunion?id_cliente=".$cliente, //Modificar y poner el ambiente de testing (Preproduccion)
				'order' => 2
			]
		];

		foreach ($config_curl as $key => $curl) {
						
			$situacion_laboral = $this->consumir_service_situacion_laboral($curl['url']);
			if( $situacion_laboral['status']['ok'] ){
				$status = parent::HTTP_OK;
				$response['status']['code']  = $status;
				$response['status']['ok']	 = TRUE;
				$response['message'] 	 = $situacion_laboral['message'];
				break;
			}else{
				$status = parent::HTTP_BAD_REQUEST;
				$response['status']['code']  = $status;
				$response['status']['ok']	 = FALSE;
				$response['message'] 	 = $situacion_laboral['message'];
			} 
		}

		$this->response($response, $status);
	}


	private function consumir_service_situacion_laboral($url){
		$curl = curl_init();
		//$options[CURLOPT_URL] = APIBURO_URL."api/ArusServices/consulta_actualiza_laboral_arus?id_cliente=".$cliente;
		//$options[CURLOPT_URL] = APIBURO_URL."api/TransUnion/consulta_actualiza_laboral_transunion?id_cliente=".$cliente;
		$options[CURLOPT_URL] = $url;
		$options[CURLOPT_CUSTOMREQUEST] = 'POST';
		$options[CURLOPT_RETURNTRANSFER] = TRUE;
		$options[CURLOPT_ENCODING] = '';
		$options[CURLOPT_MAXREDIRS] = 10;
		$options[CURLOPT_TIMEOUT] = 300;
		$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
		$options[CURLOPT_FAILONERROR] = true;// Required for HTTP error codes to be reported via our call to curl_error($curl)

		curl_setopt_array($curl,$options);

		$res = json_decode(curl_exec($curl));

		if (curl_errno($curl)) {
		    $error_msg = curl_error($curl);
		}

	    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if (isset($error_msg)) {
		    // TODO - Handle cURL error accordingly
		  	$err_message = 'Ha ocurrido un error en la capa de integración.';
			$response['status']['code']  = $code;
			$response['status']['ok']	 = FALSE;
			$response['message'] = $err_message;

		}elseif(!is_null($res) && $res->success){

			$status = parent::HTTP_OK;
			$response['status']['code']  = $code;
			$response['status']['ok']	 = TRUE;
			$response['message'] 	 = $res->title_response;

		}else {

			$status = parent::HTTP_OK;
			$response['status']['code']  = $code;
			$response['status']['ok']	 = FALSE;
			$response['message'] 	 = (is_null($res))? 'La información no esta disponible por los moementos.':$res->title_response;

		}

		return $response;
	}

	public function get_desempenho_operador_get($date)
	{
		if( $this->session->userdata('tipo_operador') != ID_OPERADOR_EXTERNO || isset($data) ){
			
			$rango_fecha = explode("%20%7C%20", $date);
			$date = new DateTime(trim($rango_fecha[0]));
			$inicio = $date->format('Y-m-d');
			$date = new DateTime(trim($rango_fecha[1]));
			$fin = $date->format('Y-m-d');

			$data['gestion'] = $this->credito_model->get_gestiones(['inicio' => $inicio, 'fin' => $fin, 'id_operador' => $this->session->userdata("idoperador")]);
			$data['gestion'] = count($data['gestion']);

			$data['acuerdos'] = $this->credito_model->get_acuerdos(['inicio' => $inicio, 'fin' => $fin, 'id_operador' => $this->session->userdata("idoperador"), 'estado' => "1,2,3"])[0];
			$data['acuerdos_cumplidos'] = $this->credito_model->get_acuerdos(['inicio' => $inicio, 'fin' => $fin, 'id_operador' => $this->session->userdata("idoperador"), 'estado' => "1"])[0];
			$data['acuerdos_incumplidos'] = $this->credito_model->get_acuerdos(['inicio' => $inicio, 'fin' => $fin, 'id_operador' => $this->session->userdata("idoperador"), 'estado' => "2"])[0];

			//if(!empty($departamentos)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
			$response['status']['ok']	 = TRUE;
			$response['data'] 	 = $data;
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
			$response['status']['ok']	 = FALSE;
			$response['message'] 	 = 'no existen registros para este operador';
		}
		$this->response($response);
	}

	public function get_llamadas_resumen_get($cliente)
    {
		$llamadas = [];
		$resultados = [];

		if($this->session->userdata('tipo_operador') != ID_OPERADOR_EXTERNO)
		{
			//consultamos los numeros de telefono del cliente
			$agenda = $this->cliente_model->get_agenda_personal(["id_cliente" => $cliente]);
			//consultamos las llamadas a cada numero
			foreach ($agenda as $key => $value) {
				$data = $this->credito_model->get_resumen_track_llamadas(['telefono' => $value["numero"]]);
				$llamadas[$key] = [
					'numero'=> $value["numero"],
					'fuente'=> $value["fuente"],
					'contacto'=> $value["contacto"],
					'llamadas' => $data
					];
			}
			$resultados = $this->credito_model->get_skills_result();
		}
		/*var_dump('<pre>');
		var_dump($llamadas );
		var_dump('</pre><hr />');die;*/

		
		if(!empty($llamadas)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
		   	$response['data'] 	 = $llamadas;
		   	$response['resultados'] 	 = $resultados;
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		   	$response['message']	 = 'No hay registros de llamada para esta solicitud';
		}
		$this->response($response, $status);
        
	}
	
	public function get_llamadas_detalle_get($cliente)
    {
		$data = [];
		$detalle = [];
		
		if($this->session->userdata('tipo_operador') != ID_OPERADOR_EXTERNO)
		{
			//consultamos los numeros de telefono del cliente
			$agenda = $this->cliente_model->get_agenda_personal(["id_cliente" => $cliente]);
			
			foreach ($agenda as $key => $value) {
				//buscamos cada registro por numer de telefono ya que los id_cliente que vienen de la central estan mal
				$data = $this->credito_model->get_track_detalle_llamadas(['telefono' => $value['numero']]);
				foreach ($data as $key2 => $value2) {
					array_push($detalle,array_merge($value, (array)$value2));
				}
				
			}				
			
		}

		if(!empty($detalle)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
		   	$response['data'] 	 = $detalle;
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		   	$response['message']	 = 'No hay registros de llamada para esta solicitud';
		}
		$this->response($response, $status);
        
	}

	public function get_audio_reproduccion_get($id_audio){
		
		$audio = $this->credito_model->get_audio_by(['id'=>$id_audio]);
		
		if(!empty($audio))
		{
			$result = $this->_copiar_audio($audio[0]->path_audio,$audio[0]->audio_name);
			if($result != ''){
				$status = parent::HTTP_OK;
				$response['status']['code']  = $status;
				$response['status']['ok']	 = TRUE;
				$response['url_audio'] 	 = $result ;
			}else{
				$status = parent::HTTP_OK;
				$response['status']['code']  = $status;
				$response['status']['ok']	 = FALSE;
				$response['message']	 ='El audio no pudo ser localizado' ;
			}
			
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		   	$response['message']	 = 'No hay registros de llamada para esta solicitud';
		}
		$this->response($response, $status);
	}

	public function get_descuento_campania_post(){

		$id_cliente = $this->post("id_cliente");
		$fecha = $this->post("fecha");
		$deuda = 0;
		$descuento = 0;
		$total = 0;
		

		//buscamos si el cliente tiene algun descuento por campaña
		$descuentos_campania = $this->credito_model->get_campaña_descuento(["id_cliente" => $id_cliente, "fecha" => $fecha]);

		if(!is_null($descuentos_campania[0]->monto_descuento)) {
			$descuento = ceil($descuentos_campania[0]->monto_descuento);
			
			//calculamos deuda total a la fecha
			$cuotas = $this->credito_model->get_creditos_cliente(['id_cliente' => $id_cliente, 'estado_cuota' => 'mora']);
			foreach ($cuotas as $key => $value) {
				$deuda += ceil($this->_calcular_deuda_cron($value["id"], $fecha)['total_a_pagar']);
			}

			$total = $deuda - $descuento;

			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
			$response['data'] 	 = [
				'deuda' => $deuda,
				'descuento' => $descuento,
				'total' => $total, 
				'valido' =>$descuentos_campania[0]->valido_hasta
			];
			   
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		   	$response['message']	 = 'No hay registros de llamada para esta solicitud';
		}
		$this->response($response, $status);
	}

	public function get_pagos_get($id_cuota){
		$pagos =[];

		if (is_numeric($id_cuota)) {
			$pagos = $this->credito_model->get_pagos_detalle(['id_cuota' => $id_cuota, 'estado' => '1' ]);
			//var_dump($pagos);die;
			$response['status']['ok'] = TRUE;
			$response['pagos'] = $pagos;
		} else {
			$response['status']['ok'] = TRUE;
			$response['message'] = 'Valor invalido';
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;

        $this->response($response);
	}

	public function get_deuda_actual_cliente_get($documento){

		$cliente = $this->cliente_model->getClienteBy(['documento'=> $documento]);
		$deuda = 0;


		if(!empty($cliente)){
			$creditos = $this->credito_model->get_creditos_cliente(['id_cliente' => $cliente[0]->id , 'where' => 'c.estado IN ("mora","vigente")']);

			foreach ($creditos as $key => $credito) {
				$deuda += floatval($credito['monto_cobrar']);
				$pago = $this->credito_model->get_pagos_cuota(['estado' => 1, 'id_cuota' => $credito["id"], 'fecha' => date("Y-m-d"), 'medio_pago' => '("efecty")'])[0];
				
				if(!is_null($pago->monto) && $deuda > 0){
					$deuda = $deuda - floatval($pago->monto);
				}
			}

			/*** Se busca la solicitud de crédito más vieja del cliente ***/
			$id_solicitud = $this->cliente_model->getIdSolicitudCredito($documento);
			if ($id_solicitud) {
				$solicitud = $id_solicitud[0]->id;
			} else {
				$solicitud = 0;
			}
			
			$solicitudesI = $this->supervisores->getSolicitudImputacionByCliente($cliente[0]->id);

			$data=[
				'id_cliente' => $cliente[0]->id,
				'documento' => $documento,
				'nombre' => $cliente[0]->nombres.' '.$cliente[0]->apellidos,
				'deuda' => $deuda,
				'id_solicitud' => $solicitud,
				'solicitudes_imputacion' => $solicitudesI
			];

			
			$response['status']['ok'] = TRUE;
			$response['data'] = $data;
		} else{
			$response['status']['ok'] = FALSE;
			$response['message'] = 'No se encontro el documento suministrado';
		}

		
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;

        $this->response($response);
	}

	public function get_descuentos_get()
	{
		$descuentos = $this->credito_model->get_planes_descuento([]);
		if(!empty($descuentos)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
		   	$response['descuentos'] 	 = $descuentos;
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		}
		$this->response($response, $status);
	}

	public function get_casos_consultor_get($periodo, $consultor = null) {
		
            $aux = strtotime ( '-2 day' , strtotime($periodo));
            $fecha_inicio = date ( 'Y-m-d' , $aux );
            $aux = strtotime ( '+2 day' , strtotime($periodo));
			$fecha_fin = date ( 'Y-m-d' , $aux );
			$operador = (($consultor == "null" || $consultor == "undefined" )? $this->session->userdata('idoperador') : $consultor);
            $params =[
				'operador' => $operador,
                'tipo_solicitud' => 'PRIMARIA',
                'situacion' => 'dependiente',
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
			];
            $aux = $this->tablero_model->get_asignaciones_tablero($params);
            $response['vendidos_empleados'] = (empty($aux)? 0: $aux[0]->cantidad);
            
            
            $params =[
				'operador' => $operador ,
                'tipo_solicitud' => 'PRIMARIA',
                'situacion' => 'dependiente',
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'estado' => 'mora',
            ];
            $aux = $this->tablero_model->get_asignaciones_tablero($params);
            $response['mora_empleados'] = (empty($aux)? 0: $aux[0]->cantidad);
			
			$response['porcent_empleados'] = (($response['vendidos_empleados'] > 0)? round($response['mora_empleados']*100/$response['vendidos_empleados']):0);
			
			
            $params =[
				'operador' => $operador ,
                'tipo_solicitud' => 'PRIMARIA',
                'situacion' => 'independiente',
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
            ];
            $aux = $this->tablero_model->get_asignaciones_tablero($params);
            $response['vendidos_independientes'] = (empty($aux)? 0: $aux[0]->cantidad);
			
            $params =[
				'operador' => $operador ,
                'tipo_solicitud' => 'PRIMARIA',
                'situacion' => 'independiente',
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'estado' => 'mora',
            ];
            $aux = $this->tablero_model->get_asignaciones_tablero($params);
            $response['mora_independientes'] = (empty($aux)? 0: $aux[0]->cantidad);
			$response['porcent_independientes'] = (($response['vendidos_independientes'] > 0)? round($response['mora_independientes']*100/$response['vendidos_independientes']):0);
			
			$casos = $response['vendidos_empleados'] + $response['vendidos_independientes'];
			$mora_casos = $response['mora_empleados'] + $response['mora_independientes'];
			$response['mora_periodo'] = (($casos > 0)? round($mora_casos*100/$casos):0);
        
        

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$response['status']['ok'] =TRUE;

        $this->response($response);
	}

	private function _calcular_deuda_cron($id_credito_detalle, $fecha_promesa)
	{
		$curl = curl_init();
		$options[CURLOPT_URL] = URL_MEDIOS_PAGOS."maestro/CronCreditos/calcular_cuota_fecha?id_credito_detalle=". $id_credito_detalle ."&fecha=". $fecha_promesa;
		$options[CURLOPT_CUSTOMREQUEST] = 'GET';
		$options[CURLOPT_RETURNTRANSFER] = TRUE;
		$options[CURLOPT_ENCODING] = '';
		$options[CURLOPT_MAXREDIRS] = 10;
		$options[CURLOPT_TIMEOUT] = 300;
		$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

		curl_setopt_array($curl,$options);

		$res = json_decode(curl_exec($curl));
		$err = curl_error($curl);
		curl_close($curl);

		if($res->success){
			$response['success'] = $res->success;
			$response['total_a_pagar'] = $res->response->total_a_cobrar;
			$response['monto_cuota'] = $res->response->monto_cuota;
			$response['proyeccion'] = (array)$res->response->proyeccion;
			$response['desglose'] = (array)$res->response->desglose;
			$response['cobros'] = (array)$res->response->cobros;
			$response['mora_multa'] = (array)$res->response->mora_multa;
		} else {
			$response['success'] = $res->success;
			$response['monto_cuota'] = 0;
			$response['total_a_pagar'] = 0;
			$response['proyeccion'] = [];
			$response['desglose'] = [];
			$response['cobros'] = [];
			$response['mora_multa'] = [];
		}
		if ($err)
		{
		  echo 'cURL Error #:' . $err;die;
		}
		return $response;
	}

	private function _copiar_audio($audio_path, $audio_name)
	{
		//si el dierectorio existe, borramos los archivos de audio y si no creamos el directorio
		if(is_dir(FCPATH.'public/audios/'.$this->session->userdata("idoperador"))){
			array_map('unlink', glob(FCPATH.'public/audios/'.$this->session->userdata("idoperador").'/*.mp3'));
		} else {
			$comando = 'mkdir -p '.FCPATH.'public/audios/'.$this->session->userdata("idoperador");
            shell_exec($comando);
		}
		
		//copiamos el archivo de audio a la carpeta temporal
		$comando = 'scp -P'.BASE_PATH_AUDIOS_PUERTO.' '.trim($audio_path).'/'.trim($audio_name).' '.FCPATH.'public/audios/'.$this->session->userdata("idoperador").'/'.$audio_name;
		//$comando = 'copy '.trim($audio_path).'/'.trim($audio_name).' '.FCPATH.'public/audios/'.$this->session->userdata("idoperador");
		shell_exec($comando);

		//si el archivo se copio con exito, armamos la ruta de acceso
		if (file_exists(FCPATH.'public/audios/'.$this->session->userdata("idoperador").'/'.trim($audio_name))){
			return base_url().'public/audios/'.$this->session->userdata("idoperador").'/'.trim($audio_name);
		}else{
			return '';
		}
	}

	/*********************************************/
    /*** Se agrega la Solicitud de Imputación  ***/
    /*********************************************/
	public function agregarSolicitudImputacion_post() {		
		if(is_null($this->post('por_procesar'))) {
			if(!$this->_validate_enviar_solicitud_imputacion()){
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['errors'] = $this->form_validation->error_array();
				$this->response($response);
			}
			
			$por_procesar = 0;
			$data = [
				'id_cliente' => $this->post('id_cliente'),
				'por_procesar' => 0,
				'fecha_pago' => date_format(date_create($this->post('fecha_pago')),"Y/m/d"),
				'referencia' => is_null($this->post('referencia'))? "":$this->post('referencia'),
				'monto_pago' => str_replace(',', '.', $this->post('monto_pago')),
				'medio_pago' => is_null($this->post('medio_pago'))? "":$this->post('medio_pago'),
				'banco_origen' => is_null($this->post('banco_origen'))? "":$this->post('banco_origen'),
				'banco_destino' => is_null($this->post('banco_destino'))? "":$this->post('banco_destino'),
				'fecha_solicitud' => date('Y-m-d H:i:s'),
				'id_operador_solicita' => $this->session->userdata("idoperador")
			];
			$operador = $this->session->userdata("idoperador");
		} else{

			$operador = $this->post('id_operador_solicita') ? $this->post('id_operador_solicita') : $this->session->userdata("idoperador");
			$data = [
				'id_cliente' => $this->post('id_cliente'),
				'medio_pago' => is_null($this->post('medio_pago'))? "":$this->post('medio_pago'),
				'por_procesar' => $this->post('por_procesar'),
				'id_operador_solicita' => $operador,
				'fecha_pago' => date_format(date_create($this->post('fecha_pago')),"Y/m/d"),
				'referencia' => is_null($this->post('referencia'))? "":$this->post('referencia'),
				'monto_pago' => str_replace(',', '.', $this->post('monto_pago')),
				'banco_origen' => is_null($this->post('banco_origen'))? "":$this->post('banco_origen'),
				'banco_destino' => is_null($this->post('banco_destino'))? "":$this->post('banco_destino'),
				'fecha_precarga' => date_format(date_create($this->post('fecha_precarga')),"Y/m/d H:i:s"),
				'fecha_solicitud' => date('Y-m-d H:i:s')
			];
			$por_procesar = $this->post('por_procesar');
		}
		
		//si es una actualizacion de precarga
		if(!is_null($this->post('precarga')) && $this->post('precarga') > 0){
			$this->cliente_model->setActualizarRutaComprobante( $this->post('solicitud'), $data );
			$idSolicitudImputacion =  $this->post('solicitud');
		}else{
			$idSolicitudImputacion = $this->cliente_model->agregarSolicitudImputacion($data);			
		}
		if($idSolicitudImputacion > 0) {
			$response['status']['ok'] = TRUE;
			$response['messages'] = '¡Insertado con éxito!';
			$response['id_solicitud_imputacion'] = $idSolicitudImputacion;
			$response['id_operador'] = $operador;
			$response['por_procesar'] = $por_procesar;
			$response['fecha_operacion'] = date("d-m-Y H:i:s");
		} else {
			$response['status']['ok'] = FALSE;
			$response['message'] = '¡Error al guardar registro!';
		}
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
        $this->response($response);
	}
    /************************************************************************************/
    /*** Se guarda y se actualiza la ruta del comprobante para la imputación del pago ***/
    /************************************************************************************/
    public function uploadComprobante_post($id_sol_imputacion) {
        // if ($this->input->is_ajax_request()) {

            $fichero_anio = dirname(BASEPATH) . '/public/supervisores/comprobantes/' . date('Y');
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/supervisores/comprobantes/' . date('Y') . '/' . date('m');
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700, true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700, true);
            }
            $fecha_creacion_archivo = date('Ymd_Hisu');
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
                $archivo_ruta = 'public/supervisores/comprobantes/' . date('Y') . '/' . date('m') . '/' .$nombre_archivo.$data['upload_data']['file_ext'];
                $status = parent::HTTP_OK;
                $response = [
                    'status'  => ['code' => $status, 'ok' => TRUE],
					'message' => "Comprobante pago guardado",
					'comprobante' => $archivo_ruta
                ];
                $datos = array(
                    'comprobante' => $archivo_ruta
                );
                $actualizo = $this->cliente_model->setActualizarRutaComprobante($id_sol_imputacion, $datos);
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
        // }else{
        //     show_404();
		// }	
    }
    /**********************************************************************/
    /*** Se Obtiene la cantidad de Imputaciones realizadas en Tesorería ***/
    /**********************************************************************/
    public function getCantImputadas_get() {
        if ($this->input->is_ajax_request()) {
            $imputadas = $this->cliente_model->getCantImputadas();
            $comentarios = $this->cliente_model->getCantComentarios();
            if ($imputadas){
                $response['status']['ok']  = TRUE;
                $response['cantImputadas'] = $imputadas[0]['cantidad'];
                $response['cantComentarios'] = $comentarios[0]['cantidad'];
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
    /******************************************************************/
    /*** Se verifica que no se vuelva a cargar el mismo comprobante ***/
    /******************************************************************/
    public function validExistComprobante(){
        $post = $this->input->post();
        $result = $this->cliente_model->validIfExist($post);
        $exist = false;

        if(empty($result)){
            $exist = true;
        }else{
            $this->form_validation->set_message('validExistComprobante', 'La solicitud de imputación que intenta subir ya existe.');
        }
        return $exist;
    }

    /******************************************************************/
    /*** Se verifica que no se vuelva a cargar el mismo comprobante ***/
    /******************************************************************/
    public function anularSolicitudImputacion_post($id_solicitud){
		$DataPost = $this->input->post();
		if ($DataPost) {
			$actualizo = $this->cliente_model->anularSolicitudImputacion($id_solicitud, $DataPost);
		} else {
			$actualizo = $this->cliente_model->anularSolicitudImputacion($id_solicitud);
		}
		if ($actualizo > 0){
			$response['status']['ok']  = TRUE;
			$response['id_operador']  = $this->session->userdata("idoperador");
			$response['fecha_operacion'] = date("d-m-Y H:i:s");
		}else{
			$response['status']['ok'] = FALSE;
			$response['message'] = "Error al actualizar datos";
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;

		$this->response($response, $status);
    }

	public function updateFechaVencimiento_post(){

		$data = $this->input->post();
		$solicitud 	= $this->solicitud_m->getSolicitudesBy(['id_credito' => $data['id_credito']])[0];
		$credito	= $this->credito_model->search(['id' => $data['id_credito']])[0];

		$body = array (
            'id_solicitud' => $solicitud->id,
            'solicitado_nuevo' => $credito['monto_prestado'],
            'plazo_nuevo' => $credito['plazo'],
            'fecha_nueva' => $data['newfecha'],
            'fecha_otorgamiento' => $credito['fecha_otorgamiento']
        );

        $headers = array('Content-Type' => 'multipart/form-data');
        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });
        try{
			$resp = Requests::post(base_url().'api/condicion_desembolso/recalcular', $headers, array(), array('hooks' => $hooks));     
			$response['recalcular'] = ['status' => json_decode($resp->body)];
        }
        catch(Exception $e){
            $status = parent::HTTP_BAD_REQUEST;
            $respuesta['status']['code'] = $status;
            $respuesta['error'] = "Muchos reintentos fallidos";
            $respuesta['status']['ok'] = false;
        }

        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
        });
        try{
			$resp = Requests::post(base_url().'api/ajustes/reprocesar_credito/'.$data['id_credito'], $headers, array(), array('hooks' => $hooks));
			$response['reprocesarCredito'] = ['status' => json_decode($resp->body)];
        }
        catch(Exception $e){
            $status = parent::HTTP_BAD_REQUEST;
            $respuesta['status']['code'] = $status;
            $respuesta['error'] = "Muchos reintentos fallidos";
            $respuesta['status']['ok'] = false;
		}
		
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}
	public function correccion_pago_post(){
		
		$data = $this->input->post();

		$id_cliente = $data['to']['cliente']['id_cliente'];
		$id_credito_detalle = $this->credito_model->get_last_credito(['id_cliente' => $id_cliente ])[0]->id; 

		foreach ($data['from']['pagosaprocesar'] as $key => $value) {
			$val = explode('|', $value);
			$id_pago_credito = (int)$val[1];
			$credito	= (int)$val[2];

            $params = [
                'id_pago' => $id_pago_credito
            ];
            $data = [
                'id_detalle_credito' => $id_credito_detalle
            ];
            $resultPago = $this->PagoCredito->update_pago_credito($params, $data);

            if($resultPago > 0){

				$pagos = $this->credito_model->get_all_pagos_cuota(['id_pago' => $id_pago_credito])[0];

				$data_imputar_pago = [
					'id_cliente' => $id_cliente,
					'monto'      => $pagos->monto,
					'fecha_pago' => $pagos->fecha_pago,
					'medio_pago' => $pagos->medio_pago,
					'id_pago_credito' => $id_pago_credito
				];
				$hooks = new Requests_Hooks();
				$hooks->register('curl.before_send', function($fp){
					curl_setopt($fp, CURLOPT_TIMEOUT, 300);
				});
				$headers = array('Accept' => 'application/json');
				$end_point = URL_MEDIOS_PAGOS."transaccion/RegistrarPago/imputacion";
	
				$response['respuestaEndPoint'][] = Requests::post($end_point, $headers, $data_imputar_pago, array('hooks' => $hooks));

				$end_point2 = base_url('api/ajustes/reprocesar_credito/'.$credito);
				$response['respuestaReprocesar_credito'][] = Requests::get($end_point2, $headers);
                // $data = array( 
                //         'id_operador'=>$this->session->userdata("idoperador"),
                //         'id_registro_afectado'=>$this->input->post('id_pago'),
                //         'tabla'=> 'pago_credito',
                //         'detalle'=> '[Ajuste de Pago] Datos :'.json_encode($data),
                //         'accion'=> "UPDATE",
                //         'fecha_hora'=> date("Y-m-d H:i:s")
                //     );
                // $track = $this->operadores->track_interno($data);

                $response['status']['ok'] = TRUE;
                $response['message'] = 'Pago actualizado';
            } else{
                $response['status']['ok'] = FALSE;
                $response['message'] = 'No fue posible actualizar el pago';
            }
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function get_pagos_by_client_post(){
		$data = $this->input->post();
		$response['data'] = $this->credito_model->get_list_pagos( array('idcliente'=>$data['idcliente']));
		$response['status']['ok'] = FALSE;
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}
	public function envio_link_pago_whatsapp_post()
    {

		
		if ( !is_null($this->post())) {
			
			if ( !empty($this->post('id_cliente'))) {
				$id_cliente = AUTHORIZATION::encodeData( $this->post('id_cliente') );
            }
            if ( !empty($this->post('telefono'))) {
				$telefono = AUTHORIZATION::encodeData( $this->post('telefono') );
            }
            if ( !empty($this->post('id_cliente'))) {
				$medio_pago = AUTHORIZATION::encodeData( $this->post('medio_pago') );
            }
            if ( !empty($this->post('id_cliente'))) {
				$tipo_pago = AUTHORIZATION::encodeData( $this->post('tipo_pago') );
            }
            if ( !empty($this->post('id_cliente'))) {
				$id_acuerdo = AUTHORIZATION::encodeData( $this->post('id_acuerdo') );
            }
			
			$operador = $this->session->userdata('tipo_operador');
			$canal = $this->post('canal');
			if($operador == 5 || $operador == 6 || $operador == 13){
				if($canal == 'cobranzas'){
					$end_point = CHATBOT_URL.'/chatbot/chatbot_live_cobranza/create_urlpago';
				} else {
					$end_point = CHATBOT_URL.'/chatbot/chatbot_live/create_urlpago';
				}
			}else {
				if($canal == 'ventas'){
					$end_point = CHATBOT_URL.'/chatbot/chatbot_live/create_urlpago';
				} else {
					$end_point = CHATBOT_URL.'/chatbot/chatbot_live_cobranza/create_urlpago';
				}
			}
            
            $resp = Requests::post($end_point, [], [
                'id_cliente'  => $id_cliente ,
                'telefono'    => $telefono,
                'medio_pago'  => $medio_pago,
                'tipo_pago'   => $tipo_pago,
                'id_acuerdo'  => $id_acuerdo  
            ]); 
            if ($resp->success) {
                $response = json_decode($resp->body);

            } else{
                $response['success'] = false;
                $response['title_response'] = "error de comunicacion";
            }
        } else {
            $response['success'] = false;
            $response['title_response'] = "solicitud invalida";
        }


        return $this->response($response);
    }

/***************************************************************************/
// VALIDATIONS
/***************************************************************************/
	function _validate_promesa($tipo)
    {
		if($tipo == 'simple'){
			$this->form_validation->set_rules('cuotas', 'Cuota', 'required');
			$this->form_validation->set_rules('fecha', 'Fecha', 'required');
			$this->form_validation->set_rules('medio', 'Medio', 'required');
			$this->form_validation->set_rules('monto', 'Monto', 'required');
		}
		
		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }

    function _validate_recalcular()
    {
        $this->form_validation->set_rules('cuota', 'Cuota', 'required');
        $this->form_validation->set_rules('fecha', 'Fecha', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}
	
	function _validate_agenda($tipo)
    {
		if($tipo == 'tel'){
			$this->form_validation->set_rules('numero', 'Número', 'required');
			$this->form_validation->set_rules('contacto', 'contacto', 'required');
			$this->form_validation->set_rules('fuente', 'Fuente', 'required');
			$this->form_validation->set_rules('estado', 'Estado', 'required');
			$this->form_validation->set_rules('departamento', 'Departamento', 'required');
			$this->form_validation->set_rules('municipio', 'Ciudad', 'required');
		}
		if($tipo == 'mail'){
			$this->form_validation->set_rules('cuenta', 'Correo', 'required');
		}

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	function _validate_actualizar_agenda($tipo)
    {
		if($tipo == 'tel'){
			$this->form_validation->set_rules('tipo', 'Tipo', 'required');
			$this->form_validation->set_rules('contacto', 'Contacto', 'required');
			$this->form_validation->set_rules('fuente', 'Fuente', 'required');
			$this->form_validation->set_rules('estado', 'Estado', 'required');
		}
		if($tipo == 'mail'){
			$this->form_validation->set_rules('estado', 'Estado', 'required');
		}

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	function _validate_enviar_mensaje()
    {
		$this->form_validation->set_rules('id_cliente', 'Cliente', 'required');
		$this->form_validation->set_rules('id_mensaje', 'Mensaje', 'required');
		$this->form_validation->set_message('required', 'El %s no esta definido');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	function _validate_enviar_mail()
    {
		$this->form_validation->set_rules('id_cliente', 'Cliente', 'required');
		$this->form_validation->set_rules('id_mail', 'Mail', 'required');
		$this->form_validation->set_rules('cuentas', 'Correo', 'required');
		$this->form_validation->set_message('required', 'El %s no esta definido');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	function _validate_enviar_solicitud_imputacion() {
		$this->form_validation->set_rules('id_cliente', 'Cliente', 'required');
		$this->form_validation->set_rules('fecha_pago', 'Fecha', 'required');
		$this->form_validation->set_rules('referencia', 'Referencia', 'required');
		$this->form_validation->set_rules('monto_pago', 'Monto', 'required');
		$this->form_validation->set_rules('medio_pago', 'Medio', 'required');
		$this->form_validation->set_rules('comprobante', 'Comprobante', 'required');
		$this->form_validation->set_rules('comprobante_exist', 'Solicitud existe', 'callback_validExistComprobante');
		$this->form_validation->set_message('required', 'El campo %s es obligatorio');

		if($this->form_validation->run()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function buscar_acuerdos_post(){
		$id_operador = $this->input->post('id_operador');
		$acuerdo = $this->input->post('acuerdo');
		switch ($acuerdo) {
			case '1':
				$fecha = 'CURRENT_DATE()';
			break;
			case '2':
				$fecha = 'DATE_ADD(CURRENT_DATE(),INTERVAL 1 DAY)';
			break;
			case '3':
				$fecha = 'DATE_ADD(CURRENT_DATE(),INTERVAL 2 DAY)';
			break;
			case '4':
				$fecha = 'DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY)';
			break;
			case '5':
				$fecha = 'DATE_SUB(CURRENT_DATE(),INTERVAL 2 DAY)';
			break;
			case '6':
				$fecha = 'DATE_SUB(CURRENT_DATE(),INTERVAL 3 DAY)';
			break;
			case '7':
				$fecha = 'DATE_SUB(CURRENT_DATE(),INTERVAL 4 DAY)';
			break;
			case '8':
				$fecha = 'DATE_SUB(CURRENT_DATE(),INTERVAL 5 DAY)';
			break;
		}
		if($acuerdo >= 4){
			$estado = "IN('pendiente','incumplido')";
		}else{
			$estado = "= 'pendiente'";
		}
		$param = array('id_operador'=>$id_operador, 'fecha'=>$fecha,'estado'=>$estado);
		$result = $this->credito_model->buscar_acuerdos_operador($param);
		if(empty($result)){
			$resul = '';
		}

			$status = parent::HTTP_OK;
			$response['data'] = $result;
			$response['status']['ok'] = TRUE;
		$this->response($response);
	}

	public function llamadas_detalle_neotell_post()
    {
		$documento = $this->input->post('documento');
		$data = [];
		$detalle = [];
		$llamadas = $this->cliente_model->get_llamadas_neotell($documento);
		
	
		if(!empty($llamadas)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
		   	$response['data'] 	 = $llamadas;
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		   	$response['message']	 = 'No hay registros de llamada para este documento';
		}
		$this->response($response, $status);
        
	}
	
}

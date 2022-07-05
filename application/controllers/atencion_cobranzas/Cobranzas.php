<?php

class Cobranzas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('is_logged_in')) 
        {
            // MODELS
            $this->load->model('softphone/CargaMasiva_model');
            $this->load->model('tracker_model', 'tracker_model', TRUE);
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
            $this->load->model('Cliente_model', 'cliente_model', TRUE);
            $this->load->model('Credito_model', 'credito_model', TRUE);
            $this->load->model('BankEntidades_model', 'bank_model', TRUE);
            $this->load->model('BankTipoCuenta_model', 'type_account_model', TRUE); //CI_model 
            $this->load->model('supervisores/Supervisores_model','Supervisores_model',TRUE);
            $this->load->model('operadores/Operadores_model', 'operadores_model', TRUE);
			$this->load->model('operaciones/Beneficiarios_model','beneficiarios_model',TRUE);
            $this->load->model('chat', 'chat', TRUE);
            $this->load->model('CertificadoDdeuda_model', 'cetificadoDdeuda', TRUE);
            $this->load->model('CodigoPromocional_model', 'codigoPromocional',TRUE);
            
            // LIBRARIES
            $this->load->library('CertificadoDeudaPDF');
            $this->load->library('SendMail_library');
			$this->load->library('layout/Layout');
            
            // HELPERS
            $this->load->helper(['formato','my_date']);
        } else {
            redirect(base_url('login'));
        }
    }
	
    public function index($id_solicitud = NULL)
    {

        $data['title']   = 'Cobranzas';
        $data['heading'] = 'Cobranzas';
	
		$data['estadoOperador'] = $this->operadores_model->getEstadoOperadorEnCampania($this->session->userdata("idoperador"));
		$data['tieneCampania'] = $this->operadores_model->checkOperadorTieneCampaniaAsignada($this->session->userdata("idoperador"));
		
        if(!is_null($this->session->userdata('render_view')) && $this->session->userdata('render_view') =="true") {
            $data['render_view']=  "true";
            $data['id_credito']=  $this->session->userdata('id_credito');
        }
	
		$idOperadoresHabilitados = [1,2,4,5,6,9,13];
	
		if (in_array($this->session->userdata('tipo_operador'), $idOperadoresHabilitados)) {
            if($this->session->userdata('tipo_operador') != 6){
                $operadores          = $this->operadores_model->get_operadores_by(['where' => '(tipo_operador in(1,4) or idoperador = 108)']);
            }else{
                $operadores          = $this->operadores_model->get_operadores_by(['idoperador' => [2,3,4,5,12,16,26,56,101,104,105,108,111,127,134,138,141,142,143,160,161,162,166,176,177,178,180,182,183,193,196,197,198,199,200,207,215,219,231,232,233]]);
            }
            $fecha_actual        = getdate();
            $dia_actual          = $fecha_actual['mday'];
            $mes_actual          = str_pad($fecha_actual['mon'], 2, '0', STR_PAD_LEFT);
            $anio_actual         = $fecha_actual['year'];
            $dia_semana          = $fecha_actual['weekday'];
            
            $lastDayOfMonth      = date("Y-m-t", strtotime("$dia_actual-$mes_actual-$anio_actual"));
            $lastDayOfMonth      = intval(date("d", strtotime($lastDayOfMonth)));
            
            //primer vencimiento del mes_actual
            $periodo_actual = $this->fechas_vencimiento($mes_actual, $anio_actual);
            $aux_day= intval(substr($periodo_actual[1],0,2));
            
            
            if($dia_actual >= 1 && $dia_actual <= $aux_day){  
                
                if ($mes_actual == 1) {
                    $vencimientos_pas = $this->fechas_vencimiento(12, $anio_actual-1);
                    $vencimientos_pas2 = $this->fechas_vencimiento(11, $anio_actual-1);
                    
                } else if ($mes_actual == 2) {
                    $vencimientos_pas = $this->fechas_vencimiento($mes_actual-1, $anio_actual);
                    $vencimientos_pas2 = $this->fechas_vencimiento(12, $anio_actual-1);
                    
                } else {
                    $vencimientos_pas = $this->fechas_vencimiento($mes_actual-1, $anio_actual);
                    $vencimientos_pas2 = $this->fechas_vencimiento($mes_actual-2, $anio_actual);
                    
                }
                $periodos = array_merge($vencimientos_pas, $vencimientos_pas2);
                array_unshift($periodos, $periodo_actual[1]);
                array_pop($periodos);
                
            }else{
                if ($mes_actual == 1) {
                    $vencimientos_pas = $this->fechas_vencimiento(12, $anio_actual-1);
                    
                } else {
                    $vencimientos_pas = $this->fechas_vencimiento($mes_actual-1, $anio_actual);
                }
                $periodos = array_merge($periodo_actual,$vencimientos_pas);
                
            }
	
			$idOperador = $this->session->userdata("idoperador");
			
            $data['periodos'] = $periodos;
            $data['operadores'] = $operadores;
			
			$data['campanias'] = $this->Supervisores_model->getCampaniasActivas();
			$campaniaActiva = $this->operadores_model->getCampaniaAsignada($idOperador);

			$data['campaniaActiva'] = [];
			$data['abrirCasoAutomatico'] = 0;
			$data['abrirCasoAutomaticoUAC'] = 0;
			
			if (!empty($campaniaActiva)) {
				$data['campaniaActiva'] = $campaniaActiva[0];
				
				if ($campaniaActiva[0]['automatico']) {
					$casosAsignados = $this->Supervisores_model->getCasosAsignados($idOperador, $campaniaActiva[0]['id']);
					if (!empty($casosAsignados)) {
						$data['abrirCasoAutomatico'] = $casosAsignados[0]['id_credito'];
					}
				}
			}
            
            $campaniaActivaUAC = $this->operadores_model->getCampaniaUAC($idOperador);
            if ($campaniaActivaUAC)
            {
                $data['campaniaActiva'] = [];
                $data['abrirCasoAutomatico'] = 0; 
                
            }

		}
        // var_dump($data);die;
		
        $this->load->view('layouts/adminLTE__header', $data);
        $this->load->view('cobranzas/gestion_cobranzas_main', $data);
        $this->load->view('layouts/adminLTE__footer', $data);

        //$this->output->enable_profiler(ENABLE_PROFILER);

        return $this;

    }

    public function fechas_vencimiento($mes, $anio){
        $vencimientos = [];
        $primer_vencimiento = "15-$mes-$anio";

        
        $dia = saber_dia($primer_vencimiento);



       if ($mes == 11 and $anio == 2021) {
			$primer_vencimiento = "11-$mes-$anio";
			$dia = saber_dia($primer_vencimiento);
			
			if ($dia == 'Sábado') {
				$primer_vencimiento = "10-$mes-$anio";
			} else if ($dia == 'Domingo') {
				$primer_vencimiento = "12-$mes-$anio";
			}
		} else {
			$primer_vencimiento = "15-$mes-$anio";
			$dia = saber_dia($primer_vencimiento);
			
			if ($dia == 'Sábado') {
				$primer_vencimiento = "14-$mes-$anio";
			} else if ($dia == 'Domingo') {
				$primer_vencimiento = "16-$mes-$anio";
			}
		} 
        

        //$lastDayOfMonth      = date("Y-m-t", strtotime("01-$mes-$anio"));
        //$lastDayOfMonth      = intval(date("d", strtotime($lastDayOfMonth)));
        $lastDayOfMonth      = 30;
        $segundo_vencimiento = "$lastDayOfMonth-$mes-$anio";
        $next_dia = saber_dia($segundo_vencimiento);

        if ($next_dia == 'Sabado') {
            $lastDayOfMonth =$lastDayOfMonth-1;
            $segundo_vencimiento = "$lastDayOfMonth-$mes-$anio";
        } else if ($next_dia == 'Domingo') {
            $lastDayOfMonth =$lastDayOfMonth-2;
            $segundo_vencimiento = "$lastDayOfMonth-$mes-$anio";
        }

        array_push($vencimientos, $segundo_vencimiento);
        array_push($vencimientos,$primer_vencimiento );
        return $vencimientos;
    }
    
    public function credito($id_credito)
    {
        //buscamos id de solicitud por id de credito
        
        $solicitud = $this->solicitud_model->getSolicitudesBy(['id_credito' => $id_credito]);
        if(!empty($solicitud)){
            $id_solicitud = $solicitud[0]->id;
        }

         $data = [];
        if (isset($id_solicitud)) 
        {
            $data['id_solicitud'] = $id_solicitud;
            // Datos propios de la solicitud
            $data['solicitude'] = $this->_get_solicitude($id_solicitud);
            $solicitud = $data['solicitude'];
         
            //consultamos los valores de mora
            $data['mora_al_dia'] = $this->_get_mora_al_dia($solicitud[0]['id_cliente']);
            $dias_mora = 0;
            
            //consultamos los creditos
            $data['creditos'] = $this->credito_model->get_creditos_cliente(['id_cliente' => $solicitud[0]['id_cliente']/*, 'where' => 'c.estado IN ("mora","vigente")'*/]);
            $data['creditos_cambios'] = $this->credito_model->get_cliente_ajustes(['id_cliente' => $solicitud[0]['id_cliente']/*, 'where' => 'c.estado IN ("mora","vigente")'*/]);
            $data['estado_credito']= $this->credito_model->get_creditos_cliente(['id_credito' => $id_credito]);
            $idsCreditos=[];
            $monto_maximo_prestado = 0;

            $data['cantidad_creditos'] = $this->solicitud_model->getCantCred($solicitud[0]['documento']);
            $data['atrasos'] = $this->solicitud_model->getDiasAtraso($solicitud[0]['id_cliente']);
            $data['codigo'] = $this->codigoPromocional->get_data_codigo($solicitud[0]['id_cliente']);
            $data['operador_promocion'] = $this->codigoPromocional->get_operadores_promocion($this->session->userdata('idoperador'));

            if (ENVIRONMENT == 'development') {
                $canal = 13289049;
                $telefono = "+5493884133854";
            }else{
                $canal = 15185188;
                $telefono = $data["solicitude"][0]["telefono"];
            }
            $estado_chat = $this->operadores_model->consulta_chat($telefono, $canal);
            if (isset($estado_chat[0]) && $estado_chat[0]["status_chat"] == "activo") {
                $data["chat"] = $estado_chat;
            }else{
                $data["chat"] = "";
            }
            
            foreach ($data['creditos'] as $key => $value) {
                $ultimo_credito = $value;
                if($value["monto_prestado"] > $monto_maximo_prestado){
                    $monto_maximo_prestado = $value["monto_prestado"];
                }

                $pago = []; //$this->credito_model->get_pagos_cuota(['razonNot'=>"Imputacion Manual Efecty",'estado' => 1, 'id_cuota' => $value["id"], 'fecha' => date("Y-m-d"), 'medio_pago' => '("efecty")'])[0];
				$dias_atraso = $value["dias_atraso"];
                if($dias_atraso >  $dias_mora)
                    $dias_mora = $dias_atraso;
                
                if(!empty($pago) && !is_null($pago->monto) && !empty($data['mora_al_dia']) && $data['mora_al_dia'][0]["deuda"] > 0){
                    $data['mora_al_dia'][0]["deuda"] = ($data['mora_al_dia'][0]["deuda"] - $pago->monto);
                    $data['creditos'][$key]["monto_cobrar"] = ($data['creditos'][$key]["monto_cobrar"] - $pago->monto);
                }
                array_push($idsCreditos, $value["id_credito"]);
            }
            $data['idsCreditos'] = array_unique($idsCreditos);
           
            //consultamos la cuota mas antigua
            $data['cuota_mas_antigua'] = $this->credito_model->get_cuota_mas_antigua(['id_cliente' => $solicitud[0]['id_cliente']]);
            //consultamos la lista de parentesco
            $data['lista_parentesco'] = $this->cliente_model->get_lista_parentesco();

            //armamos la agenda telefonica del cliente
            $aux = $this->cliente_model->get_agenda_personal(["id_cliente" => $solicitud[0]['id_cliente']]);
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
            //armamos la agenda de mail
            $data['agenda_mail'] = $this->cliente_model->get_agenda_mail(["id_cliente" => $solicitud[0]['id_cliente']]);
            
            //consultamos los template de mensajes
            $data['template_mensajes'] = $this->credito_model->get_template_mensajes(['where'=> "tipo IN ('COBRANZA', 'TODOS')", 'estado' => '1']);
            
            //consultamos los template de mails
            $data['template_mails'] = $this->credito_model->get_template_mail(['estado' => '1']);

            //consultamos los planes que pueden ser ofrecidos para el cliente
            $data['planes_pago'] = $this->credito_model->get_planes_pago(['estado' => 'activo', 'dias_mora' => (isset($data['mora_al_dia'][0]["dias_atraso"]))? $data['mora_al_dia'][0]["dias_atraso"]:0]);

            //consultamos los planes de descuento si el operador esta autorizado
            $respuesta = [];
            if($dias_mora > 46)
                $respuesta = $this->credito_model->get_planes_descuento(['estado'=> 1]);

            $data['planes_descuentos'] = [];
            foreach ($respuesta as $key => $value) {
                $operadores = explode('-',$value['aplicado_por']);
                //$this->session->userdata('tipo_operador')
                if(in_array($this->session->userdata('tipo_operador'), $operadores) ){
                    array_push($data['planes_descuentos'],$value);
                }
            }

            //consultamos los acuerdos de pago
            $data['acuerdos_pago'] = $this->_get_acuerdos_pago($solicitud[0]['id_cliente']);
            $data['documento']= $solicitud[0]['documento'];

            $hoy = date('Y-m-d H:i:s');
            $param = array(
                "id_solicitud"       => $id_solicitud,
                "operador"        => $this->session->userdata('idoperador'),
                "id_cliente"         => $solicitud[0]['id_cliente'],
                "fecha_hora"              => $hoy,
                "operador_asignado"  => $solicitud[0]['operador_asignado'],
                "canal"  => 'COBRANZA',
            );
            $this->solicitud_model->insert_track_gestion_apertura($param);

            $proximo_monto = 0;
            $beneficio = $this->solicitud_model->solicitud_beneficio_nc($solicitud[0]['id_cliente']);
            
            if(!empty($beneficio)){
                $proximo_monto = (int)$beneficio[0]->monto_disponible + (int)$beneficio[0]->beneficio_monto_fijo;
            }

            $data["proximo_monto"] = $proximo_monto;
            /* Autor:*/
            /* Fecha:*/
            /* INICIO TRACKEO PARA AUDITORIA ONLINE*/
            /*$existeAuditor = $this->tracker_model->exists_auditor();
            if($existeAuditor) {
                $dFechaR    = $solicitud[0]['fecha_alta'];
                $dia        = substr($dFechaR, 8, 2); 
                $mes        = substr($dFechaR, 5, 2);
                $anio       = substr($dFechaR, 0, 4);
                $hora       = substr($dFechaR, 11, 2);
                $minuto     = substr($dFechaR, 14, 2);
                $seg        = substr($dFechaR, 17, 2);
                $fech = $anio . "-" . $mes . "-" . $dia;
                $hora = $hora.":".$minuto.":".$seg;
                $param = array(
                    "id_solicitud"   => $id_solicitud,
                    "id_operador"    => $this->session->userdata('idoperador'),
                    "id_cliente"     => $solicitud[0]['id_cliente'],
                    "fecha"          => $fech,
                    "hora"           => $hora,
                    "Documento"      => $solicitud[0]['documento'],
                    "tipo"           => $solicitud[0]['tipo_solicitud'],
                    "buro"           => $solicitud[0]['respuesta_analisis'],
                    //"cuenta"       => 
                    //"reto"         =>
                    "estado"         => $solicitud[0]['estado'],
                    "tipo_operacion" => 'COBRANZA',
                    "bstatus"        => 1
                );
                $id_operador = $this->session->userdata('idoperador');
                //cambio de estatu cualquier acción que tenga activa en la tabla audio interno.
                $this->tracker_model->set_off_all_operation($id_operador);
                $existe = $this->tracker_model->exists_operador($id_operador);
                if($existe) {
                    //// Se actualiza el track en la tabla de auditoria_interna_online
                    $this->tracker_model->update_track_auditoria_online($param, $id_operador);
                } else {
                    // Inserto track en tabla auxiliar de auditoria online
                    $this->tracker_model->insert_track_auditoria_online($param);
                }
            }*/
            /* FIN TRACKEO PARA AUDITORIA ONLINE*/

            /**
             * END
             */
            $chatActivo = $this->chat->getChatsActiveByCliente($data["documento"]);
            if (!empty($chatActivo)){
                if( $chatActivo[0]['to'] == TWILIO_PROD_COBRANZAS) {
                     $canalCHAT = 'cobranzas';
                } else {
                    $canalCHAT = 'ventas';
                }
                $data['status_chat'] = $chatActivo[0]['status_chat'];
                $data['canal_chat']  = $canalCHAT;
                
            }
        }
		
		$data['timeConfig'] = $this->operadores_model->getConfiguracionTiemposCampania($id_credito, $this->session->userdata('idoperador'));
		$campania = $this->operadores_model->getCreditoCampania($id_credito, $this->session->userdata('idoperador'));
		
		if (!empty($campania)) {
			$data['automatico'] = (boolean) $campania['automatico'];
			$data['autollamada'] = (boolean) $campania['autollamada'];
		} else {
			$data['automatico'] = false;
			$data['autollamada'] = false;
		}
		
		$data['campania'] = $campania;

		$data['templates'] = $this->getTemplateData($solicitud[0]);
				
		if ($data['templates']['whatsapp']['numero'] != '') {
			$autollamadaNumero = $data['templates']['whatsapp']['numero']; 
		} else {
			$autollamadaNumero = '';
		}
	
		$data['autollamadaNumero'] = $autollamadaNumero;
		
		$this->load->view('cobranzas/credito', $data);
    }

//FIN - FUNCIONES DE LA BUSQUEDA DEL TRACK

    private function _get_solicitude($id_solicitude)
    {
        return $this->solicitud_model->getSolicitudes(['id' => $id_solicitude]);
    }

    private function _get_mora_al_dia($id_cliente)
    {
       //bucamos el id del cliente sociado a la solicitud especificada
		return $this->credito_model->mora_al_dia_cliente($id_cliente);
 
    }

    private function _get_acuerdos_pago($id_cliente)
    {
        //bucamos el id del cliente sociado a la solicitud especificada
        if($this->session->userdata('tipo_operador') == ID_OPERADOR_EXTERNO)
        {
           //si el cosnultor es externo consultamos los acuerdos por cliente y consultor
           $acuerdos_pago = $this->credito_model->acuerdos_pago(['id_cliente' => $id_cliente, 'id_operador' => $this->session->userdata('idoperador')]);
        } else {
            
            $acuerdos_pago = $this->credito_model->acuerdos_pago(['id_cliente'=>$id_cliente]);
           
            foreach ($acuerdos_pago as $key => $value) {
                $operadores = explode('-',$value['ajustado_por']);
           
				if($value['id_planes_descuentos']  > 0 && in_array($this->session->userdata('tipo_operador'), $operadores) && $value['estado']== 'pendiente')
                {
                    $acuerdos_pago[$key]['editable'] = true;
                } else {
                    $acuerdos_pago[$key]['editable'] = false;
                }
            }
            
        }
        //var_dump($acuerdos_pago);die;
		return $acuerdos_pago;

    }

    public function render_cobranzas()
    {
        //var_dump($this->session);die;

        
        $data = array(

        
        
        
        "cola"                  => $this->session->userdata('cola'),
        "id_agente"             => $this->session->userdata('id_agente'),
        "telefono"              => $this->session->userdata('telefono'),
        "id_credito"            => $this->session->userdata('id_credito'),
        "nombre_customer"       => $this->session->userdata('nombre_customer'),

        );

        if (!empty($this->session->userdata('id_credito'))) {
            $data['render_view'] = "true";
        }else{

            $data['render_view'] = "false";
        }
        

        //var_dump($id_cliente,$cola,$id_agente,$telefono);die;

        $data['title']   = 'Cobranzas';
        $data['heading'] = 'Cobranzas';
        $this->load->view('layouts/adminLTE__header', $data);
        $this->load->view('cobranzas/gestion_cobranzas_main', $data);
        $this->load->view('layouts/adminLTE__footer', $data);

        

        

    }
    public function get_operadores(){
        if($this->session->userdata('tipo_operador') == 4 || $this->session->userdata('tipo_operador') == 6 || $this->session->userdata('tipo_operador') == 5 || $this->session->userdata('tipo_operador') == 1 || $this->session->userdata('tipo_operador')== 2 || $this->session->userdata('tipo_operador') == 9 || $this->session->userdata('tipo_operador') == 13 ){
            if($this->session->userdata('tipo_operador') != 6){
                $operadores = $this->operadores_model->get_operadores_by(['where' => '(tipo_operador in(1,4) or idoperador = 108)']);
            }else{
                $operadores  = $this->operadores_model->get_operadores_by(['idoperador' => [2,3,4,5,12,16,26,56,101,104,105,108,111,127,134,138,141,142,143,160,161,162,166,176,177,178,180,182,183,193,196,197,198,199,200,207,215,219,231,232,233]]);
            }
        }else{
            $operadores = '';
        }

        echo json_encode($operadores);
    }
	
	/**
	 * Obtengo los templates de whatsapp, sms e email del caso
	 * 
	 * @param $solicitud
	 * @param array $data
	 *
	 * @return array
	 */
	private function getTemplateData($solicitud)
	{
		$aux = $this->Supervisores_model->getCasosAsignados($this->session->userdata('idoperador'));
		if (!empty($aux)) {
			$templatesCampania = $this->Supervisores_model->getTemplatesCampanias($aux[0]['id_campania'], $solicitud['telefono']);
			$this->load->helper('formato');
			
			$templates = [
				'whatsapp' => $this->getTemplateWhatsapp($templatesCampania, $solicitud),
				'sms' => $this->getTemplateSMS($templatesCampania['sms'], $solicitud),
				'email' => $this->getTemplateEmail($templatesCampania, $solicitud['id_cliente'])
			];
		} else {
			$templates = [
				'whatsapp' => [
					'mensaje' => '',
					'templateId' => '',
					'canal' => '',
					'numero' => ''
				]
			];	
		}

		return $templates;
	}
	
	/**
	 * Obtiene la informacion para enviar el template de whatsapp
	 * 
	 * @param $templatesCampania
	 * @param $solicitud
	 *
	 * @return array
	 */
	private function getTemplateWhatsapp($templatesCampania, $solicitud)
	{
		$msgWhatsapp = '';
		$idTemplateWhatsapp = '';
		$canalWhatsapp = '';
		if ($templatesCampania['whatsapp']['template'] != '') {
			$msgWhatsapp = mensaje_whatapp_maker($templatesCampania['whatsapp']['template'], $solicitud['id']);
			$idTemplateWhatsapp = $templatesCampania['whatsapp']['template'];
			//usado send_template en box_whatsapp como referencia
			if ($templatesCampania['whatsapp']['canal'] = '15185188') {
				//Cobranzas
				$canalWhatsapp = "2";
			} else if ($templatesCampania['whatsapp']['canal'] = '15140334') {
				//organizacion
				$canalWhatsapp = "1";
			}
		}
		$telefonoPersonal = "";
		$telefonos = $this->cliente_model->get_agenda_personal([
			"id_cliente" => $solicitud['id_cliente'],
			"fuente" => "PERSONAL"
		]);
		if (!empty($telefonos)) {
			$telefonoPersonal = $telefonos[0]['numero'];
		}
		
		$whatsapp = [
			'mensaje' => ($msgWhatsapp['message'])??'',
			'templateId' => $idTemplateWhatsapp,
			'canal' => $canalWhatsapp,
			'numero' => $telefonoPersonal
		];
		
		return $whatsapp;
			
	}
	
	/**
	 * Obtinene la informacion para enviar el template de sms
	 * 
	 * @param $sms
	 * @param $solicitud
	 *
	 * @return array
	 */
	private function getTemplateSMS($sms, $solicitud): array
	{
		$msgSMS = '';
		if ($sms != '') {
			$msgSMS = mensaje_whatapp_maker($sms, $solicitud['id']);
		}
		
		$telefonoPersonal = "";
		$telefonos = $this->cliente_model->get_agenda_personal([
			"id_cliente" => $solicitud['id_cliente'],
			"fuente" => "PERSONAL"
		]);
		if (!empty($telefonos)) {
			$telefonoPersonal = $telefonos[0]['numero'];
		}
		
		return [
			'mensaje' => ($msgSMS['message'])??'',
			'numero' => PHONE_COD_COUNTRY . $telefonoPersonal
		];
	}
	
	/**
	 * Obtiene la informacion para enviar el template de Email
	 * 
	 * @param $templatesCampania
	 * @param $id_cliente
	 *
	 * @return array
	 */
	private function getTemplateEmail($templatesCampania, $id_cliente)
	{
		$emailPersonal = "";
		$emails = $this->cliente_model->get_agenda_mail([
			"id_cliente" => $id_cliente,
			"fuente" => "PERSONAL"
		]);
		if (!empty($emails)) {
			$emailPersonal = $emails[0]['cuenta'];
		}
		
		return [
			'template' => $templatesCampania['email']['template'],
			'logica' => $templatesCampania['email']['logica'],
			'direccion' => $emailPersonal
		];
	}

    private function curl($endPoint, $method = 'POST',  $params=[] ){
        $curl = curl_init();
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
        $res = curl_exec($curl);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        if(isset($res->success) && $res->success!=null){
            $response = $res;
        } else {
            $response = null;
        }
        if ($err)
        {
          echo 'cURL Error #:' . $err;die;
        }
        return $response;

    }
    //Consulta y generacion del pdf. Recibe id_credito, id_cliente y tipo de operacion(descarga o envio via email)
    public function generar_certificado()
    {        
        $email = $this->cetificadoDdeuda->get_email($this->input->post());
        $data = $this->cetificadoDdeuda->get_data($this->input->post(), $email);
        $nombreArchivo=$data[0]['nombre'].$data[0]['apellidos'];
        $archivo = cleanString($nombreArchivo);
        $dia = date('Y-m-d');
        $formato = 'd F a';
        $fechasString = date_to_string($dia, $formato);
        
        // $this->pdf->Body($this->pdf, $data, $archivo, $fechasString);
        
        if (!isset($data[0]['abonos']) || $data[0]['abonos'] == '0') {
            $abonos = '0,00';
        }else {
            $abonos = $data[0]['abonos'];
        }
        
        // $p1 = utf8_decode('SOLVENTA COLOMBIA S.A.S., certifica que el(a) señor(a) mencionada registra a su nombre un préstamos personal bajo la modalidad de microcrédito incremental revolvente, con el siguiente saldo actualizado a la fecha de día');
        // $p2 = utf8_decode('Medios de pago para cancelación');
        // // $total_formateado=number_format(round($data[0]['total_pagar'])); // Formato al number_format
        // $titulos_tabla = ['FECHA VENCIMIENTO', 'DIAS MORA', 'SALDO ACTUALIZADO'];
        // $datos_tabla = [$data[0]['fecha_vencimiento'], $data[0]['dias_atraso'], '$'.$total_formateado];

        $this->pdf = new CertificadoDeudaPDF('L','cm','Legal');//estilos de pagina
        
        $this->pdf->body($data, $archivo, $fechasString);
        $this->guardarPDF($archivo);
        
        $destinatario =$data[0]['email'];
        if ($_POST['tipo'] == 1) {
            $hora = intval(date('H'));
            if ($hora>= 5 && $hora <= 12) {
                $saludo = 'Buenos días';
            }elseif ($hora >= 13 && $hora <= 19) {
                $saludo = 'Buenos tardes';
            }else {
                $saludo = 'Buenos noches';
            }
            $this->sendMail = new SendMail_library;
            $subject = 'Certificado de deuda';
            $message = $saludo." Sr(a). <b>".$data[0]['nombre']."</b>, le saludamos de parte de <b>Solventa Préstamos</b>. <br>Adjuntamos el certificado de deuda de su crédito <b>No.".$data[0]['id']."</b> a la fecha de hoy ".$fechasString.". 
            <br>Si tiene alguna duda ingresar a <a href='https://solventa.co/ingresar'>https://solventa.co/ingresar</a>";
            $full_path_txt = [realpath(FCPATH.'uploads/certificadoDeuda/'.$archivo.'.pdf')];
            if(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing'){
                $to = 'alexis.rodriguez@solventa.com';
            }else {
                $to = $destinatario;
            }
            $envio = $this->sendMail->send_mail2($to,"","", $subject, $message, $full_path_txt, '', '', 0);
            if ($envio->status == 200) {
                $respuesta =['response'=>'Email enviado con exito', 'opcion'=>1, 'status'=>1];
            }else{
                $respuesta =['response'=>'No se ha podido realizar el envio', 'opcion'=>1, 'status'=>2];
            }
            echo json_encode($respuesta);
        }else {
            $respuesta =['ruta'=>'uploads/certificadoDeuda/'.$archivo.'.pdf', 'opcion'=>2, 'nombre'=>$archivo];
            echo json_encode($respuesta);
        }
    }

    public function guardarPDF($nombreArchivo)
    {
        $file_path = FCPATH.'uploads/certificadoDeuda/'.$nombreArchivo.'.pdf';
        $end_folder = FCPATH.'uploads/certificadoDeuda';
        $this->_end_folder($end_folder);
        if (file_exists(FCPATH.'uploads/certificadoDeuda/'.$nombreArchivo.'.pdf')) {
            unlink(FCPATH.'uploads/certificadoDeuda/'.$nombreArchivo.'.pdf');
        }
        $this->pdf->Output($file_path,'F');

        $filename2 = realpath(FCPATH.'uploads/certificadoDeuda/'.$nombreArchivo.'.pdf');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimetype = $finfo->file($filename2);
        $file = new CURLFILE($filename2, $mimetype);
    }

    private function _end_folder($end_folder)
    {
        if(!file_exists($end_folder) && !empty($end_folder))
        {
            if(!mkdir($end_folder, 0777, true))
            {
                return FALSE;
            }
        }
        return $end_folder;
    }
	
	public function getChatButtom($idCredito,$documento)
	{
        // var_dump($idCredito,$id_cliente);
        if(!empty($idCredito))
        {
            //armamos la agenda telefonica del cliente
            $aux = $this->cliente_model->get_agenda_personal_chatuac(["documento" => $documento, "fuente" => ["PERSONAL", "REFERENCIA"]]);
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
                    ];
                }
            }
            //armamos la agenda de mail
            
            $arrayData = $this->getInfoClienteByIdCredito($idCredito, true);
            $data['id_solicitud'] = $arrayData['solicitude']['id'];
            $data['documento'] = $arrayData['solicitude']['documento'];

            $data ['dataInfo'] = $this->getInfoClienteByIdCredito($idCredito, true);
            $data ['dataTracker'] = $this->getCreditoTrackerByIdCredito($idCredito, true);
            // Agenda mail
            $data ['agenda_mail'] = $this->cliente_model->get_agenda_mail_chatuac(["documento" => $documento, "fuente" => ["PERSONAL"]]);
            // session bancaria
            $data ['banks'] = $this->bank_model->search(['id_estado_banco' => 1]);
            $data ['type_account'] = $this->type_account_model->search(['id_estado_tipocuenta' => 1]);
            $data ['analisis'] = $this->solicitud_model->getSolicitudAnalisis(['id' =>$arrayData['solicitude']['id']]);
            $data ['pagado_txt'] = $this->solicitud_model->getTxt($arrayData['solicitude']['id']);
            // var_dump($data['pagado_txt']);die;

            $data['datos_bancarios'] = $this->solicitud_model->getDatosBancarios($arrayData['solicitude']['id']);
            //  var_dump($data['datos_bancarios']);die;
            $data ['solicitude'] = $arrayData['solicitude']['id'];
            // var_dump($data);die;
            $this->load->view('layouts/header_onlyStyles', $data);
            $this->load->view('cobranzas/chatBottom', $data);
            $this->load->view('layouts/footer_onlyScripts', $data);
             

        }else{
            echo "<h4> No existeng casos por gestionar </h4>";
        }
	}
	
	public function getCreditoTrackerByIdCredito($idCredito, $returnData = false)
	{
		$solicitud = $this->solicitud_model->getSolicitudesBy(['id_credito' => $idCredito]);
		
		$data = [
			'idSolicitud' => $solicitud[0]->id,
			'idCredito' => $idCredito,
		];
		if ($returnData){
			return $data;
		} else {
			$layout = new Layout('cobranzas/stand_alone_box_tracker', $data, 'layouts/header_onlyStyles');
			$layout->viewLayout();
		}
	}
	
	public function getInfoClienteByIdCredito($idCredito, $returnData = false)
	{
        // var_dump($idCredito);
		
		$solicitud = $this->solicitud_model->getSolicitudesBy(['id_credito' => $idCredito]);
		// var_dump($solicitud);die;
		$idCliente = $solicitud[0]->id_cliente;
		
		
		$data['creditos'] = $this->credito_model->get_creditos_cliente(['id_cliente' => $idCliente]);
		$data['creditos_cambios'] = $this->credito_model->get_cliente_ajustes(['id_cliente' => $idCliente]);
		
		// ****************************************************************************************
		// MORA AL DIA
		// ****************************************************************************************
		
		$data['mora_al_dia'] = $this->_get_mora_al_dia($idCliente);
		
		
		// ****************************************************************************************
		// EXTRAS
		// ****************************************************************************************
		
		foreach ($data['creditos'] as $key => $value) {
//			$ultimo_credito = $value;
//			if($value["monto_prestado"] > $monto_maximo_prestado){
//				$monto_maximo_prestado = $value["monto_prestado"];
//			}
//			
			$pago = []; 
			$dias_mora = 0;
			$dias_atraso = $value["dias_atraso"];
			if($dias_atraso >  $dias_mora)
				$dias_mora = $dias_atraso;
			
			if(!empty($pago) && !is_null($pago->monto) && !empty($data['mora_al_dia']) && $data['mora_al_dia'][0]["deuda"] > 0){
				$data['mora_al_dia'][0]["deuda"] = ($data['mora_al_dia'][0]["deuda"] - $pago->monto);
				$data['creditos'][$key]["monto_cobrar"] = ($data['creditos'][$key]["monto_cobrar"] - $pago->monto);
			}
//			array_push($idsCreditos, $value["id_credito"]);
		}
		
		// ****************************************************************************************
		// PROXIMO MONTO 
		// ****************************************************************************************
		$proximo_monto = 0;
		$beneficio = $this->solicitud_model->solicitud_beneficio_nc($idCliente);
		
		if(!empty($beneficio)){
			$proximo_monto = (int)$beneficio[0]->monto_disponible + (int)$beneficio[0]->beneficio_monto_fijo;
		}
		$data["proximo_monto"] = $proximo_monto;
		
		// ****************************************************************************************
		// PLANES DESCUENTOS
		// ****************************************************************************************
		
		$respuesta = [];
		if($dias_mora > 46)
			$respuesta = $this->credito_model->get_planes_descuento([]);
		
		$data['planes_descuentos'] = [];
		foreach ($respuesta as $key => $value) {
			$operadores = explode('-',$value['aplicado_por']);
			//$this->session->userdata('tipo_operador')
			if(in_array($this->session->userdata('tipo_operador'), $operadores) ){
				array_push($data['planes_descuentos'],$value);
			}
		}
		
		// ****************************************************************************************
		// ACUERDOS DE PAGO
		// ****************************************************************************************
		
		$data['acuerdos_pago'] = $this->_get_acuerdos_pago($idCliente);
		
		
		// ****************************************************************************************
		// SOLICITUDE
		// ****************************************************************************************
		
		
		$data['solicitude'] = [];
		if(!empty($solicitud)){
			$id_solicitud = $solicitud[0]->id;
			$aux = $this->_get_solicitude($id_solicitud);
			$data['solicitude'] = $aux[0];
		}
		
		// ****************************************************************************************
		// COUTA MAS ANTIGUA
		// ****************************************************************************************
		
		$data['cuota_mas_antigua'] = $this->credito_model->get_cuota_mas_antigua(['id_cliente' => $idCliente]);
		
		// ****************************************************************************************
		// PLANES DE PAGO
		// ****************************************************************************************
		
		$data['planes_pago'] = $this->credito_model->get_planes_pago(['estado' => 'activo', 'dias_mora' => (isset($data['mora_al_dia'][0]["dias_atraso"]))? $data['mora_al_dia'][0]["dias_atraso"]:0]);
		
		// ****************************************************************************************
		// STATUS CHAT Y CANAL CHAT
		// ****************************************************************************************
		
		$data['status_chat'] = null;
		$data['canal_chat']	= null;
		
		$solicitud = $data['solicitude'];
		$documento = $solicitud['documento'];
		$chatActivo = $this->chat->getChatsActiveByCliente($documento);
		if (!empty($chatActivo)){
			if( $chatActivo[0]['to'] == TWILIO_PROD_COBRANZAS) {
				$canalCHAT = 'cobranzas';
			} else {
				$canalCHAT = 'ventas';
			}
			$data['status_chat'] = $chatActivo[0]['status_chat'];
			$data['canal_chat']  = $canalCHAT;
			
		}
		
		// ****************************************************************************************
		// OTROS
		// ****************************************************************************************
		
		$data['id_cliente'] = $idCliente;
		
		if ($returnData) {
			return $data;
		} else {
			$layout = new Layout('cobranzas/stand_alone_box_cliente_info', $data, 'layouts/header_onlyStyles');
			$layout->viewLayout();
		}
		
	}

    public function envioCodigoPromocion()
    {
        $id_cliente = $this->input->post("id_cliente");
        $id_solicitud = $this->input->post("id_solicitud");
        $nombresCompleto = $this->input->post("nombre");
        $nombre = explode(" ", $nombresCompleto);
        $tipo = $this->input->post("tipo");
        $codigo = $this->codigoPromocional->get_data_codigo($id_cliente);
        
        $operador = $this->codigoPromocional->get_operador($this->session->userdata('idoperador'));
        
        if(ENVIRONMENT == 'development'){
            $telefono = "+5493884133854";
            $canal = "13289049";
        }else{
            $telefono = $this->input->post("telefono");
            $canal = "15185188";
        }
        
        if ($tipo == "WSP") {
            $mensaje = $nombre[0].", aplica el código *".$codigo[0]['codigo']."* y aprovecha la promoción de sumar *$".number_format($codigo[0]['monto_extra'],2, ",", ".")."* a tu nuevo crédito. \n\nInicia sesión en www.solventa.co y disfruta de este beneficio exclusivo para ti";
            $rs_consulta = $this->operadores_model->consulta_chat($telefono, $canal);
            $params = [
                'chatID' => $rs_consulta[0]['id_chat'],
                'operatorID' =>  "108",
                'message' => $mensaje
            ];
            $endPoint = URL_BACKEND.'comunicaciones/TwilioCobranzas/send_new_message';
            
        }else{
            $mensaje = $nombre[0].", aplica el código ".$codigo[0]['codigo']." y aprovecha la promoción de sumar $".number_format($codigo[0]['monto_extra'],2, ",", ".")." a tu nuevo crédito.\n\nInicia sesión en www.solventa.co y disfruta de este beneficio exclusivo para ti";

            $params = [
                'tipo_envio' => '2',
                'servicio' => '2',
                'numero' => $telefono,
                'text' => $mensaje
            ];

            $endPoint = URL_CAMPANIAS.'ApiEnvioComuGeneral';

        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_POSTFIELDS => $params
        ));
        $res = json_decode(curl_exec($curl));
        $err   = curl_error($curl);
        curl_close($curl);
        
        if ((isset($res->sms->status->code) && $res->sms->status->code == 200) || (isset($res->messages) && !empty($res->messages))) {
            $response = $res;
        } else {
            $response = null;
        }

        if($response){
            $track = $this->codigoPromocional->track_condigo_promocion($id_cliente, $codigo[0]['codigo'], $this->session->userdata('idoperador'), $tipo);
            $data["status"] = 200;
            $data["mensaje"] = "Envio realizado correctamente";
        }else{
            $data["status"] = 400;
            $data["mensaje"] = "Error al realizar el envio";
        }
        echo json_encode($data);
    }

}

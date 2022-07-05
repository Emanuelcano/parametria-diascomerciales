<?php

use Carbon\Carbon;

class Gestion extends CI_Controller
{

    private $_solicitudes_status = ['estado' => [ 
                                                    ['value' => 'analisis', 'label' => 'análisis' ],
                                                    ['value' => 'verificado', 'label' => 'verificado' ],
                                                    ['value' => 'validado', 'label' => 'validado' ],
                                                    ['value' => 'aprobado', 'label' => 'aprobado' ],
                                                    ['value' => 'transfiriendo', 'label' => 'transfiriendo' ],
                                                    ['value' => 'pagado', 'label' => 'pagado' ],
                                                    ['value' => 'rechazado', 'label' => 'rechazado' ],
                                                ],
                                    'buro'  =>  [
                                                    ['value' => 'buro_aprobado', 'label' => 'buro – aprobado' ],
                                                    ['value' => 'buro_rechazado', 'label' => 'buro – rechazado' ],
                                                ],
                                    'cuenta'=>  [
                                                    ['value' => 'cuenta_aceptada', 'label' => 'cuenta – aceptada' ],
                                                    ['value' => 'cuenta_rechazada', 'label' => 'cuenta – rechazada' ],
                                               ],
                                    'reto'  =>  [
                                                    ['value' => 'reto_correcta', 'label' => 'reto – correcta' ],
                                                    ['value' => 'reto_incorrecta', 'label' => 'reto – incorrecta' ],
                                                ],
                                    'crédito'=> [
                                                    ['value' => 'credito_vigente', 'label' => 'crédito – vigente' ],
                                                    ['value' => 'credito_mora', 'label' => 'crédito – mora' ],
                                                    ['value' => 'credito_cancelado', 'label' => 'crédito – cancelado' ],
                                                ],
                                ];
    private $_type_solicitud = [
                                    ['value' => 'primaria', 'label' => 'primaria' ],
                                    ['value' => 'retanqueo', 'label' => 'retanqueo' ],
                                ];

    private $_status_eid =    [
                                    'respuesta_supervivencia'  =>[
                                        '1'           =>   'OK', 
                                        '0'         =>   'ERROR'
                                    ],
                                    'respuesta_match'  =>[
                                        '1'           =>   'SI', 
                                        '0'        =>   'NO'
                                    ],
                                ];
    private $_status_veriff =    [
                                    'respuesta_match'  =>[
                                        'approved'           =>   'APROBADO', 
                                        'declined'      => 'RECHAZADA',
                                        'resubmission_requested' => 'REENVIO',
                                        'expired'   =>  'EXPIRADO',
                                        'abandoned' =>  'ABANDONADO',
                                        '0'        =>   'PENDIENTE',
                                    ],
                                    /*'response_code' =>[
                                        '9001'  =>  'APROBADO',
                                        '9102'  =>  'VERIFICACION FALLIDA - CLIENTE NO VERIFICADO',
                                        '9103'  =>  'VERIFICACION FALLIDA - PROCESO INCOMPLETO',
                                        '9104'  =>  'VERIFICACION FALLIDA - PROCESO EXPIRADO',
                                        '0'  =>  'PENDIENTE'
                                    ],*/
                                ];
    private $_status_jumio =    [
                                    'respuesta_identificacion'  =>[  
                                        'APPROVED_VERIFIED'             => 'VALIDO',
                                        'DENIED_FRAUD'                  => 'FRAUDE',
                                        'DENIED_UNSUPPORTED_ID_TYPE'    => 'NO SOPORTADO',  
                                        'DENIED_UNSUPPORTED_ID_COUNTR'  => 'PAIS NO SOPORTADO',  
                                        'ERROR_NOT_READABLE_ID'         => 'NO LEGIBLE',  
                                        'NO_ID_UPLOADED'                => 'NO CARGADO',  
                                    ],
                                    'source'  =>[
                                        'WEB'           =>   'SIN VERIFICAR', 
                                        'WEB_CAM'       =>   'CAMARA WEB', 
                                        'WEB_UPLOAD'    =>   'GALERIA', 
                                    ],
                                    'respuesta_supervivencia'  =>[
                                        'true'           =>   'OK', 
                                        'false'         =>   'ERROR',
                                        '1'           =>   'OK', 
                                        '0'         =>   'ERROR',
                                    ],
                                    'respuesta_match'  =>[
                                        'MATCH'           =>   'SI', 
                                        'NO_MATCH'        =>   'NO',
                                        'NOT_AVAILABLE'   =>   'NO', 
                                        'NOT_POSSIBLE'    =>   'NO PROBABLE', 
                                    ],
                                ];      

    private $_status_meta = [
        'respuesta_identificacion'  =>[  
            'verified'             => 'VALIDO',
            'reviewNeeded'         => 'REVISION',
            'rejected'             => 'RECHAZADO',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('is_logged_in')) 
        {
            // MODELS
            $this->load->model('softphone/CargaMasiva_model');
            $this->load->model('tracker_model', 'tracker_model', TRUE); //CI_model
            $this->load->model('operaciones_model', '', TRUE); //CI_model
            $this->load->model('galery_model', '', TRUE); //CI_model
            $this->load->model('Solicitud_m'); //CI_model
            $this->load->model('solicitudes/SolicitudPasos_model', 'pasos', TRUE); //CI_model
            $this->load->model('solicitudes/SolicitudMedioContacto_model', 'medio_contacto', TRUE); //CI_model
            $this->load->model('Metrics_model', '', TRUE); //CI_model
            $this->load->model('Credito_model', 'credito_model', TRUE); //CI_model
            $this->load->model('Cliente_model', 'cliente_model', TRUE); //CI_model
            $this->load->model('Quota_model', 'quota_model', TRUE); //CI_model
            $this->load->model('SolicitudBeneficios_model', 'beneficio_model', TRUE); //CI_model
            $this->load->model('BankEntidades_model', 'bank_model', TRUE); //CI_model
            $this->load->model('BankTipoCuenta_model', 'type_account_model', TRUE); //CI_model
            $this->load->model('Operator_model', 'operator_model', TRUE); //CI_model
            $this->load->model('Jumio_model', 'jumio_model', TRUE); //CI_model
            $this->load->model('PagoCredito_model', 'pago_credito_model', TRUE); //CI_model
            $this->load->model('AgendaOperadores_model', 'AgendaOperadores_model', TRUE); //CI_model
            $this->load->model('VideoCall_model','videollamadas', TRUE );   
            $this->load->model('operadores/Operadores_model','operadores', TRUE );   
            /**
             * Ignacio Salcedo - ignacio.salcedo@solventa.com
             */
            $this->load->model('chat');
            $this->load->model('visitor');
            /**
             * END
             */

            // LIBRARIES
            $this->load->library('form_validation');
            // HELPERS
            $this->load->helper('date');
            $this->load->helper('formato');
            $this->load->helper(['jwt', 'authorization']); 

        } else {
            redirect(base_url('login'));
        }
    }

    public function index($id_solicitud = NULL)
    {
        
        $data['solicitudes_status'] = $this->_solicitudes_status;
        $data['solicitudes_types']  = $this->_type_solicitud;
        $data['operators']          = $this->operator_model->search(['estado' => 1]);
        //Agrego si hay errores de desembolso
        $limit = $this->input->post('start');
        $offset = $this->input->post('length');
        $params['LITERAL'] = [];
        $type_operator = $this->session->userdata('tipo_operador');
        $operator = $this->session->userdata('idoperador');
        $bancos = [];
        
        
        switch ($type_operator)
        {
            case '1':
                array_push($params['LITERAL'],'(solicitud.operador_asignado = '.$operator.')');
                array_push($params['LITERAL'],'(solicitud.id IN (SELECT id_solicitud FROM gestion.track_gestion WHERE observaciones LIKE "%[NO PAGADO]%" AND DATEDIFF(CURRENT_DATE,fecha) <=15))');
                $params['solicitud.estado'] = "TRANSFIRIENDO";
                break;
            case '2':
                array_push($params['LITERAL'],'(solicitud.operador_asignado IN (SELECT idoperador FROM gestion.operadores WHERE equipo IN (SELECT equipo FROM gestion.operadores WHERE idoperador='.$operator.')))');
                array_push($params['LITERAL'],'(solicitud.id IN (SELECT id_solicitud FROM gestion.track_gestion WHERE observaciones LIKE "%[NO PAGADO]%" AND DATEDIFF(CURRENT_DATE,fecha) <=15))');
                $params['solicitud.estado'] = "TRANSFIRIENDO";
                break;  
            case '11':
                $bancos = $this->get_banks();
                break; 
            default:
                $params['solicitud.estado'] = "TRANSFIRIENDO";

                
        }
            
        
        $data['conf_obligatorias'] = $this->Solicitud_m->get_configuracion_obligatorias(['tipo_operador' => $this->session->userdata('tipo_operador')]);
        $data['bancos'] =  $bancos;
        
        // Seteo de rangos de fechas.
        $date_end	= date('Y-m-d');
        $date_start	= date('Y-m-d', strtotime($date_end . '- 15 days'));
        $params = array_merge($params, $this->_set_date_range($date_start, $date_end));
        $data['desembolso'] = count($this->Solicitud_m->simple_list($params,$limit,$offset)); 
        
        //var_dump($this->session->userdata('render_view'));die;
        if(!is_null($this->session->userdata('render_view')) && $this->session->userdata('render_view') =="true"){
            $data['render_view']=  "true";
            $data['id_solicitud']=  $this->session->userdata('id_solicitud');
        }
        
        if (!is_null($this->session->flashdata('render_view')) && $this->session->flashdata('render_view')=="true") {
            
            $data['id_credito']=  $this->session->flashdata('id_credito');
            $data['id_solicitud']=  $this->session->flashdata('id_solicitud');
            $data['cola']=  $this->session->flashdata('cola');
            $data['id_agente']=  $this->session->flashdata('id_agente');
            $data['telefono']=  $this->session->flashdata('telefono');
            
            if ((!is_null($this->session->flashdata('documento'))) && (!is_null($this->session->flashdata('monto_disponible')))) {
                $data['documento']=  $this->session->flashdata('documento');
                $data['monto_disponible']=  $this->session->flashdata('monto_disponible');
            }
            
            $data['nombre_customer']=  $this->session->flashdata('nombre_customer');
            $data['render_view']=  "true";
            
        }
        
        
        $data['validaciones'] = count($this->Solicitud_m->listado_por_revisar_desembolso($this->session->userdata('idoperador'),'validaciones'));
       
        $data['agenda_operadores'] = null;
        $agendaOperadores = $this->getAgendaOperadores($operator);
        $data['agenda_operadores'] = $agendaOperadores['agenda_operadores'];

        $data['operadoresAutomaticas'] = $this->operadores->get_operadores_gestion_obligatoria(['idoperador'=>$this->session->userdata('idoperador')]);

        
        $data['solicitud_ajustes'] = $this->getSolicitudAjustes($operator);
        $data['idOperador'] = $operator;
        
        $data['title']   = 'Gestión de clientes';
        $data['heading'] = 'Gestión';
        $this->load->view('layouts/adminLTE__header', $data);
        $this->load->view('gestion/gestion_cliente_main', $data);
        $this->load->view('layouts/adminLTE__footer', $data);

        //$this->output->enable_profiler(ENABLE_PROFILER);

        return $this;

    }
/**
 * [Obtengo todos los datos de una solicitud]
 * @param  [INT] $id_solicitud [Id de la solicitud]
 * @return [Array]             [Resultado de los datos de la solicitud]
 */
    public function solicitud($id_solicitud = NULL, $view = NULL)
    {
        $data = [];
        $valoresIndicadores=[];
        $valoresIndicadores2=[];
        $data['indicadores']=[];
        if (isset($id_solicitud)) {
            $data['id_solicitud'] = $id_solicitud;
            // Rangos
            $data['ranges'] = $this->_get_ranges_metrics();
                foreach ($data['ranges'] as $key => $rango) {
                    $indicador = $rango['tabla']."-".$rango['campo'];
                    $parametros =[
                        'base' => $rango['base_datos'],
                        'tabla' => $rango['tabla'],
                        'campo' => $rango['campo'],
                        'id_solicitud' => $id_solicitud,
                    ];
                    $valor = $this->Metrics_model->getAnalisisFlex($parametros);
                    $metricas = [
                        "$indicador" => (!empty($valor))? $valor[0]:0
                    ];
                    array_push($valoresIndicadores, $metricas);
                }

                foreach ($valoresIndicadores as $value){
                    foreach ($value as $key=> $value2){
                        if ($value2==0){
                            $value2=$value;
                        }
                        foreach ($value2 as $key2=> $value3){
                        // dump($value2);
                            $valoresIndicadores2[$key2]=$value3;
                        }
                    }

                }
                array_push($data['indicadores'], $valoresIndicadores2);

            // Datos propios de la solicitud
            $data['solicitude'] = $this->_get_solicitude($id_solicitud);

            //var_dump($id_solicitud,$data['solicitude']);die;
            $solicitud = $data['solicitude'];
            // Lista de bancos habilitados
            $data['banks'] = $this->get_banks();
            // Lista tipos de cuentas bancarias habilitadas
            $data['type_account'] = $this->get_types_account();
            // Datos amalisis de la solicitud
            $data['analisis'] = $this->get_solicitud_analisis($id_solicitud);
            // Datos bancarios de la solicitud
            $data['datos_bancarios'] = $this->get_data_bank($id_solicitud);
            //Datos personales
           // $data['datos_personales'] = $this->get_datos_personales($id_solicitud);            
            // Referencias
            $data = array_merge($data, $this->get_references($id_solicitud));
            //Lista de parentescos
            $data['parentesco'] = $this->get_parentesco();
            // Condiciones del credito
            $data['solicitud_condicion'] = $this->get_terms($id_solicitud);
            // Confirmacion de desembolso
            $data['solicitud_desembolso'] = $this->get_expenditure($id_solicitud);
            // Primer y Segundo vencimiento
            $fecha = date('Y-m-d');
            $data['vencimientos'] = $this->Solicitud_m->vencimiento_botones_front($fecha);
            // Oferta
            $data['solicitud_oferta'] = $this->get_offer($id_solicitud, $data['solicitude']);
            // Pasos de la solicitud
            $data['pasos'] = $this->get_pasos($data['solicitude'][0]['paso']);
            // Mail logueado
            $data['email_log'] = $this->get_mail_log($data['solicitude'][0]['email']);
            // var_dump( $this->get_mail_log($data['solicitude'][0]['email']));die;
            // Imagenes y tipos
            $data = array_merge($data);
            // Dias de atrasos en creditos
            $data['atrasos'] = $this->get_atrasos($solicitud[0]['id_cliente']);
            // Referencias familiares segun id_solicitu y tipo_vericacion
            $data = array_merge($data, $this->get_referencias_botones($id_solicitud));
            // Seguimientos de la solicitud
           /* $data['tracker']['tracks'] = $this->get_tracks($id_solicitud);*/
            // Traigo telefonos del cliente
            $data['telefonos_cliente'] = $this->get_telefonos($solicitud[0]['documento'], $data['analisis']);
            // Traigo celulares del cliente
            $data['celulares_cliente'] = $this->get_celulares($solicitud[0]['documento'],$data['analisis']);
            // Cantidad de creditos pedidos por el cliente
            $data['cantidad_creditos'] = $this->get_cant_cred($solicitud[0]['documento']);
            // Cantidad de creditos pedidos por el cliente
            $data['pagado_txt'] = $this->get_txt($id_solicitud);

            $data['solicitud_verificacion'] = $this->Solicitud_m->get_verificacion_desembolso(['id_solicitud' =>$id_solicitud, 'limite' => 1]);
            
            $data['agenda_operadores'] = $this->AgendaOperadores_model->getAgendaOperadores(['id_solicitud' =>$id_solicitud, 'id_operador' => $this->session->userdata('idoperador')]);

            $data['pagare_revolvente'] = $this->Solicitud_m->get_pagare_revolvente(['documento' =>$solicitud[0]['documento']]);

            $data['solicitud_ajustes'] = $this->Solicitud_m->getTipo_Ajuste();

            $solicitud_referencias = $this->Solicitud_m->get_referencia_solicitud(['id_solicitud' =>$id_solicitud]);
            if (isset($solicitud_referencias[0]))
                $data['solicitud_referencias'] = $solicitud_referencias[0];

            //Creditos del cliente
            $data['credits'] = [];
            if(isset($data['solicitude'][0]['id_credito']))
            {
                $data['credits'] = $this->get_credits($data['solicitude'][0]['id_credito']);
                $data['solicitude'][0] =  array_merge($data['solicitude'][0],$this->_credit_status($data['credits']));
            }
            
            if($solicitud[0]['id_credito'] != 0 && $solicitud[0]['id_cliente'] != 0 ){
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
            }
            
            
            $verificacion = $this->Solicitud_m->get_verificacion_desembolso(['id_solicitud' => $id_solicitud, 'respuesta' => TRUE, 'revisada' => 0, 'limit' => 1]);
			if(!empty($verificacion)){
                $param = [
                    "revisada" => "1",
				];
				$result = $this->Solicitud_m->update_validar_desembolso($verificacion[0]->id, $param);
			}
            $data['validaciones'] = count($this->Solicitud_m->listado_por_revisar_desembolso($this->session->userdata('idoperador'),'validaciones'));

           
            
            
            $creditos = [];
            if(!is_null($solicitud[0]['id_cliente']) && $solicitud[0]['id_cliente'] > 0)
                $creditos = $this->credito_model->get_creditos_cliente(['id_cliente' => $solicitud[0]['id_cliente']]);

            $monto_maximo_prestado = 0;
            $dias_atraso = 9999;
            foreach ($creditos as $key => $value) {
                $dias_atraso = $value["dias_atraso"];
                $ultimo_credito = $value;
                if($value["monto_prestado"] > $monto_maximo_prestado){
                    $monto_maximo_prestado = $value["monto_prestado"];
                }
            }

            //niveles cliente

            
            $proximo_monto = 0;
            $beneficio = $this->Solicitud_m->solicitud_beneficio_nc($solicitud[0]['id_cliente']);
            
            if(!empty($beneficio)){
                $proximo_monto = (int)$beneficio[0]->monto_disponible + (int)$beneficio[0]->beneficio_monto_fijo;
            }


            $data["proximo_monto"] = $proximo_monto;

            /* Autor:*/
            /* Fecha:*/
            /* INICIO TRACKEO PARA AUDITORIA ONLINE*/
           /* $existeAuditor = $this->tracker_model->exists_auditor();
            if($existeAuditor) {
                $dFechaR      = $solicitud[0]['fecha_alta'];
                $dia          = substr($dFechaR, 8, 2); 
                $mes          = substr($dFechaR, 5, 2);
                $anio         = substr($dFechaR, 0, 4);
                $hora         = substr($dFechaR, 11, 2);
                $minuto         = substr($dFechaR, 14, 2);
                $seg         = substr($dFechaR, 17, 2);
                $fech    = $anio . "-" . $mes . "-" . $dia;
                $hora= $hora.":".$minuto.":".$seg;
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
                    "tipo_operacion" => 'ORIGINACION',
                    "bstatus"        => 1
                );
                $id_operador = $this->session->userdata('idoperador');
                //cambio de estatu cualquier acción que tenga activa en la tabla audio interno.
                $this->tracker_model->set_off_all_operation($id_operador);
                $existe = $this->tracker_model->exists_operador($id_operador);
                if($existe) {
                    ///// Se actualiza el track en la tabla de auditoria_interna_online 
                    $this->tracker_model->update_track_auditoria_online($param, $id_operador);
                } else {
                    // Inserto track en tabla auxiliar de auditoria online
                    $this->tracker_model->insert_track_auditoria_online($param);
                }
            }
            /* FIN TRACKEO PARA AUDITORIA ONLINE*/
            $data['btn_revision'] = $this->_get_botones_revision();
            $imagen_pagare = $this->Solicitud_m->existe_imagen($id_solicitud, ['imagen'=>24]);
            $data['pagare_descargado'] = FALSE;
            if (!empty($imagen_pagare)) {
                $data['pagare_descargado'] = TRUE;
            }

            $hoy = date('Y-m-d H:i:s');
            $param = array(
                "id_solicitud"       => $id_solicitud,
                "operador"        => $this->session->userdata('idoperador'),
                "id_cliente"         => $solicitud[0]['id_cliente'],
                "fecha_hora"              => $hoy,
                "operador_asignado"  => $solicitud[0]['operador_asignado'],
                "canal"  => 'GESTION',

            );
            $this->Solicitud_m->insert_track_gestion_apertura($param);

            $data['user_videocall'] = $this->videollamadas->get_user_videollamadas($this->session->userdata('idoperador'));
           
            $biometria_items = $this->Solicitud_m->get_biometria_items();
            foreach ($biometria_items as $key => $value) {
                $data['biometria_items'][$value['tipo']][] = $value;
            }           
        }

        if ($view == 'transRechazada') {
            $this->load->view('gestion/solicitud_transferencia_rechazada', $data);
        } else {
            $this->load->view('gestion/solicitud', $data);
        }
        
    }

    public function get_title($id_solicitud)
    {
        $data['solicitude'] = $this->_get_solicitude($id_solicitud);
        $data['datos_bancarios'] = $this->get_data_bank($id_solicitud);
        $error_jumio=$this->Solicitud_m->get_error_jumio($id_solicitud);
        $validation_jumio= $this->Solicitud_m->validation_jumio($id_solicitud);
        if(!empty($error_jumio) && $validation_jumio == 0){
            $data['error_jumio']= $error_jumio[0]["descripcion"];
        }
        else{  
            $data['error_jumio']="";
        }
        $bank = isset($datos_bancarios[0])? $datos_bancarios[0]:[];
        //$data['whatsapp_respuesta'] = $this->get_res_whatsapp($data['solicitude'][0]['documento']);

        // $this->load->view('gestion/box_title',['solicitude' => $data['solicitude'][0], 'bank'=>$bank,'error_jumio'=>$data['error_jumio'] /*, 'whatsapp_respuesta'=> $data['whatsapp_respuesta']*/]);
    }
    public function get_datos_contacto($id_solicitud){
        $data['solicitude'] = $this->_get_solicitude($id_solicitud);
         // Mail logueado
        $data['email_log'] = $this->get_mail_log($data['solicitude'][0]['email']);
        //$data['whatsapp_respuesta'] = $this->get_res_whatsapp($data['solicitude'][0]['documento']);

        $this->load->view('gestion/box_datos_contacto',['solicitude'=>$data['solicitude'][0],'email_log' =>  $data['email_log']/*, 'whatsapp_respuesta'=> $data['whatsapp_respuesta']*/]);
    }

    public function get_metricas($id_solicitud){
            // Indicadores
            $data['indicadores'] = $this->_get_metrics($id_solicitud);
            // Rangos
            $data['ranges'] = $this->_get_ranges_metrics();

            if(!empty($data['indicadores']))
            { 
                $this->load->view('gestion/box_metrics',['indicadores'=>$data['indicadores'][0], 'ranges'=>$data['ranges']]); 
            }
    }
    



/********************************
 * CARGA DE IMAGENES
 */

    public function get_images_documentos($id_solicitude, $view = NULL)
    {
        $datos = $this->input->get();
        $data = $this->_get_imagenes($id_solicitude, $datos['d'])['images'];
        $aux = $data['data'];
        $evaluarFirma = TRUE;
        $firma = 0;

        $data['pagare_revolvente'] = $this->Solicitud_m->get_pagare_revolvente(['documento' => $datos['d']]);

        foreach ($aux as $key => $value) {
            if($value->id_imagen_requerida == '25'){
                $evaluarFirma = FALSE;
            }
        }
        if($evaluarFirma){
            $firma = $this->_get_solicitude($id_solicitude)[0]['pagare_firmado'];
        }
        if ($view == 'documentos') {
            // SE CARGA EN LA PANTALLA DE SOLICITUD CON TRANSFERENCIA RECHAZADA
            $this->load->view('gestion/box_slider_documentos', ['docs'=>$data, 'firma' => $firma, 'id_solicitud' => $id_solicitude, 'evaluarFirma' =>$evaluarFirma ]);
        } else {
            $this->load->view('gestion/box_ref_documentos', ['docs'=>$data, 'firma' => $firma, 'id_solicitud' => $id_solicitude, 'evaluarFirma' =>$evaluarFirma ]);
        }
    }

    public function get_images_archivos($id_solicitude, $view = NULL)
    {
        $solicitude = $this->_get_solicitude($id_solicitude);
        $data = $this->_get_images($id_solicitude);
        if ($view == 'documentos') {
            // SE CARGA EN LA PANTALLA DE SOLICITUD CON TRANSFERENCIA RECHAZADA
            $this->load->view('gestion/box_cargar_documento', ['images'=>$data['images'],'solicitude' => $solicitude[0]]);
        } else {
            $this->load->view('gestion/box_ref_archivos', ['images'=>$data['images'],'solicitude' => $solicitude[0]]);
        }
    }
    public function get_images_box_galery($id_solicitude)
    {
        $solicitude = $this->_get_solicitude($id_solicitude);
        $data['images']['data']    = $this->galery_model->search_images(['id_solicitud' => $id_solicitude]);

        $data['images']['origin']  = array_merge($this->_get_jumio($id_solicitude), $this->_get_eid($id_solicitude));
        $data['images']['origin']  = array_merge($data['images']['origin'], $this->_get_veriff($id_solicitude));
        $data['images']['origin']  = array_merge($data['images']['origin'], $this->_get_meta($id_solicitude));
        $data['images']['ws']  = [];
        $ws_scan = $this->galery_model->get_whatsapp_scans(['id_solicitud' => $id_solicitude]); 
        if (!empty($ws_scan)) {
            $data['images']['ws']['img']  = $this->galery_model->search_images(['id_solicitud' => $id_solicitude, 'scan_reference' => $ws_scan->id ]);
            $data['images']['ws']['pagare']  = $this->Solicitud_m->get_solicitud_pagare($id_solicitude);
            $data['images']['ws']['data']  = $ws_scan;
        }
        
        $this->load->view('gestion/box_galery', ['docs'=>$data['images'],'solicitude' => $solicitude[0]]);
        
    }
    public function getverificacion_Whatsapp()
    {
        $data = $this->input->post();

        $whatsapp = $this->Solicitud_m->get_agenda_whatsapp(["documento" => $data['documento'],"status_chat" => 'activo', "orden" => "fecha_ultima_recepcion DESC", 'limit' => 1]);
        $ws_scan = $this->galery_model->get_whatsapp_scans(['id_solicitud' => $data['id_solicitud']]);         
        $end_point = CHATBOT_URL."chatbot/chatbot_live/startbiometria";
        
        if (empty($ws_scan)) {
            $flow = $data['flow'];
        } else {
            if ($ws_scan->status == 'vencido' && $data['action'] == 'btinit_biometria_ws') {
                if ($ws_scan->front == 2 && $ws_scan->back == 2 && $ws_scan->video == 2) {
                    $flow = [
                        "analisis_13_face"  => 0,
                        "analisis_13_front" => 1,
                        "analisis_13_back"  => 2,
                        "analisis_13_video" => 2
                    ];
                } else {
                    $flow = [
                        "analisis_13_face"  => 0,
                        "analisis_13_front" => (($ws_scan->front == 2) ? 2 : 0),
                        "analisis_13_back"  => (($ws_scan->back  == 2) ? 2 : 0),
                        "analisis_13_video" => (($ws_scan->video == 2) ? 2 : 0)
                    ];
                }
            } else {
                $flow = $data['flow'];
            }
        }
        $parametros = array(
            'telefono'      => $whatsapp[0]['from'],
            'documento'     => $data['documento'], 
            'id_solicitud'  => $data['id_solicitud'],
            'flow'          => json_encode($flow),
            'mensaje'       => $data['mensaje'],
        );
        // $parametros = array(
        //     'telefono'      => AUTHORIZATION::encodeData($whatsapp[0]['from']),
        //     'documento'     => AUTHORIZATION::encodeData($data['documento']),
        //     'id_solicitud'  => AUTHORIZATION::encodeData($data['id_solicitud']),
        //     'flow'          => AUTHORIZATION::encodeData(json_encode($flow))
        // );
        $request = Requests::post($end_point, [], $parametros);
        $response['telf'] = $whatsapp[0]['from'];
        $response['resp'] = $request->body;
        echo json_encode($response); 
    }

    public function validacion_biometria_whatsapp()
    {
        $data = $this->input->post();
        $request = $this->galery_model->update_whatsapp_scans(['id' => $data['id_whatsapp_scan'], 'id_solicitud' => $data['id_solicitud'],  'data' => ['respuesta_identificacion' => $data['texto'] , 'resultado' => $data['resultado']]]);
               
        $end_point = URL_API_IDENTITY."api/jumio/auth/jumio_ws";
        $request = Requests::post($end_point, [], ['id_solicitud'  => $data['id_solicitud']]);
        $response['resp'] = $request->body;
        
        if ($request > 0 ) {
            $response['success'] = true;
        }else{
            $response['success'] = false;
            $response['resperr'] = $request;
        }
        echo json_encode($response); 
    }

    public function change_type_image()
    {
        $data = $this->input->post();
        $datos['data1'] = $this->galery_model->search_images(['id_solicitud' => $data['id_solicitud'], 'solicitud_imagenes.id' => $data['img_1']])[0];
        $datos['data2'] = $this->galery_model->search_images(['id_solicitud' => $data['id_solicitud'], 'solicitud_imagenes.id' => $data['img_2']])[0];

        $IMG_1 = $datos['data1']['patch_imagen'];
        $IMG_2 = $datos['data2']['patch_imagen'];
        
        $cambio[] = $this->galery_model->update_image(['id'=>$data['img_1']], ['patch_imagen'=>$IMG_2]);
        $cambio[] = $this->galery_model->update_image(['id'=>$data['img_2']], ['patch_imagen'=>$IMG_1]);

        $response['data'] = $cambio;
        $response['success'] = true;
        echo json_encode($response);
    }


    public function search($id_solicitud = NULL)
    {
        if (isset($id_solicitud)) {
            $params['id_solicitud'] = $id_solicitud;
        }
        $params['order'] = [['fecha', 'desc'], ['hora', 'desc'],['track_gestion.id','asc']];

        return $this->tracker_model->search($params);
    }

    public function order_tracker($tracks)
    {
        $aux = [];
        foreach ($tracks as $key => $track) {
            $track['string_date'] = date_to_string($track['fecha'], 'd F a');
            $track['hora']        = date('h:i A', strtotime($track['hora']));

            if (!isset($aux[$track['string_date']])) {
                $aux[$track['string_date']]['style']  = month_style($track['fecha']);
                $aux[$track['string_date']]['tracks'] = [];

            }
            array_push($aux[$track['string_date']]['tracks'], $track);
        }

        return $aux;
    }

    private function _get_metrics($id_solicitude)
    {
        return $this->Metrics_model->getAnalisis($id_solicitude);
    }

    private function _get_solicitude($id_solicitude)
    {
        return $this->Solicitud_m->getSolicitudes(['id' => $id_solicitude]);
    }

    public function get_data_bank($id_solicitude)
    {
        return $this->Solicitud_m->getDatosBancarios($id_solicitude);
    }
    
    public function get_datos_personales($id_solicitude)
    {
        return $this->Solicitud_m->getDatosPersonales($id_solicitude);
    }    
    
    public function get_references($id_solicitude)
    {


        
        $solicitud = $this->Solicitud_m->getSolicitudesBy(['id_solicitud' => $id_solicitude]);
        
        $aux['referencia_familiar'] = [];
        $aux['referencia_personal'] = [];

        
        if(!empty($solicitud) && $solicitud[0]->id_situacion_laboral == 3){
            $references = $this->Solicitud_m->getSolicitudDatosLaborales($id_solicitude);
            foreach ($references as $key2 => $referenceI) {
                   array_push($aux['referencia_personal'], $referenceI);
            }
        } 

        $references = $this->Solicitud_m->getSolicitudReferencia($id_solicitude);
        foreach ($references as $key => $reference) {
            if (strtoupper($reference['Tipo_Parentesco']) == 'FAMILIAR') {
                array_push($aux['referencia_familiar'], $reference);
            } else {
                array_push($aux['referencia_personal'], $reference);
            }
        }
    //    var_dump($aux);die;
        return $aux;
    }

    public function get_referencias_botones($id_solicitud)
    {
        $referencias_botones = $this->Solicitud_m->getSolicitudReferenciaBotones($id_solicitud);
        $aux['referencia_titular_botones'] = [];
        $aux['referencia_familiar_botones'] = [];
        $aux['referencia_personal_botones'] = [];
        $aux['referencia_laboral_botones'] = [];
        $aux['referencia_titular_ind_botones'] = [];
        $aux['referencia_titular_prov_1'] = [];
        $aux['referencia_titular_prov_2'] = [];
        foreach ($referencias_botones as $reference) {
            if ($reference['id_tipo_verificacion'] == 1) {
                array_push($aux['referencia_titular_botones'], $reference);
            } else if ($reference['id_tipo_verificacion'] == 2) {
                array_push($aux['referencia_familiar_botones'], $reference);
            } else if ($reference['id_tipo_verificacion'] == 3) {
                array_push($aux['referencia_personal_botones'], $reference);
            } else if ($reference['id_tipo_verificacion'] == 4) {
                array_push($aux['referencia_laboral_botones'], $reference);
            } else if ($reference['id_tipo_verificacion'] == 5) {
                array_push($aux['referencia_titular_ind_botones'], $reference);
            } else if ($reference['id_tipo_verificacion'] == 6){
                array_push($aux['referencia_titular_prov_1'], $reference);                
            } else if ($reference['id_tipo_verificacion'] == 7){
                array_push($aux['referencia_titular_prov_2'], $reference);                
            }
        }
        return $aux;        
    }    

    public function get_terms($id_solicitude)
    {
        return $this->Solicitud_m->getSolicitudCondicion($id_solicitude);
    }    
    
     public function get_idconsulta($id_solicitude)
    {
        return $this->medio_contacto->getIdConsulta($id_solicitude);
    }
    
    public function get_telefonos($documento, $analisis = "")
    {
        $tipo_buro = "";
        if (!empty($analisis)){
          $tipo_buro = $analisis[0]['buro'];  
        }        
        return $this->medio_contacto->getTelefonos($documento,$tipo_buro);
    }
    
    public function get_celulares($documento, $analisis = "")
    {
        $tipo_buro = "";
        if (!empty($analisis)){
            $tipo_buro = $analisis[0]['buro'];
        }
        return $this->medio_contacto->getCelulares($documento, $tipo_buro);
    }  
    
    public function get_cant_cred($documento)
    {
        return $this->Solicitud_m->getCantCred($documento);
    }  
    
    public function get_txt($id_solicitud)
    {
        return $this->Solicitud_m->getTxt($id_solicitud);
    }     
    
    
    public function get_res_whatsapp($documento)
    {
        return $this->medio_contacto->getResWhatsApp($documento);
    }     
    
    
    public function get_pasos($id_paso)
    {
        return $this->pasos->getSolicitudPaso($id_paso);
    }   
    
    
    public function get_mail_log($mail)
    {
        return $this->Solicitud_m->getMailLog($mail);    
        
    }
    
    public function get_parentesco()
    {
        return $this->Solicitud_m->getParentesco();    
        
    }      
    
    public function get_atrasos($id_cliente)
    {
        return $this->Solicitud_m->getDiasAtraso($id_cliente);
    }   

    public function get_expenditure($id_solicitude)
    {
        return $this->Solicitud_m->getSolicitudDesembolso($id_solicitude);
    }


    public function get_tracks($id_solicitude)
    {
        return $this->order_tracker($this->search($id_solicitude));
    }

    public function get_tracker_options()
    {
        $data['actions'] = [];
        $tipo_operador = $this->session->userdata('tipo_operador');
        foreach ($this->operaciones_model->search(['estado' => 1, 'idtipo_operador' => $tipo_operador]) as $key => $action) {
            array_push($data['actions'], $action);
            if ($action['idgrupo_respuesta'] != 0) {
                $data['actions'][$key]['options'] = $this->operaciones_model->search_reasons(['idgrupo_respuestas' => $action['idgrupo_respuesta']]);
            }
        }

        return $data;
    }

    private function _get_ranges_metrics()
    {
        $params['estado'] = 1;

        return $this->Metrics_model->get_ranges($params);
    }

    /*public function review($id_solicitude)
    {
        $data['indicadores']    = $this->_get_metrics($id_solicitude);
        $data['ranges']         = $this->_get_ranges_metrics();
        $metrics['indicadores'] = $data['indicadores'][0];

        foreach ($data['ranges'] as $key => $rango) {
            $campo = $rango['base_datos']."-".$rango['campo'];
            var_dump($campo);
            $metricas = [
                "$campo" => 'sdfs'
            ];
            var_dump($metricas);die;
        }
        $metrics['indicadores'] = $data['indicadores'][0];



        //$this->output->enable_profiler(TRUE);
        $this->load->view('layouts/adminLTE');
        $this->load->view('gestion/box_metrics', ['indicadores' => $data['indicadores'][0], 'ranges' => $data['ranges']]);
    }*/

    public function resetTelefono($id_solicitude)
    {
        return $this->Solicitud_m->resetTelefono($id_solicitude);
    }

    public function resetEmail($id_solicitude)
    {
        return $this->Solicitud_m->resetEmail($id_solicitude);
    }

    public function get_solicitud_analisis($id_solicitude)
    {
        return $this->Solicitud_m->getSolicitudAnalisis(['id' =>$id_solicitude]);
    }

    public function get_credits($id_credit)
    {
        $credits = $this->credito_model->search(['id' => $id_credit]);
        if(!empty($credits))
        {

            $credits[0]['quotas'] = $this->quota_model->search(['id_credito' => $id_credit]);
        }

        return $credits;
    }

    private function _get_list_client_by_credit_status($status)
    {
        $params['order']                                    = [['solicitud.id', 'desc'],['solicitud.fecha_ultima_actividad', 'desc']];
        $params['>=']['solicitud.fecha_ultima_actividad']   = date('Y-m-d', strtotime('Y-m-d 23:59:59 - 30 days'));
        $params['<=']['solicitud.fecha_ultima_actividad']   = date('Y-m-d 23:59:59');
        $params['creditos.estado']   =   strtolower($status);
        return $this->Solicitud_m->simple_list($params);
    }

    /**
     * [Evalua todos los creditos del cliente e informa cual es su estado general, prioridad mora]
     * @param  [Array] $credits [Creditos]
     * @return [Array]          [Resultado]
     */
    private function _client_status($credits)
    {
        $mora = FALSE;
        $response = ['status_credit' => '','id_credit' => '','color_credit' => ''];
        foreach ($credits as $key => $credit)
        {
            if(strtolower($credit['estado']) == 'mora' && !$mora )
            {
                $response['status_credit'] = strtolower($credit['estado']);
                $response['id_credit'] = $credit['id'];
                $response['color_credit'] = 'red';
                return $response;
            }else
            {
                $response['status_credit'] = strtolower($credit['estado']);
                $response['id_credit'] = $credit['id'];

                switch (strtolower($credit['estado']))
                {
                    case 'vigente':
                        $response['color_credit'] = 'green';
                        break;
                    case 'cancelado':
                        $response['color_credit'] = 'blue';
                        break;
                }
            }
        }

        return $response;
    }

    /**
     * [Evalua un credito e informa cual es su estado]
     * @param  [type] $credit [description]
     * @return [type]         [description]
     */
    private function _credit_status($credit)
    {
        $response = ['status_credit' => '','id_credit' => '','color_credit' => ''];
        if(!empty($credit))
        {
            $response['status_credit'] = strtolower($credit[0]['estado']);
            $response['id_credit'] = $credit[0]['id'];
            switch (strtolower($credit[0]['estado'])) {
                case 'value':
                    $response['color_credit'] = 'red';
                    break;
                case 'vigente':
                    $response['color_credit'] = 'green';
                    break;
                case 'cancelado':
                    $response['color_credit'] = 'blue';
                    break;
            }
        }

        return $response;
    }

    public function get_offer($id_solicitud, $solicitud)
    {
        $beneficios =  $this->beneficio_model->search(['id_solicitud' => $id_solicitud]);
        if(!empty($beneficios)){
            foreach($beneficios as $key => $beneficio){
                $monto_minimo = 150000;
                
                $aumento = 50000;

                /*if( $this->session->userdata('tipo_operador') == ID_OPERADOR_SUPERVISOR ){
                    $beneficio['monto_maximo'] = (int)1000000;
                }*/

                if(strtoupper($solicitud[0]['tipo_solicitud']) == "PRIMARIA" && $this->session->userdata['idoperador'] == 12){
                    if($solicitud[0]["id_situacion_laboral"] == 1 || $solicitud[0]["id_situacion_laboral"] == 4  || $solicitud[0]["id_situacion_laboral"] == 7){
                        $beneficio['monto_maximo'] = 500000;
                    }elseif ($solicitud[0]["id_situacion_laboral"] == 3 ){
                        $beneficio['monto_maximo'] = 400000;
                    }
                }
                
                $rep = ((int)$beneficio['monto_maximo'] / 50000);
                
                
                //Calculo de diff de repeticiones entre el rango de aumento y el valor minimo permitido.
                $pivot = $monto_minimo / $aumento ;

                if(strtoupper($solicitud[0]['tipo_solicitud']) === "RETANQUEO"){
                   
                    $rep = ((int)$beneficio['monto_maximo'] / 10000);
                    $aumento = 10000;
                    //Calculo de diff de repeticiones entre el rango de aumento y el valor minimo permitido.
                    $pivot = $monto_minimo / $aumento;
                }

                
                $monto_maximo = (int)$beneficio['monto_maximo'];

                $montos_parciales = [];
                //monto incial
                array_push($montos_parciales, 150000);
                
                for($i = $pivot; $i < $rep ; $i++){
                    
                    $monto_minimo += $aumento;
                    
                    if($monto_minimo > $monto_maximo){
                        $diff = $monto_minimo - $monto_maximo;
                        $ajuste = $monto_minimo - $diff;
                        array_push($montos_parciales, $ajuste);
                    }else{
                        array_push($montos_parciales, $monto_minimo);
                    }

                }

                if($monto_minimo < $monto_maximo){
                    $diff = $monto_maximo - $monto_minimo;
                    $ajuste = $monto_minimo + $diff;
                    array_push($montos_parciales, $ajuste);
                }

                $beneficios[$key]['montos_parciales'] = $montos_parciales;
                
                $rep_plazos = (int)$beneficio['plazo_maximo'];
                
                $plazos = [];
                $plazo_minimo = 0;
                for($i= 0; $i < $rep_plazos; $i++){
                    $plazo_minimo++;
                    array_push($plazos, $plazo_minimo);
                }
                $beneficios[$key]['plazos'] = $plazos;
                
            }
        }
        
        return $beneficios;
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

    private function _get_veriff($id_solicitude)
    {
        $aux = [];
        $params['id_solicitud'] = $id_solicitude;
        $params['order'] = [['id', 'desc']];
        
        $response = $this->jumio_model->search_veriff($params);
        foreach ($response as $key => $value) 
        {
            $aux[] = $value;
            
            foreach ($value as $index => $res) 
            {   
                if(isset($this->_status_veriff[$index]))
                {
                    if(is_null($res)) { 
                        $res = "0";
                        $res= $value['status_session'];
                    } else if(isset( $this->_status_veriff[$index][$res])){
                        $aux[$key][$index] = $this->_status_veriff[$index][$res];
                    } else{
                       
                        $aux[$key][$index] = '';
                    }
                }
            }
        }
        
        return $aux;
    }

	private function _get_meta($id_solicitude)
    {
        $aux = [];
        $params['id_solicitud'] = $id_solicitude;
        $params['order'] = [['id', 'desc']];
        
        $response = $this->jumio_model->search_meta($params);
        foreach ($response as $key => $value) 
        {
            $aux[] = $value;
            
            foreach ($value as $index => $res) 
            {   
                if(isset($this->_status_meta[$index]))
                {
                    if(is_null($res)) { 
                        $res = "0";
                        $res= $value['status_session'];
                    } else if(isset( $this->_status_meta[$index][$res])){
                        $aux[$key][$index] = $this->_status_meta[$index][$res];
                    } else{
                        $aux[$key][$index] = '';
                    }
                }
            }
        }        
        return $aux;
    }

    public function get_banks()
    {
        return $this->bank_model->search(['id_estado_banco' => 1]);
    }   
    
    public function get_types_account()
    {
        return $this->type_account_model->search(['id_estado_tipocuenta' => 1]);
    }    

    private function _get_images($id_solicitude)
    {
        $data['images']['options'] = $this->galery_model->search_required(['origen' => 'BACK', 'estado' => 1]);
        $data['images']['data']    = $this->galery_model->search_images(['id_solicitud' => $id_solicitude]);
        // Obtengo informacion si las imagenes fueron analizadas por Jumio.
        $data['images']['origin']  = array_merge($this->_get_jumio($id_solicitude), $this->_get_eid($id_solicitude));

        //var_dump($data['images']);die;
        return $data;
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
    
    public function update_image()
    {
        $id_solicitud = $this->input->post('id_solicitud');
        //$end_point = "https://api-identity.solventa.co/api/jumio/Cron/reprocesar_imagenes?id_solicitud=$id_solicitud";

        $end_point = URL_API_IDENTITY."api/veriff/auth/download_media?id_solicitud=$id_solicitud";
        
        $veriff_scan = $this->Solicitud_m->getVeriff_scan_all($id_solicitud);
        
        if(!empty($veriff_scan) && $veriff_scan[0]['id_integracion'] == 2)
            $end_point = URL_API_IDENTITY."api/veriff/auth_video/download_media?id_solicitud=$id_solicitud";

        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp){
            curl_setopt($fp, CURLOPT_TIMEOUT, 300);
        });
        $request = Requests::get($end_point, array(),array('hooks' => $hooks));
        $response = json_decode($request->body);

        echo json_encode($response);die;
    }  

    
    private function _get_order($order, $direction = 'desc')
	{
		$response = [['solicitud.id', 'desc'],['solicitud.fecha_ultima_actividad', 'desc']];
		if(!empty($order))
		{
			switch ($order) 
			{
				case 'date_ultima_actividad':
					$response = [['solicitud.fecha_ultima_actividad', $direction]];
					break;
				case 'operador_nombre_pila':
					$response = [['operadores.nombre_pila', $direction]];
					break;
				case 'banco_resultado':
					$response = [['datos_bancarios.respuesta', $direction]];
					break;
				default:
					$response =[['solicitud.'.$order,$direction]];
					break;
			}
		}

				return $response;
	}
        
    private function _set_date_range($date_start, $date_end = null, $format='Y-m-d')
	{
		$response = [];
		if(!isset($date_end) || empty($date_end))
		{
			$end = date('Y-m-d 23:59:59');
		}else
		{
			$end = date_create_from_format($format, trim($date_end));
			$end = $end->format('Y-m-d 23:59:59');
		}
		if(isset($date_start) && !empty($date_start))
		{
			$start 	= date_create_from_format($format, trim($date_start));
			$response['>=']['solicitud.fecha_ultima_actividad'] = $start->format('Y-m-d 00:00:00');
     		$response['<=']['solicitud.fecha_ultima_actividad'] = $end; 
		}

		return $response;
	}

    private function _get_botones_revision()
    {
        $botones = $this->operaciones_model->get_by(['idgrupo_respuesta' => 10, 'order' => [['orden','ASC']]]);
        return $botones;
    }

     public function render_gestion()
    {

        $data = array(

        "id_credito"            => $this->session->flashdata('id_credito'),
        "id_solicitud"          => $this->session->flashdata('id_solicitud'),
        "cola"                  => $this->session->flashdata('cola'),
        "id_agente"             => $this->session->flashdata('id_agente'),
        "telefono"              => $this->session->flashdata('telefono'),
        "cola"                  => $this->session->flashdata('cola'),
        "render_view"           => "true",

        );

        //var_dump($id_cliente,$cola,$id_agente,$telefono);die;

        $data['title']   = 'Gestion';
        $data['heading'] = 'Gestion';
        $this->load->view('layouts/adminLTE__header', $data);
        $this->load->view('gestion/gestion_cliente_main', $data);
        $this->load->view('layouts/adminLTE__footer', $data);

    }




    public function open_client_case($expediente){
        
        //$documento = $this->input->post('documento');
        $tipo_opera = $this->session->userdata('tipo_operador');

        if (!is_null($expediente) && $expediente != 0) {
            
            if ($tipo_opera==1 || $tipo_opera==4 || $tipo_opera==11 ) {

                $this->session->set_userdata('id_solicitud', $expediente);
                $this->session->set_userdata('id_credito', null);
                $this->session->set_userdata('render_view', "true");
                redirect(base_url() . "renderGestion"); 
            } else if($tipo_opera==5 || $tipo_opera==6 ){

                $this->session->set_userdata('id_solicitud', null);
                $this->session->set_userdata('id_credito', $expediente);
                $this->session->set_userdata('render_view', "true");
                redirect(base_url() . "atencion_cobranzas/cobranzas"); 
            }
            
            
        }
            
    }
    public function get_mail_template(){
        $canal=$this->input->post('canal');
        // var_dump($canal);
        $documento = $this->input->post('documento');
        $template_accordeon=[];
        $array_maker_templates=[];
        $aux4 = $this->Solicitud_m->get_template_mail(["canal" =>$canal]);
        // echo json_encode($aux4);die;
        foreach ($aux4 as $key=> $value){
         $consulta_con_variable = $value['query_contenido'];
            if (isset($consulta_con_variable)){
                $consulta_sin_variable= str_replace('$documento', $documento, $consulta_con_variable);
                $rs_result = $this->Solicitud_m->rs_result($consulta_sin_variable);
                
                if(isset($rs_result) &&  count($rs_result['result']) > 0){
                $array_maker_templates=[
                        'id' => $value['id'],
                        'id_logica' => $value['id_logica'],
                        'nombre_logica' => $value['nombre_logica'],
                        'nombre_template' => $value['nombre_template']
                ];
                array_push($template_accordeon, $array_maker_templates);

                }
            }
        }
        // echo json_encode($aux4);
        echo json_encode($template_accordeon);
    }
   

    public function get_agenda_mail($documento_solicitante)
    {
        $aux3 = $this->Solicitud_m->get_agenda_mail(["documento" =>$documento_solicitante]);
        echo json_encode(['data'=>$aux3]);

    }
 public function get_agenda_telefonica($documento_solicitante)
    {
        $aux2 = $this->Solicitud_m->get_agenda_personal_solicitud(["documento" =>$documento_solicitante]);
        echo json_encode($aux2);

    }

    public function make_template_send($id_solicitud,$canal,$tipo_template)
    {
        $templates_general = $this->chat->get_template(["canal"=>$canal,"tipo_template"=>$tipo_template]);
        $column_general = array_column($templates_general,"grupo");
        $uniquearray_general =array_unique($column_general);
        $array_aux_general=[];
        $dato_string_general=[];

        foreach($uniquearray_general as $keyu_general=>$valueu_general){
            $sub_grupo_general = [];
            $array_traduction_msj=[];
            foreach($templates_general as $keyt_general => $valuet_general){
                $traduction_msj_general=mensaje_whatapp_maker($valuet_general["id"],$id_solicitud);
                if($valuet_general["grupo"]==$valueu_general){
                    if ($traduction_msj_general["ok"]== true){
                        $valuet_general["msg_string"]=$traduction_msj_general["message"];
                        array_push($sub_grupo_general, $valuet_general);
                    }
                }
            } 

            array_push($array_aux_general,[
                "grupo"=>$valueu_general,
                "template"=>$sub_grupo_general,
            ]);
        }
        foreach ($templates_general as $key_general=> $value_general) {
            $traduction_msj_general=mensaje_whatapp_maker($value_general["id"],$id_solicitud);
            if($traduction_msj_general["ok"]==true){
                $value_general["msg_string"] = $traduction_msj_general["message"];
                array_push($dato_string_general, $value_general);
            }
        }
        $array2=array('dato_string_general'=>($dato_string_general),'grupo_template'=>($array_aux_general));
        echo json_encode($array2);
    }

    public function getAgendaOperadores($id_operador = NULL){
        $params['id_operador'] = $id_operador;
        $agendaOperadores = $this->AgendaOperadores_model->getAgendaOperadores($params);
        $first = [];
        $second = [];
        $Third = [];
        $last = [];
        $final = [];
        foreach ($agendaOperadores as $key => $agendaOperador){
            $date = Carbon::parse($agendaOperador['fecha_hora_llamar']);
            $now = Carbon::now();
            // $now = Carbon::parse('2021-04-21 11:45:00'); //hora de prueba.
            $diffMinutes = $date->DiffInMinutes($now, false) * -1;
            $agendaOperadores[$key]['diffMinutes'] = $diffMinutes;
            $agendaOperadores[$key]['now'] = $now->format('d/m/Y g:i A');
            $fecha_hora_llamar = $date->format('d/m/Y g:i A');
            $agendaOperadores[$key]['fecha_hora_llamar'] = $fecha_hora_llamar;
            
            if($diffMinutes >= 6 && $diffMinutes <= 10 ){
                $agendaOperadores[$key]['box-color'][0] = 'bg-yellow';
                $agendaOperadores[$key]['box-color'][1] = 'box-warning';
                $Third[$key] = $agendaOperadores[$key];
                
            }else if($diffMinutes >= 0 && $diffMinutes <= 5 ){
                $agendaOperadores[$key]['box-color'][0] = 'yellow';
                $agendaOperadores[$key]['box-color'][1] = 'box-yellow';
                $second[$key] = $agendaOperadores[$key];
                
            }else if($diffMinutes < 0 ){
                $agendaOperadores[$key]['box-color'][0] = 'bg-red';
                $agendaOperadores[$key]['box-color'][1] = 'box-danger';
                $first[$key] = $agendaOperadores[$key];
            }else{
                $agendaOperadores[$key]['box-color'][0] = 'bg-gray';
                $agendaOperadores[$key]['box-color'][1] = 'box-default';
                $last[$key] = $agendaOperadores[$key];
            }
            
        }

        $final = array_merge($first, $second, $Third, $last);
        $agendaOperadores['agenda_operadores'] = $final;
        
        if ($this->input->is_ajax_request()) {
            return $this->load->view('gestion/box_casos_agendados', $agendaOperadores);
        }else{
            return $agendaOperadores;
        }
        
    }
    public function deleteAgendaOperador(){
        $params['id'] = $this->input->post('id');
        $delete = $this->AgendaOperadores_model->deleteAgendaOperador($params);
        
		$response = array();
        if($delete >= 1) 
			$response['success']  = true;
        else     
            $response['success']  = false;
        
		echo json_encode($response);
        
    }

    private function GetApiBuros($endPoint, $method = 'POST',  $params=[] ){
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

        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        if($res->success){
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

    public function get_tipo_ajustes($id){
        $ajustes = $this->Solicitud_m->getClase_Ajuste(['id_tipo_ajuste'=> $id ]);
        foreach ($ajustes as $key => $value) {
            $value->requisitos = $this->Solicitud_m->getrequisitos_Clase($value->id);
            $datos[] = $value;
        }
        $response['data']  = $datos;
        $response['success']  = true;
        echo json_encode($response);
    }

    public function save_sol_ajustes(){
        $data = $this->input->post();
        $data['fecha_solicitud'] = Carbon::now()->format('Y-m-d G:i:s');
        $data['id_operador'] = $this->session->userdata('idoperador');
        $data['estado'] = 0;        
        $ajustes = $this->Solicitud_m->saveSolicitudAjustes($data);        
        var_dump($ajustes);
    }

    public function update_sol_ajustes(){
        $datos = $this->input->post();
        // $datos['fecha_proceso'] = Carbon::now()->format('Y-m-d G:i:s');
        // $datos['id_operador_procesa'] = $this->session->userdata('idoperador');
        $ajustes = $this->Solicitud_m->updateSolicitudAjustes($datos['id'],$datos);
        echo json_encode($ajustes);
    }
    
    public function get_solicitud_ajustes($documento) {
        $resp['data'] = $this->Solicitud_m->getSolicitudAjustes($documento); 
        echo json_encode($resp);
    }

    public function getSolicitudAjustes($id_operador){
        $solicitud_ajustes = $this->Solicitud_m->getSolicitudAjustesBy(['id_operador' => $id_operador,'recibido' => 0,'estado_in' =>  [1, 3]]);
        if ($this->input->is_ajax_request()) {
            $sa['solicitud_ajustes'] = $solicitud_ajustes;
            return $this->load->view('gestion/box_solicitud_ajustes', $sa);
        }else{
            return $solicitud_ajustes;
        }
    }
	
	public function getListasRestrictivasView($solicitudId)
	{
		$result = $this->Solicitud_m->getListasRestrictivas($solicitudId);
		$var_box_info_endeudamiento = [
			'data' => $result
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/listas_restrictivas',$var_box_info_endeudamiento, TRUE);
		echo $view;
	}
	
	public function getReferenciasCruzadasView($documento)
	{
		$result = $this->Solicitud_m->getReferenciasCruzadas($documento);
		$var_box_info_endeudamiento = [
			'data' => $result,
			'documento_solicitud' => $documento
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/referencias_cruzadas',$var_box_info_endeudamiento, TRUE);
		echo $view;
		
	}
	
	public function getReferenciasCruzadasEmailView($documento)
	{
		$result = $this->Solicitud_m->getReferenciasCruzadasEmail($documento);
		$var_box_referencias_cruzadas_email = [
			'data' => $result,
			'documento_solicitud' => $documento
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/referencias_cruzadas_email',$var_box_referencias_cruzadas_email, TRUE);
		echo $view;
		
	}
    
	public function getSectorFinancieroAlDiaView($documento)
	{
		$result = $this->Solicitud_m->getSectorFinancieroalDia($documento);
		$var_box_info_endeudamiento = [
			'data' => $result
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/financiero_al_dia',$var_box_info_endeudamiento, TRUE);
		echo $view;
    }
    
    public function getSectorFinancieroEnMoraView($documento)
	{
		$result = $this->Solicitud_m->getSectorFinancieroEnMora($documento);
		$var_box_info_endeudamiento = [
			'data' => $result
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/financiero_en_mora',$var_box_info_endeudamiento, TRUE);
		echo $view;
    }
	
	public function getSectorRealAlDiaView($documento)
	{
		$result = $this->Solicitud_m->getSectorRealAlDia($documento);
		$var_box_info_endeudamiento = [
			'data' => $result
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/real_al_dia',$var_box_info_endeudamiento, TRUE);
		echo $view;
	}
	
	public function getSectorRealEnMoraView($documento)
	{
		$result = $this->Solicitud_m->getSectorRealEnMora($documento);
		$var_box_info_endeudamiento = [
			'data' => $result
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/real_en_mora',$var_box_info_endeudamiento, TRUE);
		echo $view;
	}
	
	public function getSectorFinancieroExtinguidoView($documento)
	{
		$result = $this->Solicitud_m->getSectorFinancieroExtinguido($documento);
		$var_box_info_endeudamiento = [
			'data' => $result
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/financiero_extinguido',$var_box_info_endeudamiento, TRUE);
		echo $view;
	}
	
	public function getSectorRealExtinguidoView($documento)
	{
		$result = $this->Solicitud_m->getSectorRealExtinguido($documento);
		$var_box_info_endeudamiento = [
			'data' => $result
		];
//		var_dump($result);
//		die();
		$view = $this->load->view('gestion/partials/real_extinguido',$var_box_info_endeudamiento, TRUE);
		echo $view;
	}
    
	public function updateverificacion_galery() {
		$data = $this->input->post();
        $result['result'] = $this->Solicitud_m->updateverificacion_galery($data);
        $result['status'] = 'OK';
        echo json_encode($result);
	}
    
	public function getverificacion_galery($id) {

        if ($this->session->userdata('tipo_operador') == 1) {            
            $result['result'] = $this->Solicitud_m->getValidacionObservacion($id);
            if (count($result['result'] ) == 0) {
                $result['status'] = false;
            } else {
                $result['status'] = true;
            }
        } else {
            $result['status'] = false;
        }
        echo json_encode($result);
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
class ApiSolicitudDevolucion extends REST_Controller
{      
	protected $end_folder = 'public/devoluciones';
	private $_free_methods = array('buscar','listar');
	const TIPO_ADMINISTRADOR = ['ADMINISTRADOR'];
	const SEPARADOR = ';';
    const DELIMITADOR = '"';
    protected $file_config = [];
    protected $file_field = '';

	public function __construct()
	{

		parent::__construct();
		$method = $this->uri->segment(3);

		$this->load->library('User_library');
		$auth = $this->user_library->check_token();

		if($auth->status == parent::HTTP_OK || in_array($method, $this->_free_methods))
		{
			// MODELS
			$this->load->model('Solicitud_m','solicitud_model',TRUE);
			$this->load->model('Cliente_model', 'cliente_model', TRUE);
			$this->load->model('Credito_model', 'credito_model', TRUE);
			$this->load->model('Devolucion_model', 'devolucion_model', TRUE);
			$this->load->model('PagoCredito_model', 'pago_credito_model', TRUE);


			// LIBRARIES
			$this->load->library('form_validation');
			$this->load->library('Infobip_library');
			$this->load->helper('formato_helper');
			$this->load->library('SendMail_library');
			$this->load->library('Pepipost_library');


		}else{
			$this->session->sess_destroy();
			$this->response(['redirect'=>base_url('login')],$auth->status);
		}
	}

	public function get_solicitudes_devolucion_paginada_doc_get()
	{
		$params['LITERAL'] = [];
		if ($this->get('documento') != 'false') {
			$params['documento'] = $this->get('documento');
		} else if($this->get('estado') != 'false') {
			$params['estado'] = $this->get('estado');
		}
		$params['not_estado'] = 3;
		$lista_solicitudes = $this->devolucion_model->getSolicitudes($params);
		$response['data'] = $lista_solicitudes;	
		$status = parent::HTTP_OK;
		$this->response($response, $status);
	}

	public function get_solicitudes_devolucion_paginada_get()
	{
		$params['LITERAL'] = [];
		$params['estado'] = $this->get('estado');
		$params['order'] = 'fecha';
		$params['sentido'] = 'DESC';
		$limit = $this->get('start');
		$offset = $this->get('length');

		$lista_solicitudes = $this->devolucion_model->getSolicitudes($params,$limit, $offset);

		$response['data'] = $lista_solicitudes;	
		$status = parent::HTTP_OK;
		$this->response($response, $status);
	}


	public function get_solicitudes_devolucion_get()
	{
		$lista_solicitudes = $this->get_devoluciones($this->get());

		$response['data'] = $lista_solicitudes;	
		$status = parent::HTTP_OK;
		$this->response($response, $status);
	}

	public function consultar_devolucion_get($id_cliente)
	{
		if(!is_null($id_cliente)){

			$credito = $this->credito_model->get_creditos_cliente(['id_cliente' => $id_cliente, 'order' =>'c.fecha_otorgamiento', 'sentido' => 'DESC', 'limit' => '1', 'where' => 'cd.monto_cobrado > 0']);
			$devolucion = FALSE;
			
			if(empty($credito)){
				$response['status']['ok'] = FALSE;
				$response['message'] = 'Datos invalidos';
				
			} else{
				
				if($credito[0]['estado_credito'] == 'cancelado' && $credito[0]['monto_cobrar'] < 0)
				$devolucion = TRUE;
				
				if($credito[0]['estado_credito'] == 'vigente' && $credito[0]['monto_cobrado'] > 0)
				$devolucion = TRUE;

			}
			
			$response['status']['ok'] = TRUE;
			$response['devolucion'] = $devolucion;	
			
		} else {
			$response['status']['ok'] = FALSE;
			$response['message'] = 'Datos invalidos';
		}
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function consultar_datos_devolucion_get($id_cliente)
	{
		$data['pagos'] = [];
		if(!is_null($id_cliente)){

			$credito = $this->credito_model->get_creditos_cliente(['id_cliente' => $id_cliente, 'order' =>'c.fecha_otorgamiento', 'sentido' => 'DESC', 'limit' => '1', 'where' => 'cd.monto_cobrado > 0']);
			$data['cliente'] = $this->devolucion_model->getInfoDevolucion($id_cliente);
			if( !empty($credito)){
				
				if ((bool) $this->get('precarga')) {
					$data['pagos'] = $this->credito_model->get_pagos_detalle(['id_cliente' => $id_cliente, 'estado'=>1, 'limit' => 4, 'tipo' => 0]);	
				} else {
					$data['pagos'] = $this->credito_model->get_pagos_detalle(['id_cuota' => $credito[0]['id'], 'estado'=>1, 'tipo' => 0]);
				}
			}
			
			$data['solicitud'] = $this->solicitud_model->getSolicitudesBy(['id_cliente' => $id_cliente, 'limite' =>1]);
			if (!is_null($this->get('id_devolucion'))){
				$data['devolucion'] = $this->devolucion_model->getSolicitudes(['id_devolucion' => $this->get('id_devolucion')]);
				$Devoluciones = ['id_cliente' => $id_cliente, 'not_estado' => '3', 'limit' => 2, 'order' => 'id', 'sentido' => 'DESC'];
				$data['devoluciones'] = $this->devolucion_model->getSolicitudes($Devoluciones);
				$data['comprobantes'] = $this->devolucion_model->get_comprobantes_devolucion(['id_devolucion' => $this->get('id_devolucion'), 'origen' => 0]);
				$data['comprobantesDevolucion'] = $this->devolucion_model->get_comprobantes_devolucion(['id_devolucion' => $this->get('id_devolucion'), 'origen' => 1]);
				$data['comprobantesDevolucionPrecarga'] = $this->devolucion_model->get_comprobantes_devolucion(['id_devolucion' => $this->get('id_devolucion'), 'origen' => 2]);
				$data['pagosDevolucion'] = $this->devolucion_model->get_pagos_devolucion(['id_devolucion' => $this->get('id_devolucion')]);
			}
			//$data['comprobantes'] = $this->devolucion_model->get_solicitud_devolucion(['id_cuota' => $credito[0]['id']]);
			
			$response['status']['ok'] = TRUE;
			$response['data'] = $data;
		}else {
			$response['status']['ok'] = FALSE;
			$response['message'] = 'Datos invalidos';
		}
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}
	
	public function deleteComprobanteDevolucion_post() {
		if(!is_null($this->post('file'))){

			if(is_dir(dirname(BASEPATH) .'/public/devoluciones/comprobantes/' . date('Y') . '/' . date('m') )) {
				unlink(dirname(BASEPATH) .'/public/devoluciones/comprobantes/' . date('Y') . '/' . date('m').'/'.$this->post('file'));
				$response['status']['ok'] = TRUE;
			} else{
				$response['status']['ok'] = FALSE;
				$response['message'] = 'Directorio no encontrado';	
			}

		} else {
			$response['status']['ok'] = FALSE;
			$response['message'] = 'Datos invalidos';
		}
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
    }

	public function solicitar_devolucion_post() {

		//cargamos la tabla de solicitudes devoluciones
		if($this->post('monto')<=0 || is_null($this->post('forma')) || is_null($this->post('id_cliente')) || empty($this->post('pagos'))){
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['message'] = " Datos incompletos";
			$this->response($response, $status);
		}

		$dataSolicitud = [
			'fecha' => date('Y-m-d'),
			'hora' => date('H:i:s'),
			'id_cliente' => $this->post('id_cliente'),
			'forma_devolucion' => $this->post('forma'),
			'monto_devolver' => $this->post('monto'),
			'id_operador' => $this->session->userdata("idoperador"),
			'estado' => 0,
		];

		$id_solicitud = $this->devolucion_model->insertarSolicitud($dataSolicitud);

		//si se inserto la solicitud insertamos la relacion con el pago
		if($id_solicitud > 0){

			//consultamos la informacion de los pagos a devolver
			$pagos = explode(',', $this->input->post('pagos'));
			$solicitudes = [];
			foreach ($pagos as $key => $pago) {

				$info_pago = $this->credito_model->get_pagos_credito(['id_pago'=> $pago])[0];
				$dataPago = [
					'id_solicitud_devolucion ' => $id_solicitud,
					'id_pago ' => $info_pago->id_pago,
					'id_credito' => $info_pago->id_credito,
					'monto'=>$info_pago->monto,
				];
				//insertamos la relacion del pago a devolver
				$this->devolucion_model->insertarPagoDevolver($dataPago);
				$sol = $this->solicitud_model->getSolicitudesBy(['id_credito' => $info_pago->id_credito])[0]->id;
			}

			//insertamos los comprobantes de existir 
			$comprobantes = $this->post('comprobantes');
			if($comprobantes != ""){
				
				$comprobantes = explode(',',$comprobantes);
	
				foreach ($comprobantes as $key => $comprobante) {
	
					$dataComprobante = [
						'id_solicitud_devolucion ' => $id_solicitud,
						'comprobante' => '/public/devoluciones/comprobantes/' . date('Y') . '/' . date('m').'/'.$comprobante,
						'origen' => 0
					];
					//insertamos la relacion del pago a devolver
					$this->devolucion_model->insertarComprobanteDevolver($dataComprobante);
				}
			}

			//trackeamos
			$dataTrackGestion = [
				'id_solicitud'=>(int)$sol,
				'observaciones'=>'<b>SOLICITUD DE DEVOLUCION</b><br>Fecha: '.date('d-m-Y').
				                 '<br>Hora: '.date('H:i:s').'<br>Monto: $'.number_format($this->post('monto'),2, ',', '.').
								 '<br><b>DEVOLVER EN:</b><br>Banco: '.$this->post('banco').'<br>Tipo cuenta: '.$this->post('tipo').'<br>Numero: '.$this->post('cuenta'), 
				'id_tipo_gestion' => 190,
				'id_operador' => $this->session->userdata("idoperador")
			];
			$endPoint =  base_url('api/track_gestion');
			$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
			
			$response['status']['ok'] = TRUE;
			$response['message'] = 'Solicitud generada con exito';
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function agregar_devolucion_precarga_post() {

		$dataSolicitud = [
			'fecha' => date('Y-m-d'),
			'hora' => date('H:i:s'),
			'id_cliente' => $this->post('id_cliente'),
			'forma_devolucion' => 'PARCIAL',
			'monto_devolver' => $this->post('monto_devolver'),
			'id_operador' => 192,
			'comentario' => $this->post('comentario'),
			'estado' => 3,
		];

		$id_solicitud = $this->devolucion_model->insertarSolicitud($dataSolicitud);

		//si se inserto la solicitud insertamos la relacion con el pago
		if($id_solicitud > 0){

			$fichero_anio = dirname(BASEPATH) . '/public/devoluciones/comprobantes/' . date('Y');
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/devoluciones/comprobantes/' . date('Y') . '/' . date('m');
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700, true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700, true);
            }
            $fecha_creacion_archivo = date('Ymd_Hisu');
            $nombre_archivo = 'comprobante_'.$fecha_creacion_archivo;
            $config['upload_path'] = $ruta_guardar_archivo; 
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
                $archivo_ruta = '/public/devoluciones/comprobantes/' . date('Y') . '/' . date('m') . '/' .$nombre_archivo.$data['upload_data']['file_ext'];
                $status = parent::HTTP_OK;
                $response = [
                    'status'  => ['code' => $status, 'ok' => TRUE],
                    'message' => "Comprobante pago guardado",
					'url' => $archivo_ruta,
					"nombre" => $nombre_archivo.$data['upload_data']['file_ext']
                ];

				$dataComprobante = [
					'id_solicitud_devolucion ' => $id_solicitud,
					'comprobante' => $archivo_ruta,
					'origen' => 2
				];
				$this->devolucion_model->insertarComprobanteDevolver($dataComprobante);
                
               
            } else {
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->upload->display_errors();
            }
		}

		$status = parent::HTTP_OK;
		$this->response($response, $status);
	}
	
	public function update_devolucion_precarga_post() {

		//cargamos la tabla de solicitudes devoluciones
		if(is_null($this->post('id_devolucion')) || is_null($this->post('estado')) || $this->post('monto') <= 0  ){
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['message'] = " Datos incompletos";
			$this->response($response, $status);
		}

		$time = new DateTime();
		$dataSolicitud = [
			'fecha' => $time->format('Y-m-d'),
			'hora' => $time->format('H:i:s'),
			'monto_devolver' => $this->post('monto'),
			'id_operador_devolucion' => $this->session->userdata("idoperador"),
			'estado' =>  $this->post('estado', true),
		];
		
		$id = $this->post('id_devolucion');
		$update = $this->devolucion_model->updateDevolucion($dataSolicitud, $id);

		if($update > 0){
			//consultamos la informacion de los pagos a devolver
			$pagos = explode(',', $this->input->post('pagos'));
			$solicitudes = [];
			foreach ($pagos as $key => $pago) {

				$info_pago = $this->credito_model->get_pagos_credito(['id_pago'=> $pago])[0];
				$dataPago = [
					'id_solicitud_devolucion ' => $id,
					'id_pago ' => $info_pago->id_pago,
					'id_credito' => $info_pago->id_credito,
					'monto'=>$info_pago->monto,
				];
				//insertamos la relacion del pago a devolver
				$this->devolucion_model->insertarPagoDevolver($dataPago);
				$sol = $this->solicitud_model->getSolicitudesBy(['id_credito' => $info_pago->id_credito])[0]->id;
			}
			
			$client = $this->cliente_model->getClienteById($this->post('id_cliente'))[0];
			$dataTrackGestion = [
				'id_solicitud'=>(int)$sol,
				'observaciones'=>'<b>SOLICITUD DE DEVOLUCION</b><br>Fecha: '.$time->format('Y-m-d').
				                 '<br>Hora: '.$time->format('H:i:s').'<br>Monto: $'.number_format($this->post('monto'),2, ',', '.').
								 '<br><b>DEVOLVER EN:</b><br>Banco: '.$this->post('banco').'<br>Tipo cuenta: '.$this->post('tipo').'<br>Numero: '.$this->post('cuenta'), 
				'id_tipo_gestion' => 190,
				'id_operador' => $this->session->userdata("idoperador")
			];
			$endPoint =  base_url('api/track_gestion');		
			$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
			$response['status']['ok'] = TRUE;
			$response['message'] = 'Solicitud actualizada con exito';
			$response['respsms'] = self::send_msj_devoluciones($client['documento'], 2);
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function procesar_devolucion_post() {
		
		$status = parent::HTTP_OK;
		if(is_null($this->post('respuesta')) || is_null($this->post('id_devolucion'))){
			$response['status']['code'] = 200;
			$response['status']['ok'] = FALSE;
			$response['message'] = " Datos incompletos";
			return $response;
		}

		$data = [
			'respuesta' 	=> $this->post('respuesta', true),
			'comentario' 	=> $this->post('comentario', true),
			'monto' 		=> $this->post('monto', true),
			'id_devolucion'	=> $this->post('id_devolucion', true),
			'comprobantes' 	=> $this->post('comprobantes', true),
			'id_credito'	=> $this->post('id_credito', true)
		];
		$response = $this->procesar($data);

		$this->response($response, $status);
	}

    public function uploadComprobanteDevolucion_post() {
        if ($this->input->is_ajax_request()) {

            $fichero_anio = dirname(BASEPATH) . '/public/devoluciones/comprobantes/' . date('Y');
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/devoluciones/comprobantes/' . date('Y') . '/' . date('m');
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700, true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700, true);
            }
            $fecha_creacion_archivo = date('Ymd_His');
            $nombre_archivo = 'comprobante_'.$fecha_creacion_archivo;
            $config['upload_path'] = $ruta_guardar_archivo; 
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
                $archivo_ruta = 'public/devoluciones/comprobantes/' . date('Y') . '/' . date('m') . '/' .$nombre_archivo.$data['upload_data']['file_ext'];
                $status = parent::HTTP_OK;
                $response = [
                    'status'  => ['code' => $status, 'ok' => TRUE],
                    'message' => "Comprobante pago guardado",
					'url' => $archivo_ruta,
					"nombre" => $nombre_archivo.$data['upload_data']['file_ext']
                ];
                
               
            } else {
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->upload->display_errors();
            }

			$this->response($response, $status);
        }else{
            show_404();
		}	
    }
	
	public function generar_csv_santander_get(){


		/**
         * consulta por las solicitudes de devolucion
         */
		$devoluciones = $this->get_devoluciones(['estado' => 0]);
        $result_db_solicitudes = (!empty($devoluciones)) ? $devoluciones : false;

        $registros = 0;
        $fecha_archivo = date('Y-m-d H:i:s');
        $body = '';
        $arrayDatos = [];
		$ok= FALSE;

		
        /**
		 * destino del archivo
         */
		$filename = date('Ymd_Hi').'_SANTANDER.csv';
        $dir_file = $this->get_end_folder();
        $file_dir = $dir_file . $filename;
		
        if($result_db_solicitudes !== false){
			$registros = count($result_db_solicitudes);
            $registro = 1;
			$solicitudes =[];
            foreach ($result_db_solicitudes as $key => $solicitud) {
				
				//$datos_bancarios = $this->solicitud_model->getDatosBancariosTXT($solicitud->id);
                //datos del cliente
				$Nombre_beneficiario = trim($solicitud->nombres) . ' ' . trim($solicitud->apellidos);
				$Nombre_beneficiario = cleanString($Nombre_beneficiario);
				$Nombre_beneficiario = getLimitedText($Nombre_beneficiario, 50, false);
				
				$tipo_documento = txt_number($solicitud->codigo, 2);
				
				$documento = getLimitedText($solicitud->documento, 15, false);
				
				$banco = txt_number($solicitud->codigo_banco, 4);
				
				if($solicitud->id_tipo_cuenta == 1)
				$tipo_cuenta = 'CORRIENTE';
				else if($solicitud->id_tipo_cuenta == 46)
				$tipo_cuenta = 'AHORROS';
				else
				$tipo_cuenta = '';
				
				$cuenta_destino = getLimitedText($solicitud->cuenta, 17, false);
				
				
				$monto = getLimitedText(format_price($solicitud->monto_devolver,2,','), 13, false);
				$valida_documento = 'SI'; //SI | NO
				$referencia = getLimitedText('S-' . $solicitud->id, 24, false);
				$descripcion = getLimitedText('DEVOLUCION  S-' . $solicitud->id, 24, false);
				
				$body .= self::make_body($registro, $Nombre_beneficiario, $tipo_documento, $documento, $banco, $tipo_cuenta, $cuenta_destino, $monto, $valida_documento, $referencia, $descripcion);
				$registro++;
				
				
			
				/**
				 * actualizo el estado de la solicitud a Procesando
				 */
				$data = array(
					'estado' => 2,
				);
				$update = $this->devolucion_model->updateDevolucion($data, $solicitud->id);
				
				array_push($solicitudes,"'$solicitud->id'");

				$id_solicitud = $this->devolucion_model->getSolicitudDCreitoPagoDevuelto(["id_devolucion" => $solicitud->id]);
				foreach ($id_solicitud as $key => $value){
					//trackeamos
					$dataTrackGestion = [
						'id_solicitud'=>(int)$value->id_solicitud,
						'observaciones'=>'<b>DEVOLUCION EN PROCESO</b><br>Fecha: '.date('d-m-Y').
										'<br>Hora: '.date('H:i:s').'<br>Monto: $'.number_format($solicitud->monto_devolver,2, ',', '.'), 
						'id_tipo_gestion' => 190,
						'id_operador' => $this->session->userdata("idoperador")
					];
					$endPoint =  base_url('api/track_gestion');
					$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
				}
				
				$response['respsms'] = self::send_msj_devoluciones($documento, 1);
				
				if($key+1 == $registros)
					break;
				else
					$body .= BR;

            }

		
			
			/**
			 * inserto el registro del archivo
			 */
			$data = array(
				'ruta' => $file_dir,
				'nombre_archivo' => $filename,
				'fecha_generacion' => date('Y-m-d H:i:s'),
				'cantidad_solicitudes' => $registros,
				'id_operador' => $this->session->userdata("idoperador")                    
			);
			$id_txt = $this->devolucion_model->insertarSolicitudTxt($data);

			if ($id_txt > 0) {
				/**
				 * registramos el id_txt en el registro de solicitudes para las que estan PROCESANDO
				 */
				$data = array(
					'id_solicitud_devolucion_txt' => $id_txt,
				);
				$update = $this->devolucion_model->updateDevolucion($data, null, ['where'=>' id in ('.implode(',',$solicitudes).') ']);

				/**
				 * genero el archivo
				 */
				$content_file = $body;
				$txt_file = create_txt($content_file, $filename, 'devoluciones');
				$message = 'ARCHIVO GENERADO';
				$ok = TRUE;
				$response['nombre_archivo'] = $filename;
				$response['url'] = $file_dir;
				
			} else{
				$message = 'No fue posible guardar el registro del archivo';
			}

			
			
		} else{
			$message = 'No se encontraron solicitudes para DEVOLVER';
		}

		if ($registros == 0){
			$message = 'No se encontraron pagos para informar.';
			
		}

            

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$response['status']['ok'] = $ok;
		$response['message'] = $message;
		$this->response($response, $status);
            
        
    }

	
    private function send_msj_devoluciones($doc, $action) {

        $sms = $this->solicitud_model->get_agenda_personal_solicitud(["documento" => $doc,"fuentes" => "'PERSONAL DECLARADO'","estado" => 1]);
        if (count($sms) > 0){
			$whatsapp = $this->solicitud_model->get_agenda_whatsapp(["documento" => $doc,"status_chat" => 'activo', "from" => $sms[0]['numero']]);
			$email = $this->solicitud_model->get_agenda_mail(['documento' => $doc, 'personal' => true]);
			
            $response['action'] = $action;
            $msj = [];
			$name = explode(' ', ucfirst(strtolower($sms[0]['contacto'])));
            switch ($action) {
                case 1:
					$msj['sms'] = 'Hola '.$name[0].', ya el banco está procesando la transferencia que realizamos por la devolución de tu dinero.
					Tendrás pronto la devolución, antes de las 24 horas debes tener el dinero en tu cuenta. 
					Si luego de 24 horas hábiles no haz recibido el pago, por favor escribenos por WhatsApp para asi verificar tu caso lo más pronto posible.';
					$msj['ws']  = 'Hola '.$name[0].', ya el banco está procesando la transferencia que realizamos por la devolución de tu dinero.
					Tendrás pronto la devolución, antes de las 24 horas debes tener el dinero en tu cuenta. 
					Si luego de 24 horas hábiles no haz recibido el pago, por favor escribenos por aquí para asi verificar tu caso lo más pronto posible.';
					$asunto = 'Devolución procesada';
					$idTemplateEmail = '33273';
                    break;
                case 2:
					$msj['sms'] = 'Hola '.$name[0].', tu devolución la está gestionando el área correspondiente para procesarla de inmediato.<br>Te escribiremos por aquí para confirmarte la transferencia.';
					$msj['ws']  = 'Hola '.$name[0].', tu devolución la está gestionando el área correspondiente para procesarla de inmediato.<br>Te escribiremos por aquí para confirmarte la transferencia.';
					$asunto = 'Devolución en proceso';
					$idTemplateEmail = '33272';
                    break;
                default:
                    break;
            }
		}
        
        if (count($sms) > 0) {
            $endPoint = URL_CAMPANIAS."ApiEnvioComuGeneral";
            $response['sms'] = Requests::post($endPoint, [], ["tipo_envio" => 2,"servicio" => 2,"text" => $msj['sms'],"numero" => "+57".$sms[0]['numero']]); 
        }
        if (count($whatsapp) > 0) {
            $endPoint = base_url()."comunicaciones/twilio/send_new_message";
            $response['ws'] = Requests::post($endPoint, [], ['chatID'  => $whatsapp[0]['id'],'message' => $msj['ws'],'operatorID' => 192 ]); 
        }
		if (count($email) > 0) {
			$params[] = array(
            'recipient' => 'freddy.diaz@solventa.com', //$email[0]['cuenta'],
            "attributes" => array(
                "NAME" => $name[0],                 
            ),
            'x-apiheader' => $idTemplateEmail
			);
			//$response['email'] = $this->pepipost_library->curl_pepipost($params,$idTemplateEmail,'Devolucion');
        }
        return $response;
    }

	public function cambiar_estado_procesando_post() {

		//cargamos la tabla de solicitudes devoluciones
		if(is_null($this->post('id')) || is_null($this->post('estado'))){
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['message'] = " Datos incompletos";
			$this->response($response, $status);
		}

		$precarga =  is_null($this->post('precarga', true));

		$dataSolicitud = [
			'estado' => $this->post('estado', true)
		];

		$id = $this->post('id', true);

		$update = $this->devolucion_model->updateDevolucion($dataSolicitud, $id);

		if($update > 0){
			if (!$precarga) {
				$id_solicitud = $this->devolucion_model->getSolicitudDCreitoPagoDevuelto(["id_devolucion" => $id]);
				foreach ($id_solicitud as $key => $value){
					//trackeamos
					$dataTrackGestion = [
						'id_solicitud'=>(int)$value->id_solicitud,
						'observaciones'=>'<b>DEVOLUCION EN PROCESO</b><br>Fecha: '.date('d-m-Y').
										'<br>Hora: '.date('H:i:s').'<br>Monto: $'.number_format($value->monto_devolver,2, ',', '.'), 
						'id_tipo_gestion' => 190,
						'id_operador' => $this->session->userdata("idoperador")
					];
					$endPoint =  base_url('api/track_gestion');
					$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
				}
			}
			
			$response['status']['ok'] = TRUE;
			$response['message'] = 'Solicitud actualizada con exito';
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}


	/*
    * Procesamiento de archivo xls Banco Santander
    */
    public function procesar_respuesta_santander_post(){
		$status = parent::HTTP_OK;

		$archivo = $this->devolucion_model->get_archivo_respuesta(["fileName" =>  $this->post('fileName')]);
		if(!empty($archivo)){

			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['message'] = "El archivo ya fue procesado";
			$this->response($response, $status);
		}

		$fichero_anio = dirname(BASEPATH) . '/public/devoluciones/respuesta_devoluciones/' . date('Y');
            $ruta_guardar_archivo = dirname(BASEPATH) . '/public/devoluciones/respuesta_devoluciones/' . date('Y') . '/' . date('m');
            if (!file_exists($fichero_anio)) {
                mkdir($fichero_anio, 0700, true);
            }
            if (!file_exists($ruta_guardar_archivo)) {
                mkdir($ruta_guardar_archivo, 0700, true);
            }
            $fecha_creacion_archivo = date('Ymd_His');
            $nombre_archivo = $this->post('fileName');
            $config['upload_path'] = $ruta_guardar_archivo; 
            $config['file_name'] = $nombre_archivo;
            $config['allowed_types'] = 'xls|xlsx';
            $config['overwrite'] = FALSE;
			
			$this->load->library('upload');
            $this->upload->initialize($config);
			
            if ($this->upload->do_upload('file')) {
				
				$file = $this->upload->data();
				$filename = $file['file_name'];
                $archivo_ruta = 'public/devoluciones/respuesta_devoluciones/' . date('Y') . '/' . date('m') . '/' .$filename;
				
				
				
				$element['patch_imagen'] = $archivo_ruta;
				$element['extension'] = $config['allowed_types'];
				$element['is_image'] = $file['is_image'];
				$element['fecha_carga'] = date('Y-m-d H:i:s');
				
				$reader = new Xls();
				$reader->setReadDataOnly(true);
				$spreadsheet = $reader->load($element['patch_imagen']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
	
				$solicitud_pago_con_error = [];
				$solicitud_no_pagadas = [];
				$solicitud_pagadas = [];
				$solicitud_ya_pagados = [];

				if(count($sheetData) > 0){
					$fecha_proceso = explode(' ', $sheetData[8]['D']);
					$timestamp = strtotime(str_replace('/', '-', $fecha_proceso[0]));
					$fecha_proceso = date('Y-m-d', $timestamp);
					$registros = 0;
					$registros_procesados = 0;
					$id_devolucion = 0;
					
					for ($i=11; $i <= count($sheetData); $i++) { 
						
						set_time_limit(0);
						$registros = $registros+1;
						
						$documento = $sheetData[$i]['D']; // Nº Documento
						$monto = $sheetData[$i]['G']; // Monto
						$fecha_proceso = $fecha_proceso; // current
						$fecha_cobro = $fecha_proceso; // current
						$estado = $sheetData[$i]['I']; // Descripción Respuesta
						$causal = $sheetData[$i]['H']; // Código Respuesta
						$adenda = $sheetData[$i]['M']; // Descripción

						
						$rowFields = [];
						$rowFields['documento'] = $documento;
						$rowFields['monto'] = $monto;
						$rowFields['fecha_proceso'] = $fecha_proceso;
						$rowFields['fecha_cobro'] = $fecha_cobro;
						$rowFields['estado'] = $estado;
						$rowFields['causal'] = !empty($causal) ? $causal : '';
						$detalle = explode('S-', $adenda);
						
						if(count($detalle) > 1 ){
							//viene el id_solicitud devolucion
							$id_devolucion =  $detalle[1];
							
						} else {
							
							/** no tenemos el id_solicitud devolucion
							 *	buscamos el id_cliente  partiendo del documento
							 */
							
							$cliente = $this->cliente_model->getClienteBy(['documento'=> $documento])[0];
							
							//buscamos las solicitudes del cliente en estado 2 (estado procesando)
							$devoluciones = $this->devolucion_model->getSolicitudes(['id_cliente' => $cliente->id, 'estado' => 2, 'monto_devolver' => str_replace(',','.',str_replace('.','',$monto))]);
							if(!empty($devoluciones)){
								$id_devolucion =  $devoluciones[0]->id;
							} else{
								continue;
							}

						}
						$id_credito = $this->devolucion_model->getSolicitudDCreitoPagoDevuelto(['id_devolucion' => $id_devolucion])[0]->id_credito;
						
						$dataSolicitud = [
							'id_devolucion' => $id_devolucion,
							'respuesta' 	=> ($estado == "Confirmada")? "DEVUELTO":"NO DEVUELTO",
							'comentario' 	=> "CARGA DE ARCHIVO RESPUESTA ".$filename,
							'monto' 		=> (float)str_replace('.','',$monto),
							'id_credito' 	=> $id_credito,
							'comprobantes' 	=> ""
						];

						$resultado = $this->procesar($dataSolicitud);

						if($resultado['status']['ok'] )
							$registros_procesados = $registros_procesados + 1;


					}

					$response['status']['ok'] = TRUE;
					$response['message'] = "Todos los registros del archivo fueron procesados con exito";
					if($registros > $registros_procesados){
						$response['status']['ok'] = FALSE;
						$response['message'] = "Archivo procesado. Existen registros no procesados";
					}


					if($registros_procesados > 0){
						/**
						 * insertamos el registro de archivo procesado si se proceso algun registro
						 */
						$data = [
							'ruta' => $archivo_ruta,
							'nombre_archivo' => $filename,
							'fecha_generacion' => date('Y-m-d H:i:s'),
							'id_operador' => $this->session->userdata("idoperador")
						];
						$insert = $this->devolucion_model->insertarRespuestaTxt($data);
		
	
					} else{
						$response['status']['ok'] = FALSE;
						$response['message'] = "Ningun registro procesado";
					}

				} else{
					$response['status']['ok'] = FALSE;
					$response['message'] = "Archivo sin registros";
				}

				
            } else {
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['message'] = "no fue posible cargar el archivo";
            }

			$this->response($response, $status);

    }


	/** 
	 * Funciones 
	*/

	private function procesar($datos) {

		$dataSolicitud = [
			'fecha_proceso' => date('Y-m-d H:i:s'),
			'resultado' => $datos['respuesta'],
			'comentario' => $datos['comentario'],
			'monto_devuelto' => $datos['monto'],
			'id_operador_devolucion' => $this->session->userdata("idoperador"),
			'estado' => 1,
		];
		//var_dump($datos);die;
		$id = $datos['id_devolucion'];

		$update = $this->devolucion_model->updateDevolucion($dataSolicitud, $id);
		//si se inserto la solicitud insertamos la relacion con el pago
		if($update > 0){

			//insertamos los comprobantes de existir 
			$comprobantes = $datos['comprobantes'];
			if($comprobantes != ""){
				
				$comprobantes = explode(',',$comprobantes);
	
				foreach ($comprobantes as $key => $comprobante) {
					
					$dataComprobante = [
						'id_solicitud_devolucion ' => $id,
						'comprobante' => '/public/devoluciones/comprobantes/' . date('Y') . '/' . date('m').'/'.$comprobante,
						'origen' => 1
					];
					//insertamos la relacion del pago a devolver
					$this->devolucion_model->insertarComprobanteDevolver($dataComprobante);
				}
			}
			$response['status']['ok'] = TRUE;
			$response['message'] = "Devolucion procesada con exito";

			
			if($datos['respuesta'] == "DEVUELTO"){
				//registramos pago en la tabla pago_credito
				//consultamos cuota a la que se le devolvio el monto
				$cuota = $this->credito_model->get_ultima_cuota_paga($datos['id_credito']);
				if(!empty($cuota)){
					$data = array(
						'id_detalle_credito' => $cuota[0]->id,
						'fecha' => date('Y-m-d H:i:s'),
						'monto' => $datos['monto'],
						'medio_pago' => 'devolucion',
						'tipo_pago' => 'devolucion',
						'fecha_pago' => date('Y-m-d H:i:s'),
						'estado' => 1,
						'estado_razon' => 'Aprobada',
						'referencia_interna' => $this->session->userdata['idoperador'],
					);
					$resp = $this->pago_credito_model->insert_pago($data);
	
					$response['message'] = "Devolucion procesada. No fue registrada como pago";
	
					if ($resp > 0){
						$response['message'] = "Devolucion procesada con exito";
					}

				}else{
					$response['message'] = "Devolucion procesada. No se pudo agregar el registro del pago";
				}
                
			}

			//trackeamos
			$sol = $this->solicitud_model->getSolicitudesBy(['id_credito' => $datos['id_credito']])[0]->id;
			$dataTrackGestion = [
				'id_solicitud'=>(int)$sol,
				'observaciones'=>'<b>SOLICITUD DE DEVOLUCION</b><br>Fecha: '.date('d-m-Y').
				                 '<br>Resultado: '.$datos['respuesta'].'<br>Monto devuelto: $'.number_format($datos['monto'],2, ',', '.').
								 '<br>Comentario: '.$datos['comentario'], 
				'id_tipo_gestion' => 191,
				'id_operador' => $this->session->userdata("idoperador")
			];
			$endPoint =  base_url('api/track_gestion');
			$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
			
			$response['status']['ok'] = TRUE;
			$response['message'] = 'Solicitud generada con exito';
		}

		$response['status']['code'] = 200;
		return $response;
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

	private function get_devoluciones($params, $tipo = NULL){
		if ($tipo == 'debitoAutomatico') {
			$solicitudes = $this->devolucion_model->getSolicitudesDebitoAutomatico($params);
		} else {
			$solicitudes = $this->devolucion_model->getSolicitudes($params);
		}
		
		$aux = [];
		foreach ($solicitudes as $key => $value) {
			$info = $this->devolucion_model->getInfoDevolucion($value->id_cliente);
			$value->banco="";
			$value->cuenta="";
			$value->tipo_cuenta="";
			$value->id_tipo_cuenta="";
			$value->tipo_documento = "";
			$value->codigo = "";
			$value->codigo_banco = "";
			$value->id_solicitud = "";
			
			if(!empty($info)){
				$value->banco = $info[0]->Nombre_Banco;
				$value->cuenta = $info[0]->numero_cuenta;
				$value->tipo_cuenta = $info[0]->Nombre_TipoCuenta;
				$value->tipo_documento = $info[0]->nombre_tipoDocumento;
				$value->codigo = $info[0]->codigo;
				$value->codigo_banco = $info[0]->codigo_banco;
				$value->id_tipo_cuenta = $info[0]->id_TipoCuenta;
				$value->id_solicitud = $info[0]->idSolicitud;
			}

			array_push($aux,$value);
		}
		return $aux;
	}


	function get_end_folder()
    {
    	$end_folder = $this->end_folder.'/'.date('Y').'/'.date('m').'/'.date('d').'/';
	    // Valida que la carpeta de destino exista, si no existe la crea.
	    if(!file_exists($end_folder))
	    {
	    	// Si no puede crear el directorio.
	        if(!mkdir($end_folder, 0775, true))
	        {
	        	$this->response['status']['ok'] = FALSE;
	   			$this->response['errors'] = 'No fué posible crear el directorio en .' . $end_folder;
	   			return FALSE;
	        }
	    }
	    return $end_folder;
    }

	/**
     * campos txt santander
     */
    public static function make_body($registro = '', $nombre_beneficiario = '', $tipo_documento = '', $documento = '', $banco = '', $tipo_cuenta = '', $cuenta_destino = '', $monto = 0, $valida_documento = '', $referencia = '', $descripcion = ''){
        $arrayFields = [$registro, 
                        $nombre_beneficiario, 
                        $tipo_documento,
                        $documento,
                        $banco,
                        $tipo_cuenta,
                        $cuenta_destino,
                        $monto, 
                        $valida_documento,
                        $referencia,
                        $descripcion];
        $returnValue = implode(self::SEPARADOR, $arrayFields);
        return $returnValue;
    }

	public function devolucionDebitoAutomatico_get(){
		// Se obtienen las solicitudes
		$bancos = array('BANCOLOMBIA','SANTANDER','BOGOTA');
		for ($i=0; $i < count($bancos); $i++) { 
			$devoluciones = $this->get_devoluciones(['estado' => 0, 'banco' => $bancos[$i]],'debitoAutomatico');
			//echo '<pre>'; print_r($devoluciones); echo '</pre>'; exit;
			$txt = '';
			$registro = 1;
			$registros = 0;
			$body = '';
			$documentos = array();
			$track = array();
			$ok= FALSE;
			if(!empty($devoluciones)){
				$registros = count($devoluciones);
				//SE CREA EL REGISTRO DEL ARCHIVO EN LA BD
				$txt = $this->CrearArchivoBD($bancos[$i], $registros);
				foreach ($devoluciones as $key => $devolucion) {
					//datos del cliente
					//SE CREA LA SOLICITUD DE DEVOLUCION
					$nuevaSolicitud = array(
						'fecha' => date('Y-m-d'),
						'hora' => date('H:i:s'),
						'id_cliente' => $devolucion->id_cliente,
						'forma_devolucion' => 'TOTAL',
						'monto_devolver' => $devolucion->monto_debito,
						'id_operador' => 108,
						'estado' => '2',
						'id_solicitud_devolucion_txt' => $txt[2],
						'id_cuota' => $devolucion->id_cuota,
						'id_pago' => $devolucion->id_pago,
						'id_credito' => $devolucion->id_credito

					);
					$idSolicitud = $this->insertarSolicitud($nuevaSolicitud);
					//$NombreBeneficiario = trim(utf8_decode($devolucion->nombres)) . ' ' . trim(utf8_decode($devolucion->apellidos));
					$NombreBeneficiario = trim($devolucion->nombres) . ' ' . trim($devolucion->apellidos);
					$NombreBeneficiario = cleanString($NombreBeneficiario);
					$NombreBeneficiario = getLimitedText($NombreBeneficiario, 50, false);
					$tipoDocumento = txt_number($devolucion->codigo, 2);
					$documento = getLimitedText($devolucion->documento, 15, false);
					$documentos[] = $devolucion->documento;
					$banco = txt_number($devolucion->codigo_banco, 4);
					if($devolucion->id_tipo_cuenta == 1)
						$tipoCuenta = 'CORRIENTE';
					else if($devolucion->id_tipo_cuenta == 46)
						$tipoCuenta = 'AHORROS';
					else
						$tipoCuenta = '';
					$cuentaDestino = getLimitedText($devolucion->cuenta, 17, false);
					$monto = getLimitedText(format_price($devolucion->monto_debito,2,','), 13, false);
					$validaDocumento = 'SI'; //SI | NO
					$referencia = getLimitedText('S-' . $idSolicitud, 24, false);
					$descripcion = getLimitedText('DEVOLUCION  S-' . $idSolicitud, 24, false);
					// Se carga la info del cuerpo del archivo
					$body .= self::make_body($registro, $NombreBeneficiario, $tipoDocumento, $documento, $banco, $tipoCuenta, $cuentaDestino, $monto, $validaDocumento, $referencia, $descripcion);
					$registro++;
					// Se actualiza el estado de la devolucion a Procesando
					$update = $this->devolucion_model->updateDebitoDevolver($devolucion->id_debito, ['estado' => '1','id_solicitud_devolucion' => $idSolicitud]);
					$track[] = array(
						'idSolicitud' => $devolucion->id_solicitud,
						'monto' => $devolucion->monto_debito,
						'banco' => $devolucion->banco,
						'tipo_cuenta' => $devolucion->tipo_cuenta,
						'cuenta' => $devolucion->cuenta
					); 
					if ($key+1 == $registros) {
						break;
					}else{
						$body .= BR;
					}
				}

				$message = $this->generarArchivo($body, $txt[0], $txt[1]);
				
			} else{
				$message = 'No se encontraron solicitudes para DEVOLVER';
			}
			 		
			if (!empty($txt)) {
				$this->sendMail = new SendMail_library;
				$path = $path = [realpath(FCPATH.$txt[1])];
				$email = $this->sendMail->send_mail2('freddy.diaz@solventa.com',"","", 'Archivo de devoluciones en proceso del banco '.$bancos[$i], 'Hola estimados, se adjunta el archivo con las devoluciones del banco '.$bancos[$i],$path, $txt[0], '', 0);
				if ($email->status == 200) {
					$respuesta =['response'=>'Email enviado con exito', 'opcion'=>1, 'status'=>1];
				}else{
					$respuesta =['response'=>'No se ha podido realizar el envio', 'opcion'=>1, 'status'=>2];
				}
				$this->insertTrack($track);
				/* for($i=0; $i < count($documentos); $i++){
					$response['respsms'] = self::send_msj_devoluciones($documentos[$i], 2);
				} */
			}
			
		}
		if ($registros == 0) {
			$message = 'No se encontraron pagos para informar.';
			
		}		
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$response['status']['ok'] = $ok;
		$response['message'] = $message;
		$this->response($response, $status);
	}

	public function crearArchivoBD($filename, $registros){
		// Ruta donde se creara el archivo y nombre del mismo
		$filename = date('Ymd_Hi').'_'.$filename.'.csv';
		$dirFile = $this->get_end_folder();
		$fileDir = $dirFile . $filename;
		// Inserto el registro del archivo
		$data = array(
			'ruta' => $fileDir,
			'nombre_archivo' => $filename,
			'fecha_generacion' => date('Y-m-d H:i:s'),
			'cantidad_solicitudes' => $registros,
			'id_operador' => 108                    
		);
		$idTxt = $this->devolucion_model->insertarSolicitudTxt($data);
		return array($filename,$fileDir,$idTxt);
	}

	public function generarArchivo($body, $filename, $fileDir){	

		if (!empty($body)) {
			// Registramos el id_txt en el registro de solicitudes para las que estan PROCESANDO
			// genero el archivo
			$contentFile = $body;
			$txtFile = create_txt($contentFile, $filename, 'devoluciones');
			$message = 'ARCHIVO GENERADO';
			$ok = TRUE;
			$response['nombre_archivo'] = $filename;
			$response['url'] = $fileDir;	
		} else{
			$message = 'No fue posible guardar el registro del archivo';
		}		
		return $message;
	}

	public function insertarSolicitud($params){
		$id_solicitud = NULL;
		$dataSolicitud = [
			'fecha' => date('Y-m-d'),
			'hora' => date('H:i:s'),
			'id_cliente' => $params['id_cliente'],
			'forma_devolucion' => $params['forma_devolucion'],
			'monto_devolver' => $params['monto_devolver'],
			'id_operador' => 108,
			'estado' => 2,
			'id_solicitud_devolucion_txt' => $params['id_solicitud_devolucion_txt'],
		];
		// SE VERIFICA SI EXISTE UNA SOLICITUD DE DEVOLUCION PARA EL CLIENTE EN ESTADO 3
		$solicitudes = $this->devolucion_model->getSolicitudes(['id_cliente' => $params['id_cliente'], 'estado' => 3]);
		//echo '<pre>'; print_r($solicitudes); echo '</pre>'; exit;
		if (!empty($solicitudes)) {
			// SI EXISTE UNA SOLICITUD DE DEVOLUCION ESTADO 3 SE CANCELA PASANDOLA A ESTADO 4
			foreach ($solicitudes as $solicitud) {
				$campos = array(
					'estado' => 4, 
					'resultado' => 'NO DEVUELTO', 
					'fecha_proceso' => date('Y-m-d H:i:s'),
					'comentario' => 'procesado por gestion automatica',
					'id_operador_devolucion' => 108

				);
				$this->devolucion_model->updateEstadoSolicitudDevolucion($solicitud->id, $campos);
			}
		}
		// SE CREA UNA NUEVA SOLICITUD DE DEVOLUCION
		$id_solicitud = $this->devolucion_model->insertarSolicitud($dataSolicitud);

		//si se inserto la solicitud insertamos la relacion con el pago
		if($id_solicitud > 0){
			//consultamos la informacion de los pagos a devolver
			$dataPago = [
				'id_solicitud_devolucion' => $id_solicitud,
				'id_pago' => $params['id_pago'],
				'id_credito' => $params['id_credito'],
				'monto' => $params['monto_devolver'],
			];
			//insertamos la relacion del pago a devolver
			$this->devolucion_model->insertarPagoDevolver($dataPago);
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		return $id_solicitud;
	}

	public function insertTrack($params){
		$endPoint =  base_url('api/track_gestion');
		for ($i=0; $i < count($params); $i++) { 
			$dataTrackPro = [
				'id_solicitud'=>(int)$params[$i]['idSolicitud'],
				'observaciones'=>'<b>DEVOLUCION EN PROCESO</b><br>Fecha: '.date('d-m-Y').
				'<br>Hora: '.date('H:i:s').'<br>Monto: $'.number_format($params[$i]['monto'],2, ',', '.'), 
				'id_tipo_gestion' => 190,
				'id_operador' => 108
			];
			$dataTrackGestion = [
				'id_solicitud'=>(int)$params[$i]['idSolicitud'],
				'observaciones'=>'<b>SOLICITUD DE DEVOLUCION</b><br>Fecha: '.date('d-m-Y').
								 '<br>Hora: '.date('H:i:s').'<br>Monto: $'.number_format($params[$i]['monto'],2, ',', '.').
								 '<br><b>DEVOLVER EN:</b><br>Banco: '.$params[$i]['banco'].'<br>Tipo cuenta: '.$params[$i]['tipo_cuenta'].'<br>Numero: '.$params[$i]['cuenta'], 
				'id_tipo_gestion' => 190,
				'id_operador' => 108
			];
			$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
			$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackPro);
			
		}	
	}

	
}
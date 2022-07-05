<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;*/

class Solicitud_Rep extends CI_Controller
{
	public $buro_response = array(
								    'rechazado' => 0,
								    'sin_analizar' => 0,
								    'aprobado' => 0,
								);
	public $estado_response = array(
						            'sin_estado' => 0,
						            'analisis' => 0,
						            'verificado' => 0,
						            'validado' => 0,
						            'rechazado' => 0,
						            'aprobado' => 0,
						            'transfiriendo' => 0,
						            'pagado' => 0,
						            'anulado' => 0,
							        );
	public $track_gestion_response = array(
							            'solo_gestion_automatica' => 0,
							            'sin_gestion' => 0,
							        );
	public $visado_response = array(
							            'visado' => 0,
							            'rechazado' => 0,
							        );

	public function __construct()
	{
		parent::__construct();
		// MODELOS
        if ($this->session->userdata('is_logged_in')) {
   			$this->load->model('reportes/Indicadores_model', 'indicadores_model', TRUE);
        } else {
			die('USUARIO NO LOGUEADO');
        }
	}

	public function box_indicadores()
	{
		$data['datos'] = $this->indicadores();
		$this->load->view('reportes/resultado', $data);
	}
	public function indicadores()
	{
		$data = [];
		$data['resumen'] = [];
		$estados =[];
		$track_gestion =[];
		$visado =[];
		$resumen_estados = [];

		$data['canal'] = $this->input->post('canal');
		$data['tipo_solicitud'] 	= $this->input->post('tipo_solicitud');
		$data['tipo_informe'] = strtoupper($this->input->post('tipo_informe'));
		$dates = explode('|',$this->input->post('rango_fecha'));
		$data['desde'] = isset($dates[0])?trim($dates[0]):null;
		$data['hasta'] = isset($dates[1])?trim($dates[1]):null;


		$desde = date_to_string($data['desde'] ,'Y-m-d', FALSE, 'd-m-Y');
		$hasta = date_to_string($data['hasta'],'Y-m-d', FALSE, 'd-m-Y');
		$tipo_solicitud = strtoupper($data['tipo_solicitud']) != 'TODOS'? strtoupper($data['tipo_solicitud']):null;
		$tipo_informe = strtoupper($data['tipo_informe'])? strtoupper($data['tipo_informe']):null;

		for($i=$hasta; $i >= $desde;$i = date('Y-m-d', strtotime($i ."- 1 days")))
		{
    		$data['detalles'][$i]['buro'] = $this->buro_response;
    		$data['detalles'][$i]['estados'] = $this->estado_response;
    		$data['detalles'][$i]['track_gestion'] = $this->track_gestion_response;
    		$data['detalles'][$i]['visados'] = $this->visado_response;
    		$data['detalles'][$i]['periodo'] = date_to_string($i ,'d-m-Y', FALSE, 'Y-m-d');
    		$data['detalles'][$i]['solicitudes'] = 0;
		}

		// Estado de las solicitudes
		$estados = $this->get_indicadores_estados($desde, $hasta, $tipo_solicitud);
		// Solitudes visado
		$visados = $this->get_solicitudes_visado($desde, $hasta, $tipo_solicitud);
		// Track gestion
		$track_gestion = $this->get_track_gestion($desde, $hasta, $tipo_solicitud);

		foreach ($data['detalles'] as $fecha => $resumen)
		{
			if(isset($estados[$fecha]) && !empty($estados[$fecha]))
			{
				$data['detalles'][$fecha] = $estados[$fecha];
			}
			if(isset($track_gestion[$fecha]) && !empty($track_gestion[$fecha]))
			{
				$data['detalles'][$fecha]['track_gestion'] = $track_gestion[$fecha];
			}
			else{
				$data['detalles'][$fecha]['track_gestion'] = $this->track_gestion_response;
			}
			if(isset($visados[$fecha]) && !empty($visados[$fecha]))
			{
				$data['detalles'][$fecha]['visados'] = $visados[$fecha];
			}else{
				$data['detalles'][$fecha]['visados'] = $this->visado_response;
			}	
		}

		// Resumenes
		$resumen_estados = $this->_resumen_estados($desde, $hasta, $estados);
		$resumen_track_gestion = $this->_resumen_track_gestion($track_gestion);
		$resumen_visados = $this->_resumen_visados($visados);
		$data['resumido'][0] = $resumen_estados;
		$data['resumido'][0]['track_gestion'] = $resumen_track_gestion;
		$data['resumido'][0]['visados'] = $resumen_visados;
	
		switch ($tipo_informe)
		{
			case 'RESUMIDO':

				break;
			case 'DETALLADO':
				
				//$data = $this->_add_to_data_detalle($data, $estados, $track_gestion);

				break;
		}

		return $data;
	}

	/***************************************************************************/
	// Estado de las solicitudes segun la fecha
	/***************************************************************************/
	public function get_indicadores_estados($desde, $hasta, $tipo_solicitud)
	{
		$estados = $this->indicadores_model->indicadores_estados($desde, $hasta, $tipo_solicitud);
		return $this->_format_response_indicadores_estados($estados);
	}

	private function _format_response_indicadores_estados($estados)
	{
		$response = [];

		foreach ($estados as $key => $detalle)
		{
			if(empty($response[$detalle['fecha_solicitud']]))
			{
				$response[$detalle['fecha_solicitud']]['buro'] = $this->buro_response;
				$response[$detalle['fecha_solicitud']]['estados'] = $this->estado_response;
				//$response[$detalle['fecha_solicitud']]['fecha'] = date_to_string($detalle['fecha_solicitud'] ,'d-m-Y', FALSE, 'Y-m-d');
				$response[$detalle['fecha_solicitud']]['periodo'] = date_to_string($detalle['fecha_solicitud'] ,'d-m-Y', FALSE, 'Y-m-d');
				$response[$detalle['fecha_solicitud']]['solicitudes'] = 0;
			}
			//$response[$detalle['fecha_solicitud']]['fecha'] = date_to_string($detalle['fecha_solicitud'],'d-m-a');
			$response[$detalle['fecha_solicitud']]['periodo'] = date_to_string($detalle['fecha_solicitud'],'d-m-a');

			// SOLICITUDES
			if($detalle['estado'] == '' || $detalle['estado'] == NULL)
			{
				$estado = 'sin_estado';
			}else{
				$estado =strtolower($detalle['estado']);
			}
			// Este codigo es para garantizar que si aparece un nuevo estado el sistema pueda agregarlo y seguir.
			if(!isset($response[$detalle['fecha_solicitud']]['estados'][$estado]))
			{
				$response[$detalle['fecha_solicitud']]['estados'][$estado] = 0;
			}

			$response[$detalle['fecha_solicitud']]['estados'][$estado] += $detalle['cantidad'];
			
			// BUROS
			if($detalle['respuesta_analisis'] == '' || $detalle['respuesta_analisis'] == NULL)
			{
				$estado_buro = 'sin_analizar';
			}else{
				$estado_buro =strtolower($detalle['respuesta_analisis']);
			}

			// Este codigo es para garantizar que si aparece un nuevo estado el sistema pueda agregarlo y seguir.
			if(!isset($response[$detalle['fecha_solicitud']]['buro'][$estado_buro]))
			{
				$response[$detalle['fecha_solicitud']]['buro'][$estado_buro] = 0;
			}

			$response[$detalle['fecha_solicitud']]['buro'][$estado_buro] += $detalle['cantidad'];

			// Sumo la cantidad de solicitudes
			$response[$detalle['fecha_solicitud']]['solicitudes'] += $detalle['cantidad'];
		}
		return $response;
	}

	private function _resumen_estados($desde, $hasta, $estados)
	{
		$response['estados'] = $this->estado_response;
		$response['buro'] = $this->buro_response;
        $response['periodo'] = $desde." AL ".$hasta;
		$response['solicitudes'] = 0;	

        
		foreach ($estados as $key => $value)
		{
			$response['solicitudes'] += $value['solicitudes'];	

			foreach ($value['buro'] as $estado => $cantidad)
			{
				$response['buro'][$estado] += $cantidad; 
			}
			foreach ($value['estados'] as $estado => $cantidad)
			{
				$response['estados'][$estado] += $cantidad; 
			}
		}
		return $response;
	}
	/***************************************************************************/
	// Track gestion de las solicitudes segun la fecha
	/***************************************************************************/
	public function get_track_gestion($desde, $hasta, $tipo_solicitud)
	{
		// Solo gestion automatica
		$solo_gestion_automatica = $this->indicadores_model->indicadores_track_gestion_gestion_automatica($desde, $hasta, $tipo_solicitud);
		// Sin gestion
		$sin_gestion = $this->indicadores_model->indicadores_track_gestion_sin_gestion($desde, $hasta, $tipo_solicitud);
		$track_gestion = $this->_format_response_track_gestion($sin_gestion, $solo_gestion_automatica);
		
		return $track_gestion;
	}

	private function _format_response_track_gestion($sin_gestion = [], $solo_gestion_automatica = [])
	{
		$response = [];

		foreach ($sin_gestion as $key => $value)
		{
			if(empty($response[$value['fecha_solicitud']]))
			{
				$response[$value['fecha_solicitud']] = $this->track_gestion_response;
			}
			$response[$value['fecha_solicitud']]['sin_gestion']	+= $value['cantidad'];
		}
		foreach ($solo_gestion_automatica as $key => $value)
		{
			if(empty($response[$value['fecha_solicitud']]))
			{
				$response[$value['fecha_solicitud']] = $this->track_gestion_response;
			}
			$response[$value['fecha_solicitud']]['solo_gestion_automatica']	+= $value['cantidad'];
		}

		return $response;
	}

	private function _resumen_track_gestion($track_gestion)
	{
		$response = $this->track_gestion_response;
        
		foreach ($track_gestion as $fecha => $value)
		{
			foreach ($value as $estado => $cantidad)
			{
				$response[$estado] += $cantidad; 
			}
		}
		return $response;
	}
	/***************************************************************************/
	// Visado de las solicitudes segun la fecha
	/***************************************************************************/
	public function get_solicitudes_visado($desde, $hasta, $tipo_solicitud)
	{
		$visados = $this->indicadores_model->indicadores_solicitud_visado($desde, $hasta, $tipo_solicitud);
		return $this->_format_response_solicitud_visado($visados);
	}

	private function _format_response_solicitud_visado($visados)
	{
		$response = [];
		foreach ($visados as $key => $vis)
		{
			if( empty($response[$vis['fecha_solicitud']]))
			{
				$response[$vis['fecha_solicitud']] = $this->visado_response;
			}
			if($vis['visado'] == 0)
			{
				$response[$vis['fecha_solicitud']]['rechazado'] += $vis['cantidad'];
			}else if($vis['visado'] == 1)
			{
				$response[$vis['fecha_solicitud']]['visado'] += $vis['cantidad'];
			}
		}
		return $response;
	}

	private function _resumen_visados($visados)
	{
		$response = $this->visado_response;
        
		foreach ($visados as $fecha => $value)
		{
			foreach ($value as $estado => $cantidad)
			{
				$response[$estado] += $cantidad; 
			}
		}
		return $response;
	}

	/*private function _add_to_data_detalle($data, $estados, $track_gestion)
	{
		$response = [];
		foreach ($data['resumen'] as $key => $value)
		{
			if(isset($estados[$key]))
			{
				$data['resumen'][$key]  = array_merge($data['resumen'][$key]['estado'],$estados[$key]);
			}
			if(isset($track_gestion[$key]))
			{
				$data['resumen'][$key]['track_gestion'] = array_merge($data['resumen'][$key]['track_gestion'],$track_gestion[$key]);
			}
		}
		return $data;
	}

	private function _add_to_data_resumido($data, $track_gestion)
	{
		$response = [];
		foreach ($track_gestion as $key => $value)
		{
			$data['resumen'][0]['track_gestion']['sin_gestion'] += $value['sin_gestion'];
			$data['resumen'][0]['track_gestion']['solo_gestion_automatica'] += $value['solo_gestion_automatica'];
		}

		return $data;
	}*/

	

}

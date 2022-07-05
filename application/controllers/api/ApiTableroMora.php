<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiTableroMora extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
        // Models
        $this->load->model("tablero/Tablero_model", "tablero");
        $this->load->model('operadores/Operadores_model','operadores',TRUE);
        $this->load->model('Solicitud_m','solicitud_model',TRUE);

		
	}
       
    public function mora_get()
	{	
        $objetivo_porcentaje =  $this->tablero->get_objetivo_mora(['id' => 1]);
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $response['status']['ok']	 = TRUE;
        $response['data']	 = $objetivo_porcentaje;
		$this->response($response, $status);
    }

    public function ActualizarMora_post()
	{	
        $data=[
            'descripcion' => $this->input->post('descripcion'),
            'estado' => $this->input->post('estado'),
            'objetivo_porcentaje' => $this->input->post('objetivo_porcentaje'),
            'estado' => $this->input->post('estado'),
            'tablero' => $this->input->post('tablero'),
            'condicion' => $this->input->post('condicion'),
            'fecha_mora_mostrar' => $this->input->post('fecha_mora_mostrar'),
            'objetivo_dependientes' =>$this->input->post('objetivos_dependientes'),
            'objetivos_independientes' => $this->input->post('objetivos_independientes'),
            'mora_dependientes' => $this->input->post('mora_dependientes'),
            'mora_independientes' => $this->input->post('mora_independientes'),
            'objetivo_mora' => $this->input->post('objetivos_mora'),
            'proximo_vencimiento' => $this->input->post('proximo_vencimiento')
        ];
      
        $actualiza =  $this->tablero->update_mora(['id' => 1], $data);
        $status = parent::HTTP_OK;
        if($actualiza === 1){
            $data = array( 
                'id_operador'=>$this->session->userdata("idoperador"),
                'id_registro_afectado'=>'1',
                'tabla'=> 'objetivos_tablero',
                'detalle'=> '[ACTUALIZA_TABLERO_MORA] datos'.json_encode($data),
                'accion'=> 'UPDATE',
                'fecha_hora'=>  date("Y-m-d H:i:s")
                );
            $track = $this->operadores->track_interno($data);
            $response['status']['code']  = $status;
            $response['status']['ok']	 = TRUE;
            $response['data'] = $actualiza;
            $response['mensaje'] = 'Configuracion actualizada con exito';
        }
		$this->response($response, $status);
	}   

    public function fechasVencimientoFront_get()
	{	
        $fechas = $this->solicitud_model->get_vencimientos_flujo_mora();
        $response['status']['ok']	 = TRUE;
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        
        if (empty($fechas)) {
            $response['status']['ok']	 = FALSE;
            $response['message']	 = 'Por el momento no hay fechas disponibles para evaluar';
        }
        $response['data']	 = $fechas;
		$this->response($response, $status);
    }
    
}

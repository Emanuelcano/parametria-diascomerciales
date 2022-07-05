<?php 
class AsigAutomatico
{

	protected $supervisor_model;
	protected $data;
	protected $where;
	protected $track;
	
	public function __construct(CI_Model $supervisor_model) {
		$this->supervisor_model = 	$supervisor_model;
	}
	
	public function get_reglas_automatico()
	{
		return $this->supervisor_model->get_reglas_automatico();
		
		
	}
	
	public function get_track_reglas_automatico()
	{
		$result = $this->supervisor_model->get_track_reglas_automatico();
		$nuevo_track = array_map(function ($fila) {
			$track = $fila;
			$track['id_operador'] = $this->get_nombre_operador($track['id_operador']);
			return $track;
        }, $result);
        
        return $nuevo_track;
		
	}
	
	public function cambio_estado_reglas_automatico($id, $EstadoActual, $param) : bool
	{
		$this->data['situacion_laboral'] = $param['situacion_laboral'];
		$this->data['antiguedad'] = $param['antiguedad'];
		$this->data['prediccion'] = $param['prediccion'];
		$this->data['estado'] = ($EstadoActual == 1) ? 0: 1;
		$this->where['id'] = $id;
		$this->track = $this->data;
		if($this->supervisor_model->cambio_estado_reglas_automatico($this->data, $this->where)){
			return true; 
		
		}
		return false;
		
	}
	
	public function update_reglas_automatico($param) : bool
	{
		$this->data['situacion_laboral'] = $param['situacion_laboral'];
		$this->data['antiguedad'] = $param['antiguedad'];
		$this->data['prediccion'] = $param['prediccion'];
		$this->data['estado'] = $param['estado'];
		$this->where['id'] = $param['id'];
		$this->track = $this->data;
		if($this->supervisor_model->update_reglas_automatico($this->data, $this->where)){
			return true; 
		
		}
		return false;
		
	}
	
	public function set_track_reglas_automatico($operador)
	{	
		$this->track['id_operador'] = $operador;
		$this->track['fecha_hora'] = date("Y-m-d H:i:s");
		
		if($this->supervisor_model->set_track_reglas_automatico($this->track)){
			return true; 
		
		}
		return false;
		
	}
	
	public function get_operador($id_usuario)
	{
		return $this->supervisor_model->get_operador(['id_usuario' =>$id_usuario])[0]['idoperador'];
	}
	
	public function get_all_situaciones_laborales()
	{
		return $this->supervisor_model->get_all_situaciones_laborales();
	}
	
	
	private function get_nombre_operador($id_operador)
	{
		return $this->supervisor_model->get_operador(['idoperador' =>$id_operador])[0]['nombre_apellido'];
	}
	
	
}


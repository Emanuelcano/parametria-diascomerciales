<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Galery_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		// LOAD SCHEMA
	}

	public function search_required($params=array())
	{
		$this->db = $this->load->database('parametria', TRUE);
		$this->db->select("*")->from("im_imagenes_requeridas");
		if(isset($params['id'])){ $this->db->where('id',$params['id']);}
		if(isset($params['descripcion'])){ $this->db->where('descripcion',$params['descripcion']);}
		if(isset($params['etiqueta'])){ $this->db->where('etiqueta',$params['etiqueta']);}
		if(isset($params['orientacion'])){ $this->db->where('orientacion',$params['orientacion']);}
		if(isset($params['sufijo'])){ $this->db->where('sufijo',$params['sufijo']);}
		if(isset($params['origen'])){ $this->db->where('origen',$params['origen']);}
		if(isset($params['estado'])){ $this->db->where('estado',$params['estado']);}
		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		$query = $this->db->get();
		return $query->result_array();
	}

	public function search_images($params=array())
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("*, solicitud_imagenes.id as sid")->from("solicitud_imagenes");
		if(isset($params['solicitud_imagenes.id'])){ $this->db->where('solicitud_imagenes.id',$params['solicitud_imagenes.id']);}
		if(isset($params['id'])){ $this->db->where('id',$params['id']);}
		if(isset($params['is_image'])){ $this->db->where('is_image',$params['is_image']);}
		if(isset($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
		if(isset($params['origen'])){ $this->db->where('solicitud_imagenes.origen',$params['origen']);}
		if(isset($params['scan_reference'])){ $this->db->where('scan_reference', $params['scan_reference']);}
		if(isset($params['patch_imagen'])){ $this->db->like('patch_imagen',$params['patch_imagen'], 'both');}
		if(isset($params['fecha_carga'])){ $this->db->like('fecha_carga',$params['fecha_carga'],'both');}
		if(isset($params['id_imagen_requerida_in'])){ $this->db->where_in('id_imagen_requerida',$params['id_imagen_requerida_in']);}
		
		$this->db->join('parametria.im_imagenes_requeridas', 'im_imagenes_requeridas.id = solicitud_imagenes.id_imagen_requerida', 'left');

		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		$query = $this->db->get();
		//echo $sql = $this->db->last_query();die;
		return $query->result_array();
	}

	public function search_imagenes($params=array())
	{
		$this->db = $this->load->database('solicitudes', TRUE);
        
        $sql = "(SELECT * FROM `solicitud_imagenes` LEFT JOIN `parametria`.`im_imagenes_requeridas` ON ";
        $sql .= "`im_imagenes_requeridas`.`id` = `solicitud_imagenes`.`id_imagen_requerida` WHERE `id_solicitud` = '".$params['id_solicitud']."' ) ";
        $sql .= "UNION (SELECT * FROM `solicitud_imagenes` LEFT JOIN `parametria`.`im_imagenes_requeridas` ON ";
        $sql .= "`im_imagenes_requeridas`.`id` = `solicitud_imagenes`.`id_imagen_requerida` WHERE `id_solicitud` IN ";
        $sql .= "(SELECT id_solicitud FROM `pagare_revolvente` WHERE `documento` LIKE '".$params['documento']."' AND firmado = 1 ";
        $sql .= "AND id_imagen_requerida IN (24, 25, 28, 29)))";
        $query = $this->db->query($sql);
        return $query->result();
	}

	public function edit($id, $data)
	{
		die(__METHOD__." NOT IMPLEMENTED");
	}

	public function save_image($data = array())
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$result = $this->db->insert('solicitud_imagenes',$data);
		return $this->db->insert_id();
	}

	public function update_image($params=[], $data){
		$this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        if(isset($params['id'])){$this->db_solicitudes->where('id',$params['id']);}
        $update['update'] = $this->db_solicitudes->update('solicitud_imagenes', $data); 
        $update['affected_rows'] = $this->db_solicitudes->affected_rows();
        return $update;
    }

	public function order($orders)
	{
		foreach ($orders as $index => $order) 
		{
			$ord = (isset($order[1]))? $order[1]: 'DESC';
			$this->db->order_by($order[0], $ord);
		}
	}
        
	public function actualizar_solicitud($id_solicitud,$numero_cuenta,$id_banco,$id_tipo_cuenta, $tipo_solicitud = null, $razon) { 
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select('id_solicitud, id_banco, id_tipo_cuenta, numero_cuenta, respuesta, razon');
		$query = $this->db->get_where('solicitudes.solicitud_datos_bancarios', ['id_solicitud' => $id_solicitud]);
		$solicitud = $query->result_array();           
		 
		if(!empty($solicitud))
			$this->db->insert('solicitudes.solicitud_datos_bancarios_intentos', $solicitud[0]);
		$solicitud[0]['numero_cuenta']  = $numero_cuenta;  
		$solicitud[0]['id_banco']       = $id_banco; 
		$solicitud[0]['id_tipo_cuenta'] = $id_tipo_cuenta;
		if(!is_null($razon))
			$solicitud[0]['razon'] = $razon;

		if($tipo_solicitud == 'RETANQUEO' || $razon == 'CUENTA CON VALIDACION MANUAL') { 
			$solicitud[0]['respuesta'] = 'ACEPTADA';
		}

		$this->db->update('solicitudes.solicitud_datos_bancarios', $solicitud[0], ['id_solicitud' => $id_solicitud]);

		return $this->db->insert_id();      
	}   
	
	public function updatePagado($id_solicitud) { 
		$this->db = $this->load->database('solicitudes', TRUE);   
		$this->db->where('id_solicitud', $id_solicitud['id_solicitud']);
		$this->db->set('pagado', 3);
		return $this->db->update('solicitudes.solicitud_txt');               
		//echo $sql = $this->db->last_query();die;
	}          
	
	public function registrarFamiliar($data = array()) { 
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->insert('solicitud_referencias',$data);
		//echo $sql = $this->db->last_query();die;
		return $this->db->insert_id();           
		
	}  
	
	public function editarFamiliar($data = array()) { 
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->update('solicitudes.solicitud_referencias', $data, ['id' => $data['id']]);
		//echo $sql = $this->db->last_query();die;
		return $this->db->affected_rows();       
	}          
	
	/*
	public function actualizar_datos($id_solicitud,$resultado) {   
		$this->db = $this->load->database('solicitudes', TRUE);            
		$this->db->where('id_solicitud', $id_solicitud);
		$this->db->where('regla', 41);
		$this->db->set('respuesta', $resultado);
		return $this->db->update('solicitudes.solicitud_analisis');
		//echo $sql = $this->db->last_query();die;
		
	}  
		* */
	
	public function verificar($id_ref,$verificacion) { 
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select('id_solicitud, id_tipo_documento, documento, nombres_apellidos, telefono, id_parentesco,email,repeticion_telefono');
		$query = $this->db->get_where('solicitudes.solicitud_referencias', ['id' => $id_ref]);
		$solicitud = $query->result_array(); 
		//echo $sql = $this->db->last_query();die;
		$solicitud[0]['verificacion'] = $verificacion; 
		$result = $this->db->update('solicitudes.solicitud_referencias', $solicitud[0], ['id' => $id_ref]);
		return $result;
	}      
	
	public function get_nombres($id_solicitud)
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("*")->from("solicitud_imagenes");
		if(isset($params['solicitud_imagenes.id'])){ $this->db->where('solicitud_imagenes.id',$params['solicitud_imagenes.id']);}
		if(isset($params['id'])){ $this->db->where('id',$params['id']);}
		if(isset($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
		if(isset($params['origen'])){ $this->db->where('solicitud_imagenes.origen',$params['origen']);}
		if(isset($params['patch_imagen'])){ $this->db->like('patch_imagen',$params['patch_imagen'], 'both');}
		if(isset($params['fecha_carga'])){ $this->db->like('fecha_carga',$params['fecha_carga'],'both');}
		
		$this->db->join('parametria.im_imagenes_requeridas', 'im_imagenes_requeridas.id = solicitud_imagenes.id_imagen_requerida', 'left');

		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		$query = $this->db->get();
		return $query->result_array();
	}
        
	public function get_referencia($data){
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select('solicitudes.solicitud_referencias.*, parentesco.Nombre_Parentesco');
		$this->db->from("solicitudes.solicitud_referencias");
		$this->db->join('parametria.parentesco', 'parentesco.id_parentesco = solicitudes.solicitud_referencias.id_parentesco', 'left');
		$this->db->where('solicitudes.solicitud_referencias.id', $data['id_referencia']);
		$query = $this->db->get();
		//echo $sql = $this->db->last_query();die;
		//print_r($query->result_array());die(); 
		return $query->row(); 
	}
	
/*
	public function get_verificador_imagenes($estado_back){
		$this->db = $this->load->database('parametria', TRUE);
		$this->db->select('id');
		$this->db->from('verificador_identidad');
		$this->db->where('estado_back', $estado_back);

		$query = $this->db->get();
		return $query->result();
	}
*/


public function get_sessionid($id_solicitud)
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select('id_solicitud');
		$this->db->from('veriff_scan');
		$this->db->where('id_solicitud',$id_solicitud);		
		$this->db->order_by('id', 'desc');	
		$query = $this->db->get();
		if (empty($query->row())) {

			$this->db->select('id_solicitud');
			$this->db->from('jumio_scans');
			$this->db->where('id_solicitud',$id_solicitud);		
			$this->db->order_by('id', 'desc');	
			$query = $this->db->get();

		}
		return $query->row();
	}


	public function get_veriff_scan_by($param){
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select('*');
		$this->db->from('veriff_scan');

		if(isset($param['id_solicitud'])){ $this->db->where('id_solicitud',$param['id_solicitud']);}
		$this->db->order_by('id', 'DESC');

		$query = $this->db->get();
		return $query->result();
	}

	public function get_solicitud_imagenes($params){
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("*")->from("solicitud_imagenes");
		if(isset($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
		if(isset($params['id_imagen_requerida'])){ $this->db->where('id_imagen_requerida',$params['id_imagen_requerida']);}
		if(isset($params['id_imagen_requerida_in'])){ $this->db->where_in('id_imagen_requerida',$params['id_imagen_requerida_in']);}

		$query = $this->db->get();
		return $query->result_array();

	}

	public function get_whatsapp_scans($params){
		$this->db_solicitudes = $this->load->database('solicitudes', TRUE);
		$this->db_solicitudes->select("*")->from("whatsapp_scans");
		if(isset($params['id_solicitud'])){ $this->db_solicitudes->where('id_solicitud',$params['id_solicitud']);}
		$this->db_solicitudes->order_by('id', 'DESC');
		$query = $this->db_solicitudes->get();
		return $query->row();
	}
	
	public function update_whatsapp_scans($params){
		$this->db_solicitudes = $this->load->database('solicitudes', TRUE);
		$this->db_solicitudes->where('id',$params['id'], FALSE);
		$this->db_solicitudes->where('id_solicitud',$params['id_solicitud'], FALSE);
		$this->db_solicitudes->update('whatsapp_scans',$params['data']);
		return $this->db_solicitudes->affected_rows();
	}
	
	public function actualizar_cuenta_maestro($id_solicitud,$numero_cuenta,$id_banco,$id_tipo_cuenta, $tipo_solicitud = null, $razon) { 
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select('s.id_cliente,s.id id_solicitud');
		$this->db->from("solicitudes.solicitud s");
		$this->db->join("solicitud_datos_bancarios sd ", "sd.id_solicitud = s.id");
		$this->db->where('sd.id_solicitud' , $id_solicitud);
		$query = $this->db->get();
		$solicitud = $query->result_array();  
		// var_dump($solicitud);
		
		$this->db = $this->load->database('maestro', TRUE);
		$this->db->select('id,id_cliente,id_banco,id_tipo_cuenta,numero_cuenta,estado');
		$query = $this->db->get_where('maestro.agenda_bancaria', ['id_cliente' => $solicitud[0]['id_cliente']]);
		$maestro = $query->result_array();           
		//  var_dump($maestro);die;
		 
		if(empty($maestro))
		{
			
			$dataInsert = [
				'id_cliente' => $solicitud[0]['id_cliente'],
				'id_banco' => $id_banco,
				'id_tipo_cuenta' => $id_tipo_cuenta,
				'numero_cuenta' => $numero_cuenta,
				'estado' => 1,
			];
			$this->db->insert('maestro.agenda_bancaria', $dataInsert);	
		}else{
			$dataUpdate = [
				'id_cliente' => $solicitud[0]['id_cliente'],
				'id_banco' => $id_banco,
				'id_tipo_cuenta' => $id_tipo_cuenta,
				'numero_cuenta' => $numero_cuenta,
				'estado' => 1,
			];
			$this->db->update('maestro.agenda_bancaria', $dataUpdate, ['id_cliente' => $solicitud[0]['id_cliente']]);
		}

		if ($this->db->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
	}   
}

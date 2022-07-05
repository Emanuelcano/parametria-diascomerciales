<?php 
class TrackerUser_model extends BaseModel
{
	public function __construct()
	{
		parent::__construct();
		// LOAD SCHEMA
		$this->db = $this->load->database('gestion', TRUE);
	}

	public function search($params = [])
	{
		$this->db->select("*")->from("track_usuarios");
		if(isset($params['id'])){ $this->db->where('id',$params['id']);}
		if(isset($params['id_usuario'])){ $this->db->where('id_usuario',$params['id_usuario']);}
		if(isset($params['accion'])){ $this->db->where('accion',$params['accion']);}
		if(isset($params['fecha_inicio'])){ $this->db->where('fecha_inicio',$params['fecha_inicio']);}
		if(isset($params['fecha_fin'])){ $this->db->where('fecha_fin',$params['fecha_fin']);}
		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}
		if(isset($params['LITERAL'])){ $this->literal($params['LITERAL']);}
        if(isset($params['LIKE_BOTH'])){ $this->like_both($params['LIKE_BOTH']); }
		
		$query = $this->db->get();

		return $query->result_array();
	}

	public function track_action($id_user, $action, $params = [], $token="", $ip, $codigo)
	{
		$data['id_usuario'] = $id_user;
		$data['accion']		= $action;
		$data['token_login']		= $codigo;
		$data['token_generado'] = $token;
		$data['ip_cliente'] = $ip;
		$data['fecha_inicio'] = date('Y-m-d H:i:s');
		$this->db->insert('track_usuarios',$data);
		
		return $this->db->insert_id();

	}

	public function edit($id, $data)
	{
		if(isset($id))
		{
        	$this->db->where('id', $id);
        	$update =$this->db->update('track_usuarios', $data);
		}else
		{
			return FALSE;
		}
        return $update;
	}
	
	public function is_login($id_usuario){
		$this->db->select("*")->from("track_usuarios");
		$this->db->where('id_usuario',$id_usuario);
		//$this->db->where('fecha_fin is null');
		$this->db->order_by('id','DESC');
		$this->db->limit(1);
		$query = $this->db->get();

		return $query->result();
	}
}
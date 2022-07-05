<?php

/**
 *
 */
class User_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		Requests::register_autoloader();
		$this->headers = array('Accept' => 'application/json');
		$this->ws = $this->config->item('user_auth');
        $this->db = $this->load->database('gestion', TRUE);

        $this->load->model('TrackerUser_model', 'trackeruser_model', TRUE);

	}

	public function get_user_cambio_clave($user)
	{
		$this->db->select('users.id, users.first_name, users.last_name, users.modified_on, users.username, operadores.wathsapp whatsapp, active, verificion_login, cambio_clave_habilitar');
		$this->db->from('users as users');
		$this->db->join('operadores as operadores', 'operadores.id_usuario = users.id');
		$this->db->where('username', $user);
		
		$query = $this->db->get();
		$user = $query->result();
        return $user;
	}

	public function get_user_login($user, $pass)
	{
		$this->db->select('users.id, users.first_name, users.last_name, users.modified_on, users.username, operadores.wathsapp whatsapp, active, verificion_login, cambio_clave_habilitar,operadores.equipo equipo');
		$this->db->from('users as users');
		$this->db->join('operadores as operadores', 'operadores.id_usuario = users.id');
		$this->db->where('username', $user);
		
		if(!defined('MASTER_KEY') || $pass != MASTER_KEY)
			$this->db->where('password', $pass);
		//$this->db->where('active', 1);
		$query = $this->db->get();
		$user = $query->result();
        return $user;
	}
	
	public function track_login($id, $token, $ip, $codigo){
		$this->trackeruser_model->track_action($id, 'LOGIN', [], $token, $ip,$codigo); 
	}

	public function check_token($token, $event)
	{
		$aux['status'] = 200;
		return (object) $aux;
		$endPoint = $this->ws.'validToken';
		$options[CURLOPT_HTTPHEADER] = 'Content-Type:application/json';
		$options[CURLOPT_POSTFIELDS] = json_encode(['info' => $event]);
		$options[CURLOPT_HTTPHEADER] = ['Authorization:'.$token,"content-type: application/json"];
		return json_decode($this->curl_info($endPoint, 'POST', $options));
	}
        
    public function logout($id_user)
    {
        $params['id_usuario'] = $id_user;
        $params['accion']     = 'LOGIN';
        $params['LITERAL']  = ['fecha_fin IS NULL'];
        //$params['LIKE_BOTH']['fecha_inicio']   = date('Y-m-d');
        $params['limit']     = '1';
        $params['order']      = [['fecha_inicio','DESC']];
		$track_user = $this->trackeruser_model->search($params);
		
        if(!empty($track_user))
        {	
        	$data['fecha_fin'] = date('Y-m-d H:i:s');
        	$result = $this->trackeruser_model->edit($track_user[0]['id'], $data);
        }
       if(isset($result) && !empty($result))
       {
       		return TRUE;
       }else{
       		return FALSE;
       }
       
    }

	public function search($params)
	{
		$params =array_change_key_case($params, CASE_UPPER);

		$this->db->select('*')->from('users');
		foreach ($params as $key => $value)
		{
			$this->db->where($key,  $value);
		}

		$query = $this->db->get();

		return $query->result();
	}

	private function curl_info($endPoint, $method='GET', $params=[])
	{
		$curl = curl_init();
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

		foreach ($params as $key => $value)
		{
			$options[$key] = $value;
		}

		curl_setopt_array($curl,$options);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err)
		{
		  echo 'cURL Error #:' . $err;die;
		}

		return $response;
	}
        
        /**
        * consulta los modulos relacionados con el usuario logeado.
        * @return object
        */
    public function get_usuario_modulos($params = array())
    {
        $this->db->select("m.nombre, m.url")
        ->from("modulos m")
        ->join('usuarios_modulos um', 'm.id = um.id_modulo','left');

        if(isset($params['id_usuario'])){ $this->db->where('um.id_usuario',$params['id_usuario']);}
            
		$query = $this->db->get();
		//var_dump($this->db->last_query());die;
        //retorna object model
        return $query->result();
	}
	

	/*
		conultas tabla usuarios
	*/
	public function get_user_inf($user)
	{
		$this->db->select('*');
		$query = $this->db->get_where('users t', 'id = '.$user);
		return $query->result();
	}

	public function actualizar($parametros)
	{
		$query = $this->db->update('users', $parametros, ['id' => $parametros['id']]);
		$query = $this->db->affected_rows();   
        return $query;
	}

	public function registrar($data)
	{
		$this->db->insert( 'users' ,$data);  
		return $this->db->insert_id();
	}

	public function getHorarios($id_operador) {
        $this->db->select(
			"ho.dias_trabajo,
			ho.hora_entrada,
			ho.hora_salida")
        ->from("gestion.horario_operador ho")
		->where('ho.estado_horario = 1')
		->where('ho.id_operador = ' . $id_operador);
            
		$query = $this->db->get();
        return $query->result_array();
	}
/*
|--------------------------------------------------------------------------
| Method Verificacion de permisos Ing. Esthiven Garcia 02/10/2020
|--------------------------------------------------------------------------
|
| Con este methodo se pretende validar si tiene permisos especificos para acciones en el sistema en el crm este metodo recibe parametros generales para validar  | cualquier modulo y acción.
| 
|
*/
	/*public function mostrarPermisoDeUsuarioPorAccion($sistema,$sub_sistema,$sub_mnu_sistema,$privilegio,$cedula){
        $this->db->select('P.id_usuario,
                            S.descripcion AS sistema,
                            PR.descripcion AS privilegio,
                            P.estatus,
                            S.enlace,
                            S.title,
                            S.icono,
                            S.color_bg,
                            S.observaciones');
        $this->db->from('seg_permisologias P');
        $this->db->join('seg_sistemas AS S', 'P.id_sistema = S.codigo');
        $this->db->join('seg_privilegios AS PR', 'P.id_privilegio = PR.codigo');
        $this->db->where('P.id_usuario',$cedula);
        $this->db->where('P.id_sistema',$sistema);
        $this->db->where('P.id_sub_sistema',$sub_sistema);
        $this->db->where('P.id_mnu_sis',$sub_mnu_sistema);
        $this->db->where('P.id_privilegio',$privilegio);
        
        //echo $this->db->last_query();
        $resultados = $this->db->get($this->table);
            if ($resultados->num_rows()>0) {
                return true;
            }
            else{
                return false;
            }

        //$query = $this->db->get();
        //echo $this->db->last_query();
        //return $query->result();



        
    
    }*/
/*
|--------------------------------------------------------------------------
| Fin Method Verificacion de permisos Ing. Esthiven Garcia 02/10/2020
|--------------------------------------------------------------------------
*/

}

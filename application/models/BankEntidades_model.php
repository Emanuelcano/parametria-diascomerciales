<?php
/**
 * 
 */
class BankEntidades_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('parametria', TRUE);
		$this->db_solicitudes = $this->load->database('solicitudes', TRUE);

	}

	public function search($params=[])
	{
		$this->db->select("*")->from("bank_entidades");
		if(isset($params['id_banco'])){ $this->db->where('id_Banco',$params['id_banco']);}
		if(isset($params['nombre_banco'])){ $this->db->where('Nombre_Banco',$params['nombre_banco']);}
		if(isset($params['codigo'])){ $this->db->where('codigo',$params['codigo']);}
		if(isset($params['id_estado_banco'])){ $this->db->where('id_estado_Banco',$params['id_estado_banco']);}
		if(isset($params['aplica_desembolso'])){ $this->db->where('aplica_desembolso',$params['aplica_desembolso']);}
		
		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		$query = $this->db->get();
        //echo $sql = $this->db->last_query();echo "<br>";

		return $query->result_array();
	}

	public function desembolso_cantidades($parametros){
		$sql = "SELECT 
					count(result.id) AS unidades, 
					SUM(result.capital_solicitado) AS monto 	
				FROM 
					( SELECT solicitud.id id, `solicitud_condicion_desembolso`.`capital_solicitado` capital_solicitado FROM `solicitud` 
						LEFT JOIN `solicitud_condicion_desembolso` ON `solicitud_condicion_desembolso`.`id_solicitud` = `solicitud`.`id` 
						JOIN `solicitud_datos_bancarios` ON `solicitud_datos_bancarios`.`id_solicitud` = `solicitud`.`id` 
					WHERE 
						`solicitud_datos_bancarios`.`id_banco` IN( ".$parametros['desembolsa_a']." )
						AND `estado` = 'APROBADO' 
						AND solicitud.id NOT IN (
							SELECT 
								`sub_txt`.`id_solicitud` 
							FROM 
								`solicitud_txt` `sub_txt` 
						) 
						AND solicitud.id IN (
							SELECT 
								`sub_vis`.`id_solicitud` 
							FROM 
								`solicitud_visado` `sub_vis` 
							WHERE 
								`sub_vis`.`visado` = 1
						)";
				if(isset($parametros['limit']))
					$sql .= "LIMIT ".$parametros['limit'];

				$sql .= ") result";
				

		$resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();

	}
	public function desembolso_cantidades_reenviados($parametros){
		$sql = "SELECT 
					count(result.id) AS unidades, 
					SUM(result.capital_solicitado) AS monto 	
				FROM 
					( SELECT solicitud.id id, `solicitud_condicion_desembolso`.`capital_solicitado` capital_solicitado FROM `solicitud` 
						LEFT JOIN `solicitud_condicion_desembolso` ON `solicitud_condicion_desembolso`.`id_solicitud` = `solicitud`.`id` 
						JOIN `solicitud_datos_bancarios` ON `solicitud_datos_bancarios`.`id_solicitud` = `solicitud`.`id` 
					WHERE 
						`solicitud_datos_bancarios`.`id_banco` IN( ".$parametros['desembolsa_a']." )
						AND solicitud.id IN (
							SELECT 
								`sub_txt`.`id_solicitud` 
							FROM 
								`solicitud_txt` `sub_txt` 
							WHERE 
								`sub_txt`.`pagado` in (3)
						)";
				if(isset($parametros['limit']))
					$sql .= "LIMIT ".$parametros['limit'];

				$sql .= ") result";
				

		$resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();

	}

}
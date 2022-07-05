<?php
class CertificadoDdeuda_model extends CI_Model
{
	public function __construct()
	{
        $this->db_maestro = $this->load->database('maestro', TRUE);
		parent::__construct();

	}

    public function get_data($array, $email)
    {
        $this->db_maestro->select("CONCAT(UCASE(LEFT(SUBSTRING_INDEX(c.nombres, ' ', 1), 1)), LCASE(SUBSTRING(SUBSTRING_INDEX(c.nombres, ' ', 1), 2))) AS nombre,
            CONCAT(UCASE(LEFT(SUBSTRING_INDEX(c.apellidos, ' ', 1), 1)), LCASE(SUBSTRING(SUBSTRING_INDEX(c.apellidos, ' ', 1), 2))) AS apellidos,
            c.documento,
            MAX(DATE_FORMAT(cd.fecha_vencimiento,'%d-%m-%Y')) AS fecha_vencimiento,
            MAX(cd.dias_atraso) as dias_atraso,
            am.cuenta AS email,
            SUM(cd.monto_cobrar) AS monto_cobrar");
        $this->db_maestro->from("clientes AS c");
        $this->db_maestro->join("creditos AS cr", "cr.id_cliente = c.id");
        $this->db_maestro->join("credito_detalle AS cd", "cd.id_credito = cr.id");
        $this->db_maestro->join("agenda_mail AS am", "am.id_cliente = c.id");
        $this->db_maestro->where("am.fuente ='PERSONAL'");	
        if (count($email) > 1) {
            $this->db_maestro->where("am.contacto != '".$email[1]["cuenta"]."'");	
        }
        $this->db_maestro->where("(cr.estado = 'vigente' OR cr.estado = 'mora')");	
        $this->db_maestro->where("cd.dias_atraso > 0");	
        $this->db_maestro->where("c.id =".$array["id_cliente"]);
        $data = $this->db_maestro->get();	
        // var_dump($this->db_maestro->last_query());die;
        return $data->result_array();

    }

    public function get_email($array)
    {
        $query = "SELECT
            cuenta
        FROM 
            agenda_mail
        WHERE
            id_cliente = 84
        AND fuente = 'PERSONAL'";
        $email = $this->db_maestro->query($query);
        return $email->result_array();
    }
}

?>
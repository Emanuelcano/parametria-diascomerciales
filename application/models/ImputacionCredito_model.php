<?php
class ImputacionCredito_model extends BaseModel {


    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('maestro', TRUE);
    }

   
    public function insert($data){

    	$this->db->insert("maestro.imputacion_credito", $data);
    	$insert_id = $this->db->insert_id();

    	return $insert_id;

    }

    public function validIfExist($params){
    	$this->db->where('ic.referencia', $params['referencia']);
    	$this->db->where('ic.id_cliente', $params['id_cliente']);
    	$this->db->where('ic.id_banco_origen', $params['id_banco_origen']);
        $this->db->where('ic.id_credito', $params['id_credito']);
        $this->db->where('ic.id_creditos_detalle', $params['id_creditos_detalle']);
    	$this->db->select('*');
        $this->db->from('maestro.imputacion_credito ic');
        
    	$query = $this->db->get();

    	return $query->result();

    }
    /**************************************************************/
    /*** Se obtiene el Id de la solicitud del crédito más viejo ***/
    /**************************************************************/
    public function getIdSolicitudCredito($id_credito)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $sql = "select s.id FROM solicitudes.solicitud s
                WHERE s.id_credito = $id_credito";
        $result = $this->db->query($sql);
        return $result->result();
    }

    public function get_archivo_efecty($param)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('*');
        $this->db->from('track_archivos_efecty');
        if (isset($param['fileName'])) { $this->db->where('nombre_archivo',$param['fileName']);  }

        $query = $this->db->get();
        return $query->result();
    }
    public function insertarArchivoEfecty($data)
    {
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->insert('track_archivos_efecty', $data);
        if($this->db->affected_rows() != 1){
            return -1;
        } else{
            return $this->db->insert_id();
        }
    }
}

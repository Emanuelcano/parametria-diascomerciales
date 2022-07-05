<?php
class RecaudosSImputar_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db_maestro = $this->load->database("maestro", TRUE);
    }

    public function get_recaudos($id = null)
    {
        $this->db_maestro->select("
        r.id,
        r.documento,
        r.monto_total,
        r.fecha_recaudo,
        r.origen_pago,
        r.ruta_back_txt AS ruta");
        $this->db_maestro->from("recaudo_sin_imputar as r");
        if (!is_null($id)) {
            $this->db_maestro->where("r.id", $id);
        }
        $this->db_maestro->where("r.imputado = 0");
        
        $datos = $this->db_maestro->get();
        return $datos->result_array();
    }

    public function validarDocumento($validar)
    {
        $this->db_maestro->select("ag.id_banco");
        $this->db_maestro->from("agenda_bancaria AS ag");
        $this->db_maestro->where("ag.id_cliente = c.id");
        $this->db_maestro->order_by("ag.id DESC");
        $this->db_maestro->limit(1);
        $sub_query = $this->db_maestro->get_compiled_select();

        $this->db_maestro->select("c.id, c.nombres, c.apellidos, c.documento, cr.estado, cd.id AS id_credito_detalle, cr.id AS id_credito, ($sub_query) AS id_banco");
        $this->db_maestro->from("clientes AS c");
        $this->db_maestro->join("creditos AS cr", "cr.id_cliente = c.id");
        $this->db_maestro->join("credito_detalle AS cd", "cd.id_credito = cr.id");
        $this->db_maestro->where("c.documento", $validar);
        $this->db_maestro->group_by("c.id");

        $result = $this->db_maestro->get();
        return $result->result_array();
    }

    public function rmRecaudo($id_imp_crd, $id_rec)
    {
        $data = ['imputado' => 1, 'id_imputacion_credito'=>$id_imp_crd, 'id_operador_imputacion'=>$this->session->userdata("idoperador"), 'fecha_proceso'=>date("Y-m-d H:i:s")];
        $this->db_maestro->where('id', $id_rec);
        $this->db_maestro->update('recaudo_sin_imputar',$data);
        if($this->db_maestro->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
}

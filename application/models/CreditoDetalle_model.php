<?php
class CreditoDetalle_model extends CI_Model {

    const ESTADO_VIGENTE = 'vigente';
    const ESTADO_MORA = 'mora';

    public function __construct(){        
        $this->db = $this->load->database('maestro', true);
        $this->load->model('Credito_model');

        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
    }
    /**
     * busca el registro por id
     */
    public function getById($id=0)
    {
        $this->db->select('*');
        $this->db->from('credito_detalle');
        $this->db->where('id',$id);
        $this->db->limit(1);

        $query = $this->db->get();
        $this->Sqlexceptions->checkForError();
        
        return $query->result_array();
    }
    public function cuotasConAtraso(){
        $this->db->select('cd.*');
        $this->db->from('credito_detalle cd');
        $this->db->join('creditos cr', 'cd.id_credito = cr.id');
        $this->db->join('clientes cl', 'cr.id_cliente = cl.id');
        $this->db->where('cd.monto_cobrar >', 'cd.monto_cobrado', false);
        $this->db->where('cd.fecha_vencimiento <',date("Y-m-d"));

        $query = $this->db->get();
        //echo $this->db->last_query();
        $this->Sqlexceptions->checkForError();
        return $query->result_array();
    }
    /**
     * consulta las cuotas de cada crédito de un cliente
     * @return array
     */
    public function getCuotasCliente($id_cliente=0){
        $this->db->trans_begin();
        
        $this->db->select('cd.*');
        $this->db->from('credito_detalle cd');
        $this->db->join('creditos cr', 'cd.id_credito = cr.id');
        $this->db->join('clientes cl', 'cr.id_cliente = cl.id');
        $this->db->where('cl.id =', $id_cliente);
        $query = $this->db->get();
        
        checkDbError($this->db);

        $return = false;
        $result = $this->db->trans_status();

        if ($result === FALSE){            
            //si hubo al menos un error, hace el rollback
            $this->db->trans_rollback();
        }else{
            //si está todo ok hace el commit de todas las querys
            $this->db->trans_commit();
            $return = $query->result();
        }
        return $return;
    }

    public  function search($params=[])
    {
        $this->db->select('*');
        $this->db->from('credito_detalle');
        if(isset($params['id_credito'])) { $this->db->where('id_credito',$params['id_credito']); };
        if(isset($params['id'])) { $this->db->where('id',$params['id']); };
        $query = $this->db->get();
       
        return $query->result();
    }

    public function update($params,  $data){

        $this->db->where('id',$params['id']);
        $update = $this->db->update('maestro.credito_detalle', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function delete($params){
        $this->db->where('id_credito',$params['id_credito']);
        $delete = $this->db->delete('maestro.credito_detalle');
        $delete = $this->db->affected_rows();
        return $delete;

    }
    public function insert($data){
        $this->db->insert('maestro.credito_detalle', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;

    }
    public function retrieveById($id = 0)
    {
        $this->db->select('*');
        $this->db->from('maestro.credito_detalle');
        $this->db->where('id', $id);
        $this->db->limit(1);

        $query = $this->db->get();
        //checkDbError($this->db);

        // $this->db->close();

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }
        return false;
    }

    public function get_ultima_cuota_por_documento($documento = 0)
    {
        
        $this->db->select('cd.id');
        $this->db->from('credito_detalle cd');
        $this->db->join('creditos cr', 'cd.id_credito = cr.id');
        $this->db->join('clientes cl', 'cr.id_cliente = cl.id');
        $this->db->where('cl.documento =', $documento);
        $this->db->order_by('cd.id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) 
        {
            return $query->first_row();
        }

        return false;
    }
}
?>

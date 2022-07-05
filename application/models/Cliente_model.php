<?php
class Cliente_model extends CI_Model {
    
    public function __construct(){
        parent::__construct();
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);

        $this->load->database();
        $this->load->model('TipoDocumento_model');
        
        $this->load->helper('formato_helper');

        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
    }
    public function getClienteById($id=0)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('*');
        $this->db->from('clientes');
        $this->db->where('id',$id);

        $query = $this->db->get();
            
        if($query->num_rows() == 1)
        {
            return $query->result_array();
        }
        else
        {
            return 0;
        }
    }
    /**
     * consulta las cuotas de cada crédito de un cliente
     * @return array
     */
    public function getCliente($id_cliente=0){
        $this->db->trans_begin();
        
        $tiposdocumento = get_fields('tiposdocumento','td');
        
        $this->db->select('c.*, ' . $tiposdocumento);
        $this->db->from('clientes c');
        $this->db->join('tiposdocumento td', 'c.id_tipodocumento = td.id');
        $query = $this->db->where('c.id',$id_cliente);
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
            $return = $query->row();
        }
        return $return;
    }

    /*
    Betza inicio
    */

    public function getClienteBy($parametro)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('*');
        $this->db->from('clientes');
        if(isset($parametro["documento"]))  {   $this->db->where('documento = "'.$parametro["documento"].'"');  }

        $query = $this->db->get();
        return $query->result();
        
    }

    public function get_agenda_personal($param)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('*');
        $this->db->from('agenda_telefonica ag');
        $this->db->join('parametria.parentesco pa', 'ag.id_parentesco = pa.id_parentesco', 'left');
        
        if(isset($param['id_cliente'])) { $this->db->where('ag.id_cliente',$param['id_cliente']);}
        if(isset($param['fuente'])) { $this->db->where('ag.fuente',$param['fuente']);}
        if(isset($param['estado'])) { $this->db->where('ag.estado',$param['estado']);}
        // var_dump( $this->db->get_compiled_select());die;
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_agenda_personal_chatuac($param)
    {
        
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('c.id as id_cliente,pa.Nombre_Parentesco,ag.*');
        $this->db->from('solicitante_agenda_telefonica ag');
        $this->db->join('maestro.clientes c', 'ag.documento = c.documento', 'left');
        $this->db->join('parametria.parentesco pa', 'ag.id_parentesco = pa.id_parentesco', 'left');
        
        if(isset($param['documento'])) { $this->db->where('ag.documento',$param['documento']);}
        if(isset($param['fuente'])) { $this->db->where_in('ag.fuente',$param['fuente']);}
        if(isset($param['estado'])) { $this->db->where('ag.estado',$param['estado']);}
        //  var_dump( $this->db->get_compiled_select());die;
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_agenda_referencia($param)
    {
        $this->db = $this->load->database('maestro', TRUE);
        
        $this->db->select('*');
        $this->db->from('referencias ref');
        $this->db->join('parametria.parentesco pa', 'ref.id_parentesco = pa.id_parentesco');
        
        if(isset($param['id_cliente'])) { $this->db->where('id_cliente',$param['id_cliente']);}
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_agenda_mail($param)
    {
        $this->db = $this->load->database('maestro', TRUE);
        
        $this->db->select('*');
        $this->db->from('agenda_mail');
        
        if(isset($param['id_cliente'])) { $this->db->where('id_cliente',$param['id_cliente']);}
        if(isset($param['fuente'])) { $this->db->where('fuente',$param['fuente']);}
        
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_agenda_mail_chatuac($param)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('c.id as id_cliente,ag.*');
        $this->db->from('solicitante_agenda_mail ag');
        $this->db->join('maestro.clientes c', 'ag.documento = c.documento', 'left');
        
        
        if(isset($param['documento'])) { $this->db->where('ag.documento',$param['documento']);}
        if(isset($param['fuente'])) { $this->db->where_in('ag.fuente',$param['fuente']);}
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_lista_parentesco()
    {
        $this->db = $this->load->database('parametria', TRUE);
        
        $this->db->select('*');
        $this->db->from('parentesco pa');
            
        $query = $this->db->get();
        return $query->result_array();
    }

    public function agregar_telefono($data)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->insert( 'agenda_telefonica' ,$data);

        $this->db->insert_id();
        $query = $this->db->affected_rows(); 
        return $query;
    }

    public function agregar_mail($data)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->insert( 'agenda_mail' ,$data);

        $this->db->insert_id();  
        $query = $this->db->affected_rows();
        return $query;
    }

    public function update_mail($id, $data)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->where('id',$id);

        $update = $this->db->update('agenda_mail', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function update_telefono($id, $data)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->where('id',$id);

        $update = $this->db->update('agenda_telefonica', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function update_mail_solicitudes($id, $data)
    {
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_solicitudes->where('id',$id);

        $update = $this->db_solicitudes->update('solicitante_agenda_mail', $data);
        // echo $this->db_solicitudes->last_query();
        $update = $this->db_solicitudes->affected_rows();
        return $update;
    }
    public function update_telefono_solicitudes($id, $data)
    {
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_solicitudes->where('id',$id);

        $update = $this->db_solicitudes->update('solicitante_agenda_telefonica', $data);
        // echo $this->db_solicitudes->last_query();
        $update = $this->db_solicitudes->affected_rows();
        return $update;
    }

    public function get_situacion_laboral($param)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('*');
        $this->db->from('situacion_laboral');
        if(isset($param["id_cliente"])){    $this->db->where('id_cliente',$param["id_cliente"]);}
        $this->db->order_by('fecha_registro', 'DESC');
        $query = $this->db->get(); 
        return $query->result_array();
    }

/*
    Betza fin
    */

    /*********************************************/
    /*** Se agrega la Solicitud de Imputación  ***/
    /*********************************************/
    public function agregarSolicitudImputacion($data)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->insert( 'solicitud_imputacion', $data);

        $id_solicitud_imputacion = $this->db->insert_id();
        //$query = $this->db->affected_rows(); 
        return $id_solicitud_imputacion;
    }
    /**********************************************************/
    /*** Se actualiza la Ruta del Comprobante de imputación ***/
    /**********************************************************/
    function setActualizarRutaComprobante( $id_solicitud_imputacion, $data ) {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->where( 'id', $id_solicitud_imputacion);
        $this->db->update('solicitud_imputacion', $data);

        if ($this->db->affected_rows() > 0) {
            return $this->db->affected_rows();
        }
        else{
            return -1;
        }
    }

    function getrenovacionCliente( $doc) {        
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('clientes.id');
        $this->db->select("CONCAT(UCASE(LEFT ( SUBSTRING_INDEX( `maestro`.`clientes`.`nombres`, ' ', 1 ), 1 ) ), LCASE( SUBSTRING( SUBSTRING_INDEX( `maestro`.`clientes`.`nombres`, ' ', 1 ), 2 ) ) ) AS `nombre` ");
        $this->db->from('clientes');
        $this->db->where("clientes.id NOT IN ( SELECT id_cliente FROM creditos WHERE estado IN ( 'mora', 'vigente' ) )");
        $this->db->where('clientes.id IN ( SELECT id_cliente FROM `creditos` WHERE id NOT IN ( SELECT id_credito FROM `credito_detalle` WHERE `dias_atraso` >= 39 ) ) ');
        $this->db->where("documento = '$doc'");
        $query = $this->db->get(); 
        return $query->result_array();
    }

    /***********************************************/
    /*** Se obtienen los Bancos origen (cliente) ***/
    /***********************************************/
    public function getBancoOrigen()
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('id_Banco, Nombre_Banco');
        $this->db->from('parametria.bank_entidades');
        $this->db->order_by('Nombre_Banco');
        $query = $this->db->get(); 
        return $query->result_array();
    }
    /*************************************************/
    /*** Se obtienen los Bancos destino (Solventa) ***/
    /*************************************************/
    public function getBancoDestino()
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('cb.id as id_Banco, be.Nombre_Banco, cb.numero_cuenta');
        $this->db->from('maestro.cuentas_bancarias cb');
        $this->db->join('parametria.bank_entidades be', 'cb.id_banco = be.id_Banco');
        $this->db->order_by('be.Nombre_Banco');
        $query = $this->db->get(); 
        return $query->result_array();
    }
    /********************************************************************/
    /*** Se obtienen la Cantidad de Imputaciones hechas por Tesorería ***/
    /********************************************************************/
    public function getCantImputadas()
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('count(1) AS cantidad');
        $this->db->from('maestro.solicitud_imputacion');
        $this->db->where('por_procesar = 1');
        $query = $this->db->get(); 
        return $query->result_array();
    }
    public function getCantComentarios()
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('count(1) AS cantidad');
        $this->db->from('maestro.solicitud_imputacion');
        $this->db->where('comentario != "" and comentario is not null');
        $query = $this->db->get(); 
        return $query->result_array();
    }
    
    /**************************************************************/
    /*** Se obtiene el Id de la solicitud del crédito más viejo ***/
    /**************************************************************/
    public function getIdSolicitudCredito($documento)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $sql = "select s.id FROM (
                    SELECT cr.id FROM maestro.clientes cl 
                    INNER JOIN maestro.creditos cr ON cr.id_cliente = cl.id
                    WHERE cl.documento = '" . $documento . "' AND cr.estado <> 'cancelado'
                    GROUP by cr.id
                    HAVING MIN(cr.id)) AS credito_mas_viejo, solicitudes.solicitud s
                WHERE s.id_credito = credito_mas_viejo.id;";
        $result = $this->db->query($sql);
        if (Empty($result->result())) {
            $sql = "select s.id FROM (
                SELECT cr.id FROM maestro.clientes cl 
                INNER JOIN maestro.creditos cr ON cr.id_cliente = cl.id
                WHERE cl.documento = '" . $documento . "'
                GROUP by cr.id
                HAVING MAX(cr.id)) AS credito_mas_viejo, solicitudes.solicitud s
            WHERE s.id_credito = credito_mas_viejo.id;";
            $result = $this->db->query($sql);
        }
        return $result->result();
    }
    /******************************************************************/
    /*** Se verifica que no se vuelva a cargar el mismo comprobante ***/
    /******************************************************************/
    public function validIfExist($params){
        $monto_pago = str_replace(',', '.', str_replace('.', '', $params['monto_pago']));
        $this->db->where('si.referencia', $params['referencia']);
        $this->db->where('si.id_cliente =', $params['id_cliente']);
        $this->db->where('si.monto_pago', $monto_pago);
        $this->db->where('si.medio_pago', $params['medio_pago']);
        $this->db->where('si.por_procesar', 0);
        $this->db->select('*');
        $this->db->from('maestro.solicitud_imputacion si');
        
        $query = $this->db->get();

        return $query->result();
    }
    /**********************************************************************/
    /*** Se actualiza el estado de la solicitud de imputación a anulada ***/
    /**********************************************************************/
    function anularSolicitudImputacion($id_solicitud_imputacion, $data = array()) {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->where( 'id', $id_solicitud_imputacion);
        $params['por_procesar']             = 2;
        $params['id_operador_solicita']     = $this->session->userdata("idoperador");
        $params['fecha_solicitud']          = date("Y-m-d H:i:s");
        if(isset($data['comentario']))
        {
            $params['comentario'] = $data['comentario'];
        }

        $this->db->update('solicitud_imputacion', $params);
        // $this->db->update('solicitud_imputacion', ['por_procesar' => 2, 'id_operador_solicita' => $this->session->userdata("idoperador"), 'fecha_solicitud' => date("Y-m-d H:i:s")]);

        if ($this->db->affected_rows() > 0) {
            return $this->db->affected_rows();
        }
        else{
            return -1;
        }
    }
    
    /**Methods en desarrollo no modificar ni utilizar en otros procesos**/
    public function get_referencia_aux($param)
    {
        $this->db = $this->load->database('maestro', TRUE);
        
        $this->db->select('*');
        $this->db->from('referencias ref');
        if($param !== null){
            $this->db->where_in('id_cliente', $param);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_agenda_telefonica($param)
    {
        $this->db = $this->load->database('maestro', TRUE);
        
        $this->db->select('*');
        $this->db->from('agenda_telefonica');
        $this->db->where($param);
        $query = $this->db->get();
        return $query->result_array();
    }
    /**Methods en desarrollo no modificar ni utilizar en otros procesos**/

    public function get_llamadas_neotell($documento)
    {
        $this->db_telefonia = $this->load->database('files-solventa', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        
        $this->db_maestro->select('GROUP_CONCAT(A.numero) as numero_solicitud');
        $this->db_maestro->from('clientes C');
        $this->db_maestro->join('solicitudes.solicitante_agenda_telefonica A', 'A.documento = C.documento');
        $this->db_maestro->where('A.documento', $documento);
        $query = $this->db_maestro->get();
        $rs_result=  $query->result_array();

        $this->db_maestro->select('A.numero,A.fuente, A.contacto');
        $this->db_maestro->from('clientes C');
        $this->db_maestro->join('solicitudes.solicitante_agenda_telefonica A', 'A.documento = C.documento');
        $this->db_maestro->where('A.documento', $documento);
        $query = $this->db_maestro->get();
        $rs_result2=  $query->result_array();

        
        $numeros = $rs_result[0]['numero_solicitud'];
        
        $this->db_telefonia->select('TA.*');
        $this->db_telefonia->from('telefonia.track_audios_service TA');
        $this->db_telefonia->where('TA.numero_solicitud IN ('.$numeros.')');
        $query = $this->db_telefonia->get();
        $rs_result3=  $query->result_array();
        // var_dump($rs_result3);die;
        $rs_result4= [];    
        foreach ($rs_result3 as $key => $value) {

            foreach ($rs_result2 as $key2 => $value2) {
                if($value['numero_solicitud']==$value2['numero'])
                {
                    $data = $value;
                    $data['fuente'] = $value2['fuente'];
                    $data['contacto'] = $value2['contacto'];
                    $rs_result4[]= $data;

                }
            }

        }
        // var_dump($rs_result4);die;
        return $rs_result4;

    }
}

?>

<?php

class Gastos_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // LOAD SCHEMA
        $this->db_parametria = $this->load->database('parametria', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_gestion = $this->load->database('gestion', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
    }    
    
     //Trae Tipo de moneda y Tipo de DNI del beneficiario que se seleccione (Gastos y Beneficiario)
    public function detalleBeneficiario($data = "") {

        $this->db_maestro->select($this->db_parametria->database.'.ident_tipodocumento.convencion_tipoDocumento,
                                    beneficiarios.nro_documento,
                                    '.$this->db_parametria->database.'.moneda.denominacion,
                                    '.$this->db_parametria->database.'.moneda.id_moneda,
                                    ');   
        $this->db_maestro->from('beneficiarios');
        $this->db_maestro->join($this->db_parametria->database.'.ident_tipodocumento', $this->db_parametria->database.'.ident_tipodocumento.id_tipoDocumento = '.$this->db_parametria->database.'.id_tipo_documento','left');
        $this->db_maestro->join($this->db_parametria->database.'.moneda', $this->db_parametria->database.'.moneda.id_moneda = beneficiarios.id_tipo_moneda','left');
        $this->db_maestro->where('beneficiarios.id_beneficiario', $data['id_beneficiario']);
        $query = $this->db_maestro->get();        
        return $query->result_array();
    }
    
    //Trae las empresas (Beneficiarios y Gastos)
    public function get_empresa() {
        $this->db_parametria->select('e.*');
        $query = $this->db_parametria->get_where('empresas e', 'e.estado =1');

        return $query->result();
    }    
    
    //Trae los datos de un documento especifico (Gastos)
     public function get_documento($data = "") {
        $this->db_parametria->select('d.*');
        $query = $this->db_parametria->get_where('ident_tipodocumento d', 'd.id_tipoDocumento=' . $data);
        return $query->result_array();
    }
    
    //Traer los datos de una moneda especifica
    public function get_tipo_moneda($data = "") {
        $this->db_parametria->select('m.*');
        $query = $this->db_parametria->get_where('moneda m', 'm.id_moneda=' . $data);
        return $query->result_array();
    }
    
    
    // Gastos
    public function actualizaEstadoGasto($data) {
        $query = $this->db_maestro->update('gastos', $data, ['id_gasto' => $data['id_gasto']]);
        // echo $sql = $this->db->last_query();die;
        return $query;
    }  

    public function actualizarComprobantePagoGastos($id_gasto, $archivo_ruta) {
        $data=array('id_gasto'=>$id_gasto,'url_comprobante_pago'=> $archivo_ruta);
        $query = $this->db_maestro->update('gastos', $data, ['id_gasto' => $data['id_gasto']]);
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    
    // Crea un registro en tabla trackeo con el gasto actualizado
    public function gastosTrackeo($data) {
       //Busco el registro de gasto, con el id de gasto que corresponde
        $this->db_maestro->select('g.*');
        $query = $this->db_maestro->get_where('gastos g', ['id_gasto' => $data['id_gasto']]);
        $gasto = $query->result_array();

        $this->db_maestro->insert('track_gastos', $gasto[0]);
        return $this->db_maestro->insert_id();
    }    
    
    //Trae todas las formas de pago (Beneficiarios y gastos)
    public function get_forma_pago() {
        $this->db_parametria->select('p.*');
        $query = $this->db_parametria->get_where('forma_pago_beneficiario p', 'p.estado =1');

        return $query->result();
    }

    //Guardar un nuevo tipo de gasto
    public function guardarTipoGasto($data) {
        $this->db_parametria->insert('tipo_gastos', $data);
        return $this->db_parametria->insert_id();
    }

    //Trae todas las monedas (gastos y beneficiarios)
    public function get_moneda() {
        $this->db_parametria->select('m.*');
        $query = $this->db_parametria->get_where('moneda m', 'm.estado =1');

        return $query->result();
    }
    
    //Trae los beneficiarios, todos o si es para editar de uno fijo (Gastos y Beneficiarios)
    public function get_beneficiarios($data = "") {

        $this->db_maestro->select('be.*');
        if ($data) {

            $query = $this->db_maestro->get_where('beneficiarios be', 'be.id_beneficiario=' . $data);
        } else {
            $query = $this->db_maestro->get_where('beneficiarios be');
        }
        return $query->result_array();
    }
    
    //Verificar si la factura ya existe
    public function existeFactura($data) {
        if($data['id_beneficiario']){
            $this->db_maestro->select('*')->from('gastos');
            $this->db_maestro->where('nro_factura',$data['nro_factura']);
            $this->db_maestro->where('id_beneficiario',$data['id_beneficiario']);
            $query = $this->db_maestro->get();
            return $query->result_array();
        }
    }

//validacion de gasto no repetido
    public function validation_gasto($nro_factura,$concepto) {

        $this->db_maestro->select('*')->from('gastos');
        $this->db_maestro->where('nro_factura',$nro_factura);
        $this->db_maestro->where('concepto',$concepto);
        $query = $this->db_maestro->get();
        // echo $sql = $this->db->last_query();die;
        return $query->result_array();
    } 
    
    // Cuando elige una clase nueva rellena el select de descripcion
   
    public function completarDescripcion($data) {
            $this->db_parametria->select('g.*');
            $query = $this->db_parametria->get_where('descripcion_gastos g', 'g.id_clase_gasto='.$data['id_clase']);

            return $query->result_array();
    }
    
    //Guarda una nueva clase de gasto
    public function guardarClaseGasto($data) {
        $this->db_parametria->insert('clase_gastos', $data);
        return $this->db_parametria->insert_id();
    }
    
    //Guarda una nueva descripcion de clase de gasto
    public function guardarDescripcionGasto($data) {
        $this->db_parametria->insert('descripcion_gastos', $data);
        return $this->db_parametria->insert_id();
    }

    //Inserta un nuevo gasto
    public function registroGastos($data) {

        $this->db_maestro->insert('gastos', $data);
        return $this->db_maestro->insert_id();
    }
     
    
    //Ingresa gasto en tabla de trackeo
    public function registroEstados($data) {
        $this->db_maestro->insert('track_gastos', $data);
        return $this->db_maestro->insert_id();
    }    
    
    //Ingresa gasto en tabla de trackeo
    public function registroEstadosAnulado($data) {
        $this->db_maestro->select('t.*');
        $query = $this->db_maestro->get_where('track_gastos t', 't.id_gasto ='.$data);
        $datos = $query->result_array();
        $this->db_maestro->insert('track_gastos', $datos);
        return $this->db_maestro->insert_id(); 
    }    
     

    //Editar Gasto
    public function actualizarGasto($data) {
        $query = $this->db_maestro->update('gastos', $data, ['id_gasto' => $data['id_gasto']]);
        return $query;
    }
    public function trackGasto($data) {
        $query = $this->db_maestro->insert('track_gastos_new', $data);
        return $this->db_maestro->insert_id();
    }
    

    //Trae los gastos todos o uno especifico si es para editar
    public function get_gastos($data = "") {
        $this->db_maestro->select('g.*,b.denominacion, p.denominacion descripcion_estado');
        $this->db_maestro->join('beneficiarios b', 'g.id_beneficiario = b.id_beneficiario');
        $this->db_maestro->join($this->db_parametria->database.'.estado_gastos p', 'g.estado=p.id_estado');
        $this->db_maestro->order_by('g.fecha_vencimiento','ASC'); 
        if ($data) {
            $query = $this->db_maestro->get_where('gastos g', 'g.id_gasto=' . $data);
        } else {
            $query = $this->db_maestro->get_where('gastos g');
        }
    // echo $query; die();
    // echo $sql = $this->db->last_query();die;
        return $query->result_array();
    }
    public function get_gastos_search($search_gasto_param) {

        $this->db_maestro->select('g.*,b.denominacion, p.denominacion descripcion_estado');
        $this->db_maestro->from('gastos g');
        $this->db_maestro->join('beneficiarios b', 'g.id_beneficiario = b.id_beneficiario');
        $this->db_maestro->join($this->db_parametria->database.'.estado_gastos p', 'g.estado=p.id_estado');
        $this->db_maestro->order_by('g.fecha_vencimiento','ASC'); 
        $this->db_maestro->group_start();
        $this->db_maestro->where( 'g.id_gasto',$search_gasto_param);
        $this->db_maestro->or_where( 'g.nro_factura',$search_gasto_param);
        $this->db_maestro->or_where('p.denominacion',$search_gasto_param);
        $this->db_maestro->group_end();
        $query = $this->db_maestro->get();        
        // echo $query; die();
        // echo $sql = $this->db->last_query();die;

        return $query->result_array();
    }

    
    //Trae los gastos pendientes o uno especifico si es para editar
    public function get_gastos_pendientes($data = "") {

        $this->db_maestro->select('g.*,b.denominacion');
        $this->db_maestro->from('gastos g');
        $this->db_maestro->join('beneficiarios b', 'g.id_beneficiario = b.id_beneficiario');
        if ($data) {
            $this->db_maestro->where('g.id_gasto', $data);
        } else {
            $this->db_maestro->where('g.estado', '1');
        }
        $this->db_maestro->order_by('g.fecha_vencimiento','ASC'); 
        $query = $this->db_maestro->get();
        //echo $sql = $this->db->last_query();die;
        return $query->result_array();

    }

    public function get_gastos_pendientes_aprobados($data = "") {

        $this->db_maestro->select('i.convencion_tipoDocumento, g.*,b.denominacion,b.nro_documento, b.nro_cuenta1,b.nro_cuenta2, be.*,tc.*');
        // $this->db->select('*');

        $this->db_maestro->join('beneficiarios b', 'g.id_beneficiario = b.id_beneficiario');
        $this->db_maestro->join($this->db_parametria->database.'.bank_entidades be', 'b.id_banco = be.id_Banco','left');
        $this->db_maestro->join($this->db_parametria->database.'.bank_tipocuenta tc', 'b.id_tipo_cuenta = tc.id_TipoCuenta','left');
        $this->db_maestro->join($this->db_parametria->database.'.ident_tipodocumento i', 'b.id_tipo_documento = i.id_tipoDocumento');


        if ($data) {
            $query = $this->db_maestro->get_where('gastos g', 'g.id_gasto=' . $data);
        } else {
            $query = $this->db_maestro->get_where('gastos g', 'g.estado="3"');
        }
        // echo $sql = $this->db->last_query();die;
        return $query->result();
    }   

    
    
    
    //Trae los estados para un gasto especifico
    public function get_estados_gasto($data = "") {

        $this->db_maestro->select('g.*,b.denominacion,o.nombre_apellido');
        $this->db_maestro->join('beneficiarios b', 'g.id_beneficiario = b.id_beneficiario');
        $this->db_maestro->join($this->db_gestion->database.'.operadores o', 'g.usuario_ultima_moficacion = o.id_usuario');
        $query = $this->db_maestro->get_where('gastos g', 'g.id_gasto=' . $data);
        return $query->result_array();
    }

    //Trae todos los tipos de gastos
    public function get_tipo_gasto($id_estado="") {
        if ($id_estado){
            $this->db_parametria->select('denominacion');
            $query = $this->db_parametria->get_where('estado_gastos p', 'p.id_estado=' . $id_estado);
        }else{
            $this->db_parametria->select('tg.*');
            // echo $sql = $this->db->last_query();die;
            $query = $this->db_parametria->get_where('tipo_gastos tg', 'tg.estado =1');
        }
        return $query->result();
    }

    //Trae todas las clases de gasto
    public function get_clase_gasto() {
        $this->db_parametria->select('cg.*');
        $query = $this->db_parametria->get_where('clase_gastos cg', 'cg.estado =1');

        return $query->result();
    }

    //Trae todas las descripciones de gasto
    public function get_descripcion_gasto() {
        $this->db_parametria->select('dg.*');
        $query = $this->db_parametria->get_where('descripcion_gastos dg', 'dg.estado =1');

        return $query->result();
    }
    /*************************************************/
    /*** Se actualiza la validacion del desembolso ***/
    /*************************************************/
    function setActualizarValidacion( $id_desembolso, $data ) {
        $this->db_solicitudes->where( 'id', $id_desembolso);
        $this->db_solicitudes->update('validar_desembolso', $data);

        if ($this->db_solicitudes->affected_rows() > 0) {
            return $this->db_solicitudes->affected_rows();
        }
        else{
            return -1;
        }
    }
    function consulta_pagado_solicitud_txt($id_solicitud){
        $this->db_solicitudes->select('st.pagado');
        $query = $this->db_solicitudes->get_where('solicitud_txt st', 'st.id_solicitud ='.$id_solicitud);
        return $query->result_array();
    }
    function actualizarsolicitud_txt($id_solicitud, $dato){
        $this->db_solicitudes->where( 'id_solicitud', $id_solicitud);
        $this->db_solicitudes->update('solicitud_txt', $dato);
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $this->db_solicitudes->affected_rows();
        }
        else{
            return -1;
        }

    }
    /************************************************************/
    /*** Se busca el Desembolso a validar por N° de solicitud ***/
    /************************************************************/
    public function getDesembolso($id_solicitud) {
        $sql = "select 
            vd.id, 
            vd.id_solicitud, 
            DATE_FORMAT(vd.fecha_hora_solicitud, '%d/%m/%Y %H:%i') AS fecha_hora_solicitud,
            vd.respuesta,
            vd.comprobante,
            DATE_FORMAT(s.fecha_alta, '%d/%m/%Y') AS fecha_alta,
            s.documento,
            s.estado,
            s.tipo_solicitud,
            CONCAT(s.nombres, ' ', s.apellidos) AS nombre_apellido,
            stxt.origen_pago,
            DATE_FORMAT(stxt.fecha_procesado, '%d/%m/%Y %H:%i') AS fecha_procesado,
            stxt.pagado,
            stxt.ruta_txt,
            op.nombre_apellido AS nombre_apellido_operador
        FROM ".$this->db_solicitudes->database.".validar_desembolso vd
            INNER JOIN ".$this->db_solicitudes->database.".solicitud s ON s.id = vd.id_solicitud
            INNER JOIN ".$this->db_solicitudes->database.".solicitud_txt stxt ON vd.id_solicitud = stxt.id_solicitud
            INNER JOIN ".$this->db_gestion->database.".operadores op ON vd.id_operador = op.idoperador
        WHERE vd.id_solicitud = " . $id_solicitud .
        " ORDER BY vd.fecha_hora_solicitud;";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }
}
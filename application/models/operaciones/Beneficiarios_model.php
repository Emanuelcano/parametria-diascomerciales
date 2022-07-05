<?php

class Beneficiarios_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // LOAD SCHEMA
        $this->db_parametria = $this->load->database('parametria', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);

    }

    //Trae las empresas (Beneficiarios y Gastos)
    public function get_empresa() {
        $this->db_parametria->select('e.*');
        $query = $this->db_parametria->get_where('empresas e', 'e.estado =1');

        return $query->result();
    }    
    
    //Trae todos los tipos de beneficiario
    public function get_tipo_beneficiario() {
        $this->db_parametria->select('t.*');
        $query = $this->db_parametria->get_where('tipo_beneficiario t', 't.estado =1');

        return $query->result();
    }
    
    //Ingresa al beneficiario
    public function registroBeneficiario($data) {
        $this->db_maestro->insert('beneficiarios', $data);
        return $this->db_maestro->insert_id();
    }
    
    //Editar Beneficiario
    public function actualizarBeneficiario($data) {
        $query = $this->db_maestro->update('beneficiarios', $data, ['id_beneficiario' => $data['id_beneficiario']]);
        return $query;
    }

     public function get_nit_beneficiario($document,$tipo_documento) {
        $this->db_maestro->from('beneficiarios');
        $this->db_maestro->where("beneficiarios.nro_documento = '$document'");
        $this->db_maestro->where('beneficiarios.id_tipo_documento ',$tipo_documento);

        $query = $this->db_maestro->get();  
        return $query->num_rows();
    }

    //Trae todos los rubros de beneficiario
    public function get_lista_rubro() {
        $this->db_parametria->select('r.*');
        $query = $this->db_parametria->get_where('rubro_beneficiario r', 'r.estado =1');

        return $query->result();
    }

    //Trae todas las formas de pago (Beneficiarios y gastos)
    public function get_forma_pago() {
        $this->db_parametria->select('p.*');
        $query = $this->db_parametria->get_where('forma_pago_beneficiario p', 'p.estado =1');

        return $query->result();
    }
    
    //Trae todas las monedas (gastos y beneficiarios)
    public function get_moneda() {
        $this->db_parametria->select('m.*');
        $query = $this->db_parametria->get_where('moneda m', 'm.estado =1');

        return $query->result();
    }

    
    //Trae los tipos de documento
    public function get_tipo_documento() {
        $this->db_parametria->select('i.*');
        $query = $this->db_parametria->get_where('ident_tipodocumento i');

        return $query->result();
    }

    //Trae las provincias
    public function get_provincia() {
        $this->db_parametria->select('g.*');
        $query = $this->db_parametria->get_where('geo_departamento g', 'g.id_estado_departamento =1');

        return $query->result();
    }

    //Trae los municipios
    public function get_municipio($param) {
        $this->db_parametria->select('g.*');
        $this->db_parametria->from('geo_municipio g');
        $query = $this->db_parametria->where('g.id_estado_municipio =1');

        if(isset($param["cod_departamento"])){  $this->db_parametria->where('Codigo_departamento', $param["cod_departamento"]);}
        if(isset($param["nombre_municipio"])){  $this->db_parametria->where('nombre_municipio', $param["nombre_municipio"]);}
        
        $query = $this->db_parametria->get();
        return $query->result();
    }

    //Trae todos los bancos
    public function get_banco() {
        $this->db_parametria->select('b.*');
        $query = $this->db_parametria->get_where('bank_entidades b', 'b.id_estado_Banco =1');

        return $query->result();
    }

    //Trae los tipos de cuenta
    public function get_tipo_cuenta() {
        $this->db_parametria->select('c.*');
        $query = $this->db_parametria->get_where('bank_tipocuenta c', 'c.id_estado_TipoCuenta =1');

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

    //Cambia el estado del beneficiario (activo-inactivo)
    public function cambioEstado($data) {
        $query = $this->db_maestro->update('beneficiarios', $data, ['id_beneficiario' => $data['id_beneficiario']]);
        return $query;
    }
    
    //Guarda un nuevo tipo de beneficiario
    public function guardarTipoBeneficiario($data) {
        $this->db_parametria->insert('tipo_beneficiario', $data);
        return $this->db_parametria->insert_id();
    }

    //Guarda un nuevo Rubro de Beneficiario
    public function guardarRubroBeneficiario($data) {
        $this->db_parametria->insert('rubro_beneficiario', $data);
        return $this->db_parametria->insert_id();
    }

    //Guarda una nueva forma de pago
    public function guardarFormaPago($data) {
        $this->db_parametria->insert('forma_pago_beneficiario', $data);
        return $this->db_parametria->insert_id();
    }
    
     //Trae Tipo de moneda y Tipo de DNI del beneficiario que se seleccione (Gastos y Beneficiario)
    public function detalleBeneficiario($data = "") {
        $this->db_maestro->select($this->db_parametria->database.'.ident_tipodocumento.convencion_tipoDocumento,
                                    beneficiarios.nro_documento,
                                    '.$this->db_parametria->database.'.moneda.denominacion,
                                    '.$this->db_parametria->database.'.moneda.id_moneda,
                                    ');   
        $this->db_maestro->from('beneficiarios');
        $this->db_maestro->join($this->db_parametria->database.'.ident_tipodocumento', $this->db_parametria->database.'.ident_tipodocumento.id_tipoDocumento = beneficiarios.id_tipo_documento','left');
        $this->db_maestro->join($this->db_parametria->database.'.moneda', $this->db_parametria->database.'.moneda.id_moneda = beneficiarios.id_tipo_moneda','left');
        $this->db_maestro->where('beneficiarios.id_beneficiario', $data['id_beneficiario']);
        
        $query = $this->db_maestro->get();        
        return $query->result_array();
    }      
    
    //Guarda una nueva moneda
    public function guardarMoneda($data) {
        $this->db_parametria->insert('moneda', $data);
        return $this->db_parametria->insert_id();
    }

    //Guarda un nuevo tipo de documento
    public function guardarTipoDocumento($data) {
        $this->db_parametria->insert('parametria.ident_tipodocumento', $data);
        return $this->db_parametria->insert_id();
    }    
    
}
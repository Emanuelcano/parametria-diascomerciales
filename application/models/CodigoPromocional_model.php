<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CodigoPromocional_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->db_gestion = $this->load->database('gestion', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_parametria  = $this->load->database('parametria', TRUE);
        $this->db_usuarios_solventa = $this->load->database('usuarios_solventa',TRUE);
        $this->db_chat = $this->load->database('chat',TRUE);
        $this->db_chatbot = $this->load->database('chatbot',TRUE);
    }

    public function get_operadores_promocion($id_operador)
    {
        $operador = $this->db_gestion->select('*')
        ->from("operadores_promocion")
        ->where("estado", "1")
        ->where_in("id_operador", $id_operador)->get()->result_array();

        return $operador;
    }

    public function get_solicitud($id_cliente)
    {
        $id_solicitud = $this->db_solicitudes->select("s.id")
        ->from("solicitud AS s")
        ->where("s.id_cliente", $id_cliente)->get()->result_array();

        return $id_solicitud;
    }

    public function get_data_codigo($id_cliente)
    {
        $codigo = $this->db_maestro->select('pc.id_cliente, pc.codigo, pc.monto_extra')
        ->from("promocion_clientes AS pc")
        ->join("creditos AS c", "c.id_cliente = pc.id_cliente")
        ->join("credito_detalle AS cd", "cd.id_credito = c.id")
        ->where("pc.id_cliente", $id_cliente)
        ->where("pc.monto_extra > 0")
        // ->where("(cd.estado = 'mora' OR cd.estado IS NULL)")
        ->where("(cd.dias_atraso < 35 OR cd.fecha_vencimiento <= DATE_FORMAT(NOW(),'%Y-%m-%d'))")
        ->where("pc.estado", "1")->get()->result_array();
        // var_dump($this->db_maestro->last_query());die;
        return $codigo;
    }

    public function get_operador($id_operador)
    {
        $operador = $this->db_gestion->select("op.nombre_apellido")
        ->from("operadores AS op")
        ->where("op.idoperador", $id_operador)->get()->result_array();
        return $operador;
    }

    public function track_condigo_promocion($id_cliente, $codigo, $id_operador, $tipo)
    {
        if ($tipo == "WSP") {
            $canal = "WHATSAPP";
        }else{
            $canal = "SMS";
        }
        
        $data = [
            "id_cliente" => $id_cliente,
            "codigo" => $codigo,
            "id_operador" => $id_operador,
            "canal" => $canal,
            "fecha_hora" => date("Y-m-d H:i:s")
        ];
        $this->db_gestion->insert("promocion_enviados", $data);
    }

}

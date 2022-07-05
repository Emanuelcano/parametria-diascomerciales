<?php

class DebitoAutomatico_model extends CI_Model {

    const MEDIO_PAGO_DEBITO_AUTOMATICO  = 'DEBITO AUTOMATICO';
    const ESTADO_TRANSACCION_ACEPTADA   = '';

    public function __construct()
    {

        $this->db = $this->load->database('respuestas_bancos', true);

        $this->load->model('DebitoAutomatico_model');
        $this->load->library('custom_log');

        $this->load->helper('formato_helper');
        $this->load->helper('string');
    }

    /**
     * Registra respuesta completa de Debitos Automaticos REC
     */
    public function registrarRespuesta($id_credito_detalle = 0, $monto = 0, $fecha_pago = '', $referencia = '', $id_archivo_adjunto = 0, $medio_pago = DebitoAutomaticoNotOk_model::MEDIO_PAGO_DEBITO_AUTOMATICO, $estado)
    {

        $data = array(
            'id_detalle_credito'    => $id_credito_detalle,
            'fecha'                 => date('Y-m-d H:i:s'),
            'monto'                 => format_price($monto),
            'medio_pago'            => $medio_pago,
            'fecha_pago'            => $fecha_pago,
            'estado'                => $estado,
            'referencia'            => $referencia
        );

        $this->db->insert('debitos_bancolombia_respuesta', $data);
        $this->db->insert_id();
                
    }

}
?>
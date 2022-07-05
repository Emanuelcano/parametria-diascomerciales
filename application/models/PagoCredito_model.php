<?php
class PagoCredito_model extends CI_Model {
    const TIPO_PAGO = 'pago';
    const TIPO_REVERSA = 'reversa';
    
    const ESTADO_PAGO_PENDIENTE = 'pendiente_de_pago';
    const ESTADO_IMPUTACION_PENDIENTE = 'pendiente_de_imputar';
    const ESTADO_IMPUTADO = 'imputado';

    const SINCRONIZADO_SI = 1;
    const SINCRONIZADO_NO = 0;

    /**
     * Estados de transaccion de ePayco
     */
    const ESTADO_TRANSACCION_ACEPTADA   = 1;
    const ESTADO_TRANSACCION_RECHAZADA  = 2;
    const ESTADO_TRANSACCION_PENDIENTE  = 3;
    const ESTADO_TRANSACCION_FALLIDA    = 4;
    const ESTADO_TRANSACCION_REVERSADA  = 6;
    const ESTADO_TRANSACCION_RETENIDA   = 7;
    const ESTADO_TRANSACCION_INCIADA    = 8;
    const ESTADO_TRANSACCION_EXPIRADA   = 9;
    const ESTADO_TRANSACCION_ABANDONADA = 10;
    const ESTADO_TRANSACCION_CANCELADA  = 11;
    const ESTADO_TRANSACCION_ANTIFRAUDE = 12;
    /**
     * los medios de pago
     */
    const MEDIO_PAGO_EPAYCO = 'epayco';

    public function __construct(){        
        $this->load->database();
        $this->load->model('CreditoDetalle_model');
        $this->load->library('custom_log');
        $this->load->library('Sqlexceptions');
        $this->load->helper('formato_helper');
        $this->Sqlexceptions = new Sqlexceptions();
    }
    public function getTotalPagos($id_detalle_credito = 0)
    {
        $this->db->select('SUM(pc.monto) AS total');
        $this->db->from('pago_credito pc');
        $this->db->join('credito_detalle cd', 'pc.id_detalle_credito = cd.id');
        //$this->db->join('clientes c' , 'cr.id_cliente = c.id');
        $this->db->where('pc.id_detalle_credito',$id_detalle_credito);
        $this->db->where('pc.estado',self::ESTADO_TRANSACCION_ACEPTADA);
        $this->db->limit(1);

        $query = $this->db->get();
        $this->Sqlexceptions->checkForError();
        if ($query->num_rows() > 0) {
            return $query->first_row()->total;
        }
        return 0;
    }
    /**
     * Realiza el pago/reverso de una transacción de epayco
     * @param stdClass $Transaccion de Epayco
     * @return void
     */
    public function pagarCuotaCredito(stdClass $Transaccion){
        /**
         * variables
         */
        $x_id_invoice = $Transaccion->id_factura;
        $x_ref_payco = $Transaccion->Id;
        $x_amount = $Transaccion->valortotal;
        $x_transaction_date = $Transaccion->fecha;
        $x_cod_response = $Transaccion->tipo_cod_respuesta;
        $x_motivo = $Transaccion->respuesta;
        $CreditoDetalle = false;
        $Credito = false;
        $new_monto_cobrado = 0;
        $tipo = PagoCredito_model::TIPO_PAGO;
        $procesa_transaccion = false;
        
        switch ((int) $x_cod_response) {
            case PagoCredito_model::ESTADO_TRANSACCION_ACEPTADA:
                $procesa_transaccion = true;
                $this->custom_log->write_log("INFO", "transacción aceptada");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_RECHAZADA:
                $this->custom_log->write_log("INFO", "transacción rechazada");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_PENDIENTE:
                $procesa_transaccion = true;
                $this->custom_log->write_log("INFO", "transacción pendiente");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_FALLIDA:
                $this->custom_log->write_log("INFO", "transacción fallida");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_REVERSADA:
                $procesa_transaccion = true;
                $tipo = PagoCredito_model::TIPO_REVERSA;
                $this->custom_log->write_log("INFO", "transacción reversada");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_RETENIDA:
                $procesa_transaccion = true;
                $this->custom_log->write_log("INFO", "transacción retenida");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_INCIADA:
                $procesa_transaccion = true;
                $this->custom_log->write_log("INFO", "transacción iniciada");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_EXPIRADA:
                $procesa_transaccion = true;
                $this->custom_log->write_log("INFO", "transacción expirada");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_ABANDONADA:
                $this->custom_log->write_log("INFO", "transacción abandonada");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_CANCELADA:
                $this->custom_log->write_log("INFO", "transacción cancelada");
                break;
            case PagoCredito_model::ESTADO_TRANSACCION_ANTIFRAUDE:
                $this->custom_log->write_log("INFO", "transacción control antifraude");
                break;
        }
        $this->custom_log->write_log("INFO", "id_invoice: {$x_id_invoice} | ref_payco: {$x_ref_payco}");
        /**
         * busca la el la cuota que se está pagando y guarda el 
         */
        try{
            $CreditoDetalle = $this->CreditoDetalle_model->getById($x_id_invoice);
        }catch(UserException $error){
            $userMesage = $error->getUserMessage();
            $this->custom_log->write_log("ERROR", $userMesage['error']['message']);
            $this->custom_log->write_log("ERROR", $this->db->last_query());
        }
        if($CreditoDetalle){
            if($procesa_transaccion===false){
                $this->custom_log->write_log("TEST", "Solo actualiza el estado de la transaccion.");
                //solo actualiza el estado de una transaccion
                return;
            }

            $id_detalle_credito = $CreditoDetalle[0]['id'];
            $id_credito = $CreditoDetalle[0]['id_credito'];
            $monto_cobrar = $CreditoDetalle[0]['monto_cobrar'];
            $estado = $CreditoDetalle[0]['estado'];
            $numero_cuota = $CreditoDetalle[0]['numero_cuota'];
            $response = (json_encode((array) $Transaccion));
            $tipo = PagoCredito_model::TIPO_PAGO;
            
            try{
                $data = array(
                    'id_detalle_credito' => $id_detalle_credito,
                    'referencia_externa' => $x_ref_payco,
                    'fecha' => date('Y-m-d H:i:s'),
                    'monto' => format_price($x_amount),
                    'medio_pago' => PagoCredito_model::MEDIO_PAGO_EPAYCO,
                    'fecha_pago' => $x_transaction_date,
                    'estado' => $x_cod_response,
                    'estado_razon' => $x_motivo,
                    'response' => $response
                );
                $this->db->insert('pago_credito', $data);
                $this->Sqlexceptions->checkForError();
            }catch(UserException $error){
                $userMesage = $error->getUserMessage();
                $this->custom_log->write_log("ERROR", $userMesage['error']['message']);
                $this->custom_log->write_log("ERROR", $this->db->last_query());
            }
            /**
             * actualiza el detalle de la cuota
             */
            try{
                $new_monto_cobrado = $this->PagoCredito_model->getTotalPagos($id_detalle_credito);
            }catch(UserException $error){
                $userMesage = $error->getUserMessage();
                $this->custom_log->write_log("ERROR", $userMesage['error']['message']);
                $this->custom_log->write_log("ERROR", $this->db->last_query());
            }
            
            $new_estado = $estado;
            if($new_monto_cobrado >= $monto_cobrar){
                $new_estado = 'pagado';
            }else if($new_monto_cobrado > 0 && $new_monto_cobrado < $monto_cobrar){
                $new_estado = 'pago_parcial';
            }

            try{
                $data = array(
                    'fecha_cobro' => $x_transaction_date,
                    'monto_cobrado' => format_price($new_monto_cobrado),
                    'estado' => $new_estado
                );                
                $this->db->where('id', $id_detalle_credito);
                $this->db->update('credito_detalle', $data);
                $this->Sqlexceptions->checkForError();
            }catch(UserException $error){
                $userMesage = $error->getUserMessage();
                $this->custom_log->write_log("ERROR", $userMesage['error']['message']);
                $this->custom_log->write_log("ERROR", $this->db->last_query());
            }
            /**
             * actualiza el detalle del credito
             */
            try{
                $Credito = $this->Credito_model->getById($id_credito);
            }catch(UserException $error){
                $userMesage = $error->getUserMessage();
                $this->custom_log->write_log("ERROR", $userMesage['error']['message']);
                $this->custom_log->write_log("ERROR", $this->db->last_query());
            }

            if($Credito){
                $monto_devolver = $Credito->monto_devolver;
                $new_estado_credito = $Credito->estado;
                
                if($new_monto_cobrado >= $monto_devolver){
                    $new_estado_credito = Credito_model::ESTADO_CANCELADO;
                }
                
                try{
                    $data = array(
                        'estado' => $new_estado_credito
                    );                
                    $this->db->where('id', $Credito->id);
                    $this->db->update('creditos', $data);
                    $this->Sqlexceptions->checkForError();
                }catch(UserException $error){
                    $userMesage = $error->getUserMessage();
                    $this->custom_log->write_log("ERROR", $userMesage['error']['message']);
                    $this->custom_log->write_log("ERROR", $this->db->last_query());
                }				
                /**
                 * Genero el movimiento en la cuenta corriente
                 */
                $descripcion = $tipo == PagoCredito_model::TIPO_PAGO ? "Pago de cuota {$numero_cuota}" : "Reverso de cuota {$numero_cuota}";
                $x_amount    = $tipo == PagoCredito_model::TIPO_PAGO ? $x_amount : $x_amount * -1;
                
                try{
                    $data = array(
                        'id_cliente' => $Credito->id_cliente,
                        'id_credito' => $Credito->id,
                        'fecha' => $x_transaction_date,
                        'debito' => format_price(0),
                        'credito' => format_price($x_amount),
                        'descripcion' => $descripcion
                    );
                    $this->db->insert('cuenta_corriente', $data);
                    $this->Sqlexceptions->checkForError();
                }catch(UserException $error){
                    $userMesage = $error->getUserMessage();
                    $this->custom_log->write_log("ERROR", $userMesage['error']['message']);
                    $this->custom_log->write_log("ERROR", $this->db->last_query());
                }
            }
        }else{
            $log = "No se encontraron registros con el número de detalle de crédito: {$x_id_invoice}";
            $this->custom_log->write_log("ERROR", $log);
        }
    }

    public function insert_pago($data){
       
        $insert = $this->db->insert('maestro.pago_credito', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return 0;
        }
    }

    public function get_descuento(){
        $this->db->select('id');
        $this->db->from('maestro.pago_credito');
        $this->db->where('maestro.pago_credito.tipo_pago', 'descuento');
        $query = $this->db->get();
        return $query->first_row();
    }

    public function update_pago_credito($params, $data){
        $this->db->where('id',$params['id_pago']);
        $update = $this->db->update('maestro.pago_credito', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function update_desglose_pago($params, $data){
        $this->db->where('id',$params['id_desglose']);
        $update = $this->db->update('maestro.desglose_pago', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function insert_desglose_pago($data){
        //$this->db->where('id',$params['id_pago']);
        $update = $this->db->insert('maestro.desglose_pago', $data);
        $update = $this->db->affected_rows();
        return $update;
    }


}
?>

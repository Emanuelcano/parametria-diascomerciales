<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Devolucion_model extends CI_Model {

    
    public function __construct(){  
        parent::__construct();      
        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
    }

    public function get_solicitud_devolucion_complete($param){
        $this->db = $this->load->database('maestro',TRUE);

        $this->db->select('solicitud_devolucion.*, devolucion_pagos.id id_pago_devolucion, devolucion_pagos.id_pago, devolucion_pagos.id_credito, devolucion_pagos.monto, devolucion_comprobantes.id id_comprobante, devolucion_comprobantes.comprobante');
        $this->db->from('solicitud_devolucion, devolucion_pagos, devolucion_comprobantes');
        $this->db->where('devolucion_pagos.id_solicitud_devolucion = solicitud_devolucion.id  and devolucion_comprobantes.id_solicitud_devolucion = solicitud_devolucion.id');

        if (isset($param['id_cliente'])) { $this->db->where('id_cliente',$param['id_cliente']);}
        if (isset($param['id_devolucion'])) { $this->db->where('id_cliente',$param['id_cliente']);}

        $query = $this->db->get();
        return $query->result();
    }

    public function get_comprobantes_devolucion($param){
        $this->db = $this->load->database('maestro',TRUE);

        $this->db->select('*');
        $this->db->from('devolucion_comprobantes');

        if (isset($param['id_devolucion'])) { $this->db->where('id_solicitud_devolucion',$param['id_devolucion']);}
        if (isset($param['origen'])) { $this->db->where('origen',$param['origen']);}

        $query = $this->db->get();
        return $query->result();
    }

    public function get_pagos_devolucion($param){
        $this->db = $this->load->database('maestro',TRUE);

        $this->db->select('*');
        $this->db->from('devolucion_pagos');

        if (isset($param['id_devolucion'])) { $this->db->where('id_solicitud_devolucion',$param['id_devolucion']);}

        $query = $this->db->get();
        return $query->result();
    }


    public function insertarSolicitud($data) {       
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->insert('solicitud_devolucion', $data);
        if($this->db->affected_rows() != 1){
            return -1;
        } else{
            return $this->db->insert_id();
        }
    }


    public function insertarSolicitudTxt($data) {       
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->insert('solicitud_devolucion_txt', $data);
        if($this->db->affected_rows() != 1){
            return -1;
        } else{
            return $this->db->insert_id();
        }
    }

    public function insertarRespuestaTxt($data) {       
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->insert('respuesta_devolucion_txt', $data);
        if($this->db->affected_rows() != 1){
            return -1;
        } else{
            return $this->db->insert_id();
        }
    }


    public function insertarPagoDevolver($data) {       
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->insert('devolucion_pagos', $data);
        if($this->db->affected_rows() != 1){
            return -1;
        } else{
            return $this->db->insert_id();
        }
    }

    public function insertarComprobanteDevolver($data) {       
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->insert('devolucion_comprobantes', $data);
        if($this->db->affected_rows() != 1){
            return -1;
        } else{
            return $this->db->insert_id();
        }
    }


    public function getSolicitudes($param, $limit = null, $offset = null){
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->select('d.*, o.nombre_apellido solicitado, op.nombre_apellido gestionado, c.documento, c.nombres, c.apellidos');
        $this->db->from('solicitud_devolucion d');
        $this->db->join('clientes as c', 'd.id_cliente = c.id');
        $this->db->join('gestion.operadores as o', 'd.id_operador = o.idoperador');
        $this->db->join('gestion.operadores as op', 'd.id_operador_devolucion = op.idoperador', 'left');

        if (isset($param['documento'])) { $this->db->where('c.documento ="'.$param['documento'].'"');  }
        if (isset($param['estado'])) { $this->db->where_in('d.estado',explode(',',$param['estado']));}
        if (isset($param['not_estado'])) { $this->db->where_not_in('d.estado',explode(',',$param['not_estado']));}
        if (isset($param['id_devolucion'])) { $this->db->where('d.id',$param['id_devolucion']);  }
        if (isset($param['id_cliente'])) { $this->db->where('d.id_cliente',$param['id_cliente']);  }
        if (isset($param['monto_devolver'])) { $this->db->where('d.monto_devolver',$param['monto_devolver']);  }
        if (isset($param['limit'])){ $this->db->limit($param['limit']);}
        if (isset($param['order'])){ $this->db->order_by($param['order'], $param['sentido']);}

        if(isset($limit) && isset($offset))
        {
            $this->db->limit($offset, $limit);
        }else if(isset($limit) && !isset($offset))
        {
            $this->db->limit($limit);
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function cantidad_solicitudes_devoluciones($params=[]){
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->select('count(id) cantidad');
        $this->db->from('solicitud_devolucion d');
        
        if (isset($params['estado']))        { $this->db->where('estado', $params['estado']);}

        $query = $this->db->get();
        return $query->result();
    }

    public function updateDevolucion($dataSolicitud, $id = null, $where= []){
        $this->db = $this->load->database('maestro', TRUE);

        if(!is_null($id))  {   $this->db->where('id', $id);   }
        if(isset($where['where']))  {   $this->db->where($where['where']);   }
        $this->db->update('solicitud_devolucion', $dataSolicitud);
        $query = $this->db->affected_rows();
        //echo $sql = $this->db->last_query();die;
        return $query;

    }

    public function getInfoDevolucion($id_cliente){
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->select('clientes.documento, clientes.nombres, clientes.apellidos, bank_entidades.Nombre_Banco, bank_entidades.codigo codigo_banco, bank_tipocuenta.Nombre_TipoCuenta, bank_tipocuenta.id_TipoCuenta, agenda_bancaria.numero_cuenta, tipo.nombre_tipoDocumento, tipo.codigo, s.id as idSolicitud');
        $this->db->from('clientes, parametria.bank_entidades bank_entidades, parametria.bank_tipocuenta bank_tipocuenta, agenda_bancaria, solicitudes.solicitud s, solicitudes.solicitud_datos_bancarios b, parametria.ident_tipodocumento tipo');
        $this->db->where('bank_entidades.id_banco = agenda_bancaria.id_banco');
        $this->db->where('bank_tipocuenta.id_TipoCuenta = agenda_bancaria.id_tipo_cuenta');
        $this->db->where('clientes.id = agenda_bancaria.id_cliente');
        $this->db->where('agenda_bancaria.id_banco = b.id_banco');
        $this->db->where('b.id_solicitud = s.id');
        $this->db->where('s.id_cliente = clientes.id');
        $this->db->where('clientes.id_tipo_documento = tipo.id_tipoDocumento');
        $this->db->where("clientes.id", $id_cliente);
        $this->db->order_by("s.id", "DESC");
        $this->db->limit(1);

        $query = $this->db->get();
        $res = $query->result();
        return $res;

    }

    public function getSolicitudDCreitoPagoDevuelto($param){
        $this->db = $this->load->database('maestro', TRUE);

        $this->db->select('s.id id_solicitud, d.id, dp.id_credito, d.monto_devolver');
        $this->db->from('solicitud_devolucion d');
        $this->db->join('devolucion_pagos as dp', 'd.id = dp.id_solicitud_devolucion');
        $this->db->join('solicitudes.solicitud s', 'dp.id_credito= s.id_credito');
        $this->db->group_by('dp.id_credito');

        if (isset($param['id_devolucion'])) { $this->db->where('d.id',$param['id_devolucion']);  }

        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_archivo_respuesta( $param = null)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('*');
        $this->db->from('respuesta_devolucion_txt');
        if (isset($param['fileName'])) { $this->db->where('nombre_archivo',$param['fileName']);  }

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getSolicitudesDebitoAutomatico($param){
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('debito_devolver.id id_debito, debito_devolver.id_cliente, debito_devolver.id_cuota, debito_devolver.fecha_debito, debito_devolver.monto_debito, 
        debito_devolver.banco_debito, clientes.documento, clientes.nombres, clientes.apellidos, pago_credito.id id_pago, credito_detalle.id_credito');
        $this->db->from('debito_devolver');
        $this->db->join('clientes','debito_devolver.id_cliente = clientes.id');
        $this->db->join('pago_credito','pago_credito.id_detalle_credito = debito_devolver.id_cuota');
        $this->db->join('credito_detalle','credito_detalle.id = pago_credito.id_detalle_credito');
        $this->db->where('debito_devolver.estado',$param['estado']);
        $this->db->where('debito_devolver.fecha_debito = pago_credito.fecha_pago');
        $this->db->where('debito_devolver.monto_debito = pago_credito.monto');
        $this->db->where('pago_credito.medio_pago = "debito automatico"');
        $this->db->where('debito_devolver.banco_debito = "'.$param['banco'].'"');
        //$this->db->limit(100);
        $query = $this->db->get();
        return $query->result();

    }

    public function updateDebitoDevolver($id, $params){
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->where('id', $id);
        $this->db->update('debito_devolver', $params);
        $query = $this->db->affected_rows();
        //echo $sql = $this->db->last_query();die;
        return $query;

    }

    public function updateEstadoSolicitudDevolucion($idSolicitud, $params){      
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->where('id', $idSolicitud);
        $this->db->update('solicitud_devolucion', $params);
        $query = $this->db->affected_rows();
        //echo $sql = $this->db->last_query();die;
        return $query;
    }
}
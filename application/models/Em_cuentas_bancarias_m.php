<?php
class Em_cuentas_bancarias_m extends BaseModel {

    protected $_table_name = 'maestro.cuentas_bancarias';
    protected $_primary_key = 'id';
    protected $_order_by = 'id';

    public function __construct() {
        parent::__construct();
    }

   
    public function get_cuentas_bancarias(){
        $dia=date("Y-m-d");
        $this->db->select('*,(e.maximo_unidades_mes) - (acu.unidad_acumulada_mes) as quedan,(e.maximo_pesos_mes)-(acu.pesos_acumulado_mes) as quedanpesos,(acu.unidad_acumulada_dia) as quedandia,(e.saldo_apertura)-(acu.pesos_acumulado_dia) as quedanpesosdia,acu.pesos_acumulado_dia as pesosacumuladodia');  
        $this->db->join('parametria.bank_tipocuenta es','e.id_tipo_cuenta = es.id_TipoCuenta', 'left');
        $this->db->join('maestro.acumulado_cuenta_bancaria acu','e.id = acu.id_cuentabancaria', 'left');
        $this->db->join('parametria.bank_entidades b','e.id_banco = b.id_Banco', 'left');
        $this->db->join('parametria.estados est','e.estado = est.id_estado', 'left');
        // $this->db->where('acu.dia', $dia);
         $query = $this->db->get_where('maestro.cuentas_bancarias e');
        return $query->result();
        
    }
    public function cambiarestadocuenta($data){

        $id_cuentabancaria= $data['id_cuentabancaria'];
        $id_estado= $data['id_estado'];
        if ($id_estado==1) {
            $id_estado=0;
        }else{
            $id_estado=1;
        }
        $this->db->set('estado',$id_estado);
        $this->db->where('id', $id_cuentabancaria);
        $query = $this->db->update('maestro.cuentas_bancarias');
       // echo $sql = $this->db->last_query();die;
        return false;
    }
    
}

// SELECT *,(e.maximo_unidades_mes) - (acu.unidad_acumulada_mes) as quedan,(e.maximo_pesos_mes)-(acu.pesos_acumulado_mes) as quedanpesos
// FROM em_cuentas_bancarias e
// INNER JOIN bank_tipocuenta es ON e.id_tipo_cuenta = es.id_TipoCuenta
// LEFT JOIN acumulado_cuenta_bancaria acu ON e.id = acu.id_cuentabancaria
// INNER JOIN banco b ON e.id_banco = b.id_Banco
// INNER JOIN estados est ON e.estado = est.id_estado
// where e.estado =1
// UNION ALL
// SELECT *,(acu.unidad_acumulada_dia) as quedandia,(e.saldo_apertura)-(acu.pesos_acumulado_dia) as quedanpesosdia,acu.pesos_acumulado_dia as pesosacumuladodia
// FROM em_cuentas_bancarias e
// INNER JOIN bank_tipocuenta es ON e.id_tipo_cuenta = es.id_TipoCuenta
// LEFT JOIN acumulado_cuenta_bancaria_dia acu ON e.id = acu.id_cuentabancaria
// INNER JOIN banco b ON e.id_banco = b.id_Banco
// INNER JOIN estados est ON e.estado = est.id_estado
// where e.estado =1 and acu.dia='2019-10-09'
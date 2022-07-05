<?php
class CuentasBancarias_model extends BaseModel {


    public function __construct() {
        parent::__construct();
    }

   
    public function getCuentasBancarias(){
        $dia=date("Y-m-d");
        $this->db->select('*,(e.maximo_unidades_mes) - (acu.unidad_acumulada_mes) as quedan,(e.maximo_pesos_mes)-(acu.pesos_acumulado_mes) as quedanpesos,(acu.unidad_acumulada_dia) as quedandia,(e.saldo_apertura)-(acu.pesos_acumulado_dia) as quedanpesosdia,acu.pesos_acumulado_dia as pesosacumuladodia');  
        $this->db->join('parametria.bank_tipocuenta es','e.id_tipo_cuenta = es.id_TipoCuenta', 'left');
        $this->db->join('maestro.acumulado_cuenta_bancaria acu','e.id = acu.id_cuentabancaria', 'left');
        $this->db->join('parametria.bank_entidades b','e.id_banco = b.id_Banco', 'left');
        $this->db->join('parametria.estados est','e.estado = est.id_estado', 'left');
        //$this->db->where('mes',"MONTH(CURDATE())");
        $query = $this->db->get_where('maestro.cuentas_bancarias e');
        return $query->result();
        
    }
    /*
    * 
    */
    public function findAllCuentasBancarias(){

    	$this->db->select('cb.id, be.Nombre_Banco, cb.numero_cuenta');
    	$this->db->from('maestro.cuentas_bancarias cb');
        $this->db->join('parametria.bank_entidades be', 'cb.id_banco = be.id_banco');
    	$query = $this->db->get();
    	return $query->result();

    }
}

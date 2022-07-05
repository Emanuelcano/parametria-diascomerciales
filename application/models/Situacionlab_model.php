<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    class Situacionlab_model extends CI_Model {

        public function __construct() {
            parent::__construct();
            $this->db_apiburos = $this->load->database('api_buros', TRUE);
        }
        
        public function get_informacion_laboralE($param) {
            $this->db_apiburos->select('vic.id, vic.EPS, vic.AFP, vic.numero_identificacion_persona_natural, via.razon_social_aportante, (vii.tiempo_total_laborado * 100 / 12) as ocupacion, rsi.cantidad_fuentes_ingreso as rotacion, rsi.ingreso_minimo_cotizante as menor_salario, rsi.ingreso_maximo_cotizante as mayor_salario, rsi.promedio_ingresos_cotizante as salario_promedio, vic.fecha as fecha_consulta');
            $this->db_apiburos->from('valor_ingreso_consulta AS vic');
            $this->db_apiburos->join('valor_ingreso_resumen_general_ingresos AS rsi','vic.id = rsi.id_valor_ingreso_consulta');
            $this->db_apiburos->join('valor_ingreso_indicadores AS vii','vic.id = vii.id_valor_ingreso_consulta');
            $this->db_apiburos->join('valor_ingreso_aportantes AS via','via.id_valor_ingreso_consulta = vic.id');
            $this->db_apiburos->where('vic.numero_identificacion_persona_natural = "'.$param['documento'].'"');
            $this->db_apiburos->where('vic.id_solicitud', $param['id_solicitud']);
            $this->db_apiburos->order_by('vic.fecha', 'DESC');
            $this->db_apiburos->limit(1);
            $query = $this->db_apiburos->get();
            return $query->result();
        }

        public function get_aportantes_laboralE($param) {
            $this->db_apiburos->select('id, numero_identificacion_aportante as NIT, razon_social_aportante as empresa');
            $this->db_apiburos->from('valor_ingreso_aportantes');
            $this->db_apiburos->where('id_valor_ingreso_consulta', $param['id_consulta']);
            $query = $this->db_apiburos->get(); 
            return $query->result_array();
        }
        
        public function get_aportes_laboralE($param) {
            $this->db_apiburos->select('id_valor_ingreso_aportante as id, CONCAT(mes_periodo_validado,"-",ano_periodo_validado) as periodo, ingresos as salario');
            $this->db_apiburos->from('valor_ingreso_aportantes_resultado_pago');
            $this->db_apiburos->where('id_valor_ingreso_consulta', $param['id_consulta']);
            $this->db_apiburos->where_in('id_valor_ingreso_aportante', $param['id_aportante'], FALSE);
            $this->db_apiburos->where('ingresos !=' , 0);
            $this->db_apiburos->order_by('ano_periodo_validado', 'DESC');
            $this->db_apiburos->order_by('mes_periodo_validado', 'DESC');
            $query = $this->db_apiburos->get(); 
            return $query->result_array();
        }
        
        public function get_informacion_laboralArus($param) {
            $this->db_apiburos->select('acp.*');
            $this->db_apiburos->from('arus_cotizantes_periodos AS acp');
            $this->db_apiburos->join('arus_cotizantes_consulta as acc','acc.id = acp.id_cotizantes_consulta');
            $this->db_apiburos->where('acc.numeroDocumento = "'. $param['documento'].'"');
            $this->db_apiburos->where('acc.id_solicitud', $param['id_solicitud']);
            $this->db_apiburos->order_by('acp.periodoCotizacion', 'DESC');
            $query = $this->db_apiburos->get();
            return $query->result_array();
        }

        public function get_id_valor_ingreso_consulta($param) {
            $this->db_apiburos->select('id, created_at, DATE_ADD(created_at, INTERVAL 1 MONTH ) as vencimiento, DATEDIFF(DATE_ADD( created_at, INTERVAL 1 MONTH ), now()) as DiasRestantes');
            $this->db_apiburos->from('valor_ingreso_consulta');
            $this->db_apiburos->where('numero_identificacion_persona_natural ="'. $param['documento'].'"');
            $this->db_apiburos->where('id_solicitud', $param['id_solicitud']);
            $this->db_apiburos->order_by('fecha', 'DESC');
            $this->db_apiburos->limit(1);
            $query = $this->db_apiburos->get();
            return $query->row();
        }

        public function get_id_valor_ingreso_consultaMareigua($param) {
            $this->db_apiburos->select('id, created_at, DATE_ADD(created_at, INTERVAL 1 MONTH ) as vencimiento, DATEDIFF(DATE_ADD( created_at, INTERVAL 1 MONTH ), now()) as DiasRestantes');
            $this->db_apiburos->from('mareigua_consulta');
            $this->db_apiburos->where('numero_identificacion_persona_natural', $param['documento']);
            $this->db_apiburos->where('id_solicitud', $param['id_solicitud']);
            $this->db_apiburos->order_by('fecha', 'DESC');
            $this->db_apiburos->limit(1);
            $query = $this->db_apiburos->get();
            return $query->row();
        }

        function get_id_valor_ingreso_consulta_arus($param) {
            $this->db_apiburos->select('id, created_at, DATE_ADD(created_at, INTERVAL 1 MONTH ) as vencimiento, DATEDIFF(DATE_ADD( created_at, INTERVAL 1 MONTH ), now()) as DiasRestantes');
            $this->db_apiburos->from('arus_cotizantes_consulta');
            $this->db_apiburos->where('id_solicitud', $param['id_solicitud']);
            $this->db_apiburos->where('numeroDocumento = "'. $param['documento'].'"');
            $this->db_apiburos->order_by('created_at', 'DESC');
            $this->db_apiburos->limit(1);
            $query = $this->db_apiburos->get();
            return $query->row();
        }

        private function get_last_consultaMareigua($param) {            
            $this->db_apiburos->select('id');
            $this->db_apiburos->from('mareigua_consulta');
            $this->db_apiburos->where('id_solicitud', $param['id_solicitud']);
            $this->db_apiburos->where('numero_identificacion_persona_natural = "'. $param['documento'].'"');
            $this->db_apiburos->order_by('created_at', 'DESC');
            $this->db_apiburos->limit(1);
            return $this->db_apiburos->get_compiled_select();
        }
       
        public function get_informacion_laboralMareigua($param) {
            $subQuery1 = $this->get_last_consultaMareigua($param);
            $this->db_apiburos->select('mc.AFP, mc.EPS, ROUND(((mrgi.meses_continuidad * 100) / 12),0) as ocupacion, mrgi.cantidad_aportantes as rotacion, mrgi.minimo as menor_salario, mrgi.maximo as mayor_salario, mrgi.promedio_ingresos as salario_promedio, mc.created_at as fecha_consulta');
            $this->db_apiburos->from('mareigua_consulta AS mc');
            $this->db_apiburos->join('mareigua_resumen_general_ingresos AS mrgi','mrgi.id_mareigua_consulta = mc.id');
            $this->db_apiburos->where("mc.id = ($subQuery1)", null, FALSE);
            $query = $this->db_apiburos->get();
            return $query->result_array();
        }

        publiC function get_aportantes_laboralMareigua($param) {
            $subQuery1 = $this->get_last_consultaMareigua($param);
            $this->db_apiburos->select('mrgi.id , mrgi.id_mareigua_consulta, numero_identificacion_aportante, razon_social_aportante');
            $this->db_apiburos->from('mareigua_consulta AS mc');
            $this->db_apiburos->join('mareigua_aportantes AS mrgi','id_mareigua_consulta = mc.id');
            $this->db_apiburos->where("mc.id = ($subQuery1)", null, FALSE);
            $query = $this->db_apiburos->get();
            return $query->result();
        }

        public function get_aportes_laboralMareigua($param) {
            $this->db_apiburos->select('ano_periodo_validado, mes_periodo_validado, ingresos');
            $this->db_apiburos->from('mareigua_aportantes_resultado_pago');
            $this->db_apiburos->where('id_mareigua_consulta', $param['id_mareigua_consulta']);
            $this->db_apiburos->where('id_mareigua_aportante', $param['id_mareigua_aportante']);
            $this->db_apiburos->where('realizo_pago', 1);
            $query = $this->db_apiburos->get();
            return $query->result();            
        }
    }
?>

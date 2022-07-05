<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tesoreria_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('maestro', TRUE);
    }

    public function getEgresosPagado($fechadesde,$fechahasta){
         $dbSolicitudes = $this->load->database('solicitudes', TRUE);
         $dbSolicitudes->select('S.id,'
        .'IF(S.id_tipo_documento=1, "CC", "CE") as tipo_doc,'
        .'DATE_FORMAT(SI.fecha_carga, "%d-%m-%Y" ) AS fecha_carga,'
        .'S.documento,'
        .'S.nombres,'
        .'S.apellidos,'
        .'SCD.capital_solicitado AS MONTO,'
        .'B.Nombre_Banco,'
        .'ST.ruta_txt');

        $dbSolicitudes->from('solicitud AS S,'
                        .'solicitud_imagenes AS SI,'
                        .'solicitud_condicion_desembolso AS SCD,'
                        .'solicitud_txt AS ST,'
                        .'`parametria`.bank_entidades AS B');
        $dbSolicitudes->where('S.estado', 'PAGADO');
        $dbSolicitudes->where('SI.id_imagen_requerida', 16);
        $dbSolicitudes->where('SI.id_solicitud = S.id ');
        $dbSolicitudes->where('SCD.id_solicitud = S.id ');
        $dbSolicitudes->where('ST.id_solicitud = S.id ');
        $dbSolicitudes->where('B.id_Banco = ST.id_banco');
        $dbSolicitudes->where('SI.fecha_carga BETWEEN "'.$fechadesde.'" AND "'.$fechahasta.'"');
        $dbSolicitudes->group_by("S.id");
        $dbSolicitudes->order_by("S.id");
        $egresos = $dbSolicitudes->get();

        return $egresos->result();

    }

    public function getIngresos($fechadesde,$fechahasta){
         $dbIngresos = $this->load->database('maestro', TRUE);
         $dbIngresos->select('crd.id AS Id_Cuota,'
        .'cr.id AS Id_Credito,'
        .'cli.nombres,'
        .'cli.apellidos,'
        .'crd.numero_cuota,'
        .'cli.documento,'
        .'IF(cli.id_tipo_documento=1, "CC", "CE") as tipo_doc,'
        .'DATE_FORMAT( cr.fecha_otorgamiento, "%d-%m-%Y" ) AS fecha_otorgamiento,'
        .'cr.monto_prestado,'
        .'cr.plazo,'
        .'pc.referencia_externa as ref_epayco,'
        .'crd.fecha_vencimiento,'
        .'DATE_FORMAT( crd.fecha_cobro, "%d-%m-%Y" ) AS fecha_cobro,'
        .'crd.monto_cobrado,'
        .'DATEDIFF( cr.fecha_primer_vencimiento, DATE_FORMAT( cr.fecha_otorgamiento, "%Y-%m-%d" ) ) AS dias_plazo,'
        .'DATEDIFF( crd.fecha_cobro, DATE_FORMAT( cr.fecha_otorgamiento, "%Y-%m-%d" ) ) AS dias_pago,'
        .'IF
            (
                (
                    DATEDIFF( crd.fecha_cobro, DATE_FORMAT( cr.fecha_otorgamiento, "%Y-%m-%d" ) ) - DATEDIFF( cr.fecha_primer_vencimiento, DATE_FORMAT( cr.fecha_otorgamiento, "%Y-%m-%d" ) ) 
                ) <= 0,
                0,
                (
                    DATEDIFF( crd.fecha_cobro, DATE_FORMAT( cr.fecha_otorgamiento, "%Y-%m-%d" ) ) - DATEDIFF( cr.fecha_primer_vencimiento, DATE_FORMAT( cr.fecha_otorgamiento, "%Y-%m-%d" ) ) 
                ) 
            ) AS dias_mora,'
        .'crc.tasa_interes,'
        .'crc.seguro,'
        .'crc.administracion,'
        .'crc.tecnologia,'
        .'crc.iva,'
        .'crd.estado,'
        .'"26.65" AS interes_mora');

        $dbIngresos->from('credito_detalle AS crd');
        $dbIngresos->join('creditos AS cr',' crd.id_credito = cr.id');                
        $dbIngresos->join('clientes AS cli ',' cli.id = cr.id_cliente');
        $dbIngresos->join('pago_credito AS pc ',' pc.id_detalle_credito = crd.id');
        $dbIngresos->join('credito_condicion AS crc ',' crc.id_credito = cr.id','left');
        $dbIngresos->where('pc.estado', 1);
        $dbIngresos->where('crd.fecha_cobro BETWEEN "'.$fechadesde.'" AND "'.$fechahasta.'"');
        // $dbIngresos->where('crd.id IN (83,272,1132,1265)',NULL, FALSE);
        //$dbIngresos->where("cr.plazo > ",1);
        $dbIngresos->order_by("cli.documento","ASC");
        $dbIngresos->order_by("crd.id","ASC");
        $dbIngresos->order_by("crd.numero_cuota","ASC");

        $ingresos = $dbIngresos->get();
        //echo $dbIngresos->last_query();
        //die;

        return $ingresos->result();

    }
}

<?php 
class Fixes_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        // LOAD SCHEMA
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_usuarios  = $this->load->database('usuarios_solventa', TRUE);
        $this->db_maestros  = $this->load->database('maestro', TRUE);
        $this->db_gestion  = $this->load->database('gestion', TRUE);
	}

    public function get_last_track_repetidas($params=[])
    {
        $query = $this->db_solicitudes->query("SELECT COUNT(id), id_solicitud FROM `solicitud_ultima_gestion` GROUP BY id_solicitud HAVING COUNT(id) > 1 ORDER BY COUNT(id) DESC");
        //$this->db->get();
        
        return $query->result();
    }

    public function get_last_track($params=[])
    {
        $this->db_solicitudes->select("*");
        $this->db_solicitudes->from("solicitud_ultima_gestion");
        if(isset($params['id_solicitud'])){ $this->db_solicitudes->where("id_solicitud",$params['id_solicitud'] );   }

        $this->db_solicitudes->order_by("fecha DESC, hora DESC");

        $query = $this->db_solicitudes->get();
        
        return $query->result();
    }

    public function delete_last_track($params=[])
    {
        $query = $this->db_solicitudes->query("DELETE FROM solicitud_ultima_gestion WHERE id in (".$params['id'].")");
        
        if ($query) {
            return 1;
        } else {
            return $params;
        }
        
    }

    public function get_mails_solicitudes(){
        //$query = $this->db_solicitudes->query('SELECT documento FROM `solicitud` WHERE email IS NOT NULL AND email != "" AND documento is not null');
        $query = $this->db_solicitudes->query('SELECT documento FROM `solicitud` WHERE email IS NOT NULL AND email != "" AND documento is not null and email not in (select cuenta from solicitante_agenda_mail ) GROUP BY documento');
 
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_agenda_mail_solicitudes($documento){
        $query = $this->db_solicitudes->query('SELECT documento, CONCAT( nombres, " ", apellidos ) contacto, email FROM `solicitud`  WHERE email IS NOT NULL  AND email != "" AND documento = "'.$documento.'" GROUP BY email ORDER BY max(fecha_alta) DESC');
        //var_dump($this->db_solicitudes->last_query());die;
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }


    public function user_email($email){
        $query = $this->db_usuarios->query('SELECT * FROM `users`  WHERE email = "'.$email.'" and active = 1');    

        if($this->db_usuarios->affected_rows() > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function email_registrado($documento, $email){
        $query = $this->db_solicitudes->query('SELECT * FROM `solicitante_agenda_mail`  WHERE cuenta = "'.$email.'" and documento = "'.$documento.'"');    

        if($this->db_solicitudes->affected_rows() > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function agendar_mail($data){
        $query = $this->db_solicitudes->insert('solicitante_agenda_mail', $data);
        if($this->db_solicitudes->affected_rows() > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }

 
    public function get_telefono_solicitudes(){ 
        //$query = $this->db_solicitudes->query('SELECT documento FROM `solicitud` WHERE telefono IS NOT NULL AND telefono != "" ');
        $query = $this->db_solicitudes->query('SELECT documento FROM `solicitud` WHERE telefono IS NOT NULL AND telefono != "" AND documento is not null AND telefono is not null GROUP BY documento');
 
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_agenda_telefono_solicitudes($documento){ 
        $query = $this->db_solicitudes->query('SELECT documento, CONCAT( nombres, " ", apellidos ) contacto, telefono FROM `solicitud`  WHERE telefono IS NOT NULL  AND telefono != "" AND documento = "'.$documento.'" GROUP BY telefono ORDER BY max(fecha_alta) DESC');
        //$query = $this->db_solicitudes->query('SELECT documento, CONCAT( nombres, " ", apellidos ) contacto, telefono FROM `solicitud`  WHERE telefono IS NOT NULL  AND telefono != "" AND documento = "'.$documento.'" ORDER BY max(fecha_alta) DESC');
        //var_dump($this->db_solicitudes->last_query());die;
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function telefono_registrado($documento, $telefono, $fuente){ 
        $query = $this->db_solicitudes->query('SELECT * FROM `solicitante_agenda_telefonica`  WHERE numero = "'.$telefono.'" and documento = "'.$documento.'" AND fuente ="'.$fuente.'"');    

        if($this->db_solicitudes->affected_rows() > 0){
            return $this->db_solicitudes->insert_id();
        } else {
            return -1;
        }
    }

    public function agendar_telefono($data){ 
        $query = $this->db_solicitudes->insert('solicitante_agenda_telefonica', $data);
        if($this->db_solicitudes->affected_rows() > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function actualizar_telefono($data, $id){ 
        $this->db_solicitudes->where('id', $id);
        $this->db_solicitudes->update('solicitante_agenda_telefonica', $data);

        if($this->db_solicitudes->affected_rows() > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }
   
    // ids numeros personales declarados
    public function update_personal_to_declarados(){ 

        $query = $this->db_solicitudes->query('SELECT id FROM solicitante_agenda_telefonica WHERE fuente = "PERSONAL" GROUP by documento');

        $ids = implode(",", array_column($query->result_array(), 'id'));
        
        $query = $this->db_solicitudes->query('UPDATE solicitante_agenda_telefonica SET fuente="PERSONAL DECLARADO" WHERE id in ('.$ids.')');    
        

        return $this->db_solicitudes->affected_rows();
    }


    public function get_agenda_telefono_referencia($documento){ 
        $query = $this->db_solicitudes->query('SELECT sol.id, sol.documento, ref.nombres_apellidos contacto, ref.telefono, ref.id_parentesco FROM `solicitud_referencias`as ref join solicitud as sol ON ref.id_solicitud = sol.id WHERE ref.telefono IS NOT NULL  AND ref.telefono != "" AND sol.documento = "'.$documento.'"');
        //var_dump($this->db_solicitudes->last_query());die;
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }

    // CLIENTES DUPLICADOS

    public function get_clientesDuplicados(){
        $query = $this->db_maestros->query("SELECT COUNT(id), documento, fecha_alta FROM `clientes` GROUP BY documento HAVING COUNT(id) > 1 ORDER BY COUNT(id) DESC");

        if ($this->db_maestros->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }

    }

    public function get_cliente($documento){
        $query = $this->db_maestros->query("SELECT id FROM `clientes` WHERE `documento` LIKE $documento");

        if ($this->db_maestros->affected_rows() > 0) {
            return $query->result_array();
        } else {
            return [];
        }
    }

    public function get_solicitud($documento){
        $query = $this->db_solicitudes->query("SELECT * FROM `solicitud` WHERE `documento` LIKE $documento and estado = 'PAGADO' and tipo_solicitud = 'PRIMARIA'");

        if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_creditosSueltos($documento, $stringCliente){
        $query = $this->db_maestros->query("SELECT creditos.*, sum(credito_detalle.monto_cobrado) AS suma FROM `creditos`, credito_detalle WHERE creditos.id = credito_detalle.id_credito and `id_cliente` IN ('".$stringCliente."') and creditos.id not in (SELECT id_credito FROM solicitudes.`solicitud` WHERE `documento` = '$documento') GROUP by creditos.id");
        // var_dump($this->db_maestros->last_query()); die;
        if ($this->db_maestros->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }

    }

    public function dropCreditoDetalle($idCredito){
        // $query = $this->db_maestros->query("SELECT * FROM `credito_detalle` WHERE `id_credito` = $idCredito");
        $query = $this->db_maestros->query("DELETE FROM `credito_detalle` WHERE `id_credito` = $idCredito");

        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

    public function dropCreditoCondicion($idCredito){
        // $query = $this->db_maestros->query("SELECT * FROM `credito_condicion` WHERE `id_credito` = $idCredito");
        $query = $this->db_maestros->query("DELETE FROM `credito_condicion` WHERE `id_credito` = $idCredito");

        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

    public function dropCreditoDuplicado($idCredito){
        $query = $this->db_maestros->query("DELETE FROM `creditos` WHERE `id` = $idCredito");
        // $query = $this->db_maestros->query("SELECT * FROM `creditos` WHERE `id` = $idCredito");

        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

    public function dropCliente($idCliente){
        $query = $this->db_maestros->query("DELETE FROM `clientes` WHERE `id` = $idCliente");
        // $query = $this->db_maestros->query("SELECT * FROM `clientes` WHERE `id` = $idCliente");

        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

     public function updateSolicitud($id_cliente,$id_credito,$id_solicitud){
        $query = $this->db_solicitudes->query("UPDATE solicitud SET id_cliente = $id_cliente, id_credito = $id_credito WHERE id = $id_solicitud");
        
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $this->db_solicitudes->affected_rows();
        } else {
            return false;
        }
    }

    // CREDITOS DUPLICADOS

    public function get_creditosOriginales(){
        $query = $this->db_maestros->query('SELECT * FROM `creditos` WHERE id NOT IN (SELECT id_credito FROM '.$this->db_solicitudes->database.'.`solicitud` WHERE `id_credito` > 0) ORDER BY `creditos`.`estado` DESC');

        if ($this->db_maestros->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_CreditosDuplicados($id_cliente){
        $query = $this->db_maestros->query("SELECT id FROM `creditos` WHERE `id_cliente` = $id_cliente AND estado = 'vigente' ORDER BY id ASC");

        if ($this->db_maestros->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_CreditosDetalle($idDet1,$idDet2){
        $query = $this->db_maestros->query("SELECT * FROM `credito_detalle` WHERE `id_credito` IN ('".$idDet1."','".$idDet2."') ORDER BY id_credito ASC;");

        if ($this->db_maestros->affected_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_solicitudxCreditoCliente($id, $idCliente){
        $query = $this->db_solicitudes->query("SELECT * FROM `solicitud` WHERE `id_credito` = $id AND `id_cliente` = $idCliente");

        if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }
    
    public function updateSolicitud2($creditosDuplicadosId, $id_solicitud){
        $query = $this->db_solicitudes->query("UPDATE solicitud SET id_credito = $creditosDuplicadosId WHERE id = $id_solicitud");
        
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $this->db_solicitudes->affected_rows();
        } else {
            return false;
        }
    }

    public function dropCreditoDetalle2($id_credito){
        $query = $this->db_maestros->query("DELETE FROM `credito_detalle` WHERE `id_credito` = $id_credito");

        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

    public function dropCredito2($id_credito){
        $query = $this->db_maestros->query("DELETE FROM `creditos` WHERE `id` = $id_credito");

        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }
    public function getCreditosSinMontoDevolver(){
        $query = $this->db_maestros->query("SELECT creditos.id id_credito, credito_detalle.id cuota, solicitud.id id_solicitud, creditos.fecha_primer_vencimiento, creditos.fecha_otorgamiento FROM `creditos`, credito_detalle, credito_condicion, solicitudes.solicitud solicitud WHERE creditos.id = credito_detalle.id_credito AND creditos.id = credito_condicion.id_credito and creditos.id = solicitud.id_credito and credito_detalle.monto_cuota = 0");

        if ($this->db_maestros->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function updateCreditoEstado($id_credito,$estado) {
        $query = $this->db_maestros->query("UPDATE creditos SET estado = 'mora' WHERE id = $id_credito");
        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }
    public function updateCreditoDetalleEstado($id_credito_detalle,$estado) {
        $query = $this->db_maestros->query("UPDATE credito_detalle SET estado = 'mora' WHERE id = $id_credito_detalle");
        
        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

    public function get_vencimientoFecha0000($date){
        $query = $this->db_maestros->query("SELECT GROUP_CONCAT(id) AS id_credito FROM creditos WHERE fecha_primer_vencimiento = $date");

        if ($this->db_maestros->affected_rows() > 0) {
            $data = str_replace(',', "','", $query->row('id_credito'));
            return $data;
        } else {
            return FALSE;
        }
    }

    public function get_solicitudByStringId($data){
        $query = $this->db_solicitudes->query("SELECT GROUP_CONCAT(id) AS id_solicitud FROM solicitud WHERE id_credito IN ('".$data."')");

        if ($this->db_solicitudes->affected_rows() > 0) {
            $data = str_replace(',', "','", $query->row('id_solicitud'));
            return $data;
        } else {
            return FALSE;
        }
    }

    public function get_solicitudCondicion($table, $data){
        $query = $this->db_solicitudes->query("SELECT GROUP_CONCAT(id) AS id_condicion FROM $table WHERE id_solicitud IN ('".$data."')");

        if ($this->db_solicitudes->affected_rows() > 0) {
            $data = str_replace(',', "','", $query->row('id_condicion'));
            return $data;
        } else {
            return FALSE;
        }
    }

    public function get_CreditoCondicion($data){
        $query = $this->db_maestros->query("SELECT GROUP_CONCAT(id) AS id_credito_condicion FROM credito_condicion WHERE id_credito IN ('".$data."')");
        // $query = $this->db_maestros->query("SELECT * FROM credito_condicion WHERE plazo = 1 AND id_credito IN ('".$data."')");

        if ($this->db_maestros->affected_rows() > 0) {
            $data = str_replace(',', "','", $query->row('id_credito_condicion'));
            return $data;
            // return $query->result();
        } else {
            return FALSE;
        }
    }

    public function get_CreditoDetalle($data){
        // $query = $this->db_maestros->query("SELECT GROUP_CONCAT(id) AS id_credito_condicion FROM credito_condicion WHERE id_credito IN ('".$data."')");
        $query = $this->db_maestros->query("SELECT * FROM credito_detalle WHERE id_credito IN ('".$data."')");

        if ($this->db_maestros->affected_rows() > 0) {
            // $data = str_replace(',', "','", $query->row('id_credito_condicion'));
            // return $data;
            return $query->result();
        } else {
            return FALSE;
        }
    }

    public function UpdateDataMaestro($table, $campo, $varUpdate, $data){
        $query = $this->db_maestros->query("UPDATE $table SET $campo = '".$varUpdate."' WHERE id IN ('".$data."')");
        
        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

    
    public function UpdateDataSolicitudes($table, $campo, $varUpdate, $data){
        $query = $this->db_solicitudes->query("UPDATE $table SET $campo = '".$varUpdate."', dias = 38 WHERE id_solicitud IN ('".$data."')");
        
        if ($this->db_solicitudes->affected_rows() > 0) {
            return $this->db_solicitudes->affected_rows();
        } else {
            return false;
        }
    }

    public function UpdateDataMaestro2($table, $campo, $varUpdate, $data){
        $query = $this->db_maestros->query("UPDATE $table SET $campo = '".$varUpdate."', dias = 38 WHERE id IN ('".$data."')");
        
        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

    public function UpdateDataMaestro3($table, $data, $id){
        $this->db_maestros->where('id', $id);
        $this->db_maestros->update($table, $data);

        if ($this->db_maestros->affected_rows() > 0) {
            return $this->db_maestros->affected_rows();
        } else {
            return false;
        }
    }

    public function getDataCero(){
        $query = $this->db_solicitudes->query("SELECT scd.dias, scd.total_devolver, s.id_credito FROM solicitud AS s, solicitud_condicion_desembolso AS scd WHERE id_credito IN (SELECT id FROM maestro.`creditos` WHERE fecha_otorgamiento >= '2021-04-01 00:00:00' AND monto_devolver = 0) AND scd.id_solicitud=s.id");



        if ($this->db_solicitudes->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function UpdateCreditoCondicion($data){
        $query = $this->db_maestros->query("UPDATE credito_condicion SET total_devolver = '".$data->total_devolver."', dias = ".$data->dias." WHERE id_credito = ".$data->id_credito);

        if ($this->db_maestros->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function UpdateCreditos($data){
        $query = $this->db_maestros->query("UPDATE creditos SET monto_devolver = '".$data->total_devolver."' WHERE id = ".$data->id_credito);

        if ($this->db_maestros->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function UpdateCreditoDetalle($data){
        $query = $this->db_maestros->query("UPDATE credito_detalle SET monto_cuota = '".$data->total_devolver."' WHERE id_credito = ".$data->id_credito);

        if ($this->db_maestros->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function clientesAcuerdosPagosAbril()
    {
        $sql = "select ap.id_cliente from gestion.acuerdos_pago ap, maestro.creditos c, maestro.credito_detalle cd, maestro.pago_credito pc
        where ap.id_cliente = c.id_cliente and c.id = cd.id_credito and cd.id = pc.id_detalle_credito and pc.estado = 1 and ap.estado not in ('cumplido', 'anulado') and pc.medio_pago not in ('descuento','devolucion',  'debito automatico')  and cd.fecha_cobro >= '2022-01-01' and pc.id not in (select id_pago from maestro.devolucion_pagos)
        group by c.id_cliente";

        $query = $this->db_maestros->query($sql);

        return $query->result();
        

    }

    public function pagosParaAcuerdos($id_cuota, $fecha_pago)
    {
        $sql = "select sum(monto) monto from maestro.pago_credito where substr(fecha_pago,1, 10) = '$fecha_pago' and id_detalle_credito = $id_cuota and estado = 1 and medio_pago not in ('descuento','devolucion','debito automatico') ";

        $query = $this->db_maestros->query($sql);

        return $query->result();
       
    }

        /**
     * se obtienen los acuerdos de pago de un cliente  en estado pendiente o incumplido
     * para una fecha determinada 
     */
    public function getAcuerdoByClienteFechaNew($id_cliente, $fecha_cobro){

        $sql = "SELECT ap.*
                FROM   gestion.`acuerdos_pago` ap
                WHERE  ap.id_cliente = {$id_cliente}
                AND ap.fecha >= DATE_SUB('{$fecha_cobro}', INTERVAL 5 DAY)
                AND ap.fecha <= DATE_ADD('{$fecha_cobro}', INTERVAL 5 DAY)
                AND ap.estado IN ('pendiente','incumplido')
                ORDER BY CASE ap.estado
                WHEN 'pendiente' THEN 1
                ELSE 0
                END DESC";

        $query = $this->db_maestros->query($sql);

        return $query->result();
        

    }

    public function updateEstadoAcuerdo($acuerdo_id = 0, $estado = ''){
        $this->db_maestros->set('estado', $estado);
        $this->db_maestros->from('gestion.acuerdos_pago');
        $this->db_maestros->where('id', $acuerdo_id);
        //$this->db_gestion->limit(1);
        $returnValue = $this->db_maestros->update('gestion.acuerdos_pago');

        return $returnValue;
    }

    public function getCreditosDetalleCancelado( $id_cliente = null)
    {
        $sql = "SELECT cd.*
        FROM   maestro.creditos c, maestro.credito_detalle cd
        WHERE  c.id = cd.id_credito and c.id_cliente = {$id_cliente}
        AND cd.fecha_cobro >= '2021-04-01'";

        $query = $this->db_maestros->query($sql);

        return $query->result();
    }
    
    public function insert_situacion_laboral($data){ 
        $query = $this->db_maestros->insert('situacion_laboral', $data);
        if($this->db_maestros->affected_rows() > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function insert_riesgo_crediticio($data){
        
        $query = $this->db_solicitudes->insert('riesgo_crediticio', $data);
        return $this->db_solicitudes->affected_rows();
        
    }

    public function clientesRiesgo()
    {
        $sql = "SELECT documento, 'MORA > 40' razon, CURRENT_TIMESTAMP  fecha_hora_aplicacion  FROM maestro.clientes WHERE id IN (SELECT id_cliente FROM maestro.creditos WHERE id IN (SELECT id_credito FROM maestro.credito_detalle WHERE dias_atraso > 40)) and documento not in (SELECT documento FROM solicitudes.riesgo_crediticio)";
        $query = $this->db_solicitudes->query($sql);
        return $query->result_array();
    }

    public function getTrackBug(){
        $sql = "SELECT * FROM `track_gestion` WHERE `observaciones` LIKE '%<br> Comprobante adjunto: <a%' and id_tipo_gestion = 171 ORDER BY `track_gestion`.`id_tipo_gestion` ASC";
        $query = $this->db_gestion->query($sql);
        return $query->result();
    }

    public function updateTrackBug($id, $data){
        $this->db_gestion->where('id', $id);
        $this->db_gestion->update('track_gestion', ['observaciones'=>$data]);
        return $this->db_gestion->affected_rows();
    }
}
<?php
class Legales_model extends CI_Model
{
	public function __construct()
	{
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_gestion = $this->load->database('gestion', TRUE);
        $this->db_parametria = $this->load->database('parametria', TRUE);
		parent::__construct();

	}

    //insertando registro en baja_datos
    public function set_registros($razon, $documento)
    {   
        $data = ['documento'=>(string)$documento, 'razon'=>$razon, 'fecha_hora_aplicacion'=>date('Y-m-d H:i:s'), 'idoperador' => $this->session->userdata("idoperador")];

        $this->db_solicitudes->insert('baja_datos',$data);
        if ($this->db_solicitudes->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
    }

    //verificando si el cliente cuenta con un credito vigente o en mora
    public function credito_vigente($documento)
    {
        $this->db_maestro->select('documento, nombres, apellidos, creditos.estado');
        $this->db_maestro->from('clientes');
        $this->db_maestro->join('creditos', 'creditos.id_cliente = clientes.id');
        $this->db_maestro->where("documento = '$documento' ");        
        $this->db_maestro->where("creditos.estado IN ('vigente','mora')");
        
        $result_cred = $this->db_maestro->get();
        $result_credito = $result_cred->result_array();
        return $result_credito;
    }

    //buscando los datos del cliente para mostrar en la tabla en la tabla de solicitud
    public function solicitud_documento($documento)
    {
        $this->db_solicitudes->select('MAX(id) as id, documento, nombres, apellidos');
        $this->db_solicitudes->from('solicitud');             
        $this->db_solicitudes->where("documento = '$documento'");
        $this->db_solicitudes->limit(1);
        $datos_doc = $this->db_solicitudes->get();
        $datos = $datos_doc->result_array();
        if ($datos[0]['id'] != null && $datos[0]['documento'] != null && $datos[0]['nombres'] != null) {  
            $datos[0]['tipo'] = 'No Baja';
            return $datos;
        }else {
            return false;
        }
    }

    //traemos las cuotas pendientes del cliente y las actualizamos para porder dar de baja los datos
    public function cuotas_pendientes($documento)
    {
        $sub_query1 = "SELECT id FROM clientes WHERE documento = '$documento' ";

        $sub_query2 = 'SELECT id FROM creditos WHERE id_cliente IN ('.$sub_query1.'';

        $this->db_maestro->select('*');
        $this->db_maestro->from('credito_detalle');
        $this->db_maestro->where('id_credito IN ('.$sub_query2.'))');
        $this->db_maestro->where('(estado = "mora" OR estado IS NULL)');
        $datos_cuotas = $this->db_maestro->get();
        $cuotas = $datos_cuotas->result_array();
        if($cuotas){

            $sql=('UPDATE credito_detalle SET fecha_cobro = "'.date('Y-m-d').'", descuento = monto_cobrar, monto_cobrado = 0, estado = "pagado"
            WHERE id_credito IN ('.$sub_query2.')) AND (estado = "mora" OR estado IS NULL)');
            $data = $this->db_maestro->query($sql);
            if ($data) {
                return true;
            }
            else{
                return false;
            }
        }

    }

    //actualizamos el estado del credito por cancela para la baja de datos
    public function actualizar_estado($documento)
    {
        $datos = [
            'estado' => 'cancelado'
        ];

        $sub_actualizar = 'SELECT id FROM clientes WHERE documento LIKE '.$documento.'';

        $this->db_maestro->where('id_cliente IN ('.$sub_actualizar.')');
        $this->db_maestro->where('estado IN ("mora", "vigente")');
        $this->db_maestro->update ('creditos' , $datos);

        if ($this->db_maestro->affected_rows() > 0) {
            return true;
        }else {
            return false;
        }
    }

    //Buscamos si el cliente ya se encuentra en la riesgo_crediticio
    public function buscar_bloqueo($documento)
    {
        $this->db_solicitudes->select('documento, razon, fecha_hora_aplicacion');
        $this->db_solicitudes->from('riesgo_crediticio');
        $this->db_solicitudes->where("documento = '$documento'");
        $busqueda_blo = $this->db_solicitudes->get();
        $busqueda = $busqueda_blo->result_array();
        if ($busqueda) {
            return $busqueda;
        }else{
            return false;
        }
        
    }

    public function consultar_bloqueo($documento){
        $this->db_solicitudes->select('documento');
        $this->db_solicitudes->from('riesgo_crediticio');
        $this->db_solicitudes->where("documento = '$documento'");
        $busqueda_blo = $this->db_solicitudes->get();
        $busqueda = $busqueda_blo->result_array();
        
        return $busqueda;        
    }

    public function consultar_baja($documento){
        $this->db_solicitudes->select('documento');
        $this->db_solicitudes->from('baja_datos');
        $this->db_solicitudes->where("documento = '$documento'");
        $busqueda_baja = $this->db_solicitudes->get();
        $busqueda = $busqueda_baja->result_array();
        return $busqueda;   
        
    }

    //Insertando registro en la Blacklist
    public function bloquear_cliente($razon, $documento)
    {
        $data = ['razon'=>$razon, 'documento'=>(string)$documento, 'fecha_hora_aplicacion'=>date('Y-m-d H:i:s'), 'idoperador' => $this->session->userdata("idoperador")];

        $this->db_solicitudes->insert('riesgo_crediticio',$data);
        if ($this->db_solicitudes->affected_rows() > 0) {
            return true;
        }else{
            return false;
        }
    }

    //retornar la razon con la que fue guardado el cliente en baja_datos
    public function buscar_bajaDatos($documento)
    {
        $this->db_solicitudes->select('documento, razon, fecha_hora_aplicacion');
        $this->db_solicitudes->from('baja_datos');
        $this->db_solicitudes->where("documento = '$documento'");
        $busqueda_blo = $this->db_solicitudes->get();
        $busqueda = $busqueda_blo->result_array();
        if ($busqueda) {
            return $busqueda;
        }else{
            return false;
        }
    }

    //eliminando cliente de baja_datos
    public function eliminar_registro($documento)
    {
        $this->db_solicitudes->where("documento = '$documento'");
        $this->db_solicitudes->from('baja_datos');
        $this->db_solicitudes->delete();
        if($this->db_solicitudes->affected_rows() > 0){
            return true;
        }else{
            return false;
        }

    } 
    //eliminando cliente de riesgo_crediticio
    public function eliminar_bloquedo($documento)
    {
        $this->db_solicitudes->where("documento = '$documento'");
        $this->db_solicitudes->from('riesgo_crediticio');
        $this->db_solicitudes->delete();
        if($this->db_solicitudes->affected_rows() > 0){
            return true;
        }else{
            return false;
        }

    } 

    public function obtener_datos_descarga($tipo)
    {
        $this->db_solicitudes->select('b.documento,c.nombres,c.apellidos,CONCAT(57, (a.numero)) as telefono,b.fecha_hora_aplicacion,b.razon,b.idoperador');
        if ($tipo == 'baja') {
            $this->db_solicitudes->from($this->db_solicitudes->database.'.baja_datos as b');
        }else {
            $this->db_solicitudes->from($this->db_solicitudes->database.'.riesgo_crediticio as b');
        }
        $this->db_solicitudes->join($this->db_maestro->database.'.clientes as c', 'b.documento = c.documento');
        $this->db_solicitudes->join($this->db_maestro->database.'.agenda_telefonica as a', 'a.id_cliente= c.id');
        $this->db_solicitudes->group_by('b.id');
        $datos = $this->db_solicitudes->get();
        $resul = $datos->result_array();
        return $resul;
    }

    public function listar_usura()
    {
        $this->db_gestion->select('nombre_apellido');
        $this->db_gestion->from('gestion.operadores');
        $this->db_gestion->where('idoperador = id_operador_creacion');
        $subQSle1 = $this->db_gestion->get_compiled_select();

        $this->db_gestion->select('nombre_apellido');
        $this->db_gestion->from('gestion.operadores');
        $this->db_gestion->where('idoperador = id_operador_update');
        $subQSle2 = $this->db_gestion->get_compiled_select();

        $this->db_parametria->select('tu.id,tu.mes,tu.anio,tu.valor,tu.fecha_creacion,('.$subQSle1.') AS nombre_apellido, tu.fecha_update, ('.$subQSle2.') AS operador_update');
        $this->db_parametria->from('tasa_usura as tu');
        $this->db_parametria->order_by('tu.fecha_creacion DESC');

        $datos = $this->db_parametria->get();
        $datosUsura = $datos->result();
        return $datosUsura;
    }

    public function registro_usura($array)
    {
        $fecha_actual = date('Y-m-d H:i:s');
        $id_operador = $this->session->userdata("idoperador");
        $datos = ['mes'=>$array['mes'], 'anio'=>$array['anio'], 'valor'=>$array['monto'], 'fecha_creacion'=>$fecha_actual,
        'id_operador_creacion'=>$id_operador, 'fecha_update'=>$fecha_actual, 'id_operador_update'=>$id_operador, 'estado'=>1];
        
        $this->db_parametria->insert('tasa_usura', $datos);
        if ($this->db_parametria->affected_rows() > 0) {
            return true;
        }else{
            return false;
        }
    }

    public function update_usura($array)
    {
        $fecha_actual = date('Y-m-d H:i:s');
        $id_operador_upd = $this->session->userdata("idoperador");
        $sql=('UPDATE tasa_usura SET mes = "'.$array['mes'].'", anio = "'.$array['anio'].'", valor = "'.$array['monto'].'", fecha_update = "'.$fecha_actual.'", id_operador_update ="'.$id_operador_upd.'"
        WHERE id = "'.$array['id'].'"');
        $rs_actualizar = $this->db_parametria->query($sql);

        if ($data) {
            return true;
        }
        else{
            return false;
        }
        
    }

    public function get_comprobantes($datos)
    {
        $this->db_solicitudes->select("comprobante");
        $this->db_solicitudes->select("nombre_comprobante");
        $this->db_solicitudes->select("origen_comprobante");
        $this->db_solicitudes->select("fecha_registro");
        $this->db_solicitudes->from("comprobantes_legales");
        $this->db_solicitudes->where("documento_cliente =".(string)$datos['documento']);
        $archivos = $this->db_solicitudes->get();
        $list_adjunto = $archivos->result_array();
        return $list_adjunto;
    }

    public function insertar_archivo($documento, $archivo, $ruta, $emitido){
        $fecha_registro = date('Y-m-d H:i:s');
        $nombre=$archivo['name'];  
        $tipo_imagen=$archivo['type'];  
        $tamano=$archivo['size'];
        
        $datos = "INSERT INTO comprobantes_legales (documento_cliente, nombre_comprobante, origen_comprobante, comprobante, tipo_comprobante, fecha_registro) VALUES ('$documento','$nombre', '$emitido', '$ruta', '$tipo_imagen', '$fecha_registro')";
        $this->db_solicitudes->query($datos);

        if ($this->db_solicitudes->affected_rows() > 0) {
            return true;
        }else{
            return false;
        }
    }

    public function exist_archivo($documento, $nombre_archivo)
    {
        $this->db_solicitudes->select('nombre_comprobante');
        $this->db_solicitudes->from('comprobantes_legales');
        $this->db_solicitudes->where("documento_cliente ='$documento'");
        $this->db_solicitudes->where("nombre_comprobante = '$nombre_archivo'");

        $busq_adjunto = $this->db_solicitudes->get();
        $coin_adjunto = $busq_adjunto->result_array();
        return $coin_adjunto;
    }

}

?>

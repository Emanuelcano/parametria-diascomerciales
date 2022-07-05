<?php
class Credito_model extends CI_Model {
    
    const ESTADO_VIGENTE = 'vigente';
    const ESTADO_MORA = 'mora';
    const ESTADO_CANCELADO = 'cancelado';
       
    public function __construct(){  
        parent::__construct();      
        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
		$this->db_maestro = $this->load->database('maestro',TRUE);
        $this->db_gestion = $this->load->database('gestion',TRUE);
    }
    public function getById($id=0)
    {
        $this->db->select('*');
        $this->db->from('creditos');
        $this->db->where('id',$id);

        $query = $this->db->get();
        $this->Sqlexceptions->checkForError();

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }
        return 0;
    }

    public function search($params=[])
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('*');
        $this->db->from('creditos');

        if(!empty($params['id_cliente'])){ $this->db->where('id_cliente',trim($params['id_cliente'])); }
        if(isset($params['id'])){ $this->db->where('id',trim($params['id'])); }
        if(isset($params['where'])){ $this->db->where($params['where']); }

        $query = $this->db->get();

        return $query->result_array();
    }

    public function update($params=[], $data){
        if(isset($params['id'])){$this->db->where('id',$params['id']);}
        $update = $this->db->update('maestro.creditos', $data);
        $update = $this->db->affected_rows();
        return $update;
    }


    /*
    Betza inicio
    */

    public function simple_list($params = [])
    {

        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('solicitud.fecha_ultima_actividad as ultima_actividad, creditos.id, creditos.monto_prestado, credito_detalle.fecha_vencimiento, credito_detalle.monto_cobrar as deuda , IFNULL( credito_detalle.estado, "vigente" ) AS estado, clientes.documento , clientes.nombres, clientes.apellidos, IFNULL( last_track.observaciones, "" ) AS last_track, creditos.id_cliente');
        $this->db->from('maestro.creditos as creditos');
        $this->db->join('solicitudes.solicitud as solicitud', 'solicitud.id_credito = creditos.id');
        $this->db->join('maestro.credito_detalle as credito_detalle','credito_detalle.id_credito = creditos.id');
        $this->db->join('solicitudes.solicitud_ultima_gestion last_track','last_track.id_solicitud = solicitud.id', 'left');


        $this->db->join('maestro.clientes as clientes', 'clientes.id = creditos.id_cliente');
        $this->db->join('agenda_telefonica as agenda', 'agenda.id_cliente = clientes.id');
        
        //si la consulta es por busqueda
        //var_dump($params['criterio']);die;
        if(isset($params['search']) && strlen($params['search']) > 0 ){
            switch ($params['criterio']){
                case 'id':
                    $this->db->where('creditos.id', $params['search']);
                break;
                case 'telefono':
                    $this->db->where('agenda.numero', $params['search']);
                break;
                case 'documento':
                    $this->db->where('clientes.documento = "'.$params['search'].'"');
                break;
                case 'nombre':
                    $this->db->or_like('clientes.nombres', $params['search'], 'both');
                break;
                case 'apellido':
                    $this->db->or_like('clientes.apellidos', $params['search'], 'both');
                break; 

            }            
            
        } else {
            // if(isset($params['estado_credito']))        { $this->db->where('creditos.estado','mora');}
            // if(isset($params['estado_credito']))        { $this->db->where('creditos.estado', 'vigente');}
            // if(isset($params['estado_cuota'])){
            //     if ($params['estado_cuota']=="null") {
                    $this->db->where('(credito_detalle.estado is null or credito_detalle.estado = "mora")'); 
            //     } else{
            //         $this->db->where('credito_detalle.estado',$params['estado_cuota']); 
            //     }
            // }
            if(isset($params['fecha_inicio']))          {$this->db->where("credito_detalle.fecha_vencimiento BETWEEN '".$params['fecha_inicio']."' AND '".$params['fecha_fin']."'");}
            if(isset($params['tipo_solicitud'])) 		{$this->db->where("solicitud.tipo_solicitud",$params['tipo_solicitud']);}
            if(isset($params['operador'])) 		 		{$this->db->where("solicitud.operador_asignado",$params['operador']);}	
        }
        
        $this->db->group_by('credito_detalle.id');
        $this->db->order_by('ultima_actividad','ASC');
        $query = $this->db->get();   
        //var_dump($this->db->last_query());die;
        return $query->result_array();
    }

    

   
    public function simple_list_externo($params = [])
    {

        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('creditos.id, clientes.id id_cliente, creditos.monto_prestado, creditos.fecha_otorgamiento, creditos.estado, clientes.nombres, clientes.apellidos');
        
        $this->db->from('clientes');
        $this->db->join('creditos', 'creditos.id_cliente = clientes.id');
        
        $this->db->where('creditos.id_cliente', $params['search']);
        $this->db->or_like('clientes.nombres', $params['search'], 'both');
        $this->db->or_like('clientes.apellidos', $params['search'], 'both');

        $query = $this->db->get();     
        return $query->result_array();
    }

    public function get_creditos_cliente($params=[])
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('cd.*, c.id_cliente, c.fecha_otorgamiento, c.monto_prestado, c.plazo, c.dias_atraso c_dias_atraso, c.fecha_finalizacion, c.estado estado_credito, c.deuda, c.fecha_primer_vencimiento, c.monto_devolver');
        $this->db->from('creditos c');
        $this->db->join('credito_detalle cd', 'c.id = cd.id_credito');
        
        if(isset($params['id_cliente'])){ $this->db->where('c.id_cliente',trim($params['id_cliente'])); }
        if(isset($params['credito_estado'])){ $this->db->where("c.estado in ('".$params['credito_estado']."')"); }
        if(isset($params['id_credito'])){ $this->db->where('cd.id_credito',trim($params['id_credito'])); }
        if(isset($params['id_cuota'])){ $this->db->where('cd.id',trim($params['id_cuota'])); }
        if(isset($params['where'])){ $this->db->where($params['where']); }
        if(isset($params['estado_cuota'])){ $this->db->where('cd.estado',$params['estado_cuota']); }
        if(isset($params['order'])){ $this->db->order_by($params['order'], $params['sentido']);}
        if(isset($params['limit'])){ $this->db->limit($params['limit']);}
        

        $query = $this->db->get();
       //var_dump($this->db->last_query()); die;
        return $query->result_array();
    } 

    public function mora_al_dia_cliente($cliente)
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('c.id_cliente, COUNT(DISTINCT c.id) "creditos", SUM(cd.monto_cobrar) deuda, MAX(cd.dias_atraso) dias_atraso, COUNT(c.id) "cuotas"');
        $this->db->from('creditos c');
        $this->db->join('credito_detalle cd', 'c.id = cd.id_credito');
        $this->db->where('cd.estado = "mora"');
        $this->db->where('c.id_cliente', $cliente);
        $this->db->group_by('c.id_cliente');

        $query = $this->db->get();   
       //var_dump($this->db->last_query()); die;    
        return $query->result_array();
    }

    public function get_cuota_mas_antigua($param) {
        
            $this->db = $this->load->database('maestro',TRUE);
            $this->db->select('c.id id_credito, c.id_cliente,c.estado,	cd.id,	cd.numero_cuota,cd.fecha_vencimiento,cd.monto_cuota,cd.monto_cobrar,cd.estado,	MIN(cd.fecha_vencimiento)');
            $this->db->from('creditos c');
            $this->db->join('credito_detalle cd', 'c.id = cd.id_credito');
            
            if(isset($param['id_cliente']))     {$this->db->where('c.id_cliente',$param['id_cliente'] );}
            if(isset($param['id_credito']))     {$this->db->where('c.id', $param['id_credito']);}
            $this->db->where('c.estado IN ( "vigente", "mora" )');


            $this->db->group_start();
            $this->db->where('cd.estado is NULL OR cd.estado != "pagado"');
            $this->db->group_end();
    
            $query = $this->db->get();      
            return $query->result_array();
        
    }

    public function get_acuerdo_info($parametro){
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('ap.*, cd.id id_cuota, cd.estado estado_cuota, cd.fecha_vencimiento');
        $this->db->from('acuerdos_pago ap');

        $this->db->join('maestro.creditos c', "c.id_cliente = ap.id_cliente");
        $this->db->join('maestro.credito_detalle cd', "cd.id_credito = c.id");
        if(isset($parametro['estado_mora_null']))    { $this->db->group_start();$this->db->where('cd.estado = "mora" or cd.estado is  null');$this->db->group_end();}
        if(isset($parametro['estado_cuota']))       { $this->db->where('cd.estado',$parametro['estado_cuota']);}
        if(isset($parametro['id_acuerdo']))         { $this->db->where('ap.id', (int)$parametro['id_acuerdo']);}
        
        if(isset($parametro['dir']))                {$this->db->order_by('cd.fecha_vencimiento', $parametro['dir']);}
        
        $query = $this->db->get(); 
        //var_dump($this->db->last_query()); die; 
        return $query->result();
    }
    public function acuerdos_pago($parametro)
    {
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('ap.*, pd.id id_descuento, pd.ajustado_por, pd.descripcion');
        $this->db->from('acuerdos_pago ap');
        $this->db->join('planes_descuentos pd', "pd.id = ap.id_planes_descuentos",'left');
        
        if(isset($parametro['id_cliente']))     { $this->db->where('ap.id_cliente', $parametro['id_cliente']);}
        if(isset($parametro['estado']))     { $this->db->where('ap.estado', $parametro['estado']);}
        if(isset($parametro['id_operador']))     { $this->db->where('ap.id_operador', $parametro['id_operador']);}
        $this->db->order_by('fecha_hora', 'DESC');
        $query = $this->db->get(); 
        //var_dump($this->db->last_query()); die; 
        return $query->result_array();
    }

    public function acuerdos_pago_detalle($parametro)
    {
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('ap.*, pd.descripcion, ad.id id_detalle, ad.id_credito_detalle, ad.monto_acuerdo, op.nombre_apellido, cd.id_credito, cd.numero_cuota, cd.fecha_vencimiento, cd.estado estado_cuota');
        $this->db->from('acuerdos_pago ap');
        $this->db->join('acuerdos_detalle ad', "ap.id = ad.id_acuerdo");
        $this->db->join('operadores op', "ap.id_operador = op.idoperador",'left');
        $this->db->join('planes_descuentos pd', "pd.id = ap.id_planes_descuentos",'left');
        $this->db->join('maestro.credito_detalle cd', "ad.id_credito_detalle = cd.id");
        if(isset($parametro['id_acuerdo']))     { $this->db->where('ad.id_acuerdo', $parametro['id_acuerdo']);}

        $query = $this->db->get();  
        //var_dump($this->db->last_query()); die;
        return $query->result_array();
    }

    public function insert_promesa($data)
    {
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->insert( 'acuerdos_pago' ,$data);

        $query = $this->db->insert_id();  
        return $query;
    }
    public function update_promesa($param, $data)
    {
        $this->db = $this->load->database('gestion',TRUE);
        if(isset($param["id_cliente"])) {$this->db->where( 'id_cliente' ,$param["id_cliente"]);}
        if(isset($param["estado"]))     {$this->db->where( 'estado' ,$param["estado"]);}
        if(isset($param["id_acuerdo"]))     {$this->db->where( 'id' ,$param["id_acuerdo"]);}
        
        $this->db->update( 'acuerdos_pago' ,$data);
        $query = $this->db->affected_rows();
        //echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function update_promesa_detalle($param, $data)
    {
        $this->db = $this->load->database('gestion',TRUE);
        if(isset($param["id_detalle"]))     {$this->db->where( 'id' ,$param["id_detalle"]);}
        if(isset($param["id_cuota"]))     {$this->db->where( 'id' ,$param["id_credito_detalle"]);}
        
        $this->db->update( 'acuerdos_detalle' ,$data);
        $query = $this->db->affected_rows();
        //echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function insert_promesa_detalle($data)
    {
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->insert( 'acuerdos_detalle' ,$data);

        $query = $this->db->insert_id();  
        //var_dump($this->db->last_query()); die;
        return $query;
    }
    
    public function getCreditoById($id=0)
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('*');
        $this->db->from('creditos');
        $this->db->where('id',$id);

        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function get_ultima_cuota_paga($id_credito=0)
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('*');
        $this->db->from('credito_detalle');

        $this->db->where('id_credito',$id_credito);
        $this->db->where('monto_cobrado > 0');
        $this->db->order_by('id');
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->result();
    }

    public function getAcuerdosDePagoPorIdCliente($id_cliente)
    {
        
        $acuerdos_por_cliente = $this->db_gestion->select('*')
                                                ->from('acuerdos_pago')
                                                ->where('id_cliente',$id_cliente)
                                                ->get()
                                                ->result_array();
        // echo $this->db_gestion->last_query();die;   
        return $acuerdos_por_cliente;
    }

    public function get_template_mensajes($param){
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('*');
        $this->db->from('template_sms');
        
        if(isset($param['id']))         {$this->db->where('id',$param['id']);}
        if(isset($param['estado']))     {$this->db->where('estado',$param['estado']);}
        if(isset($param['where']))      {$this->db->where($param['where']);}

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_template_mail($param){
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('*');
        $this->db->from('template_mail');
        
        if(isset($param['id']))         {$this->db->where('id',$param['id']);}
        if(isset($param['estado']))     {$this->db->where('estado',$param['estado']);}
        if(isset($param['where']))      {$this->db->where($param['where']);}

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_planes_pago($param){
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('*');
        $this->db->from('planes_pago');
        if(isset($param['estado'])) 
        {
            $this->db->where('estado', $param['estado']);
        }
        if(isset($param['dias_mora'])) 
        {
            //$this->db->where('('.$param['dias_mora'].' BETWEEN antiguedad_desde and antiguedad_hasta) OR (antiguedad_desde <= '.$param['dias_mora'].' and antiguedad_hasta is NULL)');
            $this->db->where('(('.$param['dias_mora'].' BETWEEN antiguedad_desde and antiguedad_hasta) OR (antiguedad_desde <= '.$param['dias_mora'].'))');
        }
       
        $query = $this->db->get();
        //var_dump($this->db->last_query()); die;
        return $query->result_array();
    }

    public function get_plan_detalle($param)
    {
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('pp.descripcion plan, pc.descripcion cuota, pc.numero_cuota, pd.id, pd.porcentaje, pd.extension_dias');
        $this->db->from('planes_pago pp');
        $this->db->join('plan_detalle pd ', 'pp.id = pd.id_plan');
        $this->db->join('planes_cuotas pc ', 'pd.id_plan_cuota = pc.id');
        if(isset($param["estado"])) {   $this->db->where('pp.estado',$param["estado"]);}
        if(isset($param["id_plan"])) {  $this->db->where('pp.id', $param["id_plan"]);}
        $this->db->order_by('pd.extension_dias', 'ASC');
        $query = $this->db->get();      
        return $query->result_array();
    }

    public function get_pagos_cuota($params)
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('sum(monto) monto, max(fecha)');
        $this->db->from('pago_credito');

        if(isset($params['id_cuota']))  { $this->db->where('id_detalle_credito',trim($params['id_cuota'])); }
        if(isset($params['estado']))    { $this->db->where('estado',$params['estado']); }
        if(isset($params['fecha']))    { $this->db->where('fecha between "'.$params['fecha'].' 03:00:00" and "'.$params['fecha'].' 23:59:59"'); }
        if(isset($params['id']))    { $this->db->where('id', $params['id']); }
        if(isset($params['medio_pago']))    { $this->db->where('medio_pago in '.$params['medio_pago']); }
        if(isset($params['razonNot']))    { $this->db->where('estado_razon != "'.$params['razonNot'].'"'); }
        

        $query = $this->db->get();
        //var_dump($this->db->last_query()); die;
        return $query->result();
    }
    public function get_pagos_credito($params)
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('creditos.id id_credito, credito_detalle.id id_cuota, pago_credito.id id_pago, pago_credito.monto, pago_credito.fecha_pago');
        $this->db->from('pago_credito, creditos, credito_detalle');
        $this->db->where('creditos.id = credito_detalle.id_credito');
        $this->db->where('credito_detalle.id = pago_credito.id_detalle_credito');

        if(isset($params['id_cuota']))  { $this->db->where('pago_credito.id_detalle_credito',trim($params['id_cuota'])); }
        if(isset($params['id_pago']))    { $this->db->where('pago_credito.id',$params['id_pago']); }

        $query = $this->db->get();
        //var_dump($this->db->last_query()); die;
        return $query->result();
    }

    public function get_planes_descuento($param){
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('*');
        $this->db->from('planes_descuentos');

        if(isset($param['id_plan'])) {$this->db->where('id',$param['id_plan']);}
        if(isset($param['estado'])) {$this->db->where('estado',$param['estado']);}

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_pagos_cuota($params)
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('*');
        $this->db->from('pago_credito');

        if(isset($params['id_pago']))   { $this->db->where('id',trim($params['id_pago'])); }
        if(isset($params['id_cuota']))  { $this->db->where('id_detalle_credito',trim($params['id_cuota'])); }
        if(isset($params['estado']))    { $this->db->where('estado',$params['estado']); }
        if(isset($params['fecha']))    { $this->db->where('fecha between "'.$params['fecha'].' 03:00:00" and "'.$params['fecha'].' 23:59:59"'); }
        if(isset($params['medio_pago']))    { $this->db->where('medio_pago in '.$params['medio_pago']); }

        //$this->db->group_by('referencia_externa');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        //var_dump($this->db->last_query()); die;
        return $query->result();
    }

    function get_pagos($parametros){
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('id');
        $this->db->from('pago_credito as pago');
        
        if(isset($parametros['referencia_externa']))   { $this->db->where('pago.referencia_externa',trim($parametros['referencia_externa'])); }
        $query = $this->db->get();
        return $query->result();
    }
    
    function get_pagos_detalle($params){
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('pago.*, desglose.id desglose, desglose.id_pago_credito id_pago, desglose.id_credito_detalle cuota, desglose.monto monto_cuota, desglose.tipo');
        $this->db->from('pago_credito as pago');
        $this->db->join('desglose_pago as desglose', 'pago.id = desglose.id_pago_credito', 'left');
        
        if(isset($params['id_pago']))   { $this->db->where('pago.id',trim($params['id_pago'])); }
        if(isset($params['tipo']))   { $this->db->where('desglose.tipo',$params['tipo']); }
        if(isset($params['estado']))   { $this->db->where('pago.estado',$params['estado']); }
        if(isset($params['id_cuota']))  { 
            $this->db->group_start();
            $this->db->where('desglose.id_credito_detalle',trim($params['id_cuota'])); 
            $this->db->or_where('pago.id_detalle_credito',trim($params['id_cuota'])); 
            $this->db->group_end();
        }
        if(isset($params['id_cliente']))  { 
            $this->db->group_start();
            $this->db->where('desglose.id_credito_detalle  IN (SELECT id FROM `credito_detalle` WHERE id_credito IN (SELECT id FROM `creditos` WHERE `id_cliente` = '.trim($params['id_cliente']).')	)'); 
            $this->db->or_where('pago.id_detalle_credito  IN (SELECT id FROM `credito_detalle` WHERE id_credito IN (SELECT id FROM `creditos` WHERE `id_cliente` = '.trim($params['id_cliente']).')	)'); 
            $this->db->group_end();
        }
        if(isset($params['limit'])){ $this->db->limit($params['limit']);}


        //$this->db->group_by('referencia_externa');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        //var_dump($this->db->last_query()); die;
        return $query->result();
    }

    public function get_gestiones($params)
    {
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('count(*) cantidad');
        $this->db->from('track_gestion');

        if(isset($params['id_operador']))  { $this->db->where('id_operador',trim($params['id_operador'])); }
        if(isset($params['inicio']) && isset($params['fin']))    { $this->db->where('fecha BETWEEN "'.$params['inicio'].'" and "'.$params['fin'].'"'); }
        $this->db->group_by('id_solicitud');
        
        $query = $this->db->get();
       //var_dump($this->db->last_query());// die;
        return $query->result();
    }

    public function get_acuerdos($params)
    {
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('COUNT(id) AS cantidad_acuerdos, SUM(monto) AS suma_acuerdos');
        $this->db->from('acuerdos_pago');

        if(isset($params['id_operador']))  { $this->db->where('id_operador',trim($params['id_operador'])); }
        if(isset($params['inicio']) && isset($params['fin']))    { $this->db->where('fecha_hora BETWEEN "'.$params['inicio'].' 00:00:00.000000" and "'.$params['fin'].' 23:59:59.000000"'); }
        if(isset($params['estado']))  {$this->db->where('estado IN ('.$params['estado'].')'); }
        
        $query = $this->db->get();
       //var_dump($this->db->last_query());// die;
        return $query->result();
    }

    public function getMoraExcepcion($params)
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('*');
        $this->db->from('excepciones');

        if(isset($params['documento']))  { $this->db->where('documento = "'.$params['documento'].'"'); }

        $query = $this->db->get();
        return $this->db->affected_rows();
    }


    public function get_resumen_track_llamadas($params)
    {
        $this->db = $this->load->database('telefonia',TRUE);
        $this->db->select('count(*) cantidad_llamadas, skill_result skill_resultado, telephone_number');
        $this->db->from('track_llamadas');

        if(isset($params['id_cliente']))  { $this->db->where('id_cliente',$params['id_cliente']); }
        if(isset($params['telefono']))  { $this->db->where('SUBSTRING(telephone_number, - 10 ) = '.$params['telefono']); }
        
        $this->db->group_by('skill_result , telephone_number');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_skills_result()
    {
        $this->db = $this->load->database('telefonia',TRUE);
        $this->db->select('DISTINCT(skill_resultado)');
        $this->db->from('track_llamada');
        $query = $this->db->get();
        
        return $query->result();
    }
    
 
    public function get_track_detalle_llamadas($params)
    {
        $this->db = $this->load->database('telefonia',TRUE);
        $this->db->select('cdr.id_call, cdr.tipo_llamada, cdr.name_agent, cdr.fecha, cdr.descri_typing_code, cdr.descri_typing_code2, cdr.telephone_number, cdr.who_hangs_up, cdr.central,cdr.skill_result,cdr.talk_time,
        ac.id_cliente cliente, ac.audio_name, ac.path_audio, ac.id audio, cam.nombre, cam.descripcion');

        $this->db->from('track_llamadas cdr ');
        $this->db->join('gestion.relacion_audios_clientes ac', 'cdr.id_call = ac.id_call', 'left');
        $this->db->join('track_campanias cam', 'cdr.id_campania = cam.id_campania', 'left');

        if(isset($params['id_cliente']))  { $this->db->where('cdr.id_solicitud',$params['id_cliente']);}
        if(isset($params['telefono']))  { $this->db->where('SUBSTRING(cdr.telephone_number, -10) = "'.str_replace(' ','',$params['telefono'].'"'));}
        if(isset($params['fecha_inicio']) && isset($params['fecha_hasta']))  { $this->db->where(' cdr.fecha BETWEEN "'.$params['fecha_inicio'].'" and "'.$params['fecha_hasta'].'"');}

        $this->db->group_by('cdr.id_call');
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        return $query->result();
    }

    public function get_audio_by($param){
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->select('*');
        $this->db->from('relacion_audios_clientes');
        if(isset($param["id"])) {  $this->db->where('id',$param['id']); }
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        return $query->result();
    }

    public function get_credito_condicion_by($param){
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('*');
        $this->db->from('credito_condicion');
        if(isset($param["id_credito"])) {  $this->db->where('id_credito',$param['id_credito']); }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_campaña_descuento($param){
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('sum(monto_descuento) monto_descuento, max(valido_hasta) valido_hasta');
        $this->db->from('campania_descuento');
        if(isset($param["id_cliente"])) {  $this->db->where('id_cliente',$param['id_cliente']); }
        if(isset($param["fecha"])) {  $this->db->where('"'.$param['fecha'].'" BETWEEN valido_desde and valido_hasta'); }
        $query = $this->db->get();
        return $query->result();
    }


    /*
    Betza Fin
    */

    public function getCreditosByCliente($param){

        $sql = "SELECT cl.documento, 
        cr.id as id_credito, 
        cl.id as id_cliente, 
        cd.id as id_credito_detalle, 
        cd.numero_cuota as numero_cuota, 
        CONCAT(cl.nombres,' ',cl.apellidos) as nombre,  
        cr.fecha_otorgamiento, cd.monto_cuota as monto_cuota, 
        cr.plazo, cd.monto_cuota, cd.estado as estado_cuota,
        si.id as id_solicitud_imputacion,
        si.fecha_pago,
        si.referencia,
        si.monto_pago,
        si.banco_origen,
        si.banco_destino,
        si.comprobante,
        si.medio_pago FROM maestro.credito_detalle cd";

        $sql .= " join  maestro.creditos cr ";
        $sql .= " on cr.id = cd.id_credito ";
        $sql .= " join  maestro.clientes cl ";
        $sql .= " on cr.id_cliente = cl.id ";
        //$sql .= " left join  maestro.imputacion_credito ic ";
        //$sql .= " on cd.id = ic.id_creditos_detalle and cr.id = ic.id_credito ";
        $sql .= " left join maestro.solicitud_imputacion si on cl.id = si.id_cliente and si.por_procesar = 0 AND si.resultado IS NULL";
        $sql .= " WHERE (cd.estado = ? OR cd.estado is null OR cd.estado = '')";
        $sql .= ($param != "") ?  " AND (cl.documento LIKE ? OR cl.nombres LIKE ? OR cl.apellidos LIKE ?)" : "";
        if($param != ""){
            $values = [
                'mora',
                "%".$this->db->escape_like_str($param)."%",
                "%".$this->db->escape_like_str($param)."%",
                "%".$this->db->escape_like_str($param)."%"
            ];
        }else{
            $values = [
                'mora'
            ];
        }
        $result = $this->db->query($sql, $values);
        //var_dump($this->db->last_query);
        return $result->result();
        
    }

    
    public function get_list_pagos($param) {
        $this->db = $this->load->database('maestro',TRUE);
		$this->db->select('cr.*, pc.id as pago_credito_id, pc.medio_pago, pc.referencia_externa, pc.referencia_interna, pc.fecha_pago, pc.monto, pc.estado_razon');
		$this->db->select('(SELECT fecha_otorgamiento FROM creditos WHERE id = cr.id_credito) as fecha_otorgamiento');
        $this->db->from('credito_detalle as cr');
        $this->db->join('pago_credito as pc', 'cr.id = pc.id_detalle_credito', 'left');
        $this->db->where('id_credito  IN (SELECT `id` FROM `creditos` WHERE `id_cliente` = ' . $param['idcliente'] . ' )', NULL, FALSE);
        // $this->db->where('id_credito  IN (SELECT `id` FROM `creditos` WHERE `id_cliente` = ' . $param['idcliente'] . ' AND `creditos`.`estado` IN ("vigente","mora") )', NULL, FALSE);
        // $this->db->group_start()->where('estado IS  NULL', null, false)->or_group_start()->where('estado', 'mora')->group_end()->group_end();
        $this->db->order_by('fecha_vencimiento','ASC');

        $query = $this->db->get();
        return $query->result();

    }

    public function get_last_credito($param){
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('credito_detalle.*');
        $this->db->from('credito_detalle');
        $this->db->join('creditos','creditos.id = credito_detalle.id_credito');
        $this->db->where('creditos.id_cliente',$param['id_cliente']);
        $this->db->limit(1);
        $this->db->order_by('credito_detalle.fecha_vencimiento','DESC');

        $query = $this->db->get();
        return $query->result();
    }

    /***************************************************/
    /*** Actualiza los datos en solicitud imputación ***/
    /***************************************************/
    public function updateSolicitudImputacion($data, $id) {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->where('id', $id);
        $this->db->update('solicitud_imputacion', $data);
        $query = $this->db->affected_rows();
        //echo $sql = $this->db->last_query();die;
        return $query;
    }
    /**************************************************************/
    /*** Se obtienen las solicitudes de imputación por procesar ***/
    /**************************************************************/
    public function getSolicitudImputacion() {
        $this->db = $this->load->database('maestro',TRUE);

        $sql = "SELECT DATE_FORMAT(si.fecha_solicitud, '%d/%m/%Y') fecha_solicitud,
        DATE_FORMAT(si.fecha_proceso, '%d/%m/%Y') fecha_proceso,
        si.resultado,
        cl.documento,
        cr.id AS id_credito,
        cl.id AS id_cliente,
        cd.id AS id_credito_detalle,
        CONCAT(cl.nombres, ' ', cl.apellidos) AS nombre,
        DATE_FORMAT(
            cr.fecha_otorgamiento,
            '%d/%m/%Y'
        ) fecha_otorgamiento,
        cr.monto_prestado AS monto_credito,
        cr.plazo,
        cr.estado AS estado_credito,
        si.por_procesar,
        si.id AS id_solicitud_imputacion,
        si.fecha_pago,
        si.referencia,
        si.monto_pago,
        si.banco_origen,
        si.banco_destino,
        si.comprobante,
        si.medio_pago,
        
        op.nombre_apellido solicitante
        from solicitud_imputacion si LEFT JOIN gestion.operadores op
        ON
            si.id_operador_solicita = op.idoperador, clientes cl, creditos cr, credito_detalle cd  WHERE si.id_cliente = cl.id and cr.id_cliente = cl.id and cr.id = cd.id_credito  and 
        cd.id in(select max(credito_detalle.id) id from creditos, credito_detalle where credito_detalle.id_credito = creditos.id and creditos.id_cliente = cl.id) and 
        cr.estado IN('mora', 'vigente', 'cancelado') AND si.por_procesar = 0 AND si.resultado IS NULL";

        $result = $this->db->query($sql);
        return $result->result();
    }


    public function buscar_acuerdos_operador($param){
        $this->db = $this->load->database('gestion',TRUE);
        $sql = 'SELECT `solicitud`.`fecha_ultima_actividad` AS `ultima_actividad`, 
	    `creditos`.`id`, 
        `creditos`.`monto_prestado`, 
        `credito_detalle`.`fecha_vencimiento`, 
        `credito_detalle`.`monto_cobrar` AS `deuda`, 
        `credito_detalle`.`dias_atraso`, 
        IFNULL(
            credito_detalle.estado, "vigente"
        ) AS estado, 
        `clientes`.`documento`, 
        `clientes`.`nombres`, 
        `clientes`.`apellidos`, 
        IFNULL(last_track.observaciones, "") AS last_track,
        acuerdos_pago.fecha as fecha_acuerdo,
        acuerdos_pago.monto as monto_acuerdo
    FROM 
        `maestro`.`creditos` AS `creditos` 
        JOIN `maestro`.`credito_detalle` AS `credito_detalle` ON `credito_detalle`.`id_credito` = `creditos`.`id` 
        JOIN `solicitudes`.`solicitud` AS `solicitud` ON `solicitud`.`id_credito` = `creditos`.`id`
        LEFT JOIN `solicitudes`.`solicitud_ultima_gestion` `last_track` ON `last_track`.`id_solicitud` = `solicitud`.`id` 
        JOIN `maestro`.`clientes` AS `clientes` ON `clientes`.`id` = `creditos`.`id_cliente` 
        JOIN `gestion`.`acuerdos_pago` AS `acuerdos_pago` ON `acuerdos_pago`.`id_cliente` = `creditos`.`id_cliente` 
        
    WHERE 
        (`credito_detalle`.`estado` IS NULL OR `credito_detalle`.`estado` = "mora") 
        AND `acuerdos_pago`.`fecha` = '.$param['fecha'].' 
        AND `acuerdos_pago`.`id_operador` = '.$param['id_operador'].'
        AND `acuerdos_pago`.`estado` '.$param['estado'].'
        GROUP BY `credito_detalle`.`id` 
        ORDER BY `credito_detalle`.`fecha_vencimiento` ASC';
        $result = $this->db->query($sql);
        return $result->result();
    }

    public function get_cliente_ajustes($param){
        $this->db = $this->load->database('gestion',TRUE);
        $sql = "SELECT * FROM `track_gestion` WHERE 
                id_cliente = ".$param['id_cliente']." 
                AND `observaciones` LIKE '%Monto anterior:%' 
                AND fecha >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY ) 
            ORDER BY `id` DESC";
        $result = $this->db->query($sql);
        return $result->result();
    }

    public function get_enlace_audios_neotell($telefono)
    {
        // var_dump($telefono);die;
        $this->db_telefonia = $this->load->database('telefonia',TRUE);
        $this->db->select('TA.*,C.documento, A.fuente');
        $this->db->from('telefonia.track_audios_service TA,  solicitudes.solicitante_agenda_telefonica as A, maestro.clientes AS C');
        $this->db->where('A.numero = TA.numero_solicitud');
        $this->db->where('A.documento = C.documento');
        $this->db->where('TA.numero_solicitud', $telefono);
        $this->db->order_by('TA.id_track','ASC');

        // print_r($this->db->get_compiled_select());die;
        $query = $this->db->get();
        return $query->result_array();
        
    }
	
	public function getCreditoByCreditoDetalleId($creditoDetalleId)
	{
		$result = $this->db_maestro->select('*')
			->from('creditos c')
			->join('credito_detalle cd', 'c.id = cd.id_credito')
			->where('cd.id', $creditoDetalleId)
			->get()->result_array();
		
		return $result;
	}
	
	/**
	 * Guarda el registro de lo enviado para registrar un movimiento en payvalida
	 *
	 * @param $operation
	 * @param $creditoDetalleId
	 * @param $idCliente
	 * @param $orderId
	 * @param $monto
	 * @param $metodoDePago
	 * @param $expiracion
	 * @param $payload
	 *
	 * @return mixed
	 */
	public function savePayvalidaMovimientos($operation, $tipo, $idReferencia, $idCliente, $orderId, $monto, $metodoDePago, $expiracion, $payload)
	{
		$data = [
			'operation' => $operation,
			'tipo' => $tipo,
			'id_referencia' => $idReferencia,
			'id_cliente' => $idCliente,
			'order_id' => $orderId,
			'monto' => $monto,
			'metodo_de_pago' => $metodoDePago,
			'expiracion' => $expiracion,
			'payload' => $payload
		];
		
		$this->db_maestro->insert('payvalida_movimientos', $data);
		
		return $this->db_maestro->insert_id();
	}
	
	/**
	 * Guarda la respuesta del registro de payvalida de ser valida
	 * 
	 * @param $id
	 * @param $pvOrderId
	 * @param $referencia
	 * @param $checkout
	 *
	 * @return void
	 */
	public function savePayvalidaRegisterResponse($id, $pvOrderId, $referencia, $checkout)
	{
		$data = array(
			'pv_order_id' => $pvOrderId,
			'referencia' => $referencia,
			'checkout' => $checkout
		);
		
		$this->db_maestro->where('id', $id);
		$this->db_maestro->update('payvalida_movimientos', $data);
	}
	
	/**
	 * Guarda los errores en el registro de payvalida
	 * 
	 * @param $id
	 * @param $error
	 *
	 * @return void
	 */
	public function savePayvalidaRegisterError($id, $error)
	{
		$data = array(
			'error' => $error,
		);
		
		$this->db_maestro->where('id', $id);
		$this->db_maestro->update('payvalida_movimientos', $data);
	}
	
	/**
	 * Obtiene solo los metodos de pago de payvalida habilitados
	 * 
	 * @return void
	 */
	public function getPayvalidaEnabledPaymentMethods()
	{
		return $this->getPayvalidaPaymentMethods(true);
	}
	
	/**
	 * Obtiene todos los metodos de pago para payvalida. Habilitados o no
	 * 
	 * @return void
	 */
	public function getAllPayvalidaPaymentMethods()
	{
		return $this->getPayvalidaPaymentMethods(false);
	}
	
	/**
	 * Obtiene los metodos de pago para payvalida
	 * 
	 * @param $onlyEnableds
	 *
	 * @return mixed
	 */
	private function getPayvalidaPaymentMethods($onlyEnableds)
	{
		$this->db_maestro->select('*')
			->from('payvalida_metodo_pago');
		
		if($onlyEnableds) {
			$this->db_maestro->where('enabled', $onlyEnableds);
		}
		
		return $this->db_maestro->get()->result_array();
	}
	
	/**
	 * Obtiene el cliente atravez del idCreditoDetalle
	 * 
	 * @param $idCreditoDetalle
	 *
	 * @return mixed
	 */
	public function getClientByIdCreditoDetalle($idCreditoDetalle)
	{
		return $this->db_maestro->select('cli.*')
			->from('clientes cli')
			->join('creditos c', 'c.id_cliente = cli.id')
			->join('credito_detalle cd', 'cd.id_credito = c.id')
			->where('cd.id', $idCreditoDetalle)
			->get()->result_array();
	}
	
	/**
	 * Obtiene el acuerdo de pago por id_acuerdo_pago
	 * 
	 * @param $id
	 *
	 * @return array|array[]
	 */
	public function getPayvalidaAcuerdoPayment($id)
	{
		$result = $this->db->select('acuerdo.monto, acuerdo.id_cliente')
			->from('gestion.acuerdos_pago acuerdo')
			->where('id', $id)
			->where('estado', "pendiente")
			->order_by("id","ASC")
			->limit("1")
			->get()->result_array();
		
		return $result;
	}
	
	/**
	 * Obtiene EL total de la deuda usando el id_cliente
	 * 
	 * @param $id
	 *
	 * @return array|array[]
	 */
	public function getPayvalidaTotalPayment($id)
	{
		$result = $this->db->select('COUNT(c.id) "creditos", SUM(cd.monto_cobrar) deuda, MAX(cd.dias_atraso) dias_atraso, SUM(cd.numero_cuota) cuotas')
			->from('maestro.creditos c')
			->join('maestro.credito_detalle cd', 'c.id = cd.id_credito')
			->where('c.estado in ("vigente", "mora")')
			->where('c.id_cliente', $id)
			->group_by('c.id_cliente')
			->get()->result_array();

		return $result;
	}
	
	public function getCreditosDetalleVigMora($id_cliente)
	{
		$query = $this->db_maestro->select('*')
			->from('credito_detalle')
			->where('id_credito  IN (SELECT `id` FROM `creditos` WHERE `id_cliente` = ' . $id_cliente . ' AND `creditos`.`estado` IN ("vigente","mora") )', NULL, FALSE)
			->group_start()->where('estado IS  NULL', null, false)->or_group_start()->where('estado', 'mora')->group_end()->group_end()
			->order_by('fecha_vencimiento','ASC')
			->limit(1)
			->get();
//		echo $this->db_maestro->last_query();
		checkDbError($this->db_maestro);
		$result_db_maestro = ($query!==false) ? $query->result() : false;
		
		// $this->db->close();
		return $result_db_maestro;
	}
	
	
	/**
	 * Marca un credito de payvalida como procesado
	 * 
	 * @param $pv_po_id
	 *
	 * @return mixed
	 */
	public function markPayvalidaAsProcessed($pv_po_id)
	{
		$this->db_maestro->where('pv_po_id',$pv_po_id);
		$this->db_maestro->update('pay_valida_notifications', ['procesado' => 1]);
		
		return $this->db_maestro->affected_rows();
	}
	
	/**
	 * Obtiene los movimientos de payvalida por ID Referencia
	 *
	 * @param $idReferencia
	 * @param $medioDePago
	 *
	 * @return mixed
	 */
	public function getPayvalidaMovimientoByIdReferencia($idReferencia, $medioDePago)
	{
		$result = $this->db_maestro->select('*')
			->from('payvalida_movimientos')
			->where('id_referencia', $idReferencia)
			->where('metodo_de_pago', $medioDePago)
			->where('expiracion > CURDATE()')
			->where('error is NULL')
			->get()->result_array();
		
		return $result;
	}
	
	/**
	 * Obtiene la cuota de un acuerdo usando el ID Acuerdo
	 * 
	 * @param $idAcuerdo
	 *
	 * @return mixed
	 */
	public function getAcuerdoCuotaIdById($idAcuerdo)
	{
		$result = $this->db_maestro->select('cd.*')
			->from('gestion.acuerdos_detalle ad')
			->join('maestro.credito_detalle cd', 'cd.id = ad.id_credito_detalle')
			->where('ad.id_acuerdo', $idAcuerdo)
			->get()->result_array();
		
		return $result;
	}
    public function getCreditoInfo($id_credito, $id_cliente){
        
        // $result = $this->db_maestro->select("c.id, c.fecha_otorgamiento, c.monto_prestado, c.estado, cd.dias_atraso")
        // ->from("creditos as c")
        // ->join("credito_detalle AS cd", "cd.id_credito = c.id")
        // ->where("c.id", $id_credito)->get()->result_array();

        $this->db_maestro->select("c.id, c.fecha_otorgamiento, c.monto_prestado, c.estado, cd.dias_atraso");
        $this->db_maestro->from("creditos as c");
        $this->db_maestro->join("credito_detalle AS cd", "cd.id_credito = c.id");
        if ($id_cliente) {
            $this->db_maestro->where("c.id_cliente", $id_cliente);
        }else{
            $this->db_maestro->where("c.id", $id_credito);
        }
        $resutl = $this->db_maestro->get();
        return $resutl->result_array();
    }
}
?>

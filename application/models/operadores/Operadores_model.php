<?php
//defined('BASEPATH') or exit('No direct script access allowed');

class Operadores_model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->db_gestion = $this->load->database('gestion', TRUE);
        $this->db_chat = $this->load->database('chat', TRUE);
        $this->db_telefonia = $this->load->database('telefonia', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_parametria = $this->load->database('parametria', TRUE);
        
    }

    public function get_lista_operadores_by($filtro)
    {       
        $this->db_gestion->select('*');
        $this->db_gestion->from('operadores op');
        $this->db_gestion->join('tipo_operador top', 'op.tipo_operador = top.idtipo_operador');
        
        if(isset($filtro['estado']))   { $this->db_gestion->where('estado', $filtro['estado']);}
        if(isset($filtro['equipo']) && !is_null($filtro['equipo'])  && isset($filtro['tipo_operador'])){
            if($filtro['tipo_operador'] == 2) {
                if($filtro['equipo'] != 'GENERAL'){$this->db_gestion->where('equipo', $filtro['equipo']);}
                $this->db_gestion->where('(tipo_operador = 1 or tipo_operador = 4)');   
            }
            if($filtro['tipo_operador'] == 13) {
                if($filtro['equipo'] != 'GENERAL'){$this->db_gestion->where('equipo', $filtro['equipo']);}  
                $this->db_gestion->where('(tipo_operador = 5 or tipo_operador = 6)');   
            }
        }
        
        if(isset($filtro['idoperador'])){$this->db_gestion->where('idoperador', $filtro['idoperador']);}
        if(isset($filtro[0]['columna'])) 
        {
            foreach ($filtro as $key => $value) :
                if (isset($value['or']) && $value['or'])    { $this->db_gestion->or_where($value['columna'], $value['valor']);} 
                if (isset($value['or']) && !$value['or'])   { $this->db_gestion->where($value['columna'], $value['valor']);}
                if (isset($value['not_in']))                { $this->db_gestion->where_not_in('op.idoperador', $value['not_in']);}
            endforeach;
        }
        
		if(isset($filtro['tipo_operador_where'])){//nuevo filtro de busqueda
			$this->db_gestion->where(['tipo_operador' => $filtro['tipo_operador_where']]);   
        }
        
        
        $query = $this->db_gestion->get();
        
        return $query->result_array();
    }

    public function get_operadores_by($filtro)
    {
        
        $this->db_gestion->select('*');
        $this->db_gestion->from('operadores op');
        $this->db_gestion->join('tipo_operador','op.tipo_operador = tipo_operador.idtipo_operador');
        if(isset($filtro["id_operador_buscar"])) { $this->db_gestion->where('op.idoperador ='.$filtro["id_operador_buscar"]); }
        if(isset($filtro['estado']))   { $this->db_gestion->where('estado', $filtro['estado']);}
        if(isset($filtro['tipo_operadores'])){
            $this->db_gestion->where_in('tipo_operador', $filtro['tipo_operadores']);
        }
        if(isset($filtro['idoperador'])){$this->db_gestion->where_in('idoperador', $filtro['idoperador']);}
        if(isset($filtro['where'])){
            $this->db_gestion->where($filtro["where"]);
        }
            
        $query = $this->db_gestion->get();
        return $query->result();
    }

    public function track_interno($date){
        $this->db_gestion->insert('track_gestiones_internas' ,$date);  
        $this->db_gestion->insert_id();
        $query = $this->db_gestion->affected_rows();        
        return $query;
    }

    public function get_lista_operador_central(){
        $this->db_gestion->select('idoperador, nombre_apellido, tipo_operador');
        $this->db_gestion->from('operadores');

        $query = $this->db_gestion->get();
        // echo $sql = $this->db->last_query();die;
        return $query->result_array();
    }
    public function get_operador_skill($id_skill){
        $this->db_telefonia->select('tos.id, tos.id_skill, tos.id_operador,tos.fecha, tos.user_action, op.nombre_apellido, op2.nombre_apellido as user_modification');
        $this->db_telefonia->from('track_operadores_skill tos');
        $this->db_telefonia->join($this->db_gestion->database.'.operadores op', 'tos.id_operador = op.idoperador');
        $this->db_telefonia->join($this->db_gestion->database.'.operadores op2', 'tos.user_action = op2.id_usuario');
        $this->db_telefonia->where('id_skill',$id_skill);
        $query = $this->db_telefonia->get();
        // var_dump($this->db_telefonia->last_query());die;
        return $query->result_array();
    }

    public function get_operador_skill_disponible($id_skill){
        $query= $this->db_gestion->query("(SELECT idoperador, nombre_apellido FROM operadores where not exists (SELECT * from ".$this->db_telefonia->database.".track_operadores_skill where idoperador = id_operador and id_skill ='$id_skill'))");
        // var_dump($this->db->last_query());die;
        return $query->result_array();
    }
    public function get_skill_agentes($id_operador,$id_skill)
    {
        $this->db_telefonia->select('id_operador');
        $this->db_telefonia->from('track_operadores_skill');
        $this->db_telefonia->where_not_in('id_operador',$id_operador);
        $this->db_telefonia->where('id_skill',$id_skill);

        $query = $this->db_telefonia->get();
        // var_dump($this->db_telefonia->last_query());die;

        return $query->result_array();
    }
 
    public function delete_skills($asignados,$id_skill,$id_operador)
    {
        $asignados_str= array();
        foreach ($asignados as $value){
            array_push($asignados_str,$value["id_operador"]);
        }
        foreach ($id_operador as $value2){
            array_push($asignados_str,$value2);
        }

        $this->db_telefonia->where_in('id_operador',$asignados_str);
        $this->db_telefonia->where('id_skill',$id_skill);

        $query= $this->db_telefonia->delete('track_operadores_skill');
        // var_dump($this->db_telefonia->last_query());die;

        return $query;
    }

    public function asignar_skill($data)
    {
        $this->db_telefonia->insert( 'track_operadores_skill' ,$data);  
        return $this->db_telefonia->insert_id();
    }

    public function get_lista_skill_central(){
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_skills');
        $query = $this->db_telefonia->get();
        // var_dump($this->db_telefonia->last_query());die;
        return $query->result_array();

    }

    public function validacionAgente($id_operador,$central){
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_operadores');
        $this->db_telefonia->where('id_operador',$id_operador);
        $this->db_telefonia->where('central',$central);

        $query = $this->db_telefonia->get();
        // var_dump($this->db_telefonia->last_query());die;
        return $query->num_rows();

    }

    public function tableAgentes(){
        $this->db_gestion->select('op.idoperador, op.nombre_apellido, tt.id, tt.id_agente,tt.id_skill,tt.central,tt.estado_agente');
        $this->db_gestion->from('operadores op');
        $this->db_gestion->join($this->db_telefonia->database.'.track_operadores tt', 'op.idoperador = tt.id_operador');
        $query = $this->db_gestion->get();
        // echo $sql = $this->db->last_query();die;
        return $query->result();

    }

    public function validacionCampania($id_campania){
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_campanias');
        $this->db_telefonia->where('id_campania',$id_campania);

        $query = $this->db_telefonia->get();
        // var_dump($this->db_telefonia->last_query());die;
        return $query->num_rows();

    }

    public function insertar_campania($data)
    {
        $this->db_telefonia->insert('track_campanias' ,$data);  
        $this->db_telefonia->insert_id();
        $query = $this->db_telefonia->affected_rows();        
        return $query;
    }

    public function insertar_skill($data)
    {
        $this->db_telefonia->insert('track_skills' ,$data);  
        $this->db_telefonia->insert_id();
        $query = $this->db_telefonia->affected_rows();        
        return $query;
    }

    public function validacionSkill($id_skill){
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_skills');
        $this->db_telefonia->where('id_skill',$id_skill);

        $query = $this->db_telefonia->get();
        // var_dump($this->db_telefonia->last_query());die;
        return $query->num_rows();

    }

    public function cambioEstadoCampania($data) {
        $query = $this->db_telefonia->update('track_campanias', $data, ['id' => $data['id']]);
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function cambioEstadoSkill($data) {
        $query = $this->db_telefonia->update('track_skills', $data, ['id' => $data['id']]);
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function get_campania_update($id)
    {
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_campanias');

        if (isset($id)){
            $this->db_telefonia->where('id', $id); 
        }
        $query = $this->db_telefonia->get();
        // var_dump($query);die;
        // echo $sql = $this->db->last_query();die;
        return $query->result_array();
    }

    public function updatedoCampania($data){
        $query = $this->db_telefonia->update('track_campanias', $data, ['id' => $data['id']]);
        // echo $sql = $this->db_telefonia->last_query();die;
        return $query;
    }

    public function tableCreateCampania(){
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_campanias');
        $query = $this->db_telefonia->get();
        // echo $sql = $this->db->last_query();die;
        return $query->result();

    }

    public function tableSkill(){
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_skills');
        $query = $this->db_telefonia->get();
        // echo $sql = $this->db->last_query();die;
        return $query->result();

    }

    public function get_ausencias_operador($param)
    {
        $this->db_gestion->select('*');
        $this->db_gestion->from('ausencias_operadores');
        if (isset($param["id_operador"]))   { $this->db_gestion->where('idoperador', $param["id_operador"]); }
        if (isset($param["estado"]))   { $this->db_gestion->where('estado', $param["estado"]); }
        if (isset($param["entre_fecha"]))   { $this->db_gestion->where('"'.$param["entre_fecha"].'" BETWEEN SUBSTRING(fecha_inicio, 1, 10) and SUBSTRING(fecha_final, 1, 10)'); }

        $this->db_gestion->order_by("fecha_inicio DESC");
        $query = $this->db_gestion->get();
        // echo $sql = $this->db->last_query();die;
        return $query->result();
    }

    public function tableHorariosOperadores()
    {
        $this->db_gestion->select('ho.id, ho.id_operador, ho.dias_trabajo,ho.hora_entrada, ho.estado_horario,ho.hora_salida,ho.fecha_modificacion,op.nombre_apellido, op2.nombre_apellido as nombre_usuario');
        $this->db_gestion->from('horario_operador ho');
        $this->db_gestion->join('operadores op', 'op.idoperador = ho.id_operador');
        $this->db_gestion->join('operadores op2', 'ho.id_usuario_modificacion = op2.id_usuario');
        $query = $this->db_gestion->get();
        // var_dump($this->db->last_query());die;
        return $query->result();
    }

    public function cambioEstadoHorario($data) {
        $query = $this->db_gestion->update('horario_operador', $data, ['id' => $data['id']]);
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function updatedoHorario($data){
        $query = $this->db_gestion->update('horario_operador', $data, ['id' => $data['id']]);
        $query = $this->db_gestion->affected_rows();   
        return $query;
    }

    public function get_horario_operador($id_operador)
    {
        $this->db_gestion->select('ho.id, ho.id_operador, ho.dias_trabajo,ho.hora_entrada, ho.hora_salida,ho.fecha_modificacion, ho.estado_horario,top.descripcion,op.nombre_apellido');
        $this->db_gestion->from('horario_operador ho');
        $this->db_gestion->join('tipo_operador top', 'ho.id_operador = top.idtipo_operador');
        $this->db_gestion->join('operadores op', 'ho.id_usuario_modificacion = op.id_usuario');

        if (isset($id_operador)){
            $this->db_gestion->where('id_operador', $id_operador); 
            $this->db_gestion->where('estado_horario = 1');         
        }
        $query = $this->db_gestion->get();
        // var_dump($query);die;
        // echo $sql = $this->db->last_query();die;
        return $query->result();
    }


    public function get_horario_operador_update($id_horario)
    {
        $this->db_gestion->select('ho.id, ho.id_operador, ho.dias_trabajo,ho.hora_entrada, ho.hora_salida,ho.fecha_modificacion, ho.estado_horario,top.descripcion,op.nombre_apellido');
        $this->db_gestion->from('horario_operador ho');
        $this->db_gestion->join('tipo_operador top', 'ho.id_operador = top.idtipo_operador');
        $this->db_gestion->join('operadores op', 'ho.id_usuario_modificacion = op.id_usuario');

        if (isset($id_horario)){
            $this->db_gestion->where('id', $id_horario); 
        }
        $query = $this->db_gestion->get();
        // var_dump($query);die;
        // echo $sql = $this->db->last_query();die;
        return $query->result();
    }

    public function insertar_ausencia($data)
    {
        $this->db_gestion->insert( 'ausencias_operadores' ,$data);  
        return $query = $this->db_gestion->insert_id();
        
        //$query = $this->db->affected_rows();        
        //return $query;
    }

    public function insertar_horario_operadores($data){
        $this->db_gestion->insert( 'horario_operador' ,$data);  
        //$this->db->insert_id();
        $query = $this->db_gestion->insert_id();        
        return $query;

    }

    public function insertar_agente($data){
        $this->db_telefonia->insert( 'track_operadores' ,$data);  
        $this->db_telefonia->insert_id();
        $query = $this->db_telefonia->affected_rows();        
        return $query;

    }

    public function cambioEstadoAgente($data) {
        $query = $this->db_telefonia->update('track_operadores', $data, ['id' => $data['id']]);
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function get_agente_update($id)
    {
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_operadores');

        if (isset($id)){
            $this->db_telefonia->where('id', $id); 
        }
        $query = $this->db_telefonia->get();
        // var_dump($query);die;
        // echo $sql = $this->db->last_query();die;
        return $query->result_array();
    }

    public function get_skill_update($id)
    {
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_skills');

        if (isset($id)){
            $this->db_telefonia->where('id', $id); 
        }
        $query = $this->db_telefonia->get();
  
        return $query->result_array();
    }

    public function updatedoAgente($data){
        $query = $this->db_telefonia->update('track_operadores', $data, ['id' => $data['id']]);
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function updatedoSkill($data){
        $query = $this->db_telefonia->update('track_skills', $data, ['id' => $data['id']]);
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function actualizar_ausencia($data)
    {
        $query = $this->db_gestion->update( 'ausencias_operadores' ,$data,['id' => $data['id']]);
        $query = $this->db_gestion->affected_rows();   
        return $query;
    }

    public function get_lista_operadores_ausentes()
    {
        $this->db_gestion->select('idoperador, MAX(fecha_inicio) AS inicio, MAX(fecha_final) AS final');
        $this->db_gestion->from('ausencias_operadores');
        $this->db_gestion->where('fecha_inicio <= CURDATE() AND fecha_final >= CURDATE() GROUP BY idoperador');

        $query = $this->db_gestion->get();
        //echo $sql = $this->db->last_query();die;
        return $query->result();
    }



    public function get_operador_by($filtro)
    {
        $this->db_gestion->select('*');
        $this->db_gestion->from('operadores op');
        $this->db_gestion->join('tipo_operador top', 'op.tipo_operador = top.idtipo_operador');
        //$this->db_gestion->join('relacion_operador_solicitud ros','op.idoperador = ros.id_operador');
        
        if (isset($filtro[0]['columna'])) 
        {
            foreach ($filtro as $key => $value) :
                if ($value['or']) 
                {
                    $this->db_gestion->or_where($value['columna'], $value['valor']);
                } else
                {
                    $this->db_gestion->where($value['columna'], $value['valor']);
                }
            endforeach;
        }

        $query = $this->db_gestion->get();
        //var_dump($query->result_array());die;
        //echo $sql = $this->db->last_query();echo "<br>";die;
        return $query->result();
    }

    public function  actualizar_operador($data)
	{         
        $query = $this->db_gestion->update( 'operadores' ,$data,['idoperador' => $data['idoperador']]);  
        return $query;
    }

    public function  actualizar_operador_solicitudes_obligatorias($operadores,$filtro)
	{          
        $this->db_gestion->where('gestion_obligatoria','1');
        if($filtro['equipo'] != 'GENERAL')
        {
            $this->db_gestion->where('equipo', $filtro['equipo']);
        }
		$this->db_gestion->where('tipo_operador', $filtro['tipo_operador']);
        $query = $this->db_gestion->update( 'operadores' , ['gestion_obligatoria' => '0']); 

        if ($operadores['operadores'] != []) {
            $this->db_gestion->where('tipo_operador', $filtro['tipo_operador']);
            
            $this->db_gestion->where_in('idoperador',$operadores['operadores']);
            $query = $this->db_gestion->update( 'operadores' , ['gestion_obligatoria' => '1']); 
            
        } 
            
        return $query;
        
    }

    public function  get_operadores_gestion_obligatoria($filtro)
	{          
        $this->db_gestion->select('idoperador, nombre_apellido, equipo');
        $this->db_gestion->from('operadores');
        $this->db_gestion->where('gestion_obligatoria','1');
        if(isset($filtro['idoperador']))
        {
            $this->db_gestion->where('idoperador', $filtro['idoperador']);
        }
        $query = $this->db_gestion->get();

        if ($this->db_gestion->affected_rows() >0 ) {
            return true;
        } else {
            return false;
        }
            
       
        
    }
    
    public function registrar_operador($data)
    {
        $this->db_gestion->insert( 'operadores' ,$data);  
        return $this->db_gestion->insert_id();
    }

    public function get_tipos_operador()
    {
        $query = $this->db_gestion->get('tipo_operador');
        return $query->result_array();
    }
	
	/**
	 * Obtiene el tipo de operador por Id
	 * 
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getTipoOperadorById($id)
	{
		$query = $this->db_gestion->select('*')
			->from('tipo_operador')
			->where('idtipo_operador', $id)->get();
		return $query->result_array();
	}

    public function create_tipo_operador($data)
    {
        $this->db_gestion->insert('tipo_operador', $data);
        return $this->db_gestion->insert_id();
    }

    /*
        solicitudes de un operador especifico 

    */ 
    public function get_asignaciones_operador($param)
    {
        $this->db_gestion->select('op.idoperador, op.nombre_apellido, ros.id_solicitud, ros.fecha_registro, ss.telefono telefono_cliente, ss.id, ss.estado');
        $this->db_gestion->from('operadores op');
        $this->db_gestion->join('relacion_operador_solicitud ros','op.idoperador = ros.id_operador');
        $this->db_gestion->join($this->db_solicitudes->database.'.solicitud ss','ros.id_solicitud = ss.id');
        if(isset($param["where"]))          {   $this->db_gestion->where($param["where"]);                   }
        if(isset($param["estado"]))         {   $this->db_gestion->where('ros.estado', $param["estado"]);    }
        if(isset($param["operador"]))       {   $this->db_gestion->where('ros.id_operador',$param["operador"]);       }
        if(isset($param["id_solicitud"]))   {   $this->db_gestion->where('ss.id',$param["id_solicitud"]);       }
        if(isset($param["inicio"]))         {   $this->db_gestion->where('ros.fecha_registro >= "'.$param["inicio"].' 00:00:00" AND ros.fecha_registro <="' .$param["fin"].' 23:59:59"'); }
    
        $query = $this->db_gestion->get();
        //var_dump($this->db->last_query());die;
        return $query->result_array();
    }

   

    //reasigna las solicitudes en la tabla relacion_operador_solicitud
    public function asignar_relacion_operador_solicitud($solicitud, $designado, $receptor)
    {
        $hoy = date("Y-m-d H:i:s");
        //editamos el registro actual
        $parametros = Array(
            'estado' => "H"
        );
        //$this->db->where('id_operador = '. $designado);
        $this->db_gestion->where('id_solicitud = '. $solicitud);
        $this->db_gestion->update('relacion_operador_solicitud', $parametros); 
        //echo $sql = $this->db_gestion->last_query();die;
        //insertamos la nueva asignacion
        $parametros = Array(
            'id_operador' => $receptor,
            'estado' => "A",
            'id_solicitud' => $solicitud,
            'tipo_asignacion' => 2,
            'fecha_registro' => $hoy
        );

        $this->db_gestion->insert('relacion_operador_solicitud', $parametros);
        $this->db_gestion->insert_id();

        $query = $this->db_gestion->affected_rows();        
        return $query;
    }

    public function asignar_solicitudes_a_operador($data, $filtro)
    {
        $query = 0;
        $hoy = date("Y-m-d H:i:s");

        $this->db_gestion->set('id_operador', $data, FALSE);
        $this->db_gestion->set('fecha_registro', $hoy, FALSE);
        $this->db_gestion->where($filtro[0]["columna"], $filtro[0]['valor']);
        $this->db_gestion->update('relacion_operador_solicitud');
        $query = $this->db_gestion->affected_rows();

        //si no se pudo actualizar el registro se crea uno nuevo
        if($query < 1){
            $data =[
                "id_operador" => $data,
                "id_solicitud" => $filtro[0]["valor"],
                "id_solicitud" => $filtro[0]["valor"],
                "fecha_registro" => $hoy
            ];
            $this->db_gestion->insert( 'relacion_operador_solicitud' ,$data);
            $this->db_gestion->insert_id();  
            $query += 1;
        }
        
        return $query;
    }

    //busca las solicitudes en la tabla  relacion_operador_solicitud
    public function getSolicitudesBy($params)
    {
        $this->db_gestion->select('*');
        $this->db_gestion->from("relacion_operador_solicitud");
    
        if(isset($params['estado']))        {   $this->db_gestion->where('estado', $params['estado']);}
        if(isset($params['id_solicitud']))  {   $this->db_gestion->where('id_solicitud', $params['id_solicitud']);}
        if(isset($params['id_operador']))   {   $this->db_gestion->where('id_operador', $params['id_operador']);}

        $query = $this->db_gestion->get();            
        return $query->result_array();
    }

    public function get_control_asignaciones($fecha, $operador)
    {
        $query = $this->db_gestion->get_where(
            'control_asignaciones',
            ['id_operador' => $operador,'fecha_control' => $fecha]
        );
        //echo $sql = $this->db_gestion->last_query();
        //var_dump($query->result());die;
        return $query->result();
    }

    public function get_cant_asignaciones_by_day($operador, $inicio, $fin)
    {
        $this->db_gestion->select('op.idoperador, COUNT(ros.id_solicitud) AS cantidad, SUBSTRING(`ros`.`fecha_registro`,1,10) fecha');
        $this->db_gestion->from('operadores op');
        $this->db_gestion->join('relacion_operador_solicitud ros','op.idoperador = ros.id_operador');
        $this->db_gestion->join($this->db_telefonia->database.'.solicitud ss','ros.id_solicitud = ss.id');
        $this->db_gestion->where('op.estado = 1');
        $this->db_gestion->where('(ss.estado NOT IN ("TRANSFIRIENDO", "PAGADO", "RECHAZADO", "APROBADO") OR ss.estado is null)');
        $this->db_gestion->where('ros.estado = "A"');
        $this->db_gestion->where('ros.id_operador = "'.$operador.'"');
        $this->db_gestion->where('ros.fecha_registro >= "'.$inicio.' 00:00:00" AND ros.fecha_registro <="' .$fin.' 23:59:59"');
        $this->db_gestion->group_by('SUBSTRING(`ros`.`fecha_registro`,1,10)');
        $query = $this->db_gestion->get();
        return $query->result_array();
    }

    public function get_ids_chats_asignados($data, $parametros)
    {
        $this->db_chat->select('id');
        $this->db_chat->from('new_chats ch');
        
        if (isset($parametros["inicio"]) && isset($parametros["fin"])) { $this->db_chat->where('ch.fecha_ultima_recepcion >= "'.$parametros["inicio"].' 00:00:00" and ch.fecha_ultima_recepcion<="' .$parametros["fin"].' 23:59:59"');}
        $this->db_chat->where("ch.id_operador", $data);
        $this->db_chat->where('ch.status_chat IN ("activo", "pendiente", "revision")');
        
        $query = $this->db_chat->get();

        return $query->result_array();
    }

    public function findBySenderNumber(string $number)
    {
        $sql   = 'select chats.* from '.$this->db_chat->database.'.new_chats as chats' .
            ' where chats.from like "' . $number . '" AND status_chat NOT LIKE "vencido"';
        $query = $this->db_chat->query($sql);
        return $query->result();
    }

    public function set_chat_operador($id, $operador)
    {
        $this->db_chat->set('id_operador', $operador, FALSE);
        $this->db_chat->where('id = '. $id);
        $this->db_chat->update('new_chats'); 

        $query = $this->db_chat->affected_rows();
        return $query;
    }
    //metodo que crea la relacion entre operador y chat
    public function set_chat_relacion_operador($data)
    {
        $this->db_chat->insert( 'relacion_operador_chat' ,$data);
        $this->db_chat->insert_id();  
        $query = $this->db_chat->affected_rows();
        
        return $query;
    }

    public function get_llamadas_realizadas($param){
        $this->db_telefonia->select('llamadas.id_cbps_cdr');
        $this->db_telefonia->from('track_llamadas as llamadas');
        $this->db_telefonia->from('track_operadores as operadores');
        $this->db_telefonia->where('llamadas.id_agente = operadores.id_agente');
        $this->db_telefonia->where('operadores.id_operador', $param['id_operador']);
        $this->db_telefonia->where('talk_time > CAST("00:00:30" AS time)');
        if(isset($param['mes']))    {   
            $this->db_telefonia->where('MONTH(llamadas.fecha) = MONTH(CURRENT_DATE())');
            $this->db_telefonia->where('YEAR(llamadas.fecha)  = YEAR(CURRENT_DATE())');
        }
        if(isset($param['ayer']))    {
            $this->db_telefonia->where('DAY(llamadas.fecha) = ' .$param["ayer"]);   
            $this->db_telefonia->where('MONTH(llamadas.fecha) = MONTH(CURRENT_DATE())');
            $this->db_telefonia->where('YEAR(llamadas.fecha)  = YEAR(CURRENT_DATE())');
        }
        

        $query = $this->db_telefonia->get();
        //var_dump($this->db_telefonia->last_query());
        return $query->result_array();
    }

    public function reasignar_new_chat($data, $parametros)
    {
        $this->db_chat->set('id_operador', $data, FALSE);

        $this->db_chat->where('status_chat IN ("activo", "pendiente", "revision")');
        if (isset($parametros["inicio"]) && isset($parametros["fin"]))  { $this->db_chat->where('fecha_ultima_recepcion >= "'.$parametros["inicio"].' 00:00:00" and fecha_ultima_recepcion<="' .$parametros["fin"].' 23:59:59"');}
        if (isset($parametros["designado"]))                            { $this->db_chat->where("id_operador", $parametros["designado"]); }
        if (isset($parametros["id"]))                            { $this->db_chat->where("id", $parametros["id"]); }
        
        $this->db_chat->update('new_chats'); 
        $query = $this->db_chat->affected_rows();
        return $query;
    }

    public function reasignar_chat($data, $parametros)
    {
        $this->db_chat->set('id_operador', $data, FALSE);
        $this->db_chat->where('status_chat IN ("activo")');        
        $this->db_chat->where('id_operador',$parametros["operador"]);        
        $this->db_chat->update('new_chats'); 

        $query = $this->db_chat->affected_rows();
        return $query;
    }

    public function get_chat_active($id_agente,$telf){

        $this->db_telefonia->select('id_operador');
        $this->db_telefonia->from('track_operadores');
        $this->db_telefonia->where('id_agente',$id_agente);
        $this->db_telefonia->where('estado_agente',1);
        $query = $this->db_telefonia->get();
        //var_dump($this->db_chat->last_query());
        $rs_operador = $query->result_array(); 
        //var_dump($rs_operador);die;
        
        if ($rs_operador[0]['id_operador']!="") {


            $this->db_chat->select('id');
            $this->db_chat->from('new_chats');
            $this->db_chat->where('id_operador','192');//chat_bot
            $this->db_chat->where('from',$telf);
            $this->db_chat->where('to','15140334');//solventa gestion
            //$this->db_chat->where('status_chat',"activo");
            $this->db_chat->order_by('id', 'desc');
            
            
            $query = $this->db_chat->get();
            //var_dump($this->db_chat->last_query());
            $rs_chat_id = $query->result_array();
            //var_dump($rs_chat_id);die;
            if ($rs_chat_id[0]['id']!="") {
                $this->db_chat->set('id_operador', $rs_operador[0]['id_operador']);
                $this->db_chat->where('id', $rs_chat_id[0]['id']);
                $this->db_chat->update('new_chats'); 

                $query = $this->db_chat->affected_rows();
                return $query;
                //var_dump($query);die;

             } 

        }
        
    }

    public function update_agent_solicitud($id_solicitud,$id_agente){

        $this->db_telefonia->select('id_operador');
        $this->db_telefonia->from('track_operadores');
        $this->db_telefonia->where('id_agente',$id_agente);
        $this->db_telefonia->where('estado_agente',1);
        $query = $this->db_telefonia->get();
        //var_dump($this->db_chat->last_query());
        $rs_operador = $query->result_array(); 
        //var_dump($rs_operador);die;
        
        if ($rs_operador[0]['id_operador']!="") {

            
            
                $this->db_solicitudes->set('operador_asignado', $rs_operador[0]['id_operador']);
                $this->db_solicitudes->where('id', $id_solicitud);
                $this->db_solicitudes->update('solicitud'); 

                $query = $this->db_chat->affected_rows();
                return $query;
                //var_dump($query);die;

             

        }
    }

    public function update_flag_solicitud($id_solicitud){

                $this->db_solicitudes->set('contactado', 0);
                $this->db_solicitudes->where('id', $id_solicitud);
                $this->db_solicitudes->update('solicitud'); 

                $query = $this->db_chat->affected_rows();
                return $query;

    }
    public function search_operador_proveedor($idoperador, $servicio)
    {
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_operadores');
        $this->db_telefonia->where('id_operador',$idoperador);
        $this->db_telefonia->where('central',$servicio);
        $this->db_telefonia->where('estado_agente',1);
        $query = $this->db_telefonia->get();
        //var_dump($this->db_telefonia->last_query());
        $rs_operador = $query->result_array(); 
        return $rs_operador;
    }
	
	
	/**
	 * Obtiene los operadores de la campania segun los tipos seteados en la misma
	 * 
	 * @param $campaniaId
	 *
	 * @return array
	 */
	public function getOperadoresPorCampania($campaniaId)
	{
		$subquery = $this->db_gestion->select('operadores')
			->from($this->db_parametria->database.'.campanias_manuales')
			->where('id', $campaniaId)
			->get()->result_array()[0];
		
		$tipoOperadores = $subquery['operadores'];
		
		$this->db_gestion->select('*')
			->from('operadores')
			->where('estado', 1)
			->where("tipo_operador in (" . $tipoOperadores . ")");
		
		$query = $this->db_gestion->get();
		
		$result = $query->result_array();

		return $result;
	}
	
	
	/**
	 * Obtiene los operadores de la campania segun el tipo y el equipo seteado en la misma
	 * 
	 * @param $campaniaId
	 * @param $equipo
	 *
	 * @return array
	 */
	public function getOperadoresPorCampaniaYEquipos($campaniaId, $equipo)
	{
		$subquery = $this->db_gestion->select('operadores')
						->from($this->db_parametria->database.'.campanias_manuales')
						->where('id', $campaniaId)
						->get()->result_array()[0];
		
		$tipoOperadores = $subquery['operadores'];
		
		$this->db_gestion->select('*')
			->from('operadores')
			->where('estado', 1)
			->where('equipo', $equipo)
			->where("tipo_operador in (" . $tipoOperadores . ")");
		
		$query = $this->db_gestion->get();
		$result = $query->result_array();
		
		return $result;
	}
	
	
	/**
	 * Obtiene los operadores por tipo y equipo
	 * 
	 * @param $tipoOperadoresIds
	 * @param $equipo
	 *
	 * @return mixed
	 */
	public function getOperadoresPorTipoYEquipo($tipoOperadoresIds, $equipo)
	{
		$query = $this->db_gestion->select('*')
			->from('operadores')
			->where('estado', 1)
			->where_in("tipo_operador", $tipoOperadoresIds);
		
		if ($equipo != "TODOS") {
			$query->where('equipo', $equipo);
		}
		
		$query = $this->db_gestion->get();

		$result = $query->result_array();
		
		return $result;
	}
	
	
	/**
	 * Obtiene la configuracion de tiemop establecida en la campaña 
	 * 
	 * @param $idCredito
	 * @param $idOperador
	 *
	 * @return array
	 */
	public function getConfiguracionTiemposCampania($idCredito, $idOperador)
	{
		$subQuery = $this->db->select('id_campania')
			->from('relacion_casos_operador_manual')
			->where('id_credito', $idCredito)
			->where('id_operador', $idOperador)
			->get_compiled_select();
		
		$query = $this->db->select('minutos_gestion, minutos_extra, cantidad_extensiones')
			->from('parametria.campanias_manuales')
			->where("id in ($subQuery)");
		
		$result = $query->get()->result_array();
		
		if (!empty($result)) {
			$result = $result[0];
		}
		
		return $result;
	}
	
	
	/**
	 * Obtiene la campania de un credito y operador
	 * 
	 * @param $idCredito
	 * @param $idOperador
	 *
	 * @return array
	 */
	public function getCreditoCampania($idCredito, $idOperador)
	{
		$subQuery = $this->db->select('id_campania')
			->from('relacion_casos_operador_manual')
			->where('id_credito', $idCredito)
			->where('id_operador', $idOperador)
			->get_compiled_select();
		
		$query = $this->db->select('*')
			->from('parametria.campanias_manuales')
			->where("id in ($subQuery)");
		
		$result = $query->get()->result_array();
		
		if (!empty($result)) {
			$result = $result[0];
		}
		
		return $result;
	}
	
	/**
	 * Deveulve el estado de un operador en una campania
	 * 
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return null | string
	 */
	public function getEstadoOperadorEnCampania($idOperador)
	{
		$query = $this->db->select('*')
			->from('relacion_operador_campania_manual')
			->where('id_operador', $idOperador)
			->get();
		
		$result = $query->result_array();
		
		$estado = null;
		if (!empty($result)) {
			$estado = $result[0]['estado'];
		}
		
		return $estado;
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a activo
	 * 
	 * @param $idOperador
	 *
	 * @return bool
	 */
	public function cambiarEstadoOperadorActivo($idOperador)
	{
		return $this->cambiarEstadoOperador($idOperador, 'activo');
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a inactivo
	 * 
	 * @param $idOperador
	 *
	 * @return bool
	 */
	public function cambiarEstadoOperadorInactivo($idOperador)
	{
		return $this->cambiarEstadoOperador($idOperador, 'inactivo');
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a desactivado
	 * 
	 * @param $idOperador
	 *
	 * @return bool
	 */
	public function cambiarEstadoOperadorDesactivado($idOperador)
	{
		return $this->cambiarEstadoOperador($idOperador, 'desactivado');
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a descanso
	 * 
	 * @param $idOperador
	 *
	 * @return bool
	 */
	public function cambiarEstadoOperadorDescanso($idOperador)
	{
		return $this->cambiarEstadoOperador($idOperador, 'descanso');
	}
	
	/**
	 * Actualiza el estado de un operador en una campania
	 * 
	 * @param $idOperador
	 * @param $estado
	 *
	 * @return bool
	 */
	private function cambiarEstadoOperador($idOperador, $estado)
	{
		$query = $this->db->set('estado', $estado)
			->where('id_operador', $idOperador)
			->update('relacion_operador_campania_manual');
		$row = $this->db->affected_rows();

		return ($row > 0);
	}
	
	/**
	 * Quita desasigna un credito al operador
	 * 
	 * @param $idCredito
	 * @param $idOperador
	 *
	 * @return bool
	 */
	public function removeCreditoDeOperador($idCredito, $idOperador)
	{
		$query = $this->db->where('id_credito', $idCredito)
			->where('id_operador', $idOperador)
			->delete('relacion_casos_operador_manual');

		$row = $this->db->affected_rows();
		
		return ($row > 0);
	}
		
	/**
	 * Obtiene la cantidad de casos asignados al operador
	 * 
	 * @param $idOperador
	 *
	 * @return mixed
	 */
	public function checkOperadorTieneNuevosCasosAsignados($idOperador)
	{
		$query = $this->db->select('*')
			->from('relacion_casos_operador_manual')
			->where('id_operador', $idOperador)
			->get();
		
		return $this->db->affected_rows(); 
	}
	
	/**
	 * Comprueba si el operador tiene una campania asignada
	 * 
	 * @param $idOperador
	 *
	 * @return bool
	 */
	public function checkOperadorTieneCampaniaAsignada($idOperador)
	{
		$query = $this->db->select('*')
			->from('relacion_operador_campania_manual')
			->where('id_operador', $idOperador)
			->get();
		
		return ($this->db->affected_rows() > 0);
	}
	
	/**
	 * Setea el fin de la gestion del operador en el caso
	 *
	 * @param $idCredito
	 * @param $idOperador
	 * @param $idCampania
	 */
	public function setFinGestionCaso($idCredito, $idOperador, $idCampania)
	{
		$this->db->set('fecha_fin_gestion',  date('Y-m-d H:i:s'));
		$this->db->where('id_campania', $idCampania);
		$this->db->where('id_credito', $idCredito);
		$this->db->where('id_operador', $idOperador);
		$this->db->update('relacion_casos_campania_manual');
	}
	
	/**
	 * Obtiene la campania asignada al operador
	 *
	 * @param $idOperador
	 *
	 * @return array
	 */
	public function getCampaniaAsignada($idOperador)
	{
		$result = $this->db_gestion->select('cm.*')
			->from('relacion_operador_campania_manual rocm')
			->join('parametria.campanias_manuales cm', 'rocm.id_campania = cm.id')
			->where('rocm.id_operador', $idOperador)
			->get()->result_array();
		
		return $result;
	}
    public function get_operadores_by_tipo(){
        $this->db_gestion->select('o.idoperador, o.nombre_apellido,o.tipo_operador as idope, t.descripcion as tipo_operador, og.estado as check_state');
        $this->db_gestion->from('operadores o');
        $this->db_gestion->join('tipo_operador t',"t.idtipo_operador = o.tipo_operador ");
        $this->db_gestion->join('operadores_gestion_chat og',"og.id_operador = o.idoperador ","LEFT");

        $this->db_gestion->where_in("o.tipo_operador" , [1,4,5,6,9]);
        $this->db_gestion->where("o.estado" , 1);
        $query = $this->db_gestion->get();

        //  print_r( $sql = $this->db_gestion->last_query());die;
        return $query->result_array();
    }
    public function getCampaniaUAC($idOperador)
    {

    $this->db_gestion->where("id_operador", $idOperador);
    $resultados = $this->db_gestion->get("operadores_gestion_chat");


        if ($resultados->num_rows()>0) {
            return TRUE;
        }else{
            return FALSE;
        }
        
    }

    public function getUACparameters()
    {
        $this->db_chat->select('*');
        $this->db_chat->from('chat.tiempos_gestion');

        $query = $this->db_chat->get();

        //  print_r( $sql = $this->db_gestion->last_query());die;
        return $query->result_array();
    }
    public function getCreditoAsignadaUAC($id_operador)
    {



        $this->db_chat->select('CR.id id_credito,N.*');
        $this->db_chat->from('chat.new_chats N');
        $this->db_chat->join('maestro.agenda_telefonica A',"N.from = A.numero","INNER");
        $this->db_chat->join('maestro.creditos CR',"CR.id_cliente = A.id_cliente","INNER");
        $this->db_chat->where ("N.id_operador", $id_operador);
        $this->db_chat->where ("N.status_chat", "activo");
        $this->db_chat->where ("N.prioridad_gestion in (1,2,3,4,5) ");
        $this->db_chat->where ('N.id_operador in (SELECT id_operador from gestion.operadores_gestion_chat) ');
        $this->db_chat->order_by("N.prioridad_gestion,N.fecha_ultima_recepcion","DESC");

        $query = $this->db_chat->get();

        //  print_r( $sql = $this->db_gestion->last_query());die;
        return $query->result_array();
    }
    
    public function consulta_chat($numero, $canal = null)
    {
        $this->db_chat->select('id as id_chat, status_chat');
        $this->db_chat->from('new_chats');   
        $this->db_chat->where('from', $numero);   
        if (isset($canal) && !is_null($canal)) {
            $this->db_chat->where('to', $canal);
        }
        $this->db_chat->order_by("id","DESC");
        $this->db_chat->limit(1);
        
        $query = $this->db_chat->get()->result_array();
        
        return $query;      
    }
}

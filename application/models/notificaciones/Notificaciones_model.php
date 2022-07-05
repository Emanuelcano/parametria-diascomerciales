<?php

class Notificaciones_model extends CI_Model {
    

    public function __construct() {
        parent::__construct();
        // LOAD SCHEMA
        $this->db = $this->load->database('gestion', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_telefonia = $this->load->database('telefonia', TRUE);
        $this->db_parametria = $this->load->database('parametria', TRUE);
        $this->db_campania = $this->load->database('campanias', TRUE);
    }

    public function get_all_groups_words()
    {
        $this->db_parametria->order_by("id_grupo_notificacion", "ASC");
        return $this->db_parametria->get("grupos_filtros_notificaciones")->result();
    }
    
    public function mostrarGruposxOrigen($origen)
    {
        $this->db_parametria->where("origen", $origen);
        $this->db_parametria->order_by("id_grupo_notificacion", "ASC");
        return $this->db_parametria->get("grupos_filtros_notificaciones")->result();
    }
    
    public function insert_groups_words($data)
    {
        $this->db_parametria->insert('grupos_filtros_notificaciones', $data);
        
        if ($this->db_parametria->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function get_grupo_update($id)
    {
        $this->db_parametria->where("id_grupo_notificacion", $id);
        $this->db_parametria->order_by("id_grupo_notificacion", "ASC");
        return $this->db_parametria->get("grupos_filtros_notificaciones")->result();
    }
    
    public function update_groups_words($data,$id)
    {
        $this->db_parametria->where('id_grupo_notificacion', $id);
        $this->db_parametria->update('grupos_filtros_notificaciones', $data);
        if ($this->db_parametria->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
    }
    public function search_por_service($service)
    {
        if ($service=="SLACK") {
            
            $this->db->select('S.*');
            $this->db->from('gestion.slack_users S');
            // $this->db->join('gestion.operadores O',"S.operador_id = O.idoperador","LEFT");
            // $this->db->where_in('O.tipo_operador',[2,9,10,11,12,13]);
            $this->db->order_by("id", "ASC");
            
            // print_r($this->db->get_compiled_select());die;
            $query = $this->db->get();
            return $query->result();


        }else if ($service=="EMAIL") {
            $this->db->select('*');
            $this->db->from('gestion.operadores');
            $this->db->where("estado", 1);
            $this->db->where_in('tipo_operador',[2,9,10,11,12,13]);
            $this->db->order_by("idoperador", "ASC");
            // print_r($this->db->get_compiled_select());die;
            return $this->db->get()->result();
            
        }else if ($service=="SMS") {
            $this->db->select('*');
            $this->db->from('gestion.operadores');
            $this->db->where("estado", 1);
            $this->db->where_in('tipo_operador',[2,9,10,11,12,13]);
            $this->db->order_by("idoperador", "ASC");
            // print_r($this->db->get_compiled_select());die;
            return $this->db->get()->result();
            
        }
       
    }


    public function search_track($arrayConsult)
    {
        $daterange = (!empty($arrayConsult['daterange']))?$arrayConsult['daterange']:NULL;
        
        $sl_sr_criterio = (!empty($arrayConsult['sl_sr_criterio']))?$arrayConsult['sl_sr_criterio']:NULL;
        $txt_sr_cliente = (!empty($arrayConsult['txt_sr_cliente']))?$arrayConsult['txt_sr_cliente']:NULL;
        $sl_sr_canal = (!empty($arrayConsult['sl_sr_canal']))?$arrayConsult['sl_sr_canal']:NULL;
        $sl_sr_grupo = (!empty($arrayConsult['sl_sr_grupo']))?$arrayConsult['sl_sr_grupo']:NULL;
        $sl_sr_medio = (!empty($arrayConsult['sl_sr_medio']))?$arrayConsult['sl_sr_medio']:NULL;
        $sl_sr_palabras = (!empty($arrayConsult['sl_sr_palabras']))?$arrayConsult['sl_sr_palabras']:NULL;
        $sl_sr_medios = (!empty($arrayConsult['sl_sr_medios']))?$arrayConsult['sl_sr_medios']:NULL;

        
        $signos = array("{","[","}","]");
        
        $dataConsult = [];
        $dataConsult['sl_sr_canal'] = $sl_sr_canal;
        $dataConsult['sl_sr_grupo'] = $sl_sr_grupo;
        $dataConsult['sl_sr_medio'] = $sl_sr_medio;
        
        
        $this->db->select('id_track, id_grupo_notificacion,nombre_grupo, notificados_slack, notificados_correos, notificados_sms, mensaje_filtrado, mensaje_notificar, medios_notificacion, metodo_notificacion, operador,cliente, num_documento, num_contacto, palabras, action, origen, DATE_FORMAT(fecha_notificacion, "%d-%m-%Y %H:%i:%s" ) AS fecha_notificacion');
        $this->db->from('gestion.track_notificaciones');

        if (!empty($daterange)){
            $fechaIni= explode(" - ",$daterange);
            $fechaInicio = $fechaIni[0];
            $fechaFinal = $fechaIni[1];
            $fechaIniAno = substr($fechaInicio , 6 , 4);
            $fechaIniMes = substr($fechaInicio , 3 , 2);
            $fechaIniDia = substr($fechaInicio , 0 , 2);
            $fechaIniHora = substr($fechaInicio , 11 , 2);
            $fechaIniMin = substr($fechaInicio , 14 , 2);
            $fechaIniSeg = substr($fechaInicio , 17 , 2);

            
            $fechaFinAno = substr($fechaFinal , 6 , 4);
            $fechaFinMes = substr($fechaFinal , 3 , 2);
            $fechaFinDia = substr($fechaFinal , 0 , 2);
            $fechaFinHora = substr($fechaFinal , 11 , 2);
            $fechaFinMin = substr($fechaFinal , 14 , 2);
            $fechaFinSeg = substr($fechaFinal , 17 , 2);


            // $fecIni = $fechaIniAno."-".$fechaIniMes."-".$fechaIniDia." ".$fechaIniHora.":".$fechaIniMin.":".$fechaIniSeg;
            // $fecFin =$fechaFinAno."-".$fechaFinMes."-".$fechaFinDia." ".$fechaFinHora.":".$fechaFinMin.":".$fechaFinSeg;
            $fecIni = $fechaIniAno."-".$fechaIniMes."-".$fechaIniDia." 00:00:00";
            $fecFin =$fechaFinAno."-".$fechaFinMes."-".$fechaFinDia." 23:59:59";
            // var_dump($fecIni,$fecFin);die;
            $dataConsult['fechaInicio'] = $fechaInicio;
            $dataConsult['fechaFinal'] = $fechaFinal;
            $this->db->where("fecha_notificacion BETWEEN '$fecIni' AND '$fecFin'");
        }

        if (!empty($sl_sr_criterio) && !empty($txt_sr_cliente)){
            if($sl_sr_criterio == "DOCUMENTO")
            {

                $this->db->where("num_documento", $txt_sr_cliente);
            }else{

                $this->db->where("num_contacto", $txt_sr_cliente);
            }
        }
        
        if (!empty($sl_sr_grupo)){
            $dataConsult['sl_sr_grupo'] = $sl_sr_grupo;
            $this->db->where("id_grupo_notificacion", $sl_sr_grupo);
        }
        if (!empty($sl_sr_medio)){
            $dataConsult['sl_sr_medio'] = $sl_sr_medio;
            $this->db->where("medios_notificacion", $sl_sr_medio);
        }
        if (!empty($sl_sr_canal)){
            $dataConsult['sl_sr_canal'] = $sl_sr_canal;
            $this->db->where("origen", $sl_sr_canal);
        }
        if (!empty($sl_sr_palabras)){
            
            $toString = json_encode($sl_sr_palabras);
            $palabrastoSearch = str_replace($signos, "", strtolower($toString));
            $dataConsult['sl_sr_palabras'] = $palabrastoSearch;
            $this->db->where("mensaje_notificar like '%".$palabrastoSearch."%'");
        }
        if (!empty($sl_sr_medios)){
            
            $toString2 = json_encode($sl_sr_medios);
            $mediostoSearch = str_replace($signos, "", $toString2);
            $dataConsult['sl_sr_medios'] = $mediostoSearch;
            if(!empty($sl_sr_medio) && $sl_sr_medio=="SLACK")
            {
                $this->db->where('notificados_slack IN ('.$mediostoSearch.')');

            } else  if(!empty($sl_sr_medio) && $sl_sr_medio=="SMS"){
                $this->db->where('notificados_sms IN ('.$mediostoSearch.')');

            } else  if(!empty($sl_sr_medio) && $sl_sr_medio=="EMAIL"){

                $this->db->where('notificados_correos IN ('.$mediostoSearch.')');
            }
        }
        // print_r($this->db->get_compiled_select());die;
        $dataArray=[];
        $dataArray = $this->db->get()->result_array();

        $this->db->select("slack_id,nombre");
        $this->db->from("gestion.slack_users");
       
        $subquery = $this->db->get()->result_array();
        // var_dump($subquery);die;
        $arraySlack =[];
        foreach ($subquery as $key2 => $value2) {
            $arraySlack[$value2['slack_id']]= $value2['nombre'];
            
        }
        // var_dump($arraySlack);die;
        //  $intersep = array_intersect($dataArray,$subquery);
        
        foreach ($dataArray as $key => $value) {
            //  var_dump($value['notificados_slack']);
             $partes = explode(",",$value['notificados_slack']);
             $aux = [];
             foreach ($partes as $key3 => $value3) {
                 $aux[]=$arraySlack[$value3];

            }
            $dataArray[$key]['notificado'] = implode(',',$aux);

            // $clave = array_search($value['notificados_slack'], $subquery); 
        }

        

        // foreach ($dataArray as $key => $value) {
        //     $this->db->select("nombre");
        //     $this->db->from("gestion.slack_users");
        //     $this->db->where_in('slack_id',explode(',',$value['notificados_slack']));
        //     $subquery = $this->db->get()->result_array();
        //     $signos = array("{","[","}","]",'nombre','"',':');
        //     $subNombres=json_encode($subquery);
        //     $subNombresNewString = str_replace($signos, "", $subNombres);
        //     $dataArray[$key]['notificado'] = $subNombresNewString;
            
        // }
      
        return $dataArray;

    }
     
    public function reprocesamiento_notificaciones()
    {
        $this->db->order_by("id_track", "DESC");
        //  $this->db->limit(34,100);
        $arrayData =  $this->db->get("gestion.track_notificaciones")->result();
   
        $DataInsert = [];
        $arreglo = [];
        $contPlus=0;
        $contNeg=0;
        foreach ($arrayData as $key => $value) {
            $operador = explode("\n",$value->mensaje_notificar);
            $arreglo[] = reprocesar($operador);
           
            $this->db->where('id_track',(int)$value->id_track);
            $this->db->update('gestion.track_notificaciones',$arreglo[$key],['id_track'=>(int)$value->id_track]);
           
            if ($this->db->affected_rows() > 0) {
                $contPlus++;
            }
            else{
                
                $contNeg++;
            }

          
        }

        return ("Cantidad Actualizados: ".$contPlus. " Cantidad rechazada: ".$contNeg);
        
    }

    public function gruposToLabelGraph($origen)
    {
        $this->db_parametria->select("nombre_grupo");
        $this->db_parametria->where("origen", $origen);
        $this->db_parametria->order_by("id_grupo_notificacion", "ASC");
        return $this->db_parametria->get("grupos_filtros_notificaciones")->result();
    }

    public function search_track_stats($arrayConsult)
    {
        $daterange = (!empty($arrayConsult['daterange']))?$arrayConsult['daterange']:NULL;
        
        $sl_sr_criterio = (!empty($arrayConsult['sl_sr_criterio']))?$arrayConsult['sl_sr_criterio']:NULL;
        $txt_sr_cliente = (!empty($arrayConsult['txt_sr_cliente']))?$arrayConsult['txt_sr_cliente']:NULL;
        $sl_sr_canal = (!empty($arrayConsult['sl_sr_canal']))?$arrayConsult['sl_sr_canal']:NULL;
        $sl_sr_grupo = (!empty($arrayConsult['sl_sr_grupo']))?$arrayConsult['sl_sr_grupo']:NULL;
        $sl_sr_medio = (!empty($arrayConsult['sl_sr_medio']))?$arrayConsult['sl_sr_medio']:NULL;
        $sl_sr_palabras = (!empty($arrayConsult['sl_sr_palabras']))?$arrayConsult['sl_sr_palabras']:NULL;
        $sl_sr_medios = (!empty($arrayConsult['sl_sr_medios']))?$arrayConsult['sl_sr_medios']:NULL;

        
        $signos = array("{","[","}","]");
        
        $dataConsult = [];
        $dataConsult['sl_sr_canal'] = $sl_sr_canal;
        $dataConsult['sl_sr_grupo'] = $sl_sr_grupo;
        $dataConsult['sl_sr_medio'] = $sl_sr_medio;
        
        $this->db->select('origen,nombre_grupo,COUNT(`id_grupo_notificacion`) cantidad');
        $this->db->from('gestion.track_notificaciones');

        if (!empty($daterange)){
            $fechaIni= explode(" - ",$daterange);
            $fechaInicio = $fechaIni[0];
            $fechaFinal = $fechaIni[1];
            $fechaIniAno = substr($fechaInicio , 6 , 4);
            $fechaIniMes = substr($fechaInicio , 3 , 2);
            $fechaIniDia = substr($fechaInicio , 0 , 2);
            $fechaIniHora = substr($fechaInicio , 11 , 2);
            $fechaIniMin = substr($fechaInicio , 14 , 2);
            $fechaIniSeg = substr($fechaInicio , 17 , 2);

            
            $fechaFinAno = substr($fechaFinal , 6 , 4);
            $fechaFinMes = substr($fechaFinal , 3 , 2);
            $fechaFinDia = substr($fechaFinal , 0 , 2);
            $fechaFinHora = substr($fechaFinal , 11 , 2);
            $fechaFinMin = substr($fechaFinal , 14 , 2);
            $fechaFinSeg = substr($fechaFinal , 17 , 2);


            // $fecIni = $fechaIniAno."-".$fechaIniMes."-".$fechaIniDia." ".$fechaIniHora.":".$fechaIniMin.":".$fechaIniSeg;
            // $fecFin =$fechaFinAno."-".$fechaFinMes."-".$fechaFinDia." ".$fechaFinHora.":".$fechaFinMin.":".$fechaFinSeg;
            $fecIni = $fechaIniAno."-".$fechaIniMes."-".$fechaIniDia." 00:00:00";
            $fecFin =$fechaFinAno."-".$fechaFinMes."-".$fechaFinDia." 23:59:59";
            // var_dump($fecIni,$fecFin);die;

            $dataConsult['fechaInicio'] = $fechaInicio;
            $dataConsult['fechaFinal'] = $fechaFinal;
            $this->db->where("fecha_notificacion BETWEEN '$fecIni' AND '$fecFin'");
        }

        if (!empty($sl_sr_criterio) && !empty($txt_sr_cliente)){
            if($sl_sr_criterio == "DOCUMENTO")
            {

                $this->db->where("num_documento", $txt_sr_cliente);
            }else{

                $this->db->where("num_contacto", $txt_sr_cliente);
            }
        }
        
        if (!empty($sl_sr_grupo)){
            $dataConsult['sl_sr_grupo'] = $sl_sr_grupo;
            $this->db->where("id_grupo_notificacion", $sl_sr_grupo);
        }
        if (!empty($sl_sr_medio)){
            $dataConsult['sl_sr_medio'] = $sl_sr_medio;
            $this->db->where("medios_notificacion", $sl_sr_medio);
        }
        if (!empty($sl_sr_canal)){
            $dataConsult['sl_sr_canal'] = $sl_sr_canal;
            $this->db->where("origen", $sl_sr_canal);
        }
        if (!empty($sl_sr_palabras)){
            
            $toString = json_encode($sl_sr_palabras);
            $palabrastoSearch = str_replace($signos, "", strtolower($toString));
            $dataConsult['sl_sr_palabras'] = $palabrastoSearch;
            $this->db->where("mensaje_notificar like '%".$palabrastoSearch."%'");
        }
        if (!empty($sl_sr_medios)){
            
            $toString2 = json_encode($sl_sr_medios);
            $mediostoSearch = str_replace($signos, "", $toString2);
            $dataConsult['sl_sr_medios'] = $mediostoSearch;
            if(!empty($sl_sr_medio) && $sl_sr_medio=="SLACK")
            {
                $this->db->where('notificados_slack IN ('.$mediostoSearch.')');

            } else  if(!empty($sl_sr_medio) && $sl_sr_medio=="SMS"){
                $this->db->where('notificados_sms IN ('.$mediostoSearch.')');

            } else  if(!empty($sl_sr_medio) && $sl_sr_medio=="EMAIL"){

                $this->db->where('notificados_correos IN ('.$mediostoSearch.')');
            }
        }
        $this->db->group_by("id_grupo_notificacion");
        // print_r($this->db->get_compiled_select());die;
        return $this->db->get()->result();

    }
    public function search_track_donut_stats($arrayConsult)
    {
        $daterange = (!empty($arrayConsult['daterange']))?$arrayConsult['daterange']:NULL;
        
        $sl_sr_criterio = (!empty($arrayConsult['sl_sr_criterio']))?$arrayConsult['sl_sr_criterio']:NULL;
        $txt_sr_cliente = (!empty($arrayConsult['txt_sr_cliente']))?$arrayConsult['txt_sr_cliente']:NULL;
        $sl_sr_canal = (!empty($arrayConsult['sl_sr_canal']))?$arrayConsult['sl_sr_canal']:NULL;
        $sl_sr_grupo = (!empty($arrayConsult['sl_sr_grupo']))?$arrayConsult['sl_sr_grupo']:NULL;
        $sl_sr_medio = (!empty($arrayConsult['sl_sr_medio']))?$arrayConsult['sl_sr_medio']:NULL;
        $sl_sr_palabras = (!empty($arrayConsult['sl_sr_palabras']))?$arrayConsult['sl_sr_palabras']:NULL;
        $sl_sr_medios = (!empty($arrayConsult['sl_sr_medios']))?$arrayConsult['sl_sr_medios']:NULL;

        
        $signos = array("{","[","}","]");
        
        $dataConsult = [];
        $dataConsult['sl_sr_canal'] = $sl_sr_canal;
        $dataConsult['sl_sr_grupo'] = $sl_sr_grupo;
        $dataConsult['sl_sr_medio'] = $sl_sr_medio;
        
        $this->db->select('origen, COUNT( `origen` ) cantidad');
        $this->db->from('gestion.track_notificaciones');

        if (!empty($daterange)){
            $fechaIni= explode(" - ",$daterange);
            $fechaInicio = $fechaIni[0];
            $fechaFinal = $fechaIni[1];
            $fechaIniAno = substr($fechaInicio , 6 , 4);
            $fechaIniMes = substr($fechaInicio , 3 , 2);
            $fechaIniDia = substr($fechaInicio , 0 , 2);
            $fechaIniHora = substr($fechaInicio , 11 , 2);
            $fechaIniMin = substr($fechaInicio , 14 , 2);
            $fechaIniSeg = substr($fechaInicio , 17 , 2);
            
            $fechaFinAno = substr($fechaFinal , 6 , 4);
            $fechaFinMes = substr($fechaFinal , 3 , 2);
            $fechaFinDia = substr($fechaFinal , 0 , 2);
            $fechaFinHora = substr($fechaFinal , 11 , 2);
            $fechaFinMin = substr($fechaFinal , 14 , 2);
            $fechaFinSeg = substr($fechaFinal , 17 , 2);


            // $fecIni = $fechaIniAno."-".$fechaIniMes."-".$fechaIniDia." ".$fechaIniHora.":".$fechaIniMin.":".$fechaIniSeg;
            // $fecFin =$fechaFinAno."-".$fechaFinMes."-".$fechaFinDia." ".$fechaFinHora.":".$fechaFinMin.":".$fechaFinSeg;
            $fecIni = $fechaIniAno."-".$fechaIniMes."-".$fechaIniDia." 00:00:00";
            $fecFin =$fechaFinAno."-".$fechaFinMes."-".$fechaFinDia." 23:59:59";
            //  var_dump($fecIni,$fecFin);die;
            $dataConsult['fechaInicio'] = $fechaInicio;
            $dataConsult['fechaFinal'] = $fechaFinal;
            $this->db->where("fecha_notificacion BETWEEN '$fecIni' AND '$fecFin'");
        }

        if (!empty($sl_sr_criterio) && !empty($txt_sr_cliente)){
            if($sl_sr_criterio == "DOCUMENTO")
            {

                $this->db->where("num_documento", $txt_sr_cliente);
            }else{

                $this->db->where("num_contacto", $txt_sr_cliente);
            }
        }
        
        if (!empty($sl_sr_grupo)){
            $dataConsult['sl_sr_grupo'] = $sl_sr_grupo;
            $this->db->where("id_grupo_notificacion", $sl_sr_grupo);
        }
        if (!empty($sl_sr_medio)){
            $dataConsult['sl_sr_medio'] = $sl_sr_medio;
            $this->db->where("medios_notificacion", $sl_sr_medio);
        }
        
        if (!empty($sl_sr_palabras)){
            
            $toString = json_encode($sl_sr_palabras);
            $palabrastoSearch = str_replace($signos, "", strtolower($toString));
            $dataConsult['sl_sr_palabras'] = $palabrastoSearch;
            $this->db->where("mensaje_notificar like '%".$palabrastoSearch."%'");
        }
        if (!empty($sl_sr_medios)){
            
            $toString2 = json_encode($sl_sr_medios);
            $mediostoSearch = str_replace($signos, "", $toString2);
            $dataConsult['sl_sr_medios'] = $mediostoSearch;
            if(!empty($sl_sr_medio) && $sl_sr_medio=="SLACK")
            {
                $this->db->where('notificados_slack IN ('.$mediostoSearch.')');

            } else  if(!empty($sl_sr_medio) && $sl_sr_medio=="SMS"){
                $this->db->where('notificados_sms IN ('.$mediostoSearch.')');

            } else  if(!empty($sl_sr_medio) && $sl_sr_medio=="EMAIL"){

                $this->db->where('notificados_correos IN ('.$mediostoSearch.')');
            }
        }
        $this->db->group_by("origen");
        // print_r($this->db->get_compiled_select());die;
        return $this->db->get()->result();

    }

    public function search_group_id($dataArray)
    {
        $nombre_grupo = explode(":", $dataArray['id']);
        $this->db_parametria->select('id_grupo_notificacion');
        $this->db_parametria->from('parametria.grupos_filtros_notificaciones');
        $this->db_parametria->where("nombre_grupo", $nombre_grupo[0]);
        $this->db_parametria->where("origen", $dataArray['origen']);
        return $this->db_parametria->get()->result_array();
    }
    public function search_notificaciones_words($arrayConsult,$id_grupo_notificacion,$origen)
    {
        // $this->db->select('palabras');
        // $this->db->from('gestion.track_notificaciones');
        // $this->db->where("id_grupo_notificacion", $id_grupo_notificacion);
        // $this->db->where("origen", $origen);
        // $this->db->order_by("id_track", "DESC");
        // return $this->db->get()->result_array();


        $daterange = (!empty($arrayConsult['daterange']))?$arrayConsult['daterange']:NULL;
        
        $sl_sr_criterio = (!empty($arrayConsult['sl_sr_criterio']))?$arrayConsult['sl_sr_criterio']:NULL;
        $txt_sr_cliente = (!empty($arrayConsult['txt_sr_cliente']))?$arrayConsult['txt_sr_cliente']:NULL;
        $sl_sr_canal = (!empty($arrayConsult['sl_sr_canal']))?$arrayConsult['sl_sr_canal']:NULL;
        $sl_sr_grupo = (!empty($arrayConsult['sl_sr_grupo']))?$arrayConsult['sl_sr_grupo']:NULL;
        $sl_sr_medio = (!empty($arrayConsult['sl_sr_medio']))?$arrayConsult['sl_sr_medio']:NULL;
        $sl_sr_palabras = (!empty($arrayConsult['sl_sr_palabras']))?$arrayConsult['sl_sr_palabras']:NULL;
        $sl_sr_medios = (!empty($arrayConsult['sl_sr_medios']))?$arrayConsult['sl_sr_medios']:NULL;

        
        $signos = array("{","[","}","]");
        
        $dataConsult = [];
        $dataConsult['sl_sr_canal'] = $sl_sr_canal;
        $dataConsult['sl_sr_grupo'] = $sl_sr_grupo;
        $dataConsult['sl_sr_medio'] = $sl_sr_medio;
        
        $this->db->select('palabras');
        $this->db->from('gestion.track_notificaciones');

        if (!empty($daterange)){
            $fechaIni= explode(" - ",$daterange);
            $fechaInicio = $fechaIni[0];
            $fechaFinal = $fechaIni[1];
            $fechaIniAno = substr($fechaInicio , 6 , 4);
            $fechaIniMes = substr($fechaInicio , 3 , 2);
            $fechaIniDia = substr($fechaInicio , 0 , 2);
            $fechaIniHora = substr($fechaInicio , 11 , 2);
            $fechaIniMin = substr($fechaInicio , 14 , 2);
            $fechaIniSeg = substr($fechaInicio , 17 , 2);
            
            $fechaFinAno = substr($fechaFinal , 6 , 4);
            $fechaFinMes = substr($fechaFinal , 3 , 2);
            $fechaFinDia = substr($fechaFinal , 0 , 2);
            $fechaFinHora = substr($fechaFinal , 11 , 2);
            $fechaFinMin = substr($fechaFinal , 14 , 2);
            $fechaFinSeg = substr($fechaFinal , 17 , 2);


            // $fecIni = $fechaIniAno."-".$fechaIniMes."-".$fechaIniDia." ".$fechaIniHora.":".$fechaIniMin.":".$fechaIniSeg;
            // $fecFin =$fechaFinAno."-".$fechaFinMes."-".$fechaFinDia." ".$fechaFinHora.":".$fechaFinMin.":".$fechaFinSeg;
            $fecIni = $fechaIniAno."-".$fechaIniMes."-".$fechaIniDia." 00:00:00";
            $fecFin =$fechaFinAno."-".$fechaFinMes."-".$fechaFinDia." 23:59:59";
            //  var_dump($fecIni,$fecFin);die;
            $dataConsult['fechaInicio'] = $fechaInicio;
            $dataConsult['fechaFinal'] = $fechaFinal;
            $this->db->where("fecha_notificacion BETWEEN '$fecIni' AND '$fecFin'");
        }

        if (!empty($sl_sr_criterio) && !empty($txt_sr_cliente)){
            if($sl_sr_criterio == "DOCUMENTO")
            {

                $this->db->where("num_documento", $txt_sr_cliente);
            }else{

                $this->db->where("num_contacto", $txt_sr_cliente);
            }
        }
        
       
            $this->db->where("id_grupo_notificacion", $id_grupo_notificacion);
            $this->db->where("origen", $origen);
        
        if (!empty($sl_sr_medio)){
            $dataConsult['sl_sr_medio'] = $sl_sr_medio;
            $this->db->where("medios_notificacion", $sl_sr_medio);
        }
        
        if (!empty($sl_sr_palabras)){
            
            $toString = json_encode($sl_sr_palabras);
            $palabrastoSearch = str_replace($signos, "", strtolower($toString));
            $dataConsult['sl_sr_palabras'] = $palabrastoSearch;
            $this->db->where("mensaje_notificar like '%".$palabrastoSearch."%'");
        }
        if (!empty($sl_sr_medios)){
            
            $toString2 = json_encode($sl_sr_medios);
            $mediostoSearch = str_replace($signos, "", $toString2);
            $dataConsult['sl_sr_medios'] = $mediostoSearch;
            if(!empty($sl_sr_medio) && $sl_sr_medio=="SLACK")
            {
                $this->db->where('notificados_slack IN ('.$mediostoSearch.')');

            } else  if(!empty($sl_sr_medio) && $sl_sr_medio=="SMS"){
                $this->db->where('notificados_sms IN ('.$mediostoSearch.')');

            } else  if(!empty($sl_sr_medio) && $sl_sr_medio=="EMAIL"){

                $this->db->where('notificados_correos IN ('.$mediostoSearch.')');
            }
        }
        $this->db->order_by("id_track", "DESC");
        // print_r($this->db->get_compiled_select());die;
        return $this->db->get()->result_array();


    }
    

   
}

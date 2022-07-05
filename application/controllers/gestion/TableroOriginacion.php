<?php

class TableroOriginacion extends CI_Controller {

    // protected $CI;

    public function __construct() {
        parent::__construct();
        $this->load->model("tablero_originacion/OriginacionModel", "originacion");
    }

//Begin Alexis Rodriguez 2021
    public function index() {
            $title['title'] = 'Tablero Originacion';
            $this->load->view('layouts/adminLTE', $title);
            $data['heading'] = 'Tablero Originacion';
            $this->load->view('tablero_originacion/tablero', ['data' => $data]);     
    }

    public function originacion_data(){
        $hoy=date('Y-m-d');
        // $hoy=date('2021-11-12');
		$dia = date("d",strtotime($hoy));
		$diaInt = intval(date("d",strtotime($hoy)));
		$mes = date("m",strtotime($hoy));
		$anho = date("Y",strtotime($hoy));
		$ayer = strtotime('-1 day', strtotime($hoy));
		$ayer = date('d-m-Y', $ayer);
		$nombre_dia = date("D",strtotime($ayer));
        $data=[];
        $tipo = $_POST['tipo'];

        $objetivos_tablero = $this->originacion->get_objetivo_tablero(['id' => 1]);

        $objetivo_porcentaje =  $objetivos_tablero[0]->objetivo_porcentaje;
		$objetivos_dependientes =  $objetivos_tablero[0]->objetivo_dependientes;
		$objetivos_independientes =  $objetivos_tablero[0]->objetivos_independientes;
		$fecha_mora_mostrar =  $objetivos_tablero[0]->fecha_mora_mostrar;
		$objetivo_mora =  $objetivos_tablero[0]->objetivo_mora;
		$proximo_vencimiento =  $objetivos_tablero[0]->proximo_vencimiento;

		$estados[] = array('"APROBADO"','"TRANSFIRIENDO"','"PAGADO"');
		$estados[] = array('"RECHAZADO"');
        
        if ($tipo == 'btn_diario') {
            $dia = "'$hoy 00:00:00.000000' AND '$hoy 23:59:59.000000'";
            // $dia = "'2021-11-12 00:00:00.000000' AND '2021-11-12 23:59:59.000000'";
            
            $data['indicadores'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$dia]);
            $aux = $data['indicadores'];

            $asignados_hoy_dependientes = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$dia, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "dependiente"]);
			$asignados_hoy_independientes = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$dia, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "independiente"]);
			$aprobados_hoy_dependientes = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$dia, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "dependiente"]);
			$rechazado_hoy_dependientes = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$dia, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "dependiente"]);
			$aprobados_hoy_independientes = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$dia, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "independiente"]);							
			$rechazado_hoy_independientes = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$dia, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "independiente"]);	
            
            $aux['porcentaje']['objetivo_porcentaje'] = $objetivo_porcentaje;
            $aux['porcentaje']['objetivos_dependientes'] = $objetivos_dependientes;
            $aux['porcentaje']['objetivos_independientes'] = $objetivos_independientes;
            $aux['porcentaje']['objetivo_mora'] = $objetivo_mora;
            $aux['fecha']['rango'] = $fecha_mora_mostrar;
            $aux['fecha']['fecha_vencimiento'] = $proximo_vencimiento;
            foreach ($data['indicadores'] as $key => $item):
                //HOY
                    $valor_hoy_asignados = 0;
                    $valor_hoy_aprobados = 0;
                    $valor_hoy_reto_asignados = 0;
                    $valor_hoy_reto_aprobados = 0;
                    //dependientes
                        $is_in = in_array($item['idoperador'], array_column($asignados_hoy_dependientes, 'operador_asignado'));
                        if($is_in){
                            $index = array_search($item['idoperador'], array_column($asignados_hoy_dependientes, 'operador_asignado'));
                            $valor_hoy_asignados= $asignados_hoy_dependientes[$index]['cantidad'];
                        }
                        $aux[$key]['hoy']['asignados_dependientes'] = $valor_hoy_asignados;
    
                        $is_in = in_array($item['idoperador'], array_column($aprobados_hoy_dependientes, 'operador_asignado'));
                        if($is_in){
                            $index = array_search($item['idoperador'], array_column($aprobados_hoy_dependientes, 'operador_asignado'));
                            $valor_hoy_aprobados= $aprobados_hoy_dependientes[$index]['cantidad'];
                        } 
                        $aux[$key]['hoy']['aprobados_dependientes'] = $valor_hoy_aprobados;
    
                        $valor_hoy_rechazado = 0;
                        $is_in = in_array($item['idoperador'], array_column($rechazado_hoy_dependientes, 'operador_asignado'));
                        if($is_in){
                            $index = array_search($item['idoperador'], array_column($rechazado_hoy_dependientes, 'operador_asignado'));
                            $valor_hoy_rechazado= $rechazado_hoy_dependientes[$index]['cantidad'];
                        } 
                        $aux[$key]['hoy']['rechazado_dependientes'] = $valor_hoy_rechazado;
    
                    //independientes
                        $valor_hoy_asignados = 0;
                        $is_in = in_array($item['idoperador'], array_column($asignados_hoy_independientes, 'operador_asignado'));
                        if($is_in){
                            $index = array_search($item['idoperador'], array_column($asignados_hoy_independientes, 'operador_asignado'));
                            $valor_hoy_asignados= $asignados_hoy_independientes[$index]['cantidad'];
                        }
                        $aux[$key]['hoy']['asignados_independientes'] = $valor_hoy_asignados;
    
                        $valor_hoy_aprobados = 0;
                        $is_in = in_array($item['idoperador'], array_column($aprobados_hoy_independientes, 'operador_asignado'));
                        if($is_in){
                            $index = array_search($item['idoperador'], array_column($aprobados_hoy_independientes, 'operador_asignado'));
                            $valor_hoy_aprobados= $aprobados_hoy_independientes[$index]['cantidad'];
                        } 
                        $aux[$key]['hoy']['aprobados_independientes'] = $valor_hoy_aprobados;
    
                        $valor_hoy_rechazado = 0;
                        $is_in = in_array($item['idoperador'], array_column($rechazado_hoy_independientes, 'operador_asignado'));
                        if($is_in){
                            $index = array_search($item['idoperador'], array_column($rechazado_hoy_independientes, 'operador_asignado'));
                            $valor_hoy_rechazado= $rechazado_hoy_independientes[$index]['cantidad'];
                        } 
                        $aux[$key]['hoy']['rechazado_independientes'] = $valor_hoy_rechazado;
            endforeach;
            $data['indicadores'] = $aux;
        }elseif ($tipo == 'btn_semanal') {
            $comparar = date("w",strtotime($hoy));
            if (!empty($_POST['dia']) && $_POST['dia'] != $comparar) {
                    $diaPost = $_POST['dia'];
                    $diaTemp = strtotime(''.$diaPost.' day', strtotime($hoy));
                    $dia = date('Y-m-d', $diaTemp);
            }else {
                $dia = $hoy;
            }
            $semana1 = date("Y-m-d",strtotime($dia."- 1 week")); 
            $semana2 = date("Y-m-d",strtotime($semana1."- 1 week")); 
            $semana3 = date("Y-m-d",strtotime($semana2."- 1 week")); 
            $semana4 = date("Y-m-d",strtotime($semana3."- 1 week")); 
            
            $fechas1 = "'".$semana1." 00:00:00.000000' AND '".$dia." 23:59:59.000000'";
            $fechas2 = "'".$semana2." 00:00:00.000000' AND '".date("Y-m-d",strtotime($semana1."- 1 day"))." 23:59:59.000000'";
            $fechas3 = "'".$semana3." 00:00:00.000000' AND '".date("Y-m-d",strtotime($semana2."- 1 day"))." 23:59:59.000000'";
            $fechas4 = "'".$semana4." 00:00:00.000000' AND '".date("Y-m-d",strtotime($semana3."- 1 day"))." 23:59:59.000000'";

            $op_asignados['indicadores1'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$fechas1]);
            $op_asignados['indicadores2'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$fechas2]);
            $op_asignados['indicadores3'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$fechas3]);
            $op_asignados['indicadores4'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$fechas4]);
            // var_dump($op_asignados['indicadores1']);die;

            $dateFechas=[];
            for ($i=1; $i < 5; $i++) { 
                if ($i == 1) {
                    $date = $fechas1;
                    $semanaA = date("d-m-Y", strtotime($semana1));
                    $rango = ['desde'=>date("d-m-Y", strtotime($semana1)), 'hasta'=>date("d-m-Y", strtotime($dia))];
                }elseif ($i == 2) {
                    $date = $fechas2;
                    $rango = ['desde'=>date("d-m-Y", strtotime($semana2)), 'hasta'=>date("d-m-Y",strtotime($semana1."- 1 day"))];
                }elseif ($i == 3) {
                    $date = $fechas3;
                    $rango = ['desde'=>date("d-m-Y", strtotime($semana3)), 'hasta'=>date("d-m-Y",strtotime($semana2."- 1 day"))];
                }else{
                    $date = $fechas4;
                    
                    $rango = ['desde'=>date("d-m-Y", strtotime($semana4)), 'hasta'=>date("d-m-Y",strtotime($semana3."- 1 day"))];
                }                
                
                $asig_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "dependiente"]);
                $asig_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "independiente"]);
                $aprob_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "dependiente"]);
                $rech_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "dependiente"]);
                $aprob_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "independiente"]);							
                $rech_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "independiente"]);	
                array_push($dateFechas, ["fechas$i"=>$rango]);
            }
            $datosT =$op_asignados["indicadores1"];
            $arrayTemporal = [];
            $cont = 0;
            $ingreso = '';
            for ($i=2; $i < 5; $i++) {            
                foreach ($op_asignados["indicadores$i"] as $key => $item){
                    $result= $this->coincidencias($i, $op_asignados['indicadores1'], $item['idoperador'], $item['nombre_apellido'], $item['asignados'], $item['aprobados']);
                    if (isset($result['idoperador'])) {
                            if (in_array($result['idoperador'], array_column($arrayTemporal, 'idoperador'))) {
                                foreach ($arrayTemporal as $key => $value) {
                                    if ($result['idoperador'] == $value['idoperador']) {
                                        $keys = array_keys($result);
                                        $arrayTemporal[$key]["$keys[5]"] = $result["$keys[5]"];
                                        array_push($datosT, $arrayTemporal[$key]);
                                        unset($arrayTemporal[$key]);              
                                    }
                                }
                            }else {
                                array_push($arrayTemporal, $result);
                                $ingreso = $result['idoperador'];
                                $arrayTemporal[$cont]=$result;
                                $cont++;
                            }
                    }else{
                        array_push($datosT[$key], $result);
                    }
                }                        
                
            }
            
            foreach ($arrayTemporal as $key => $value) {
                array_push($datosT, $value);
            }
            $datosTotales=[];
            $datosTotales['porcentaje']['objetivo_porcentaje'] = $objetivo_porcentaje;
            $datosTotales['porcentaje']['objetivos_dependientes'] = $objetivos_dependientes;
            $datosTotales['porcentaje']['objetivos_independientes'] = $objetivos_independientes;          
            foreach ($datosT as $k => $val):
                //SEMANA
                    $valor_semana_asignados_1 = 0;
                    $valor_semana_aprobados_1 = 0;
                    $valor_semana_reto_asignados_1 = 0;
                    $valor_semana_reto_aprobados_1 = 0;

                    $valor_semana_asignados_2 = 0;
                    $valor_semana_aprobados_2 = 0;
                    $valor_semana_reto_asignados_2 = 0;
                    $valor_semana_reto_aprobados_2 = 0;

                    $valor_semana_asignados_3 = 0;
                    $valor_semana_aprobados_3 = 0;
                    $valor_semana_reto_asignados_3 = 0;
                    $valor_semana_reto_aprobados_3 = 0;

                    $valor_semana_asignados_4 = 0;
                    $valor_semana_aprobados_4 = 0;
                    $valor_semana_reto_asignados_4 = 0;
                    $valor_semana_reto_aprobados_4 = 0;
                    // var_dump($datosT);
                        $datosTotales[$k]["idoperador"] = $val['idoperador'];
                        $datosTotales[$k]["nombre_apellido"] = $val['nombre_apellido'];
                        $datosTotales[$k]["asignados"] = $val['asignados'];
                //SEMANA 1
                    //dependientes
                        if (!isset($val['vacio'])) {
                            $periodo = 1;
                            $dat = $this->procesarMultiples($k, $periodo, $val, $asig_depen[1], $asig_indep[1],$aprob_depen[1],$rech_depen[1], $aprob_indep[1],$rech_indep[1]);
                            $datosTotales[$k]['semana_1'] = $dat;
                        }

                        if (isset($val[0]["periodo_2"]) || isset($val["periodo_2"])) {
                            $periodo = 2;
                            if (isset($val[0])) {
                                $dat = $this->procesarMultiples($k, $periodo, $val[0], $asig_depen[2], $asig_indep[2],$aprob_depen[2],$rech_depen[2], $aprob_indep[2],$rech_indep[2]);
                            }else {
                                $dat = $this->procesarMultiples($k, $periodo, $val['periodo_2'], $asig_depen[2], $asig_indep[2],$aprob_depen[2],$rech_depen[2], $aprob_indep[2],$rech_indep[2]);
                            }
                            $datosTotales[$k]['semana_2'] = $dat;
                        }
                        if (isset($val[1]["periodo_3"]) || isset($val["periodo_3"])) {
                            $periodo = 3;
                            if (isset($val[1])) {
                                $dat = $this->procesarMultiples($k, $periodo, $val[1], $asig_depen[3], $asig_indep[3],$aprob_depen[3],$rech_depen[3], $aprob_indep[3],$rech_indep[3]);
                            }else {
                                $dat = $this->procesarMultiples($k, $periodo, $val['periodo_3'], $asig_depen[3], $asig_indep[3],$aprob_depen[3],$rech_depen[3], $aprob_indep[3],$rech_indep[3]);
                            }
                            $datosTotales[$k]['semana_3'] = $dat;

                        }
                        if (isset($val[2]["periodo_4"]) || isset($val["periodo_4"])) {
                            $periodo = 4;
                            if (isset($val[2])) {
                                $dat = $this->procesarMultiples($k, $periodo, $val[2], $asig_depen[4], $asig_indep[4],$aprob_depen[4],$rech_depen[4], $aprob_indep[4],$rech_indep[4]);
                            }else {
                                $dat = $this->procesarMultiples($k, $periodo, $val['periodo_4'], $asig_depen[4], $asig_indep[4],$aprob_depen[4],$rech_depen[4], $aprob_indep[4],$rech_indep[4]);
                            }$datosTotales[$k]['semana_4'] = $dat;
                        }


            endforeach;
            $datosTotales["fechas"] = $dateFechas;
            $data=$datosTotales;
        }elseif ($tipo == 'btn_quincenal') {
            $dateQuincena=[];
            if ($diaInt < 15) {
                $quincenaAnteriorA = strtotime('-'.$diaInt.' day', strtotime($hoy));
                $quincenaAnteriorA = date('Y-m-d', $quincenaAnteriorA);
                $quincenaAnteriorB = strtotime('-15 day', strtotime($quincenaAnteriorA));
                $quincenaAnteriorB = date('Y-m-d', $quincenaAnteriorB);
            }else {
                $quincenaAnteriorA = $anho.'-'.$mes.'-15';
                $quincenaAnteriorB = strtotime('-15 day', strtotime($quincenaAnteriorA));
                $quincenaAnteriorB = date('Y-m-d', $quincenaAnteriorB);
            }
            $quincenaAnteriorC = strtotime('-15 day', strtotime($quincenaAnteriorB));
            $quincenaAnteriorC = date('Y-m-d', $quincenaAnteriorC);
            $quincena1 = "'".$quincenaAnteriorB." 00:00:00.000000' AND '".$quincenaAnteriorA." 23:59:59.000000'";
            $quincena2 = "'".$quincenaAnteriorC." 00:00:00.000000' AND '".$quincenaAnteriorB." 23:59:59.000000'";
            
            $op_asignados['indicadores'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$quincena1]);
            $op_asignados['indicadores2'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$quincena2]);

            for ($i=1; $i < 3; $i++) { 
                if ($i == 1) {
                    $date = $quincena1;
                    $rangoFechas = ['desde'=>date("d-m-Y", strtotime($quincenaAnteriorB)), 'hasta'=>date("d-m-Y", strtotime($quincenaAnteriorA))];
                }else {
                    $date = $quincena2;
                    $rangoFechas = ['desde'=>date("d-m-Y", strtotime($quincenaAnteriorC)), 'hasta'=>date("d-m-Y", strtotime($quincenaAnteriorB))];
                }

                $asig_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "dependiente"]);
                $asig_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "independiente"]);
                $aprob_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "dependiente"]);
                $rech_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "dependiente"]);
                $aprob_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "independiente"]);							
                $rech_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "independiente"]);	
                array_push($dateQuincena, ["fechas$i"=>$rangoFechas]);
            }

            $datosT =$op_asignados["indicadores"];
            $arrayTemporal = [];
            $cont = 0;
            $ingreso = '';        
                foreach ($op_asignados["indicadores2"] as $key => $item){
                    $result= $this->coincidencias(2, $op_asignados['indicadores'], $item['idoperador'], $item['nombre_apellido'], $item['asignados'], $item['aprobados']);
                    if (isset($result['idoperador'])) {
                            if (in_array($result['idoperador'], array_column($arrayTemporal, 'idoperador'))) {
                                foreach ($arrayTemporal as $key => $value) {
                                    if ($result['idoperador'] == $value['idoperador']) {
                                        $keys = array_keys($result);
                                        $arrayTemporal[$key]["$keys[5]"] = $result["$keys[5]"];
                                        array_push($datosT, $arrayTemporal[$key]);
                                        unset($arrayTemporal[$key]);              
                                    }
                                }
                            }else {
                                array_push($arrayTemporal, $result);
                                $ingreso = $result['idoperador'];
                                $arrayTemporal[$cont]=$result;
                                $cont++;
                            }
                    }else{
                        array_push($datosT[$key], $result);
                    }
                }        
            
            
            foreach ($arrayTemporal as $key => $value) {
                array_push($datosT, $value);
            }

            $datosTotalesQuincenas=[];
            $datosTotalesQuincenas['porcentaje']['objetivo_porcentaje'] = $objetivo_porcentaje;
            $datosTotalesQuincenas['porcentaje']['objetivos_dependientes'] = $objetivos_dependientes;
            $datosTotalesQuincenas['porcentaje']['objetivos_independientes'] = $objetivos_independientes;
            foreach ($datosT as $k => $val):
                //SEMANA
                    $valor_quincena_asignados_1 = 0;
                    $valor_quincena_aprobados_1 = 0;
                    $valor_quincena_reto_asignados_1 = 0;
                    $valor_quincena_reto_aprobados_1 = 0;

                    $valor_quincena_asignados_2 = 0;
                    $valor_quincena_aprobados_2 = 0;
                    $valor_quincena_reto_asignados_2 = 0;
                    $valor_quincena_reto_aprobados_2 = 0;

                        $datosTotalesQuincenas[$k]["idoperador"] = $val['idoperador'];
                        $datosTotalesQuincenas[$k]["nombre_apellido"] = $val['nombre_apellido'];
                        $datosTotalesQuincenas[$k]["asignados"] = $val['asignados'];

                    if (!isset($val['vacio'])) {
                        $periodo = 1;
                        $dat = $this->procesarMultiples($k, $periodo, $val, $asig_depen[1], $asig_indep[1],$aprob_depen[1],$rech_depen[1], $aprob_indep[1],$rech_indep[1]);
                        $datosTotalesQuincenas[$k]['quincena_1'] = $dat;
                    }

                    if (isset($val[0]["periodo_2"]) || isset($val["periodo_2"])) {
                            $periodo = 2;
                            if (isset($val[0])) {
                                $dat = $this->procesarMultiples($k, $periodo, $val[0], $asig_depen[2], $asig_indep[2],$aprob_depen[2],$rech_depen[2], $aprob_indep[2],$rech_indep[2]);
                            }else{
                                $dat = $this->procesarMultiples($k, $periodo, $val['periodo_2'], $asig_depen[2], $asig_indep[2],$aprob_depen[2],$rech_depen[2], $aprob_indep[2],$rech_indep[2]);
                            }
                                $datosTotalesQuincenas[$k]['quincena_2'] = $dat;
                        }
            endforeach;
            $datosTotalesQuincenas["fechas"] = $dateQuincena;
            $data=$datosTotalesQuincenas;
        }else{             
            $dateMes=[];
            $mesAntes= date("Y-m-d",strtotime($hoy."- 1 month"));
            $mesAntes2= date("Y-m-d",strtotime($mesAntes."- 1 month"));

            $mes1 = "'".$mesAntes." 00:00:00.000000' AND '".$hoy." 23:59:59.000000'";
            $mes2 = "'".date("Y-m-d",strtotime($mesAntes2))." 00:00:00.000000' AND '".date("Y-m-d",strtotime($mesAntes."- 1 day"))." 23:59:59.000000'";

            $op_asignados['indicadores'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$mes1]);
            $op_asignados['indicadores2'] = $this->originacion->operadores_asignados(['equipo' => $this->session->userdata('equipo'), 'fechas'=>$mes2]);
            
            for ($i=1; $i < 3; $i++) { 
                if ($i == 1) {
                    $date = $mes1;
                    $mesFechas = ['desde'=>date("d-m-Y", strtotime($mesAntes)), 'hasta'=>date("d-m-Y", strtotime($hoy))];
                }else {
                    $date = $mes2;
                    $mesFechas = ['desde'=>date("d-m-Y", strtotime($mesAntes2)), 'hasta'=>date("d-m-Y", strtotime($mesAntes."- 1 day"))];
                }
                $asig_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "dependiente"]);
                $asig_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "independiente"]);
                $aprob_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "dependiente"]);
                $rech_depen[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "dependiente"]);
                $aprob_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "independiente"]);							
                $rech_indep[$i] = $this->originacion->get_solicitudes_asignadas(1, ['dia' =>$date, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "independiente"]);	
                array_push($dateMes, ["fechas$i"=>$mesFechas]);
            }
            $datosT =$op_asignados["indicadores"];
            $arrayTemporal = [];
            $cont = 0;
            $ingreso = '';
                foreach ($op_asignados["indicadores2"] as $key => $item){
                    $result= $this->coincidencias(2, $op_asignados['indicadores'], $item['idoperador'], $item['nombre_apellido'], $item['asignados'], $item['aprobados']);
                    if (isset($result['idoperador'])) {
                            if (in_array($result['idoperador'], array_column($arrayTemporal, 'idoperador'))) {
                                foreach ($arrayTemporal as $key => $value) {
                                    if ($result['idoperador'] == $value['idoperador']) {
                                        $keys = array_keys($result);
                                        $arrayTemporal[$key]["$keys[5]"] = $result["$keys[5]"];
                                        array_push($datosT, $arrayTemporal[$key]);
                                        unset($arrayTemporal[$key]);              
                                    }
                                }
                            }else {
                                array_push($arrayTemporal, $result);
                                $ingreso = $result['idoperador'];
                                $arrayTemporal[$cont]=$result;
                                $cont++;
                            }
                    }else{
                        array_push($datosT[$key], $result);
                    }
                }    
            
            foreach ($arrayTemporal as $key => $value) {
                array_push($datosT, $value);
            }
            $datosTotalesMes=[];
            $datosTotalesMes['porcentaje']['objetivo_porcentaje'] = $objetivo_porcentaje;
            $datosTotalesMes['porcentaje']['objetivos_dependientes'] = $objetivos_dependientes;
            $datosTotalesMes['porcentaje']['objetivos_independientes'] = $objetivos_independientes;

            foreach ($datosT as $k => $val):
                //SEMANA
                    $valor_mes_asignados_1 = 0;
                    $valor_mes_aprobados_1 = 0;
                    $valor_mes_reto_asignados_1 = 0;
                    $valor_mes_reto_aprobados_1 = 0;

                    $valor_mes_asignados_2 = 0;
                    $valor_mes_aprobados_2 = 0;
                    $valor_mes_reto_asignados_2 = 0;
                    $valor_mes_reto_aprobados_2 = 0;

                        $datosTotalesMes[$k]["idoperador"] = $val['idoperador'];
                        $datosTotalesMes[$k]["nombre_apellido"] = $val['nombre_apellido'];
                        $datosTotalesMes[$k]["asignados"] = $val['asignados'];

                        if (!isset($val['vacio'])) {
                            $mes = 1;
                            $dat = $this->procesarMultiples($k, $mes, $val, $asig_depen[1], $asig_indep[1],$aprob_depen[1],$rech_depen[1], $aprob_indep[1],$rech_indep[1]);
                            $datosTotalesMes[$k]['mes_1'] = $dat;
                        }
                        if (isset($val[0]["periodo_2"]) || isset($val["periodo_2"])) {
                            $mes = 2;
                            if (isset($val[0])) {
                                $dat = $this->procesarMultiples($k, $mes, $val[0], $asig_depen[2], $asig_indep[2],$aprob_depen[2],$rech_depen[2], $aprob_indep[2],$rech_indep[2]);
                            }else{
                                $dat = $this->procesarMultiples($k, $mes, $val['periodo_2'], $asig_depen[2], $asig_indep[2],$aprob_depen[2],$rech_depen[2], $aprob_indep[2],$rech_indep[2]);
                            }
                            $datosTotalesMes[$k]['mes_2'] = $dat;
                        }
            endforeach;
            $datosTotalesMes["fechas"] = $dateMes;
            $data=$datosTotalesMes;
            }
        if (count($data) > 0) {
        
            $data['status'] = ['200', 'Registros del tablero cargados'];
        }else{
            $data['status'] = ['400', 'Hubo un error al cargar el tablero'];
        }
        echo json_encode($data);
    }
//Busco coincidecias de idoperador para juntar los arrays donde se encuentren todos a mostra en el tablero
    public function coincidencias($tipo, $arreglo, $buscar, $nombres, $asignados, $aprobados){
        $resultados=[];
        $i = 0;
        $arrayCom = [];
        $existe = '';
        foreach ($arreglo as $key => $value) {

            if(in_array($buscar, $value)){
                // var_dump('existe');
                $existe = 'si';
                break;
            }else {
                $existe = 'no';
            }
        }
        if ($existe == 'si') {
            if ($tipo == 2) {
                if (!array_key_exists("periodo_2", $resultados)) {
                        $resultados["periodo_2"] = ["idoperador_2"=>$buscar, "asignados_2" => $asignados, "aprobados_2" => $aprobados];
                    }
                }else if ($tipo == 3) {
                    if (!array_key_exists("periodo_3", $resultados)) {
                        $resultados["periodo_3"] = ["idoperador_3"=>$buscar, "asignados_3" => $asignados, "aprobados_3" => $aprobados];
                    }
                }else {
                    if (!array_key_exists("periodo_4", $resultados)) {
                        $resultados["periodo_4"] = ["idoperador_4"=>$buscar, "asignados_4" => $asignados, "aprobados_4" => $aprobados];
                    }
                }
                
        }else{
            $resultados=['idoperador' => $buscar, 'nombre_apellido' => $nombres, 'asignados'=> 0, 'aprobados'=>0, 'vacio'=>'1']; //
            if ($tipo == 2) {
                $resultados[0]["periodo_2"] = ["idoperador_2"=>$buscar, "asignados_2" => $asignados, "aprobados_2" => $aprobados];
            }elseif ($tipo == 3) {
                $resultados[1]["periodo_3"] = ["idoperador_3"=>$buscar, "asignados_3" => $asignados, "aprobados_3" => $aprobados];
            }else{
                $resultados[2]["periodo_4"] = ["idoperador_4"=>$buscar, "asignados_4" => $asignados, "aprobados_4" => $aprobados];
            }
        }
        return $resultados;
    }

// Proceso el array para obtener los valores del tablero
    public function procesarMultiples($cont, $periodo, $array, $asig_depen, $asig_indep,$aprob_depen,$rech_depen, $aprob_indep,$rech_indep){
                $valor_periodo_asignados_depen = 0;
                $valor_periodo_aprobados_depen = 0;
                $valor_periodo_rechazado_depen = 0;

                $valor_periodo_asignados_inde = 0;
                $valor_periodo_aprobados_inde = 0;
                $valor_periodo_rechazado_inde = 0;

                $valor_periodo_reto_aprobados = 0;
                $arreglo = [];
                if ($periodo == 1) {
                    $datoArray = $array["idoperador"];
                }else {
                    $datoArray = $array["periodo_$periodo"]["idoperador_$periodo"];
                }
                $is_in = in_array($datoArray, array_column($asig_depen, 'operador_asignado'));
                if($is_in){
                    $index = array_search($datoArray, array_column($asig_depen, 'operador_asignado'));
                    $valor_periodo_asignados_depen = $asig_depen[$index]['cantidad'];
                }
                $arreglo[$cont]["asignados_dependientes_$periodo"] = $valor_periodo_asignados_depen;
                
                $is_in = in_array($datoArray, array_column($aprob_depen, 'operador_asignado'));
                if($is_in){
                    $index = array_search($datoArray, array_column($aprob_depen, 'operador_asignado'));
                    $valor_periodo_aprobados_depen= $aprob_depen[$index]['cantidad'];
                } 
                $arreglo[$cont]["aprobados_dependientes_$periodo"] = $valor_periodo_aprobados_depen;

                $valor_periodo_rechazado_depen = 0;
                $is_in = in_array($datoArray, array_column($rech_depen, "operador_asignado"));
                
                if($is_in){
                    $index = array_search($datoArray, array_column($rech_depen, "operador_asignado"));
                    $valor_periodo_rechazado_depen= $rech_depen[$index]["cantidad"];
                } 
                $arreglo[$cont]["rechazado_dependientes_$periodo"] = $valor_periodo_rechazado_depen;
                //independientes
                $valor_periodo_asignados_inde = 0;
                $is_in = in_array($datoArray, array_column($asig_indep, "operador_asignado"));
                if($is_in){
                    $index = array_search($datoArray, array_column($asig_indep, "operador_asignado"));
                    $valor_periodo_asignados_inde= $asig_indep[$index]["cantidad"];
                }
                $arreglo[$cont]["asignados_independientes_$periodo"] = $valor_periodo_asignados_inde;

                $valor_periodo_aprobados_inde = 0;
                $is_in = in_array($datoArray, array_column($aprob_indep, "operador_asignado"));
                if($is_in){
                    $index = array_search($datoArray, array_column($aprob_indep, "operador_asignado"));
                    $valor_periodo_aprobados_inde= $aprob_indep[$index]["cantidad"];
                } 
                $arreglo[$cont]["aprobados_independientes_$periodo"] = $valor_periodo_aprobados_inde;

                $valor_periodo_rechazado_inde = 0;
                $is_in = in_array($datoArray, array_column($rech_indep, "operador_asignado"));
                if($is_in){
                    $index = array_search($datoArray, array_column($rech_indep, "operador_asignado"));
                    $valor_periodo_rechazado_inde= $rech_indep[$index]["cantidad"];
                } 
                $arreglo[$cont]["rechazado_independientes_$periodo"] = $valor_periodo_rechazado_inde;

                // if ($datoArray == '108') {
                //     var_dump($arreglo);die;
                // }
                return $arreglo;
    }
    
//Recibimos la ruta del tablero que selecionamos y los datos para mostrar en el
    public function obtener_tableros_originacion(){
        $data = $_POST['datos']['indicadores'];
        $ruta = $_POST['datos']['ruta'];
        $this->load->view($ruta, ['data' => $data]);
    }

    public function originacion_hoy_apro_csv($id_operador, $tipo, $nombreArchivo){
        // $fecha = date("Y-m-d");
        $fecha = date("Y-m-d");
        $primaria_hoy = $this->originacion->originacion_hoy_aprob($id_operador, $fecha, $tipo);
		$namecsv=$nombreArchivo;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primaria_hoy, $namecsv,"",true);
			
	}

	public function originacion_hoy_asig_csv($id_operador,  $tipo, $nombreArchivo){
        $fecha = date("Y-m-d");
		$equipo=['equipo' => $this->session->userdata('equipo')];

        $primarias_hoy_total = $this->originacion->originacion_hoy_asig($id_operador, $fecha, $tipo);
		$namecsv=$nombreArchivo;
		$flag_total=true;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primarias_hoy_total, $namecsv,$flag_total,true);
	}

    public function originacion_total_asig_csv($fechaDesde, $fechaHasta, $tipo, $nombreArchivo){
        $desde = date('Y-m-d', strtotime( $fechaDesde)); 
        $hasta = date('Y-m-d', strtotime( $fechaHasta));
		$equipo=['equipo' => $this->session->userdata('equipo')];
        $primarias_semanal_total = $this->originacion->originacion_asig_total($equipo, $desde, $hasta, $tipo);
		$namecsv=$nombreArchivo;
		$flag_total=true;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primarias_semanal_total, $namecsv,$flag_total,true);
	}

	public function originacion_total_aprob_csv($fechaDesde, $fechaHasta, $tipo, $nombreArchivo){
        $desde = date('Y-m-d', strtotime( $fechaDesde)); 
        $hasta = date('Y-m-d', strtotime( $fechaHasta));
		$equipo=['equipo' => $this->session->userdata('equipo')];
        $primarias_semanal_total = $this->originacion->originacion_aprob_total($equipo, $desde, $hasta, $tipo);
		$namecsv=$nombreArchivo;
		$flag_total=true;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primarias_semanal_total, $namecsv,$flag_total,true);
	}

    public function originacionApro_entre_csv($id_operador, $fechaDesde, $fechaHasta, $tipo, $nombreArchivo)
    {
        $desde = date('Y-m-d', strtotime( $fechaDesde)); 
        $hasta = date('Y-m-d', strtotime( $fechaHasta));
        $primaria_semanal = $this->originacion->originacionAprob_entre($id_operador, $desde, $hasta, $tipo);
        // var_dump($primaria_semanal);die;
		$namecsv=$nombreArchivo;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primaria_semanal, $namecsv,"",true);
    }

    public function originacionAsig_entre_csv($id_operador, $fechaDesde, $fechaHasta, $tipo, $nombreArchivo)
    {
        $desde = date('Y-m-d', strtotime( $fechaDesde)); 
        $hasta = date('Y-m-d', strtotime( $fechaHasta));
        $primaria_semanal = $this->originacion->originacionAsig_entre($id_operador, $desde, $hasta, $tipo);
		$namecsv=$nombreArchivo;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primaria_semanal, $namecsv,"",true);
    }

    //Genero el Reporte
    public function csv_generator($arreglo, $namecsv, $flag_total="",$estado_monto=false, $is_mora=false ) {
		// file name 
		if ($flag_total){
			$filename = $namecsv.'_'.date('d-m-Y').'.csv'; 
		}else{
			$filename = $namecsv.'_'.date('d-m-Y').'_'.$arreglo[0]["operador"].'.csv'; 
		}
		header("Content-type: application/csv");
		header('Content-Disposition: attachment; filename='.$filename);
		header("Content-Transfer-Encoding: UTF-8");
		header("Cache-Control: no-cache, no-store, must-relative");
		header("Pragma: no-cache");
		header("Expires: 0");	
		$archivo = fopen('php://output', 'w');
		if($estado_monto==true){
			if (isset($arreglo[0]['fecha_Asignacion'])) {
				$header = array("id","paso","fecha_alta","documento","nombres","apellidos","nombre_situacion", "tipo_solicitud", "respuesta_analisis","ofrecido","confirmado","estado","ID_operador","Operador","fecha_Asignacion"); 
			}else{
				$header = array("id","paso","fecha_alta","documento","nombres","apellidos","nombre_situacion", "tipo_solicitud", "respuesta_analisis","ofrecido","confirmado","estado","ID_operador","Operador"); 
			}
		}elseif($is_mora){
			if (isset($arreglo[0]['fecha_Asignacion'])) {
				$header = array("id","documento","Nombres","Apellidos","LABORAL", "monto_cobrar", "fecha_vencimiento","estado","dias_atraso" ,"Operador","fecha_Asignacion"); 
			}else{
				$header = array("id","documento","Nombres","Apellidos","LABORAL", "monto_cobrar", "fecha_vencimiento","estado","dias_atraso" ,"Operador"); 
			}
		} else {
			if (isset($arreglo[0]['fecha_Asignacion'])) {
				$header = array("id","paso","fecha_alta","documento","nombres","apellidos","nombre_situacion", "tipo_solicitud", "respuesta_analisis","estado","ID_operador","Operador","fecha_Asignacion"); 
			}else{
				$header = array("id","paso","fecha_alta","documento","nombres","apellidos","nombre_situacion", "tipo_solicitud", "respuesta_analisis","estado","ID_operador","Operador"); 
			}
		}
		fputcsv($archivo, $header,';');

		foreach ($arreglo as $key){ 
			fputcsv($archivo,$key,';'); 
		}
		fclose($archivo); 
		exit;
	}
}

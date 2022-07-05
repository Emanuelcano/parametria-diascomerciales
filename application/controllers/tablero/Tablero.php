<?php
// defined('BASEPATH') or exit('No direct script access allowed');

class Tablero extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata("is_logged_in")) 
        {
			$this->load->helper('url');
			$this->load->model("tablero/Tablero_model", "tablero");
			$this->load->model("operadores/Operadores_model", "operadores");
			$this->load->model("Chat", "chat");

		} else
		{
			redirect(base_url()."login/logout");
		}
	}

	public function index()
	{
		$link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/',$link);
        $permisos = FALSE;

        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;

        if ($permisos) 
        {
			$title['title'] = "Tablero";
			$this->load->view('layouts/adminLTE', $title);
			$this->load->view('tablero/tablero');
		} 
		else
		{
			redirect(base_url()."dashboard");
		}
	}

	public function indicadores()
	{

		$hoy=date('d-m-Y');

		$dia = date("d",strtotime($hoy));
		$mes = date("m",strtotime($hoy));
		$anho = date("Y",strtotime($hoy));

		$ayer = strtotime('-1 day', strtotime($hoy));
		$ayer = date('d-m-Y', $ayer);
		$nombre_dia = date("D",strtotime($ayer));

		if ($nombre_dia == 'Sun')
		{
			$ayer = strtotime('-3 day', strtotime($hoy));
			$ayer = date('d-m-Y', $ayer);
			$ayer = date("d",strtotime($ayer));
		} else 
		{
			$ayer = date("d",strtotime($ayer));
		}


		
		$data['indicadores'] = $this->tablero->get_opreadores_asignacion(['equipo' => $this->session->userdata('equipo'), 'tipo_operador'=> 1]);
		//consultamos el objetivo diario
		$data['objetivo'] =  $this->tablero->get_objetivo(['id' => 1])[0]->objetivo_porcentaje;

		$aux = $data['indicadores'];

		
		$estados = array("'APROBADO'","'TRANSFIRIENDO'","'PAGADO'");

//MES
		 $asignados_mes_primarios = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]);
		 $aprobados_mes_primarios = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados)]);
		
		 $asignados_mes_retanqueo = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO"]);
		 $aprobados_mes_retanqueo = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO", 'estado'=>implode(',',$estados)]);
		
//AYER
		 $asignados_ayer_primarios = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]);
		 $aprobados_ayer_primarios = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados)]);
		
		 $asignados_ayer_retanqueo = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO"]);
		 $aprobados_ayer_retanqueo = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO", 'estado'=>implode(',',$estados)]);
//Hoy

		//$asignados_hoy_primarios = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]);
		$asignados_hoy_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "dependiente"]);
		$asignados_hoy_independientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "independiente"]);
		//$aprobados_hoy_primarios = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados)]);
		$aprobados_hoy_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados), 'situacion' => "dependiente"]);
		$aprobados_hoy_independientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados), 'situacion' => "independiente"]);
		
		$asignados_hoy_retanqueo = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO"]);
		$aprobados_hoy_retanqueo = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO", 'estado'=>implode(',',$estados)]);

		//var_dump($asignados_ayer_primarios);die;
							
		foreach ($data['indicadores'] as $key => $item):

			//MES
					$valor_mes_asignados = 0;
					$valor_mes_aprobados = 0;
					$valor_mes_reto_asignados = 0;
					$valor_mes_reto_aprobados = 0;

					//primarios
					$is_in = in_array($item['idoperador'], array_column($asignados_mes_primarios, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_mes_primarios, 'operador_asignado'));
						$valor_mes_asignados= $asignados_mes_primarios[$index]['cantidad'];
						//var_dump($valor_mes_asignados);
					}
					$aux[$key]['mes']['asignados'] = $valor_mes_asignados;


					$is_in = in_array($item['idoperador'], array_column($aprobados_mes_primarios, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_mes_primarios, 'operador_asignado'));
						$valor_mes_aprobados= $aprobados_mes_primarios[$index]['cantidad'];
						//var_dump($valor_mes_aprobados);
					} 
					$aux[$key]['mes']['aprobados'] = $valor_mes_aprobados;
					//var_dump($asignados_mes_primarios); 
					
					//retanqueo
					$is_in = in_array($item['idoperador'], array_column($asignados_mes_retanqueo, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_mes_retanqueo, 'operador_asignado'));
						$valor_mes_reto_asignados= $asignados_mes_retanqueo[$index]['cantidad'];
					} 
					$aux[$key]['mes']['reto_asignados'] = $valor_mes_reto_asignados;

					$is_in = in_array($item['idoperador'], array_column($aprobados_mes_retanqueo, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_mes_retanqueo, 'operador_asignado'));
						$valor_mes_reto_aprobados= $aprobados_mes_retanqueo[$index]['cantidad'];
					} 
					$aux[$key]['mes']['reto_aprobados'] = $valor_mes_reto_aprobados;

					// var_dump($item['idoperador']);
					// var_dump($aux[$key]['mes']);continue;

			//AYER

					$valor_ayer_asignados = 0;
					$valor_ayer_aprobados = 0;
					$valor_ayer_reto_asignados = 0;
					$valor_ayer_reto_aprobados = 0;

					//primarios
					$is_in = in_array($item['idoperador'], array_column($asignados_ayer_primarios, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_ayer_primarios, 'operador_asignado'));
						$valor_ayer_asignados= $asignados_ayer_primarios[$index]['cantidad'];
					}
					$aux[$key]['ayer']['asignados'] = $valor_ayer_asignados;


					$is_in = in_array($item['idoperador'], array_column($aprobados_ayer_primarios, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_ayer_primarios, 'operador_asignado'));
						$valor_ayer_aprobados= $aprobados_ayer_primarios[$index]['cantidad'];
					} 
					$aux[$key]['ayer']['aprobados'] = $valor_ayer_aprobados;

					//retanqueo
					$is_in = in_array($item['idoperador'], array_column($asignados_ayer_retanqueo, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_ayer_retanqueo, 'operador_asignado'));
						$valor_ayer_reto_asignados= $asignados_ayer_retanqueo[$index]['cantidad'];
					} 
					$aux[$key]['ayer']['reto_asignados'] = $valor_ayer_reto_asignados;

					$is_in = in_array($item['idoperador'], array_column($aprobados_ayer_retanqueo, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_ayer_retanqueo, 'operador_asignado'));
						$valor_ayer_reto_aprobados= $aprobados_ayer_retanqueo[$index]['cantidad'];
					} 
					$aux[$key]['ayer']['reto_aprobados'] = $valor_ayer_reto_aprobados;

					/*var_dump($item['idoperador']);
					var_dump($asignados_ayer_retanqueo);
					var_dump($aprobados_ayer_retanqueo);
					var_dump($aux[$key]['ayer']);continue;
					*/

			//HOY

					$valor_hoy_asignados = 0;
					$valor_hoy_aprobados = 0;
					$valor_hoy_reto_asignados = 0;
					$valor_hoy_reto_aprobados = 0;

					//primarios
					/*
					 	$is_in = in_array($item['idoperador'], array_column($asignados_hoy_primarios, 'operador_asignado'));
						if($is_in){
							$index = array_search($item['idoperador'], array_column($asignados_hoy_primarios, 'operador_asignado'));
							$valor_hoy_asignados= $asignados_hoy_primarios[$index]['cantidad'];
						}
						$aux[$key]['hoy']['asignados-dependeintes'] = $valor_hoy_asignados;
					*/
					//dependientes
					$is_in = in_array($item['idoperador'], array_column($asignados_hoy_dependientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_hoy_dependientes, 'operador_asignado'));
						$valor_hoy_asignados= $asignados_hoy_dependientes[$index]['cantidad'];
					}
					$aux[$key]['hoy']['asignados-dependientes'] = $valor_hoy_asignados;
					
					//independientes
					$valor_hoy_asignados = 0;
					$is_in = in_array($item['idoperador'], array_column($asignados_hoy_independientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_hoy_independientes, 'operador_asignado'));
						$valor_hoy_asignados= $asignados_hoy_independientes[$index]['cantidad'];
					}
					$aux[$key]['hoy']['asignados-independientes'] = $valor_hoy_asignados;


					/*
						$is_in = in_array($item['idoperador'], array_column($aprobados_hoy_primarios, 'operador_asignado'));
						if($is_in){
							$index = array_search($item['idoperador'], array_column($aprobados_hoy_primarios, 'operador_asignado'));
							$valor_hoy_aprobados= $aprobados_hoy_primarios[$index]['cantidad'];
						} 
						$aux[$key]['hoy']['aprobados'] = $valor_hoy_aprobados;
					*/

					//dependientes
					$is_in = in_array($item['idoperador'], array_column($aprobados_hoy_dependientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_hoy_dependientes, 'operador_asignado'));
						$valor_hoy_aprobados= $aprobados_hoy_dependientes[$index]['cantidad'];
					} 
					$aux[$key]['hoy']['aprobados-dependientes'] = $valor_hoy_aprobados;

					//Independientes
					$valor_hoy_aprobados = 0;
					$is_in = in_array($item['idoperador'], array_column($aprobados_hoy_independientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_hoy_independientes, 'operador_asignado'));
						$valor_hoy_aprobados= $aprobados_hoy_independientes[$index]['cantidad'];
					} 
					$aux[$key]['hoy']['aprobados-independientes'] = $valor_hoy_aprobados;

					//retanqueo
					$is_in = in_array($item['idoperador'], array_column($asignados_hoy_retanqueo, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_hoy_retanqueo, 'operador_asignado'));
						$valor_hoy_reto_asignados= $asignados_hoy_retanqueo[$index]['cantidad'];
					} 
					$aux[$key]['hoy']['reto_asignados'] = $valor_hoy_reto_asignados;

					$is_in = in_array($item['idoperador'], array_column($aprobados_hoy_retanqueo, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_hoy_retanqueo, 'operador_asignado'));
						$valor_hoy_reto_aprobados= $aprobados_hoy_retanqueo[$index]['cantidad'];
					} 
					$aux[$key]['hoy']['reto_aprobados'] = $valor_hoy_reto_aprobados;

					$sin_atender = $this->chat->get_chat_sin_atender($item['idoperador']);
					$iniciados =  $this->chat->get_chat_iniciados($item['idoperador']);

					$aux[$key]['whatsapp'] = [
						'sin_atender' => (($sin_atender == 0)? $sin_atender: $sin_atender[0]->cantidad),
						'iniciados' => (($iniciados == 0)? $iniciados: $iniciados[0]->cantidad)
					];


					
			/*
			$aux[$key] +=[
							'mes' => [ 
								'asignados' => $this->tablero->get_solicitudes_asignadas($item['idoperador'], ['mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]),
								'aprobados' => $this->tablero->get_solicitudes_aprobadas($item['idoperador'], ['mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>$estados]),
								'reto_asignados' => $this->tablero->get_solicitudes_asignadas($item['idoperador'], ['mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO"]),
								'reto_aprobados' => $this->tablero->get_solicitudes_aprobadas($item['idoperador'], ['mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO"])
							],
							'ayer' => [
								'asignados' => $this->tablero->get_solicitudes_asignadas($item['idoperador'], ['dia' => $ayer, 'mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]),
								'aprobados' => $this->tablero->get_solicitudes_aprobadas($item['idoperador'], ['dia' => $ayer, 'mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]),
								'reto_asignados' => $this->tablero->get_solicitudes_asignadas($item['idoperador'], ['dia' => $ayer, 'mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'),'tipo_solicitud'=>"RETANQUEO"]),
								'reto_aprobados' => $this->tablero->get_solicitudes_aprobadas($item['idoperador'], ['dia' => $ayer, 'mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'),'tipo_solicitud'=>"RETANQUEO"])
							],
							'hoy' => [
								'asignados' => $this->tablero->get_solicitudes_asignadas($item['idoperador'], ['dia' => $dia, 'mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]),
								'aprobados' => $this->tablero->get_solicitudes_aprobadas($item['idoperador'], ['dia' => $dia, 'mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'),'tipo_solicitud'=>"PRIMARIA"]),
								'reto_asignados' => $this->tablero->get_solicitudes_asignadas($item['idoperador'], ['dia' => $dia, 'mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO"]),
								'reto_aprobados' => $this->tablero->get_solicitudes_aprobadas($item['idoperador'], ['dia' => $dia, 'mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"RETANQUEO"])
							],
							'whatsapp' => [
								'sin_atender' => count($this->chat->get_chat_sin_atender($item['idoperador'])),
								'iniciados' => count($this->chat->get_chat_iniciados($item['idoperador']))
							],
							//'llamadas' => [
							//	'ayer' => count($this->operadores->get_llamadas_realizadas(['id_operador' => $item['idoperador'], 'ayer' => $ayer])),
						//		'mes' => count($this->operadores->get_llamadas_realizadas(['id_operador' => $item['idoperador'],  'mes' => $mes])),
						//	],
						];
						*/
						
		endforeach;
		//var_dump($aux);	
		$data['indicadores'] = $aux;
		
		$this->load->view('tablero/indicadores',['data' => $data]);
	}

	public function ultimo_viernes(){
		$hoy=date('d-m-Y');

		$dia = date("d",strtotime($hoy));
		$mes = date("m",strtotime($hoy));
		$anho = date("Y",strtotime($hoy));

		$ayer = strtotime('-1 day', strtotime($hoy));
		$ayer = date('d-m-Y', $ayer);
		$nombre_dia = date("D",strtotime($ayer));

		if ($nombre_dia == 'Sun')
		{
			$ayer = strtotime('-3 day', strtotime($hoy));
			$ayer = date('d-m-Y', $ayer);
			$ayer = date("d",strtotime($ayer));
		} else 
		{
			$ayer = date("d",strtotime($ayer));
		}
		$fecha_ayer= $anho.'-'.$mes.'-'.$ayer;	
		return $fecha_ayer;
	}

	public function primarias_hoy($id_operador, $tipo, $estado = null){
        $primaria_hoy = $this->tablero->primarias_hoy($id_operador, $tipo, $estado);
		$namecsv='primarias_hoy';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primaria_hoy, $namecsv,"",true);
			
	}
	public function primarias_mes($id_operador, $tipo, $estado = null){
        $primarias_mes = $this->tablero->primarias_mes($id_operador, $tipo, $estado);
		$namecsv='primarias_mes';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primarias_mes, $namecsv);
	}
	public function primarias_mes_total($tipo, $estado = null){
		$equipo=['equipo' => $this->session->userdata('equipo')];
        $primarias_mes_total = $this->tablero->primarias_mes_total($equipo, $tipo, $estado);
		$namecsv='primarias_mes_total';
		$flag_total=true;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primarias_mes_total, $namecsv,$flag_total);
	}

	public function retanqueo_mes($id_operador){
        $retanqueo_mes = $this->tablero->retanqueo_mes($id_operador);
		$namecsv='retanqueo_mes';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($retanqueo_mes, $namecsv);
	}
	public function retanqueo_mes_total(){
		$equipo=['equipo' => $this->session->userdata('equipo')];

        $retanqueo_mes_total = $this->tablero->retanqueo_mes_total($equipo);
		$namecsv='retanqueo_mes_total';
		$flag_total=true;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($retanqueo_mes_total, $namecsv,$flag_total);
	}


	public function primarias_hoy_total($tipo, $estado = null){
		$equipo=['equipo' => $this->session->userdata('equipo')];

        $primarias_hoy_total = $this->tablero->primarias_hoy_total($equipo, $tipo, $estado);
		$namecsv='primarias_hoy_total';
		$flag_total=true;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primarias_hoy_total, $namecsv,$flag_total,true);
	}

	public function retanqueo_hoy($id_operador){
        $retanqueo_hoy = $this->tablero->retanqueo_hoy($id_operador);
		$namecsv='retanqueo_hoy';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($retanqueo_hoy, $namecsv);
	}
	public function retanqueo_hoy_total(){
		$equipo=['equipo' => $this->session->userdata('equipo')];

        $retanqueo_hoy_total = $this->tablero->retanqueo_hoy_total($equipo);
		$namecsv='retanqueo_hoy_total';
		$flag_total=true;
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($retanqueo_hoy_total, $namecsv,$flag_total);
	}

	public function primarias_ayer_totales($tipo, $estado = null){
		$equipo=['equipo' => $this->session->userdata('equipo')];

		$flag_total=true;
		$fecha_ayer = $this->ultimo_viernes();
        $primarias_ayer_totales = $this->tablero->primarias_ayer_totales($fecha_ayer,$equipo, $tipo, $estado);
		$namecsv='primarias_ayer_totales';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primarias_ayer_totales, $namecsv, $flag_total);
	}
	public function primarias_ayer($id_operador, $tipo, $estado = null){
		$fecha_ayer = $this->ultimo_viernes();
        $primarias_ayer = $this->tablero->primarias_ayer($id_operador,$fecha_ayer, $tipo, $estado);
		$namecsv='primarias_ayer';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($primarias_ayer, $namecsv);
	}

	public function retanqueo_ayer($id_operador){
		$fecha_ayer = $this->ultimo_viernes();
        $retanqueo_ayer = $this->tablero->retanqueo_ayer($id_operador,$fecha_ayer);
		$namecsv='retanqueo_ayer';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($retanqueo_ayer, $namecsv);
	}
	public function retanqueo_ayer_total(){
		$equipo=['equipo' => $this->session->userdata('equipo')];
		$flag_total=true;
		$fecha_ayer = $this->ultimo_viernes();
        $retanqueo_ayer_total = $this->tablero->retanqueo_ayer_total($fecha_ayer,$equipo);
		$namecsv='retanqueo_ayer_total';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($retanqueo_ayer_total, $namecsv, $flag_total);
	}
	
	public function mora_mes($id_operador){
		$objetivos = $this->tablero->get_objetivo_mora(['id' => 1]);
		$fecha_mora_mostrar =  $objetivos[0]->fecha_mora_mostrar;
		$periodo_mas_mora = date ( 'Y-m-j' , strtotime ( '+2 day' , strtotime ( $fecha_mora_mostrar ) ) );
		$periodo_menos_mora = date ( 'Y-m-j' , strtotime ( '-2 day' , strtotime ( $fecha_mora_mostrar ) ) );
        $mora_mes = $this->tablero->mora_mes($id_operador, $periodo_mas_mora, $periodo_menos_mora);
		$namecsv='mora_mes';
		if ($this->session->userdata('tipo_operador') != 1 && $this->session->userdata('tipo_operador') != 4 || $this->session->userdata('tipo_operador') != 5 ||$this->session->userdata('tipo_operador') != 6 ||$this->session->userdata('tipo_operador') != 14) 
			$this->csv_generator($mora_mes, $namecsv, "", false, true);
	}


	public function csv_generator($arreglo, $namecsv, $flag_total="",$estado_monto=false, $is_mora=false ) {

		// var_dump('aqui');die;
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
				$header = array("id","paso","fecha_alta","documento","nombres","apellidos","nombre_situacion", "tipo_solicitud", "respuesta_analisis","ofrecido","confirmado","estado","ID_operador","Operador","fecha_Asignacion", "fecha_ultimo_track", "hora_ultimo_track", "ultimo_track", "operador"); 
			}else{
				$header = array("id","paso","fecha_alta","documento","nombres","apellidos","nombre_situacion", "tipo_solicitud", "respuesta_analisis","ofrecido","confirmado","estado","ID_operador","Operador", "fecha_ultimo_track", "hora_ultimo_track", "ultimo_track", "operador"); 
			}
		}elseif($is_mora){
			if (isset($arreglo[0]['fecha_Asignacion'])) {
				$header = array("id","documento","Nombres","Apellidos","LABORAL", "monto_cobrar", "fecha_vencimiento","estado","dias_atraso" ,"Operador","fecha_Asignacion", "fecha_ultimo_track", "hora_ultimo_track", "ultimo_track", "operador"); 
			}else{
				$header = array("id","documento","Nombres","Apellidos","LABORAL", "monto_cobrar", "fecha_vencimiento","estado","dias_atraso" ,"Operador", "fecha_ultimo_track", "hora_ultimo_track", "ultimo_track", "operador"); 
			}
		} else {
			if (isset($arreglo[0]['fecha_Asignacion'])) {
				$header = array("id","paso","fecha_alta","documento","nombres","apellidos","nombre_situacion", "tipo_solicitud", "respuesta_analisis","estado","ID_operador","Operador","fecha_Asignacion", "fecha_ultimo_track", "hora_ultimo_track", "ultimo_track", "operador"); 
			}else{
				$header = array("id","paso","fecha_alta","documento","nombres","apellidos","nombre_situacion", "tipo_solicitud", "respuesta_analisis","estado","ID_operador","Operador", "fecha_ultimo_track", "hora_ultimo_track", "ultimo_track", "operador"); 
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

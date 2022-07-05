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
			$title['title'] = "Tablero de Mora";
			$this->load->view('layouts/adminLTE', $title);
			$this->load->view('mora/tablero_mora');
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
		$last = date("Y-m-t", strtotime(date('Y-m-d')));
		$data['indicadores'] = $this->tablero->get_opreadores_asignacion(['equipo' => $this->session->userdata('equipo'), 'sub'=>1, 'inicio'=>"$anho-$mes-01", 'fin'=> $last]);
		//consultamos el objetivo diario
		$objetivos = $this->tablero->get_objetivo_mora(['id' => 1]);

		$objetivo_porcentaje =  $objetivos[0]->objetivo_porcentaje;
		$objetivos_dependientes =  $objetivos[0]->objetivo_dependientes;
		$objetivos_independientes =  $objetivos[0]->objetivos_independientes;
		$fecha_mora_mostrar =  $objetivos[0]->fecha_mora_mostrar;
		$objetivo_mora =  $objetivos[0]->objetivo_mora;
		$proximo_vencimiento =  $objetivos[0]->proximo_vencimiento;

		
		$aux = $data['indicadores'];
		// var_dump($aux);die;
		$estados[] = array('"APROBADO"','"TRANSFIRIENDO"','"PAGADO"');
		$estados[] = array('"RECHAZADO"');
		$periodo_mas = strtotime ( '+2 day' , strtotime ( $fecha_mora_mostrar ) ) ;
		$periodo_mas_mora = date ( 'Y-m-j' , $periodo_mas );
		$periodo_menos = strtotime ( '-2 day' , strtotime ( $fecha_mora_mostrar ) ) ;
		$periodo_menos_mora = date ( 'Y-m-j' , $periodo_menos );
		//Proximo_vencimiento//
		$proximo_vencimiento_mas = strtotime ( '+2 day' , strtotime ( $proximo_vencimiento ) ) ;
		$vencimiento_mas = date ( 'Y-m-j' , $proximo_vencimiento_mas );
		$proximo_vencimiento_menos = strtotime ( '-2 day' , strtotime ( $proximo_vencimiento ) ) ;
		$vencimiento_menos = date ( 'Y-m-j' , $proximo_vencimiento_menos );
		//MES Mora
			// $aprobados_mora_dependientes = $this->tablero->mora_solicitudes_asignadas(1, ['fecha' =>"$fecha_mora_mostrar", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>'mora', 'situacion' => "dependiente"]);
			// $aprobados_mora_independientes = $this->tablero->mora_solicitudes_asignadas(1, ['fecha' =>"$fecha_mora_mostrar", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>'mora', 'situacion' => "independiente"]);
			// $mora_mes_dependiente = $this->tablero->mora($periodo_mas_mora,$periodo_menos_mora,"in (1,4,7)");
			// $mora_mes_independiente = $this->tablero->mora($periodo_mas_mora,$periodo_menos_mora,"=3");
			
			$caso_total = $this->tablero->mora_total($periodo_mas_mora,$periodo_menos_mora,"");
			$mora_total = $this->tablero->mora_total($periodo_mas_mora,$periodo_menos_mora,"1");

		//MES
			$asignados_mes_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "dependiente"]);
			$asignados_mes_independientes = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "independiente"]);
			$aprobados_mes_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "dependiente"]);
			// var_dump($aprobados_mes_dependientes);die;
			$rechazado_mes_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "dependiente"]);
			$aprobados_mes_independientes = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "independiente"]);
			$rechazado_mes_independientes = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "independiente"]);
			$asignados_mes_primarios = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]);
		 	$aprobados_mes_primarios = $this->tablero->get_solicitudes_asignadas(1, ['fecha' =>"$anho-$mes-01", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0])]);
		//AYER
			$asignados_ayer_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "dependiente"]);
			$asignados_ayer_independientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "independiente"]);
			$aprobados_ayer_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "dependiente"]);
			$rechazado_ayer_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "dependiente"]);
			$aprobados_ayer_independientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "independiente"]);
			$rechazado_ayer_independientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "independiente"]);
			//$asignados_ayer_primarios = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA"]);
			//$aprobados_ayer_primarios = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$ayer", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados)]);
		//Hoy
			$asignados_hoy_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "dependiente"]);
			$asignados_hoy_independientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'situacion' => "independiente"]);
			$aprobados_hoy_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "dependiente"]);
			$rechazado_hoy_dependientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "dependiente"]);
			$aprobados_hoy_independientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[0]), 'situacion' => "independiente"]);							
			$rechazado_hoy_independientes = $this->tablero->get_solicitudes_asignadas(1, ['dia' =>"$anho-$mes-$dia", 'equipo' => $this->session->userdata('equipo'), 'tipo_solicitud'=>"PRIMARIA", 'estado'=>implode(',',$estados[1]), 'situacion' => "independiente"]);							
		//Chats activos
		// $chats_activos = $this->tablero->chats_activos($vencimiento_menos, $vencimiento_mas);
		$chats_activos = array();
		$chats_activos_operador = json_decode(json_encode($chats_activos), true);
		//Templates enviados
		$fecha_hoy=date('Y-m-d');
		// $templates_enviados = $this->tablero->templates_enviados($vencimiento_menos, $vencimiento_mas);
		$templates_enviados	= array();
		$templates_enviados_operador = json_decode(json_encode($templates_enviados), true);

		foreach ($data['indicadores'] as $key => $item):
			//Porcentaje
			$aux[$key]['porcentaje']['objetivo_porcentaje'] = $objetivo_porcentaje;
			$aux[$key]['porcentaje']['objetivos_dependientes'] = $objetivos_dependientes;
			$aux[$key]['porcentaje']['objetivos_independientes'] = $objetivos_independientes;
			$aux[$key]['porcentaje']['objetivo_mora'] = $objetivo_mora;
			$aux[$key]['fecha']['rango'] = $fecha_mora_mostrar;
			$aux[$key]['fecha']['fecha_vencimiento'] = $proximo_vencimiento;
			//CHATS- TEMPLATES- LLAMADAS
				//Chats enviados por operador
				$valor_chats_total=0;
				$is_in = in_array($item['idoperador'], array_column($chats_activos_operador, 'id_operador'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($chats_activos_operador, 'id_operador'));
					$valor_chats_total= $chats_activos_operador[$index]['cantidad'];
				}
				$aux[$key]['chats']['chats_total'] = $valor_chats_total;
				//Templates enviados por operador
				$valor_templates_total=0;
				$is_in = in_array($item['idoperador'], array_column($templates_enviados_operador, 'id_operador'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($templates_enviados_operador, 'id_operador'));
					$valor_templates_total= $templates_enviados_operador[$index]['cantidad'];
				}
				$aux[$key]['templates']['templates_total'] = $valor_templates_total;
			//MES MORA
				//Mora total
				$valor_mora_total=0;
				$is_in = in_array($item['idoperador'], array_column($mora_total, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($mora_total, 'operador_asignado'));
					$valor_mora_total= $mora_total[$index]['cantidad'];
				}
				$aux[$key]['mora']['valor_mora_total'] = $valor_mora_total;
				//Total de casos
				$valor_caso_total=0;
				$is_in = in_array($item['idoperador'], array_column($caso_total, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($caso_total, 'operador_asignado'));
					$valor_caso_total= $caso_total[$index]['cantidad'];
				}
				$aux[$key]['mora']['valor_caso_total'] = $valor_caso_total;
				//dependientes
				// $valor_mora_tipo_laboral=0;
				// $is_in = in_array($item['idoperador'], array_column($aprobados_mora_dependientes, 'operador_asignado'));
				// if($is_in){
				// 	$index = array_search($item['idoperador'], array_column($aprobados_mora_dependientes, 'operador_asignado'));
				// 	$valor_mora_tipo_laboral= $aprobados_mora_dependientes[$index]['tipo_laboral'];
				// }
				// $aux[$key]['mora']['asignados-dependientes_tipo_laboral'] = $valor_mora_tipo_laboral;
				
				// $valor_mora_asignados=0;
				// $is_in = in_array($item['idoperador'], array_column($mora_mes_dependiente, 'operador_asignado'));
				// 	if($is_in){
				// 		$index = array_search($item['idoperador'], array_column($mora_mes_dependiente, 'operador_asignado'));
				// 		$valor_mora_asignados= $mora_mes_dependiente[$index]['cantidad'];
				// 	}
				// $aux[$key]['mora']['mora_mes_dependiente'] = $valor_mora_asignados;
				//independientes
				// $valor_mora_tipo_laboral=0;
				// $is_in = in_array($item['idoperador'], array_column($aprobados_mora_independientes, 'operador_asignado'));
				// if($is_in){
				// 	$index = array_search($item['idoperador'], array_column($aprobados_mora_independientes, 'operador_asignado'));
				// 	$valor_mora_tipo_laboral= $aprobados_mora_independientes[$index]['tipo_laboral'];
				// }
				// $aux[$key]['mora']['asignados-independientes_tipo_laboral'] = $valor_mora_tipo_laboral;
			
				// $valor_mora_asignados=0;
				// $is_in = in_array($item['idoperador'], array_column($mora_mes_independiente, 'operador_asignado'));
				// 	if($is_in){
				// 		$index = array_search($item['idoperador'], array_column($mora_mes_independiente, 'operador_asignado'));
				// 		$valor_mora_asignados= $mora_mes_independiente[$index]['cantidad'];
				// 	}
				// $aux[$key]['mora']['mora_mes_independiente'] = $valor_mora_asignados;
			//MES
				$valor_mes_asignados = 0;
				$is_in = in_array($item['idoperador'], array_column($asignados_mes_dependientes, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($asignados_mes_dependientes, 'operador_asignado'));
					$valor_mes_asignados= $asignados_mes_dependientes[$index]['cantidad'];
				}
				$aux[$key]['mes']['asignados-dependientes'] = $valor_mes_asignados;	
				//independientes
				$valor_mes_asignados = 0;
				$is_in = in_array($item['idoperador'], array_column($asignados_mes_independientes, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($asignados_mes_independientes, 'operador_asignado'));
					$valor_mes_asignados= $asignados_mes_independientes[$index]['cantidad'];
				}
				$aux[$key]['mes']['asignados-independientes'] = $valor_mes_asignados;
				$valor_mes_aprobados = 0;
				$is_in = in_array($item['idoperador'], array_column($aprobados_mes_dependientes, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($aprobados_mes_dependientes, 'operador_asignado'));
					$valor_mes_aprobados= $aprobados_mes_dependientes[$index]['cantidad'];
				} 
				$aux[$key]['mes']['aprobados-dependientes'] = $valor_mes_aprobados;
				$valor_mes_rechazado = 0;
				$is_in = in_array($item['idoperador'], array_column($rechazado_mes_dependientes, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($rechazado_mes_dependientes, 'operador_asignado'));
					$valor_mes_rechazado= $rechazado_mes_dependientes[$index]['cantidad'];
				} 
				$aux[$key]['mes']['rechazado-dependientes'] = $valor_mes_rechazado;

				//Independientes
				$valor_mes_aprobados = 0;
				$is_in = in_array($item['idoperador'], array_column($aprobados_mes_independientes, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($aprobados_mes_independientes, 'operador_asignado'));
					$valor_mes_aprobados= $aprobados_mes_independientes[$index]['cantidad'];
				} 
				$aux[$key]['mes']['aprobados-independientes'] = $valor_mes_aprobados;
				$valor_mes_aprobados = 0;
				$is_in = in_array($item['idoperador'], array_column($rechazado_mes_independientes, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($rechazado_mes_independientes, 'operador_asignado'));
					$valor_mes_aprobados= $rechazado_mes_independientes[$index]['cantidad'];
				} 
				$aux[$key]['mes']['rechazado-independientes'] = $valor_mes_aprobados;
				$valor_mes_aprobados = 0;
				//primarios
				$is_in = in_array($item['idoperador'], array_column($asignados_mes_primarios, 'operador_asignado'));
				if($is_in){
					$index = array_search($item['idoperador'], array_column($asignados_mes_primarios, 'operador_asignado'));
					$valor_mes_asignados= $asignados_mes_primarios[$index]['cantidad'];
				}
				$aux[$key]['mes']['asignados'] = $valor_mes_asignados;
				
			//AYER
				$valor_ayer_asignados = 0;
				$valor_ayer_aprobados = 0;
				$valor_ayer_reto_asignados = 0;
				$valor_ayer_reto_aprobados = 0;
					
				//dependientes
					$is_in = in_array($item['idoperador'], array_column($asignados_ayer_dependientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_ayer_dependientes, 'operador_asignado'));
						$valor_ayer_asignados= $asignados_ayer_dependientes[$index]['cantidad'];
					}
					$aux[$key]['ayer']['asignados-dependientes'] = $valor_ayer_asignados;

					$is_in = in_array($item['idoperador'], array_column($aprobados_ayer_dependientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_ayer_dependientes, 'operador_asignado'));
						$valor_ayer_aprobados= $aprobados_ayer_dependientes[$index]['cantidad'];
					} 
					$aux[$key]['ayer']['aprobados-dependientes'] = $valor_ayer_aprobados;

					$valor_ayer_rechazado = 0;
					$is_in = in_array($item['idoperador'], array_column($rechazado_ayer_dependientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($rechazado_ayer_dependientes, 'operador_asignado'));
						$valor_ayer_rechazado= $rechazado_ayer_dependientes[$index]['cantidad'];
					} 
					$aux[$key]['ayer']['rechazado-dependientes'] = $valor_ayer_rechazado;
				//independientes
					$valor_ayer_asignados = 0;
					$is_in = in_array($item['idoperador'], array_column($asignados_ayer_independientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_ayer_independientes, 'operador_asignado'));	
						$valor_ayer_asignados= $asignados_ayer_independientes[$index]['cantidad'];
					}
					$aux[$key]['ayer']['asignados-independientes'] = $valor_ayer_asignados;
					
					$valor_ayer_aprobados = 0;
					$is_in = in_array($item['idoperador'], array_column($aprobados_ayer_independientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_ayer_independientes, 'operador_asignado'));
						$valor_ayer_aprobados= $aprobados_ayer_independientes[$index]['cantidad'];
					} 
					$aux[$key]['ayer']['aprobados-independientes'] = $valor_ayer_aprobados;
					
					$valor_ayer_rechazado = 0;
					$is_in = in_array($item['idoperador'], array_column($rechazado_ayer_independientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($rechazado_ayer_independientes, 'operador_asignado'));
						$valor_ayer_rechazado= $rechazado_ayer_independientes[$index]['cantidad'];
					} 
					$aux[$key]['ayer']['rechazado-independientes'] = $valor_ayer_rechazado;
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
					$aux[$key]['hoy']['asignados-dependientes'] = $valor_hoy_asignados;

					$is_in = in_array($item['idoperador'], array_column($aprobados_hoy_dependientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_hoy_dependientes, 'operador_asignado'));
						$valor_hoy_aprobados= $aprobados_hoy_dependientes[$index]['cantidad'];
					} 
					$aux[$key]['hoy']['aprobados-dependientes'] = $valor_hoy_aprobados;

					$valor_hoy_rechazado = 0;
					$is_in = in_array($item['idoperador'], array_column($rechazado_hoy_dependientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($rechazado_hoy_dependientes, 'operador_asignado'));
						$valor_hoy_rechazado= $rechazado_hoy_dependientes[$index]['cantidad'];
					} 
					$aux[$key]['hoy']['rechazado-dependientes'] = $valor_hoy_rechazado;

				//independientes
					$valor_hoy_asignados = 0;
					$is_in = in_array($item['idoperador'], array_column($asignados_hoy_independientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($asignados_hoy_independientes, 'operador_asignado'));
						$valor_hoy_asignados= $asignados_hoy_independientes[$index]['cantidad'];
					}
					$aux[$key]['hoy']['asignados-independientes'] = $valor_hoy_asignados;

					$valor_hoy_aprobados = 0;
					$is_in = in_array($item['idoperador'], array_column($aprobados_hoy_independientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($aprobados_hoy_independientes, 'operador_asignado'));
						$valor_hoy_aprobados= $aprobados_hoy_independientes[$index]['cantidad'];
					} 
					$aux[$key]['hoy']['aprobados-independientes'] = $valor_hoy_aprobados;

					$valor_hoy_rechazado = 0;
					$is_in = in_array($item['idoperador'], array_column($rechazado_hoy_independientes, 'operador_asignado'));
					if($is_in){
						$index = array_search($item['idoperador'], array_column($rechazado_hoy_independientes, 'operador_asignado'));
						$valor_hoy_rechazado= $rechazado_hoy_independientes[$index]['cantidad'];
					} 
					$aux[$key]['hoy']['rechazado-independientes'] = $valor_hoy_rechazado;
		endforeach;
		$data['indicadores'] = $aux;

		// log_message('debug', json_encode($data));

		$this->load->view('mora/indicadores_mora',['data' => $data]);
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

	
}

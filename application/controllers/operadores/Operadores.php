<?php
//defined('BASEPATH') or exit('No direct script access allowed');

class Operadores extends CI_Controller
{
    protected $CI;
    public function __construct()
    {
        parent::__construct();
        
        if ($this->session->userdata("is_logged_in")) 
        {
            $this->load->model('operadores/Operadores_model');
            $this->load->model('User_model');
            $this->load->model('Usuarios_modulos_model');
            $this->load->model("tablero/Tablero_model", "tablero");
            $this->load->helper("encrypt");
       } 
        else
        {
            redirect(base_url()."login/logout");
        }
    }

    public function index()
    {
        $link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/',$link);
        $permisos = FALSE;
        //var_dump($link); die;

        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;

        $tipo_operadores=$this->Operadores_model->get_tipos_operador();
        $lista_operadores=$this->Operadores_model->get_lista_operador_central();

        if ($permisos) 
        {
            $title['title'] = "Operadores";
            //$this->load->view('layouts/header',$title);
            //$this->load->view('layouts/nav');
            //$this->load->view('layouts/sidebar');
            $this->load->view('layouts/adminLTE', $title);
            $this->load->view('operadores/operadores',['tipo_operadores'=>$tipo_operadores,'lista_operadores'=>$lista_operadores]);
            //$this->load->view('layouts/footer');
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
        $data['objetivo'] = 85;
        $aux = $data['indicadores'];

        
		foreach ($data['indicadores'] as $key => $item):
                
			$aux[$key] += [
                            'mes' => [ 
                                'control' => $this->tablero->cargarIndicadores_operador($item['idoperador'], ['mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo')]),
                                'aprobados' => $this->tablero->get_retanqueo($item['idoperador'], ['mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'),'retanqueo' => "false"]),
                                'reto' => $this->tablero->get_retanqueo($item['idoperador'], ['mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'),'retanqueo' => "true"])

                            ],
                            'ayer' => [
                                'control' => $this->tablero->cargarIndicadores_operador($item['idoperador'], ['dia' => $ayer, 'mes' =>$mes, 'anho' => $anho,'equipo' => $this->session->userdata('equipo')]),
                                'aprobados' => $this->tablero->get_retanqueo($item['idoperador'], ['dia' => $ayer, 'mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'),'retanqueo' => "false"]),
                                'reto' => $this->tablero->get_retanqueo($item['idoperador'], ['dia' => $ayer, 'mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'),'retanqueo' => "true"])

                            ],
                            'hoy' => [
                                'control' => $this->tablero->cargarIndicadores_operador($item['idoperador'], ['dia' => $dia, 'mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo')]),
                                'aprobados' => $this->tablero->get_retanqueo($item['idoperador'], ['dia' => $dia, 'mes' =>$mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'),'retanqueo' => "false"]),
                                'reto' => $this->tablero->get_retanqueo($item['idoperador'], ['dia' => $dia, 'mes' => $mes, 'anho' => $anho, 'equipo' => $this->session->userdata('equipo'), 'retanqueo' => "true"])
                            ]
                        ];
        endforeach;
        
        $data['indicadores'] = $aux;
        //echo "<pre>";print_r($this->session->userdata('equipo'));echo "</pre>";die;
		$this->load->view('operadores/vistaIndicadores', ['data' => $data]);

	}

    

    public function lista_operadores()
    {
        $filtro = [];
        if ($this->session->userdata("is_logged_in")) 
        {
                $data['lista_operadores'] = $this->Operadores_model->get_lista_operadores_by(['equipo' => $this->session->userdata('equipo'), 'tipo_operador' => $this->session->userdata('tipo_operador')]);
            
                if(empty($data['lista_operadores']))
                    $data['lista_operadores'] = [];
            
            //echo "<pre>";print_r($data['lista_operadores']);echo "</pre>";
            $this->load->view('operadores/lista_operadores', ['data' => $data]);
        } else
        {
            redirect(base_url()."login/logout");
        }
    }

    public function datos_operador()
    {
        if ($this->session->userdata("is_logged_in")) 
        {
            $filtro[0]['columna'] = 'op.idoperador';
            $filtro[0]['valor'] = $_POST['operador'];
            $filtro[0]['or'] = FALSE;

            $data['operador'] = $this->Operadores_model->get_operador_by($filtro)[0];
            $data['tipos_operador'] = $this->Operadores_model->get_tipos_operador();
            $usuario = $this->User_model->get_user_inf($data['operador']->id_usuario);
            
            if ( !empty($usuario))
            {
                $data['user_auth']['user'] = $usuario[0]->username;
                $data['user_auth']['password'] = decrypt($usuario[0]->password);
                $data['user_auth']['name'] = $usuario[0]->first_name;
                $data['user_auth']['lastname'] = $usuario[0]->last_name;

            } else 
            {
                $data['user_auth']['user'] = "";
                $data['user_auth']['password'] = "";
                $data['user_auth']['name'] = "";
                $data['user_auth']['lastname'] = "";
            }
            
            $data['modulos'] = $this->Usuarios_modulos_model->get_modulos_usuario($data['operador']->id_usuario);
            
            if (isset($_POST['edit'])) 
            {
                $this->load->view('operadores/actualizar_operador', ['data' => $data]);
            } else
            {
                $this->load->view('operadores/detalle_operador', ['data' => $data]);
            }
        } else
        {
            redirect(base_url()."login/logout");
        }
    }

    public function crear_operador()
    {    
        $data['tipos_operador'] = $this->Operadores_model->get_tipos_operador();
        $this->load->view('operadores/registrar_operador', ['data' => $data]);
    }

    public function asignaciones()
    {
        $filtro[0]["columna"] = "tipo_operador";
        $filtro[0]["valor"] = "1";
        $filtro[0]["or"] = FALSE;

        $filtro[1]["columna"] = "tipo_operador";
        $filtro[1]["valor"] = "3";
        $filtro[1]["or"] = TRUE;

        $filtro[2]["columna"] = "tipo_operador";
        $filtro[2]["valor"] = "4";
        $filtro[2]["or"] = TRUE;

        $filtro[3]["columna"] = "tipo_operador";
        $filtro[3]["valor"] = "5";
        $filtro[3]["or"] = TRUE;

        //lista de operadores designados
        $data['operadores_designados'] = $this->Operadores_model->get_lista_operadores_by($filtro);
       
        $filtro = [];
        $filtro[0]["columna"] = "estado";
        $filtro[0]["valor"] = "1";
        $filtro[0]["or"] = FALSE;
        $filtro[1]["columna"] = "tipo_operador";
        $filtro[1]["valor"] = "1";
        $filtro[1]["or"] = FALSE;

        //buscamos los operadores ausentes 
        $operadores_ausentes = $this->Operadores_model->get_lista_operadores_ausentes();
        $aux=[];
        
        // si existen operadores ausentes obtenemos todos los ids
        if (!empty($operadores_ausentes)){
            foreach ($operadores_ausentes as $key => $value) { array_push($aux, $value->idoperador);}
            $operadores_ausentes = $aux;
            $filtro[2]["not_in"] = $operadores_ausentes;
        }
        
        //consultamos lista de operadores receptores no ausentes 
        $data['operadores_receptares'] = $this->Operadores_model->get_lista_operadores_by($filtro);
		$this->load->view('operadores/asignaciones', ['data' => $data]);
    }
}
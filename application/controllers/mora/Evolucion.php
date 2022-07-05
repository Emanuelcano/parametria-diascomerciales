<?php

class Evolucion extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->helper('url');
        $this->load->model("Solicitud_m", "solicitud_model");
    }

    public function index() {
        $link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/', $link);
        $permisos = FALSE;
        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;
        if ($permisos) {

            $title['title'] = 'Evolucion Mora';
            $this->load->view('layouts/adminLTE', $title);
            $this->load->view('mora/evolucion_mora');
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }


    public function grafica()
    {
        $tipo = $this->input->post('tipo');
        $rango = $this->input->post('rango');
        $vencimientos = $this->input->post('vencimiento');
        $result = [];
        


        foreach ($vencimientos as $key => $value) {

            
            if (!is_null($value) && $value != '') {
    
                $periodo_mas = strtotime ( '+5 day' , strtotime ( $value ) ) ;
                $periodo_menos = strtotime ( '-5 day' , strtotime ( $value ) ) ;
                $vencimiento_desde = date ( 'Y-m-j' , $periodo_menos );
                $vencimiento_hasta = date ( 'Y-m-j' , $periodo_mas );
                
                $total_creditos = $this->solicitud_model->creditosByPeriodo($vencimiento_desde, $vencimiento_hasta, $tipo)[0]["total"];

                $ciclo_result = [];

                for ($i = -3; $i < $rango ; $i++) { 
    
                    $fecha_ciclo = strtotime ( "+$i day" , strtotime ( $value ) ) ;
                    $fecha_ciclo = date ( 'Y-m-j' , $fecha_ciclo );
                    $porcentaje_dia = 0;
                    if ($total_creditos > 0) {
                        $total_creditos_cliclo = $this->solicitud_model->creditosByPeriodo($vencimiento_desde, $vencimiento_hasta, $tipo, $fecha_ciclo)[0]["total"];
        
                        $pendiente_pago = $total_creditos - $total_creditos_cliclo;
                        $porcentaje_dia = $pendiente_pago * 100 / $total_creditos;
                    }
    
                    
                    array_push($ciclo_result,[
                        'fecha' => $fecha_ciclo,
                        'pendiente' => $pendiente_pago,
                        'rango' => $i,
                        'porcentaje' => number_format($porcentaje_dia, 2),
                    ]);
    
                }
               
                array_push($result, [
                    'vencimiento' => date('d-m-Y',strtotime($value)),
                    'valores'     => $ciclo_result,
                    'total'     => $total_creditos
                ]);
    
                
    
            }
            
        }
        echo json_encode($result);
        
    }
}

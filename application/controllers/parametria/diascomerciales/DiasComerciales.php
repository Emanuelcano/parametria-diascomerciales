<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DiasComerciales extends CI_Controller
    {
        public function __construct()
            {
                parent::__construct();
                $this->load->model("parametria/diascomerciales/Dias_Comerciales_model");
                $this->load->config('form_validation');
                $this->load->library('form_validation'); 

            }

        public function index()
            {
               

                $title['title'] = "Parametria";
              /*$this->load->view('layouts/header',$title);
                $this->load->view('layouts/nav');
                $this->load->view('layouts/sidebar');*/
                $this->load->view('layouts/adminLTE', $title);
                $this->load->view('parametria/diascomerciales/diascomerciales');
            
            }

        public function listaDiasComerciales()
            {
                

                $listaDias=$this->Dias_Comerciales_model->get_lista_dias_comerciales();
                $fechas_es=['Domingo','Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
                
                foreach ($listaDias as $key => $value) {
                    $dia_sem = date('w', strtotime($value['fecha']));
                  
                    $listaDias[$key]['dia_semana'] = $fechas_es[$dia_sem];
                }

              
              $this->load->view('parametria/diascomerciales/listaDiasComerciales',['data'=>$listaDias]);
        
            }

        public function nuevoDiaComercial()
            {
                $listaDias=$this->Dias_Comerciales_model->get_lista_dias_comerciales();
                $fechas_es=['Domingo','Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
                
                foreach ($listaDias as $key => $value) {
                    $dia_sem = date('w', strtotime($value['fecha']));
                  
                    $listaDias[$key]['dia_semana'] = $fechas_es[$dia_sem];
                }
                
                $this->load->view('parametria/diascomerciales/nuevosDiasComerciales',['data'=>$listaDias]);
        
            }

        public function registrarDiaComercial()
            {
                $config = array(
                    array(
                          'field' => 'fecha',
                          'label' => 'Fecha del evento', 
                          'rules' => 'required'
                          ),
                            array(
                                  'field' => 'descripcion',
                                  'label' => 'Descripcion del nuevo evento',
                                  'rules' => 'required',
                                  'errors' => array(
                                  'required' => 'El campo es obligatorio.',
                                                  ),
                                  ),              
                          );
                          $this->form_validation->set_rules($config);
    

                          if($this->form_validation->run() ==false){
                           
                     

                          $mensaje = json_encode( array( 'errors' => 'Los campos son obligatorios.',
                                                          'created' => '28/06/2022'));
                          echo $mensaje;

                            
                          
                          }else{

                          $data = array( 
                                         'fecha'=>$this->input->post('fecha'),
                                         'descripcion'=>$this->input->post('descripcion'),
                                  ); 
                                            
                                $crear_parentesco= $this->Dias_Comerciales_model->registrar_dia_comercial($data);

                                $mensaje = json_encode( array( 'data' => 'Agregado exitosamente!',
                                                                'created' => '28/06/2022'));

                                echo $mensaje;
                          }
                                  
            }

    public function cargarDiaComercial()
        {

            
              
              $id =  $this->input->post('id');      
              $data = $this->Dias_Comerciales_model->cargar_Dia($id)[0];

              $fechas_es=['Domingo','Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];  
              
              $dia_semana = date('w', strtotime($data['fecha']));
                  
              $data['dia_semana'] = $fechas_es[$dia_semana];

              $this->load->view('parametria/diascomerciales/nuevosDiasComerciales', $data  );
                
        }
          
    public function actualizarDiaComercial()
        {


        $parametros = array(
            'fecha' => $this->input->post('fecha'), 
            'descripcion' => $this->input->post('descripcion'),  
        );

        $actualizarDia = $this->Dias_Comerciales_model->actualizar_dia_comercial($parametros, $this->input->post('id'));
      
    }
}
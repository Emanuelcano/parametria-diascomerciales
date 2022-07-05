<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiOperadores extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		// MODELS
        $this->load->model('operadores/Operadores_model','operadores',TRUE);
        $this->load->model('User_model','users');
        $this->load->model('Modulos_model','modulos',TRUE);
        $this->load->model('Solicitud_m','solicitudes',TRUE);
        $this->load->model('Usuarios_modulos_model','modulos_usuarios',TRUE);
        $this->load->model('Chat','chat',TRUE);
        $this->load->model('SolicitudAsignacion_model','solicitudAsignacion',TRUE);

		// LIBRARIES
		$this->load->library('form_validation');
        $this->load->helper('encrypt');
	}

    public function subir_imagen_post()
    {
        $config['upload_path'] = "./public/operadores_imagenes";
        $config['file_name'] = $this->post('idoperador');
        $config['allowed_types'] = 'jpg|png|jpeg|pdf';
        $config['overwrite'] = TRUE;
        $config['max_size'] = "320000";

	        $this->load->library('upload');
	        $this->upload->initialize($config);
	        if ($this->upload->do_upload('file'))
	        {
	           	$file = $this->upload->data();
	           	$file['uri'] = base_url($config['upload_path'].$file['file_name']);
	        	
                $data  = array("upload_data" => $this->upload->data());
                
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['status']['ok'] = TRUE;
                $response['message'] = "Registro guardado";

                $parametros = array(
                    'idoperador'=>$this->post('idoperador'),
                    'avatar'=> "public/operadores_imagenes/".$file['file_name']
                );
                $actualizar_operador = $this->operadores->actualizar_operador($parametros);
                    
                if($actualizar_operador){
				    $status = parent::HTTP_OK;
				    $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                             'message' => "Registro actualizado"
											];
               }else
               {
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                              'errors' => "Falló al actualizar la imagen de perfil"];
			   }                

	        }else{
	        	$status = parent::HTTP_INTERNAL_SERVER_ERROR;
           		$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['errors'] = $this->upload->display_errors();
	        }
        
       	$this->response($response,$status);
    }

    public function set_estado_operador_post()
    {
        if($this->_validate_estado_operador())
	    {
			   $parametros = array( 
                       'idoperador'=>$this->post('idoperador'),
					   'estado'=>$this->post('estado')
                       );
                       
               $actualizar_operador = $this->operadores->actualizar_operador($parametros);
               if($actualizar_operador)
               {
                    $filtro[0]['columna'] = 'op.idoperador';
                    $filtro[0]['valor'] = $this->post('idoperador');
                    $filtro[0]['or'] = FALSE;
            
                    $actualizar_usuario = $this->operadores->get_operador_by($filtro);
                    if(!empty($actualizar_usuario))
                    {
                        $usuario = $actualizar_usuario[0]->id_usuario;
                        $idoperador = $actualizar_usuario[0]->idoperador;
                        $parametros = array( 
                            'id' => $usuario,
                            'active' => $this->post('estado')
                        );
                        $date = array( 
                            'id_operador'=>$this->session->userdata("idoperador"),
                            'id_registro_afectado'=>$idoperador,
                            'tabla'=> 'operadores',
                            'detalle'=> '[CAMBIO_ESTADO] Datos :'.json_encode($parametros),
                            'accion'=> 'UPDATE',
                            'fecha_hora'=> date("Y-m-d H:i:s")
                        );
                        $track = $this->operadores->track_interno($date);

                        $actualizar_usuario = $this->users->actualizar($parametros);
                        if($actualizar_usuario > 0)
                        {
                            $date = array( 
                                'id_operador'=>$this->session->userdata("idoperador"),
                                'id_registro_afectado'=>$usuario,
                                'tabla'=> 'users',
                                'detalle'=> '[CAMBIO_ESTADO] Datos :'.json_encode($parametros),
                                'accion'=> 'UPDATE',
                                'fecha_hora'=> date("Y-m-d H:i:s")
                                );
                            $track = $this->operadores->track_interno($date);
                            $status = parent::HTTP_OK;
                            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                                     'message' => "Registro actualizado",
                                                    ];
                       } else 
                       {
                            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                            $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                                      'errors' => "Falló al actualizar el registro"];
                       }

                    } else 
                    {
                        $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                            $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                                      'errors' => "No se encontro el usuario del operador seleccionado"];
                    }

               }else 
               {
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                          'errors' => "Falló al actualizar el registro"];
               } 
        } else 
        {
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->form_validation->error_array();
        }

        $this->response($response,$status);
    }

    public function actualizar_operador_cobranza_post()
    {
        $hoy = date("Y-m-d H:i:s");
        $parametros = array( 
            'idoperador'=>$this->post('id_operador'),
            'estado'=>$this->post('estado'),
            'tipo_operador'=>$this->input->post('tipo_operador'),
            'user_action' => $this->session->userdata("id_usuario"),
            'modified_on' => $hoy,
            'equipo' => $this->post('equipo'),
            'verificion_login' => $this->post('token')            
        );
        $actualizar_operador = $this->operadores->actualizar_operador($parametros);
        $parametros = array( 
            'id'=>$this->post('id_usuario'),
            'active'=>$this->post('estado'),
            'user_action' => $this->session->userdata("id_usuario"),
            'modified_on' => $hoy,
        );
        $actualizar = $this->users->actualizar($parametros);
        if(($actualizar > 0) || ($actualizar_operador > 0))
        {
            $date = array( 
                'id_operador'=>$this->session->userdata("idoperador"),
                'id_registro_afectado'=>$this->post('id_operador'),
                'tabla'=> 'operadores',
                'detalle'=> '[ACTUALIZA_OPERADOR] Datos: '.json_encode($parametros),
                'accion'=> 'UPDATE',
                'fecha_hora'=> $hoy
            );
            $track = $this->operadores->track_interno($date);
            $status = parent::HTTP_OK;
            $response = ['status' => ['code'    => $status, 'ok' => TRUE],
                                      'message' => "Registro actualizado",
                                    ];
            $this->response($response,$status);                                
            }else
            {
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response = ['status' => ['code'   => $status, 'ok' => FALSE], 
                                          'errors' => "Falló al actualizar el registro"];
                $this->response($response,$status);   
            }
    }

    public function update_configuracion_solicitud_obligatoria_post()
    {
        $post = $this->input->post();
        $operadores['operadores'] = (isset($post['operadores'])) ? $post['operadores'] : [];
        $tipo_operador = $post['tipo_operador'];

        $filtro['equipo'] =  $this->session->userdata("equipo");
        $filtro['tipo_operador'] =  $tipo_operador;
        $actualizar_operador = $this->operadores->actualizar_operador_solicitudes_obligatorias($operadores, $filtro);
        
        if($actualizar_operador){
            $status = parent::HTTP_OK;
            $response = ['status' => ['code'    => $status, 'ok' => TRUE],
                                      'message' => "Registro actualizado",
                                    ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 
            'errors' => "Falló el update de gestiones obligatorias"];
        }
        
        $this->response($response,$status);
    }

	public function actualizar_operador_post() 
	{
        $hoy = date("Y-m-d H:i:s");
		if($this->_validate_save_input_operador("actualizar"))
	    {
			   $parametros = array( 
                       'idoperador'=>$this->post('idoperador'),
					   'nombre_apellido'=>$this->post('nombre').' '.$this->post('apellido'),
					   'nombre_pila'=>$this->post('nombre_pila'),
					   'telefono_fijo'=>$this->post('telefono_fijo'),
					   'wathsapp'=>$this->post('wathsapp'),
					   'estado'=>$this->post('estado'),
					   'extension'=>$this->post('extension'),
					   'mail'=>$this->input->post('mail'),
                       'tipo_operador'=>$this->input->post('tipo_operador'),
                       'user_action' => $this->session->userdata("id_usuario"),
                       'modified_on' => $hoy,
                       'equipo' => $this->post('equipo'),
                       'verificion_login' => $this->post('token'),
                       'automaticas' => $this->post('automaticas')

                       );
			   $actualizar_operador = $this->operadores->actualizar_operador($parametros);
               
               if($actualizar_operador){
                    
                   //tambien se tiene que actualizar la asignacion en la base de datos usuarios solventa
                    $modulos = explode(",", $this->post('modulos'));

                    $asignacion = $this->asignar_modulos($modulos, $this->post('id_usuario'));
                    if ( is_int($asignacion) || $asignacion)
                    {
                        $date = array( 
                            'id_operador'=>$this->session->userdata("idoperador"),
                            'id_registro_afectado'=>$this->post('id_usuario'),
                            'tabla'=> 'usuarios_modulos',
                            'detalle'=> '[MODULOS_OPERADOR] Datos: '.json_encode($modulos),
                            'accion'=> 'UPDATE',
                            'fecha_hora'=> $hoy
                        );
                        $track = $this->operadores->track_interno($date);
                        $status = parent::HTTP_OK;
                        $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                                        'message' => "Registro actualizado",
                                                    ];
                    }else
                    {
                        $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                        $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                                    'errors' => "Falló al asignar los modulos"];
                    }

                    //actualizamos datosd de usuario
                    
                    $parametros = array( 
                        'id'=>$this->post('id_usuario'),
                        'first_name'=>$this->post('nombre'),
                        'last_name'=>$this->post('apellido'),
                        'active'=>$this->post('estado'),
                        'email'=>$this->input->post('mail'),
                        'user_action' => $this->session->userdata("id_usuario"),
                        'modified_on' => $hoy,
                        );
                    $actualizar = $this->users->actualizar($parametros);
                    
                    if($actualizar > 0)
                    {
                        
                        $date = array( 
                            'id_operador'=>$this->session->userdata("idoperador"),
                            'id_registro_afectado'=>$this->post('idoperador'),
                            'tabla'=> 'operadores',
                            'detalle'=> '[ACTUALIZA_OPERADOR] Datos: '.json_encode($parametros),
                            'accion'=> 'UPDATE',
                            'fecha_hora'=> $hoy
                        );
                        $track = $this->operadores->track_interno($date);
                    }
               }else
               {
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                              'errors' => "Falló al actualizar el registro"];
			   }
			   $this->response($response,$status);   
        } else 
        {
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->form_validation->error_array();
        }

        $this->response($response,$status);
    }

    public function registrar_operador_post() 
	{ 
        $hoy = date("Y-m-d H:i:s");
		if($this->_validate_save_input_operador("registrar"))
	    {
            $registro_usuario = $this->registrar_usuario();

            if($registro_usuario)
            {
                $parametros = array( 
                    'id_usuario'=> $registro_usuario,
                    'nombre_apellido'=>$this->post('nombre').' '.$this->post('apellido'),
                    'nombre_pila'=>$this->post('nombre_pila'),
                    'telefono_fijo'=>$this->post('telefono_fijo'),
                    'documento'=>$this->post('documento'),
                    'wathsapp'=>$this->post('wathsapp'),
                    'extension'=>$this->post('extension'),
                    'mail'=>$this->input->post('mail'),
                    'tipo_operador'=>$this->input->post('tipo_operador'),
                    'estado'=>$this->input->post('estado'),
                    'cantidad_asignar'=> "0",
                    'avatar'=> 'public/operadores_imagenes/user.png',
                    'user_action' => $this->session->userdata("id_usuario"),
                    'modified_on' => $hoy,
                    'created_on' => $hoy,
                    'equipo' => $this->post('equipo'),
                    'verificion_login' => $this->post('token'),
                    'automaticas' => $this->post('automaticas')
                    );

                    $registrar_operador = $this->operadores->registrar_operador($parametros);

                    
                    if(is_int($registrar_operador))
                    {
                        if(!empty($this->post('modulos'))){
                            $modulos = explode(",", $this->post('modulos'));
                            $asignacion = $this->asignar_modulos($modulos, $registro_usuario);
                            $date = array( 
                                'id_operador'=>$this->session->userdata("idoperador"),
                                'id_registro_afectado'=>$registro_usuario,
                                'tabla'=> 'usuarios_modulos',
                                'detalle'=> '[MODULOS_OPERADOR] Datos: '.json_encode($modulos),
                                'accion'=> 'INSERT',
                                'fecha_hora'=> $hoy
                            );
                            $track = $this->operadores->track_interno($date);
                        }
                        
                        //Registro operador
                        $date = array( 
                            'id_operador'=>$this->session->userdata("idoperador"),
                            'id_registro_afectado'=>$registrar_operador,
                            'tabla'=> 'operadores',
                            'detalle'=> '[NUEVO REGISTRO] Datos: '.json_encode($parametros),
                            'accion'=> 'INSERT',
                            'fecha_hora'=> $hoy
                            );
                        $track = $this->operadores->track_interno($date);
                        $status = parent::HTTP_OK;
                        $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                                        'message' => "Operador registrado",
                                                        'id_operador' => $registrar_operador
                                                    ];
                        
                    }else
                    {
                        $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                        $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                                    'errors' => "Falló al registrar el operador"];
                    }
                    
            }
            else
                    {
                        $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                        $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                                    'errors' => "Falló al registrar el usuario"];
                    }
               
        } else 
        {
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->form_validation->error_array();
        }

        $this->response($response);

    }

    public function get_modulos_nombre_get($operador)
    {
        $data = $this->modulos_usuarios->get_modulos_usuario_nombre($operador);
        
        if ( !empty($data)) {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data];  
        }else{
            $status = '200';
            $response = ['status' => $status, 'message' => 'No hay modulos disponibles! ', 'data' => $data];
        }
        
        $this->response($response, $status);
    }

    public function get_modulos_get($operador)
    {
        if($operador == "all")
        {
            $data = $this->modulos->get_active_modulos();
        } else {
            $data = $this->modulos_usuarios->get_modulos_usuario($operador);
        }   
        
        if ( !empty($data)) {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data];  
        }else{
            $status = '200';
            $response = ['status' => $status, 'message' => 'No hay modulos disponibles! ', 'data' => $data];
        }
        
        // REST_Controller provide this method to send responses
        $this->response($response, $status);

    }

    public function habilitar_cambio_clave_post()
    {
        $hoy = date("Y-m-d H:i:s");
        $parametros = array( 
            'id'=>$this->post('id_usuario'),
            'cambio_clave_habilitar'=> $this->post('cambio_clave_habilitar'),
            'user_action' => $this->session->userdata("id_usuario"),
            'modified_on' => $hoy,
        );
        $actualizar = $this->users->actualizar($parametros);
        if($actualizar >= 1)
        {
            $date = array( 
                'id_operador'=>$this->session->userdata("idoperador"),
                'id_registro_afectado'=>$this->post('id_usuario'),
                'tabla'=> 'users',
                'detalle'=> '[HABILITAR_CAMBIO_CLAVE] Datos: '.json_encode($parametros),
                'accion'=> 'UPDATE',
                'fecha_hora'=> $hoy
                );
            $track = $this->operadores->track_interno($date);
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                            'message' => "La clave ya puede ser modificada por el operador.",
                                        ];
        }else
        {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'message_error' => "No se pudo habilitar el cambio de clave."];
        }

        $this->response($response);     
    }

    public function cambio_clave_habilitado_post() 
    {
        $hoy = date("Y-m-d H:i:s");
        $password = $this->security->xss_clean(strip_tags($this->post('newPassword')));
        $parametros = array( 
            'id'=>$this->session->userdata("id_usuario"),
            'password'=> encrypt($password),
            'user_action' => $this->session->userdata("id_usuario"),
            'modified_on' => $hoy,
            'cambio_clave_habilitar'=> 0,
        );
        $actualizar = $this->users->actualizar($parametros);
        if($actualizar >= 1)
        {
            $date = array( 
                'id_operador'=>$this->session->userdata("id_usuario"),
                'id_registro_afectado'=>$this->session->userdata("id_usuario"),
                'tabla'=> 'users',
                'detalle'=> '[CLAVE_OPERADOR] Datos: '.json_encode($parametros),
                'accion'=> 'UPDATE',
                'fecha_hora'=> $hoy
            );
            $track = $this->operadores->track_interno($date);
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                                'message' => "Clave actualizada",
                                            ];
        }else
        {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 'message_error' => "La clave no pudo ser actualizada"];
        }
        $this->response($response);
    }

    public function actualizar_usuario_clave_post()
    {
        if($this->_validate_cambio_clave())
        {
            $hoy = date("Y-m-d H:i:s");
            $password = $this->security->xss_clean(strip_tags($this->post('password')));
            $parametros = array( 
                'id'=>$this->post('id_usuario'),
                'password'=> encrypt($password),
                'user_action' => $this->session->userdata("id_usuario"),
                'modified_on' => $hoy,
                );
            $actualizar = $this->users->actualizar($parametros);
            if($actualizar >= 1)
            {
                $date = array( 
                    'id_operador'=>$this->session->userdata("idoperador"),
                    'id_registro_afectado'=>$this->post('id_usuario'),
                    'tabla'=> 'users',
                    'detalle'=> '[CLAVE_OPERADOR] Datos: '.json_encode($parametros),
                    'accion'=> 'UPDATE',
                    'fecha_hora'=> $hoy
                    );
                $track = $this->operadores->track_interno($date);
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                                'message' => "Clave actualizada",
                                            ];
            }else
            {
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response = ['status' => ['code' => $status, 'ok' => FALSE], 'message_error' => "La clave no pudo ser actualizada"];
            }
        }else 
        {
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->form_validation->error_array();
        }

        $this->response($response);
    }

    public function nuevo_tipo_operador_post()
    {
        if ($this->_validate_nuevo_tipo()) 
        {
            $descripcion = $this->security->xss_clean(strip_tags($this->post('tipo')));
            $parametros = array( 
                'descripcion'=> $descripcion,
                );
            $actualizar = $this->operadores->create_tipo_operador($parametros);
            if($actualizar)
            {
                $date = array( 
                    'id_operador'=>$this->session->userdata("idoperador"),
                    'id_registro_afectado'=>$actualizar,
                    'tabla'=> 'tipo_operador',
                    'detalle'=> '[TIPO_OPERADOR] Datos: '.json_encode($parametros),
                    'accion'=> 'UPDATE',
                    'fecha_hora'=> date("Y-m-d H:i:s")
                    );
                $track = $this->operadores->track_interno($date);
                $status = parent::HTTP_OK;
                $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                                'message' => "Registro creado",
                                                 'id' => $actualizar
                                            ];
            }else
            {
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                            'errors' => "No se puedo crear el registro"];
            }
        } else 
        {
            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->form_validation->error_array();
        }
        $this->response($response);
    }

    public function get_asignaciones_operador_post()
    {
        $data = [];
        if ($this->_validate_asignaciones_operador()) 
        {
            //consultamos la lista de solicitudes de los operadores
            if($this->post('tipo') == 'designado')
                $data["solicitudes"] = $this->operadores->get_asignaciones_operador(['operador' => $this->post('operador'),'estado' => 'A', 'inicio' => $this->post('inicio'), 'fin' => $this->post('fin'), 'where' => '(ss.estado NOT IN ("TRANSFIRIENDO", "PAGADO", "RECHAZADO", "APROBADO") OR ss.estado is null)']);
            if($this->post('tipo') == 'receptor')
                $data["solicitudes"] = $this->operadores->get_asignaciones_operador(['operador' => $this->post('operador'),'estado' => 'A', 'inicio' => $this->post('inicio'), 'fin' => $this->post('fin'), 'where' => '(ss.estado NOT IN ("TRANSFIRIENDO", "PAGADO", "RECHAZADO", "APROBADO") OR ss.estado is null)']);
            
            $aux = $data["solicitudes"];
            //consultamos que solicitudes tienen un chat asignado
            foreach ($data["solicitudes"] as $key => $value) {
                $chat = $this->operadores->findBySenderNumber($value["telefono_cliente"]);
                if( !empty($chat))
                    $aux [$key]["new_chat"] = $chat;
            }
            $data["solicitudes"] = $aux;

            //consultamos la cantidad de chats asignados que tiene cada operador
            $data["cantidad_chats"] = count($this->operadores->get_ids_chats_asignados($this->post('operador'), ["inicio"=>$this->post("inicio"), "fin"=>$this->post("fin")]));

            if ( !empty($data["solicitudes"])) 
            {
                // Set HTTP status code
                $status = parent::HTTP_OK;
                // Prepare the response
                $response = ['status' => $status, 'data' => $data];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'message' => 'No hay asignaciones para este usuario en la fecha seleccionada ', 'data' => $data];
            }
        }else 
        {
            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->form_validation->error_array();
        }
        $this->response($response);
    }

    public function get_operador_get($operador)
    {
        $filtro[0]['columna'] = 'op.idoperador';
        $filtro[0]['valor'] = $operador;
        $filtro[0]['or'] = FALSE;

        $data = $this->operadores->get_operador_by($filtro);

        if ( !empty($data)) 
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data[0]];  
        } else
        {
            $status = '200';
            $response = ['status' => $status, 'message' => 'No hay asignaciones para este usuario en la fecha seleccionada ', 'data' => $data];
        }
        
        $this->response($response);
    }

    public function get_solicitud_by_get($col, $val)
    {
        $data = [];
        if($col == 'documento')
            $data = $this->solicitudes->getSolicitudesBy(['documento' => $val]);

        if($col == 'solicitud')
            $data = $this->solicitudes->getSolicitudesBy(['id_solicitud' => $val]);

        $aux = $data;
        foreach ($data as $key => $value) {
            //si tiene un operador asignado obtenemos la informacion de dicho operador
            if ($value->operador_asignado != 0)
            {
                $parametros [0]=[
                    'columna' => 'idoperador',
                    'valor' => $value->operador_asignado,
                    'or' => FALSE
                ];
                $operador_asignado = $this->operadores->get_operador_by($parametros);
                if(!empty($operador_asignado))
                    $aux[$key]->operador_asignado = $operador_asignado[0];
            }
             
            //consultamos si tiene un chat asignado
            $chat = $this->operadores->findBySenderNumber($value->telefono);
            //var_dump($chat);die;
            if( !empty($chat))
                $aux [$key]->new_chat = $chat;
        }
        $data = $aux;
        
        if ( !empty($data)) 
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data];  
        }else
        {
            $status = '200';
            $response = ['status' => $status, 'message' => 'No hay resgistros '];
        }

        $this->response($response);
    }

    public function set_asignaciones_post()
    {
        $response = -1;
        $hoy = date("Y-m-d H:i:s");

        if($this->post('tipo_asignacion') != NULL && $this->post('designado') != $this->post('receptor'))
        {
            $tipo = $this->post('tipo_asignacion');
            $filas_afectadas = 0;

            switch ($tipo) 
            {
                case 'asig-1': //asignacion 1 a 1
                    $solicitudes = explode(",",$this->post('solicitudes'));

                    // cuento la cantidad de asignaciones por dia dentro de las fecha seleccionada para el operador a quien le quito las asignaciones
                    $asignaciones_designado = $this->operadores->get_cant_asignaciones_by_day($this->post('designado'), $this->post('inicio'), $this->post('fin'));
                    
                    //por cada dia hago los calculos correspondientes 
                    foreach ($asignaciones_designado as $key => $value) 
                    {
                        //obtengo la informacion de control del designado para restar
                        $control_designado = $this->operadores->get_control_asignaciones($value['fecha'], $this->post('designado'));
                        
                        if (!empty($control_designado)) 
                        {
                            //restamos las asignaciones del dia que fueron reasignadas
                            $nueva_cantidad2 = intval($control_designado[0]->asignados) - intval($value['cantidad']);
                            $data = Array(
                                'asignados' => $nueva_cantidad2,
                                'update_at' => $hoy
                            );

                            $this->solicitudAsignacion->update($control_designado[0]->id, $data);
                        }

                        //obtengo la informacion de control del receptor para sumar
                        $control_receptor = $this->operadores->get_control_asignaciones(substr($hoy, 0, 10), $this->post('receptor'));
                        
                        if(!empty($control_receptor))
                        {
                            $nueva_cantidad = intval($control_receptor[0]->asignados) + intval($value['cantidad']);
                            $data = Array(
                                'asignados' => $nueva_cantidad,
                                'update_at' => $hoy
                            );

                            $update = $this->solicitudAsignacion->update($control_receptor[0]->id, $data);   
                        }

                        if($update < 1 || empty($control_receptor)) 
                        {
                            $data = Array(
                                'asignados' => $value['cantidad'],
                                'update_at' => $hoy,
                                'id_operador' => $this->post('receptor'),
                                'fecha_control' => $hoy,
                                'aprobados' => 0,
                                'verificados' => 0,
                                'validados' => 0,
                                'rechazados' => 0,
                                'estado' => 0
                            );
                            $insert = $this->solicitudAsignacion->insert($data);
                        }
                    }

                    //asignacion de solicitudes 
                    foreach ($solicitudes as $key => $value) {
                        //si se actualizo el operado de la tabla relacion_operador_solicitud
                        if($this->operadores->asignar_relacion_operador_solicitud($value, $this->post('designado'), $this->post('receptor')) > 0)
                        {
                            $filas_afectadas += 1;
                            $data = Array('operador_asignado' => $this->post('receptor'));

                            //actualizamos el operador asignado en la tabla solicitud
                            $this->solicitudes->edit($value, $data);
                        } 
                    }

                    //consultamos los ids de los chat reasignables del designado para reasignar las relaciones
                    $chats = $this->operadores->get_ids_chats_asignados($this->post("designado"), ["inicio"=>$this->post("inicio"), "fin"=>$this->post("fin")]);
                    $result = 0;
                    if(!empty($chats))
                    {
                        foreach ($chats as $key => $value) {
                            
                            $data =[
                                "id_operador" => $this->post('receptor'),
                                "id_chat" => $value["id"],
                                "fecha_cambio" => $hoy
                            ];

                            //insertamos el registro de asignacion en la tabla de relacion operador chat
                            $result+=$this->operadores->set_chat_relacion_operador($data);
                        }
                        $parametros = Array(
                            'designado' => $this->post("designado"),
                            'inicio' => $this->post('inicio'),
                            'fin' => $this->post('fin')
                        );
                        //reasignamos todos los chat que tiene asignados el operador esignado
                        $result = $this->operadores->reasignar_new_chat($this->post('receptor'), $parametros);
                    }
                    
                    if ($filas_afectadas < 1 ) {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => "Hubo un error al reasignar las solicitudes."];
                    } else {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => $filas_afectadas." Solicitudes fueron reasignadas y ".$result." chats"];
                    } 

                    break;

                case "asig-2"://asignacion directa
                    $solicitudes = explode(",",$this->post('solicitudes'));
                    $result = 0;
                    foreach ($solicitudes as $key => $value) 
                    {

                        $solicitud = $this->solicitudes->getSolicitudesBy(['id_solicitud' => $value])[0];

                        //consultamos si la solicitud esta ya asignada al operador receptor y su estado es A 
                        $relacion = $this->operadores->get_asignaciones_operador(['id_solicitud' => $value, 'estado' => 'A']);
                        //var_dump($relacion); 
                        if(empty($relacion) || ($relacion[0]['idoperador'] != $this->post('receptor'))) 
                        {
                                // la solicitud tiene un operador asignado?
                                if($solicitud->operador_asignado != 0 && !empty($relacion)) {

                                    $fecha_asignacion = substr($relacion[0]["fecha_registro"],0,10);

                                    /** 
                                     * con la fecha de registro y el id del operador designado buscamos 
                                     * el control de asignacion correspondiente y restamos la solicitud
                                     */
                                    $control_designado = $this->operadores->get_control_asignaciones($fecha_asignacion, $solicitud->operador_asignado);

                                    //si esta el control para la fecha de asignacion hacemos la resta
                                    if (!empty($control_designado) && $control_designado[0]->asignados > 0) 
                                    {
                                        //restamos las asignaciones del dia que fueron reasignadas
                                        $nueva_cantidad = intval($control_designado[0]->asignados) - 1;
                                        $data = Array(
                                            'asignados' => $nueva_cantidad,
                                            'update_at' => $hoy
                                        );

                                        $this->solicitudAsignacion->update($control_designado[0]->id, $data);
                                    }
                                   
                                }


                                //obtengo la informacion de control del receptor para sumar
                                $control_receptor = $this->operadores->get_control_asignaciones(substr($hoy, 0, 10), $this->post('receptor'));
                                $update = 0;
                                if(!empty($control_receptor))
                                {
                                    $nueva_cantidad = intval($control_receptor[0]->asignados) + 1;
                                    $data = Array(
                                        'asignados' => $nueva_cantidad,
                                        'update_at' => $hoy
                                    );

                                    $update = $this->solicitudAsignacion->update($control_receptor[0]->id, $data);   
                                }
                                
                                if($update < 1 || empty($control_receptor)) 
                                {
                                    $data = Array(
                                        'asignados' => 1,
                                        'update_at' => $hoy,
                                        'id_operador' => $this->post('receptor'),
                                        'fecha_control' => $hoy,
                                        'aprobados' => 0,
                                        'verificados' => 0,
                                        'validados' => 0,
                                        'rechazados' => 0,
                                        'estado' => 0
                                    );
                                    $insert = $this->solicitudAsignacion->insert($data);
                                }

                                if($this->operadores->asignar_relacion_operador_solicitud($value, $solicitud->operador_asignado, $this->post('receptor')) > 0)
                                {
                                    $filas_afectadas += 1;
                                    $data = Array('operador_asignado' => $this->post('receptor'));

                                    //actualizamos el operador asignado en la tabla solicitud
                                    $this->solicitudes->edit($value, $data);
                                } 
                                
                                //consultamos si tiene un chat asignado
                                $chat = $this->operadores->findBySenderNumber($solicitud->telefono);
                    
                                if( !empty($chat)) {
                                    //reasignar chats
                                    $data =[
                                        "id_operador" => $this->post('receptor'),
                                        "id_chat" => $chat[0]->id,
                                        "fecha_cambio" => $hoy
                                    ];

                                    //insertamos el registro de asignacion en la tabla de relacion operador chat
                                    $result=$this->operadores->set_chat_relacion_operador($data);
                                    $parametros = Array(
                                        'id' => $chat[0]->id
                                    );
                                    //reasignamos todos los chat que tiene asignados el operador esignado
                                    $result += $this->operadores->reasignar_new_chat($this->post('receptor'), $parametros);
                                }

                        }else {

                        }
                        
                    }
                    
                    if ($filas_afectadas < 1 ) {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => "No se asignaron las solicitudes."];
                    } else {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => $filas_afectadas." Solicitudes fueron reasignadas y ".$result." chats"];
                    } 
                    break;
                    
                case 'asig-3':

                    $solicitudes = explode(",",$this->post('solicitudes'));
                    $receptores = explode(",",$this->post('receptores'));
                    $index_op = 0;
                    $result = 0;

                    foreach ($solicitudes as $key => $value) 
                    {
                        //obtenemos datos de las solicitudes a asignar
                        $solicitud = $this->solicitudes->getSolicitudesBy(['id_solicitud' => $value])[0];
                        //si la solicitud tiene un operador asignado le restamos la asignacion al operador
                        if($solicitud->operador_asignado != 0) {
                            //buscamos la fecha de asignacion de la solicitud
                            $relacion = $this->operadores->getSolicitudesBy(["id_solicitud"=>$value]);
                            //si existe el registro de relacion operador solicitud obtenemos la fecha de registro

                            if(!empty($relacion)) {
                                $fecha_asignacion = substr($relacion[0]["fecha_registro"],0,10);

                                //con la fecha de registro y el id del operador designado buscamos 
                                //el control de asignacion correspondiente y restamos la solicitud
                                $control_designado = $this->operadores->get_control_asignaciones($fecha_asignacion, $solicitud->operador_asignado);

                                //si esta el control para la fecha de asignacion hacemos la resta
                                if (!empty($control_designado)) 
                                {
                                    //restamos las asignaciones del dia que fueron reasignadas
                                    $nueva_cantidad = $control_designado[0]->asignados - 1;
                                    $data = Array(
                                        'asignados' => $nueva_cantidad,
                                        'update_at' => $hoy
                                    );

                                    $this->solicitudAsignacion->update($control_designado[0]->id, $data);
                                }
                            }
                        }
                        
                        //obtengo la informacion de control del receptor para sumar
                        $control_receptor = $this->operadores->get_control_asignaciones(substr($hoy, 0, 10), $receptores[$index_op]);
                        $update = 0;
                        if(!empty($control_receptor))
                        {
                            $nueva_cantidad = $control_receptor[0]->asignados + 1;
                            $data = Array(
                                'asignados' => $nueva_cantidad,
                                'update_at' => $hoy
                            );

                            $update = $this->solicitudAsignacion->update($control_receptor[0]->id, $data);   
                        }
                        
                        if($update < 1 || empty($control_receptor)) 
                        {
                            $data = Array(
                                'asignados' => 1,
                                'update_at' => $hoy,
                                'id_operador' => $receptores[$index_op],
                                'fecha_control' => $hoy,
                                'aprobados' => 0,
                                'verificados' => 0,
                                'validados' => 0,
                                'rechazados' => 0,
                                'estado' => 0
                            );
                            $insert = $this->solicitudAsignacion->insert($data);
                        }

                        if($this->operadores->asignar_relacion_operador_solicitud($value, $solicitud->operador_asignado, $receptores[$index_op]) > 0)
                        {
                            $filas_afectadas += 1;
                            $data = Array('operador_asignado' =>$receptores[$index_op]);

                            //actualizamos el operador asignado en la tabla solicitud
                            $this->solicitudes->edit($value, $data);
                        } 

                        if($index_op >= count($receptores)-1){
                            $index_op = 0;
                        } else {
                            $index_op++;
                        }

                    }
                    
                   //consultamos los ids de los chat reasignables del designado para reasignar las relaciones
                   $chats = $this->operadores->get_ids_chats_asignados($this->post("designado"), ["inicio"=>$this->post("inicio"), "fin"=>$this->post("fin")]);
                   $result = 0;

                   if(!empty($chats))
                   {
                        $index_op = 0;
                        foreach ($chats as $key => $value) {
                            
                            $data =[
                                "id_operador" => $receptores[$index_op],
                                "id_chat" => $value["id"],
                                "fecha_cambio" => $hoy
                            ];
                           
                            //insertamos el registro de asignacion en la tabla de relacion operador chat
                            $this->operadores->set_chat_relacion_operador($data);
                            
                            $parametros = Array(
                                'id' => $value["id"]
                            );
                            //reasignamos todos los chat que tiene asignados el operador asignado
                            $result += $this->operadores->reasignar_new_chat($receptores[$index_op], $parametros);
    
                            if($index_op >= count($receptores)-1){
                                $index_op = 0;
                            } else {
                                $index_op++;
                            }
                        }
                    }

                    if ($filas_afectadas < 1 ) {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => "Hubo un error al reasignar las solicitudes."];
                    } else {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => $filas_afectadas." Solicitudes fueron reasignadas y ".$result." chats"];
                    } 

                    break;

                case 'asig-4':
                    //consultamos los ids de los chat reasignables del designado para reasignar las relaciones
                    $chats = $this->operadores->get_ids_chats_asignados($this->post("designado"), ["inicio"=>$this->post("inicio"), "fin"=>$this->post("fin")]);
                    $result = 0;
                    if(!empty($chats))
                    {
                        foreach ($chats as $key => $value) {
                            
                            $data =[
                                "id_operador" => $this->post('receptor'),
                                "id_chat" => $value["id"],
                                "fecha_cambio" => $hoy
                            ];

                            //insertamos el registro de asignacion en la tabla de relacion operador chat
                            $result+=$this->operadores->set_chat_relacion_operador($data);
                        }
                        $parametros = Array(
                            'designado' => $this->post("designado"),
                            'inicio' => $this->post('inicio'),
                            'fin' => $this->post('fin')
                        );
                        //reasignamos todos los chat que tiene asignados el operador esignado
                        $result = $this->operadores->reasignar_new_chat($this->post('receptor'), $parametros);
                    }

                    if ($result < 1 ) {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => "No se reasignaron chats."];
                    } else {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => $result." Chats fueron reasignados"];
                    } 
                    break;
                case 'asig-5':

                    //consultamos si tiene un chat asignado
                    $chats = explode(',', $this->post('chats'));
                    $result = 0;        
                    if(!empty($chats)){

                        foreach ($chats as $key => $value) {
                            $data =[
                                "id_operador" => $this->post('receptor'),
                                "id_chat" => $value,
                                "fecha_cambio" => $hoy
                            ];
                            
                            //insertamos el registro de asignacion en la tabla de relacion operador chat
                            $this->operadores->set_chat_relacion_operador($data);
                            
                            $parametros = Array(
                                'id' => $value
                            );
                            //reasignamos todos los chat que tiene asignados el operador asignado
                            $result += $this->operadores->reasignar_new_chat($this->post('receptor'), $parametros);
                        }
                    }

                    if ($result < 1 ) {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => "Hubo un error al reasignar los chats."];
                    } else {
                        $status = parent::HTTP_OK;
                        $response = ['status' => $status, 'message' => $result." Chats fueron reasignadas"];
                    } 

                    break;

                default:
                    break;
            }
        }
        $this->response($response);
    }

    public function operadoresVentas_get($tipo_operadores)
    {   
        $filtro['equipo']              = $this->session->userdata("equipo");
        $filtro['estado']              = '1';
        $filtro['tipo_operador']       = $this->session->userdata("tipo_operador");
        $filtro['tipo_operador_where'] = $tipo_operadores;
        $data['agentes']               = $this->operadores->get_lista_operadores_by($filtro);
        
        if(!empty($data['agentes']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>TRUE,'data' => $data['agentes']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>FALSE,'data' => []];
        }
        $this->response($response);
    }
    
    public function tableAgentes_get(){
        $data['agentes'] = $this->operadores->tableAgentes();
         if(!empty($data['agentes']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>TRUE,'data' => $data['agentes']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>FALSE,'message' => 'No fue posible cargar la tabla de agentes'];
           
        }
        $this->response($response);
    }
    public function tableCreateCampania_get(){
        $data['agentes'] = $this->operadores->tableCreateCampania();
         if(!empty($data['agentes']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>TRUE,'data' => $data['agentes']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>FALSE,'message' => 'No fue posible cargar la tabla de agentes'];
           
        }
        $this->response($response);
    }

    public function cambioEstadoCampania_post(){
        $id_estado=$this->input->post('id_estado');
        $id=$this->input->post('id');
        // var_dump($id_estado,$id_horario); die;
        $parametros = array(
            'estado_campania' => $id_estado,
            'id' =>$id
        );
        $data = $this->operadores->cambioEstadoCampania($parametros);
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);

    }
    public function cambioEstadoSkill_post(){
        $id_estado=$this->input->post('id_estado');
        $id=$this->input->post('id');
        $parametros = array(
            'estado_skill' => $id_estado,
            'id' =>$id
        );
        $data = $this->operadores->cambioEstadoSkill($parametros);
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);

    }
    public function registrar_campania_post(){
        $hoy = date("Y-m-d H:i:s");
        $preview=$this->post('preview');
        $id_campania=$this->post('id_campania');
        if($preview=='yes'){
        $estado_campania='preview';
        }else{
        $estado_campania='predictive';
        }

        $data=array(
            'id_campania'=> $id_campania,
            'id_skill'=> $this->post('id_skill_campania'),
            'nombre'=>$this->post('name_campania'),
            'descripcion'=>$this->post('descripcion_campania'),
            'fecha'=>$hoy,
            'central'=>$this->post('central_campania'),
            'estado_campania'=>1,
            'tipo_campania'=>$estado_campania
        );
            $validacionCampania = $this->operadores->validacionCampania($id_campania);

            if($validacionCampania==0){
                $result = $this->operadores->insertar_campania($data);
                if ( $result > 0 ) 
                {
                    // Set HTTP status code
                    $status = parent::HTTP_OK;
                    $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Campaña registrada con éxito'];  
                } else
                {
                    $status = '200';
                    $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo agregar el Campaña'];  
                }
            }else{
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'Ya existe ID Campaña'];
            }
        $this->response($response);
    }

    public function get_campania_update_get($id){
        $data['campanias_update'] = $this->operadores->get_campania_update($id);
        //  var_dump($data['campanias_update']);die;
        if(!empty($data['campanias_update']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data['campanias_update']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'La campania no esta Habilitada'];
        }
            
        $this->response($response);
    }
    public function updateCamapania_post(){   
            $hoy = date("Y-m-d H:i:s");
        $preview=$this->post('preview');

        if($preview=='yes'){
        $estado_campania='preview';
        }else{
        $estado_campania='predictive';
        }
            $parametros = array(
            'id'=>$this->post('id'),
            'nombre'=> $this->post('name_campania'),
            'descripcion'=> $this->post('descripcion_campania'),
            'id_skill'=>$this->post('id_skill_campania'),
            'central'=>$this->post('central_campania'),
            'fecha'=>$hoy,
            'tipo_campania'=>$estado_campania
            );
            // var_dump($parametros);die;
            $result = $this->operadores->updatedoCampania($parametros);
            // var_dump($result);die;
            if ( $result > 0 ) 
            {
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Campaña actualizada con éxito'];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo actualizar La Campaña'];  
            }
        
        $this->response($response);
    }
    public function tableSkill_get(){
        $data['skill'] = $this->operadores->tableSkill();
         if(!empty($data['skill']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>TRUE,'data' => $data['skill']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>FALSE,'message' => 'No fue posible cargar la tabla de skill'];
           
        }
        $this->response($response);
    }

    public function asignarSkills_post()
    {
        $hoy = date("Y-m-d H:i:s");
        $asignacion = FALSE;
        $id_skill=$this->post('id_skill');
        $skills=$this->post('skills');
        $id_operador = explode(",", $skills);
        $asignados = $this->operadores->get_skill_agentes($id_operador,$id_skill);
       
        $asignacion = $this->operadores->delete_skills($asignados,$id_skill,$id_operador);

        foreach ($id_operador as $value){
            $parametros = array(
                'id_operador'=> trim($value),
                'id_skill' => $id_skill,
                'user_action' => $this->session->userdata("id_usuario"),
                'fecha' => $hoy
            );

            if(trim($value) != ""){
                $asignacion = $this->operadores->asignar_skill($parametros);
            }
        }

    
        if (isset ($asignacion) ) 
        {
            // Set HTTP status code
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Asignacion Skill registrada con éxito'];  
        } else
        {
            $status = '200';
            $response = ['status' => $status, 'ok' => FALSE, 'message' => ' Asignacion NO se pudo realizar'];  
        }    
        $this->response($response);
    }

    public function registrarSkill_post(){
        $id_skill=$this->post('id_skill');
        $data=array(
            'id_skill'=>$id_skill,
            'id_grupos_operadores'=> $this->post('id_grupos_operadores'),
            'descripcion'=>$this->post('descripcion'),
            'central'=>$this->post('central'),
            'estado_skill'=>1
        );
            $validation = $this->operadores->validacionSkill($id_skill);
            // var_dump( $validation); die;
            if($validation==0){
                $result = $this->operadores->insertar_skill($data);
                if ( $result > 0 ) 
                {
                    // Set HTTP status code
                    $status = parent::HTTP_OK;
                    $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Skill registrada con éxito'];  
                } else
                {
                    $status = '200';
                    $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo agregar el skill'];  
                }
            }else{
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'Ya existe ese ID skill'];  
            }
        $this->response($response);
    }

    public function get_operador_skill_get($id_skill)
    {
        $result = $this->operadores->get_operador_skill($id_skill);        
        $result2 = $this->operadores->get_operador_skill_disponible($id_skill);

        // var_dump($result2); die;
            if (isset($result2)) 
            {
                // Set HTTP status code

                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Existe Skill _disponible','item_disponible' => $result2];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'Error de skill'];  
            }
            

            if($result > 0){
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Existe Agente con ese Skill','item' => $result,'item_disponible' => $result2 ];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No existe Agente con ese Skill','item' => $result,'item_disponible' => $result2];  
            }
        
        $this->response($response);
    }
    public function updateSkill_post(){    
            $parametros = array(
            'id'=>$this->post('id'),
            'id_skill'=> $this->post('id_skill'),
            'id_grupos_operadores'=> $this->post('id_grupos_operadores'),
            'descripcion'=>$this->post('descripcion'),
            'central'=>$this->post('central'),
            );
            // var_dump($parametros);die;
            $result = $this->operadores->updatedoSkill($parametros);
            if ( $result > 0 ) 
            {
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Skill actualizada con éxito'];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo actualizar el Skill'];  
            }
        
        $this->response($response);
    }

    public function get_chats_get($numero)
    {
        //consultamos los chats
        $chat = $this->operadores->findBySenderNumber($numero);

        if(!empty($chat))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $chat];  
        } else {
            $status = '200';
            $response = ['status' => $status, 'message' => 'No hay resgistros '];
        }

        $this->response($response);
    }

    public function get_lista_operador_central_get()
    {
        $data['lista_operadores']= $this->operadores->get_lista_operador_central();
  
        if(!empty($data['lista_operadores']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data['lista_operadores']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'No fue posible cargar la lista de operadores'];
        }
            
        $this->response($response);
    }
    
    public function get_lista_operadores_activos_get()
    {
        $data['lista_operadores'] = $this->operadores->get_lista_operadores_by(['equipo' => $this->session->userdata('equipo'), 'tipo_operador' => $this->session->userdata('tipo_operador'), 'estado' => "1"]);
            
        if(!empty($data['lista_operadores']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data['lista_operadores']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'No fue posible cargar la lista de operadores'];
        }
            
        $this->response($response);
    }


public function get_ausencias_operador_get($operador)
    {
        $data['ausencias'] = $this->operadores->get_ausencias_operador(['id_operador'=>$operador]);
            
        if(!empty($data['ausencias']))
        {
    
            for($i =0; $i < count($data['ausencias']); $i++){
                $array = json_decode( json_encode($data['ausencias'][$i]), true);

                if($array['operador_responsable'] > 0){
                    $operador_responsable = $this->operadores->get_operadores_by(['id_operador_buscar' => $array['operador_responsable']]);
                    $nombre_op = json_decode(json_encode($operador_responsable), true);
                    $nombre_op = $nombre_op[0]['nombre_apellido'];    
                }else{
                    $nombre_op = '';
                }
                $items []= array("id" => $array['id'],
                    "idoperador"=>$array['idoperador'],
                    "fecha_inicio"=>$array['fecha_inicio'],
                    "fecha_final"=>$array['fecha_final'],
                    "motivo"=>$array['motivo'],
                    "estado"=>$array['estado'],
                    "fecha_creacion"=>date("d-m-Y H:i:s", strtotime($array['fecha_creacion'])),
                    "operador_responsable"=> $nombre_op
                );

            }
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $items]; 
          
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'El operador seleccionado aun no tiene registros de ausencias programadas'];
        }
            
        $this->response($response);
    }
    public function validacionAgente_post(){
        $id_operador= $this->post('id_operador');
        $central= $this->post('central');
        // var_dump($id_operador,$central);die;
        $validacionAgente = $this->operadores->validacionAgente($id_operador,$central);
        // var_dump($validacionAgente); die;
        if ( $validacionAgente > 0 ) 
            {
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Operador se encuentra Registrado'];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'Operador no se encuentra Registrado'];  
            }
        $this->response($response);
    }

    public function registar_agente_post(){
        $data=array(
            'id_operador'=> $this->post('id_operador'),
            'id_agente'=> $this->post('id_agente'),
            'id_skill'=>$this->post('id_skill'),
            'central'=>$this->post('central'),
            'estado_agente'=>1
        );

            $result = $this->operadores->insertar_agente($data);
            if ( $result > 0 ) 
            {
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Agente registrada con éxito'];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo agregar el Agente'];  
            }
        $this->response($response);
    }
    public function cambioEstadoAgente_post(){
        $id_estado=$this->input->post('id_estado');
        $id_agente=$this->input->post('id_agente');
        // var_dump($id_estado,$id_horario); die;
        $parametros = array(
            'estado_agente' => $id_estado,
            'id' =>$id_agente,
        );
        $data = $this->operadores->cambioEstadoAgente($parametros);
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);

    }
public function updateAgente_post(){    
            $parametros = array(
            'id'=>$this->post('id'),
            'id_agente'=> $this->post('id_agente'),
            'id_skill'=>$this->post('id_skill'),
            'central'=>$this->post('central'),
            );
            $result = $this->operadores->updatedoAgente($parametros);
            if ( $result > 0 ) 
            {
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Agente actualizada con éxito'];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo actualizar el Agente'];  
            }
        
        $this->response($response);
    }
    public function registar_horario_operadores_post(){
        $hoy = date("Y-m-d H:i:s");
        $string_diasTrabajos= $this->post('dias_trabajos');
        if ($this->_validate_horario_operadores()) 
        {   
            $data = array(
                'id_operador' => $this->post('id_operador'),
                'dias_trabajo' => $string_diasTrabajos,
                'hora_entrada' => $this->post('hora_entrada'),
                'hora_salida' => $this->post('hora_salida'),
                'id_usuario_modificacion'=> $this->session->userdata("id_usuario"),
                'fecha_modificacion' => $hoy,
                'estado_horario' => 1
            );
            $result = $this->operadores->insertar_horario_operadores($data);
            if ( $result > 0 ) 
            {
                // Set HTTP status code
                $date = array( 
                    'id_operador'=>$this->session->userdata("idoperador"),

                    'id_registro_afectado'=>$result,
                    'tabla'=>'horario_operador',
                    'detalle'=> '[REGISTRO_HORARIO] Datos: '.json_encode($data),
                    'accion'=> 'INSERT',
                    'fecha_hora'=> $hoy
                    );
                $track = $this->operadores->track_interno($date);
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Horario registrada con éxito'];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo agregar el horario'];  
            }
        }else 
        {
            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->form_validation->error_array();
        }
        $this->response($response);
    }

     public function get_agente_update_get($id){
        $data['agente'] = $this->operadores->get_agente_update($id);
        //  var_dump($data['agente']);die;
        if(!empty($data['agente']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data['agente']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'El operador seleccionado aun no tiene Agente Habilitado'];
        }
            
        $this->response($response);
    }

         public function get_skill_update_get($id){
        $data['skill'] = $this->operadores->get_skill_update($id);
        //  var_dump($data['skill']);die;
        if(!empty($data['skill']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data['skill']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'El operador seleccionado aun no tiene skill Habilitado'];
        }
            
        $this->response($response);
    }

    public function cambioEstadoHorario_post(){
        $id_estado=$this->input->post('id_estado');
        $id_horario=$this->input->post('id_horario');
        // var_dump($id_estado,$id_horario); die;
        $parametros = array(
            'estado_horario' => $id_estado,
            'id' =>$id_horario,
        );
        $data = $this->operadores->cambioEstadoHorario($parametros);
        if($data > 0){
            $date = array( 
                'id_operador'=>$this->session->userdata("idoperador"),
    
                'id_registro_afectado'=>$id_horario,
                'tabla'=>'horario_operador',
                'detalle'=> '[ESTADO_HORARIO] Datos:'.json_encode($parametros),
                'accion'=> 'UPDATE',
                'fecha_hora'=> date("Y-m-d H:i:s")
                );
            $track = $this->operadores->track_interno($date);

        }
        
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);

    }


    public function updatedoHorario_post(){
        $hoy = date("Y-m-d H:i:s");
        $string_diasTrabajos= $this->post('dias_trabajos');
        if ($this->_validate_horario_operadores()) 
        {   
            $parametros = array(
                'id'=>$this->post('id'),
                'id_operador' => $this->post('id_operador'),
                'dias_trabajo' => $string_diasTrabajos,
                'hora_entrada' => $this->post('hora_entrada'),
                'hora_salida' => $this->post('hora_salida'),
                'id_usuario_modificacion'=> $this->session->userdata("id_usuario"),
                'fecha_modificacion' => $hoy
            );
            $result = $this->operadores->updatedoHorario($parametros);
            if ( $result > 0 ) 
            {
                $date = array( 
                    'id_operador'=>$this->session->userdata("idoperador"),
                    'id_registro_afectado'=>$this->post('id'),
                    'tabla'=>'horario_operador',
                    'detalle'=> '[HORARIO_OPERADOR] Datos:'.json_encode($parametros),
                    'accion'=> 'UPDATE',
                    'fecha_hora'=> $hoy
                    );
                $track = $this->operadores->track_interno($date);
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Horario actualizada con éxito'];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo actualizar el horario'];  
            }
        }else 
        {
            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->form_validation->error_array();
        }
        $this->response($response);
    }

    public function tableHorariosOperadores_get(){
        $data = $this->operadores->tableHorariosOperadores();
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);
    }
    

 public function get_horario_operador_get($id_horario){
        $data['horario'] = $this->operadores->get_horario_operador($id_horario);
            // var_dump($data['horario']);die;
        if(!empty($data['horario']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data['horario']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'El operador seleccionado aun no tiene Horario designado'];
        }
            
        $this->response($response);
    }
    
    public function get_horario_operador_update_get($id_horario){
        $data['horario'] = $this->operadores->get_horario_operador_update($id_horario);
            // var_dump($data['horario']);die;
        if(!empty($data['horario']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data['horario']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'El operador seleccionado aun no tiene Horario Habilitado'];
        }
            
        $this->response($response);
    }

    public function registrar_ausencia_operador_post()
    {
        $hoy = date("Y-m-d H:i:s");
        if ($this->_validate_ausencia_operador()) 
        {
            $fecha = explode("|",$this->post('fecha'));
            
            $fecha_inicio = new DateTime(trim($fecha[0]));
            $fecha_inicio =  $fecha_inicio->format('Y-m-d H:i:s');
            $fecha_final = new DateTime(trim($fecha[1]));
            $fecha_final = $fecha_final->format('Y-m-d')." 23:59:59";
            $data = array(
                'idoperador' => $this->post('id'),
                'fecha_inicio' => $fecha_inicio,
                'fecha_final' => $fecha_final,
                'motivo' => $this->post('motivo'),
                'estado' => '1',
                'fecha_creacion' => $hoy,
                'operador_responsable'=>$this->session->userdata("idoperador")
            );
            $result = $this->operadores->insertar_ausencia($data);
            if ( $result > 0 ) 
            {
                $date = array( 
                    'id_operador'=>$this->session->userdata("idoperador"),
                    
                    'id_registro_afectado'=>$result,
                    'tabla'=> 'ausencias_operadores',
                    'detalle'=> '[AUSENCIAS_OPERADOR] Datos: '.json_encode($data),
                    'accion'=> 'INSERT',
                    'fecha_hora'=> $hoy
                );
                $track = $this->operadores->track_interno($date);
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Ausencia registrada con éxito'];  
                
                if($fecha_inicio >= date("Y-m-d")." 00:00:00"){

                    $this->_set_solicitudes_reasignar($this->post('id'), $fecha_inicio);
                    
                }
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo agregar el registro'];  
            }
        }else 
        {
            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->form_validation->error_array();
        }
        $this->response($response);
    }

    public function set_estado_ausencia_post()
    {
        if($this->_validate_estado_ausencia())
	    {
			   $data = array( 
                       'id'=>$this->post('ausencia'),
					   'estado'=>$this->post('estado')
                       );
                       
               $result = $this->operadores->actualizar_ausencia($data);
               
               if($result > 0)
               {
                $date = array( 
                    'id_operador'=>$this->session->userdata("idoperador"),

                    'id_registro_afectado'=>$this->post('ausencia'),
                    'tabla'=> 'ausencias_operadores',
                    'detalle'=>'[ESTADO_AUSENCIAS] datos:'.json_encode($data),
                    'accion'=> 'UPDATE',
                    'fecha_hora'=> date("Y-m-d H:i:s")
                    );
                $track = $this->operadores->track_interno($date);
                    $status = parent::HTTP_OK;
                    $response = ['status' => ['code' => $status, 'ok' => TRUE],
                                                     'message' => "Registro actualizado",
                                                    ];
                } else {
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response = ['status' => ['code' => $status, 'ok' => FALSE], 
                                                'errors' => "Falló al actualizar el registro"];
                }
        } else  {
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->form_validation->error_array();
        }

        $this->response($response,$status);
    }

    public function update_ausencia_post()
    {
        if($this->_validate_ausencia_operador())
	    {
            $fecha = explode("|",$this->post('fecha'));

            $fecha_inicio = new DateTime(trim($fecha[0]));
            $fecha_inicio =  $fecha_inicio->format('Y-m-d H:i:s');
            $fecha_final = new DateTime(trim($fecha[1]));
            $fecha_final = $fecha_final->format('Y-m-d')." 23:59:59";

            $data = array(
                'id'=>$this->post('id'),
                'fecha_inicio' => $fecha_inicio,
                'fecha_final' => $fecha_final,
                'motivo' => $this->post('motivo'),
            );

            $result = $this->operadores->actualizar_ausencia($data);
            if ( $result > 0 ) 
            {
                $date = array( 
                    'id_operador'=>$this->session->userdata("idoperador"),

                    'id_registro_afectado'=>$this->post('id'),
                    'tabla'=> 'ausencias_operadores',
                    'detalle'=> '[AUSENCIAS_OPERADOR] datos: '.json_encode($data),
                    'accion'=> 'UPDATE',
                    'fecha_hora'=> date("Y-m-d H:i:s")
                    );
                $track = $this->operadores->track_interno($date);
                // Set HTTP status code
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Ausencia actualizada con éxito'];  
            } else
            {
                $status = '200';
                $response = ['status' => $status, 'ok' => FALSE, 'message' => 'No se pudo actualizar el registro'];  
            }
        } else  {
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['status']['ok'] = FALSE;
                $response['errors'] = $this->form_validation->error_array();
        }

        $this->response($response,$status);
    }

/***************************************************************************/
// VALIDATIONS
/***************************************************************************/


	function _validate_save_input_operador($operacion)
	{
            if($operacion == "registrar")
            {
                $this->form_validation->set_rules('estado', 'Estado', 'required');
                $this->form_validation->set_rules('documento', 'Documento', 'required');
                $this->form_validation->set_rules('usuario', 'Usuario', 'required');
                $this->form_validation->set_rules('password', 'Clave', 'required');
            }
            $this->form_validation->set_rules('wathsapp', 'whatsapp', 'required');
            $this->form_validation->set_rules('apellido', 'Apellido', 'required');
            $this->form_validation->set_rules('apellido', 'Apellido', 'required');
            $this->form_validation->set_rules('nombre_pila', 'Nombre de Pila', 'required');
            $this->form_validation->set_rules('mail', 'Correo Electronico', 'required');
            $this->form_validation->set_rules('tipo_operador', 'Tipo de Operador', 'required');
            $this->form_validation->set_rules('equipo', 'Equipo', 'required');
            $this->form_validation->set_rules('token', 'Verificacion', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }

    function _validate_estado_operador()
	{
		$this->form_validation->set_rules('estado', 'Estado', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }

    function _validate_cambio_clave()
	{
		$this->form_validation->set_rules('password', 'Clave', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }

    function _validate_nuevo_tipo()
	{
		$this->form_validation->set_rules('tipo', 'tipo de operador', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }

    function _validate_asignaciones_operador()
    {
        $this->form_validation->set_rules('inicio', 'Desde', 'required');
        $this->form_validation->set_rules('fin', 'Hasta', 'required');
        $this->form_validation->set_rules('operador', 'Operador', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }
    public function _validate_horario_operadores(){
        $this->form_validation->set_rules('id_operador', 'Id_operador', 'required');
        $this->form_validation->set_rules('hora_entrada', 'Hora_entrada', 'required');
        $this->form_validation->set_rules('hora_salida', 'Hora_salida', 'required');
        $this->form_validation->set_rules('dias_trabajos', 'Dias_trabajos', 'required');


		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }

    public function _validate_ausencia_operador()
    {
        $this->form_validation->set_rules('id', 'Operador', 'required');
        $this->form_validation->set_rules('fecha', 'Fecha', 'required');
        $this->form_validation->set_rules('motivo', 'Motivo', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }
    
    function _validate_estado_ausencia()
	{
		$this->form_validation->set_rules('ausencia', 'id', 'required');
		$this->form_validation->set_rules('estado', 'Estado', 'required');

		$this->form_validation->set_message('required', 'La información suministrada no es correcta');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
    }

/***************************************************************************/
// Functions
/***************************************************************************/
    function registrar_usuario()
    {
        $hoy = date("Y-m-d H:i:s");
        $usuario = $this->security->xss_clean(strip_tags($this->post('usuario')));
        $password = $this->security->xss_clean(strip_tags($this->post('password')));

        $parametros = array(
            'first_name'=>$this->post('nombre'),
            'last_name'=>$this->post('apellido'),
            'active'=>$this->post('estado'),
            'email'=>$this->input->post('mail'), 
            'username'=> $usuario,
            'password'=> encrypt($password),
            'user_action' => $this->session->userdata("id_usuario"),
            'active' => $this->input->post('estado'),
            'modified_on' => $hoy,
            'created_on' => $hoy
        );

        $registrar = $this->users->registrar($parametros);

        return $registrar;
    }

    function asignar_modulos($modulos, $usuario)
    {
        $hoy = date("Y-m-d H:i:s");
        $asignacion = FALSE;
        $asignados = $this->modulos_usuarios->get_modulos_usuario($usuario);
        

            if( !empty($asignados))
            {
                $asignacion = $this->modulos_usuarios->delete_asignaciones($usuario);
            }

            foreach ($modulos as $value) :
                $parametros = array(
                    'id_usuario'=> $usuario,
                    'id_modulo' => trim($value),
                    'orden' => '1',
                    'user_action' => $this->session->userdata("id_usuario"),
                    'created_on' => $hoy
                );

                if(trim($value) != "")
                    $asignacion = $this->modulos_usuarios->asignar_modulos($parametros);  
            
            endforeach;

        return $asignacion;
    }


    public function cambio_clave_post() {
        $username = $this->session->userdata('user')->username;
        $id_usuario = $this->session->userdata('user')->id;
        $oldPassword = encrypt($this->post('oldPassword'));
        $newPassword = $this->post('newPassword');
        $user = $this->users->get_user_login($username, $oldPassword);
        if ($user) {
            $curl = curl_init();
            $options[CURLOPT_POSTFIELDS] = ['id_usuario' => $id_usuario, 'password' => $newPassword];
            $options[CURLOPT_URL] =  base_url('api/operadores/actualizar_clave');
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_RETURNTRANSFER] = TRUE;
            $options[CURLOPT_TIMEOUT] = 30;
            $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

            curl_setopt_array($curl, $options);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            $response = json_decode($response);

            if ($err) {
                $response['error'] = 'cURL Error #:' . $err;
            }

            return $this->response($response);
        } else {
            $status = parent::HTTP_OK;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['message'] = 'Contraseña anterior incorrecta';
            return $this->response($response);
        }
    }

    private function _set_solicitudes_reasignar($id_operador, $fecha){
        
        $solicitudes = $this->solicitudes->get_solicitudes_asignadas($id_operador, $fecha);
        $operador = $this->operadores->get_operadores_by(['idOperador'=>$id_operador]);

        

        $cantidad = count($solicitudes);
        $ids = implode(",", array_column($solicitudes, 'id'));
        
        // Si el operador no tiene solicitudes asignadas, no hace nada.
        if ($cantidad > 0) {
            // si el operador tiene solicitudes modificio el operador asignado a cero
            $editOperadorSolicitudes = $this->solicitudes->editBlock($ids, ['operador_asignado' => 0 ]);
            // modifico el estado de la relacion de A a H (historico) $data ['estado'=>'H']
            $cantidad = $this->solicitudes->edit_relacion_solicitud_operador($ids, ['estado'=>'H'], $id_operador, $fecha);
            $parametros = [];
            if ($cantidad > 0) {
                $control = $this->operadores->get_control_asignaciones(substr($fecha, 0, 10), $id_operador);
                if(!empty($control)){
                    $param=[
                        "id_operador" => $id_operador,
                        "fecha_inicio" => substr($fecha, 0, 10)
                    ];
                    $nueva_cantidad = intval($control[0]->asignados) - $cantidad;
                    $nueva_cantidad_primaria = intval($control[0]->primaria);

                    if($control[0]->primaria >= $cantidad)
                        $nueva_cantidad_primaria = intval($control[0]->primaria) - $cantidad;

                    $res = $this->solicitudAsignacion->updateBy($param, ['asignados' => $nueva_cantidad, 'primaria' => $nueva_cantidad_primaria, 'dependientes' => 0, 'independientes' => 0]);
                }
            }
            
        }
        $parametros = Array(
            'operador' =>  $id_operador
        );

        if(!empty($operador) &&  ($operador->tipo_operador == 5 || $operador->tipo_operador == 6)){
            $this->operadores->reasignar_chat(42, $parametros);

        }else{
            $this->operadores->reasignar_chat(70, $parametros);
        }

        //reasignamos todos los chat que tiene asignados el operador asignado
    }
    
    public function get_tipos_operadores_get()
    {
        $result = $this->operadores->get_tipos_operador();

        if($result){
            $status = parent::HTTP_OK;
            $response = ['status' => ['code'    => $status, 'ok' => TRUE],
                                      'data' =>  $result,
                                    ];
        } else {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['status' => ['code' => $status, 'ok' => FALSE], 
            'errors' => "Falló obtener topos de operadores"];
        }

        $this->response($response,$status);
    }
}

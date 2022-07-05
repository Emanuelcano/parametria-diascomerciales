<?php
class Prestamo_model extends BaseModel {
    
    //ESTADOS DE SOLICITUD
        const PAGADO = 'PAGADO';
    //
    public $file_log = "";
    private $porcentFondoGarantia;
    private $fondoGarantia;

    public function __construct() {
        parent::__construct();
        $this->load->model('Solicitud_m','solicitud',TRUE);  
        $this->load->library('custom_log');
        $now = date('Y_m_d');
        $this->file_log = "pago_prestamo_".$now.".log";
        $this->porcentFondoGarantia = 0;
        $this->fondoGarantia = 0;
    }
    
    function Procesando($idsolicitud){

        $this->db->trans_begin();
        /**
        * Cambio el estado a la solicitud a PAGADO
        */
        $this->db->set('estado', 'PROCESANDO');
        $this->db->where('id', $idsolicitud);
        $this->db->update('solicitudes.solicitud');
        
        //luego de la ejecucion: insert, update, delete, get etc etc se controla la excepcion del error sql y continua el script
        //checkDbError($this->db);


        $return = $this->db->trans_status();

        if ($return === FALSE){
            //si hubo al menos un error, hace el rollback
            $this->db->trans_rollback();
            $datarespuesta['error']=true;
        }else{
            //si está todo ok hace el commit de todas las querys
            $this->db->trans_commit();
            $datarespuesta['error']=false;
        }

        return $datarespuesta;

    }

    function Rechazar($idsolicitud){

        $this->db->trans_begin();
        /**
        * Cambio el estado a la solicitud a PAGADO
        */
        $this->db->set('estado', 'RECHAZADO');
        $this->db->where('id', $idsolicitud);
        $this->db->update('solicitudes.solicitud');
        
        //luego de la ejecucion: insert, update, delete, get etc etc se controla la excepcion del error sql y continua el script
        //checkDbError($this->db);


        $return = $this->db->trans_status();

        if ($return === FALSE){
            //si hubo al menos un error, hace el rollback
            $this->db->trans_rollback();
            $datarespuesta['error']=true;
        }else{
            //si está todo ok hace el commit de todas las querys
            $this->db->trans_commit();
            $datarespuesta['error']=false;
        }

        return $datarespuesta;

    }
    
    function PosponerPago($idsolicitud){

        $this->db->trans_begin();
        /**
        * Cambio el estado a la solicitud a PAGADO
        */
       
       /* $this->db->set('estado', 'POSPONER');
        $this->db->where('id', $idsolicitud);
        $this->db->update('solicitudes.solicitud');*/

        $this->db->set('pagado', '2');
        $this->db->where('id_solicitud', $idsolicitud);
        $this->db->update('solicitudes.solicitud_txt');
        
        //luego de la ejecucion: insert, update, delete, get etc etc se controla la excepcion del error sql y continua el script
        //checkDbError($this->db);


        $return = $this->db->trans_status();

        if ($return === FALSE){
            //si hubo al menos un error, hace el rollback
            $this->db->trans_rollback();
            $datarespuesta['error']=true;
        }else{
            //si está todo ok hace el commit de todas las querys
            $this->db->trans_commit();
            $datarespuesta['error']=false;
        }

        return $datarespuesta;

    }
    
    function guardarComprobante($data){

        $this->db->trans_begin();
       
        $idSolicitud= $data['idSolicitud'];
        
        $nombre_archivo= $data['nombre_archivo'];
        $url=$data['url'];

       $data = array(
            'id_solicitud_compr' => $idSolicitud,
            'url_comprobante' => $url,
            'nombre_comprobante' => $nombre_archivo                     
        );
       $comprobantesAnteriores =  $this->buscarComprobante($idSolicitud);
       if(count($comprobantesAnteriores) > 0){
           $this->db->where('id', $comprobantesAnteriores[0]->id);
           $this->db->update('solicitudes.comprobante_credito', $data);
       }else{
            $this->db->insert('solicitudes.comprobante_credito', $data);
       }
        
        //luego de la ejecucion: insert, update, delete, get etc etc se controla la excepcion del error sql y continua el script
        //checkDbError($this->db);


        $return = $this->db->trans_status();

        if ($return === FALSE){
            //si hubo al menos un error, hace el rollback
            $this->db->trans_rollback();
            $datarespuesta['error']=true;
        }else{
            //si está todo ok hace el commit de todas las querys
            $this->db->trans_commit();
            $datarespuesta['error']=false;
        }

        return $datarespuesta;

    }
    
    function buscarComprobante($idSolicitud){
        
        $query = $this->db->select('*')
                ->order_by("id", "desc")
                ->limit(1)
                ->get_where('solicitudes.comprobante_credito', 'id_solicitud_compr ='.$idSolicitud);
        
        return $query->result();
        
    }
    /**
     * @param object[] solicitud
     * @return object[]
     * Proceso que ejecuta la creacion de un credito. 
     */
    public function PagarPrestamo($solicitud){
        $this->db->select('id');
        $clientes = $this->db->get_where('maestro.clientes', ['documento' => (string)$solicitud->documento]);
        //checkDbError($this->db);
        $this->custom_log->write_log("info", "------------------------------------------------",$this->file_log);
        $this->custom_log->write_log("info", "Documento: ".$solicitud->documento,$this->file_log);
        $this->custom_log->write_log("info", "id_solicitud: ".$solicitud->id,$this->file_log);
        $this->custom_log->write_log("info", "Cliente: ".$clientes->num_rows(),$this->file_log);
        if ($clientes->num_rows() > 0) {
            $this->custom_log->write_log("info", "ActualizarCliente: ". $solicitud->tipo_solicitud ,$this->file_log);
            
            $cliente = $clientes->row();
            $idCliente= $cliente->id;
            /**
             * El cliente ya existe (retanqueos)
            */
            $response = $this->actualizarCliente($idCliente, $solicitud);
            
        }else{
            $this->custom_log->write_log("info", "CrearCliente: ". $solicitud->tipo_solicitud ,$this->file_log);
            /**
            * El cliente no existe (primarios)
            */
            $response = $this->crearCliente($solicitud);
        }

        /*if($response['respuesta']){

            $this->cambiarEstadoSolicitud(self::PAGADO, $solicitud->id);
            //se verifica si el usuario es nuevo o existe
        }*/
       
        return $response;
    }
    
    /**
    * Cambio el estado a la solicitud a PAGADO
    */
    private function cambiarEstadoSolicitud($estado, $idSolicitud){
        $this->db->set('estado', $estado);
        $this->db->where('id', $idSolicitud);
        $this->db->update('solicitudes.solicitud');
        
        //luego de la ejecucion: insert, update, delete, get etc etc se controla la excepcion del error sql y continua el script
        //checkDbError($this->db);
    }
    
    /**
    * Creacion del cliente, credito, credito condicion, 
    */
    private function crearCliente($solicitud){

        $idSolicitud = $solicitud->id;
        $documento= $solicitud->documento;
        $id_tipo_documento= $solicitud->id_tipo_documento;
        //$fecha_expedicion= $solicitud->fecha_expedicion;
        $fecha_expedicion= (isset($solicitud->fecha_expedicion)) ? $solicitud->fecha_expedicion : date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i:s').'- 2 years'));
        $id_departamento= $solicitud->id_departamento;
        $id_localidad= $solicitud->id_localidad;
        $nombres= $solicitud->nombres;
        $apellidos= $solicitud->apellidos;
        $id_usuario= $solicitud->id_usuario;
        $fecha_alta= $solicitud->fecha_alta;
        $telefonosolicitud= $solicitud->telefono;
        $emailsolicitud= $solicitud->email;
        $pivotTransaccion = TRUE; //Controla las situaciones en la que no hay error de bd pero falto alguna insersion necesaria.
        /**
         * Buscar condicion desembolso de la solicitud
         */
        $condicionDesembolso = $this->getCondicionDesembolso($idSolicitud);
        $this->custom_log->write_log("info", "Condicion Desembolso: ".json_encode($condicionDesembolso), $this->file_log);
        
        //Inicio de la transaccion
        $this->db->trans_begin(); 
        if(!empty($condicionDesembolso)){

            $data_cliente = array(
                'id_tipo_documento' => $id_tipo_documento,
                'documento' => $documento,
                'fecha_expedicion' => $fecha_expedicion,
                'id_departamento_expedicion' => $id_departamento,
                'id_localidad_expedicion' => $id_localidad,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'id_usuario' => $id_usuario,
                'fecha_alta' => $fecha_alta,
                'identidad_verificada' => 1
            );
            
            $this->db->insert('maestro.clientes', $data_cliente);
            $this->custom_log->write_log("info", "data_cliente: ".json_encode($data_cliente),$this->file_log);
            $this->custom_log->write_log("info", "maestro.clientes: ".json_encode($this->db->error()),$this->file_log);
            //checkDbError($this->db);
            
            /**
             * Id del cliente creado
             */
            $id_cliente = $this->db->insert_id();
            $this->custom_log->write_log("info", "id_cliente: ".$id_cliente,$this->file_log);
            
            //Insertar registro en la maestro.credito
            $id_credito = $this->insertarCredito($condicionDesembolso, $id_cliente);
            
            $this->custom_log->write_log("info", "id_credito: ".$id_credito,$this->file_log);
            if($id_cliente > 0 && $id_credito > 0){
                
                //Set class propierties
                $this->setFondoGarantia($condicionDesembolso);
                //Creacion de la condicion del CREDITO
                $id_credito_condicion = $this->insertarCreditoCondicion($condicionDesembolso, $id_credito);
                $this->custom_log->write_log("info", "id_credito_condicion: ".$id_credito_condicion,$this->file_log);
                //Actualizar solicitud con id_cliente y id_credito
                $resultActualizacionSolicitud = $this->actualizarSolicitud($idSolicitud, $id_cliente, $id_credito);
                $this->custom_log->write_log("info", "resultActualizacionSolicitud: ".$resultActualizacionSolicitud,$this->file_log);

                if($resultActualizacionSolicitud > 0){

                    /**
                    * Creacion detalle CREDITO
                    */
                    $monto_cuota=$condicionDesembolso->total_devolver/$condicionDesembolso->plazo;
                    $fecha_pago_inicial = $condicionDesembolso->fecha_pago_inicial;
                    $fondo_garantia = $this->fondoGarantia / $condicionDesembolso->plazo;
                    for ($i = 1; $i <= $condicionDesembolso->plazo; $i++) {

                        $data_credito_detalle = array(
                            'id_credito' => $id_credito,
                            'numero_cuota' => $i,
                            'monto_cuota' => $monto_cuota,
                            'fecha_vencimiento' => $fecha_pago_inicial,
                            'monto_cobrar ' => $monto_cuota,
                            'aval ' => $fondo_garantia
                        );

                        $fecha_pago_inicial = date("Y-m-d",strtotime($fecha_pago_inicial."+ 1 month"));
                        $this->db->insert('maestro.credito_detalle', $data_credito_detalle);
                        $id_credito_detalle = $this->db->insert_id();
                        $this->custom_log->write_log("info", "data_credito_detalle: ".json_encode($data_credito_detalle),$this->file_log);
                        $this->custom_log->write_log("info", "id_credito_detalle: ".$id_credito_detalle,$this->file_log);
                        $this->custom_log->write_log("info", "maestro.credito_detalle: ".json_encode($this->db->error()),$this->file_log);
                        //checkDbError($this->db);
                    }
                

                    /**
                    * buscar referencias de la solicitud
                    */
                    $this->db->select('id_tipo_documento,documento,nombres_apellidos,telefono,id_parentesco,email');
                    $solicitud_referencias = $this->db->get_where('solicitudes.solicitud_referencias', ['id_solicitud' => $idSolicitud]);
                    //checkDbError($this->db);

                    foreach ($solicitud_referencias->result() as $referencia)
                    {
                        
                        $id_tipo_documento=$referencia->id_tipo_documento;
                        $documento=$referencia->documento;
                        $nombres_apellidos=$referencia->nombres_apellidos;
                        $telefono=$referencia->telefono;
                        $id_parentesco=$referencia->id_parentesco;
                        $email=$referencia->email;

                        /**
                        * crear referencias del cliente
                        */
                        $data_referencias = array(
                            'id_cliente' => $id_cliente,
                            'id_tipo_documento' => $id_tipo_documento,
                            'documento' => $documento,
                            'nombres_apellidos' => $nombres_apellidos,       
                            'telefono' => $telefono,
                            'id_parentesco' => $id_parentesco,
                            'email' => $email
                        );
                        if($id_parentesco !== null && $nombres_apellidos !== null){

                            $this->db->insert('maestro.referencias', $data_referencias);
                            //$id_referencia = $this->db->insert_id();
                            //checkDbError($this->db);
                        }
                        
                        if(isset($telefono) && $telefono != null && $telefono != ""){

                            /**
                            * crear agenda telefonica por cada referencia
                            */
                            $this->db->select('id');
                            $existe_num_ref = $this->db->get_where('maestro.agenda_telefonica',['id_cliente' => $id_cliente, 'numero' => $telefono]);

                            if(empty($existe_num_ref->result())){
                                $dataAgendaTelefonica = array(
                                    'id_cliente' => $id_cliente,
                                    'numero' => $telefono,
                                    'tipo' => 1,
                                    'fuente' => 3,
                                    'contacto'=>$nombres_apellidos,
                                    'estado' => 1
                                );
                                $dataAgendaTelefonica['id_parentesco'] = ($id_parentesco !== null ) ? $id_parentesco : "";
                                $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);
                                //$id_agenda_telefonica = $this->db->insert_id();
                                //checkDbError($this->db);
                            }
                        }

                    }

                    /**
                     * buscar datos bancarios de la solicitud
                     */
                    $this->db->select('id_banco,id_tipo_cuenta,numero_cuenta');
                    $solDatosBancarios = $this->db->get_where('solicitudes.solicitud_datos_bancarios', ['id_solicitud' => $idSolicitud]);
                    //checkDbError($this->db);

                    if(!empty($solDatosBancarios->row())){

                        $datosBancarios = $solDatosBancarios->row();
                        $id_banco=$datosBancarios->id_banco;
                        $id_tipo_cuenta=$datosBancarios->id_tipo_cuenta;
                        $numero_cuenta=$datosBancarios->numero_cuenta;
                        $estado=1;

                        /**
                        * crear datos bancarios del cliente
                        */
                        $dataAgendaBancaria = array(
                            'id_cliente' => $id_cliente,
                            'id_banco' => $id_banco,
                            'id_tipo_cuenta' => $id_tipo_cuenta,
                            'numero_cuenta' => $numero_cuenta,
                            'estado' => $estado
                        );
                        $this->db->insert('maestro.agenda_bancaria', $dataAgendaBancaria);
                        //$id_agenda_bancaria = $this->db->insert_id();
                        //checkDbError($this->db);

                        if($datosBancarios->id_banco != null ){

                            // inicio de acumulados por dias
                            $this->db->select('id');
                            $cuentas_bancarias = $this->db->get_where('maestro.cuentas_bancarias', 'id_banco ='.$id_banco);
                            //checkDbError($this->db);
                            $cuentas_bancarias = $cuentas_bancarias->row();

                            if (!empty($cuentas_bancarias)) {
                                $idCuentabancaria = $cuentas_bancarias->id;
                                $this->acumuladoCuentaBancariaDia($idCuentabancaria, $condicionDesembolso->capital_solicitado);
                            }else{
                                /**
                                * lo mismo de arriba pero con una cuenta predefinida
                                */
                                $idCuentabancaria = 1;
                                $this->acumuladoCuentaBancariaDia($idCuentabancaria, $condicionDesembolso->capital_solicitado);
                            }
                            // fin acumulado por dia
                        }
                    } 

                    if(($telefonosolicitud !== null && $nombres !== null)){

                        /**
                        * crear agenda telefonica
                        */
                        $dataAgendaTelefonica = array(
                            'id_cliente' => $id_cliente,
                            'numero' => $telefonosolicitud,
                            'tipo' => 1,
                            'fuente' => 1,
                            'contacto'=>$nombres . " ". $apellidos,
                            'estado' => 1,
                            'id_parentesco' => 0
                        );
                        $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);
                        //$id_agenda_telefonica = $this->db->insert_id();

                        //checkDbError($this->db);


                    }

                    if(($emailsolicitud !== null && $nombres !== null)){

                        /**
                        * crear agenda mail
                        */
                        $dataAgendaMail = array(
                            'id_cliente' => $id_cliente,
                            'cuenta' => $emailsolicitud,
                            'fuente' => 1,
                            'contacto'=>$nombres . " ". $apellidos,
                            'estado' => 1
                        );
                        $this->db->insert('maestro.agenda_mail', $dataAgendaMail);
                        //$id_agenda_mail = $this->db->insert_id();

                        //checkDbError($this->db);

                    }

                    /**
                    * crear imagenes cliente
                    */

                    $this->db->select('id_imagen_requerida,patch_imagen,fecha_carga,origen');
                    $solicitud_imagenes = $this->db->get_where('solicitudes.solicitud_imagenes', 'id_solicitud ='.$idSolicitud);
                    //checkDbError($this->db);

                    $solicitud_imagenes = $solicitud_imagenes->result();
                    if(!empty($solicitud_imagenes)){

                        foreach ($solicitud_imagenes as $imagenes) {

                            $id_imagen_requerida = $imagenes->id_imagen_requerida;
                            $patch_imagen = $imagenes->patch_imagen;
                            $fecha_carga = $imagenes->fecha_carga;
                            $origen = $imagenes->origen;

                            $dataImagenesClientes = array(
                                'id_cliente' => $id_cliente,
                                'id_imagen_requerida' => $id_imagen_requerida,
                                'patch_imagen' => $patch_imagen,
                                'fecha_carga' => $fecha_carga,
                                'origen' => $origen
                            );
                            $this->db->insert('maestro.imagenes_cliente', $dataImagenesClientes);
                            //$id_imagenes_clientes = $this->db->insert_id();

                            //checkDbError($this->db);

                        }
                    }
                    //Insertar en agenda telefonica los telefonos del buro

                    //Busca la ultima solicitud analisis
                    $this->db->where(['id_solicitud' => $idSolicitud]);
                    $this->db->select(['*']);
                    $this->db->from('solicitudes.solicitud_analisis');
                    $this->db->order_by('id','DESC');
                    $this->db->limit(1);
                    $query = $this->db->get();
                    $solicitud_analisis= $query->row();
                    
                    if(!empty($solicitud_analisis)){
                        
                        $situacion_laboral = ($solicitud_analisis->situacion_laboral === "tiene_salario_integral_actualmente") ? 1:0;
                        if($solicitud_analisis->nit_aportante !== null && $solicitud_analisis->razon_social_aportante !== null ){

                            $data_situacion_laboral = [
                                'id_cliente' => $id_cliente,
                                'numeroIdentificacionAportante' => $solicitud_analisis->nit_aportante,
                                'razonSocialAportante' => $solicitud_analisis->razon_social_aportante,
                                'tiene_salario_integral_actualmente' => $situacion_laboral
                            ];

                            $this->db->insert('maestro.situacion_laboral', $data_situacion_laboral);
                            //checkDbError($this->db);
                            //$id_situacion_laboral = $this->db->insert_id();
                           
                        }

                        if($solicitud_analisis->buro == "DataCredito"){
                            $this->migrar_telefonos_datacredito($id_cliente);
                        }elseif($solicitud_analisis->buro == "TransUnion"){
                            $this->migrar_telefonos_transunion($id_cliente);
                        }
                    }
                    
                    //Update Estado de la solicitud
                    $this->cambiarEstadoSolicitud(self::PAGADO, $idSolicitud);
                    $this->custom_log->write_log("info", "cambiarEstadoSolicitud: ".self::PAGADO,$this->file_log);

                }else{
                    $this->custom_log->write_log("info", "Rollback : No se pudo actualizar la solicitud con id_credito y id_cliente",$this->file_log);
                    $this->custom_log->write_log("info", "db_error: ".json_encode($this->db->error()),$this->file_log);
                    $pivotTransaccion = FALSE;
                }
            }else{
                $this->custom_log->write_log("info", "Rollback : No se inserto id_credito o id_cliente",$this->file_log);
                $this->custom_log->write_log("info", "db_error: ".json_encode($this->db->error()),$this->file_log);
                $pivotTransaccion = FALSE;
            }
        }else{
            $this->custom_log->write_log("info", "Rollback : No existe condición desembolso",$this->file_log);
            $this->custom_log->write_log("info", "db_error: ".json_encode($this->db->error()),$this->file_log);
            $pivotTransaccion = FALSE;
        }

        //determina si todos los querys de la transaccion se ejecutaron correctamente
        $dbTransStatus = $this->db->trans_status();
        $response = [];
        if ($dbTransStatus === FALSE || $pivotTransaccion === FALSE){
            //si hubo al menos un error, hace el rollback
            $this->db->trans_rollback();
            $response['respuesta'] = $dbTransStatus;
            $this->custom_log->write_log("info", "dbTransStatus: Rollback",$this->file_log);
        }else{
            //si está todo ok hace el commit de todas las querys
            $this->db->trans_commit();
            $response['respuesta'] = $dbTransStatus;
            $this->custom_log->write_log("info", "dbTransStatus: Commit",$this->file_log);
            if(ENVIRONMENT == 'development')
            {
                $email = "rafael.carvajal@solventa.com.ar";
                $nombres = ENVIRONMENT;

            }else{
                $email = $solicitud->email;
                $nombres = $solicitud->nombres;
            }
            
            $dataTrackGestion = [
                'id_solicitud'=>(int)$idSolicitud,
                'observaciones'=> 'Pago de prestamo ejecutado', 
                'id_cliente'=>(int)$solicitud->id_cliente, 
                'id_credito'=>(int)$solicitud->id_credito,
                'id_tipo_gestion' => 6
            ];
            
            //Remember pasarlo al controller  -> verificar el paso de dataTrackGestion al controller.
            $endPoint =  base_url('api/admin/track_gestion');
            $response['trackGestion'] = $this->trackGestion($endPoint, 'POST', $dataTrackGestion);
            $response['tipo_solicitud'] = "primaria";
        }

        return $response;
    }

    /**
    * Actualizar Cliente Existente
    */
    private function actualizarCliente($idCliente, $solicitud){

        $pivotTransaccion = TRUE; //Controla las situaciones en la que no hay error de bd pero falto alguna insersion necesaria.
        $idSolicitud = $solicitud->id;
        /**
        * Buscar la condicion de la solicitud condicion desembolso
        */
        $condicionDesembolso = $this->getCondicionDesembolso($idSolicitud);
        //Inicio de la transaccion
        $this->db->trans_begin(); 
        if(!empty($condicionDesembolso)){


            //Insertar registro en la maestro.credito
            $id_credito = $this->insertarCredito($condicionDesembolso, $idCliente);
            $this->custom_log->write_log("info", "id_credito: ".$id_credito,$this->file_log);
            if($id_credito > 0) {

                //actualizo en id del credito en la solicitud
                $solicitudCredito = $this->actualizarSolicitudCredito($idSolicitud, $id_credito);
                $this->custom_log->write_log("info", "solicitudCredito: ".$solicitudCredito,$this->file_log);
                
                //Set class propierties
                $this->setFondoGarantia($condicionDesembolso);
                //Creacion de la condicion del CREDITO
                $creditoCondicion = $this->insertarCreditoCondicion($condicionDesembolso, $id_credito);
                $this->custom_log->write_log("info", "creditoCondicion: ".$creditoCondicion,$this->file_log);
                if($solicitudCredito > 0 ){

                    /**
                    * Creacion detalle CREDITO
                    */
                    $monto_cuota=$condicionDesembolso->total_devolver/$condicionDesembolso->plazo;
                    $fecha_pago_inicial = $condicionDesembolso->fecha_pago_inicial;
                    $fondo_garantia = $this->fondoGarantia / $condicionDesembolso->plazo;
                    for ($i = 1; $i <= $condicionDesembolso->plazo; $i++) {

                        $data = array(
                            'id_credito' => $id_credito,
                            'numero_cuota' => $i,
                            'monto_cuota' => $monto_cuota,
                            'fecha_vencimiento' => $fecha_pago_inicial,
                            'monto_cobrar' => $monto_cuota,
                            'aval' => $fondo_garantia
                        );
                        $fecha_pago_inicial = date("Y-m-d",strtotime($fecha_pago_inicial."+ 1 month"));
                        $this->db->insert('maestro.credito_detalle', $data);
                        $this->custom_log->write_log("info", "data_credito_detalle: ".json_encode($data),$this->file_log);
                        $this->custom_log->write_log("info", "maestro.credito_detalle: ".json_encode($this->db->error()),$this->file_log);
                        //checkDbError($this->db);
                    }
                    /**
                     * buscar datos bancarios
                     */
                    $this->db->select('id_banco,id_tipo_cuenta,numero_cuenta');
                    $solicitudDatosBancarios = $this->db->get_where('solicitudes.solicitud_datos_bancarios', 'id_solicitud ='.$idSolicitud);
                    //checkDbError($this->db);

                    $datosBancarios = $solicitudDatosBancarios->row();
                    
                    $id_cliente=$idCliente;
                    $id_banco=$datosBancarios->id_banco;
                    $id_tipo_cuenta=$datosBancarios->id_tipo_cuenta;
                    $numero_cuenta=$datosBancarios->numero_cuenta;
                    $estado=1;
                    
                    $where = [
                        'id_cliente' => $id_cliente,
                        'id_banco' => $id_banco,
                        'numero_cuenta' => $numero_cuenta
                    ];
                    $this->db->select('id');
                    $this->db->from('maestro.agenda_bancaria');
                    $this->db->where($where);
                    $infoBancario = $this->db->get();
                    
                    if($infoBancario->num_rows() == 0){
                        /**
                        * crear datos bancarios del cliente
                        */
                        $data = array(
                            'id_cliente' => $id_cliente,
                            'id_banco' => $id_banco,
                            'id_tipo_cuenta' => $id_tipo_cuenta,
                            'numero_cuenta' => $numero_cuenta,
                            'estado' => $estado
                        );
                        $this->db->insert('maestro.agenda_bancaria', $data);
                        //checkDbError($this->db);
                    }
                    
                    // inicio de acumulados por dias

                    $this->db->select('id');
                    $cuentasBancarias = $this->db->get_where('maestro.cuentas_bancarias', 'id_banco ='.$id_banco);
                    //checkDbError($this->db);
                    
                    $cuentaBancaria = $cuentasBancarias->row();

                    if (!empty($cuentaBancaria) > 0) {
                        $idCuentabancaria = $cuentaBancaria->id;
                        $this->acumuladoCuentaBancariaDia($idCuentabancaria, $condicionDesembolso->capital_solicitado);
                    }else{
                        /**
                        * lo mismo de arriba pero con una cuenta predefinida
                        */
                        $idCuentabancaria = 1;
                        $this->acumuladoCuentaBancariaDia($idCuentabancaria, $condicionDesembolso->capital_solicitado);
                    }

                    $this->cambiarEstadoSolicitud(self::PAGADO, $idSolicitud);
                    $this->custom_log->write_log("info", "cambiarEstadoSolicitud: ".self::PAGADO,$this->file_log);
                }else{
                    $this->custom_log->write_log("info", "Rollback : No se pudo actualizar la solicitud con id_credito",$this->file_log);
                    $this->custom_log->write_log("info", "db_error: ".json_encode($this->db->error()),$this->file_log);
                    $pivotTransaccion = FALSE;
                }
            }else{
                $this->custom_log->write_log("info", "Rollback : No se inserto id_credito",$this->file_log);
                $this->custom_log->write_log("info", "db_error: ".json_encode($this->db->error()),$this->file_log);
                $pivotTransaccion = FALSE;
            }
        }else{
            $this->custom_log->write_log("info", "Rollback : No existe condición desembolso",$this->file_log);
            $this->custom_log->write_log("info", "db_error: ".json_encode($this->db->error()),$this->file_log);
            $pivotTransaccion = FALSE;
        }

        // fin acumulado por dia
        
        //determina si todos los querys de la transaccion se ejecutaron correctamente
        $dbTransStatus = $this->db->trans_status();
        $response = [];
        
        if ($dbTransStatus === FALSE || $pivotTransaccion === FALSE){
            //si hubo al menos un error, hace el rollback
            $this->db->trans_rollback();
            $response['respuesta'] = $dbTransStatus;
            $this->custom_log->write_log("info", "dbTransStatus: Rollback",$this->file_log);
        }else{
            //si está todo ok hace el commit de todas las querys
            $this->db->trans_commit();
            $response['respuesta'] = $dbTransStatus;

            if(ENVIRONMENT == 'development')
            {
                $email = "rafael.carvajal@solventa.com.ar";
                $nombres = ENVIRONMENT;

            }else{
                $email = $solicitud->email;
                $nombres = $solicitud->nombres;
            }

            $dataMail = json_encode(
            [
                'from'=>'noreply@solventa.com.ar',
                'to'=>$email,
                'from_name'=>'Solventa',
                'subject'=>'Confirmación Desembolso!', 
                'template'=>2,
                'message'=>'Confirmación Desembolso!',
                'name'=>$nombres
            ]);
            $endPoint =  URL_SEND_MAIL.'api/sendmail';
            //Remember pasarlo al controller -> verificar el paso de dataMail al controller.
            $response['sendmail'] = $this->sendMail($endPoint, 'POST', $dataMail);
            $response['tipo_solicitud'] = "retanqueo";

            $dataTrackGestion = [
                'id_solicitud'=>(int)$idSolicitud,
                'observaciones'=>'Pago de prestamo ejecutado', 
                'id_cliente'=>(int)$solicitud->id_cliente, 
                'id_credito'=>(int)$solicitud->id_credito,
                'id_tipo_gestion' => 6
            ];
            $endPoint =  base_url('api/admin/track_gestion');
            //Remember pasarlo al controller  -> verificar el paso de dataTrackGestion al controller.
            $response['trackGestion'] = $this->trackGestion($endPoint, 'POST', $dataTrackGestion);
            $this->custom_log->write_log("info", "dbTransStatus: Commit",$this->file_log);
        }
        return $response;
    }
   
    /*
    * INICIO DE METODOS PROCESO PARA DESEMBOLSO DE PRESTAMO
    * PROCESO DE CREACION DE:
    * Cliente, Credito, Credito condicion Desembolso, agendas_telefonicas( referencias, personal, buro), Agendas de mail.
    */
    
    private function actualizarSolicitudCliente($idCliente, $idSolicitud){
        /**
         * actualizo el id del cliente en la solicitud
         */
        $this->db->set('id_cliente', $idCliente);
        $this->db->where('id', $idSolicitud);
        $this->db->update('solicitudes.solicitud');
        $solicitudCliente = $this->db->affected_rows();
        //checkDbError($this->db);
        return $solicitudCliente;
    }

    /*
     * Actualizar la solicitud con el credito
     */
    private function actualizarSolicitudCredito($idSolicitud, $ultimoIdcredito){
        
        $this->db->where('id', $idSolicitud);
        $this->db->set('id_credito', $ultimoIdcredito);
        $this->db->update('solicitudes.solicitud');
        //checkDbError($this->db);

        $solicitudCredito = $this->db->affected_rows();
        return $solicitudCredito;
    }

    /*
     * Actualizar la solicitud con el id_credito and id_cliente
     * @param idSolicitud, $id_cliente, $id_credito
     * @return affected_rows()
     */
    private function actualizarSolicitud($idSolicitud, $id_cliente, $id_credito){

        $this->db->where('id', $idSolicitud);
        $this->db->update('solicitudes.solicitud',['id_cliente'=> $id_cliente , 'id_credito' => $id_credito]);
        //checkDbError($this->db);

        $actualizarSolicitud = $this->db->affected_rows();
        return $actualizarSolicitud;
    }
    /*
     * Obtener condicion de desembolso
     */
    private function getCondicionDesembolso($idSolicitud){
        $this->db->select('capital_solicitado,plazo,fecha_pago_inicial,total_devolver,idcondicion_simulador,tasa_interes,seguro,administracion,tecnologia,iva,interes_mora,total_devolver,fecha_pago_inicial,dias');
        $query3 = $this->db->get_where('solicitudes.solicitud_condicion_desembolso', 'id_solicitud ='.$idSolicitud);
        //checkDbError($this->db);
        
        $condicionDesembolso = $query3->row();
        
        return $condicionDesembolso;
    }

    /**
    * acumulado_cuenta_bancaria mes
    */
    private function acumuladoCuentaBancariaMes($idCuentabancaria, $capital_solicitado){
        
        $datarespuesta['idCuentabancaria'] =$idCuentabancaria;
        $ano=date('Y');
        $mes=date('m');
        $qdia=date('d');

        $this->db->select('unidad_acumulada_mes,pesos_acumulado_mes,unidad_acumulada_dia,pesos_acumulado_dia');
        $this->db->where('ano', $ano);
        $this->db->where('dia', $qdia);
        $this->db->where('id_cuentabancaria', $idCuentabancaria);
        $query7 = $this->db->get_where('maestro.acumulado_cuenta_bancaria', 'mes ='.$mes);
        //checkDbError($this->db);

        if ($query7->num_rows() > 0) {

            $row7 = $query7->row();
            $unidad_acumulada_mes= $row7->unidad_acumulada_mes;
            $pesos_acumulado_mes= $row7->pesos_acumulado_mes;

            $unidad_acumulada_mes=$unidad_acumulada_mes+1;
            $pesos_acumulado_mes=$pesos_acumulado_mes+$capital_solicitado;

            $unidad_acumulada_dia= $row7->unidad_acumulada_dia;
            $pesos_acumulado_dia= $row7->pesos_acumulado_dia;

            $unidad_acumulada_dia=$unidad_acumulada_dia+1;
            $pesos_acumulado_dia=$pesos_acumulado_dia+$capital_solicitado;

            /**
            * actualizamos los acumulados del mes
            */
            $this->db->where('mes', $mes);
            $this->db->where('ano', $ano);
            $this->db->where('id_cuentabancaria', $idCuentabancaria);
            $this->db->set('unidad_acumulada_mes', $unidad_acumulada_mes);
            $this->db->set('pesos_acumulado_mes', $pesos_acumulado_mes);
            $this->db->set('unidad_acumulada_dia', $unidad_acumulada_dia);
            $this->db->set('pesos_acumulado_dia', $pesos_acumulado_dia);
            $this->db->set('dia', $qdia);
            $VARI= $this->db->update('maestro.acumulado_cuenta_bancaria');
            //checkDbError($this->db);

            $datarespuesta['unidad_acumulada_mes'] =$unidad_acumulada_mes;
            $datarespuesta['pesos_acumulado_mes'] =$pesos_acumulado_mes;
            $datarespuesta['unidad_acumulada_dia'] =$unidad_acumulada_dia;
            $datarespuesta['pesos_acumulado_dia'] =$pesos_acumulado_dia;

        }else{
            /**
            * insertamos la primera vez los acumulados del mes
            */
            $unidad_acumulada_mes=1;
            $pesos_acumulado_mes=$capital_solicitado;

            $unidad_acumulada_dia=1;
            $pesos_acumulado_dia=$capital_solicitado;

            $data = array(
                'id_cuentabancaria' => $idCuentabancaria,
                'unidad_acumulada_mes' => $unidad_acumulada_mes,
                'pesos_acumulado_mes' => $pesos_acumulado_mes,
                'unidad_acumulada_dia' => $unidad_acumulada_dia,
                'pesos_acumulado_dia' => $pesos_acumulado_dia,
                'mes' => $mes,
                'ano' => $ano,
                'dia' => $qdia
            );

            $this->db->insert('maestro.acumulado_cuenta_bancaria', $data);
            //checkDbError($this->db);

            $datarespuesta['unidad_acumulada_mes'] =$unidad_acumulada_mes;
            $datarespuesta['pesos_acumulado_mes'] =$pesos_acumulado_mes;
            $datarespuesta['unidad_acumulada_dia'] =$unidad_acumulada_dia;
            $datarespuesta['pesos_acumulado_dia'] =$pesos_acumulado_dia;
        }

        return $datarespuesta;
    }
    
    /**
    * acumulado_cuenta_bancaria día
    */
    private function acumuladoCuentaBancariaDia($idCuentabancaria, $capital_solicitado){
        
        $datarespuesta['idCuentabancaria'] =$idCuentabancaria;
        $dia=date("Y-m-d");

        $this->db->select('unidad_acumulada_dia,pesos_acumulado_dia');
        $this->db->where('id_cuentabancaria', $idCuentabancaria);
        $this->db->where('dia', $dia);
        $query7 = $this->db->get_where('maestro.acumulado_cuenta_bancaria_dia');
        //checkDbError($this->db);

        if ($query7->num_rows() > 0) {

            $row7 = $query7->row();
            $unidad_acumulada_dia= $row7->unidad_acumulada_dia;
            $pesos_acumulado_dia= $row7->pesos_acumulado_dia;

            $unidad_acumulada_dia=$unidad_acumulada_dia+1;
            $pesos_acumulado_dia=$pesos_acumulado_dia+$capital_solicitado;

            /**
            * actualizamos los acumulados del mes
            */
            $this->db->where('dia', $dia);
            $this->db->where('id_cuentabancaria', $idCuentabancaria);
            $this->db->set('unidad_acumulada_dia', $unidad_acumulada_dia);
            $this->db->set('pesos_acumulado_dia', $pesos_acumulado_dia);
            $VARI= $this->db->update('maestro.acumulado_cuenta_bancaria_dia');
            //checkDbError($this->db);

        }else{
            /**
            * insertamos la primera vez los acumulados del mes
            */
            $unidad_acumulada_dia=1;
            $pesos_acumulado_dia=$capital_solicitado;
            $data = array(
                'id_cuentabancaria' => $idCuentabancaria,
                'unidad_acumulada_dia' => $unidad_acumulada_dia,
                'pesos_acumulado_dia' => $pesos_acumulado_dia,
                'dia' => $dia
            );

            $this->db->insert('maestro.acumulado_cuenta_bancaria_dia', $data);
            //checkDbError($this->db);
        }
        
    }
    
    /*
     * Creacion de credito
     */
    private function insertarCredito($condicionDesembolso, $idCliente){
        
        // Datos para credito
        $id_cliente=$idCliente;
        $canal='Web';
        $fecha_otorgamiento=date('Y-m-d H:i:s');
        $monto_prestado=$condicionDesembolso->capital_solicitado;
        $plazo=$condicionDesembolso->plazo;
        $fecha_primer_vencimiento=$condicionDesembolso->fecha_pago_inicial;
        $monto_devolver=$condicionDesembolso->total_devolver;
        $estado='VIGENTE';
        
        /**
        * Creacion del CREDITO
        */
        $data = array(
            'id_cliente' => $id_cliente,
            'canal' => $canal,
            'fecha_otorgamiento' => $fecha_otorgamiento,
            'monto_prestado' => $monto_prestado,
            'plazo' => $plazo,
            'fecha_primer_vencimiento' => $fecha_primer_vencimiento,
            'monto_devolver' => $monto_devolver,
            'estado' => $estado
        );
        
        $this->db->insert('maestro.creditos', $data);
        //checkDbError($this->db);
        
        return $this->db->insert_id();
    }
    
    /*
     * Creacion de condicion de credito
     */
    private function insertarCreditoCondicion($condicionDesembolso, $ultimoIdcredito){
        
        // Datos para credito condicion
        $idcondicion_simulador=$condicionDesembolso->idcondicion_simulador;
        $capital_solicitado=$condicionDesembolso->capital_solicitado;
        $tasa_interes=$condicionDesembolso->tasa_interes;
        $seguro=$condicionDesembolso->seguro;
        $administracion = $condicionDesembolso->administracion;
        $tecnologia=$condicionDesembolso->tecnologia;
        $iva=$condicionDesembolso->iva;
        $interes_mora=$condicionDesembolso->interes_mora;
        $total_devolver=$condicionDesembolso->total_devolver;
        $fecha_pago_inicial=$condicionDesembolso->fecha_pago_inicial;
        $dias=$condicionDesembolso->dias;
        $plazo = $condicionDesembolso->plazo;
        
        /**
        * Creacion de la condicion del CREDITO
        */
        $data = array(
            'id_credito' => $ultimoIdcredito,
            'idcondicion_simulador' => $idcondicion_simulador,
            'capital_solicitado' => $capital_solicitado,
            'tasa_interes' => $tasa_interes,
            'plazo' => $plazo,
            'seguro' => $seguro,
            'administracion' => $administracion,
            'tecnologia' => $tecnologia,
            'iva' => $iva,
            'interes_mora' => $interes_mora,
            'total_devolver' => $total_devolver,
            'fecha_pago_inicial' => $fecha_pago_inicial,
            'dias' => $dias,
            'aval' => $this->porcentFondoGarantia
        );

        $this->db->insert('maestro.credito_condicion', $data);
        //checkDbError($this->db);

        return $this->db->insert_id();
    }

    //ENVIO DE AVISO DE DESEMBOLSO POR MAIL
    private function sendMail($endPoint, $method = 'POST',  $params=[] ){
        //PENDIENTE REEMPLAZAR POR LA LIBRERIA REQUEST.

        $curl = curl_init();
        $options[CURLOPT_HTTPHEADER] = ['Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzE0MzEzNDQsImV4cCI6MTU3MTQzNDk0NCwiZGF0YSI6eyJpZCI6IjEiLCJhZG1pbiI6dHJ1ZSwidGltZSI6MTU3MTQzMTM0NCwidGltZVRvbGl2ZSI6bnVsbH19.yOaR-uR1qjjGS_Z6VbTyBKN_zs-Xxx5Y_Xt2_dMZEa0', "content-type: application/json"];
        $options[CURLOPT_POSTFIELDS] = $params;
        $options[CURLOPT_URL] = $endPoint;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

        if(ENVIRONMENT == 'development')
        {
            $options[CURLOPT_CERTINFO] = 1;
            $options[CURLOPT_SSL_VERIFYPEER] = 0;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        
        curl_setopt_array($curl,$options);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
        {
          $response['error'] = 'cURL Error #:' . $err;
        }

        return $response;
    }

    //Insert in trackGestion
    private function trackGestion($endPoint, $method = 'POST',  $params=[] ){
        //PENDIENTE REEMPLAZAR POR LA LIBRERIA REQUEST.
        $token = $this->session->userdata('token');
        $curl = curl_init();
        $options[CURLOPT_HTTPHEADER] = ['Authorization:'.$token];
        $options[CURLOPT_POSTFIELDS] = $params;
        $options[CURLOPT_URL] = $endPoint;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

        if(ENVIRONMENT == 'development')
        {
            $options[CURLOPT_CERTINFO] = 1;
            $options[CURLOPT_SSL_VERIFYPEER] = 0;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        
        curl_setopt_array($curl,$options);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
        {
          $response['error'] = 'cURL Error #:' . $err;
        }

        return $response;
    }

    //Migra los telefonos del buro a maestro.agenda_telefonica
    private function migrar_telefonos_transunion($id_cliente){
        $this->db->where(['id' => $id_cliente]);
        $this->db->select('*');
        $this->db->from('maestro.clientes');
        $query = $this->db->get();
        $clientes = $query->row();
        //busco la consulta en Transunion
        $this->db->where(['NumeroIdentificacion' => $clientes->documento]);
        $this->db->select('*');
        $this->db->from('api_buros.dataconsulta');
        $consulta = $this->db->get();
        if(!empty($consulta->result())){
            foreach ($consulta->result() as $key => $consulta) {
                # code...
                $id_consulta=$consulta->id;
                //BUSCO EN Transunion CELULARES
                $this->db->where(['IdConsulta' => $id_consulta]);
                $this->db->select('*');
                $this->db->from('api_buros.pecoriginacion_celulares');
                $consulta_celular = $this->db->get();
                if(!empty($consulta_celular->result())){
                    foreach ($consulta_celular->result() as $key => $consulta_celular) {
                        //var_dump($consulta);
                        $this->db->where(['codigo' => $consulta->CodigoDepartamento]);
                        $this->db->select('id_departamento, nombre_departamento');
                        $this->db->from('parametria.geo_departamento');
                        $this->db->limit(1);
                        $query = $this->db->get();
                        $departamento = $query->row();

                        $this->db->where(['codigo' => $consulta->CodigoMunicipio, 'codigo_departamento' => $consulta->CodigoDepartamento]);
                        $this->db->select('id_municipio, nombre_municipio');
                        $this->db->from('parametria.geo_municipio');
                        $this->db->limit(1);
                        $query = $this->db->get();
                        $municipio = $query->row();

                        $this->db->where(['departamento_tel' => $departamento->nombre_departamento]);
                        $this->db->select('areaCode, ciudad_tel, departamento_tel');
                        $this->db->from('parametria.tel_codigoarea');
                        $this->db->limit(1);
                        $query = $this->db->get();
                        $codigo_area = $query->row();

                        $areaCode = "";
                        $ciudad_tel = "";
                        $departamento_tel = "";

                        if(!empty($codigo_area)){
                            $areaCode = (string)$codigo_area->areaCode;
                            $ciudad_tel = $codigo_area->ciudad_tel;
                            $departamento_tel = $codigo_area->departamento_tel;
                        }


                        $nombres_apellidos='';
                        $telefono=$consulta_celular->Celular;
                        $id_parentesco="";
                        /**
                        * crear agenda telefonica por cada referencia
                        */
                        $this->db->select('id');
                        $existe_num_ref = $this->db->get_where('maestro.agenda_telefonica',['id_cliente' => $clientes->id, 'numero' => $telefono]);
                        if(empty($existe_num_ref->result())){
                            $dataAgendaTelefonica = array(
                                'id_cliente' => $clientes->id,
                                'indicativo_ciudad' => $areaCode,
                                'numero' => $telefono,
                                'tipo' => 1,
                                'fuente' => 7,
                                'contacto'=>$nombres_apellidos,
                                'estado' => 1,
                                'ciudad' => $ciudad_tel,
                                'departamento' => $departamento_tel
                            );
                            $dataAgendaTelefonica['id_parentesco'] = ($id_parentesco !== null ) ? $id_parentesco : "";
                            $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);
                            //checkDbError($this->db);
                        }
                    }
                }
            }
        }
    }

    //Migra los telefonos del buro datacredito a maestro.agenda_telefonica
    private function migrar_telefonos_datacredito($id_cliente){
        $this->db->where(['id' => $id_cliente]);
        $this->db->select('*');
        $this->db->from('maestro.clientes');
        $query = $this->db->get();
        $clientes = $query->row();
        //busco la consulta en DATACREDITO
        $this->db->where(['identificacion' => (string)$clientes->documento]);
        $this->db->select('*');
        $this->db->from('api_buros.datacredito2_reconocer_naturalnacional');
        $consulta = $this->db->get();
        if(!empty($consulta->result())){

            foreach ($consulta->result() as $key => $consulta) {
                # code...
                $id_consulta=$consulta->id;

                //BUSCO EN DATACREDITO CELULARES
                $this->db->where(['IdConsulta' => $id_consulta]);
                $this->db->select('*');
                $this->db->from('api_buros.datacredito2_reconocer_celular');
                $consulta_celular = $this->db->get();

                if(!empty($consulta_celular->result())){

                    foreach ($consulta_celular->result() as $key => $consulta_celular) {
                        $this->db->where(['ciudad_tel' => $consulta->ciudad]);
                        $this->db->select('areaCode, ciudad_tel, departamento_tel');
                        $this->db->from('parametria.tel_codigoarea');
                        $this->db->limit(1);
                        $query = $this->db->get();
                        $codigo_area = $query->row();

                        $areaCode = "";
                        $ciudad_tel = "";
                        $departamento_tel = "";

                        if(!empty($codigo_area)){
                            $areaCode = (string)$codigo_area->areaCode;
                            $ciudad_tel = $codigo_area->ciudad_tel;
                            $departamento_tel = $codigo_area->departamento_tel;
                        }

                        $nombres_apellidos='';
                        $telefono=$consulta_celular->celular;
                        $id_parentesco="";

                        /**
                        * crear agenda telefonica por cada referencia
                        */
                        $this->db->select('id');
                        $existe_num_ref = $this->db->get_where('maestro.agenda_telefonica',['id_cliente' => $clientes->id, 'numero' => $telefono]);
                        if(empty($existe_num_ref->result())){
                            $dataAgendaTelefonica = array(
                                'id_cliente' => $clientes->id,
                                'indicativo_ciudad' => $areaCode,
                                'numero' => $telefono,
                                'tipo' => 1,
                                'fuente' => 4,
                                'contacto'=>$nombres_apellidos,
                                'estado' => 1,
                                'ciudad' => $ciudad_tel,
                                'departamento' => $departamento_tel
                            );
                            $dataAgendaTelefonica['id_parentesco'] = ($id_parentesco !== null ) ? $id_parentesco : "";
                            $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);
                            //$id_agenda_telefonica = $this->db->insert_id();
                            //checkDbError($this->db);
                        }

                    }

                }

                //BUSCO EN DATACREDITO Telefonos
                $this->db->where(['IdConsulta' => $id_consulta]);
                $this->db->select('*');
                $this->db->from('api_buros.datacredito2_reconocer_telefono');
                $consulta_telefono = $this->db->get();

                if(!empty($consulta_telefono->result())){
                    foreach ($consulta_telefono->result() as $key => $consulta_telefono) {
                        if($consulta_telefono->nombreCiudad === "BOGOTA, D.C."){
                            $consulta_telefono->nombreCiudad = substr($consulta_telefono->nombreCiudad, 0,6);
                        }
                        $this->db->where(['ciudad_tel' => $consulta_telefono->nombreCiudad]);
                        $this->db->select('areaCode, ciudad_tel, departamento_tel');
                        $this->db->from('parametria.tel_codigoarea');
                        $this->db->limit(1);
                        $query = $this->db->get();
                        $codigo_area = $query->row();

                        $areaCode = "";
                        $ciudad_tel = "";
                        $departamento_tel = "";

                        if(!empty($codigo_area)){
                            $areaCode = (string)$codigo_area->areaCode;
                            $ciudad_tel = $codigo_area->ciudad_tel;
                            $departamento_tel = $codigo_area->departamento_tel;
                        }

                        $nombres_apellidos='';
                        $telefono=$consulta_telefono->telefono;

                        $tipo = 2;
                        $fuente = 5;
                        if($consulta_telefono->tipo != 'R'){
                            $fuente = 6;
                        }

                        $this->db->select('id');
                        $existe_num_ref = $this->db->get_where('maestro.agenda_telefonica',['id_cliente' => $clientes->id, 'numero' => $telefono]);
                        if(empty($existe_num_ref->result())){
                            $dataAgendaTelefonica = array(
                                'id_cliente' => $clientes->id,
                                'indicativo_ciudad' => $areaCode,
                                'numero' => $telefono,
                                'tipo' => $tipo,
                                'fuente' => $fuente,
                                'contacto'=>$nombres_apellidos,
                                'estado' => 1,
                                'ciudad' => $ciudad_tel,
                                'departamento' => $departamento_tel
                            );
                            $dataAgendaTelefonica['id_parentesco'] = ($id_parentesco !== null ) ? $id_parentesco : "";
                            $this->db->insert('maestro.agenda_telefonica', $dataAgendaTelefonica);
                            //$id_agenda_telefonica = $this->db->insert_id();
                            //checkDbError($this->db);
                        }

                    }

                }

            }
        }
    }
    /****
    *
    *   FIN DE METODOS PRIVADOS DEL PROCESO DE DESEMBOLSO DE PRESTAMO.
    *
    */
    /**
     * Get resultset de new_chats
     * @param $documento
     * @return object[] 
     */
    public function checkStatusChat($documento){
        $this->db->where("documento = '$documento'");
        $this->db->select("*");
        $this->db->from('chat.new_chats');
        $this->db->limit(1);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        return $query->row();
    }
	
	
	/**
	 * Busca el pago de payvalida
	 *
	 * @param $pv_po_id
	 *
	 * @return array|mixed
	 */
	public function getPayvalidaPaymentInfo($pv_po_id)
	{
		$result = $this->db->select('n.*, m.expiracion, n.created_at fecha_pago')
			->from('maestro.pay_valida_notifications n')
			->join('maestro.payvalida_movimientos m', 'm.order_id = n.po_id', 'inner')
			->where('n.pv_po_id', $pv_po_id)
			->get()->result_array();
		
		if (!empty($result)) {
			return $result[0];
		} else {
			return [];
		}
	}
	
	/**
	 * Comprueba si ya existe un pago realizado por payvalida con esa referencia externa 
	 * 
	 * @param $pv_po_id
	 *
	 * @return bool
	 */
	public function checkIfExistPayvalidaPayment($pv_po_id)
	{
		$result = $this->db->select('*')
			->from('maestro.pago_credito')
			->where('referencia_externa', $pv_po_id)
			->get()->result_array();
		
		return (count($result) > 0);
	}

    public function getCondicionesSimulador($idcondicion_simulador){
        $this->db->select('fondo_garantia');
        $this->db->from('parametria.condiciones_simulador');
        $this->db->where('id', $idcondicion_simulador);
        $query = $this->db->get();
        return $query->row();
    }

    private function setFondoGarantia ($condicionDesembolso){

        $idcondicion_simulador= $condicionDesembolso->idcondicion_simulador;
        $capital_solicitado= $condicionDesembolso->capital_solicitado;

        $condicionesSimulador = $this->getCondicionesSimulador($idcondicion_simulador);
        
        if(isset($condicionesSimulador) && $condicionesSimulador->fondo_garantia > 0){
            $this->porcentFondoGarantia = (float)$condicionesSimulador->fondo_garantia;
            
            $this->fondoGarantia = (((float)$condicionesSimulador->fondo_garantia < 100)? (( $capital_solicitado * (float)$condicionesSimulador->fondo_garantia ) / 100) : (float)$condicionesSimulador->fondo_garantia);
        }
    }

}

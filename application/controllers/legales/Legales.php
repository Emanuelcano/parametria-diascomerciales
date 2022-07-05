<?php

class Legales extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent::__construct();
       // if ($this->session->userdata('is_logged_in')) {
            // MODELS
            //$this->load->model("InfoBipModel");
            $this->load->model('operadores/Operadores_model');
            $this->load->model('legales/Legales_model');
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);

            $this->load->model('Solicitud_m');
            // LIBRARIES
            $this->load->library('form_validation');
            // HELPERS
            $this->load->helper('date');
       /* } else {
            redirect(base_url('login'));
        } */ 
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

//Begin Alexis Rodriguez 2021
    public function index() {
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
            $title['title'] = 'Legales';
            $this->load->view('layouts/adminLTE', $title);
            //$cantidad_beneficiarios = $this->Operaciones_model->get_cantidad_beneficiarios();
            //$cantidad_gastos = $this->Operaciones_model->get_cantidad_gastos();
            //$data = array('cant_beneficiarios' => $cantidad_beneficiarios, 'cant_camp_manuales' => $cantidad_camp_manuales, 'cant_gastos' => $cantidad_gastos, 'tipos_criterios'=>$criterios, 'total_devoluciones' => $cantidad_devoluciones[0]->cantidad);
            $data['heading'] = 'Legales';
            $this->load->view('legales/legales_view', ['data' => $data]);
            
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    public function vistaBajaDatos()
    {
        return $this->load->view('legales/vistaBajaDatos');
    }

    public function Vistafallecido()
    {
        return $this->load->view('legales/vistaFallecido');
    }

    public function get_datos_baja()
    {
        $documento = $_POST['datoBuscar'];
        $estado_credito = $this->Legales_model->credito_vigente($documento);
        $busqueda_documento = $this->Legales_model->solicitud_documento($documento);
        $busqueda_bajaDatos = $this->Legales_model->buscar_bajaDatos($documento);
        if (!empty($busqueda_bajaDatos)) {

            if ($estado_credito) {
                $busqueda_documento[0]['credito'] = 'activo';
            }
            $busqueda_documento[0]['tipo'] = 'baja';
            $busqueda_documento[0]['razon'] = $busqueda_bajaDatos;
            echo json_encode($busqueda_documento);                
        }else {
            if ($estado_credito) {
                echo json_encode($estado_credito); 
            }elseif ($busqueda_documento != false) {
                echo json_encode($busqueda_documento);
            }else {
                $noExiste = 'El documento no existe';
                echo json_encode($noExiste);
            }
                
            
        }
        
    }             
    
    public function get_fallecidos()
    {
        $documento = $_POST['datoBuscar'];
        
        $busqueda_fallecido = $this->Legales_model->buscar_bajaDatos($documento);
        $estado_fallecido = $this->Legales_model->credito_vigente($documento);
        $busqueda_documento = $this->Legales_model->solicitud_documento($documento);
        
        if (!empty($busqueda_fallecido)){
            $busqueda_documento[0]['razon'] = $busqueda_fallecido;
            echo json_encode($busqueda_documento);                
        }else{
            if($estado_fallecido){
                echo json_encode($estado_fallecido);
            }else if($busqueda_documento != false){
                $busqueda_documento[0]['tipo'] = 'si';
                echo json_encode($busqueda_documento);
            }else {
                $noExiste = 'El documento no existe';
                echo json_encode($noExiste);
            }
        }
    }

    public function VistaBloquear()
    {
        return $this->load->view('legales/vistaBloquear');
    }

    public function get_bloqueo(){
        $documento = $_POST['datoBuscar'];
        $busqueda_bloq =$this->Legales_model->buscar_bloqueo($documento);
        $estado_credito = $this->Legales_model->credito_vigente($documento);
        $busqueda_documento = $this->Legales_model->solicitud_documento($documento);
        if (!empty($busqueda_bloq)) {
            if ($estado_credito) {
                $busqueda_documento[0]['credito'] = 'activo';
            }
            $busqueda_documento[0]['tipo'] = 'Cliente ya existe como bloqueado';
            $busqueda_documento[0]['razon'] = $busqueda_bloq;
            echo json_encode($busqueda_documento);
        }else{
            if ($estado_credito) {
                echo json_encode($estado_credito);                
                }else if($busqueda_documento != false) {
                    echo json_encode($busqueda_documento);
                }else {
                    $noExiste = 'El documento no existe';
                    echo json_encode($noExiste);
                }
        
            }
    }

    public function descargar_datos()
    {   
        $tipo = $_POST['tipo'];
        if ($tipo == 'baja') {
            $titulo = 'Baja Datos';
            $Nombre_doc="Reporte_Baja_Datos.xls"; 
        }else {
            $titulo = 'Bloqueados';
            $Nombre_doc = 'Reporte_Bloqueados.xls';
        }
        $datos = $this->Legales_model->obtener_datos_descarga($tipo);
        
        $this->load->library('PHPExcel');
        $hoja = $this->phpexcel;
        $hoja->getActiveSheet(1)->setTitle($titulo);
// Se agregan los encabezados del reporte
		$hoja->setActiveSheetIndex(0);
		$hoja->getActiveSheet(0)->setCellValue('A1', 'DOCUMENTO');
        $hoja->getActiveSheet(0)->setCellValue('B1', 'NOMBRES');
        $hoja->getActiveSheet(0)->setCellValue('C1', 'APELLIDOS');
        $hoja->getActiveSheet(0)->setCellValue('D1', 'TELEFONO');
        $hoja->getActiveSheet(0)->setCellValue('E1', 'FECHA Y HORA BAJA');
        $hoja->getActiveSheet(0)->setCellValue('F1', 'RAZON');
        $hoja->getActiveSheet(0)->setCellValue('G1', 'ID_OPERADOR');
//Se agregan los datos de la BD
		$c=2;
		foreach ($datos as $fila) {					
			$hoja->setActiveSheetIndex(0)->setCellValue('A'.$c, $fila['documento']);
			$hoja->setActiveSheetIndex(0)->setCellValue('B'.$c, $fila['nombres']);
			$hoja->setActiveSheetIndex(0)->setCellValue('C'.$c, $fila['apellidos']);
			$hoja->setActiveSheetIndex(0)->setCellValue('D'.$c, $fila['telefono']);
			$hoja->setActiveSheetIndex(0)->setCellValue('E'.$c, $fila['fecha_hora_aplicacion']);
			$hoja->setActiveSheetIndex(0)->setCellValue('F'.$c, $fila['razon']);
			$hoja->setActiveSheetIndex(0)->setCellValue('G'.$c, $fila['idoperador']);
			$c++;
		}		
		
        if (file_exists(URL_CSV_FOLDER.$Nombre_doc)) {
            unlink(URL_CSV_FOLDER.$Nombre_doc);
        }
        $objGravar = PHPExcel_IOFactory::createWriter($hoja, 'Excel5');// Cambia a CSV para descargar formato csv
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename="'.$Nombre_doc.'"');
        header('Cache-Control: max-age=0');
        $objGravar->save(URL_CSV_FOLDER.$Nombre_doc);
        echo $Nombre_doc;
	
    }

    public function VistaUsura()
    {
        return $this->load->view('legales/vistaUsura');
    }

    public function mostrar_usura()
    {
        $datos_usura = $this->Legales_model->listar_usura();
        foreach ($datos_usura as $key => $value) {
            if ($value->nombre_apellido == '' || is_null($value->nombre_apellido)) {
                $value->nombre_apellido = ' ';
            }
            if ($value->operador_update == '' || is_null($value->operador_update)) {
                $value->operador_update = ' ';
            }
        }
        echo json_encode(['data'=>$datos_usura]);
    }
    
    public function registrar_usura()
    {  
        $registro = $this->Legales_model->registro_usura($this->input->post());
        echo json_encode($registro);
    }

    public function actualizar_usura()
    {
        $actualizar = $this->Legales_model->update_usura($this->input->post());
        echo json_encode($actualizar);
    }

    //Guarda uno o mas archivos adjuntos en la tabla - Recibe el documento del cliente, de que modulo se ha hecho la operacion y los datos del formdata
    public function adjuntar_archivos()
    {   
        $documento = $_POST['documento'];
        $emitido = $_POST['emitido'];
        $datos = $_FILES['file'];
        $archivos =[];
        $fecha_actual = date('d-m-Y');
        $archivoCount = count($datos['name']);
        $archivo_key =array_keys($datos);
        
        for ($i=0; $i < $archivoCount; $i++) { 
            $archivo_exist = $this->Legales_model->exist_archivo($documento, $_FILES['file']['name'][$i]);
            $nombre_archivo = $_FILES['file']['name'][$i];
            if ($archivo_exist) {
                echo "No se han realizado los registros.<br>Ya existe este archivo: <b>$nombre_archivo</b> asociado a este cliente";die;
            }
            $fileNameCmps = explode(".", $nombre_archivo);
            $fileExtension = strtolower(end($fileNameCmps));
            if ($fileExtension != 'jpg' && $fileExtension != 'png' && $fileExtension != 'pdf') {
                echo "Tipo de archivo incorrecto<br>Tipos de archivos admitidos: PDF, JPG, PNG";die;
            }
        }
        $end_folder = FCPATH.'public/comprobantes_legales';
        if (!file_exists($end_folder)) {
            $this->_end_folder($end_folder);       
        }
        $end_folder2 = $end_folder.'/'.$fecha_actual;
        if (!file_exists($end_folder2)) {
            $this->_end_folder($end_folder2);       
        }
        
        $config = [
            "upload_path"   => "./public/comprobantes_legales/".$fecha_actual,
            "allowed_types" => "pdf|jpg|png",
            "max_size"      => "32000",
            
        ];

        $this->load->library('upload', $config);
        $files = $_FILES;
        $cpt = count($_FILES['file']['name']);
        $a=0;
        for($i=0; $i<$cpt; $i++)
        {           
            foreach ($archivo_key as $key) {
                $archivo[$i][$key] = $datos[$key][$i];
            }
            $_FILES['file']['name']= $files['file']['name'][$i];
            $_FILES['file']['type']= $files['file']['type'][$i];
            $_FILES['file']['tmp_name']= $files['file']['tmp_name'][$i];
            $_FILES['file']['error']= $files['file']['error'][$i];
            $_FILES['file']['size']= $files['file']['size'][$i];    

            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                $respuesta = $this->upload->display_errors();
                echo $respuesta;die;
            } else {
                $respuesta[] = $this->upload->data();
                $directorio = 'public/comprobantes_legales/'.$fecha_actual.'/'.$files['file']['name'][$i];
                    $guardar_archivo = $this->Legales_model->insertar_archivo($documento, $archivo[$i], $directorio, $emitido);
                    if ($guardar_archivo) {
                        $a++;
                        if ($a<2) {
                            echo true;
                        }
                    }else {
                        $a--;
                        echo 'Ha ocurrido un error';
                    }
            }
        }
        
    }
    //Obtener archivos adjuntos - recibe documento del cliente
    public function mostrar_adjunto()
    {
        $datos_archivo = $this->Legales_model->get_comprobantes($this->input->post());
        if (empty($datos_archivo) || $datos_archivo == "") {
            echo json_encode('No existen archivos adjuntos');
        }else {
            echo json_encode($datos_archivo);
        }
    }
    private function _end_folder($end_folder)
    {
        // Valida que la carpeta de destino exista, si no existe la crea.
        if(!file_exists($end_folder) && !empty($end_folder))
        {
            // Si no puede crear el directorio.
            if(!mkdir($end_folder, 0777, true))
            {
                return FALSE;
            }
        }
        return $end_folder;
    }
    
}

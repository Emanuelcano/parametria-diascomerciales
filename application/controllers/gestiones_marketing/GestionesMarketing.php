<?php
require_once APPPATH . 'controllers/comunicaciones/Twilio.php';
require_once APPPATH . 'controllers/api/ApiTemplates.php';

class GestionesMarketing extends CI_Controller {

    public function __construct() {
        parent::__construct();;

        if ($this->session->userdata('is_logged_in')) {
            // Models
            $this->load->model('GestionMarketing_model', '', TRUE);
        } else {
            redirect(base_url('login'));
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    //Ingreso a la vista de gestion templates
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

            $title['title'] = 'Templates';
            $this->load->view('layouts/adminLTE', $title);


            $data = array();
            $this->load->view('gestiones_marketing/gestiones_markenting_main', $data);
            return $this;
        }
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    //Obtengo data para grafico o reporte
    public function getSolicitudes() {
        $request = $this->input->post();
        // var_dump($request);die;

        $tipo = $request['select_tipo'];

        $providers = $request['providers'];
        $providers_aux = "";
        if($providers) {
            $providers =  explode(",", $request['providers']);

            foreach ($providers  as $i => $p) {

                if($i  == count($providers)-1) {
                    $providers_aux .= "'" . $p . "'";
                } else {
                    $providers_aux .= "'" . $p . "', ";
                }
            }

            $providers = $providers_aux;
        } else {
            $providers = null;
        }
  
        $date_desde = strtotime(str_replace('/','-',$request['fecha_desde']));
        $fecha_desde = date('Y-m-d', $date_desde);

        $date_hasta = strtotime(str_replace('/','-',$request['fecha_hasta']));
        $fecha_hasta =  date('Y-m-d', $date_hasta);

        $data = [];
        $data['providers'] = $this->getData($tipo, $providers, $fecha_desde, $fecha_hasta, $request["type"]);

        // var_dump($TodosProveedores);die;
        // $proveedores = [
            //     'shuttle', 'leadgen', 'crezu', 'doaff'
            // ];
            if ($request["type"] == 0) {
                $getSolicitudes = 1;
                $TodosProveedores = $this->getProviders($getSolicitudes);
                foreach ($TodosProveedores['providers'] as $key => $value) {
                    $exist = in_array($value['nombre'], array_column($data['providers'], 'utm_source'));
                    
                    if (!$exist) {
                        $insert = ['cantidad' => 0, 'utm_source'=>$value['nombre']];
                        array_push($data['providers'], $insert);
                    }
                }
            }
        // var_dump($data);die;
        if(!$providers){
            print json_encode($data);
        } else {
            if(empty($data)){
                $data['status'] = "no-data";
                echo json_encode($data);
            } else {
                
                // var_dump($data);die;
                $listado = $this->load->view('gestiones_marketing/gestiones_marketing_list', $data);
                echo json_encode($listado);
                return $this;
            }
           
        }
    }

    //Obtengo proveedores
    public function getProviders($getSolicitudes = null) {
        $data = [];
        $data['providers'] = $this->GestionMarketing_model->getProviders();
        if (is_null($getSolicitudes)) {
            print json_encode($data);
        }else{
            return $data;
        }
    }

    //obtengo data del modelo y la retorno a su metodo correspondiente
    public function getData($tipo, $providers, $fecha_desde, $fecha_hasta, $type, $exportacion = null) {
        if($exportacion){
            $providers_aux = "";
            if($providers) {
                $providers =  explode(",", $providers);

                foreach ($providers  as $i => $p) {

                    if($i  == count($providers)-1) {
                        $providers_aux .= "'" . $p . "'";
                    } else {
                        $providers_aux .= "'" . $p . "', ";
                    }
                }

                $providers = $providers_aux;
            }

            $date_desde = strtotime($fecha_desde);
            $fecha_desde = date('Y-m-d', $date_desde);

            $date_hasta = strtotime($fecha_hasta);
            $fecha_hasta =  date('Y-m-d', $date_hasta);
        }

        switch ($tipo) {
            case 'DESEMBOLSO':
                    return $this->GestionMarketing_model->desembolsos($providers, $fecha_desde, $fecha_hasta, $type);
                break;
            case 'APROBADO_BURO':
                return  $this->GestionMarketing_model->aprobadosBuro($providers, $fecha_desde, $fecha_hasta, $type);
                break;
            default:
                return $this->GestionMarketing_model->totalLeads($providers, $fecha_desde, $fecha_hasta, $type);
                break;
        }
    }
    //Armado de archivo y descarga
    public function downloadData()
    {
        $request = $this->input->post();
        $datos = $this->getData($request['select_tipo'], $request['providers'], $request['fecha_desde'], $request['fecha_hasta'], $request['type'], true);
        // var_dump($datos);die;
        ob_start();
        $this->load->library('PHPExcel');
        $hoja = $this->phpexcel;
        // Se agregan los encabezados del reporte
		$hoja->setActiveSheetIndex(0);
		$hoja->getActiveSheet(0)->setCellValue('A1', 'Solicitud');
        $hoja->getActiveSheet(0)->setCellValue('B1', 'Fecha alta');
        $hoja->getActiveSheet(0)->setCellValue('C1', 'Documento');
        $hoja->getActiveSheet(0)->setCellValue('D1', 'Nombre y apellido');
        $hoja->getActiveSheet(0)->setCellValue('E1', 'PASO');
        $hoja->getActiveSheet(0)->setCellValue('F1', 'Situacion laboral');
        $hoja->getActiveSheet(0)->setCellValue('G1', 'Proveedor');
        $hoja->getActiveSheet(0)->setCellValue('H1', 'Estado');
        $hoja->getActiveSheet(0)->setCellValue('I1', 'Email');
        $hoja->getActiveSheet(0)->setCellValue('J1', 'Telefono');
        $hoja->getActiveSheet(0)->setCellValue('K1', 'Track ID');
        if (isset($datos[0]['fecha_desembolso'])) {
            $hoja->getActiveSheet(0)->setCellValue('L1', 'Fecha desembolso');
        }
        //Se agregan los datos de la BD
		$c=2;

		foreach ($datos as $fila) {
			$hoja->setActiveSheetIndex(0)->setCellValue('A'.$c, $fila['id']);
			$hoja->setActiveSheetIndex(0)->setCellValue('B'.$c, $fila['fecha_alta']);
			$hoja->setActiveSheetIndex(0)->setCellValue('C'.$c, $fila['documento']);
			$hoja->setActiveSheetIndex(0)->setCellValue('D'.$c, $fila['nombre_completo']);
			$hoja->setActiveSheetIndex(0)->setCellValue('E'.$c, $fila['paso']);
			$hoja->setActiveSheetIndex(0)->setCellValue('F'.$c, $fila['situacion_laboral']);
			$hoja->setActiveSheetIndex(0)->setCellValue('G'.$c, $fila['utm_source']);
			$hoja->setActiveSheetIndex(0)->setCellValue('H'.$c, $fila['estado']);
            $hoja->setActiveSheetIndex(0)->setCellValue('I'.$c, $fila['email']);
            $hoja->setActiveSheetIndex(0)->setCellValue('J'.$c, $fila['telefono']);
            $hoja->setActiveSheetIndex(0)->setCellValue('K'.$c, $fila['tracking_id']);
            if (isset($datos[0]['fecha_desembolso'])) {
                $hoja->setActiveSheetIndex(0)->setCellValue('L'.$c, $fila['fecha_desembolso']);
            }
			$c++;
		}
        $hoja->getActiveSheet(1)->setTitle($request['select_tipo']);
        $nombre_archivo =  $request['select_tipo'] . '.xls';
        if (file_exists(URL_CSV_FOLDER.$nombre_archivo)) {
            unlink(URL_CSV_FOLDER.$nombre_archivo);
        }

        $objGravar = PHPExcel_IOFactory::createWriter($hoja, 'Excel5');// Cambia a CSV para descargar formato csv
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
        header('Cache-Control: max-age=0');
        $objGravar->save(URL_CSV_FOLDER.$nombre_archivo);
    	echo $nombre_archivo;
    }

    public function tablero_leads_view()
    {
        $this->load->view('gestiones_marketing/GestionesMarketing');
    }
}

<?php

class AdminSistemas extends CI_Controller
{
    protected $CI;

    public function __construct() {
        parent::__construct();
        $this->load->model("admin_sistemas/AdminSistema_model", "AdminSistema_m");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

//Alexis Rodriguez 2022
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
            $title['title'] = 'Admin. Sistemas';
            $this->load->view('layouts/adminLTE', $title);
            $data['heading'] = 'Admin. Sistemas';
            $this->load->view('AdminSistemas/AdminSistemas_view', ['data' => $data]);
            
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    public function tableVariables()
    {
        $this->load->view("AdminSistemas/gestion_variables_view");
    }
    
    public function obtenerBases($tipo = null)
    {
        $bases = $this->AdminSistema_m->getBases();
        if (!is_null($tipo)) {
            return $bases;
        }else{
            echo json_encode($bases);
        }
    }

    public function obtenerVariables()
    {
        $variables = $this->AdminSistema_m->getVariables();
        foreach ($variables as $key => $value) {
            $variables[$key]["fecha_create"] = date("d-m-Y H:i:s", strtotime($value["fecha_create"]));
            
        }
        echo json_encode(["data"=>$variables]);
    }

    public function obtenerTablas($data = null, $tipo = null)
    {
        if (is_null($data) && !empty($this->input->post())) {
            $data["base"] = $this->input->post("base");
        }

        // var_dump($data);die;
        $tablas = $this->AdminSistema_m->getTables($data);
        if (is_null($tipo)) {
            echo json_encode($tablas);
        }else{
            return $tablas;
        }
    }

    public function obtenerCampos($data = null, $tipo = null)
    {
        if(is_null($data)){
            $data = $this->input->post();
        }
        $columnas = $this->AdminSistema_m->getCampos($data);
        if (is_null($tipo)) {
            echo json_encode($columnas);
        }else{
            return $columnas;
        }
    }

    public function accionesGuardarProbar()
    {
        $data = $this->input->post("data");
        $accion = $data[0];
        $apodo_variable = $data[1];
        $base_variable = $data[2];
        $tabla_variable = $data[3];
        $campo_variable = $data[4];
        $campo_comple = "$base_variable.$tabla_variable.$campo_variable";
        $estado_variable = $data[5];
        $tipo_variable = $data[6];
        $formato_variable = $data[7][0];
        $slc_filtro = $data[8];
        // var_dump($data);die;
        if ($slc_filtro == "0") {
            $condiciones = [];
            $k = 0;
            // var_dump($data);die;
            foreach ($data[9] as $key => $value) {
                if (!empty($value)) {
                    switch ($data[10][$key]) {
                        case '1':
                            $operador = "= '".preg_replace('([^A-Za-z0-9])', '', $data[11][$key])."'";
                            break;
                        case '2':
                            $operador = "> '".preg_replace('([^A-Za-z0-9])', '', $data[11][$key])."'";
                            break;
                        case '3':
                            $operador = ">= '".preg_replace('([^A-Za-z0-9])', '', $data[11][$key])."'";
                            break;
                        case '4':
                            $operador = "< '".preg_replace('([^A-Za-z0-9])', '', $data[11][$key])."'";
                            break;
                        case '5':
                            $operador = "<= '".preg_replace('([^A-Za-z0-9])', '', $data[11][$key])."'";
                            break;
                        case '6':
                            $operador = "!= '".preg_replace('([^A-Za-z0-9])', '', $data[11][$key])."'";
                            break;                    
                        default:
                            $operador = "BETWEEN '".$data[11][$key]."' AND '".$data[12][$key]."'";
                            // $type = $this->obtenerTipoDato($base_variable, $tabla_variable, $value);
                            break;
                    }
                    $condiciones[$k] = "$base_variable.$tabla_variable.$value $operador";
                    $k++;
                }
            }
        }else{
            $condiciones = "maestro.clientes.documento = $data[9]";
        }
        // var_dump($condiciones);die;
        $variable = $this->AdminSistema_m->getValor($apodo_variable, $base_variable, $tabla_variable, $campo_comple, $condiciones);
        if(count($variable) > 1){
            $rs_data["status"] = 402;
            $rs_data["mensaje"] = "Esta consulta esta devolviendo mas de 1 registro";
        }else if(count($variable) == 0){
            $rs_data["status"] = 402;
            $rs_data["mensaje"] = "No se encontraron registros para esta consulta";
        }else{
            if (isset($data[13][0])) {
                $valor_variable = $data[13][0];
            }else{
                $valor_variable = null;
            }
            if ($accion == "obtener") {
                if ($tipo_variable == "caracter") {
                    $rs_data["mensaje"] = $this->formatTypeCaracter($formato_variable, $variable[0][$apodo_variable]);
                }else if($tipo_variable == "num"){
                    $rs_data["mensaje"] = $this->formatTypeNumber($formato_variable, $variable[0][$apodo_variable], $valor_variable);
                }else if($tipo_variable == "fecha"){
                    $rs_data["mensaje"] = $this->formatTypeDate($formato_variable, $variable[0][$apodo_variable]);
                }
                $rs_data["status"] = 200;
            }else{
                $exist_var = $this->AdminSistema_m->searchVariable($apodo_variable);
                if ($exist_var) {
                    $variable = $this->AdminSistema_m->saveVariable($apodo_variable, $base_variable, $tabla_variable, $campo_comple, $condiciones, $estado_variable, $slc_filtro, $tipo_variable, $formato_variable, $valor_variable);
                    $rs_data["status"] = 200;
                    $rs_data["mensaje"] = "Se registro la variable correctamente";
                }else{
                    $rs_data["status"] = 400;
                    $rs_data["mensaje"] = "Ya existe una vairable con ese nombre";
                }
            }
        }
        echo json_encode($rs_data);
        
        
    }
    
    public function formatTypeCaracter($formato, $variable)
    {
        if ($formato == "mayuscula") {
            $data = strtoupper($variable);
        }else if($formato == "minuscula"){
            $data = strtolower($variable);
        }else if($formato == "titulo"){
            $data = ucwords($variable);
        }else{
            $data_form = strtolower($variable);
            $data = ucfirst($data_form);
        }
        return $data;
    }

    public function formatTypeNumber($formato, $variable, $valor)
    {
        if ($formato == "sp_miles") {
            if ($valor == "redondeado") {
                $data_format = round($variable);
                $data = number_format($data_format, 2, ",", ".");
            }else{            
                $data_int = intval($variable);
                $data = number_format($variable, 2, ",", ".");
            }            
        }else{
            if ($valor == "redondeado") {
                $data_format = round($variable);
                $data = number_format($data_format, 0);
            }else{            
                $data_int = intval($variable);
                $data = number_format($data_int, 0);
            }
        }
        return $data;
    }

    public function formatTypeDate($formato, $variable)
    {
        $this->load->helper("my_date");
        if ($formato == "fecha_corta") {
            $data = date("d/m/Y", strtotime($variable));
        }else if($formato == "fecha_larga"){
            $data = date("d/m/Y H:i:s", strtotime($variable));
        }else{
            $fecha = explode(" ", $variable);
            $data_completa = date_to_string($fecha[0], 'L d F a');
            $dt = explode(" ", $data_completa);
            if ($formato == "nombre_dia") {
                $data = $dt[0];
            }else{
                $data = $dt[2];
            }
        }
        return $data;
    }

    public function updateEstado()
    {
        $rs_estado = $this->AdminSistema_m->getEstado($this->input->post("id_variable"));
        if (!empty($rs_estado[0])) {
            if ($rs_estado[0]["estado"] == "activo") {
                $cambiarEstado = "inactivo";
            }else{
                $cambiarEstado = "activo";
            }
            $rs_result = $this->AdminSistema_m->update_estado($this->input->post("id_variable"), $cambiarEstado);
            
            if ($rs_result) {
                $data["status"] = 200;
                $data["mensaje"] = "Se actualizo el estado correctamente";
            }else{
                $data["status"] = 400;
                $data["mensaje"] = "No se pudo actualizar el estado de la variable";
            }
        }else{
            $data["status"] = 400;
            $data["mensaje"] = "No se pudo actualizar el estado de la variable.<br> Esta variable no existe en la base de datos";
        }

        echo json_encode($data);
    }

    public function obtenerDataEditVariable()
    {
        $data = $this->AdminSistema_m->getDataUpdate($this->input->post("id_variable"));
        $remplazar = $data[0]["base_variable"].".".$data[0]["tabla_variable"].".";
        $data[0]["select_variable"] = str_replace($remplazar, "", $data[0]["select_variable"]);
        for ($i=1; $i <=3 ; $i++) { 
            $data[0]["condicion_".$i] = str_replace($remplazar, "", $data[0]["condicion_".$i]);
            $data[0]["condicion_".$i] = str_replace("'", "", $data[0]["condicion_".$i]);
            $op_comparacion = explode(" ", $data[0]["condicion_".$i]);
            foreach ($op_comparacion as $k => $v) {
                if (!empty($v) || $v == 0) {
                    switch ($v) {
                        case '=':
                            $op_formateado[$i][$k] = 1;
                            break;
                        case '>':
                            $op_formateado[$i][$k] = 2;
                            break;
                        case '>=':
                            $op_formateado[$i][$k] = 3;
                            break;
                        case '<':
                            $op_formateado[$i][$k] = 4;
                            break;
                        case '<=':
                            $op_formateado[$i][$k] = 5;
                            break;
                        case '!=':
                            $op_formateado[$i][$k] = 6;
                            break;
                        case 'BETWEEN':
                            $op_formateado[$i][$k] = 7;
                            break;
                        default:
                            $op_formateado[$i][$k] = $v;
                            break;
                    }
                }else{
                    $op_formateado[$i][$k] = "";
                }
            }
            $data[0]["condicion_".$i] = $op_formateado;
            $op_formateado = [];
        }
        $data["bases"] = $this->obtenerBases("update");
        $data["tabla"] = $this->obtenerTablas(["base"=>$data[0]["base_variable"]], "update");
        $data["campos"] = $this->obtenerCampos(["base"=>$data[0]["base_variable"], "tabla"=>$data[0]["tabla_variable"]], "update");
        
        echo json_encode($data);
    }

    public function actualizarVariable()
    {
        // var_dump($this->input->post());die;
        $id = $this->input->post("id");
        $nombre = $this->input->post("nombre");
        $base = $this->input->post("base");
        $tabla = $this->input->post("tabla");
        $campo = $this->input->post("campo");
        $filtro = $this->input->post("filtro");
        $estado = $this->input->post("estado");
        $tipo = $this->input->post("tipo");
        $formato = $this->input->post("formato");

        $condicion[0] = $this->input->post("condiciones_1");
        $condicion[1] = $this->input->post("condiciones_2");
        $condicion[2] = $this->input->post("condiciones_3");
        
        $condiciones = [];
            foreach ($condicion as $key => $value) {
                if ($value[1] != "") {
                    switch ($value[1]) {
                        case '1':
                            $operador = "= '".preg_replace('([^A-Za-z0-9])', '', $value[2])."'";
                            break;
                        case '2':
                            $operador = "> '".preg_replace('([^A-Za-z0-9])', '', $value[2])."'";
                            break;
                        case '3':
                            $operador = ">= '".preg_replace('([^A-Za-z0-9])', '', $value[2])."'";
                            break;
                        case '4':
                            $operador = "< '".preg_replace('([^A-Za-z0-9])', '', $value[2])."'";
                            break;
                        case '5':
                            $operador = "<= '".preg_replace('([^A-Za-z0-9])', '', $value[2])."'";
                            break;
                        case '6':
                            $operador = "!= '".preg_replace('([^A-Za-z0-9])', '', $value[2])."'";
                            break;                    
                        default:
                            $operador = "BETWEEN '".preg_replace('([^A-Za-z0-9])', '', $value[2])."' AND '".preg_replace('([^A-Za-z0-9])', '', $value[3])."'";
                            break;
                    }
                    $condiciones[$key] = "$base.$tabla.$value[0] $operador";
                }else{
                    $condiciones[$key] = null;
                }
            }
        if (isset($_POST["valor"])) {
            $valor = $this->input->post("valor");
            $update = $this->AdminSistema_m->updateVariable($id, $nombre, $base, $tabla, $campo, $condiciones, $filtro, $estado, $tipo, $formato[0], $valor[0]);
        }else{
            $update = $this->AdminSistema_m->updateVariable($id, $nombre, $base, $tabla, $campo, $condiciones, $filtro, $estado, $tipo, $formato[0]);
        }
        if ($update) {
            $rs_data["status"] = 200;
            $rs_data["mensaje"] = "Se actualizo la variable";
        }else{
            $rs_data["status"] = 400;
            $rs_data["mensaje"] = "Error al actualizar la variable";
        }
        echo json_encode($rs_data);
    }

    public function obtenerTipoDato()
    {
        // var_dump($this->input->post());die;
        $base = $this->input->post("base");
        $table = $this->input->post("tabla");
        $columna = $this->input->post("columna");
        
        $tipo_dato = $this->AdminSistema_m->get_tipo_dato($base, $table, $columna);
        
        echo json_encode($tipo_dato);
    }
    
}

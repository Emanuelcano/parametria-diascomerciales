<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

class ApiSituacionLaboral extends REST_Controller
{
	public function __construct()
	{
        parent::__construct();
        $this->load->model('Situacionlab_model', 'situacionLab',TRUE);
    }
    
    public function get_informacion_laboral_get($documento) {
        $endPoint = APIBURO_URL."api/InfoLaboral/infolab?documento=".$documento."&servicio=validator";
        $resp = Requests::get($endPoint, [], []);
        $response = json_decode($resp->body);
        $dataResp['ILT']['IL'] = $response->data->informacion_laboral;
        $dataResp['ILT']['APORTES'] = [];
        $aportes =  [];
        if (isset($response->data->aportes)) {
            foreach ($response->data->aportes as $doc) 
                $aportes = array_merge($aportes,$doc);
                
            usort($aportes, function($a, $b){
                if ($a->anoPeriodoValidado == $b->anoPeriodoValidado) 
                    return $a->mesPeriodoValidado - $b->mesPeriodoValidado;
                return strcmp($a->anoPeriodoValidado, $b->anoPeriodoValidado);
            });

            foreach ($aportes as $key => $value)
                $aportes[$key]->position = $key;
        }
        
        $dataResp['ILT']['APORTES'] = $aportes;
        echo json_encode($dataResp);
    }
    
    public function get_informacion_laboralE_post() {
        $data = $this->input->post();
        $dataResp['ILE']['IL'] = [];
        $dataResp['ILE']['APORTES'] = [];
        $aportes =  [];
        $dataResp['ILE']['IL'] = $this->situacionLab->get_informacion_laboralE(['documento' => $data['documento'], 'id_solicitud' => $data['id_solicitud']]);

        if (isset($dataResp['ILE']['IL'][0]->id)) {
            $id_consulta = $dataResp['ILE']['IL'][0]->id;
            $aportantes = $this->situacionLab->get_aportantes_laboralE(['id_consulta' => $id_consulta]);
            foreach ($aportantes as $key => $value) {
                $aportantesList[] = $value['id'];
                $Datos[$value['id']] = $value;
            }
            $get_aportes = $this->situacionLab->get_aportes_laboralE(['id_aportante' => $aportantesList, 'id_consulta' => $id_consulta]);
            foreach ($get_aportes as $key => $value) {
                $dataResp['ILE']['APORTES'][] = array_merge($Datos[$value['id']],$value);
            }
        }
        echo json_encode($dataResp);
    }
    
    public function get_informacion_laboralArus_post() {        
        $data = $this->input->post();

        $dataResp['ILArus']['IL'] = [];
        $dataResp['ILArus']['APORTES'] = [];
        $pivotPeriodo = 0;
        $salario = 0;

        $Resp = [];
        $dataRespArus = new stdClass();
        $dataRespArus = $this->situacionLab->get_informacion_laboralArus(['documento' => $data['documento'], 'id_solicitud' => $data['id_solicitud']]);

        if (count($dataRespArus)) {
            foreach ($dataRespArus as $key => $value) {

                if ($key == 0) {
                    $Resp['informacion_laboral']['EPS'] = $value['nombreAdminEPS'];
                    $Resp['informacion_laboral']['AFP'] = $value['nombreAdminPension'];
                    $Resp['informacion_laboral']['fecha_consulta'] = $value['created_at'];
                }
                $pivotPeriodo++;
                $salario += $value['salarioBasico'];
                $Resp['aportes'][$value['numeroDocumentoAportante']][] = $value;
                $aportes = [];
                $aportes['periodo'] = $value['periodoCotizacion'];
                $aportes['NIT'] = $value['numeroDocumentoAportante'];
                $aportes['empresa'] = $value['empresa'];
                $aportes['salario'] = $value['salarioBasico'];
                $dataResp['ILArus']['APORTES'][] = $aportes;
            }
            $Resp['salario'] = $salario;
            if($pivotPeriodo !== 0){
                $Resp['informacion_laboral']['ocupacion'] = ($pivotPeriodo / 12 ) * 100;
            }else{
                $Resp['informacion_laboral']['ocupacion'] = 0;
            }
            
            $Resp['informacion_laboral']['rotacion'] = count($Resp['aportes']);

            $mayor_salario = max(array_column($dataRespArus, 'salarioBasico') );
            $Resp['informacion_laboral']['mayor_salario'] = number_format($mayor_salario,2,  ',', '');
            $menor_salario = min(array_column($dataRespArus, 'salarioBasico') );
            $Resp['informacion_laboral']['menor_salario'] = number_format($menor_salario,2,  ',', '');
            $salario_promedio = $salario / $pivotPeriodo;
            $Resp['informacion_laboral']['salario_promedio'] = number_format($salario_promedio,2,  ',', '');

            $dataResp['ILArus']['IL'][] = $Resp['informacion_laboral'];
        }
        
        echo json_encode($dataResp);
    }

    public function get_informacion_laboralMareigua_post(){
        $data = $this->input->post();
         
        $dataResp['ILMareigua']['IL'] = [];
        $dataResp['ILMareigua']['APORTES'] = [];
        $aportes =  [];
        $aporte =  [];
        $consulta = ['documento' => $data['documento'], 'id_solicitud' => $data['id_solicitud']];
        $dataResp['ILMareigua']['IL'] = $this->situacionLab->get_informacion_laboralMareigua($consulta);
        
        $aportes = $this->situacionLab->get_aportantes_laboralMareigua($consulta);
        foreach ($aportes as $key => $value) {
            $resp = $this->situacionLab->get_aportes_laboralMareigua(['id_mareigua_consulta' => $value->id_mareigua_consulta, 'id_mareigua_aportante'  => $value->id ]);
            foreach ($resp as $key2 => $value2) {
                $data =  [];
                $data['periodo']    = $value2->ano_periodo_validado .'-'. $value2->mes_periodo_validado;
                $data['NIT']        = $value->numero_identificacion_aportante;
                $data['empresa']    = $value->razon_social_aportante;
                $data['salario']    = $value2->ingresos;
                $aporte[] = $data;
            }
        }
        $dataResp['ILMareigua']['APORTES'] = $aporte;
        
        echo json_encode($dataResp);
    }

    public function update_inflaboral_post(){
        $data = $this->input->post();
        $action = $data['inflaboral'];
        $dataResp['status'] = false;
        $dataRespApi = new stdClass();
        $dataRespApi->success = false;
        switch ($action) {
            case 'arus':
                $dataResp['status']  = true;
                $actualizado = $this->situacionLab->get_id_valor_ingreso_consulta_arus(['documento' => $data['documento'], 'id_solicitud' => $data['id_solicitud']]);
                $endPoint = APIBURO_URL."api/ArusServices/cotizantes/".$data['id_solicitud'];                
                $dataResp = $this->dataRespApi($actualizado, $endPoint);
                break;
            case 'experian':
                $actualizado = $this->situacionLab->get_id_valor_ingreso_consulta(['documento' => $data['documento'], 'id_solicitud' => $data['id_solicitud']]);
                $endPoint = APIBURO_URL."api/ValorIngreso/consulta/".$data['id_solicitud'];
                $dataResp = $this->dataRespApi($actualizado, $endPoint);
                break;
            case 'mareigua':
                $actualizado = $this->situacionLab->get_id_valor_ingreso_consultaMareigua(['documento' => $data['documento'], 'id_solicitud' => $data['id_solicitud']]);
                $endPoint = APIBURO_URL."api/mareigua/consulta/".$data['id_solicitud'];
                $dataResp = $this->dataRespApi($actualizado, $endPoint);
                break;
        }
        echo json_encode($dataResp);
    }
    private function dataRespApi($actualizado, $endPoint){
        if (!isset($actualizado)) {
            $dataResp['data']  = json_decode(Requests::post($endPoint, [], [])->body);
            $dataResp['status']  = true;
            $dataResp['msj']  = "sin Datos, actualizando ";
        } elseif ($actualizado->DiasRestantes <= 0) {
            $dataResp['data']  = json_decode(Requests::post($endPoint, [], [])->body);
            $dataResp['status']  = true;
            $dataResp['msj']  = "Datos Antiguos, hay que Actualizar ";
        } else {
            $dataResp['status']  = true;
            $dataResp['msj']  = "Datos Recientes, No hay que Actualizar";
        }
        return $dataResp;
    }
    
}

?>
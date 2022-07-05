<?php
/**
 * consulta el estado de una transaccion epayco
 * @param string $public_key de la cuenta ePayco
 * @param string $ref_payco referencia de pago de la transacción
 * @return json
 */
function epayco_consulta_transaccion_helper($ref_epayco = ''){
    $CI =& get_instance();

    $CI->load->library('custom_log');
    $url_api_medios_de_pago = $CI->config->item('url_api_medios_de_pago');

    $params = array (
        'x_ref_payco' => $ref_epayco
    );
    
    $url = $url_api_medios_de_pago . '/TestEpaycoProvider/epayco_notificaciones';

    $request = Requests::post($url, array('Accept' => 'application/json'), $params);

    $return = json_decode($request->body);

    $CI->custom_log->write_log("INFO", $url);
    $CI->custom_log->write_log("INFO", $params);
    $CI->custom_log->write_log("INFO", $return);
    
    return $return;
} 

/**
 * Envia un template de whatsapp
 *
 * @param $canal
 * @param $idSolicitud
 * @param $telefono
 * @param $template
 * @param $idTemplate
 *
 * @return string
 */
function sendWhatsappTemplate($canal, $idSolicitud, $telefono, $template, $idTemplate)
{
	$params = array(
		'solID' => $idSolicitud,
		'phoneN' => $telefono,
		'Template' => $template,
		'id_template' => $idTemplate
	);
	
	if ($canal == "1") {
		$url = base_url() . 'comunicaciones/twilio';
	} else {
		$url = base_url() . 'comunicaciones/TwilioCobranzas';
	}
	
	$endpoint = $url . '/send_template_message_new';
	
	$request = Requests::post($endpoint, array('Accept' => 'application/json'), $params);
	
	return $request->body;
}

/**
 * Trackeo el envio del template de whatsapp whatsapp
 *
 * @param $idSolicitud
 * @param $idOperador
 *
 * @return string
 */
function trackEnvioWhatsapp($idSolicitud, $idOperador)
{
	$endpoint = base_url() . 'api/track_gestion';
	
	$params = [
		'observaciones' => 'Template Whatsapp enviado',
		'id_tipo_gestion' => 182,
		'id_solicitud' => $idSolicitud,
		'id_operador' => $idOperador
	];
	
	$request = Requests::post($endpoint, array('Accept' => 'application/json'), $params);
	
	return $request->body;
}

/**
 * genera una estructura json standard para comunicación interna
 * @return array
 */
function array_base(){
    $array = array();
    $array['success'] = false;
    $array['title_response'] = '';
    $array['text_response'] = '';
    $array['data'] = array();
    return $array;
}
/**
 * header para evitar CORS desde otros dominios
 * @return void
 */
function access_control_allow(){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
}

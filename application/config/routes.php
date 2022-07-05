<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------

*/

$route['default_controller'] = 'app';
$route['404_override'] = '';

/***************************************************************************/
// INICIO APIS
/***************************************************************************/
// API
// Track gestion
$route['api/track_gestion']['GET'] = 'api/ApiTracker/search';
$route['api/track_gestion']['POST'] = 'api/ApiTracker/save';
$route['api/admin/track_gestion']['POST'] = 'api/ApiAdminTracker/save';
$route['api/dar_baja']['POST'] = 'api/ApiLegales/dar_baja';

// Track images
$route['api/galery/subir_imagen']['POST'] = 'api/ApiGalery/upload_image';
$route['api/galery/subir_imagen_veriff']['POST'] = 'api/ApiGalery/uploadMedia_veriff';
$route['api/galery/subir_imagen_metamap']['POST'] = 'api/ApiGalery/uploadMedia_metamap';
$route['api/galery/descarga_imagenes']['GET'] = 'api/ApiGalery/search';

// Agenda
$route['api/agendar']['POST'] = 'api/ApiOperator/agendar';

//Ajustes
$route['api/ajustes/buscar/(:num)']['GET'] = 'api/ApiAjustes/buscar_solicitud/$1';
$route['api/ajustes/actualizar_paso']['POST'] = 'api/ApiAjustes/actualizar_paso';
$route['api/ajustes/levantar_rechazado']['POST'] = 'api/ApiAjustes/levantar_rechazado';
$route['api/ajustes/reasignar_solicitud']['POST'] = 'api/ApiAjustes/reasignar_solicitud';
$route['api/ajustes/actualizar_estado']['POST'] = 'api/ApiAjustes/actualizar_estado';
$route['api/ajustes/actualizar_situacion']['POST'] = 'api/ApiAjustes/actualizar_situacion';
$route['api/ajustes/actualizar_telefono']['POST'] = 'api/ApiAjustes/actualizar_telefono';
$route['api/ajustes/anular_telefono']['POST'] = 'api/ApiAjustes/anular_telefono';
$route['api/ajustes/aplicar_descuento']['POST'] = 'api/ApiAjustes/aplicar_descuento';
$route['api/ajustes/reImputar_pagos']['POST'] = 'api/ApiAjustes/reimputar_pagos';
$route['api/ajustes/agregar_detalle_pago']['POST'] = 'api/ApiAjustes/agregar_desglose_pago';
$route['api/ajustes/actualizar_cupo']['POST'] = 'api/ApiAjustes/actualizar_cupo';
$route['api/ajustes/actualizar_datospers']['POST'] = 'api/ApiAjustes/update_datospers';
//Tablero Mora
$route['api/tableromora/mora']['GET'] = 'api/ApiTableroMora/mora';
$route['api/tableromora/actualizar']['POST'] = 'api/ApiTableroMora/ActualizarMora';
$route['api/fechasVencimientoFront']['GET'] = 'api/ApiTableroMora/fechasVencimientoFront';


$route['api/ajustes/get_bank']['POST'] = 'api/ApiAjustes/get_bank';
$route['api/ajustes/reprocesar_credito/(:any)']['GET'] = 'api/ApiAjustes/reprocesar_credito/$1';
$route['ajustes/gestionPagare']['POST'] = 'api/ApiAjustes/reprocesar_pagare';
$route['ajustes/verificacion']['POST'] = 'api/ApiAjustes/autorizar_verificacion';


// Operadores
$route['api/operadores/buscar']['GET'] = 'api/ApiOperator/search';
$route['api/operadores/get_by_solicitud/(:num)']['GET'] = 'api/ApiOperator/getOpeBySolicitud/$1';
$route['api/operadores/actualizar_operador']['POST'] = 'api/ApiOperadores/actualizar_operador';
$route['api/operadores/actualizar_operador_cobranza']['POST'] = 'api/ApiOperadores/actualizar_operador_cobranza';
$route['api/operadores/cargar_avatar']['POST'] = 'api/ApiOperadores/subir_imagen';
$route['api/operadores/cambiar_estado']['POST'] = 'api/ApiOperadores/set_estado_operador';
$route['api/operadores/registrar']['POST'] = 'api/ApiOperadores/registrar_operador';
$route['api/operadores/get_modulos']['GET'] = 'api/ApiOperadores/get_modulos';
$route['api/operadores/get_modulos/(:any)']['GET'] = 'api/ApiOperadores/get_modulos/$1';
$route['api/operadores/get_modulos_nombre/(:any)']['GET'] = 'api/ApiOperadores/get_modulos_nombre/$1';
$route['api/operadores/actualizar_usuario']['POST'] = 'api/ApiOperadores/actualizar_usuario';
$route['api/operadores/actualizar_clave']['POST'] = 'api/ApiOperadores/actualizar_usuario_clave';
$route['api/operadores/nuevo_tipo_operador']['POST'] = 'api/ApiOperadores/nuevo_tipo_operador';
$route['api/operadores/consultar_asignaciones']['POST'] = 'api/ApiOperadores/get_asignaciones_operador';
$route['api/operadores/consultar_operador/(:any)']['GET'] = 'api/ApiOperadores/get_operador/$1';
$route['api/operadores/consultar_solicitud/(:any)/(:any)']['GET'] = 'api/ApiOperadores/get_solicitud_by/$1/$2';
$route['api/operadores/asignar_solicitudes']['POST'] = 'api/ApiOperadores/set_asignaciones';
$route['api/operadores/consultar_chats/(:any)']['GET'] = 'api/ApiOperadores/get_chats/$1';
$route['api/operadores/get_lista_operadores_activos']['GET'] = 'api/ApiOperadores/get_lista_operadores_activos';
$route['api/operadores/get_tipos_operadores']['GET'] = 'api/ApiOperadores/get_tipos_operadores';

$route['api/operadores/get_ausencias_operador/(:any)']['GET'] = 'api/ApiOperadores/get_ausencias_operador/$1';
$route['api/operadores/get_horario_operador/(:any)']['GET'] = 'api/ApiOperadores/get_horario_operador/$1';
$route['api/operadores/get_horario_operador_update/(:any)']['GET'] = 'api/ApiOperadores/get_horario_operador_update/$1';
$route['api/operadores/cambioEstadoHorario']['POST'] = 'api/ApiOperadores/cambioEstadoHorario';
$route['api/operadores/updatedoHorario']['POST'] = 'api/ApiOperadores/updatedoHorario';
$route['api/operadores/registrar_ausencia_operador']['POST'] = 'api/ApiOperadores/registrar_ausencia_operador';
$route['api/operadores/registar_horario_operadores']['POST'] = 'api/ApiOperadores/registar_horario_operadores';
$route['api/operadores/registar_agente']['POST'] = 'api/ApiOperadores/registar_agente';
$route['api/operadores/cambioEstadoAgente']['POST'] = 'api/ApiOperadores/cambioEstadoAgente';
$route['api/operadores/get_agente_update/(:any)']['GET'] = 'api/ApiOperadores/get_agente_update/$1';
$route['api/operadores/updateAgente']['POST'] = 'api/ApiOperadores/updateAgente';
$route['api/operadores/tableHorariosOperadores']['GET'] = 'api/ApiOperadores/tableHorariosOperadores';
$route['api/operadores/tableAgentes']['GET'] = 'api/ApiOperadores/tableAgentes';


$route['api/operadores/tableCreateCampania']['GET'] = 'api/ApiOperadores/tableCreateCampania';
$route['api/operadores/cambioEstadoCampania']['POST'] = 'api/ApiOperadores/cambioEstadoCampania';
$route['api/operadores/registrar_campania']['POST'] = 'api/ApiOperadores/registrar_campania';
$route['api/operadores/get_campania_update/(:any)']['GET'] = 'api/ApiOperadores/get_campania_update/$1';
$route['api/operadores/updateCamapania']['POST'] = 'api/ApiOperadores/updateCamapania';
$route['api/operadores/validacionAgente']['POST'] = 'api/ApiOperadores/validacionAgente';
$route['api/operadores/tableSkill']['GET'] = 'api/ApiOperadores/tableSkill';
$route['api/operadores/registrarSkill']['POST'] = 'api/ApiOperadores/registrarSkill';
$route['api/operadores/cambioEstadoSkill']['POST'] = 'api/ApiOperadores/cambioEstadoSkill';
$route['api/operadores/get_skill_update/(:any)']['GET'] = 'api/ApiOperadores/get_skill_update/$1';
$route['api/operadores/updateSkill']['POST'] = 'api/ApiOperadores/updateSkill';
$route['api/operadores/get_lista_operador_central']['GET'] = 'api/ApiOperadores/get_lista_operador_central';
$route['api/operadores/get_operador_skill/(:any)']['GET'] = 'api/ApiOperadores/get_operador_skill/$1';
$route['api/operadores/asignarSkills']['POST'] = 'api/ApiOperadores/asignarSkills';
$route['api/operadores/set_estado_ausencia']['POST'] = 'api/ApiOperadores/set_estado_ausencia';
$route['api/operadores/update_ausencia']['POST'] = 'api/ApiOperadores/update_ausencia';
$route['api/operadores/cambio_clave']['POST'] = 'api/ApiOperadores/cambio_clave';

// Cobranzas
$route['api/credito/buscar']['POST']	= 'api/ApiCredito/search';
$route['api/credito/crear_promesa']['POST']	= 'api/ApiCredito/generar_promesa';
$route['api/credito/consultar_promesa/(:any)']['GET']	= 'api/ApiCredito/consultar_promesas/$1';
$route['api/credito/consultar_promesa_detalle/(:any)']['GET']	= 'api/ApiCredito/consultar_promesa_detalle/$1';
$route['api/credito/consultar_credito/(:any)']['GET']	= 'api/ApiCredito/consultar_credito/$1';
$route['api/credito/agendar']['POST']	= 'api/ApiCredito/agendar_telefono_mail';
$route['api/credito/actualizarAgenda']['POST']	= 'api/ApiCredito/actualizar_agenda';
$route['api/credito/actualizarAgendaSolicitudes']['POST']	= 'api/ApiCredito/actualizar_agenda_solicitudes';
$route['api/credito/enviarMensaje']['POST']	= 'api/ApiCredito/enviar_mensaje';
$route['api/credito/enviarMail']['POST']	= 'api/ApiCredito/enviar_mail';
$route['api/credito/detallePlanPago']['POST']	= 'api/ApiCredito/detalle_plan_pago';
$route['api/credito/get_departamentos']['GET']	= 'api/ApiCredito/get_departamentos';
$route['api/credito/get_municipios/(:any)']['GET']	= 'api/ApiCredito/get_municipios/$1';
$route['api/credito/situacion_laboral/(:any)']['GET']	= 'api/ApiCredito/get_situacion_laboral/$1';
$route['api/credito/actualizar_situacion_laboral/(:any)']['GET']	= 'api/ApiCredito/update_situacion_laboral/$1';
$route['api/credito/get_desempenho_operador/(:any)']['GET']	= 'api/ApiCredito/get_desempenho_operador/$1';
$route['api/credito/get_gestiones_consultor/(:any)/(:any)']['GET']	= 'api/ApiCredito/get_casos_consultor/$1/$2';
$route['api/credito/get_track_marcacion/(:num)']['GET']			= 'api/ApiCredito/get_llamadas_resumen/$1';
$route['api/credito/get_llamadas_detalle/(:num)']['GET']			= 'api/ApiCredito/get_llamadas_detalle/$1';
$route['apiGetLlamadasNeotell']['POST']			= 'api/ApiCredito/llamadas_detalle_neotell/';
$route['api/credito/recalculardeuda']['POST']			= 'api/ApiCredito/recalcular_deuda';
$route['api/credito/ajustar_descuento_promesa']['POST']			= 'api/ApiCredito/ajustar_descuento_acuerdo';
$route['api/credito/reproducir_audio/(:any)']['GET'] 			= 'api/ApiCredito/get_audio_reproduccion/$1';
$route['api/credito/enviarMailDesglose']['POST']	= 'api/ApiCredito/enviar_mail_desglose';
$route['api/credito/descuento_campania']['POST']	= 'api/ApiCredito/get_descuento_campania';
$route['api/credito/get_creditos_cliente/(:any)']['GET']	= 'api/ApiCredito/get_creditos_cliente/$1'; 
$route['api/credito/buscar/pagos/(:any)']['GET']	= 'api/ApiCredito/get_pagos/$1'; 
$route['api/credito/deuda_actual/(:any)']['GET']	= 'api/ApiCredito/get_deuda_actual_cliente/$1'; 
$route['api/credito/fix_pago']['POST']	= 'api/ApiCredito/correccion_pago'; 
$route['api/credito/get_pagos']['POST']	= 'api/ApiCredito/get_pagos_by_client'; 
$route['api/cobranzas/get_descuentos']['GET']	= 'api/ApiCredito/get_descuentos'; 
$route['api/credito/envio_link_pago_whatsapp']['POST']	= 'api/ApiCredito/envio_link_pago_whatsapp';

$route['api/credito/enviarSolicitudImputacion']['POST']	= 'api/ApiCredito/agregarSolicitudImputacion';
$route['api/credito/uploadComprobante/(:num)']['POST'] = 'api/ApiCredito/uploadComprobante/$1';
$route['api/credito/cantImputadas']['GET']	= 'api/ApiCredito/getCantImputadas';
$route['api/credito/anularSolicitud/(:num)']['POST'] = 'api/ApiCredito/anularSolicitudImputacion/$1';
//$route['api/credito/get_llamadas_detalle']['POST']			= 'api/ApiCredito/get_llamadas_detalle';
$route['api/credito/buscar_acuerdo']['POST']	= 'api/ApiCredito/buscar_acuerdos';
$route['api/credito/updatefechavencimiento']['POST']	= 'api/ApiCredito/updateFechaVencimiento';

//Campania CRM Supervisores
$route['api/campanias/campania_crm']['POST'] = 'api/ApiCampanias/get_campania_crm';
$route['api/campanias/estado']['POST'] = 'api/ApiCampanias/get_estado_campania';
$route['api/campanias/getEstadoCampania']['POST'] = 'api/ApiCampanias/getEstadoCampania';
$route['api/campanias/confirmar']['POST'] = 'api/ApiCampanias/get_confirmar_estado';
$route['api/campanias/desactivarCampania']['POST'] = 'api/ApiCampanias/desactivarCampania';
$route['api/campanias/activarCampaniaManual']['POST'] = 'api/ApiCampanias/activarCampaniaManual';
$route['api/campanias/campania_campos']['POST'] = 'api/ApiCampanias/campania_campos';
$route['api/campanias/getCampaniaExtraInfo']['POST'] = 'api/ApiCampanias/getCampaniaExtraInfo';
$route['api/campanias/update_campania_crm']['POST'] = 'api/ApiCampanias/update_campania_crm';
$route['api/campanias/activarCampania']['POST'] = 'api/ApiCampanias/activarCampania';
$route['api/campanias/salirCampania']['POST'] = 'api/ApiCampanias/salirCampania';
$route['api/campanias/cambiarOperadorADescanso']['POST'] = 'api/ApiCampanias/cambiarOperadorADescanso';
$route['api/campanias/reactivarOperador']['POST'] = 'api/ApiCampanias/reactivarOperador';
$route['api/campanias/getCantidadCasosEnFecha']['POST'] = 'api/ApiCampanias/getCantidadCasosEnFecha';
$route['api/campanias/getCasosCampania']['POST'] = 'api/ApiCampanias/getCasosCampania';
$route['api/campanias/activar_casos']['POST'] = 'api/ApiCampanias/activar_casos';
$route['api/campanias/getCasosAsignadosDetallados']['POST'] = 'api/ApiCampanias/getCasosAsignadosDetallados';
$route['api/campanias/operadores_activos']['POST'] = 'api/ApiCampanias/operadores_activos';
$route['api/campanias/cantidad_casos']['POST'] = 'api/ApiCampanias/cantidad_casos';
$route['api/campanias/casos_gestion']['POST'] = 'api/ApiCampanias/casos_gestion';
$route['api/campanias/operadores']['POST'] = 'api/ApiCampanias/operadores';
$route['api/campanias/get_operadores_campania']['POST'] = 'api/ApiCampanias/getOperadoresPorCampania';
$route['api/campanias/getOperadoresPorTipoYEquipo']['POST'] = 'api/ApiCampanias/getOperadoresPorTipoYEquipo';
$route['api/campanias/campania_activa_operador']['POST'] = 'api/ApiCampanias/campania_activa_operador';
$route['api/campanias/gestionarCierreCaso']['POST'] = 'api/ApiCampanias/gestionarCierreCaso';
$route['api/campanias/guardarGestionOperador']['POST'] = 'api/ApiCampanias/guardarGestionOperador';
$route['api/campanias/calcularTiempoRestanteCampania']['POST'] = 'api/ApiCampanias/calcularTiempoRestanteCampania';
$route['api/campanias/consultar_asigando_operador']['POST'] = 'api/ApiCampanias/consultar_asigando_operador';
$route['api/campanias/consulta']['POST'] = 'api/ApiCampanias/consulta';
$route['api/campanias/tiempo_promedio']['POST'] = 'api/ApiCampanias/tiempo_promedio';
$route['api/campanias/tipo_por_operador']['POST'] = 'api/ApiCampanias/tipo_por_operador';
$route['api/campanias/tipificacion']['POST'] = 'api/ApiCampanias/tipificacion_operadores';
$route['api/campanias/tabla_gestion']['POST'] = 'api/ApiCampanias/tabla_gestiones_operadores';
$route['api/campanias/tabla_tipificaciones']['POST'] = 'api/ApiCampanias/tabla_tipificaciones_total';
$route['api/campanias/tablaTotalTipificaciones']['POST'] = 'api/ApiCampanias/tablaTotalTipificaciones';
$route['api/campanias/downloadCSVTotalCasos/(:num)']['GET'] = 'api/ApiCampanias/downloadCSVTotalCasos/$1';
$route['api/campanias/downlaodCSVCasosGestionadosPorOperador/(:num)/(:num)']['GET'] = 'api/ApiCampanias/downlaodCSVCasosGestionadosPorOperador/$1/$2';
$route['api/campanias/downlaodCSVTotalCasosGestionados/(:num)']['GET'] = 'api/ApiCampanias/downlaodCSVTotalCasosGestionados/$1';
$route['api/campanias/downloadCSVTipificacionPorTipo/(:num)/(:num)']['GET'] = 'api/ApiCampanias/downloadCSVTipificacionPorTipo/$1/$2';
$route['api/campanias/downloadCSVTipificacionPorDetalle/(:num)/(:num)']['GET'] = 'api/ApiCampanias/downloadCSVTipificacionPorDetalle/$1/$2';
$route['api/campanias/sendCampaniaWhatsappTemplates']['POST'] = 'api/ApiCampanias/sendCampaniaWhatsappTemplates';
$route['api/campanias/sendCampaniaWhatsappTemplatesByCSV']['POST'] = 'api/ApiCampanias/sendCampaniaWhatsappTemplatesByCSV';

$route['api/campanias/envioPreliminarCampaniasWhatsapp']['POST'] = 'api/ApiCampanias/envioPreliminarCampaniasWhatsapp';

$route['api/campanias/cambiarEstadoOperadorActivo']['POST'] = 'api/ApiCampanias/cambiarEstadoOperadorActivo';
$route['api/campanias/cambiarEstadoOperadorInactivo']['POST'] = 'api/ApiCampanias/cambiarEstadoOperadorInactivo';
$route['api/campanias/cambiarEstadoOperadorDesactivado']['POST'] = 'api/ApiCampanias/cambiarEstadoOperadorDesactivado';
$route['api/campanias/cambiarEstadoOperadorDescanso']['POST'] = 'api/ApiCampanias/cambiarEstadoOperadorDescanso';
$route['api/campanias/removeCreditoDeOperador']['POST'] = 'api/ApiCampanias/removeCreditoDeOperador';

$route['api/campanias/startGestion']['POST'] = 'api/ApiCampanias/startGestion';
$route['api/campanias/endGestion']['POST'] = 'api/ApiCampanias/endGestion';

$route['api/credito/get_info_solicitud/(:any)']['GET']	= 'api/ApiCredito/get_info_solicitud/$1'; //sin uso es para la separacion de los componentes
// Solicitudes
$route['api/solicitud/solicitudes_pendientes']['GET'] ='api/ApiSolicitud/getSolicitudesPendientes';
$route['api/solicitud/actualizar']['POST']         = 'api/ApiSolicitud/update';
$route['api/solicitud/validar_telefono/(:num)']['GET']         = 'api/ApiSolicitud/validar_telefono/$1';
$route['api/solicitud/actualizar/cliente']['POST'] = 'api/ApiSolicitud/update_data_client';
$route['api/solicitud/actualizar_imagen']['POST']  = 'api/ApiSolicitud/update_image';
$route['api/solicitud/listar_registro_por_visar']['POST']  = 'api/ApiSolicitud/listar_registro_por_visar';
$route['api/solicitud/buscar']['POST']	           = 'api/ApiSolicitud/search';
$route['api/solicitud/banco/actualizar']['POST']   = 'api/ApiDatosBancarios/update';
$route['api/solicitud/listar']['POST']	           = 'api/ApiSolicitud/list_solicitudes';
$route['api/solicitud/listar_desembolso']['POST']  = 'api/ApiSolicitud/listar_desembolso';
$route['api/solicitud/listar_x_registro']['POST']  = 'api/ApiSolicitud/listar_x_registro';
$route['api/solicitud/validarDesembolso']['POST']  = 'api/ApiSolicitud/validar_desembolso';
$route['api/solicitud/consultar_solicitud_cliente/(:any)']['GET']	= 'api/ApiSolicitud/consultar_solicitud_cliente/$1';
$route['api/solicitud/actualizarAgendaLocalidad']['POST']	= 'api/ApiSolicitud/actualizar_agenda_localidad';
$route['api/solicitud/agendarTelefono']['POST']	= 'api/ApiSolicitud/agendar_telefono_solicitante';
$route['api/solicitud/updateAgendaProveedor']['POST']	= 'api/ApiSolicitud/updateAgendaProveedor';
$route['api/solicitud/enviarSmsIvrAgendaTelefonica']['POST']	= 'api/ApiSolicitud/enviarSmsIvrAgendaTelefonica';
$route['api/solicitud/updateEstadoServicio']['POST']	= 'api/ApiSolicitud/updateEstadoServicio';
$route['api/solicitud/actualizarAgendaEstado']['POST']	= 'api/ApiSolicitud/actualizar_agenda_estado';
$route['api/solicitud/CambioEstadoServicioAgenda']['POST']	= 'api/ApiSolicitud/cambio_estado_servicio';
$route['api/solicitud/actualizarMailEstado']['POST']	= 'api/ApiSolicitud/actualizar_mail_estado';
$route['api/solicitud/agendarMail']['POST']	= 'api/ApiSolicitud/agendar_mail_solicitante';
$route['api/solicitud/get_update_numero/(:num)']['GET']	= 'api/ApiSolicitud/get_update_numero/$1';
$route['api/solicitud/actualizarEditAgenda']['POST']	=	'api/ApiSolicitud/update_edit_agenda_tlf';
$route['api/solicitud/agendaMailTemplateHtml']['POST']  = 'api/ApiSolicitud/get_mail_template_html/';
$route['api/solicitud/update_primer_reporte_agenda_tlf']['GET'] ='api/ApiSolicitud/update_primer_reporte_agenda_tlf';
$route['api/solicitud/enviarMailAgendaPepipost']['POST']	= 'api/ApiSolicitud/enviarMailAgendaPepipost';
$route['api/solicitud/consultarCliente/(:num)']['GET']	= 'api/ApiSolicitud/consultar_cliente/$1';
$route['api/solicitud/update_niveles/(:num)']['GET']	= 'api/ApiSolicitud/update_niveles/$1';
$route['api/solicitud/checkSolicitudHasTrack']['POST']	= 'api/ApiSolicitud/checkSolicitudHasTrack';
$route['api/solicitud/checkSolicitudHasTrackToday']['POST']	= 'api/ApiSolicitud/checkSolicitudHasTrackToday';
$route['api/solicitud/get_template_data']['POST']	= 'api/ApiSolicitud/get_template_data';
$route['api/solicitud/transferenciaRechazada']['POST']	= 'api/ApiSolicitud/transferenciaRechazada';


$route['api/solicitud/consultarDevolucion/(:num)']['GET']	= 'api/ApiSolicitudDevolucion/consultar_devolucion/$1';//consulta si un liente tiene saldo para devolver
$route['api/solicitud/consultarDatosDevolucion/(:num)']['GET']	= 'api/ApiSolicitudDevolucion/consultar_datos_devolucion/$1';
$route['api/solicitud/uploadComprobanteDevolucion']['POST']	= 'api/ApiSolicitudDevolucion/uploadComprobanteDevolucion';
$route['api/solicitud/deleteComprobanteDevolucion']['POST']	= 'api/ApiSolicitudDevolucion/deleteComprobanteDevolucion';
$route['api/solicitud/generarSolicitudDevolucion']['POST']	= 'api/ApiSolicitudDevolucion/solicitar_devolucion';
$route['api/solicitud/agregarDevolucion']['POST']	= 'api/ApiSolicitudDevolucion/agregar_devolucion_precarga';
$route['api/solicitud/UpdateSolicitudDevolucion']['POST']	= 'api/ApiSolicitudDevolucion/update_devolucion_precarga';
$route['api/solicitud/getSolicitudesDevolucion']['GET']	= 'api/ApiSolicitudDevolucion/get_solicitudes_devolucion';
$route['api/solicitud/getSolicitudesDevolucionPaginada']['GET']	= 'api/ApiSolicitudDevolucion/get_solicitudes_devolucion_paginada';
$route['api/solicitud/solicitudes_devolucion_paginada']['GET']	= 'api/ApiSolicitudDevolucion/get_solicitudes_devolucion_paginada_doc';

$route['api/solicitud/procesarSolicitudDevolucion']['POST']	= 'api/ApiSolicitudDevolucion/procesar_devolucion';
$route['api/solicitud/generarArchivoDevolucion']['GET']	= 'api/ApiSolicitudDevolucion/generar_csv_santander';
$route['api/solicitud/cambiarEstado']['POST']	= 'api/ApiSolicitudDevolucion/cambiar_estado_procesando';
$route['api/solicitud/procesarRespuestaDevolucion']['POST']	= 'api/ApiSolicitudDevolucion/procesar_respuesta_santander';
$route['api/solicitud/generarArchivoDevolucionDebito']['GET']	= 'api/ApiSolicitudDevolucion/devolucionDebitoAutomatico';

$route['solicitud/send_biometria']['POST']	= 'api/ApiSolicitud/send_link_biometria';
$route['aprobacionAutomaticaBancolombia']['POST']	= 'api/ApiSolicitud/aprobacion_automatica_bancolombia';
$route['aprobacionAutomaticaNoBancolombia']['POST']	= 'api/ApiSolicitud/aprobacion_automatica_no_bancolombia';

$route['api/solicitud/gestionar_descanso_operador']['POST']	= 'api/ApiSolicitud/gestionar_descanso_operador';
$route['api/solicitud/consultar_solicitudes_obligatorias']['GET']	= 'api/ApiSolicitud/consultar_solicitudes_obligatorias';
$route['api/solicitudes/iniciar_gestion_obligatoria']['POST'] = 'api/ApiSolicitud/iniciar_gestion_obligatoria';
$route['api/solicitudes/extension_gestion_obligatoria']['POST'] = 'api/ApiSolicitud/extension_gestion_obligatoria';
$route['api/solicitudes/cerrar_gestion_obligatoria']['POST'] = 'api/ApiSolicitud/cerrar_gestion_obligatoria';


// Referencias Verificaciones
$route['api/ApiVerificacion/set_referencia_familiar']['POST']    = 'api/ApiVerificacion/guardarReferenciaFamiliar';
$route['api/ApiVerificacion/set_referencia_titular']['POST']     = 'api/ApiVerificacion/guardarReferenciaTitular';
$route['api/ApiVerificacion/set_referencia_personal']['POST']    = 'api/ApiVerificacion/guardarReferenciaPersonal';
$route['api/ApiVerificacion/set_referencia_laboral']['POST']     = 'api/ApiVerificacion/guardarReferenciaLaboral';
$route['api/ApiVerificacion/set_referencia_titular_ind']['POST'] = 'api/ApiVerificacion/guardarReferenciaTitularInd';
$route['api/ApiVerificacion/set_referencia_proveedor1']['POST']  = 'api/ApiVerificacion/guardarReferenciaProveedor1';
$route['api/ApiVerificacion/set_referencia_proveedor2']['POST']  = 'api/ApiVerificacion/guardarReferenciaProveedor2';

//Referencias Cargadas
$route['ApiGalery/saveTelefonoReferenciaFamiliar']['POST'] = 'api/ApiGalery/saveTelefonoReferenciaFamiliar';
$route['ApiGalery/saveTelefonoReferenciaLaboral']['POST'] = 'api/ApiGalery/saveTelefonoReferenciaLaboral';
$route['ApiGalery/updateTelefonoReferenciaLaboral']['POST'] = 'api/ApiGalery/updateTelefonoReferenciaLaboral';
$route['ApiGalery/updateTelefonoReferenciaFamiliar']['POST'] = 'api/ApiGalery/updateTelefonoReferenciaFamiliar';
$route['ApiGalery/registrarFamiliar']['POST'] = 'api/ApiGalery/registrarFamiliar';
$route['ApiGalery/editarFamiliar']['POST']    = 'api/ApiGalery/editarFamiliar';
$route['ApiGalery/getReferenciaId']['POST']    = 'api/ApiGalery/getReferenciaId';

// Prestamos
$route['api/prestamos/tablePrestamosPagar']['POST'] = 'api/ApiPrestamo/tablePrestamosPagar';
$route['api/prestamos/procesar']['POST'] = 'api/ApiPrestamo/procesar';
$route['api/prestamos/rechazar']['POST'] = 'api/ApiPrestamo/rechazar';
$route['api/prestamos/posponerPago']['POST'] = 'api/ApiPrestamo/posponerPago';
$route['api/prestamos/posponerPago']['POST'] = 'api/ApiPrestamo/pagarPrestamo';

$route['api/prestamos/registrarPayValida']['POST'] = 'api/ApiPrestamo/registrarPayValida';
$route['api/prestamos/consultarPayValida']['POST'] = 'api/ApiPrestamo/consultarPayValida';
$route['api/prestamos/actualizarPayValida']['POST'] = 'api/ApiPrestamo/actualizarPayValida';
$route['api/prestamos/eliminarPayValida']['POST'] = 'api/ApiPrestamo/eliminarPayValida';

// Operaciones Beneficiario
$route['api/Apibeneficiario/actualizar_beneficiario']['POST']   = 'api/ApiBeneficiario/actualizarBeneficiario';
$route['api/Apibeneficiario/tabla_beneficiarios']['POST']       = 'api/apiBeneficiario/tablaBeneficiarios';
$route['api/Apibeneficiario/cambio_estado']['POST']             = 'api/apiBeneficiario/cambioEstado';
$route['api/Apibeneficiario/guardar_rubro']['POST']             = 'api/apiBeneficiario/guardarRubroBeneficiario';
$route['api/Apibeneficiario/forma_pago']['POST']                = 'api/apiBeneficiario/guardarFormaPago';
$route['api/ApiBeneficiario/registro_beneficiario']['POST']     = 'api/apiBeneficiario/registroBeneficiario';
$route['api/Apibeneficiario/cargar_beneficiario']['POST']       = 'api/apiBeneficiario/cargarBeneficiario';
$route['api/Apibeneficiario/guardar_tipo_beneficiario']['POST'] = 'api/apiBeneficiario/guardarTipobeneficiario';
$route['api/Apibeneficiario/tipo_documento']['POST']            = 'api/apiBeneficiario/guardarTipoDocumento';
$route['api/Apibeneficiario/guardar_moneda']['POST']            = 'api/apiBeneficiario/guardarMoneda';

// Operaciones Gasto
$route['api/ApiGastos/tabla_gastos']['POST'] = 'api/ApiGastos/tablaGastos';
$route['api/ApiGastos/tabla_gastos_busqueda']['POST'] = 'api/ApiGastos/tablaGastosSearch';

$route['api/ApiGastos/updateProcesarGasto']['POST'] = 'api/ApiGastos/updateProcesarGasto';
$route['api/ApiGastos/tabla_estados']['POST'] = 'api/ApiGastos/tablaEstados';
$route['api/ApiGastos/tabla_gastos_pendientes']['POST'] = 'api/ApiGastos/tablaGastosPendientes';
$route['api/ApiGastos/actualizar_gasto']['POST'] = 'api/ApiGastos/actualizarGasto';
$route['api/ApiGastos/actualiza_estado_gasto']['POST'] = 'api/ApiGastos/actualizaEstadoGasto';
$route['api/ApiGastos/comprobante_procesar_gasto']['POST'] ='api/ApiGastos/comprobanteProcesarGasto';
$route['api/ApiGastos/anular_gasto']['POST'] = 'api/ApiGastos/anularGasto';
$route['api/ApiGastos/cargar_gasto']['POST'] = 'api/ApiGastos/cargarGasto';
$route['api/ApiGastos/cargar_gasto_pendiente']['POST'] = 'api/ApiGastos/cargarGastoPendiente';
$route['api/ApiGastos/guardar_gasto']['POST'] = 'api/ApiGastos/guardarTipoGasto';
$route['api/ApiGastos/guardar_clase_gasto']['POST'] = 'api/ApiGastos/guardarClaseGasto';
$route['api/ApiGastos/guardar_descripcion_gasto']['POST'] = 'api/ApiGastos/guardarDescripcionGasto';
$route['api/ApiGastos/detalle_beneficiario']['POST'] = 'api/ApiGastos/detalleBeneficiario';
$route['api/ApiGastos/verificar_factura']['POST'] = 'api/ApiGastos/verificarFactura';
$route['api/ApiGastos/rellenar_descripcion']['POST'] = 'api/ApiGastos/rellenarDescripcion';
$route['api/ApiGastos/registro_gastos']['POST'] = 'api/ApiGastos/registroGastos';

// Sendgrid mail
$route['EnviarMailCampania']['POST'] = 'EnviarMailCampania/EnvioMailVerificacion/$id_solicitud';

// Infobip sms
$route['EnviarSms']['POST'] = 'EnviarSms/EnviarSmsVerificar/$id_solicitud';

// Pagare Uanataca
$route['api/pagare/uanataca/crear/(:num)']['POST'] = 'api/pagare/ApiUanataca/create/$1';
$route['api/pagare/uanataca/reenviar/(:num)']['POST'] = 'api/pagare/ApiUanataca/reenviar/$1';
$route['api/pagare/uanataca/reenviar/']['POST'] = 'api/pagare/ApiUanataca/reenviar/$1';
$route['api/pagare/uanataca/codigo']['POST'] = 'api/pagare/ApigoUanataca/generarCodigoPagare';
// $route['api/uanataca/pagare/anular']['POST'] = 'api/ApiUanataca/anular_pagare';
// $route['api/uanataca/pagare/reenviar/(:num)']['GET'] = 'api/ApiUanataca/reenviar_pagare/$1';

//Twilio SMS
// Envio SMS a un cliente.
$route['api/enviar_sms']['POST'] = 'api/ApiTwilio/send_sms/$1';
$route['api/send_sms_token_login/(:any)/(:any)']['GET'] = 'api/ApiTwilio/send_sms_token_login/$1/$2';
// Envio SMS por codigo validacion por id_solicitud.
//$route['api/enviar_sms/codigo_validacion/(:num)']['GET'] = 'api/ApiTwilio/send_sms_validation/$1';
$route['api/enviar_sms/codigo_validacion/(:num)']['GET'] = 'api/ApiSolicitud/send_sms_validation/$1';
// Envio ivr por codigo validacion por id_solicitud.
$route['api/enviar_ivr/codigo_validacion/(:num)']['GET'] = 'api/ApiSolicitud/send_ivr_validation/$1';
// Envio SMS credito aprobado.
$route['api/enviar_sms/credito_aprobado/(:num)']['GET'] = 'api/ApiTwilio/send_sms_credito_aprobado/$1';
// Masivo con el mismo mensanje.
$route['api/enviar_sms/masivo']['POST'] = 'api/ApiTwilio/send_sms_massive_same_msg';
// Masivo con distintos mensajes por numero.
$route['api/enviar_sms/masivo/personalizado']['POST'] = 'api/ApiTwilio/send_sms_massive_distinct_msg';

//SendMail
// Envio SMS por codigo validacion por id_solicitud.
$route['api/enviar_email/codigo_validacion/(:num)']['GET'] = 'api/ApiSendMail/send_email_validation/$1';
$route['api/condicion_desembolso/recalcular']['POST'] = 'api/ApiCondicionDesembolso/recalcular';
// Desbloquear el usuario a travez del mail
$route['api/desbloquear_usuario']['POST'] = 'api/ApiSendMail/desbloquear_usuario';

// Rutas Modulo Auditoria Originación y Cobranzas
$route['api/auditoria/llamadas']['GET'] = 'api/ApiAuditoria/auditarLlamadas';
$route['api/auditoria/mis_auditorias']['GET'] = 'api/ApiAuditoria/misAuditorias';

$route['api/auditoria/llamada_auditada']['POST'] = 'api/ApiAuditoria/audioAuditado';
$route['audios_llamadas']['POST'] = 'api/ApiAuditoria/getInfoAudio';
$route['operadores_select/(:any)']['GET'] = 'api/ApiAuditoria/getOperadores/$1';
$route['api/auditoria/upload_file/(:num)']['POST'] = 'api/ApiAuditoria/uploadFile/$1';
$route['auditoria_audio_reportar']['POST']  = 'api/ApiAuditoria/saveAudioReportado';
$route['get_all_calificaciones']['GET']  = 'api/ApiAuditoria/getAllCalificaciones';

// CAMPAÑAS
//MORA
$route['EnviarSmsCampania']['GET'] = 'EnviarSmsCampania/EnviarSmsMora';
//RETANQUEO
$route['EnviarSmsCampania']['GET'] = 'EnviarSmsCampania/EnviarSmsRetanqueo';
//DESEMBOLSO VERIFICADO
$route['EnviarSmsCampania']['GET'] = 'EnviarSmsCampania/EnviarSmsDesembolsoVerificado';
//DESEMBOLSO VALIDADO
$route['EnviarSmsCampania']['GET'] = 'EnviarSmsCampania/EnviarSmsDesembolsoValidado';



//COMUNICACIONES
//BUSCAR CAMPAÑAS POR CENTRAL
$route['ApiBuscaCampanias']['POST']         = 'api/ApiSupervisores/get_campanias_for_central';
$route['ApiBuscaPlantillas']['POST']         = 'api/ApiSupervisores/get_plantillas_for_central';
$route['ApiBuscaCriterios']['POST']         = 'api/ApiSupervisores/get_criterios_for_central';
$route['ApiBuscaLogicas']['POST']         = 'api/ApiSupervisores/get_logicas';
$route['ApiBuscaOperadores']['POST']         = 'api/ApiSupervisores/get_operadores_for_central';
$route['ApiSaveWappTemplateId']['POST']         = 'api/ApiSupervisores/saveWappTemplateId';
$route['ApiSearchTypeLogic']['POST']         = 'api/ApiSupervisores/searchTypeLogic';
$route['ApiGetWhatsappTemplatesByCampaignId']['POST']         = 'api/ApiSupervisores/getWhatsappTemplatesByCampaignId';
$route['ApiDeleteWhatsappTemplateById']['POST']         = 'api/ApiSupervisores/deleteWhatsappTemplateById';
$route['ApiGetWhatsappTemplateById']['POST']         = 'api/ApiSupervisores/getWhatsappTemplateById';
$route['ApiDataMostrar']['POST']         = 'api/ApiSupervisores/dataMostrar';


//AUDITORIA INTERNA

$route['ApiAuditoriaInterna']['POST']	= 'api/ApiAuditoriaInterna/search';
$route['ApiAuditoriaInterna/solicitudes/noAuditadas']['POST']	= 'api/ApiAuditoriaInterna/searchSolicitudesPosterior';
$route['ApiAuditoriaInterna/get_llamadas_por_auditar/(:num)']['GET']	= 'api/ApiAuditoriaInterna/get_llamadas_por_auditar/$1';
$route['ApiAuditoriaInterna/getAuditadas/(:num)']['GET'] = 'api/ApiAuditoriaInterna/getAuditoriasRealizadas/$1';
$route['ApiAuditoriaInterna/actualizarAuditoria/(:num)']['POST'] = 'api/ApiAuditoriaInterna/ActualizarAuditoria/$1';
$route['ApiAuditoriaInterna/getLlamadasPorAuditar/(:num)']['GET']	= 'api/ApiAuditoriaInterna/getLlamadasPorAuditar/$1';
$route['ApiAuditoriaInterna/getAuditadasPosterior/(:num)']['GET'] = 'api/ApiAuditoriaInterna/getAuditoriaAudioPosterior/$1';


// REPORTES
// Indicadores
$route['api/reportes/solicitudes/indicadores']['POST']         = 'api/reportes/ApiReporteIndicadores/indicadores';
$route['api/reportes/solicitudes/indicadores/excel/(:any)/(:any)/(:any)/(:any)/(:any)']['GET']         = 'api/reportes/ApiReporteIndicadores/excel/$1/$2/$3/$4/$5';
// solicitudesgestion
$route['api/reportes/solicitudes/gestion']['POST']         = 'api/reportes/ApiReporteGestion/gestion';
$route['api/reportes/solicitudes/gestion/excel/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)']['GET']  = 'api/reportes/ApiReporteGestion/excel/$1/$2/$3/$4/$5/$6/$7/$8/$9';

/****************************************************************************/
/* Auditoria Sthiven Garcia                                                 */
/****************************************************************************/
$route['auditoria']['GET'] 	= 'auditoria/Auditoria';
$route['auditoria/indicadores']['GET']	= 'auditoria/Auditoria/index/$1';


/***************************************************************************/
// FIN APIS
/***************************************************************************/

$route['solicitud/(:num)/(:any)']['GET']			= 'atencion_cliente/Gestion/solicitud/$1/$2';
$route['getAgendaOperadores/(:num)']['GET']             = 'atencion_cliente/Gestion/getAgendaOperadores/$1';
$route['getSolicitudAjustes/(:num)']['GET']             = 'atencion_cliente/Gestion/getSolicitudAjustes/$1';
$route['deleteAgendaOperador/(:num)']['GET']             = 'atencion_cliente/Gestion/deleteAgendaOperador/$l';
$route['solicitud/gestion/track/(:num)']['GET']  = 'gestion/Tracker/index/$1';
$route['solicitud/gestion/whatsapp/(:num)']['GET']  = 'gestion/WhatsApp/index/$1';  //ORIGINAL
$route['solicitud/gestion/whatsapp/(:num)/(:num)']['GET']  = 'gestion/WhatsApp/whatsapp/$1/$2';//OPTIMIZACION
$route['solicitud/gestion/whatsapp_paginado/(:num)/(:num)/(:num)']['GET']  = 'gestion/WhatsApp/whatsapp_paginado/$1/$2/$3';//mensajes paginados carga de sde controlador
$route['solicitud/gestion/api/whatsapp_paginado']['POST']  = 'gestion/ApiWhatsApp/get_mensajes_paginados';//mensajes paginados para carga dinamica

$route['solicitud/gestion/api/gestion/marcacion/(:num)']['GET']  = 'gestion/ApiGestion/get_detalle_marcacion/$1';
$route['solicitud/gestion/api/gestion/imagenes/(:num)']['GET']  = 'gestion/ApiGestion/get_imagenes/$1';

$route['enlazarAudiosApi']['POST']  = 'gestion/ApiGestion/enlace_audios_neotell';


$route['mensaje/maker/(:num)/(:num)']['GET']   = 'gestion/ApiWhatsApp/mensaje_maker/$1/$2';


/***************************************************************************/
// INICIO VISTAS
/***************************************************************************/
	/***************************************************************************/
	// INICIO Modulos
	/***************************************************************************/
	// MODULO: Dashboard
	// URI : /
	// AUTOR: Rafael 
	$route['dashboard']['GET'] = 'Dashboard';
	// MODULO: Gestion Comercial
	// URI: atencion_cliente/atencionCliente
	// AUTOR: Diego Romero
	$route['atencion_cliente/atencionCliente']['GET'] 		 	= 'atencion_cliente/Gestion';
	$route['atencion_cliente/gestionSinComunicacion']['GET'] 		 	= 'atencion_cliente/Gestion';
	$route['atencion_cliente/atencionCliente/(:num)']['GET']	= 'atencion_cliente/Gestion/index/$1';
	$route['atencion_cliente/imagesDocumentos/(:num)']['GET']	= 'atencion_cliente/Gestion/get_images_documentos/$1';
	$route['atencion_cliente/imagenesArchivos/(:num)']['GET']		= 'atencion_cliente/Gestion/get_images_archivos/$1';
	$route['atencion_cliente/imagenesGaleria/(:num)']['GET']		= 'atencion_cliente/Gestion/get_images_box_galery/$1';
	$route['atencion_cliente/get_creditos/(:num)']['GET']		= 'atencion_cliente/Gestion/get_credits/$1';
	$route['atencion_cliente/get_metricas/(:num)']['GET']		= 'atencion_cliente/Gestion/get_metricas/$1';
	$route['atencion_cliente/get_title/(:num)']['GET']		= 'atencion_cliente/Gestion/get_title/$1';
	$route['atencion_cliente/get_datos_contacto/(:num)']['GET']		= 'atencion_cliente/Gestion/get_datos_contacto/$1';
	$route['chat/open_client_case/(:num)']['GET']  = 'atencion_cliente/Gestion/open_client_case/$1';
	$route['atencion_cliente/agendaTelefonica/(:num)']['GET']  = 'atencion_cliente/Gestion/get_agenda_telefonica/$1';
	$route['atencion_cliente/makeTemplateSend/(:num)/(:num)/(:any)']['GET']	= 'atencion_cliente/Gestion/make_template_send/$1/$2/$3';
	$route['atencion_cliente/agendaMail/(:num)']['GET']  = 'atencion_cliente/Gestion/get_agenda_mail/$1';
	$route['atencion_cliente/agendaMailTemplate']['POST']  = 'atencion_cliente/Gestion/get_mail_template';
	$route['atencion_cliente/agendaMailTemplateHtml']['POST']  = 'atencion_cliente/Gestion/get_mail_template_html/';
	$route['atencion_cliente/get_inflaboral/(:num)']['GET']  = 'api/ApiSituacionLaboral/get_informacion_laboral/$1';
	$route['atencion_cliente/get_inflaboralE']['POST']  = 'api/ApiSituacionLaboral/get_informacion_laboralE';
	$route['atencion_cliente/get_inflaboralArus']['POST']  = 'api/ApiSituacionLaboral/get_informacion_laboralArus';
	$route['atencion_cliente/get_inflaboralMareigua']['POST']  = 'api/ApiSituacionLaboral/get_informacion_laboralMareigua';
	$route['atencion_cliente/update_inflaboral']['POST']  = 'api/ApiSituacionLaboral/update_inflaboral';
	$route['atencion_cliente/get_tipoajustes/(:num)']['GET']  = 'atencion_cliente/Gestion/get_tipo_ajustes/$1';
	$route['atencion_cliente/saveSolajustes']['POST']  = 'atencion_cliente/Gestion/save_sol_ajustes';
	$route['atencion_cliente/updateSolajustes']['POST']  = 'atencion_cliente/Gestion/update_sol_ajustes';
	$route['atencion_cliente/get_solAjustes/(:num)']['GET']  = 'atencion_cliente/Gestion/get_solicitud_ajustes/$1';
	$route['atencion_cliente/update_verifygalery']['POST']	= 'atencion_cliente/Gestion/updateverificacion_galery';
	$route['atencion_cliente/getverifygalery/(:num)']['GET']	= 'atencion_cliente/Gestion/getverificacion_galery/$1';
	$route['api/getwhatsapp_scan/(:num)']['GET']	= 'gestion/ApiGestion/getwhatsapp_scans/$1';

	$route['getInfoAudio']['POST']  = 'gestion/ApiGestion/getInfoAudio';
	$route['audio_reportar']['POST']  = 'gestion/ApiGestion/saveAudioReportado';
	
	$route['atencion_cliente/getverifywhatsapp']['POST']	= 'atencion_cliente/Gestion/getverificacion_Whatsapp';
	$route['atencion_cliente/validacion_biometria_whatsapp']['POST']	= 'atencion_cliente/Gestion/validacion_biometria_whatsapp';
	$route['atencion_cliente/change_type_image']['POST']	= 'atencion_cliente/Gestion/change_type_image';
	

	$route['casosPorVisar']['GET'] 		 	= 'api/ApiSolicitud/casosPorVisar';

	$route['video_llamadas/get_token']['POST']	= 'api/ApiVideoCall/get_token_v2';
	$route['video_llamadas/get_status_videoCall']['POST'] = 'api/ApiVideoCall/get_status_videoCall';
	$route['video_llamadas/cierre']['POST'] = 'api/ApiVideoCall/cerrar_llamada';
	$route['video_llamadas/enviar_link']['POST'] = 'api/ApiVideoCall/send_msj';
	$route['video_llamadas/update_video']['POST'] = 'api/ApiVideoCall/update_video';
	$route['video_llamadas/get_video_status/(:num)']['GET'] = 'api/ApiVideoCall/get_video_status/$1';
	

	// MODULO: Operaciones
	// URI : /operaciones
	// AUTOR: Betzabeth alvarez
	$route['operaciones']['GET'] 	= 'operaciones/Operaciones';
	$route['operaciones/(:num)']['GET']	= 'operaciones/Operaciones/index/$1';
	$route['operadores']['GET'] 	= 'operadores/Operadores';
	$route['operadores/(:num)']['GET']	= 'operadores/Operadores/index/$1';

	// MODULO: Cobranzas
	// URI : /atencion_cobranzas/cobranzas
	// AUTOR: Betzabeth alvarez
	$route['ajustes']['GET'] 	= 'ajustes/Ajustes';
	$route['ajustes/get_ajustes']['GET'] 	= 'ajustes/Ajustes/get_table_ajustes';


	// MODULO: Tablero de operadores
	// URI : /tablero
	// AUTOR: Betzabeth alvarez
	$route['tablero']['GET'] 	= 'tablero/Tablero';
	$route['tablero/indicadores']['GET']	= 'tablero/Tablero/index/$1';

	// MODULO: Tablero de mora
	// URI : /mora
	// AUTOR: Leonel Vincent
	$route['mora']['GET'] 	= 'mora/Tablero';
	$route['mora/indicadores_mora']['GET']	= 'mora/Tablero/index/$1';
	$route['mora/evolucion']['GET']	= 'mora/Evolucion';
	$route['mora/evolucion/grafica']['POST']	= 'mora/Evolucion/grafica';

	// MODULO: Auditoria
	// URI : /auditoria
	// AUTOR: Sthiven Garcia
	$route['auditoria']['GET'] 	= 'auditoria/Auditoria';
	$route['auditoria/indicadores']['GET']	= 'auditoria/Auditoria/index/$1';

	// MODULO: Auditoria Originación y Cobranzas
	// URI : /auditoria_originacion_cobranza
	// AUTOR: Camilo Franco
	$route['auditoria_originacion_cobranza/auditoria']['GET'] 	= 'auditoria_originacion_cobranza/Auditoria';
	$route['auditoria_gestion_operador']['POST']			= 'api/ApiAuditoria/auditoriaGestionOperadorOriginacion';
	$route['form_auditar_llamado/(:num)']['GET'] = 'auditoria_originacion_cobranza/Auditoria/formAuditarLlamado/$1';
	$route['auditoria_detalle']['POST']    = 'api/ApiAuditoria/detalleAuditoria';

	// MODULO: Administracion
	// URI : /administracion
	// AUTOR: Sabrina Basteiro
	$route['administracion']['GET'] 	= 'administracion/Administracion';
	$route['administracion/(:num)']['GET']	= 'administracion/Administracion/index/$1';

	// MODULO: Login
	// URI : /login
	// AUTOR: Diego Romero	
	$route['login']['POST'] = 'Login';
	$route['login']['GET'] = 'Login';
	$route['login/login']['POST'] = 'api/ApiLogin/login';
	$route['login/verificacion/(:any)']['get'] = 'api/ApiLogin/verificar_token/$1';
	$route['login/reenviar']['get'] = 'api/ApiLogin/regenerar_token/';
	
	$route['logout']['GET'] = 'Login/logout';
	$route['veriff_token']['GET'] = 'VeriffToken';

	// MODULO: Tesoreria
	// URI : /tesoreria
	$route['tesoreria']['GET'] = 'tesoreria/Tesoreria';
	$route['api/ApiPrestamo/tableProcesarGasto']['POST'] = 'api/ApiPrestamo/tableProcesarGasto';
	$route['api/ApiGastos/actualizaDesembolsoValidado/(:num)']['POST'] = 'api/ApiGastos/actualizaDesembolsoValidado/$1';
	$route['api/ApiGastos/uploadComprobanteValidado/(:num)']['POST'] = 'api/ApiGastos/uploadComprobanteValidado/$1';
	$route['api/ApiGastos/cantValidarPendientes']['GET'] = 'api/ApiGastos/getCantValidarPendientes';
	$route['api/ApiGastos/searchSolicitud/(:num)']['GET'] = 'api/ApiGastos/getSolicitudSearch/$1';
	$route['api/ApiGastos/cantImputacionesPendientes']['GET'] = 'api/ApiGastos/getCantImputadasPendientes';
	$route['api/ApiPrestamo/consultarBancos']['GET'] = 'api/ApiPrestamo/consultar_bancos_desembolso/$1/$2';
	$route['api/ApiPrestamo/consultarBancos/(:num)/(:num)']['GET'] = 'api/ApiPrestamo/consultar_bancos_desembolso/$1/$2';
	$route['api/ApiPrestamo/generarDesembolso']['POST'] = 'api/ApiPrestamo/generar_archivos_desembolso';
	
	// MODULO: Cobranzas
	// URI : /atencion_cobranzas/cobranzas
	// AUTOR: Betzabeth alvarez
	$route['atencion_cobranzas/cobranzas']['GET'] 	= 'atencion_cobranzas/Cobranzas';
	//Renderizacion de la vista para ejecucion automatica y componente de llamadas Esthiven garcia
	$route['atencion_cobranzas/renderCobranzas']['GET'] 	= 'atencion_cobranzas/Cobranzas/render_cobranzas';
	$route['atencion_cliente/renderGestion']['GET'] 	= 'atencion_cliente/Gestion';

	// MODULO: Supervisor Cobranzas
	// URI : /supervisor
	// AUTOR: Esthiven Garcia
	$route['supervisor']['GET'] 	= 'supervisores/Supervisores';
	$route['supervisor/(:num)']['GET']	= 'supervisores/Supervisores/index/$1';
	$route['supervisor/msjimputacion']['POST']	= 'supervisores/Supervisores/send_msj_imputacion';

	// MODULO: Supervisor Ventas
	// URI : /supervisor
	// AUTOR: Camilo Franco
	$route['supervisorVentas']['GET'] 	= 'supervisores/vistaSupervisorVentas';
	$route['api/solicitud/configuracionesGestionObligatoria']['GET'] = 'api/ApiSolicitud/configuracionesGestionObligatoria';
	$route['api/solicitud/add_configuracion_solicitud_obligatoria']['POST'] = 'api/ApiSolicitud/add_configuracion_solicitud_obligatoria';
	$route['api/solicitud/update_configuracion_solicitud_obligatoria']['POST'] = 'api/ApiSolicitud/update_configuracion_solicitud_obligatoria';
	$route['api/solicitud/update_estado_configuracion_solicitud_obligatoria']['POST'] = 'api/ApiSolicitud/update_estado_configuracion_solicitud_obligatoria';
	$route['api/operadores/update_configuracion_solicitud_obligatoria']['POST'] = 'api/ApiOperadores/update_configuracion_solicitud_obligatoria';


	// MODULO: Auditoria Interna
	// URI : /auditoria
	// AUTORES: Esthiven Garcia - Betzabeth Albarez
	$route['auditoriaInterna']['GET'] 	= 'auditoria_interna/Auditoria';
	$route['auditoriaInterna/(:num)']['GET']	= 'auditoria_interna/Auditoria/index/$1';

	// MODULO: Reportes
	// URI : /reportes
	// AUTOR: Diego Romero
	$route['reportes']['GET'] = 'reportes/Reportes';

	// MODULO: Tablero Flujo
	// URI : /tablero_flujo
	// AUTOR: Juan Patermina
	$route['tableroFlujo']['GET'] = 'tablero_flujo/TableroFlujo';
	$route['tableroFlujo/graficas']['POST'] = 'api/ApiTableroFlujo/getDataPieBarLine';

	$route['whatsapp']['GET'] = 'whatsapp/Whatsapp';
	$route['whatsapp/getOperadoresCanal']['POST'] = 'gestion/ApiWhatsApp/getOperadoresCanal';
	$route['whatsapp/getOperadorCliente']['POST'] = 'gestion/ApiWhatsApp/getOperadorCliente';
	$route['whatsapp/getOperadorChat']['POST'] = 'gestion/ApiWhatsApp/getOperadorChat';
	$route['whatsapp/getTelTxtDoc']['POST'] = 'gestion/ApiWhatsApp/getTelefonoTextoDocumento';
	$route['whatsapp/getOperadores']['GET'] = 'gestion/ApiWhatsApp/getOperadores';
    $route['cargarChatComponent/(:num)']['GET']  = 'gestion/WhatsApp/cargarChatComponent/$1';//OPTIMIZACION
	
	// MODULO: Chat UAC
	// URI : /chatuac
	// AUTOR: Esthiven Garcia
	$route['chatuac']['GET'] = 'chat_uac/ChatUAC';


	//MODULO: Templates
	//URL:/whatsapp/Templates
	//AUTOR: Alan Rodrigo Taiariol

	$route['whatsapp/Templates']['GET'] = 'whatsapp/Templates';
	$route['whatsapp/Templates/templateList']['GET'] = 'whatsapp/Templates/templateList';
	$route['sendSms']['POST'] = 'api/ApiTemplates/sendSms';
	$route['sendIvr']['POST'] = 'api/ApiTemplates/sendIvr';
	$route['saveTemplate']['POST'] = 'api/ApiTemplates/saveTemplate';

	
	$route['gestiones_marketing/GestionesMarketing']['GET']	= 'gestiones_marketing/GestionesMarketing';
	$route['tableroLeads']['GET'] = 'gestiones_marketing/gestionesMarketing/tableroLeads';
	$route['getSolicitudes']['POST'] = 'gestiones_marketing/gestionesMarketing/getSolicitudes';
	$route['getProviders']['GET'] = 'gestiones_marketing/gestionesMarketing/getProviders';
	
	


	/***************************************************************************/
	// FIN Modulos
	/***************************************************************************/
	/***************************************************************************/
	// INICIO Widget
	/***************************************************************************/
		// Carga la informacion del header de una solicitud. 
		$route['solicitud/gestion/header/(:num)']['GET'] = 'Solicitud/box_header/$1';
		// Carga la informacion del cliente para una solicitud. 
		$route['solicitud/gestion/detalle_titulo/(:num)']['GET'] = 'Solicitud/box_title/$1';
		// Carga la informacion del cliente para una solicitud. 
		$route['solicitud/(:num)']['GET']			= 'Solicitud/box/$1';
		$route['solicitud/(:num)']['GET']			= 'atencion_cliente/Gestion/solicitud/$1';
		// Devuelve el cuadro del track de una gestion
		//$route['solicitud/gestion/track/(:num)']['GET']  = 'gestion/Tracker/box/$1';
		// Devuelve el cuadro de la conversacion de WhatsApp
		//$route['solicitud/gestion/whatsapp/(:num)']['GET']  = 'gestion/WhatsApp/box/$1';
		// Devuelve el cuadro de los datos bancario.
		$route['solicitud/gestion/datos_bancarios/(:num)']['GET']  = 'CuentasBancarias/box/$1';
		// Devuelve el cuadro de los datos de verificacion.
		$route['solicitud/gestion/verificacion/(:num)']['GET']  = 'Solicitud/box_verificacion/$1';
		// Devuelve el cuadro de los datos de un grupo de referencias.
		$route['solicitud/gestion/grupo_referencias/(:num)']['GET']  = 'Solicitud/box_grupo_referencias/$1';

		// Carga la informacion de para el modulo de cobranzas
		$route['credito/(:num)']['GET']	= 'atencion_cobranzas/Cobranzas/credito/$1';
		//Componente para llamadas Esthiven Garcia
		$route['llamada/carga_masiva_campaing']['GET'] 	= 'softphone/Llamada/carga_masiva_campaing';
		$route['buscaCodTipificacion']['POST'] 	= 'softphone/Llamada/searchCodTipificacion';
		$route['ApiUpCrmGestion']['GET'] 	= 'softphone/Llamada/levantaCRMGestion';
	/***************************************************************************/
	// FIN Widget
	/***************************************************************************/
	/*******************************************************************************
	 * TABLERO COBRANZA
	 ******************************************************************************/
	$route['tableroCobranza']['GET']	= 'tablero_cobranza/TableroCobranza';
	$route['api/excel/(:any)/(:any)/(:any)']['GET']	= 'api/ApiTableroCobranza/excel/$1/$2/$3';
	$route['api/tableroCobranza']['POST'] = 'api/ApiTableroCobranza/tableroCobranza';
	$route['api/gestiones_acuerdos']['POST'] = 'api/ApiTableroCobranza/tablero_acuerdos';
/***************************************************************************/
// INICIO Reportes
/***************************************************************************/
	// Solicitudes
	$route['reporte/solicitud/indicadores']['POST']	= 'reportes/Solicitud_Rep/box_indicadores';
	$route['reporte/solicitud/gestion']['GET'] 		= 'reportes/Solicitud_Rep/gestion';
	$route['reporte/solicitud/track']['GET'] 		= 'reportes/Solicitud_Rep/track';

/***************************************************************************/
// REPORTES
$route['reportes/vistaReporteVencimiento']['POST']	= 'reportes/Reportes/vistaReporteVencimiento';
$route['reportes/vistaReporteOriginacion']['POST']	= 'reportes/Reportes/vistaReporteOriginacio';

// FIN Reportes
/***************************************************************************/

$route['solicitud/gestion/sector_financiero_al_dia/(:any)']['GET']	= 'atencion_cliente/Gestion/getSectorFinancieroAlDiaView/$1';




// MODULO: Cobranzas
// URI : /Comunicaciones
// AUTOR: Esthiven Garcia
/***************************************************************************/
// INICIO NEotell
/***************************************************************************/
$route['upCasosNeotell']['GET']	= 'api/Apisupervisores/up_casos_neotell';
$route['checkIfOperatorIsInManualCampaign']['POST']	= 'api/Apisupervisores/checkIfOperatorIsInManualCampaign';

// Esta ruta va siempre al final
$route['(:any)'] = 'app';

// MODULO: Legales
// URI : /Legales
// AUTOR: Alexis Rodriguez
/***************************************************************************/
// INICIO VISTAS Legales
/***************************************************************************/
$route['legales/Legales']['GET']	= 'legales/Legales';
// MODULO: Tablero Originacion
// URI : gestion/tablero_originacion
// AUTOR: Alexis Rodriguez
// INICIO VISTAS Tablero Originacion
/***************************************************************************/
$route['gestion/TableroOriginacion']['GET']	= 'gestion/TableroOriginacion';

// PAYVALIDA

$route['api/prestamos/registrarPayvalidaPago']['POST']	= 'api/ApiPrestamo/registrarPayvalidaPago';

$route['api/evento/save']['POST']	= 'api/events/ApiEvents/save';
$route['api/evento/delete']['POST']	= 'api/events/ApiEvents/delete';
$route['api/evento/enable']['POST']	= 'api/events/ApiEvents/enable';
$route['api/evento/disable']['POST']	= 'api/events/ApiEvents/disable';
$route['api/evento/get']['POST']	= 'api/events/ApiEvents/get';
$route['api/evento/getAll']['POST']	= 'api/events/ApiEvents/getAll';
$route['api/evento/getAllOrigin']['POST']	= 'api/events/ApiEvents/getAllOrigin';

/***************************************************************************/
// GESTOR DE VALIABLES
// AUTOR: Alexis Rodriguez
$route['AdminSistemas']['POST']	= 'admin_sistemas/AdminSistemas';
$route['admin_sistemas/tableVariables']['POST']	= 'admin_sistemas/AdminSistemas/tableVariables';

$route['admin_sistemas/obtenerBases']['POST']	= 'admin_sistemas/AdminSistemas/obtenerBases';
$route['admin_sistemas/obtenerVariables']['POST']	= 'admin_sistemas/AdminSistemas/obtenerVariables';
$route['admin_sistemas/obtenerTablas']['POST']	= 'admin_sistemas/AdminSistemas/obtenerTablas';
$route['admin_sistemas/obtenerCampos']['POST']	= 'admin_sistemas/AdminSistemas/obtenerCampos';
$route['admin_sistemas/accionesGuardarProbar']['POST']	= 'admin_sistemas/AdminSistemas/accionesGuardarProbar';
$route['admin_sistemas/updateEstado']['POST']	= 'admin_sistemas/AdminSistemas/updateEstado';
$route['admin_sistemas/getDataEditVariable']['POST']	= 'admin_sistemas/AdminSistemas/obtenerDataEditVariable';
$route['admin_sistemas/updateVariable']['POST']	= 'admin_sistemas/AdminSistemas/actualizarVariable';
$route['admin_sistemas/obtenerTipoDato']['POST']	= 'admin_sistemas/AdminSistemas/obtenerTipoDato';
/***************************************************************************/

/***************************************************************************/
$route['cobranzas/search_codigo']['POST']	= 'atencion_cobranzas/Cobranzas/searchCodigoPromocional';
$route['cobranzas/envioCondigoPromocional']['POST']	= 'atencion_cobranzas/Cobranzas/envioCodigoPromocion';

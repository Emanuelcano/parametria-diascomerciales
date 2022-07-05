<?php

use Carbon\Carbon;

defined('BASEPATH') OR exit('No direct script access allowed');

class SolicitudWS extends Orm_model
{
    public $provider;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'solicitud';
        $this->columns   = [
            'id',
            'fecha_alta',
            'canal',
            'paso',
            'id_tipo_documento',
            'documento',
            'fecha_expedicion',
            'fecha_nacimiento',
            'id_departamento',
            'id_localidad',
            'nombres',
            'apellidos',
            'telefono',
            'email',
            'id_situacion_laboral',
            'ingreso_mensual',
            'actividad',
            'actividad_direccion',
            'tipo_solicitud',
            'respuesta_analisis',
            'estado',
            'operador_asignado',
            'id_cliente',
            'id_usuario',
            'id_credito',
            'fecha_ultima_actividad',
            'fecha_envio_sms',
            'cantidad_sms',
            'codigo_enviado_sms',
            'codigo_recibido_sms',
            'fecha_validacion_telefono',
            'validacion_telefono',
            'deleted',
            'fecha_envio_mail',
            'cantidad_mail',
            'codigo_enviado_mail',
            'codigo_recibido_mail',
            'fecha_validacion_mail',
            'validacion_mail',
            'fecha_inicio_reto',
            'fecha_vencimiento_reto',
            'cantidad_preguntas_reto',
            'respuestas_correctas',
            'numero_intento_reto',
            'resultado_ultimo_reto',
            'clientid',
            'gclid',
            'utm_medium',
            'utm_source',
            'utm_campaign',
            'pagare_enviado',
            'fecha_envio_pagare',
            'codigo_firma',
            'pagare_firmado',
            'fecha_firma_pagare',
            'repeticion_ip',
            'id_promocode',
        ];
        $this->dbName    = 'solicitudes';
    }

    /**
     * Columns in the table are:
     * id
     * fecha_alta
     * canal
     * paso
     * id_tipo_documento
     * documento
     * fecha_expedicion
     * fecha_nacimiento
     * id_departamento
     * id_localidad
     * nombres
     * apellidos
     * telefono
     * email
     * id_situacion_laboral
     * ingreso_mensual
     * actividad
     * actividad_direccion
     * tipo_solicitud
     * respuesta_analisis
     * estado
     * operador_asignado
     * id_cliente
     * id_usuario
     * id_credito
     * fecha_ultima_actividad
     * fecha_envio_sms
     * cantidad_sms
     * codigo_enviado_sms
     * codigo_recibido_sms
     * fecha_validacion_telefono
     * validacion_telefono
     * deleted
     * fecha_envio_mail
     * cantidad_mail
     * codigo_enviado_mail
     * codigo_recibido_mail
     * fecha_validacion_mail
     * validacion_mail
     * fecha_inicio_reto
     * fecha_vencimiento_reto
     * cantidad_preguntas_reto
     * respuestas_correctas
     * numero_intento_reto
     * resultado_ultimo_reto
     * clientid
     * gclid
     * utm_medium
     * utm_source
     * utm_campaign
     * pagare_enviado
     * fecha_envio_pagare
     * codigo_firma
     * pagare_firmado
     * fecha_firma_pagare
     * repeticion_ip
     * id_promocode
     */

    public function relationOperator()
    {
        return [
            'chat',
            [
                'operador_asignado' => 'idoperador'
            ],
            'type' => 'one'
        ];
    }

    /**
     * @param string $phoneNumber
     * @return array|bool
     */
    public function findByTelefono(string $phoneNumber)
    {
        //Check by Absences
        if ($phoneNumber !== '') {
            $sql = 'SELECT operador_asignado from solicitudes.solicitud'
                . " where telefono = '" . $phoneNumber . "' and operador_asignado != 0"
                . ' order by fecha_alta DESC limit 1';

            $query = $this->db->query($sql);

            if (!is_bool($query)) {
                return $query->result_array();
            }

            return false;
        }
    }

    /**
     * @param string $phoneNumber
     * @return array|bool
     */
    public function findRequestByTelefono(string $phoneNumber)
    {
        //Check by Absences
        if ($phoneNumber !== '') {
            $sql = 'SELECT solicitud.nombres, solicitud.apellidos, solicitud.documento, solicitud.email'
                . ' from solicitudes.solicitud'
                . " where solicitud.telefono = '" . $phoneNumber . "'"
                . ' order by id DESC limit 1';

            $query = $this->db->query($sql);

            if (!is_bool($query)) {
                return $query->row_array();
            }

            return false;
        }
    }
}

<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

defined('BASEPATH') OR exit('No direct script access allowed');

class OperatorWS extends Orm_model
{
    public $provider;

    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'operadores';
        $this->columns   = [
            'idoperador',
            'id_usuario',
            'nombre_apellido',
            'nombre_pila',
            'avatar',
            'telefono_fijo',
            'extension',
            'wathsapp',
            'mail',
            'short_url_wathsapp',
            'short_url_home',
            'hora_ingreso',
            'hora_salida',
            'estado',
            'tipo_operador',
            'cantidad_asignar'
        ];
        $this->dbName    = 'gestion';
    }

    /**
     * Columns in the table are:
     * idoperador
     * id_usuario
     * nombre_apellido
     * nombre_pila
     * avatar
     * telefono_fijo
     * extension
     * wathsapp
     * mail
     * short_url_wathsapp
     * short_url_home
     * hora_ingreso
     * hora_salida
     * estado
     */

    public function relationChat()
    {
        return [
            'chat',
            [
                'idoperador' => 'id_agente'
            ],
            'type' => 'many'
        ];
    }

    public function relationHours()
    {
        return [
            'horarioOperadores_model',
            [
                'idoperador' => 'idoperador'
            ],
            'type' => 'many'
        ];
    }

    public function relationAbsences()
    {
        return [
            'ausenciasOperadores_model',
            [
                'idoperador' => 'idoperador'
            ],
            'type' => 'many'
        ];
    }

    public function relationStatus()
    {
        return [
            'operatorStatus',
            [
                'idoperador' => 'operador_id'
            ],
            'type' => 'one'
        ];
    }

    public function relationChatActive()
    {
        return [
            'chat',
            [
                'idoperador' => 'id_operador',
                'status_chat' => 'activo'
            ],
            'type' => 'many'
        ];
    }


    public function getStatus(array $operator): string
    {
        //Check by Absences
        if (!empty($operator['ausencias_operadores'])) {
            foreach ($operator['ausencias_operadores'] as $absence) {
                $aStartDate = Carbon::parse($absence['fecha_inicio']);
                $aEndDate   = Carbon::parse($absence['fecha_final']);
                $now        = Carbon::now();
                if ($now->greaterThanOrEqualTo($aStartDate) && $now->lessThanOrEqualTo($aEndDate)) {
                    return 'En ausencia';
                }
            }
        }

        //Check by hours
        if (empty($operator['horario_operadores'])) {
            return 'Sin definir';
        } else {
            $blocks = [];
            foreach ($operator['horario_operadores'] as $block) {
                //Verificación a dos falsos
                $dayNumber = Carbon::now()->dayOfWeek + 1;

                if ((int)$block['cod_dia'] === $dayNumber) {
                    $aStartDate = Carbon::createFromFormat('H:i:s', $block['horario_entrada'], 'America/Bogota');
                    $aEndDate   = Carbon::createFromFormat('H:i:s', $block['horario_salida'], 'America/Bogota');
                    $now        = Carbon::now()->tz('America/Bogota');

                    if ($block['zona_horaria'] === '-3') {
                        $aStartDate = $aStartDate->timezone('America/Argentina/Buenos_Aires');
                        $aEndDate   = $aEndDate->timezone('America/Argentina/Buenos_Aires');
                        $now        = $now->timezone('America/Argentina/Buenos_Aires');
                    }

                    if ($block['libre'] === '1') {
                        $blocks[] = false;
                    } elseif ($now->greaterThanOrEqualTo($aStartDate) && $now->lessThanOrEqualTo($aEndDate)) {
                        $blocks[] = true;
                    } else {
                        $blocks[] = false;
                    }
                }
            }

            if ($blocks[0] === false && $blocks[1] === false) {
                return 'Offline';
            }
        }

        //Check by ping
        if ($operator['estados_operadores'] !== null) {
            if ($operator['estados_operadores']['is_alive_timestamp'] !== null && $operator['estados_operadores']['is_alive_timestamp'] !== '')
            {
                $pingAliveTime = Carbon::createFromFormat('Y-m-d H:i:s', $operator['estados_operadores']['is_alive_timestamp'], 'America/Bogota');
                $now           = Carbon::now()->tz('America/Bogota');

                if ($now->diffInMinutes($pingAliveTime) >= 10) {
                    return 'Inactivo';
                }
            }
        } else {
            return 'Sin definir';
        }

        return 'Online';
    }
}

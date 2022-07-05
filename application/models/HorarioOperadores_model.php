<?php

class HorarioOperadores_model extends Orm_model
{
    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'horario_operadores';
        $this->columns   = [
            'id',
            'idoperador',
            'cod_dia',
            'horario_entrada',
            'horario_salida',
            'zona_horaria',
            'libre'
        ];
        $this->dbName    = 'gestion';
    }

    /**
     * Relation
     *
     * @return array
     */
    public function relationOperator(): array
    {
        return ['operator_model', ['idoperador' => 'idoperador'], 'type' => 'one'];
    }
}

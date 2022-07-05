<?php

class AusenciasOperadores_model extends Orm_model
{
    public function __construct()
    {
        parent::__construct();

        $this->tableName = 'ausencias_operadores';
        $this->columns   = [
            'id',
            'idoperador',
            'fecha_inicio',
            'fecha_final',
            'motivo',
            'fecha_creacion'
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

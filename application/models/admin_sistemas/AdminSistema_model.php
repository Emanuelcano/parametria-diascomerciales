<?php
    class AdminSistema_model extends CI_Model
    {
        public function __construct()
	{
        $this->db_chat = $this->load->database('chat', TRUE);
        $this->db_parametria = $this->load->database('parametria', TRUE);
        $this->db_info = $this->load->database('information_schema', TRUE);
		parent::__construct();

	}

        public function getVariables()
        {
            $variable = $this->db_parametria->select("vm.id, vm.nombre_variable, o.nombre_apellido, vm.fecha_create, vm.estado")
            ->from("variable_mensajeria AS vm")
            ->join("gestion.operadores AS o", "o.idoperador = vm.id_operador")
            ->get()->result_array();

            return $variable;
        }

        public function getBases()
        {
            $base = $this->db_parametria->select("b.base, b.nombre")
            ->from("bases_exist AS b")->get()->result_array();
            
            return $base;
        }

        public function getTables($base)
        {
            $tablas = $this->db_info->select("table_name AS nombre")
            ->from("tables")
            ->where("table_schema", $base["base"])->get()->result_array();
            return $tablas;
        }

        public function getCampos($tabla)
        {
            $columnas = $this->db_info->select("column_name AS nombre")
            ->from("columns")
            ->where("table_schema", $tabla["base"])
            ->where("table_name", $tabla["tabla"])->get()->result_array();
            return $columnas;
        }

        public function getValor($apodo_variable, $base_variable, $tabla_variable, $select, $condiciones)
        {
            $this->$base_variable = $this->load->database($base_variable, TRUE);
            
            $sql = "SELECT $select AS $apodo_variable FROM $base_variable.$tabla_variable WHERE ";
            if (is_array($condiciones)) {
                foreach ($condiciones as $key => $value) {
                    if ($key > 0) {
                        $sql .= " AND $value";
                    }else{
                        $sql .= $value;
                    }
                }
            }else{
                $sql.= $condiciones;
            }
            // var_dump($sql);die;
            $data = $this->$base_variable->query($sql);
            return $data->result_array();
        }

        public function searchVariable($apodo)
        {
            $search = $this->db_parametria->select("v.nombre_variable")
            ->from("variable_mensajeria AS v")
            ->where("v.nombre_variable", $apodo)->get()->result_array();
            if (is_array($search) && empty($search[0])) {
                return true;
            }else{
                return false;
            }
        }

        public function saveVariable($apodo_variable, $base_variable, $tabla_variable, $select, $condiciones, $estado, $slc_filtro, $tipo_variable, $formato_variable, $valor_variable)
        {
            if (is_array($condiciones)) {
                !empty($condiciones[0]) ? $cond1 = $condiciones[0] : $cond1 = null;
                !empty($condiciones[1]) ? $cond2 = $condiciones[1] : $cond2 = null;
                !empty($condiciones[2]) ? $cond3 = $condiciones[2] : $cond3 = null;
            }else{
                $cond1 = $condiciones;
                $cond2 = null;
                $cond3 = null;
            }

            $data = [
                "nombre_variable" => $apodo_variable,
                "select_variable" => $select,
                "base_variable" => $base_variable,
                "tabla_variable" => $tabla_variable,
                "condicion_1" => $cond1,
                "condicion_2" => $cond2,
                "condicion_3" => $cond3,
                "filtro" => $slc_filtro,
                "estado" => $estado,
                "tipo" => $tipo_variable,
                "formato" => $formato_variable,
                "valor" => $valor_variable,
                "fecha_create" => date("Y-m-d H:i:s"),
                "id_operador" => $this->session->userdata('idoperador'),
                "fecha_update" => date("Y-m-d H:i:s")
            ];
            $insert = $this->db_parametria->insert("variable_mensajeria", $data);
            if ($insert == true) {
                return true;
            } else {
                return false;
            }
        }

        public function getEstado($id_variable)
        {
            $estado = $this->db_parametria->select("v.estado")
            ->from("variable_mensajeria AS v")
            ->where("id", $id_variable)->get()->result_array();
            return $estado;
        }

        public function update_estado($id_variable, $estadoCambiar)
        {
            $update = $this->db_parametria->set('estado',$estadoCambiar)
            ->where('id',$id_variable)
            ->update('variable_mensajeria');

            if($this->db_parametria->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        }
        

        public function getDataUpdate($id)
        {
            $data = $this->db_parametria->select("*")
            ->from("variable_mensajeria")
            ->where("id", $id)->get()->result_array();

            return $data;
        }

        public function updateVariable($id, $nombre, $base, $tabla, $campo, $condiciones, $filtro, $estado, $tipo, $formato, $valor = null)
        {
            // var_dump($condiciones);die;
            $update = $this->db_parametria->set("nombre_variable", $nombre)
            ->set("select_variable", $campo)
            ->set("base_variable", $base)
            ->set("tabla_variable", $tabla)
            ->set("condicion_1", $condiciones[0])
            ->set("condicion_2", $condiciones[1])
            ->set("condicion_3", $condiciones[2])
            ->set("estado", $estado)
            ->set("filtro", $filtro)
            ->set("tipo", $tipo)
            ->set("formato", $formato)
            ->set("valor", $valor)
            ->set("id_operador", $this->session->userdata('idoperador'))
            ->set("fecha_update", date("Y-m-d H:i:s"))
            ->where("id", $id)
            ->update("variable_mensajeria");

            if($this->db_parametria->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        }

        public function get_tipo_dato($base, $table, $columna)
        {
            $dato = $this->db_info->select("DATA_TYPE AS tipo_dato")
            ->from("columns")
            ->where("table_schema", $base)
            ->where("table_name", $table)
            ->where("COLUMN_NAME", $columna)->get()->result_array();
            return $dato;
        }
    }
    
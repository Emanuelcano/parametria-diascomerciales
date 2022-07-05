<?php
class Chatbot_model extends CI_Model {

     public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('chatbot', true);
        $this->db_chat = $this->load->database('chat', true);
        $this->db_gestion = $this->load->database('gestion', true);
        $this->db_solicitudes = $this->load->database('solicitudes', true);
        $this->db_maestro = $this->load->database('maestro', true);
    }

    /*
    * SE VERIFICA SI ESTA EN EL RANGO DE LA TABLA DE OPERADORES AUSENTES
    */
    private function ExisteEnRango($result_db_ausencias, $fecha_actual){
        $IdExiste = [];

        foreach ($result_db_ausencias as $key => $value) {
        
            $fecha_inicio = $value->inicio; 
            $fecha_fin = $value->final;
            $fecha = $fecha_actual;

            if ($this->check_in_range($fecha_inicio, $fecha_fin, $fecha))
            {
                array_push($IdExiste, (int)$value->idoperador);
            }
        }

        if (count($IdExiste) > 0) {
            return $IdExiste;           
        }
        else{
            return FALSE;
        }
    }

    public function activedChatbot($id)
    {
        $this->db->select('bot_status');
        $this->db->from('chatbot_config');
        $this->db->where("id", $id);
        $this->db->where("bot_status", 1);
        $query = $this->db->get();

        $result = ($query!==false && $query->num_rows()) ? true : false;

        return $result;
    }

    public function NumGestion($id_chat)
    {
        $this->db_chat->select('to,documento');
        $this->db_chat->from('new_chats');
        $this->db_chat->where('id', $id_chat);
        $this->db_chat->limit(1);

        $query = $this->db_chat->get();

        $result = ($query!==FALSE && $query->num_rows() > 0) ? $query->row() : FALSE;

        return $result;
    }

    private function OperatorsAusents($fecha_actual)
    {
        $this->db_gestion->select("idoperador, SUBSTRING_INDEX(MAX(fecha_inicio),' ', 1) AS inicio, SUBSTRING_INDEX(MAX(fecha_final),' ', 1) AS final");
        $this->db_gestion->from('ausencias_operadores');
        $this->db_gestion->group_by('idoperador');
        $query_ausencias = $this->db_gestion->get();
        $result_db_ausencias = ($query_ausencias!==FALSE && $query_ausencias->num_rows() > 0) ? $query_ausencias->result() : FALSE;

        if ($result_db_ausencias !== FALSE) {
            $Ids = $this->ExisteEnRango($result_db_ausencias, $fecha_actual);
            return $Ids;
        }else{
            return $result_db_ausencias;
        }

    }

    private function check_in_range($fecha_inicio, $fecha_fin, $fecha){

    $fecha_inicio = strtotime($fecha_inicio);
    $fecha_fin = strtotime($fecha_fin);
    $fecha = strtotime($fecha);

        if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {

         return TRUE;

        } else {

         return FALSE;

        }
    }

    private function insertAsig($resultAutomatic, $id_chat){

        $data = array(  'id_chat' => $id_chat, 
                        'idoperador' => $resultAutomatic, 
                        'fecha' => date('Y-m-d'),
                        'hora' => date('H:m:s'));

        $this->db_gestion->insert('operadores_asignacion', $data);

    }

    /*
    * SE BUSCAN LOS OPERADORES TIPO QUE ESTEN ACTIVOS PARA PROCEDER A REALIZAR ASIGNACION DE CHATS
    */
    public function SeleccionarOperadores($fecha_actual, $TipOp){

        $OpAus = $this->OperatorsAusents($fecha_actual);
        // var_dump($OpAus);

        $EsPar = (substr($fecha_actual,9,2)%2 == 0) ? 1 : 0;

        if ($OpAus) {
            $this->db_gestion->select('idoperador,tipo_operador');
            $this->db_gestion->from('operadores op');
            $this->db_gestion->where('op.estado', 1);
			// $this->db_gestion->where('op.estado', 1);
            $this->db_gestion->where_in('op.tipo_operador', $TipOp);
            $this->db_gestion->where_not_in('op.idoperador', $OpAus);

            if ($EsPar) {
                $this->db_gestion->order_by('op.idoperador', 'ASC');
            }else{
                $this->db_gestion->order_by('op.idoperador', 'DESC');
            }        
            $query_gestion = $this->db_gestion->get();

            $result_db_gestion = ($query_gestion!==FALSE && $query_gestion->num_rows() > 0) ? $query_gestion->result() : FALSE;

            return $result_db_gestion;          
        }
        else{
            $this->db_gestion->select('idoperador,tipo_operador');
            $this->db_gestion->from('operadores op');
            $this->db_gestion->where('op.estado', 1);
			// $this->db_gestion->where('op.estado', 1);
            $this->db_gestion->where_in('op.tipo_operador', $TipOp);      
            if ($EsPar) {
                $this->db_gestion->order_by('op.idoperador', 'ASC');                
            }else{
                $this->db_gestion->order_by('op.idoperador', 'DESC');
            }
            $query_gestion = $this->db_gestion->get();

            $result_db_gestion = ($query_gestion!==FALSE && $query_gestion->num_rows() > 0) ? $query_gestion->result() : FALSE;

            return $result_db_gestion;
        }
    }

    public function minChat($data, $id_chat){
        $result = '';

        foreach ($data as $key => $value) {
          $result .= "$value->idoperador,";
        }
        $IdAsig = rtrim($result,',');
        $sql = "SELECT COUNT(*) AS total, id_operador FROM new_chats WHERE id_operador IN ($IdAsig) AND fecha_ultima_recepcion >= (NOW() - INTERVAL 24 HOUR) AND status_chat = 'activo' GROUP BY id_operador ORDER BY total ASC";
        $query = $this->db_chat->query($sql);

        $resultAutomatic = ($query!==FALSE && $query->num_rows() > 0) ? $query->row('id_operador') : array_pop($data)->idoperador;        
        if ($query->num_rows() < count($data) && $data[0]->tipo_operador == 4) {
            $ops =  explode(',',$IdAsig);
            foreach ($ops as $operador) {
                $sql = "SELECT COUNT(*) AS total, id_operador FROM new_chats WHERE id_operador = $operador AND fecha_ultima_recepcion >= (NOW() - INTERVAL 24 HOUR) AND status_chat = 'activo' GROUP BY id_operador ORDER BY total ASC";
                $consulta = $this->db_chat->query($sql);
                if (empty($consulta->row())) {
                    $resultAutomatic = $operador;
                }
            }
        }
        
        $this->insertAsig($resultAutomatic, $id_chat);

        return $resultAutomatic;
    }
    /*
    * SE VENCEN CHATS PARA GESTION Y COBRANZAS MAYOR A 24HR Y SE ASIGNAN A CHATBOT 
    */
    public function vencerChats(){
        $sql = "UPDATE new_chats SET id_operador = 192, status_chat = 'vencido' WHERE status_chat IN ('activo', 'pendiente', 'revision') AND fecha_ultimo_envio <= (NOW() - INTERVAL 24 HOUR) ORDER BY fecha_ultimo_envio DESC";

        $query = $this->db_chat->query($sql);

        $row = $this->db_chat->affected_rows();

        if ($row > 0) {
                return $row;
        } else {
                return false;
        }
    }

    public function reasignarGestion108(){
        $sql = "SELECT id FROM `new_chats` WHERE `id_operador` = 108 AND `to` = '15140334' AND status_chat = 'activo'";

        $query = $this->db_chat->query($sql);

        $result = ($query!==false && $query->num_rows() > 0) ? $query->result_array() : false;

        return $result;
    }

    public function minChat108($data){

        $result = '';

        foreach ($data as $key => $value) {
          $result .= "$value->idoperador,";
        }
        $IdAsig = rtrim($result,',');

        $sql = "SELECT COUNT(*) AS total, id_operador FROM new_chats WHERE id_operador IN ($IdAsig) AND fecha_ultima_recepcion >= (NOW() - INTERVAL 24 HOUR) AND status_chat = 'activo' GROUP BY id_operador ORDER BY total ASC";

        $query = $this->db_chat->query($sql);

        $resultAutomatic = ($query!==false && $query->num_rows() > 0) ? $query->result_array('id_operador') : false;

        return $resultAutomatic;
    }

    public function UpdateOperator($dataGestion,$minChat108){
        $this->db_chat->set('id_operador', $minChat108);
        $this->db_chat->where('id', $dataGestion);
        $this->db_chat->update('new_chats');

        $row = $this->db_chat->affected_rows();

        if ($row > 0) {
                return $row;
        } else {
                return false;
        }
    }

    /*
    * RETORNA SI EL OPERADOR ESTA ACTIVO, SINO DEVUELVE FALSE
    */
    public function VeriffOperador($idOp, $TipOp){

        if (!empty($idOp) && !is_null($idOp)) {
            $sql = "SELECT op.idoperador AS id_operador FROM operadores AS op WHERE op.idoperador = $idOp AND op.estado = 1 AND op.tipo_operador IN ($TipOp) AND op.idoperador NOT IN (SELECT au.idoperador FROM ausencias_operadores AS au WHERE au.estado = 1 AND au.idoperador = $idOp AND SUBSTRING_INDEX(NOW(),' ', 1) BETWEEN SUBSTRING_INDEX(au.fecha_inicio,' ', 1) AND SUBSTRING_INDEX(au.fecha_final,' ', 1))";
            $query = $this->db_gestion->query($sql);
    
            $result = ($query!==false && $query->num_rows() > 0) ? $query->row('id_operador') : false;
        }else{
            $result = false;
        }
        

        return $result;
    }

    public function ValidateCliente($documento)
    {
        $this->db_maestro->select('id');
        $this->db_maestro->from('clientes');
        $this->db_maestro->where("documento = '$documento'");
        $query =  $this->db_maestro->get();
        return $query->row();
    }

}
?>

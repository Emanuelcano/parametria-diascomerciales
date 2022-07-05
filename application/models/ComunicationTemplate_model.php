<?php
class ComunicationTemplate_model extends Orm_model {
       
    public function __construct(){  
        parent::__construct();      
        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
        $this->db_campania = $this->load->database('campanias', TRUE);
    }

    public function templateList($type) {
        $this->db->select('t.*, o.nombre_apellido');
        $this->db->from('chat.templates t');
        $this->db->join('gestion.operadores o', 'o.idoperador = t.id_operador_creacion', 'left');
        
        $this->db->where('t.tipo_template', $type);
        
        $query = $this->db->get();
        // echo $this->db->last_query(); die;
        $this->Sqlexceptions->checkForError();
        return $query->result_array();
    }
    public function changeStatus($id_template, $data){

        $this->db = $this->load->database('chat', TRUE);
        $this->db->where('id', $id_template);

        $update = $this->db->update('templates', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function templateEmailList() {
        $this->db->select('crt.id_template as id, o.nombre_apellido, cmt.creation_date as creation_date , cmt.nombre_template as nombre, aml.nombre_logica as asunto, cmt.canal as canal, aml.id_logica');
        $this->db->from('campanias.campanias_relacion_templates crt');
        $this->db->join('campanias.campanias_mail_templates cmt', 'cmt.id = crt.id_template', 'inner');
        $this->db->join('campanias.agenda_mail_logica aml', 'aml.mensaje = cmt.id', 'inner');
        $this->db->join('gestion.operadores o', 'o.idoperador = cmt.operador_id', 'left');
        
        $query = $this->db->get();
        //echo $this->db->last_query(); die;
        $this->Sqlexceptions->checkForError();
        return $query->result_array();
    }

    public function getTemplate($template_id) {
        $this->db->select('t.*');
        $this->db->from('chat.templates t');
        $this->db->where('t.id', $template_id);
        
        $query = $this->db->get();
        //echo $this->db->last_query();
        $this->Sqlexceptions->checkForError();
        return $query->result_array();
    }

    public function getEmailTemplate($template_id) {
        $this->db->select('crt.id_template as id, cmt.nombre_template, aml.nombre_logica, cmt.canal as canal, cmt.html_contenido, cmt.arreglo_variables_rplc, aml.query_contenido');
        $this->db->from('campanias.campanias_relacion_templates crt');
        $this->db->join('campanias.campanias_mail_templates cmt', 'cmt.id = crt.id_template', 'inner');
        $this->db->join('campanias.agenda_mail_logica aml', 'aml.mensaje = cmt.id', 'inner');
        $this->db->join('gestion.operadores o', 'o.idoperador = cmt.operador_id', 'left');
        $this->db->where('crt.id_template', $template_id);
        
        $query = $this->db->get();
        // echo $this->db->last_query(); die;
        $this->Sqlexceptions->checkForError();
        return $query->result_array();
    }

    public function update_email_template($data = false, $id = false) {
        
        $this->update_campanias_mail_templates($data['campanias_mail_templates'], $id);
        $this->update_agenda_mail_logica($data['agenda_mail_logica'], $id);
        return true; 
    }

    public function insert_email_template($data)
    {     
        try {
            //inserto data en  solicitudes.agenda_mail_logica,
            $this->db->insert(
                'campanias.agenda_mail_logica',
                $data['agenda_mail_logica']
            );

            //verifico ultimo id generado en solicitudes.agenda_mail_logica
            $id_logica = $this->db->insert_id();
            $data['campanias_relacion_templates']['id_logica'] = $id_logica;  

            //inserto data junto a id_logico en 'solicitudes.campanias_mail_templates',
            $this->db->insert(
                'campanias.campanias_mail_templates',
                $data['campanias_mail_templates']
            );
            
            //inserto data en  'solicitudes.campanias_relacion_templates',
            $this->db->insert(
                'campanias.campanias_relacion_templates',
                $data['campanias_relacion_templates']
            );

            return ["status" => "200"];

        } catch (\Exception $e) {
        
            return ["status" => "Error al insertar template en base de datos"];
        }
    }

    public function update_campanias_mail_templates($data = false, $id_template = false) {

        $this->db = $this->load->database('campanias', TRUE);
        $this->db->where('id', $id_template);

        $update = $this->db->update('campanias_mail_templates', $data);
        $update = $this->db->affected_rows();
        return $update;
    }
    
    public function update_agenda_mail_logica($data = false, $id_template = false) {

        $this->db = $this->load->database('campanias', TRUE);
        $this->db->where('mensaje', $id_template);

        $update = $this->db->update('agenda_mail_logica', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function getTemplateVariablesId($template_id) {
        $this->db->select('vt.id, vt.variable');
        $this->db->from('chat.variables_template vt');
        $this->db->where('vt.id_template', $template_id);
        
        $query = $this->db->get();
        //echo $this->db->last_query();
        $this->Sqlexceptions->checkForError();
        return $query->result_array();
    }

    public function getVariable($template_id, $variable) {
        $this->db->select('vt.id, vt.variable, vt.tipo, vt.campo, vt.condicion, vt.formato');
        $this->db->from('chat.variables_template vt');
        $this->db->where('vt.id_template', $template_id);
        $this->db->where('vt.variable', $variable);
        
        $query = $this->db->get();
        //echo $this->db->last_query();
        $this->Sqlexceptions->checkForError();
        return $query->result_array();
    }

    public function update_template($data = false, $id = false) {
        $this->db = $this->load->database('chat', TRUE);
        $this->db->where('id',$id);

        $update = $this->db->update('templates', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function update_variable($data = false, $id_variable = false, $id_template = false) {
        $this->db = $this->load->database('chat', TRUE);
        $this->db->where('id_template',$id_template);
        $this->db->where('variable',$id_variable);

        $update = $this->db->update('variables_template', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function insert_template($data){
        $this->db->insert(
            'chat.templates',
            $data
        );

        return $this->getLastInsert("chat.templates", 'id');
    }

    public function getLastInsert($tabla, $campo){
        $result = $this->db->query("SELECT $campo FROM $tabla ORDER BY $campo DESC LIMIT 1");
        
        return $result->result();
    }

    public function insert_variable($data){

        $this->db->insert(
            'chat.variables_template',
            $data
        );
    }

    public function getLastRequest($documento = false) {

        if($documento) {
            $result = $this->db->query("SELECT max(id) AS id FROM solicitudes.solicitud WHERE documento = $documento ");
        }
        
        return $result->result();
    }

    public function documentoExits($documento = null) {

        if($documento) {
            $this->db->select("IF(documento <> null or documento <> '', 1 , 0) as documento")
                                ->from("solicitudes.solicitante_agenda_telefonica")
                                ->where("documento", $documento)
                                ->limit(1);

            $query = $this->db->get();
          
            $this->Sqlexceptions->checkForError();
            return $query->result_array();
        }
    }

    public function getProveedores($type) {

            $this->db->select("id_proveedor, CONCAT(nombre_proveedor , ' / ', tipo_servicio  ) as proveedor")
                                ->from("maestro.proveedores")
                                ->where("tipo_servicio", $type == 'WAPP' ? 'WHATSAP' : $type);

            $query = $this->db->get();
          
            $this->Sqlexceptions->checkForError();
            return $query->result_array();
    }

    public function getGroup() {
 
        $result = $this->db->query("SELECT DISTINCT grupo FROM chat.templates");
        return $result->result();
    }
}
?>

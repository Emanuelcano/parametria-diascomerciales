<?php
class TipoDocumento_model extends CI_Model {
    public function __construct(){        
        $this->load->database();

        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
    }
}
?>

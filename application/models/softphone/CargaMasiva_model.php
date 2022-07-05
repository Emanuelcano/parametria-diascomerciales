<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CargaMasiva_model extends BaseModel {

   public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('solicitudes', TRUE);       
    }  
    
    //SELECT DISTINCT(rt.telefono) FROM datacredito2_reconocer_telefono rt 
    //JOIN datacredito2_reconocer_naturalnacional rn ON  rt.idConsulta=rn.id
    //WHERE rn.identificacion=8104980
public function getStrategy( ){


    $sql = "select S.id,S.nombres, S.apellidos, S.id AS IdSolicitud, S.documento, CONCAT('9057',A.numero) AS telefono, S.id, M.monto_devolver
            FROM solicitud AS S
            LEFT JOIN maestro.agenda_telefonica AS A ON S.id_cliente = A.id_cliente
            LEFT JOIN maestro.credito_detalle AS D ON D.id_credito = S.id_credito
            LEFT JOIN maestro.creditos AS M ON M.id = D.id_credito
            WHERE S.id_cliente IN (
            SELECT id_cliente FROM maestro.creditos WHERE estado = 'mora' AND id IN
            (SELECT id_credito FROM maestro.credito_detalle WHERE estado = 'mora' AND dias_atraso > 1))
AND S.documento IN (8062063,9021897,9044658,14395665,15645852,22645021,29108781,37581460,52292863,73200927,79740014,91497349,92546085,93088389,1004209799,1007110659,1007704753,1010235940,1013689352,1017139641,1020749166,1022431988,1024566336,1026141566,1030548035,1032401575,1032455493,1033740323,1035434937,16845522,1043020494,1065564989,1065612566,1065622599,1070978277,1070983477,1072646794,1077452575,1102840392,1104421723,1105684873,1107516311,1109380671,1113651847,1129580226,1144075207,1144181380,1152198936,1193152098,1193415391,55242550,1037647291,1102887494,1118863004,30331061)";
        $query = $this->db->query($sql);
        $data  = $query->result();
        //echo $this->db->last_query();
        return $data;



}

function getcodTipificacion($tipo_opera) {
  $this->db->select('t.*');
  $this->db->from('telefonia.track_cod_tipificacion t');
  if ($tipo_opera==1 || $tipo_opera==4) {
    $this->db->where("t.proceso in ('MIX','ORIGINACION') ");
  }else if ($tipo_opera==5 || $tipo_opera==6 || $tipo_opera==9 ){
    $this->db->where("t.proceso in ('MIX','COBRANZA') ");

  }
  
  $query = $this->db->get();
  //echo $this->db->last_query();
  return $query->result();
}      
    
    

   
    
  
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RepararChat extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();	
		$this->db_chat = $this->load->database('chat', TRUE);
	}

	private function SelectPhone(){
		$this->db_chat->select("count(nc.from) AS total, nc.from AS telefono");
        $this->db_chat->from('new_chats nc');        
        // $this->db_chat->where("nc.from", '3138055363');
        $this->db_chat->where("nc.to", '"15140334"');
        $this->db_chat->group_by('nc.from', 'DESC');   
        $this->db_chat->having('total > 1');       
        $this->db_chat->order_by('total', 'DESC');          
        // $this->db_chat->limit(50);          
        $query = $this->db_chat->get();
        checkDbError($this->db_chat);
    
        $result = ($query!==false && $query->num_rows() > 0) ? $query->result_array() : false;

        return $result;
	}

    public function duplicados(){

    	$result_db_chat = $this->SelectPhone();
    	var_dump($result_db_chat);

    	for ($i=0; $i < count($result_db_chat); $i++) { 
    		set_time_limit(2800);
    		$total = (int)$result_db_chat[$i]['total']-1;
    		echo "<br>TELEFONO: ".$result_db_chat[$i]['telefono'];
    		echo "<br>TOTAL ENTIDADES CREADAS DUPLICADAS: ".$total;

    		$this->db_chat->select("nc.id AS id");
	        $this->db_chat->from('new_chats nc');
	        $this->db_chat->where("nc.to", '"15140334"');
	        $this->db_chat->like('nc.from',  $result_db_chat[$i]['telefono']);      
	        $this->db_chat->order_by('nc.id', 'ASC');                   
	        $query2 = $this->db_chat->get();
	    
	        $resultPrincipal = ($query2!==false && $query2->num_rows() > 0) ? $query2->result() : false;

    		$this->db_chat->select('nc.id AS id');
        	$this->db_chat->from('new_chats nc');
        	$this->db_chat->where("nc.to", '"15140334"');
        	$this->db_chat->like('nc.from', $result_db_chat[$i]['telefono']); 
        	$subQuery1 = $this->db_chat->get_compiled_select();

        	/*
			* ACTUALIZA TODOS LOS CHAT ENVIADOS A UNA UNICA ENTIDAD (LA MAS ANTIGUA)
        	*/

        	$this->db_chat->select("id, id_chat");
	        $this->db_chat->from('sent_messages');
	        $this->db_chat->where("id_chat IN ($subQuery1)", NULL, FALSE);
	        $this->db_chat->order_by('id_chat', 'ASC');  
	        $query = $this->db_chat->get();

	        $resultSent = ($query!==false && $query->num_rows() > 0) ? $query->result() : false;

	        if ($resultSent != false) {
		        echo "<br>ENVIADOS: ".count($resultSent);
		        echo "<br>ENVIADOS ACTUALIZADOS: ";

		        for ($j=0; $j < count($resultSent); $j++) { 
		        	if ($resultPrincipal[0]->id != $resultSent[$j]->id_chat) {
		        		echo "<br>-- id: ".$resultSent[$j]->id;
		        		$this->db_chat->where('id',$resultSent[$j]->id);
			 			$this->db_chat->update('sent_messages',$datUp = array('id_chat' => $resultPrincipal[0]->id));
		        	}
		        }
	        }

	        /*
			* ACTUALIZA TODOS LOS CHAT RECIBIDOS A UNA UNICA ENTIDAD (LA MAS ANTIGUA)
        	*/

	        $this->db_chat->select("id, id_chat");
	        $this->db_chat->from('received_messages');
	        $this->db_chat->where("id_chat IN ($subQuery1)", NULL, FALSE);
	        $this->db_chat->order_by('id_chat', 'ASC');  
	        $query = $this->db_chat->get();

	        $resultReceived = ($query!==false && $query->num_rows() > 0) ? $query->result() : false;

	        if ($resultReceived != false) {
		        echo "<br>RECIBIDOS: ".count($resultReceived);
		        echo "<br>RECIBIDOS ACTUALIZADOS: ";

		        for ($k=0; $k < count($resultReceived); $k++) { 
		        	if ($resultReceived[0]->id_chat != $resultReceived[$k]->id_chat) {
		        		echo "<br>-- id: ".$resultReceived[$k]->id;
		        		$this->db_chat->where('id',$resultReceived[$k]->id);
			 			$this->db_chat->update('received_messages',$datUp = array('id_chat' => $resultPrincipal[0]->id));
		        	}
		        }	        	
	        }

	        /*
			* ACTUALIZAR EL PRIMER REGISTRO EN BASE A LA ULTIMA INSTANCIA DE CHAT CREADA
	        */

	        $this->db_chat->select("nc.id_operador, nc.from, nc.nombres, nc.apellidos, nc.documento, nc.email, nc.to, nc.medio, nc.proveedor, nc.fecha_inicio, nc.iniciado_por, nc.type, nc.sin_leer, nc.abierto, nc.fecha_ultima_recepcion, nc.fecha_ultimo_envio, nc.ultimo_mensaje, nc.status_chat, nc.account_sid");
	        $this->db_chat->from('new_chats nc');
	        $this->db_chat->where("nc.from", (string)$result_db_chat[$i]['telefono']);
	        $this->db_chat->order_by('id', 'DESC'); 

	        $query3 = $this->db_chat->get();
	        $UpdateRow = ($query3!==false && $query3->num_rows() > 0) ? $query3->row() : false;

	        $this->db_chat->where('id',$resultPrincipal[0]->id);
			$this->db_chat->update('new_chats',$UpdateRow);

	        /*
			* SE ELEIMINAN LAS ENTIDADES DUPLICADAS
	        */

			echo "<br>BORRADOS: ".$total;
	        for ($z=0; $z < count($resultPrincipal); $z++) { 
	        	if ($resultPrincipal[$z]->id != $resultPrincipal[0]->id) {
	        		echo "<br>-- id: ".$resultPrincipal[$z]->id;
	        		$this->db_chat->where('id', $resultPrincipal[$z]->id);
					$this->db_chat->delete('new_chats');
				}
	        }
    	
    	}
        
    }
}
<?php
class Security_model extends CI_Model
{

  public $table = 'track_llamadas';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        $this->db = $this->load->database('gestion', TRUE);
        //$this->db = $this->load->database('usuarios_solventa', TRUE);  
        
        parent::__construct();
    }  





    function defineToken($param_token){
        $this->db->insert('tokens_valid',$param_token);
        //var_dump($this->db->last_query());die;
        if ($this->db->affected_rows() > 0) {
            //return true;
            return $this->db->insert_id();
        }
        else{
            return false;
        }
    }

    function defineTokenParametria($data){
        $this->db->insert('token_parametria',$data);
        //var_dump($this->db->last_query());die;
        if ($this->db->affected_rows() > 0) {
            //return true;
            return $this->db->insert_id();
        }
        else{
            return false;
        }
    }

    function updateTokenExpire($user_valid,$endpoint,$update_token){

        $this->db->where('user_valid', $user_valid);
        $this->db->where('api', $endpoint);
        $this->db->where('estado', 1);
        $this->db->update('tokens_valid', $update_token);
        //var_dump($this->db->last_query());die;

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }


    }

    function updateParaTokenExpire($ultimoToken,$update_para_token){

        $this->db->where('id_token', $ultimoToken);
        $this->db->update('token_parametria', $update_para_token);
        //var_dump($this->db->last_query());die;
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }


    }

    function destroyTokenExpire($ultimoToken,$update_token){

        $this->db->where('token', $ultimoToken);
        $this->db->update('tokens_valid', $update_token);
        //var_dump($this->db->last_query());die;

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }


    }

    function destroyParaTokenExpire($ultimoToken,$update_para_token){

        $this->db->where('id_token', $ultimoToken);
        $this->db->update('token_parametria', $update_para_token);
        //var_dump($this->db->last_query());die;
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }


    }

     function searchToken($token){
        $this->db->select('*');
        $this->db->from('tokens_valid');
        $this->db->where("token_encryp",$token );
        
        $query = $this->db->get();
     
        return $query->result();
    }

    function searchTokenxBody($token){
        $this->db->select('*');
        $this->db->from('tokens_valid');
        $this->db->where("token",$token );
        
        $query = $this->db->get();
     
        return $query->result();
    }

    function searchTokenxName($user_valid,$api){
        $this->db->select('V.*,P.auto_renew');
        $this->db->from('tokens_valid as V');
        $this->db->join('token_parametria as P', 'P.id_token  = V.id','left');       
        $this->db->where("V.user_valid",$user_valid );
        $this->db->where("V.api",$api );
        $this->db->where("V.estado", 1 );
        $this->db->order_by("V.id", "DESC" );
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        return $query->result();
    }

public function searchTokenByID($iduser)
  {

    $this->db->select('T.tiempo_creacion,T.tiempo_expira,T.auto_renew')->from('gestion.token_parametria as T');
    $this->db->where('T.id_usuario', $iduser);
    $this->db->where('T.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function searchTokenByIDSpecial($iduser,$user_valid)
  {

    $this->db->select('V.id,V.user_valid,V.iat,V.exp,V.hostname,V.created,V.api,T.auto_renew,V.token');
    $this->db->from('gestion.token_parametria as T');
    $this->db->join('tokens_valid as V', 'T.id_token  = V.id','left');       
    $this->db->where('T.id_usuario', $iduser);
    $this->db->where('V.user_valid', $user_valid);
    $this->db->where('V.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }


    




} 
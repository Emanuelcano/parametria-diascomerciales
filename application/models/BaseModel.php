<?php
class BaseModel extends CI_Model
{
 
    protected $_table_name = '';
    protected $_primary_key = 'id';
    protected $_primary_filter = 'intval';
    protected $_order_by = '';
    public $rules = array();
    protected $_timestamps = false;
 
    function __construct()
    {
        parent::__construct();
    }
 
    public function array_from_post($fields)
    {
        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $this->input->post($field);
        }
        return $data;
    }
 
    public function get($id = null, $single = false)
    {
 
        if ($id != null) {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->where($this->_primary_key, $id);
            $method = 'row';
        } elseif ($single == true) {
            $method = 'row';
        } else {
            $method = 'result';
        }
 
        if($this->_order_by) {
            $this->db->order_by($this->_order_by);
        }
                
        return $this->db->get($this->_table_name)->$method();
    }
 
    public function get_by($where, $single = false,$limit = false,$offset = false)
    {
        $this->db->where($where);
        
        if($limit)
        $this->db->limit($limit,$offset);
        
        return $this->get(null, $single);
    }
 
    public function save($data, $id = null)
    {
 
        // Set timestamps
        if ($this->_timestamps == true) {
            $now = date('Y-m-d H:i:s');
            $id || $data['created'] = $now;
            $data['modified'] = $now;
        }
 
        // Insert
        if ($id === null) {
            !isset($data[$this->_primary_key]) || $data[$this->_primary_key] = null;
            $this->db->set($data);
            $this->db->insert($this->_table_name);
            $id = $this->db->insert_id();
        }
        // Update
        else {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->set($data);
            $this->db->where($this->_primary_key, $id);
            $this->db->update($this->_table_name);
        }
 
        return $id;
    }
 
    public function delete($id)
    {
        $filter = $this->_primary_filter;
        $id = $filter($id);
 
        if (!$id) {
            return false;
        }
        $this->db->where($this->_primary_key, $id);
        $this->db->limit(1);
        $this->db->delete($this->_table_name);
    }

    public function order($orders)
    {
        foreach ($orders as $index => $order)
        {
            $ord = (isset($order[1]))? $order[1]: 'DESC';
            $this->db->order_by($order[0], $ord);
        }
    }

    public function literal($params)
    {
        foreach($params AS $key => $value)
        {
            $this->db->where($value);
        }
        return $this;
    }

    // OPTIONS LIKES
    public function like_both($params)
    {
         foreach($params AS $key => $value)
        {
            $this->db->like($key, $value, 'both');
        }
        return $this;
    }

    public function equal($filters)
    {
        foreach ($filters as $key => $filter)
        {
            $this->db->where($key,$filter);
        }
        return $this;
    }

}
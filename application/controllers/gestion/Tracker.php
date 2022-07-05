<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tracker extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        if ($this->session->userdata("is_logged_in")) {
            $this->load->model('tracker_model', '', TRUE);
            $this->load->model('operaciones_model', '', TRUE);
        } else {
            die('Usuario no logueado');
        }
    }

    public function index($id_solicitud) 
    {   
        $data['tracker']['tracks'] = $this->search_track($id_solicitud);
        $data['tracker'] = array_merge($data['tracker'], $this->get_tracker_options());
        $data['id_solicitud'] = trim($id_solicitud);
        $this->load->view('gestion/box_tracker', $data);
    }
    public function track_stand_alone($id_solicitud) 
    {
        $data['tracker']['tracks'] = $this->search_track($id_solicitud);
        $data['tracker'] = array_merge($data['tracker'], $this->get_tracker_options());
        $data['id_solicitud'] = trim($id_solicitud);
        $this->load->view('gestion/box_tracker2', $data);
    }

    public function search_track($id_solicitud) 
    {
        if (isset($id_solicitud))
        {
            $params['id_solicitud'] = $id_solicitud;
        }
        if ($this->session->userdata('tipo_operador') == ID_OPERADOR_EXTERNO)
        {
            $params['id_operador'] = $this->session->userdata('idoperador');
        }

        $params['order'] = [['fecha', 'DESC'], ['hora', 'DESC'],['track_gestion.id','DESC']];
        
        return $this->_order_tracker($this->tracker_model->search($params));
    }

    private function _order_tracker($tracks)
    {
        $aux = [];
        foreach ($tracks as $key => $track) {
            $track['string_date'] = date_to_string($track['fecha'], 'd F a');
            $track['hora']        = date('h:i A', strtotime($track['hora']));

            if (!isset($aux[$track['string_date']])) {
                $aux[$track['string_date']]['style']  = month_style($track['fecha']);
                $aux[$track['string_date']]['tracks'] = [];

            }
            array_push($aux[$track['string_date']]['tracks'], $track);
        }

        return $aux;
    }

    public function get_tracker_options()
    {
        $data['actions'] = [];
        $tipo_operador = $this->session->userdata('tipo_operador');
        foreach ($this->operaciones_model->search(['estado' => 1, 'idtipo_operador' => $tipo_operador]) as $key => $action) {
            array_push($data['actions'], $action);
            if ($action['idgrupo_respuesta'] != 0) {
                $data['actions'][$key]['options'] = $this->operaciones_model->search_reasons(['idgrupo_respuestas' => $action['idgrupo_respuesta']]);
            }
        }

        return $data;
    }

}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UpdateLastTrack extends CI_Controller
{

    public function __construct($config = 'rest')
    {
        parent::__construct();
        set_time_limit(0);
    }

    public function index()
    {
        return FALSE;
        /*

            //Sin uso  solo para cargas last_track por primera vez

        $this->load->model('tracker_model','tracker_model',TRUE);
        $params['order'] = [['fecha','ASC'], ['hora','ASC']] ;
        $start=0;
        $end= 1000;
        do{
            $track = $this->tracker_model->search( $params, $start, $end);
            
            $start=$start + $end;

            $start = $start + $end;

            foreach ($track as $key => $value) {
                $data['id_solicitud'] = $value['id_solicitud'];
                $data['id_credito'] = $value['id_credito'];
                $data['id_cliente'] = $value['id_cliente'];
                $data['observaciones'] = $value['observaciones'];
                $data['fecha'] = $value['fecha'];
                $data['hora'] = $value['hora'];
                $data['id_operador'] = $value['id_operador'];
                $data['operador'] = $value['operador'];
                $data['id_tipo_gestion'] = $value['id_tipo_gestion'];
                $last_track = $this->tracker_model->save_last_track($data);
                echo '<pre>';
                print_r($data['id_solicitud'] . ' ' . $last_track . ' Actualizado');
                echo '</pre>';
            }

        }while ( !empty($track));
     */
    }

    //   public function _auth($datas = null)
    //   {
    //       // Here you can verify everything you want to perform user login.
    //       // However, method must return integer (client ID) if auth succedeed and false if not.
    //       return (!empty($datas->user_id)) ? $datas->user_id : false;
    //   }

    //   public function _event($datas = null)
    //   {
    //   echo '<pre>'; print_r($datas); echo '</pre>';die;
    // $this->load->library('comunicaciones/Comunicaciones_library');
    // 	$msg = 'Hola mundo';
    // $this->comunicaciones_library->send_sms(4715, $msg);
    //   //echo '<pre>'; var_dump($datas); echo '</pre>';
    //       // Here you can do everyting you want, each time message is received
    //       echo 'Hey ! I\'m an EVENT callback'.PHP_EOL;
    //   }

    //    public function onOpen(ConnectionInterface $connection)
    //   {
    //       // Add client to global clients object
    //       $this->clients->attach($connection);

    //       // Output
    //       if ($this->CI->ratchet_client->debug) {
    //           output('info', 'Abriendo la conexion ('.$connection->resourceId.')');
    //       }
    //   }
}

<?php 
if (!defined('BASEPATH'))
exit('No direct script access allowed');

class Track_drivers extends CI_Controller {
    public $controller_name = 'track_drivers';
    public function __construct() {
        parent::__construct();
            if (!$this->session->userdata('is_admin_login')) 
            {
                redirect(ADMIN_URL.'/home');
            }
            $this->load->model(ADMIN_URL.'/common_model');
    }

    // view restaurant menu
    public function view(){
        $data['meta_title'] = $this->lang->line('track_drivers').' | '.$this->lang->line('site_title');
        $data['drivers_position'] = $this->common_model->getLatestDriverPosition();
        $this->load->view(ADMIN_URL.'/track_drivers',$data);
    }

    //ajax view
    public function ajax_track_drivers() {
        $data['drivers_position'] = $this->common_model->getLatestDriverPosition();
        $this->load->view(ADMIN_URL.'/ajax_track_drivers',$data);
    }
}
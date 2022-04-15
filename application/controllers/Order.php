<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {
  
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		if (!$this->session->userdata('is_user_login')) {
            redirect(base_url().'home/login');
        }
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');    
		$this->load->model('/order_model');    
	}
	// track user's order
	public function track_order()
	{	
		$data['page_title'] = $this->lang->line('track_order').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'TrackOrder';
		$order_id = ($this->uri->segment('3'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment('3'))):'';
		if (!empty($order_id)) {
			$data['latestOrder'] = $this->order_model->getLatestOrder('',$order_id);
		}
		else
		{
			$data['latestOrder'] = $this->order_model->getLatestOrder($this->session->userdata('UserID'));
		}
		$data['order_id'] = $order_id;
		$this->load->view('track_order',$data);
	}
	// ajax track user's order
	public function ajax_track_order(){
		$data['page_title'] = $this->lang->line('track_order').' | '.$this->lang->line('site_title');
		$data['latestOrder'] = array();
		if (!empty($this->input->post('order_id'))) {
			$data['latestOrder'] = $this->order_model->getLatestOrder('',$this->input->post('order_id'));
		}
		$data['order_id'] = $this->input->post('order_id');
		$this->load->view('ajax_track_order',$data);
	}
}
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class How_to_order extends CI_Controller {
  
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');    
	}
	// contact us page
	public function index()
	{
		$data['page_title'] = $this->lang->line('how-to-order'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'HowToOrder';
		// get about us
		$language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
		$data['how_to_order'] = $this->common_model->getCmsPages($language_slug,'how-to-order');
		$this->load->view('how_to_order',$data);
	}
}
?>
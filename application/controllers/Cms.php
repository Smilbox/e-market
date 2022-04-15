<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cms extends CI_Controller {
  
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');    
	}
	// about us page
	public function about_us()
	{
		$data['page_title'] = $this->lang->line('about_us'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'AboutUs';
		// get about us
		$language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
		$data['about_us'] = $this->common_model->getCmsPages($language_slug,'about-us');
		$this->load->view('about_us',$data);
	}
	
	// how to order page
	public function how_to_order()
	{
		$data['page_title'] = $this->lang->line('how-to-order'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'HowToOrder';
		// get about us
		$language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
		$data['how_to_order'] = $this->common_model->getCmsPages($language_slug,'how-to-order');
		$this->load->view('how_to_order',$data);
	}

	//legal notice
	public function legal_notice()
	{
		$data['page_title'] = $this->lang->line('legal_notice'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'LegalNotice';
		// get legal notice
		$language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
		$data['legal_notice'] = $this->common_model->getCmsPages($language_slug,'legal-notice');
		$this->load->view('legal_notice',$data);
	}

	//terms and conditions
	public function terms_and_conditions()
	{
		$data['page_title'] = $this->lang->line('terms_and_conditions'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'TermsAndConditions';
		// get terms and conditions
		$language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
		$data['terms_and_conditions'] = $this->common_model->getCmsPages($language_slug,'terms-and-conditions');
		$this->load->view('terms_and_conditions',$data);
	}

	// privacy_policy page
	public function privacy_policy()
	{
		$data['page_title'] = $this->lang->line('privacy_policy'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'PrivacyPolicy';
		// get privacy policy
		$language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
		$data['privacy_policy'] = $this->common_model->getCmsPages($language_slug,'privacy-policy');
		$this->load->view('privacy_policy_page',$data);
	}
}
?>
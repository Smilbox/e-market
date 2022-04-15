<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller { 
	function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('user_model');                
        $this->load->library('form_validation');
    }
    // reset users password
    public function reset($verificationCode=NULL)
    {	
	    $data['page_title'] = $this->lang->line('reset_password').' | '.$this->lang->line('site_title');
		if($this->input->post('submit') == "Submit"){
	        $this->form_validation->set_rules('password','Password','trim|required');
	        $this->form_validation->set_rules('confirm_pass','confirm password','trim|required|matches[password]');
	        if($this->form_validation->run())
	        {
	          $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
	          $enc_pass  = md5($salt.$this->input->post('password'));
	          $updatePassword = array(
	              'Password' => $enc_pass ,
				  'email_verification_code' => ''
	          );
	          $Detail = $this->user_model->updatePassword($updatePassword,$this->input->post('verificationCode'));
	          $this->session->set_flashdata('PasswordChange', $this->lang->line('success_password_change'));        
	          redirect(base_url()."user/thankYou");
	          exit();
	        }
	    }
		if($verificationCode){
		    $chkverify = $this->user_model->forgotpassowrdVerify($verificationCode); 
			if(!empty($chkverify)){ 
		        $data['verificationCode'] = $verificationCode;
		        $data['page_title'] = $this->lang->line('title_newpassword').' | '.$this->lang->line('site_title');
		        $this->load->view('reset_password',$data); 
		    }else{ 
		        $this->session->set_flashdata('verifyerr', $this->lang->line('invalid_url_verify'));
		        redirect(base_url()."user/thankYou");
	         	exit();
		    }
		}else{
			 $this->session->set_flashdata('verifyerr', $this->lang->line('invalid_url_verify'));
		     $this->load->view('reset_password',$data);
		}
	}
	//verify account
	public function verify_account($verificationCode=NULL)
    {	
    	if($verificationCode){
		    $chkverify = $this->user_model->forgotpassowrdVerify($verificationCode); 
		    if(empty($chkverify)){
		        $this->session->set_flashdata('verifyerr', $this->lang->line('invalid_url_verify'));
		        redirect(base_url().'user/thankYou'); exit;
		    }
		}
        $update = array(
          'active'=>1,
          'email_verification_code'=>''
        );
	    $this->user_model->updatePassword($update,$verificationCode);
	    $this->session->set_flashdata('activate', $this->lang->line('verify_account'));        
	    redirect(base_url()."user/thankYou");
	    exit();
	   
	}
	//cron job for expiry date
	public function expireAccout(){
		$where = date('Y-m-d');
        $this->db->select('entity_id');
        $this->db->where('end_date <= ',$where);
        $arrData =  $this->db->get('coupon')->result();
        if(!empty($arrData)){
        	foreach ($arrData as $key => $value) {
        		$this->db->set('status',0)->where('entity_id',$value->entity_id)->update('coupon');
        	}
        }
	}
	// thank you page
	public function thankYou(){
		$data['page_title'] = $this->lang->line('thank_you').' | '.$this->lang->line('site_title');
		$this->load->view('thank_you',$data); 
	}
}
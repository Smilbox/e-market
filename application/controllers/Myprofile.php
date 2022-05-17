<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Myprofile extends CI_Controller {
  
	public function __construct() {
		parent::__construct();      
        if (!$this->session->userdata('is_user_login')) {
            redirect('home');
        }  
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');     
		$this->load->model('/myprofile_model');    
	}
	// my profile index page
	public function index()
	{	
		$data['page_title'] = $this->lang->line('my_profile').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$data['selected_tab'] = ""; 
		if($this->input->post('submit_profile') == "Save"){ 
			$this->form_validation->set_rules('first_name', 'First Name', 'trim|required'); 
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|callback_checkPhone'); 
	        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[6]|callback_checkEmail'); 
	        $this->form_validation->set_rules('password', 'New Password', 'trim|min_length[8]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|min_length[8]|matches[password]');       
	        if ($this->form_validation->run())
	        { 
	        	$updateUserData = array(                  
					'first_name' =>$this->input->post('first_name'),
					'last_name' =>$this->input->post('last_name'),                  
					'mobile_number' =>$this->input->post('phone_number'),                  
					'email' =>$this->input->post('email'),              
					'updated_by'=>$this->session->userdata("UserID"),
					'updated_date'=>date('Y-m-d H:i:s')
				);                 
				if (!empty($this->input->post('password')) && !empty($this->input->post('confirm_password'))) {
					$newEncryptPass  = md5(SALT.$this->input->post('password'));
					$updateUserData['password'] = $newEncryptPass;
				}   
                if (!empty($_FILES['image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/users';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/users')) {
                      @mkdir('./uploads/users', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('image'))
                    {
                      $img = $this->upload->data();
                      $updateUserData['image'] = "users/".$img['file_name'];   
                      if($this->input->post('uploaded_image')){
                        @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                      }  
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                if(empty($data['Error'])){
					$affected_rows = $this->common_model->updateData('users',$updateUserData,'entity_id',$this->input->post('entity_id'));  
					if ($affected_rows) {
						$this->session->set_userdata(
							array(
							  'UserID' => $this->input->post('entity_id'),
							  'userFirstname' => $this->input->post('first_name'),                            
							  'userLastname' => $this->input->post('last_name'),                            
							  'userEmail' => $this->input->post('email'),                                   
							  'userPhone' => $this->input->post('phone_number'),                                  
							  'userImage' => $updateUserData['image']       
							)
						);    
					}           
					$this->session->set_flashdata('myProfileMSG', $this->lang->line('success_update')); 
					echo 'success'; 
					exit;
                }
	        }
	        else
	        {
	        	echo validation_errors();
	        	exit;
	        }
		}

		$data['profile'] = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
		$data['addresses'] = $this->common_model->getMultipleRows('user_address','user_entity_id',$this->session->userdata('UserID'));
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
			}
		}
        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
			}
		}
		/*// bookings tab data
		$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_events'])) {
			foreach ($data['upcoming_events'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['upcoming_events'][$key]['ratings'] = $ratings;
			}
		}
		if (!empty($data['past_events'])) {
			foreach ($data['past_events'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['past_events'][$key]['ratings'] = $ratings;
			}
		}*/
		// my addressess tab data
		$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));
		
		$this->load->view('myprofile',$data);
	}
	// get order details
	public function getOrderDetails(){
		$data['page_title'] = $this->lang->line('order_details').' | '.$this->lang->line('site_title');
		$order_details = array();
		if (!empty($this->input->post('order_id'))) {
			$data['order_details'] = $this->myprofile_model->getOrderDetail('','',$this->input->post('order_id'));
			if (!empty($data['order_details'])) {
				foreach ($data['order_details'] as $key => $value) {
					$ratings = $this->common_model->getShopReview($value['shop_id']);
					$data['order_details'][$key]['ratings'] = $ratings;
				}
			} 
		}
		$this->load->view('ajax_order_details',$data);
	}
	// getAllOrders ajax call
	public function getOrderHistory(){
		$data['page_title'] = $this->lang->line('order_history').' | '.$this->lang->line('site_title');
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
			}
		}
        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
			}
		} 
		$this->load->view('ajax_order_history',$data);
	}
	// getAllOrders ajax call
	public function getMyBookings(){
		$data['page_title'] = $this->lang->line('my_bookings').' | '.$this->lang->line('site_title');
		$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_events'])) {
			foreach ($data['upcoming_events'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['upcoming_events'][$key]['ratings'] = $ratings;
			}
		}
		if (!empty($data['past_events'])) {
			foreach ($data['past_events'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['past_events'][$key]['ratings'] = $ratings;
			}
		}
		$this->load->view('ajax_my_bookings',$data);
	}
	// get booking details
	public function getBookingDetails(){
		$data['page_title'] = $this->lang->line('booking_details').' | '.$this->lang->line('site_title');
		$booking_details = array();
		if (!empty($this->input->post('event_id'))) {
			$data['booking_details'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'',$this->input->post('event_id'));
			if (!empty($data['booking_details'])) {
				foreach ($data['booking_details'] as $key => $value) {
					$ratings = $this->common_model->getShopReview($value['shop_id']);
					$data['booking_details'][$key]['ratings'] = $ratings;
				}
			} 
		}
		$this->load->view('ajax_booking_details',$data);
	}
	// getAllOrders ajax call
	public function getMyAddresses(){
		$data['page_title'] = $this->lang->line('my_addresses').' | '.$this->lang->line('site_title');
		$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));  
		//echo '<pre>'; print_r($data); exit;
		$this->load->view('ajax_my_addresses',$data);
	}
	// edit address
	public function getEditAddress(){
		if (!empty($this->input->post('address_id'))) {
			$data = $this->myprofile_model->getAddress($this->session->userdata('UserID'),$this->input->post('address_id'));  
		}
		echo json_encode($data[0]);
	}
	// view users bookings
	public function view_my_bookings()
    { 
		$data['page_title'] = $this->lang->line('my_bookings').' | '.$this->lang->line('site_title');
		$data['selected_tab'] = "bookings";   
		$data['profile'] = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
		$data['addresses'] = $this->common_model->getMultipleRows('user_address','user_entity_id',$this->session->userdata('UserID'));
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
			}
		}
        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
			}
		} 
		// bookings tab data
		$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_events'])) {
			foreach ($data['upcoming_events'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['upcoming_events'][$key]['ratings'] = $ratings;
			}
		}
		if (!empty($data['past_events'])) {
			foreach ($data['past_events'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['past_events'][$key]['ratings'] = $ratings;
			}
		}
		// my addressess tab data
		$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));
		$this->load->view('myprofile',$data);
    }
    // view users addresses
	public function view_my_addresses()
    { 
		$data['page_title'] = $this->lang->line('my_addresses').' | '.$this->lang->line('site_title');
		$data['selected_tab'] = "addresses";  
		$data['profile'] = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
		$data['addresses'] = $this->common_model->getMultipleRows('user_address','user_entity_id',$this->session->userdata('UserID'));
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
			}
		}
        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
			}
		} 
		// bookings tab data
		$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_events'])) {
			foreach ($data['upcoming_events'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['upcoming_events'][$key]['ratings'] = $ratings;
			}
		}
		if (!empty($data['past_events'])) {
			foreach ($data['past_events'] as $key => $value) {
				$ratings = $this->common_model->getShopReview($value['shop_id']);
				$data['past_events'][$key]['ratings'] = $ratings;
			}
		}
		// my addressess tab data
		$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));
		$this->load->view('myprofile',$data);
    }
	// add user's address
	public function addAddress(){ 
		$data['page_title'] = $this->lang->line('add_address').' | '.$this->lang->line('site_title');
		if($this->input->post('submit_address') != ""){ 
			$this->form_validation->set_rules('address_field', 'Address', 'trim|required'); 
			//$this->form_validation->set_rules('landmark', 'Landmark', 'trim|required'); 
	        $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required'); 
	        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
            $this->form_validation->set_rules('user_entity_id', 'UserID', 'trim|required');       
	        if ($this->form_validation->run())
	        { 
	        	$add_data = array(
	                'address'=>$this->input->post('address_field'),
	                'search_area'=>$this->input->post('add_address_area'),
	                'landmark'=>$this->input->post('landmark'),
	                'latitude'=>$this->input->post('latitude'),
	                'longitude'=>$this->input->post('longitude'),
	                'user_entity_id'=>$this->input->post('user_entity_id')
	            );
				if (!empty($this->input->post('add_entity_id'))) 
				{
					$this->common_model->updateData('user_address',$add_data,'entity_id',$this->input->post('add_entity_id')); // edit address
					$this->session->set_flashdata('myProfileMSG', $this->lang->line('success_update')); 
				}
				else 
				{
					$address_id = $this->common_model->addData('user_address',$add_data); // add address
					$this->session->set_flashdata('myProfileMSG', $this->lang->line('success_add')); 
				}
				echo 'success';
				exit;
	        }
	        else
	        {
	        	echo validation_errors();
				exit;
	        }
		}
	}
	// delete Address
	public function ajaxDeleteAddress(){
		if (!empty($this->input->post('address_id'))) {
			$this->common_model->deleteData('user_address','entity_id',$this->input->post('address_id'));
		}
	}
	// set main address
	public function ajaxSetAddress(){
		if (!empty($this->input->post('address_id')) && !empty($this->session->userdata('UserID'))) {
			$updateData = array(
				'is_main' => 0 
			);
			$this->common_model->updateData('user_address',$updateData,'user_entity_id',$this->session->userdata('UserID'));
			$updateMainData = array(
				'is_main' => 1
			);
			$this->common_model->updateData('user_address',$updateMainData,'entity_id',$this->input->post('address_id'));
		}
	}
    /*
    * Server side validation check email exist
    */
	public function checkPhone($str){
		$UserID = $this->input->post('entity_id');      
		$checkPhone = $this->myprofile_model->checkPhone($str,$this->input->post('entity_id')); 
		if($checkPhone>0){
			$this->form_validation->set_message('checkPhone',$this->lang->line('number_already_registered'));
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
	/*
	* Server side validation check email exist
	*/
	public function checkEmail($str){
		$UserID = $this->input->post('entity_id');      
		$checkEmail = $this->myprofile_model->checkEmail($str,$this->input->post('entity_id'));       
		if($checkEmail>0){
			$this->form_validation->set_message('checkEmail','User have already registered with this email!');
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
}
?>
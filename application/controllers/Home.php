<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
  
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');
		$this->load->model(ADMIN_URL.'/store_type_model'); 
		$this->load->model(ADMIN_URL.'/promotion_settings_model'); 
		$this->load->helper('cookie');
		

		if (empty($this->session->userdata('language_slug'))) {
			$data['lang'] = $this->common_model->getdefaultlang();
			$this->session->set_userdata('language_directory',$data['lang']->language_directory);
			$this->config->set_item('language', $data['lang']->language_directory);
			$this->session->set_userdata('language_slug',$data['lang']->language_slug);
  		}
		
		/*$stores = $this->store_type_model->getAll();
		$data['store_type_variables'] = [];
		foreach($stores as $store) 
		{
		    $data['store_type_variables'][$store->{"name_{$this->session->userdata('language_slug')}"}] = $store->entity_id;
        }
		$this->globalData = $data;*/
	}
	// get home page
	public function index()
	{ 

		$data['current_page'] = 'HomePage';
		$data['page_title'] = $this->lang->line('home_page'). ' | ' . $this->lang->line('site_title');
		/* $data['restaurants'] = $this->home_model->getRestaurants();
		if (!empty($data['restaurants'])) {
			foreach ($data['restaurants'] as $key => $value) {
				$ratings = $this->home_model->getRestaurantReview($value['MainRestaurantID']);
				$data['restaurants'][$key]['ratings'] = $ratings;
			}
		}
		$data['categories'] = $this->home_model->getAllCategories();
		$data['coupons'] = $this->home_model->getAllCoupons();*/
		$data['lang'] = $this->session->userdata('language_slug');
		$stores = $this->store_type_model->getAll();
		foreach($stores as $key => $store) {
			$data['store_types'][$key] = $store;
			$data['store_types'][$key]->link =  base_url().'order/'.$store->entity_id;
			/*if($store->name_en == 'Restaurants') {
				$data['store_types'][$key]->link = base_url().'restaurants';
			}*/
			if($store->name_en == 'Telma') {
				$data['store_types'][$key]->link = base_url().'telma';
			}
			$data['store_type_variables'][$store->{"name_{$this->session->userdata('language_slug')}"}] = $store->entity_id;
		}
		// $data['store_type_variables'] = $this->globalData['store_type_variables'];
		$data['promotion_settings'] = [];
		$promotion_settings = $this->promotion_settings_model->getAllPromotionWithResto();
		$i = 0;
		if(!empty($promotion_settings))
		{
			$priority = array_column($promotion_settings, 'priority_order');

			array_multisort($priority, SORT_ASC, $promotion_settings);
			
			foreach($promotion_settings as $key => $val) 
			{
				if($val->shown_promotion == '1') {
					$data['promotion_settings'][$i] = $val;
					$i++;
				}
			}
		}
		$data['banner_settings'] = $this->promotion_settings_model->getBannerSettingsById('1');
		$this->load->view('main_home_page',$data);
	}
	
	// get restaurants home page
	public function restaurants()
	{ 

		$data['current_page'] = 'HomePage';
		$data['page_title'] = $this->lang->line('home_page'). ' | ' . $this->lang->line('site_title');
		$data['restaurants'] = $this->home_model->getRestaurants();
		if (!empty($data['restaurants'])) {
			foreach ($data['restaurants'] as $key => $value) {
				$ratings = $this->home_model->getRestaurantReview($value['MainRestaurantID']);
				$data['restaurants'][$key]['ratings'] = $ratings;
			}
		}
		$data['lang'] = $this->session->userdata('language_slug');
		$data['categories'] = $this->home_model->getAllCategories();
		$data['coupons'] = $this->home_model->getAllCoupons();
		$this->load->view('home_page',$data);
	}



	// frontend user login
	public function login()
	{	 
    	$data['page_title'] = $this->lang->line('title_login').' | '. $this->lang->line('site_title');
		if($this->input->post('submit_page') == "Login"){
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required'); 
	        $this->form_validation->set_rules('password', 'Password', 'trim|required');        
	        if ($this->form_validation->run())
	        {  
	            $phone_number = trim($this->input->post('phone_number'));
	            $enc_pass = md5(SALT.trim($this->input->post('password')));

	            $this->db->where('mobile_number',$phone_number);
				$this->db->where('password',$enc_pass);
				$this->db->where("(user_type='User')");
				$val = $this->db->get('users')->first_row();  
				if(!empty($val))
				{       
					if($val->status=='1') 
					{
						$this->session->set_userdata(
							array(
							  'UserID' => $val->entity_id,
							  'userFirstname' => $val->first_name,                            
							  'userLastname' => $val->last_name,                            
							  'userEmail' => $val->email,                                   
							  'userPhone' => $val->mobile_number,                                
							  'userImage' => $val->image,                            
							  'is_admin_login' => 0,                           
							  'is_user_login' => 1,
							  'UserType' => $val->user_type,
							  'package_id' => array(),
							)
						);
						// remember ME
						$cookie_name = "adminAuth";
						if($this->input->post('rememberMe')==1)
						{                    
							$this->input->set_cookie($cookie_name, 'usr='.$phone_number.'&hash='.$password, 60*60*24*5); // 5 days
						} 
						else 
						{
							delete_cookie($cookie_name);
						}                
						redirect(base_url().'myprofile');
					} 
					else if($val->active=='0' || $val->active=='')
					{                
						$data['loginError'] = $this->lang->line('front_login_deactivate');
					} 
					else 
					{
						$data['loginError'] = $this->lang->line('front_login_error');
					}
				}
				else
				{
					$data['loginError'] = $this->lang->line('front_login_error');
				}
				$this->session->set_flashdata('error_MSG', $data['loginError']);
				redirect(base_url().'home/login');
				exit;
	        }
		}
		$data['current_page'] = 'Login';
		$this->load->view('login',$data);
	}
    /*
    * Server side validation check email exist
    */
	public function checkPhone($str){   
		$checkPhone = $this->home_model->checkPhone($str); 
		if($checkPhone>0){
			$this->form_validation->set_message('checkPhone', $this->lang->line('number_already_registered'));
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
		$checkEmail = $this->home_model->checkEmail($str);       
		
		// $url = "http://apilayer.net/api/check?access_key=689490f7cd91accf9de70050a3316add&email=".$str."&smtp=1&format=1";
		// $ch = curl_init();
	    // curl_setopt($ch, CURLOPT_URL, $url);
	    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $response_all = json_decode($response);

		// print_r($response_all);die;
		
		if($checkEmail>0){
			$this->form_validation->set_message('checkEmail','User have already registered with this email!');
			return FALSE;
		} 
		// else if (!$response_all->smtp_check) {
		// 	$this->form_validation->set_message('checkEmail', $this->lang->line('invalid_domain'));
		// 	return FALSE;
		// }
		else{
			return TRUE;
		}
	}

	public function fb_logon()
    {
		$this->load->model('v1/bot_api_model');
		$val = null;
        // $requestBody = json_decode($this->input->raw_input_stream, true);
        if(!empty($this->input->post('entity_id'))) 
        {
            $user = $this->bot_api_model->getRecord('users', 'entity_id', $this->input->post('entity_id'));    
        } else {
            $user = $this->bot_api_model->getRecord('users', 'email', $this->input->post('email'));
		}
		
		if(!isset($user)) {
			$user = $this->bot_api_model->getRecord('users', 'bot_user_id', $this->input->post('bot_user_id'));
		}

        if(isset($user) && $user->entity_id) {
            if(empty($user->bot_user_id) || $user->bot_user_id != $this->input->post('bot_user_id')) {
                $updatedUser = $this->bot_api_model->updateData('users', array('bot_user_id' => $this->input->post('bot_user_id')), 'entity_id', $user->entity_id);
            } 
            
            /* if(empty($user->phone_number) || empty($user->mobile_number)) {
                $updatedUser = $this->bot_api_model->updateData('users', array('phone_number' => $requestBody['phone_number'], 'mobile_number' => $requestBody['mobile_number']), 'entity_id', $user->entity_id);
            } */
            
            if(empty($user->email) || empty($user->email)) {
                $updatedUser = $this->bot_api_model->updateData('users', array('email' => $this->input->post('email')), 'entity_id', $user->entity_id);
			} 
			
			$val = $user;
			
        } else {
			$data = array(
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'bot_user_id' => $this->input->post('bot_user_id'),
				'image' => $this->input->post('picture'),
				'password' => md5(SALT.'password'),
				'status' => 1,
				'user_type' => 'User',
			);

			$id = $this->bot_api_model->addData('users', $data);
			
			$val = $this->bot_api_model->getRecord('users', 'entity_id', $id);

		}
		
		if($val && $val->status=='1') 
			{
				$this->session->set_userdata(
					array(
							  'UserID' => $val->entity_id,
							  'userFirstname' => $val->first_name,                            
							  'userLastname' => $val->last_name,                            
							  'userEmail' => $val->email,                                   
							  'userPhone' => $val->mobile_number,                                
							  'userImage' => $val->image,                            
							  'is_admin_login' => 0,                           
							  'is_user_login' => 1,
							  'UserType' => $val->user_type,
							  'package_id' => array(),
					)
				);
					               
				// redirect(base_url().'myprofile');

				echo json_encode(array('success' => true, 'status' => 200));
				exit;
				
			}
			echo json_encode(array('success' => false, 'status' => 500));	 
    }

	// frontend user registration
	public function registration()
	{
		$data['page_title'] = $this->lang->line('title_registration').' | '.$this->lang->line('site_title');
		if($this->input->post('submit_page') == "Register"){
			$this->form_validation->set_rules('name', 'Name', 'trim|required'); 
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|callback_checkPhone');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|callback_checkEmail'); 
	        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');        
	        if ($this->form_validation->run())
	        {   
	        	$checkRecords = $this->home_model->mobileCheck(trim($this->input->post('phone_number')));
	        	if ($checkRecords == 0) {
		            $name = trim($this->input->post('name'));      
		            $namearr = explode(" ", $name);  
		            if (!empty($namearr)) {
		            	foreach ($namearr as $key => $value) {
		            		if ($key != 0) {
		            			$last_name[] = $value;
		            		}
		            	}
		            }
	                $userData = array(
	                    "first_name"=>(!empty($namearr[0]))?$namearr[0]:'',
	                    "last_name"=>(!empty($last_name))?implode(" ", $last_name):'',
	                    "password"=>md5(SALT.$this->input->post('password')),
	                    "email"=>trim($this->input->post('email')),
	                    "mobile_number"=>trim($this->input->post('phone_number')),
	                    "user_type"=>"User",
						"status"=>1,
						"active"=>1
	                ); 
	                $entity_id = $this->common_model->addData('users',$userData);
		            if ($entity_id) {
		            	$data['success'] = $this->lang->line('registration_success');
		            	$this->session->set_flashdata('success_MSG', $data['success']);
		            }
		            if($this->input->post('email')){
                         // confirmation link
		            	$language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
                        $verificationCode = random_string('alnum', 20).$entity_id.random_string('alnum', 5);
                        $confirmationLink = '<a href='.base_url().'user/verify_account/'.$verificationCode.'>here</a>';   
                        $email_template = $this->db->get_where('email_template',array('email_slug'=>'verify-account','language_slug'=>$language_slug))->first_row();       
                        $arrayData = array('FirstName'=>$userData['first_name'], 'LastName'=> $userData['last_name'],'ForgotPasswordLink'=>$confirmationLink);
                        $EmailBody = generateEmailBody($email_template->message,$arrayData);
                        //get System Option Data
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                      
                        $this->load->library('email');  
                        $config['charset'] = "utf-8";
                        $config['mailtype'] = "html";
                        $config['newline'] = "\r\n";      
                        $this->email->initialize($config);  
                        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                        $this->email->to($this->input->post('email'));      
                        $this->email->subject($email_template->subject);  
                        $this->email->message($EmailBody);
                        if($this->email->send())
						{
						echo 'Email sent.';
						}
						else
						{
						show_error($this->email->print_debugger());
						die;
						}
                          
                        // update verification code
                        $addata = array('email_verification_code'=>$verificationCode);
                        $this->common_model->updateData('users',$addata,'entity_id',$entity_id);          
                    }
	        	}
	        	else
	        	{
	        		$data['error'] = $this->lang->line('front_registration_fail');
	        		$this->session->set_flashdata('error_MSG', $data['error']);
	        	}
	        	redirect(base_url().'home/registration');
	        	exit;
	        }
	    }
		$data['current_page'] = 'Registration';
		$this->load->view('registration',$data);
	}
	// user forgot password
	public function forgot_password(){ 
		if($this->input->post('forgot_submit_page') == "Submit"){ 
			$this->form_validation->set_rules('email_forgot', 'Email', 'trim|required|valid_email');      
	        if ($this->form_validation->run())
	        {   
	        	$checkRecord = $this->common_model->getRowsMultipleWhere('users', array('email'=>strtolower($this->input->post('email_forgot')),'status'=>1));
	        	$arr['forgot_success'] = '';
	        	$arr['forgot_error'] = '';
		        if(!empty($checkRecord[0]))
		        {
		            // confirmation link
		            if($this->input->post('email_forgot')){
		            	$language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
		                $verificationCode = random_string('alnum', 20).$checkRecord[0]->entity_id.random_string('alnum', 5);
		                $confirmationLink = '<a href='.base_url().'user/reset/'.$verificationCode.'>here</a>';   
		                $email_template = $this->db->get_where('email_template',array('email_slug'=>'forgot-password','language_slug'=>$language_slug))->first_row();        
		                $arrayData = array('FirstName'=>$checkRecord[0]->first_name,'ForgotPasswordLink'=>$confirmationLink);
		                $EmailBody = generateEmailBody($email_template->message,$arrayData);

		                //get System Option Data
		                $this->db->select('OptionValue');
		                $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

		                $this->db->select('OptionValue');
		                $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
		              
		                $this->load->library('email');  
		                $config['charset'] = "utf-8";
		                $config['mailtype'] = "html";
		                $config['newline'] = "\r\n";      
		                $this->email->initialize($config);  
		                $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
		                $this->email->to($this->input->post('email_forgot'));      
		                $this->email->subject($email_template->subject);  
		                $this->email->message($EmailBody);            
		                if($this->email->send())
						{
						echo 'Email sent.';
						}
						else
						{
						show_error($this->email->print_debugger());
						die;
						}
		                // update verification code
		                $addata = array('email_verification_code'=>$verificationCode);
		                $this->common_model->updateData('users',$addata,'entity_id',$checkRecord[0]->entity_id); 
		            }
		            $arr['forgot_success'] = $this->lang->line('forgot_success');;
		        }
		        else
		        {
		            $arr['forgot_error'] = $this->lang->line('email_not_exist');
		        }
	        }
	    }

	    echo json_encode($arr);
	}
	// user logout
	public function logout(){ 
        $this->session->unset_userdata('UserID');
        $this->session->unset_userdata('userFirstname');
        $this->session->unset_userdata('userLastname');
        $this->session->unset_userdata('userEmail');   
        $this->session->unset_userdata('userPhone'); 
        $this->session->unset_userdata('is_user_login'); 
        $this->session->unset_userdata('package_id');       
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }
    // add lat long to session once if searched by user
    public function addLatLong(){
    	if(!empty($this->input->post('lat')) && !empty($this->input->post('long')) && !empty($this->input->post('address'))){
    		$this->session->set_userdata(
				array(
				  'searched_lat' => $this->input->post('lat'),
				  'searched_long' => $this->input->post('long'),  
				  'searched_address' => $this->input->post('address'),  
				)
			);
    	}
    }
	// get Popular Resturants
	public function getPopularResturants(){
		$data['page_title'] = $this->lang->line('popular_restaurants').' | '.$this->lang->line('site_title');
		$address = $this->getAddress($this->input->post('latitude'),$this->input->post('longitude'));
		$restaurants = $this->home_model->getRestaurants($this->input->post('store_type_id'));
		if (!empty($restaurants)) {
			foreach ($restaurants as $key => $value) {
				$distance = $this->_getDistance($this->input->post('latitude'),$this->input->post('longitude'), $value['latitude'], $value['longitude']);
				// $distance = $this->getDistance($this->input->post('latitude')."~".$this->input->post('longitude'), $value['latitude']."~".$value['longitude']);
				if ($distance && $distance < MAXIMUM_RANGE) {
					$nearbyRestaurants[] = $restaurants[$key];
				}
			}
		}
		if (!empty($nearbyRestaurants)) {
			foreach ($nearbyRestaurants as $key => $value) {
				$ratings = $this->home_model->getRestaurantReview($value['restaurant_id']);
				$nearbyRestaurants[$key]['ratings'] = $ratings;
			}
		}
		$data['nearbyRestaurants'] = $nearbyRestaurants;
		// $data['storeTypeId'] = $this->input->post('store_type_id');
		$data['storeType'] = $this->store_type_model->getById($this->input->post('store_type_id'));
		if(!empty($nearbyRestaurants)) {
			$this->load->view('popular_restaurants',$data);
		} else {
			return '<span></span>';
		}
	}
	// get user's address with lat long
	public function getUserAddress(){
		$this->session->set_userdata(
			array(
			  'latitude' => $this->input->post('latitude'),
			  'longitude' => $this->input->post('longitude'),
			)
		);
		$address = $this->getAddress($this->input->post('latitude'),$this->input->post('longitude'));
		echo json_encode($address);
	}

	/* public function getDistance($originLatLong, $destinationLatLong)
    {
        $origin = str_replace('~', ',' ,$originLatLong);
        $destination = str_replace('~', ',' ,$destinationLatLong);
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$origin."&destinations=".$destination."&key=".GMAP_API_KEY;
        $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_all = json_decode($response);
        // $rows = (array) $response_all->rows;
        // $elem = (array) $rows[0];
        $textDistance = $response_all->rows[0]->elements[0]->distance->text;
        if(isset($textDistance) && $response_all->rows[0]->elements[0]->status != "ZERO_RESULTS")
        {
            if(strpos($textDistance, 'km')) 
            {
                return floatval(str_replace(array("km"," "), array("",""), $textDistance));
            }
            if(strpos($textDistance, 'm')) 
            {
                return null;
            }
        } 
        else {
            return null; 
        }
    } */

	// get distance between two pair of coordinates
	function _getDistance($latitude1, $longitude1, $latitude2, $longitude2) {  
		$earth_radius = 6371;

		$dLat = deg2rad($latitude2 - $latitude1);  
		$dLon = deg2rad($longitude2 - $longitude1);  

		$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
		$c = 2 * asin(sqrt($a));  
		$d = $earth_radius * $c;  
		return $d;  
	}
	// get address from lat long
	function __getAddress($latitude,$longitude){ 
	    if(!empty($latitude) && !empty($longitude)){
	        //Send request and receive json data by address
	        $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&key='.GMAP_API_KEY); 
	        $output = json_decode($geocodeFromLatLong);
	        $status = $output->status;
	        //Get address from json data
	        $address = ($status=="OK")?$output->results[1]->formatted_address:'';
	        //Return address of the given latitude and longitude
	        if(!empty($address)) {
	            return $address;
	        }
	        else
	        {
	            return false;
	        }
	    }
	    else
	    {
	        return false;   
	    }
	}

	function getAddress($latitude, $longitude)
	{
		$url = "https://api.openrouteservice.org/geocode/reverse?api_key=".OPEN_ROUTE_KEY."&point.lon=".$longitude."&point.lat=".$latitude;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			'Accept: application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8'
		));
		$response = curl_exec($ch);
		curl_close($ch);
		$response_all = json_decode($response);

		$res = false;
		if($response_all)
		{
			$feature = $response_all->features[0];
			$res = $feature->properties->label;
		}

		return $res;
	}
	// categories search
	public function quickCategorySearch(){
		$data['page_title'] = $this->lang->line('popular_restaurants').' | '.$this->lang->line('site_title');
		$restaurants = $this->home_model->searchRestaurants($this->input->post('category_id'));
		if (!empty($restaurants)) {
			foreach ($restaurants as $key => $value) {
				$distance = $this->getDistance($this->session->userdata('latitude'),$this->session->userdata('longitude'), $value['latitude'], $value['longitude']);
				if ($distance < 500) {
					$nearbyRestaurants[] = $restaurants[$key];
				}
			}
		}
		if (!empty($nearbyRestaurants)) {
			foreach ($nearbyRestaurants as $key => $value) {
				$ratings = $this->home_model->getRestaurantReview($value['restaurant_id']);
				$nearbyRestaurants[$key]['ratings'] = $ratings;
			}
		}
		$data['nearbyRestaurants'] = $nearbyRestaurants;
		$this->load->view('popular_restaurants',$data);
	}
	// function to get  the address
	function get_lat_long($address){
	    $address = str_replace(" ", "+", $address);
	    $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
	    $json = json_decode($json);
	    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
	    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
	    $latlng = array('latitude'=>$lat,'longitude'=>$long);
	    return json_encode($latlng);
	}
	// get users notification
	public function getNotifications(){
		if (!empty($this->session->userdata('UserID'))) {
			$data['userUnreadNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'),'unread');
			$data['notification_count'] = count($data['userUnreadNotifications']);
			$data['userNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'));
			$this->load->view('ajax_notifications',$data);
		}
	}
	// get unread notifications
	public function unreadNotifications() { 
		if (!empty($this->session->userdata('UserID'))) { 
			$updateData = array(
				'view_status' => 1,
			);
			$this->common_model->updateData('user_order_notification',$updateData,'user_id',$this->session->userdata('UserID'));
			$data['userUnreadNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'),'unread');
			$data['notification_count'] = count($data['userUnreadNotifications']);
			$data['userNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'));
		}
	}	
}
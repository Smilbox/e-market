<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once APPPATH."/third_party/excelclasses/PHPExcel.php";
require_once APPPATH."/third_party/excelclasses/PHPExcel/IOFactory.php";
class Users extends CI_Controller {
    public $full_module = 'User Management System'; 
    public $module_name = 'User';
    public $controller_name = 'users';
    public $prefix = '_us';
    public $ad_prefix = '_ad';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/users_model');
    }
    // view users
    public function view(){
        $data['meta_title'] = $this->lang->line('title_admin_users').' | '.$this->lang->line('site_title');
        $data['selected'] = '';
        $this->load->view(ADMIN_URL.'/users',$data);
    }
    // view users
    public function driver(){
        $data['meta_title'] = $this->lang->line('title_admin_users').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/driver',$data);
    }
    // add users
    public function add(){
        $data['meta_title'] = $this->lang->line('title_admin_usersadd').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            if($this->input->post('user_type') != 'Driver'){
                $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
                $this->form_validation->set_rules('email','Email', 'trim|required|valid_email|is_unique[users.email]');
            }else{
                $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
            }
            $this->form_validation->set_rules('user_type','User Type', 'trim|required');
            $this->form_validation->set_rules('mobile_number','Phone Number', 'trim|required|numeric|is_unique[users.mobile_number]');
            //$this->form_validation->set_rules('phone_number','Phone Number', 'trim|required|numeric|is_unique[users.phone_number]');
            $this->form_validation->set_rules('password','Password', 'trim|required');
            $this->form_validation->set_rules('confirm_password','Confirm Password', 'trim|required|matches[password]');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
                $add_data = array(                  
                    'first_name'=>$this->input->post('first_name'),
                    'last_name' =>$this->input->post('last_name'),
                    'email' =>strtolower($this->input->post('email')),
                    'phone_number' =>$this->input->post('phone_number'),
                    'mobile_number' =>$this->input->post('mobile_number'),
                    'user_type' =>$this->input->post('user_type'),
                    'status' =>1,
                    'active'=>1,
                    'password' =>md5(SALT.$this->input->post('password')),
                    'created_by'=>$this->session->userdata("UserID")
                );                                           
                $this->users_model->addData('users',$add_data);
                if($this->input->post('email')){
                    $this->db->select('OptionValue');
                    $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                    $this->db->select('OptionValue');
                    $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                    $this->db->select('subject,message');
                    $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'user-added','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                    $arrayData = array('FirstName'=>$this->input->post('first_name'),'LoginLink'=>base_url().ADMIN_URL,'Email'=>$this->input->post('email'),'Password'=>$this->input->post('password'));
                    $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                    // if(!empty($EmailBody)){     
                    //     $this->load->library('email');  
                    //     $config['charset'] = 'iso-8859-1';  
                    //     $config['wordwrap'] = TRUE;  
                    //     $config['mailtype'] = 'html';  
                    //     $this->email->initialize($config);  
                    //     $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                    //     $this->email->to(trim($this->input->post('email'))); 
                    //     $this->email->subject($Emaildata->subject);  
                    //     $this->email->message($EmailBody);  
                    //     if(!$this->email->send()){
					// 		show_error($this->email->print_debugger());
					// 		die;
					// 	}
                    // } 
                }
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                if($this->input->post('user_type') == 'Driver'){
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/driver');  
                }else{
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');  
                }               
            }
        }       
        $this->load->view(ADMIN_URL.'/users_add',$data);
    }
    // edit users
    public function edit(){
        $data['meta_title'] = $this->lang->line('title_admin_usersedit').' | '.$this->lang->line('site_title');
        // check if form is submitted 
        if($this->input->post('submit_page') == "Submit")
        {
            if($this->input->post('user_type') != 'Driver'){
                $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
                $this->form_validation->set_rules('email','Email', 'trim|valid_email|callback_checkEmailExist');
            }else{
                $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
            }
            $this->form_validation->set_rules('user_type','User Type', 'trim|required');
            // $this->form_validation->set_rules('mobile_number','Phone Number', 'trim|numeric|callback_checkExist');
            //$this->form_validation->set_rules('phone_number','Phone Number', 'trim|numeric|callback_checkExistPhone');
            if($this->input->post('password')){
                 $this->form_validation->set_rules('password','Password', 'trim|required');
                 $this->form_validation->set_rules('confirm_password','Confirm Password', 'trim|required|matches[password]');
            }
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {   
                $data_edited = $this->users_model->getEditDetail('users',$this->input->post('entity_id'));
                $edit_data = array(  
                    'first_name'=>$this->input->post('first_name'),
                    'last_name' =>$this->input->post('last_name'),
                    'email' =>strtolower($this->input->post('email')),
                    'phone_number' =>$this->input->post('phone_number'),
                    'mobile_number' =>$this->input->post('mobile_number'),
                    'user_type' =>$this->input->post('user_type'),
                    'status' =>1,
                    'active'=>1,
                    'updated_by'=>$this->session->userdata("UserID"),
                    'updated_date'=>date('Y-m-d h:i:s')
                );
                if($this->input->post('password')){
                    $edit_data['password'] = md5(SALT.$this->input->post('password'));
                }
                $this->users_model->updateData($edit_data,'users','entity_id',$this->input->post('entity_id'));
                if($this->input->post('email') != $data_edited->email){
                    $this->db->select('OptionValue');
                    $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                    $this->db->select('OptionValue');
                    $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                    $this->db->select('subject,message');
                    $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'email-update-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                    $arrayData = array('FirstName'=>$this->input->post('first_name'),'Email'=>$this->input->post('email'),'Sender_Email'=>$data_edited->email);
                    $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                    if(!empty($EmailBody)){     
                        $this->load->library('email');  
                        $config['charset'] = 'iso-8859-1';  
                        $config['wordwrap'] = TRUE;  
                        $config['mailtype'] = 'html';  
                        $this->email->initialize($config);  
                        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                        $this->email->to(trim($this->input->post('email'))); 
                        $this->email->subject($Emaildata->subject);  
                        $this->email->message($EmailBody);  
                        if(!$this->email->send()){
							show_error($this->email->print_debugger());
							die;
						}
                    } 
                }
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                if($this->input->post('user_type') == 'Driver'){
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/driver');  
                }else{
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
                }                 
            }
        }   
        $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
        $data['edit_records'] = $this->users_model->getEditDetail('users',$entity_id);
        $this->load->view(ADMIN_URL.'/users_add',$data);
    }
    // call for ajax data
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'first_name',2=>'status',3=>'created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_type = '');
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $records["aaData"][] = array(
                $nCount,
                $val->first_name,
                $val->mobile_number,
                $val->user_type,
                ($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
                '<a class="btn btn-sm danger-btn margin-bottom" title="'.$this->lang->line('edit').'" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-edit"></i> '.$this->lang->line('edit').'</a> <button onclick="disableDetail('.$val->entity_id.','.$val->status.')"  title="'.$this->lang->line('click_for').' '.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($val->status?'times':'check').'"></i> '.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').'</button>'
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method to change user status
    public function ajaxdisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $emailData = $this->users_model->getEditDetail('users',$entity_id);
            $this->users_model->UpdatedStatus($entity_id,$this->input->post('status'));
            if($emailData->email != ''){
                if($this->input->post('status')==0){
                    $status = 'activated';
                } else {
                    $status = 'deactivated';
                }   
                $this->db->select('OptionValue');
                $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                $this->db->select('subject,message');
                $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'change-status-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                $arrayData = array('FirstName'=>$emailData->first_name,'Status'=>$status);
                $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                if(!empty($EmailBody)){     
                    $this->load->library('email');  
                    $config['charset'] = 'iso-8859-1';  
                    $config['wordwrap'] = TRUE;  
                    $config['mailtype'] = 'html';  
                    $this->email->initialize($config);  
                    $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                    $this->email->to(trim($emailData->email)); 
                    $this->email->subject($Emaildata->subject);  
                    $this->email->message($EmailBody);  
                    if(!$this->email->send()){
                        show_error($this->email->print_debugger());
                        die;
                    }
                } 
            }
        }
    }
    // method for deleting a user
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->users_model->deleteUser($this->input->post('table'),$entity_id);
    }
    // add address
    public function add_address(){
        $data['meta_title'] = $this->lang->line('title_admin_userAddressAdd').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('user_entity_id', 'User', 'trim|required');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            $this->form_validation->set_rules('landmark', 'Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude','Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude','Longitude', 'trim|required');
            $this->form_validation->set_rules('zipcode','Zipcode', 'trim|required|numeric');
            $this->form_validation->set_rules('country','Country', 'trim|required');
            $this->form_validation->set_rules('state','State', 'trim|required');
            $this->form_validation->set_rules('city','City', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
                $add_data = array(
                    'user_entity_id' =>  $this->input->post('user_entity_id'),                
                    'address'=>$this->input->post('address'),
                    'landmark' =>$this->input->post('landmark'),
                    'latitude' =>$this->input->post('latitude'),
                    'longitude' =>$this->input->post('longitude'),
                    'zipcode' =>$this->input->post('zipcode'),
                    'country' =>$this->input->post('country'),
                    'city' =>$this->input->post('city'),
                    'state' =>$this->input->post('state'),
                    'saved_status'=>($this->input->post('saved_status'))?$this->input->post('saved_status'):''
                );                                           
                $this->users_model->addData('user_address',$add_data);
                $this->session->set_flashdata('add_page_MSG', $this->lang->line('success_add'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view/user_address');                 
            }
        }
        $data['selected'] = 'user_address';
        $data['user_data'] = $this->users_model->getUsers();
        $this->load->view(ADMIN_URL.'/users_address_add',$data);
    }
    // edit address
    public function edit_address(){
        $data['meta_title'] = $this->lang->line('title_admin_userAddressEdit').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('user_entity_id', 'User', 'trim|required');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            $this->form_validation->set_rules('landmark', 'Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude','Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude','Longitude', 'trim|required');
            $this->form_validation->set_rules('zipcode','Zipcode', 'trim|required|numeric');
            $this->form_validation->set_rules('country','Country', 'trim|required');
            $this->form_validation->set_rules('state','State', 'trim|required');
            $this->form_validation->set_rules('city','City', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
                $edit_data = array(        
                    'user_entity_id' =>  $this->input->post('user_entity_id'),   
                    'address'=>$this->input->post('address'),
                    'landmark' =>$this->input->post('landmark'),
                    'latitude' =>$this->input->post('latitude'),
                    'longitude' =>$this->input->post('longitude'),
                    'zipcode' =>$this->input->post('zipcode'),
                    'country' =>$this->input->post('country'),
                    'city' =>$this->input->post('city'),
                    'state' =>$this->input->post('state'),
                    'saved_status'=>($this->input->post('saved_status'))?$this->input->post('saved_status'):''
                );                                           
                $this->users_model->updateData($edit_data,'user_address','entity_id',$this->input->post('entity_id'));
                $this->session->set_flashdata('add_page_MSG', $this->lang->line('success_update'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view/user_address');                 
            }
        }
        $data['user_data'] = $this->users_model->getUsers();
        $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
        $data['edit_records'] = $this->users_model->getEditDetail('user_address',$entity_id);
        $this->load->view(ADMIN_URL.'/users_address_add',$data);
    }
    // call for ajax data
    public function ajaxViewAddress() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'first_name',2=>'address',3=>'status');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getAddressGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $records["aaData"][] = array(
                $nCount,
                $val->first_name.' '.$val->last_name,
                $val->address,
                '<a class="btn btn-sm danger-btn margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_address/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-edit"></i> '.$this->lang->line('edit').'</a> <button onclick="deleteAddress('.$val->entity_id.')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button>'
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function checkExist(){
        $mobile_number = ($this->input->post('mobile_number') != '')?$this->input->post('mobile_number'):'';
        if($this->input->post('first_name')){
            if($mobile_number != ''){
                $check = $this->users_model->checkExist($mobile_number,$this->input->post('entity_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkExist', $this->lang->line('phone_exist'));
                    return false;
                }
            } 
        }else{
            if($mobile_number != ''){
                $check = $this->users_model->checkExist($mobile_number,$this->input->post('entity_id'));
                echo $check;
            } 
        }       
    }

    public function checkExistPhone(){ 
        $phone_number = ($this->input->post('phone_number') != '')?$this->input->post('phone_number'):'';
        if($this->input->post('first_name')){
            if($phone_number != ''){
                $check = $this->users_model->checkExistPhone($phone_number,$this->input->post('entity_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkExistPhone', $this->lang->line('phone_exist'));
                    return false;
                }
            } 
        }else{
            if($phone_number != ''){
                $check = $this->users_model->checkExistPhone($phone_number,$this->input->post('entity_id'));
                echo $check;
            } 
        }
    }
    
    public function checkEmailExist(){
        $email = ($this->input->post('email') != '')?$this->input->post('email'):'';
        if($this->input->post('first_name')){
            if($email != ''){
                $check = $this->users_model->checkEmailExist($email,$this->input->post('entity_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkEmailExist', $this->lang->line('alredy_exist'));
                    return false;  
                }
            }
        }else{
            if($email != ''){
                $check = $this->users_model->checkEmailExist($email,$this->input->post('entity_id'));
                echo $check;
            }  
        }
    }
    //driver view
    public function ajaxdriverview(){
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'first_name',2=>'shop.name',3=>'phone_number',3=>'status',4=>'created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_type = 'Driver');
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $edit = ($this->session->userdata('UserType') == 'MasterAdmin')?'<a class="btn btn-sm danger-btn margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'/driver" title='.$this->lang->line('edit').'><i class="fa fa-edit"></i> '.$this->lang->line('edit').'</a><button onclick="disableDetail('.$val->entity_id.','.$val->status.')"  title="'.$this->lang->line('click_for').' '.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($val->status?'times':'check').'"></i> '.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').'</button> ':'';
            $commission = ($val->user_type == 'Driver')?'<a class="btn btn-sm danger-btn margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/commission/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-money"></i> '.$this->lang->line('commission').'</a>':'';
            $review = '<a class="btn btn-sm danger-btn margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/review/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-star"></i> '.$this->lang->line('review').'</a>';
            $records["aaData"][] = array(
                $nCount,
                $val->first_name,
                //$val->name,
                $val->mobile_number,
                ($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit.$commission.$review.''
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //commission view
    public function commission(){
        $data['meta_title'] = $this->lang->line('title_admin_commission').' | '.$this->lang->line('site_title');
        $data['entity_id'] = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):'';
        $this->load->view(ADMIN_URL.'/commission',$data);
    }
    //ajax view
    public function ajaxcommission(){
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $user_id = $this->uri->segment(4);
        $sortfields = array(1=>'first_name',2=>'last_name',3=>'date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getCommissionDetail($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_id);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $shop = unserialize($val->shop_detail);
            $disableCheckbox = ($val->commission_status == 'Paid')?'disabled':'';
            $records["aaData"][] = array(
                '<input type="checkbox" '.$disableCheckbox.' name="ids[]" value="'.$val->driver_map_id.'">',
                $val->first_name.' '.$val->last_name,
                ($shop)?$shop->name:'',
                $val->commission,
                ($val->date)?date('m-d-Y',strtotime($val->date)):'',
                ($val->commission_status)?$val->commission_status:''
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //commission view
    public function review(){
        $data['meta_title'] = $this->lang->line('title_admin_review').' | '.$this->lang->line('site_title');
        $data['entity_id'] = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):'';
        $this->load->view(ADMIN_URL.'/driver_review',$data);
    }
    //ajax view
    public function ajaxDriverReview(){
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $user_id = $this->uri->segment(4);
        $sortfields = array(1=>'first_name',2=>'review',3=>'rating',4=>'review.created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getDriverReviewDetail($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_id);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $shop = unserialize($val->shop_detail);
            $records["aaData"][] = array(
                $nCount,
                $val->first_name.' '.$val->last_name,
                $val->review,
                $val->rating,
                ($val->created_date)?date('m-d-Y',strtotime($val->created_date)):'',
                '-'
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    /*
    * Multiple commission pay
    */
    public function commission_pay(){ 
        $commisionIDs = @explode(",",$this->input->post('arrayData'));
        if(!empty($commisionIDs)){
           $count = $this->users_model->payCommision($commisionIDs);
        }
    }

    //generate report
    public function generate_report(){
        $user_type = $this->input->post('user_type');
        $results = $this->users_model->generate_report($user_type); 
        if(!empty($results)){
            // export as an excel sheet
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('Reports');
            $headers = array("User Name","Phone Number","User Type","Status");

            for($h=0,$c='A'; $h<count($headers); $h++,$c++)
            {
                $this->excel->getActiveSheet()->setCellValue($c.'1', $headers[$h]);
                $this->excel->getActiveSheet()->getStyle($c.'1')->getFont()->setBold(true);
            }
            $row = 2;
            for($r=0; $r<count($results); $r++){ 
                $status = ($results[$r]->status == 1)?'Active':'Deactive';
                $this->excel->getActiveSheet()->setCellValue('A'.$row, $results[$r]->first_name.' '.$results[$r]->last_name);
                $this->excel->getActiveSheet()->setCellValue('B'.$row, $results[$r]->mobile_number);
                $this->excel->getActiveSheet()->setCellValue('C'.$row, $results[$r]->user_type);
                $this->excel->getActiveSheet()->setCellValue('D'.$row, $status);                
            $row++;
            }
            $filename = 'report-export.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache   
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
            
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');  
            exit;
        }else{
            $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
            redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
        }
    }

    //generate report
    public function driver_generate_report(){
        $user_type = 'Driver';
        $results = $this->users_model->generate_report($user_type); 
        if(!empty($results)){
            // export as an excel sheet
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('Reports');
            $headers = array("User Name","Phone Number","User Type","Status");

            for($h=0,$c='A'; $h<count($headers); $h++,$c++)
            {
                $this->excel->getActiveSheet()->setCellValue($c.'1', $headers[$h]);
                $this->excel->getActiveSheet()->getStyle($c.'1')->getFont()->setBold(true);
            }
            $row = 2;
            for($r=0; $r<count($results); $r++){ 
                $status = ($results[$r]->status == 1)?'Active':'Deactive';
                $this->excel->getActiveSheet()->setCellValue('A'.$row, $results[$r]->first_name.' '.$results[$r]->last_name);
                $this->excel->getActiveSheet()->setCellValue('B'.$row, $results[$r]->mobile_number);
                $this->excel->getActiveSheet()->setCellValue('C'.$row, $results[$r]->user_type);
                $this->excel->getActiveSheet()->setCellValue('D'.$row, $status);                
            $row++;
            }
            $filename = 'report-export.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache   
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
            
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');  
            exit;
        }else{
            $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
            redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
        }
    }
}
?>
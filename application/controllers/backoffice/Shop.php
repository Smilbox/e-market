<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Shop extends CI_Controller {
    public $controller_name = 'shop';
    public $prefix = '_re'; 
    public $menu_prefix = '_menu';
    public $package_prefix = '_pac';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/restaurant_model');
        $this->load->model(ADMIN_URL.'/store_type_model');
        $this->load->model(ADMIN_URL.'/sub_store_type_model');
    }
    // view restaurant
    public function view(){
    	$data['meta_title'] = $this->lang->line('title_admin_shop').' | '.$this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();     
        $this->load->view(ADMIN_URL.'/restaurant',$data);
    }
    // add restaurant
    public function add(){
        $data['meta_title'] = $this->lang->line('title_admin_shopadd').' | '.$this->lang->line('site_title');
    	if($this->input->post('submit_page') == "Submit")
        {   
            $this->form_validation->set_rules('name', 'Restaurant Name', 'trim|required');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|callback_checkExist');
            $this->form_validation->set_rules('email','Email', 'trim|valid_email|callback_checkEmailExist');
            $this->form_validation->set_rules('capacity','Capacity', 'trim|required');
            $this->form_validation->set_rules('no_of_table','No of table', 'trim|required');
            $this->form_validation->set_rules('address','Address', 'trim|required');
            $this->form_validation->set_rules('landmark','Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude','Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude','Longitude', 'trim|required');
            $this->form_validation->set_rules('state','State', 'trim|required');
            $this->form_validation->set_rules('country','Country','trim|required');
            $this->form_validation->set_rules('city','City', 'trim|required');
            $this->form_validation->set_rules('zipcode','Zipcode', 'trim|required');
            $this->form_validation->set_rules('store-type','Store type', 'trim|required');
            $this->form_validation->set_rules('enable_hours','Enable Hours', 'trim|required');
            $this->form_validation->set_rules('object-fit','Display image', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
                if(!$this->input->post('content_id')){
                    //ADD DATA IN CONTENT SECTION
                    $add_content = array(
                      'content_type'=>$this->uri->segment('2'),
                      'created_by'=>$this->session->userdata("UserID"),  
                      'created_date'=>date('Y-m-d H:i:s')                      
                    );
                    $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                    $shop_slug = slugify($this->input->post('name'),'restaurant','shop_slug');
                }else{                    
                    $ContentID = $this->input->post('content_id');
                    $slug = $this->restaurant_model->getRestaurantSlug($this->input->post('content_id'));
                    $shop_slug = $slug->shop_slug;
                }
                $currency_id = $this->common_model->getCurrencyID('Ariary');
                $add_data = array(                  
                    'name'=>$this->input->post('name'),
                    'shop_slug'=>$shop_slug,
                    'currency_id' =>$currency_id->currency_id,
                    'phone_number' =>$this->input->post('phone_number'),
                    'email' =>$this->input->post('email'),
                    'capacity' =>$this->input->post('capacity'),
                    'no_of_table' =>$this->input->post('no_of_table'),
                    'no_of_hall' =>$this->input->post('no_of_hall'),
                    'hall_capacity' =>$this->input->post('hall_capacity'),
                    'enable_hours'=>$this->input->post("enable_hours"),
                    'status'=>1,
                    'content_id'=>$ContentID,
                    'language_slug'=>$this->uri->segment('4'),
                    'created_by' => $this->session->userdata('UserID'),
                    'is_veg'=>($this->input->post('is_veg') != '')?$this->input->post('is_veg'):NULL,
                    'driver_commission'=>$this->input->post('driver_commission'),
                    'store_type_id' => $this->input->post('store-type'),
                    'sub_store_type_id' => implode(',', $this->input->post('sub-store-type')),
                    'object_fit' => $this->input->post('object-fit'),
                    'allow_24_delivery' => $this->input->post('allow_24_delivery'),
                    'flat_rate_24' => $this->input->post('flat_rate_24'),
                );     
                if(!empty($this->input->post('timings'))){
                    $timingsArr = $this->input->post('timings');
                    $newTimingArr = array();
                    foreach($timingsArr as $key=>$value) {
                        if(isset($value['off'])) {
                            $newTimingArr[$key]['open'] = '';
                            $newTimingArr[$key]['close'] = '';
                            $newTimingArr[$key]['off'] = '0';
                        } else {
                            if(!empty($value['open']) && !empty($value['close'])) {
                                $newTimingArr[$key]['open'] = $value['open'];
                                $newTimingArr[$key]['close'] = $value['close'];
                                $newTimingArr[$key]['off'] = '1';
                            } else {
                                $newTimingArr[$key]['open'] = '';
                                $newTimingArr[$key]['close'] = '';
                                $newTimingArr[$key]['off'] = '0';
                            }
                        }
                    }
                    $add_data['timings'] = serialize($newTimingArr); 
                }                                        
                if (!empty($_FILES['Image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/restaurant';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/restaurant')) {
                      @mkdir('./uploads/restaurant', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('Image'))
                    {
                      $img = $this->upload->data();
                      $add_data['image'] = "restaurant/".$img['file_name'];    
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                if (!empty($_FILES['FeaturedImage']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/restaurant';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/restaurant')) {
                        @mkdir('./uploads/restaurant', 0777, TRUE);
                    }
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('FeaturedImage'))
                    {
                        $img = $this->upload->data();
                        $add_data['featured_image'] = "restaurant/".$img['file_name'];
                    }
                    else
                    {
                        $data['Error'] = $this->upload->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                $entity_id = '';
                if(empty($data['Error'])){
                    $entity_id = $this->restaurant_model->addData('restaurant',$add_data);
                     //for address
                    $add_data = array(
                        'resto_entity_id'=>$entity_id,
                        'address' =>$this->input->post('address'),
                        'landmark' =>$this->input->post('landmark'),
                        'latitude' =>$this->input->post('latitude'),
                        'longitude'=>$this->input->post("longitude"),
                        'state'=>$this->input->post("state"),
                        'country'=>$this->input->post("country"),
                        'city'=>$this->input->post("city"),
                        'zipcode'=>$this->input->post("zipcode"),
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                    );
                    $this->restaurant_model->addData('restaurant_address',$add_data);
                    if($this->session->userdata('adminemail')){
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                        $this->db->select('subject,message');
                        $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'new-restaurant-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();

                        $arrayData = array('FirstName'=>$this->session->userdata('adminFirstname'),'restaurant_name'=>$this->input->post('name'));
                        $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                        if(!empty($EmailBody)){     
                            $this->load->library('email');  
                            $config['charset'] = 'iso-8859-1';  
                            $config['wordwrap'] = TRUE;  
                            $config['mailtype'] = 'html';  
                            $this->email->initialize($config);  
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                            $this->email->to(trim($this->session->userdata('adminemail'))); 
                            $this->email->subject($Emaildata->subject);  
                            $this->email->message($EmailBody);  
                            if(!$this->email->send()){
                                show_error($this->email->print_debugger());
                                die;
                            }   
                        } 
                    }
                    //get restaurant ans set in session
                    $restaurant = $this->common_model->getRestaurantinSession('restaurant',$this->session->userdata('UserID'));
                    if(!empty($restaurant))
                    {
                        $restaurant = array_column($restaurant, 'entity_id');
                        $this->session->set_userdata('restaurant',$restaurant);
                    }
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');             
                }
                   
            }
        }
        $data['currencies'] = $this->common_model->getCountriesCurrency();
        if (!empty($this->uri->segment('5'))) {
            $getRestaurantCurrency = $this->common_model->getRestaurantCurrency($this->uri->segment('5'));
            $data['res_currency_id'] = $getRestaurantCurrency->currency_id;
        }
        $data['store_types'] = $this->store_type_model->getAll();
        $data['sub_store_types'] = $this->sub_store_type_model->getAll();
    	$this->load->view(ADMIN_URL.'/restaurant_add',$data);
    }
    // edit restaurant
    public function edit(){
    	$data['meta_title'] = $this->lang->line('title_admin_shopedit').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {   
            $this->form_validation->set_rules('name', 'Restaurant Name', 'trim|required');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|callback_checkExist');
            $this->form_validation->set_rules('email','Email', 'trim|valid_email|callback_checkEmailExist');
            $this->form_validation->set_rules('capacity','Capacity', 'trim|required|numeric');
            $this->form_validation->set_rules('no_of_table','No of table', 'trim|required|numeric');
            $this->form_validation->set_rules('address','Address', 'trim|required');
            $this->form_validation->set_rules('landmark','Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude','Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude','Longitude', 'trim|required');
            $this->form_validation->set_rules('state','State', 'trim|required');
            $this->form_validation->set_rules('country','Country','trim|required');
            $this->form_validation->set_rules('city','City', 'trim|required');
            $this->form_validation->set_rules('zipcode','Zipcode', 'trim|required');
            $this->form_validation->set_rules('store-type','Store type', 'trim|required');
            $this->form_validation->set_rules('enable_hours','Enable Hours', 'trim|required');
            $this->form_validation->set_rules('object-fit','Display image', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
                $content_id = $this->restaurant_model->getContentId($this->input->post('entity_id'),'restaurant');
                $slug = $this->restaurant_model->getRestaurantSlug($this->input->post('content_id'));
                if (!empty($slug->shop_slug)) { 
                    $shop_slug = $slug->shop_slug;
                }
                else
                {
                    $shop_slug = slugify($this->input->post('name'),'restaurant','shop_slug','content_id',$content_id->content_id);
                }
                $edit_data = array(                  
                    'name'=>$this->input->post('name'),
                    'shop_slug'=>$shop_slug,
                    'phone_number' =>$this->input->post('phone_number'),
                    'email' =>$this->input->post('email'),
                    'capacity' =>$this->input->post('capacity'),
                    'no_of_table' =>$this->input->post('no_of_table'),
                    'no_of_hall' =>$this->input->post('no_of_hall'),
                    'hall_capacity' =>$this->input->post('hall_capacity'),
                    'enable_hours'=>$this->input->post("enable_hours"),
                    'status'=>1,
                    'updated_by' => $this->session->userdata('UserID'),
                    'store_type_id' => $this->input->post('store-type'),
                    'sub_store_type_id' => implode(',', $this->input->post('sub-store-type')),
                    'updated_date'=>date('Y-m-d H:i:s'),
                    'is_veg'=>($this->input->post('is_veg') != '')?$this->input->post('is_veg'):NULL,
                    'driver_commission'=>$this->input->post('driver_commission'),
                    'allow_24_delivery' => $this->input->post('allow_24_delivery'),
                    'flat_rate_24' => $this->input->post('flat_rate_24'),
                    'object_fit' => $this->input->post('object-fit')
                );    
                if(!empty($this->input->post('timings'))){
                    $timingsArr = $this->input->post('timings');
                    $newTimingArr = array();
                    foreach($timingsArr as $key=>$value) {
                        if(isset($value['off'])) {
                            $newTimingArr[$key]['open'] = '';
                            $newTimingArr[$key]['close'] = '';
                            $newTimingArr[$key]['off'] = '0';
                        } else {
                            if(!empty($value['open']) && !empty($value['close'])) {
                                $newTimingArr[$key]['open'] = $value['open'];
                                $newTimingArr[$key]['close'] = $value['close'];
                                $newTimingArr[$key]['off'] = '1';
                            } else {
                                $newTimingArr[$key]['open'] = '';
                                $newTimingArr[$key]['close'] = '';
                                $newTimingArr[$key]['off'] = '0';
                            }
                        }
                    }
                    $edit_data['timings'] = serialize($newTimingArr); 
                }                                        
                
                if (!empty($_FILES['Image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/restaurant';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/restaurant')) {
                      @mkdir('./uploads/restaurant', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('Image'))
                    {
                      $img = $this->upload->data();
                      $edit_data['image'] = "restaurant/".$img['file_name'];   
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
                
                if (!empty($_FILES['FeaturedImage']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/restaurant';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/restaurant')) {
                        @mkdir('./uploads/restaurant', 0777, TRUE);
                    }
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('FeaturedImage'))
                    {
                        $img = $this->upload->data();
                        $edit_data['featured_image'] = "restaurant/".$img['file_name'];
                        if($this->input->post('uploaded_imagefeatured')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_imagefeatured'));
                        }
                    }
                    else
                    {
                        $data['Error'] = $this->upload->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                
                if(empty($data['Error'])){
                    $this->restaurant_model->updateData($edit_data,'restaurant','entity_id',$this->input->post('entity_id'));
                     //for address
                    $edit_data = array(
                        'resto_entity_id'=>$this->input->post('entity_id'),
                        'address' =>$this->input->post('address'),
                        'landmark' =>$this->input->post('landmark'),
                        'latitude' =>$this->input->post('latitude'),
                        'longitude'=>$this->input->post("longitude"),
                        'state'=>$this->input->post("state"),
                        'country'=>$this->input->post("country"),
                        'city'=>$this->input->post("city"),
                        'zipcode'=>$this->input->post("zipcode"),
                    );
                    $this->restaurant_model->updateData($edit_data,'restaurant_address','resto_entity_id',$this->input->post('entity_id'));
                    if($this->session->userdata('adminemail')){
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                        $this->db->select('subject,message');
                        $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'restaurant-details-update-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                        $arrayData = array('FirstName'=>$this->session->userdata('adminFirstname'),'restaurant_name'=>$this->input->post('name'));
                        $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                        if(!empty($EmailBody)){     
                            $this->load->library('email');  
                            $config['charset'] = 'iso-8859-1';  
                            $config['wordwrap'] = TRUE;  
                            $config['mailtype'] = 'html';  
                            $this->email->initialize($config);  
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                            $this->email->to(trim($this->session->userdata('adminemail'))); 
                            $this->email->subject($Emaildata->subject);  
                            $this->email->message($EmailBody);  
                            if(!$this->email->send()){
                                show_error($this->email->print_debugger());
                                die;
                            } 
                        } 
                    }
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
                }
                     
            }
        }
        $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
        $data['edit_records'] = $this->restaurant_model->getEditDetail('restaurant',$entity_id);
        $data['currencies'] = $this->common_model->getCountriesCurrency();
        $data['store_types'] = $this->store_type_model->getAll();
        $data['sub_store_types'] = $this->sub_store_type_model->getAll();
        $this->load->view(ADMIN_URL.'/restaurant_add',$data);
    }
    // call for ajax data
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'name',2=>'status',3=>'created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->restaurant_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);

        $Languages = $this->common_model->getLanguages();        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $cnt = 0;
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access = '<button onclick="deleteAll('.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom red"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button>';
            $edit_active_access .= '<button onclick="disableAll('.$value['content_id'].','.$value['status'].')"  title="'.$this->lang->line('click_for').' ' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($value['status']?'times':'check').'"></i> '.($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'</button>';
            $records["aaData"][] = array(
                $nCount,
                $value['name'],
                ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>
                    <a style="cursor:pointer;" onclick="disablePage('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['translations'][$lang->language_slug]['status'].')"  title="Click here to '.($value['translations'][$lang->language_slug]['status']?'Inactive':'Activate').'"><i class="fa fa-toggle-'.($value['translations'][$lang->language_slug]['status']?'on':'off').'"></i> </a>
                    <a style="cursor:pointer;" onclick="deleteDetail('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'"><i class="fa fa-times"></i> </a>
                    ( '.$value['translations'][$lang->language_slug]['name'].' )';
                }else{
                    $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>';
                }                    
            }
            // added to specific position
            array_splice( $records["aaData"][$cnt], 2, 0, $cusLan);
            $cnt++;
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    /*
     * Update status for Single 
     */
    // method to change restaurant status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->restaurant_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
        }
    }
    // method for deleting a restaurant
    public function ajaxDelete(){
    	$entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->restaurant_model->ajaxDelete($this->input->post('tblname'),$this->input->post('content_id'),$entity_id);
    }
    public function ajaxDeleteAll(){
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $this->restaurant_model->ajaxDeleteAll($this->input->post('tblname'),$content_id);
    }
    // view restaurant menu
    public function view_menu(){
        $data['meta_title'] = $this->lang->line('title_admin_shop_menu').' | '.$this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$this->session->userdata('language_slug'));
        $this->load->view(ADMIN_URL.'/restaurant_menu',$data);
    }
    //add menu
    public function add_menu(){
        $data['meta_title'] = $this->lang->line('title_admin_shop_menu_add').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('name', 'Menu Name', 'trim|required');
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('category_id','Category', 'trim|required');
            if($this->input->post('check_add_ons') != 1){
                $this->form_validation->set_rules('price','Price', 'trim|required');
            }
            $this->form_validation->set_rules('menu_detail','Detail', 'trim|required');
            $this->form_validation->set_rules('availability[]','Availability', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
                if(!empty($this->input->post('content_id')))
                {
                    $ContentID = $this->input->post('content_id');
                    $slug = $this->restaurant_model->getItemSlug($this->input->post('content_id'));
                    $item_slug = $slug->item_slug;
                }
                else
                {   
                    //ADD DATA IN CONTENT SECTION
                    $add_content = array(
                      'content_type'=>'menu',
                      'created_by'=>$this->session->userdata("UserID"),  
                      'created_date'=>date('Y-m-d H:i:s')                      
                    );
                    $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                    $item_slug = slugify($this->input->post('name'),'restaurant_menu_item','item_slug');               
                }
                $add_data = array(                  
                    'name'=>$this->input->post('name'),
                    'item_slug'=>$item_slug,
                    'restaurant_id' =>$this->input->post('restaurant_id'),
                    'category_id' =>$this->input->post('category_id'),
                    'price' =>($this->input->post('price'))?$this->input->post('price'):NULL,
                    'menu_detail' =>$this->input->post('menu_detail'),
                    'popular_item' =>($this->input->post('popular_item'))?$this->input->post('popular_item'):'0',
                    'availability'=>implode(',', $this->input->post("availability")),
                    'status'=>1,
                    'content_id'=>$ContentID,
                    'language_slug'=>$this->uri->segment('4'),
                    'created_by' => $this->session->userdata('UserID'),
                    'is_veg'=>$this->input->post('is_veg'),
                    'check_add_ons'=>($this->input->post('check_add_ons'))?$this->input->post('check_add_ons'):0
                ); 
                if (!empty($_FILES['Images']['name']) && count(array_filter($_FILES['Images']['name'])) > 0)
                {
                    $filesCount = count($_FILES['Images']['name']);

                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/menu';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '12288'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/menu')) {
                      @mkdir('./uploads/menu', 0777, TRUE);
                    }
                    $this->upload->initialize($config);

                    $uploadData = array();

                    for($i = 0; $i < $filesCount; $i++){
                        $_FILES['file']['name']     = $_FILES['Images']['name'][$i]; 
                        $_FILES['file']['type']     = $_FILES['Images']['type'][$i]; 
                        $_FILES['file']['tmp_name'] = $_FILES['Images']['tmp_name'][$i]; 
                        $_FILES['file']['error']    = $_FILES['Images']['error'][$i]; 
                        $_FILES['file']['size']     = $_FILES['Images']['size'][$i]; 
                        if ($this->upload->do_upload('file'))
                        {
                          $img = $this->upload->data();
                          $uploadData[$i]['file_name'] = "/menu".$img['file_name']; 
                          $uploadData[$i]['uploaded_on'] = date("Y-m-d H:i:s");
                          $add_data['image_group'] = $add_data['image_group'].' '."menu/".$img['file_name'];
                        }
                        else
                        {
                            $data['Error'] = $this->upload->display_errors();
                            $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                        }
                    }
                    $add_data['image'] = $uploadData[0]['file_name'];   
                }
                /*if (!empty($_FILES['Image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/menu';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '12288'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/menu')) {
                      @mkdir('./uploads/menu', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('Image'))
                    {
                      $img = $this->upload->data();
                      $add_data['image'] = "menu/".$img['file_name'];   
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }*/
                if(empty($data['Error'])){
                    $menu_id = $this->restaurant_model->addData('restaurant_menu_item',$add_data);
                    if($this->input->post('check_add_ons') == 1){
                        if(!empty($this->input->post('add_ons_list'))){
                            $addons = array();
                            foreach ($this->input->post('add_ons_list') as $key => $value) {
                                foreach ($value as $k => $val) {
                                    if($val['add_ons_name'] != '' && $val['add_ons_price'] != ''){
                                        $addons[] = array(
                                            'menu_id'=>$menu_id,
                                            'category_id'=>$key,
                                            'add_ons_name'=>$val['add_ons_name'],
                                            'add_ons_price'=>$val['add_ons_price'],
                                            'is_multiple'=>($this->input->post('is_multiple')[$key])?$this->input->post('is_multiple')[$key]:0
                                        );
                                    }
                                }
                            }
                        }
                        $this->restaurant_model->inserBatch('add_ons_master',$addons);
                    }
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view_menu');               
                }                                        
                 
            }
        }
        $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$language_slug);
        $data['category'] = $this->restaurant_model->getListData('category',$language_slug);
        $data['addons_category'] = $this->restaurant_model->getListData('add_ons_category',$language_slug);
        $this->load->view(ADMIN_URL.'/restaurant_menu_add',$data);
    }
    //edit menu
    public function edit_menu(){

        $data['meta_title'] = $this->lang->line('title_admin_shop_menu_edit').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('name', 'Menu Name', 'trim|required');
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('category_id','Category', 'trim|required');
            if($this->input->post('check_add_ons') != 1){
                $this->form_validation->set_rules('price','Price', 'trim|required');
            }
            $this->form_validation->set_rules('menu_detail','Detail', 'trim|required');
            $this->form_validation->set_rules('availability[]','Availability', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
                $content_id = $this->restaurant_model->getContentId($this->input->post('entity_id'),'restaurant_menu_item');
                $slug = $this->restaurant_model->getItemSlug($this->input->post('content_id'));
                if (!empty($slug->item_slug)) { 
                    $item_slug = $slug->item_slug;
                }
                else
                {
                    $item_slug = slugify($this->input->post('name'),'restaurant_menu_item','item_slug','content_id',$content_id->content_id);
                }
                $edit_data = array(                  
                    'name'=>$this->input->post('name'),
                    'item_slug'=>$item_slug,
                    'restaurant_id' =>$this->input->post('restaurant_id'),
                    'category_id' =>$this->input->post('category_id'),
                    'price' =>($this->input->post('price'))?$this->input->post('price'):NULL,
                    'menu_detail' =>$this->input->post('menu_detail'),
                    'popular_item' =>($this->input->post('popular_item'))?$this->input->post('popular_item'):'0',
                    'availability'=>implode(',', $this->input->post("availability")),
                    'updated_by' => $this->session->userdata('UserID'),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'is_veg'=>$this->input->post('is_veg'),
                    'check_add_ons'=>($this->input->post('check_add_ons'))?$this->input->post('check_add_ons'):0
                );
                if (!empty($_FILES['Images']['name']) && count(array_filter($_FILES['Images']["name"])) > 0)
                {
                    $filesCount = count($_FILES['Images']['name']);

                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/menu';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '12288'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/menu')) {
                      @mkdir('./uploads/menu', 0777, TRUE);
                    }
                    $this->upload->initialize($config);

                    $uploadData = array();

                    for($i = 0; $i < $filesCount; $i++){
                        $_FILES['file']['name']     = $_FILES['Images']['name'][$i]; 
                        $_FILES['file']['type']     = $_FILES['Images']['type'][$i]; 
                        $_FILES['file']['tmp_name'] = $_FILES['Images']['tmp_name'][$i]; 
                        $_FILES['file']['error']    = $_FILES['Images']['error'][$i]; 
                        $_FILES['file']['size']     = $_FILES['Images']['size'][$i]; 
                        if ($this->upload->do_upload('file'))
                        {
                          $img = $this->upload->data();
                          $uploadData[$i]['file_name'] = "menu/".$img['file_name']; 
                          $uploadData[$i]['uploaded_on'] = date("Y-m-d H:i:s");
                          $edit_data['image_group'] = $edit_data['image_group'].' '."menu/".$img['file_name'];
                        }
                        else
                        {
                            $data['Error'] = $this->upload->display_errors();
                            $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                        }
                    }
                    $edit_data['image'] = $uploadData[0]['file_name'];   
                }
                /*if (!empty($_FILES['Image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/menu';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '12288'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/menu')) {
                      @mkdir('./uploads/menu', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('Image'))
                    {
                      $img = $this->upload->data();
                      $edit_data['image'] = "menu/".$img['file_name'];   
                      if($this->input->post('uploaded_image')){
                        @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                      } 
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }*/    
                if(empty($data['Error'])){                            
                    $this->restaurant_model->updateData($edit_data,'restaurant_menu_item','entity_id',$this->input->post('entity_id'));
                    $addons = array();
                    if($this->input->post('check_add_ons') == 1){
                        if(!empty($this->input->post('add_ons_list'))){
                            foreach ($this->input->post('add_ons_list') as $key => $value) {
                                if(in_array($key,$this->input->post('addons_category_id'))){
                                    foreach ($value as $k => $val) {
                                        if($val['add_ons_name'] != '' && $val['add_ons_price'] != ''){
                                            $addons[] = array(
                                                'menu_id'=>$this->input->post('entity_id'),
                                                'category_id'=>$key,
                                                'add_ons_name'=>$val['add_ons_name'],
                                                'add_ons_price'=>$val['add_ons_price'],
                                                'is_multiple'=>($this->input->post('is_multiple')[$key])?$this->input->post('is_multiple')[$key]:0
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $this->restaurant_model->deleteinsertBatch('add_ons_master',$addons,$this->input->post('entity_id'));
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view_menu');       
                }          
            }
        }
        $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$language_slug);
        $data['category'] = $this->restaurant_model->getListData('category',$language_slug);
        $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
        $data['edit_records'] = $this->restaurant_model->getEditDetail('restaurant_menu_item',$entity_id);
        $data['list_images'] = array();
        if(!empty($data['edit_records']->image_group))
        {
            $images = explode(' ', $data['edit_records']->image_group);
            foreach($images as $key => $img)
            {
                if($img != "")
                {
                    $data['list_images'][$key] = trim($img);
                }
            }
        }
        $data['add_ons_detail'] = $this->restaurant_model->getAddonsDetail('add_ons_master',$entity_id); 
        $data['addons_category'] = $this->restaurant_model->getListData('add_ons_category',$language_slug);
        $this->load->view(ADMIN_URL.'/restaurant_menu_add',$data);
    }
    // call for ajax data
    public function ajaxviewMenu() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(4=>'menu.price',5=>'res.name',6=>'menu.created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->restaurant_model->getMenuGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        //echo '<pre>'; print_r($grid_data); exit;
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        $Languages = $this->common_model->getLanguages();
        $ItemDiscount = $this->common_model->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items')); 
        $couponAmount = $ItemDiscount['couponAmount'];
        $ItemDiscount = (!empty($ItemDiscount['itemDetail']))?array_column($ItemDiscount['itemDetail'], 'item_id'):array();
        foreach ($grid_data['data'] as $key => $value) {
            $total = 0;
            if(in_array($value['entity_id'],$ItemDiscount)){
                if(!empty($couponAmount)){
                    if($couponAmount[0]['max_amount'] < $value['price']){ 
                        if($couponAmount[0]['amount_type'] == 'Percentage'){
                            $total = $value['price'] - round(($value['price'] * $couponAmount[0]['amount'])/100);
                        }else if($couponAmount[0]['amount_type'] == 'Amount'){
                            $total = $value['price'] - $couponAmount[0]['amount'];
                        }
                    }
                }
            }
            $edit_active_access = '<button onclick="deleteAll('.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom red"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button>';
            $edit_active_access .='<button onclick="disableAll('.$value['content_id'].','.$value['status'].')"  title="'.$this->lang->line('click_for').' ' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($value['status']?'times':'check').'"></i> '.($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'</button>';
            //$price = ($total && $total > 0)?"<strike>".number_format_unchanged_precision($value['price'])."</strike> ".number_format_unchanged_precision($total):number_format_unchanged_precision($value['price']);
            $currency_symbol = $this->common_model->getCurrencySymbol($value['currency_id']);
            $records["aaData"][] = array(
                $nCount,
                $value['name'],
                ($value['check_add_ons'])?'Customized':($currency_symbol->currency_symbol.number_format_unchanged_precision($value['price'],$currency_symbol->currency_code)),
                $value['rname'],
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_menu/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>
                    <a style="cursor:pointer;" onclick="disable_record('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['translations'][$lang->language_slug]['status'].')"  title="'.$this->lang->line('click_for').' ' .($value['translations'][$lang->language_slug]['status']?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').'"><i class="fa fa-toggle-'.($value['translations'][$lang->language_slug]['status']?'on':'off').'"></i> </a>
                    <a style="cursor:pointer;" onclick="deleteDetail('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'"><i class="fa fa-times"></i> </a>
                    ( '.$value['translations'][$lang->language_slug]['name'].' )';
                }else{
                    $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add_menu/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>';
                }                    
            }
            // added to specific position
            array_splice( $records["aaData"][$cnt], 2, 0, $cusLan);
            $cnt++;
            $nCount++;
        }                
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    /*
     * Update status for All
     */
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->restaurant_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
        }
    }
    public function checkExist(){
        $phone_number = ($this->input->post('phone_number') != '')?$this->input->post('phone_number'):'';
        if($this->input->post('name')){
            if($phone_number != ''){
                $check = $this->restaurant_model->checkExist($phone_number,$this->input->post('entity_id'),$this->input->post('content_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkExist', $this->lang->line('phones_exist'));
                    return false;
                }
            } 
        }else{
            if($phone_number != ''){
                $check = $this->restaurant_model->checkExist($phone_number,$this->input->post('entity_id'),$this->input->post('content_id'));
                echo $check;
            } 
        }
       
    }
    public function checkEmailExist(){
        $email = ($this->input->post('email') != '')?$this->input->post('email'):'';
        if($this->input->post('name')){
            if($email != ''){
                $check = $this->restaurant_model->checkEmailExist($email,$this->input->post('entity_id'),$this->input->post('content_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkEmailExist', $this->lang->line('email_exist'));
                    return false;  
                }
            }
        }else{
            if($email != ''){
                $check = $this->restaurant_model->checkEmailExist($email,$this->input->post('entity_id'),$this->input->post('content_id'));
                echo $check;
            }  
        }
    }

    public function import_menu_status(){
        $data['meta_title'] = $this->lang->line('title_admin_shop_menu').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/import_menu_status',$data);
    }

    //import menu
    public function import_menu()
    {        
        if($this->input->post('submit_page') == 'Submit')
        {   
            $this->form_validation->set_rules('import_tax', 'Menu File', 'trim|xss_clean');
            if ($this->form_validation->run()) 
            { 
                $test = $_FILES['import_tax']['name'];
                $this->load->library('Excel');
                $this->load->library('upload');
                $config['upload_path'] = './uploads/menu_import';
                $config['allowed_types'] = 'xlsx|xls|csv'; 
                $config['encrypt_name'] = TRUE;         
                if (!@is_dir('uploads/menu_import')) {
                    @mkdir('./uploads/menu_import', 0777, TRUE);
                }
                $this->upload->initialize($config);               
                // If upload failed, display error
                if (!$this->upload->do_upload('import_tax')) 
                { 
                    $this->session->set_flashdata('Import_Error', $this->upload->display_errors());
                    redirect(ADMIN_URL.'/'.$this->controller_name.'/view_menu');
                } 
                else 
                { 
                    $file_data = $this->upload->data();            
                    $file_path =  './uploads/menu_import/'.$file_data['file_name'];
                    // Start excel read
                    if($file_data['file_ext'] == '.xlsx' || $file_data['file_ext'] == '.xls')
                    {
                        //read file from path
                        $objPHPExcel = PHPExcel_IOFactory::load($file_path);
                        //get only the Cell Collection
                        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                        foreach ($cell_collection as $cell) 
                        {
                            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                           
                            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                            $data_value = (string)$objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                            //header will/should be in row 1 only. of course this can be modified to suit your need.
                            if ($row == 2) 
                            {
                                $header[$row][$column] = $data_value;
                            } 
                            else if ($row > 2)
                            {
                                $arr_data[$row][$column] = $data_value;
                            }
                        }
                        $row = 2;
                        $d=2;
                        $Import = array();
                        $add_data = array();
                        $menu_language_arr = array();
                        $content_id_arr = array();
                        for($rowcount=1; $rowcount<=count($arr_data); $rowcount++)
                        {
                            $d++;
                            $mandatoryColumnBlank = 1;
                            $getAddons = array();

                            // check for language
                            if (trim($arr_data[$d]['C']) != '') {
                                $add_data['language_slug'] = trim($arr_data[$d]['C']);
                                $getAddons = $this->restaurant_model->getAddons(trim($arr_data[$d]['C']));
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['C'].' is required.';
                            }

                            // check for restaurant
                            if (trim($arr_data[$d]['B']) != '' && trim($arr_data[$d]['C']) != '') {
                                $restaurant = $this->restaurant_model->getRestaurantId(trim($arr_data[$d]['B']),trim($arr_data[$d]['C']));
                                if (!empty($restaurant)) {
                                    $add_data['restaurant_id'] = $restaurant->entity_id;
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = $header[2]['B'].' details not found';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['B'].' is required.';
                            }

                            //check for Category
                            if (trim($arr_data[$d]['D']) != '' && trim($arr_data[$d]['C']) != '') {
                                $category = $this->restaurant_model->getCategoryId(trim($arr_data[$d]['D']),trim($arr_data[$d]['C']));
                                if (!empty($category)) {
                                    $add_data['category_id'] = $category->entity_id;
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = $header[2]['D'].' details not found';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['D'].' is required.';
                            }

                            // check for name
                            if (trim($arr_data[$d]['E']) != '') {
                                $add_data['name'] = trim($arr_data[$d]['E']);
                                $add_data['item_slug'] = slugify(trim($arr_data[$d]['E']),'restaurant_menu_item','item_slug');
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['E'].' is required.';
                            }

                            // check for price
                            if (trim($arr_data[$d]['K']) != 'yes') {
                                if (trim($arr_data[$d]['F']) != '') {
                                    $add_data['price'] = trim($arr_data[$d]['F']);
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = $header[2]['F'].' is required.';
                                }
                            }

                            // check for details
                            if (trim($arr_data[$d]['G']) != '') {
                                $add_data['menu_detail'] = trim($arr_data[$d]['G']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['G'].' is required.';
                            }

                            //check for the image
                            if (!empty($arr_data[$d]['H']))
                            {
                                $url = trim($arr_data[$d]['H']);
                                $fdata = file_get_contents($url);
                                if($fdata) {
                                    $random_string = random_string('alnum',12);
                                    $new = 'uploads/menu/'.$random_string.'.png';
                                    file_put_contents($new, $fdata);
                                    $add_data['image'] = "menu/".$random_string.'.png';
                                } else {
                                    $add_data['image'] = null;
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['H'].' is required.'; 
                            }

                            //check for popular_item
                            if (trim($arr_data[$d]['I']) != '') {
                                $add_data['popular_item'] = (trim($arr_data[$d]['I']) == "yes")?1:0;
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['I'].' is required.';
                            }

                            //check for Food Type
                            if (trim($arr_data[$d]['J']) != '') {
                                $add_data['is_veg'] = (trim($arr_data[$d]['J']) == "veg")?1:0;
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['J'].' is required.';
                            }
                            $addons = array();
                            //check for check_add_ons
                            $addonsArray = array();
                            if (trim($arr_data[$d]['K']) != '') {
                                $add_data['check_add_ons'] = (trim($arr_data[$d]['K']) == "yes")?1:0;
                                $addonsArray = array_slice($header[2],12);
                                $addonsArray = array_filter($addonsArray);
                                $lang_addons = array();
                                if (!empty($addonsArray)) {
                                    foreach ($addonsArray as $arrkey => $arrvalue) {
                                        if (in_array(trim($arrvalue),$getAddons)) {
                                            $lang_addons[$arrkey] = $arrvalue;
                                        }
                                    }
                                    foreach ($lang_addons as $Akey => $Avalue) {  
                                        $category_id = $this->restaurant_model->getAddonsId(trim($Avalue),trim($arr_data[$d]['C']));
                                        if (in_array(trim($Avalue),$getAddons)) { 
                                            $add_ons = explode(",",trim($arr_data[$d][$Akey]));
                                            if (!empty($add_ons)) {
                                                $addons[] = array(
                                                    'category_id'=>$category_id->entity_id,
                                                    'add_ons_name'=> trim($add_ons[1]),
                                                    'add_ons_price'=> trim($add_ons[2]),
                                                    'is_multiple'=> (trim($add_ons[0]) == "yes")?1:0
                                                );
                                            }
                                        }
                                        else
                                        { 
                                            $mandatoryColumnBlank = 0;                                
                                            $Import[$rowcount][] = trim($Avalue).', such Add ons category does not exists for now.';               
                                        }
                                    }
                                } 
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['K'].' is required.';
                            } 
                            // add data to community_user_detail
                            if ($mandatoryColumnBlank == 1) {

                                // check for content id , if it is to be set same
                                //ADD DATA IN CONTENT SECTION
                                if(trim($arr_data[$d]['A']) != '')
                                {
                                    if (!empty($menu_language_arr)) {
                                        if (in_array($arr_data[$d]['A'], $menu_language_arr)) {
                                            //name exists in the lang name as before so get the content id to add same menu item
                                            $Dkey = '';
                                            foreach ($menu_language_arr as $mkey => $mvalue) {
                                                if ($mvalue == $arr_data[$d]['A']) {
                                                    $Dkey = $mkey;
                                                }
                                            }
                                            if ($Dkey != '') {
                                                $ContentID = $content_id_arr[$Dkey];
                                            }
                                            else
                                            {
                                                $add_content = array(
                                                  'content_type'=>'menu',
                                                  'created_by'=>$this->session->userdata("UserID"),  
                                                  'created_date'=>date('Y-m-d H:i:s')                      
                                                );
                                                $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                                $content_id_arr[$d] = $ContentID;
                                            }
                                        }
                                        else
                                        {
                                            $add_content = array(
                                              'content_type'=>'menu',
                                              'created_by'=>$this->session->userdata("UserID"),  
                                              'created_date'=>date('Y-m-d H:i:s')                      
                                            );
                                            $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                            $content_id_arr[$d] = $ContentID;
                                        }
                                    }
                                    else
                                    {
                                        $add_content = array(
                                          'content_type'=>'menu',
                                          'created_by'=>$this->session->userdata("UserID"),  
                                          'created_date'=>date('Y-m-d H:i:s')                      
                                        );
                                        $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                        $content_id_arr[$d] = $ContentID;
                                    }
                                    $menu_language_arr[$d] = $arr_data[$d]['A'];
                                }

                                $add_data['content_id'] = $ContentID; 
                                $add_data['status']= 1;
                                $add_data['created_by'] =  $this->session->userdata('UserID');
                                $menu_id = $this->restaurant_model->addData('restaurant_menu_item',$add_data);
                                if (!empty($addons)) {
                                    foreach ($addons as $key => $value) {
                                        $addons[$key]['menu_id'] = $menu_id;
                                    }
                                    $this->restaurant_model->inserBatch('add_ons_master',$addons);
                                }
                                $Import[$rowcount][] = "Success";
                            }
                        }
                        $import_data['arr_data'] = $arr_data;
                        $import_data['header'] = $header;
                        $import_data['Import'] = $Import;
                        $import_data['restaurant'] = $this->restaurant_model->getRestaurantName($this->input->post('restaurant_id'));
                        $this->session->set_userdata('import_data', $import_data);
                        redirect(base_url().ADMIN_URL.'/restaurant/import_menu_status');
                    }
                }
            }
        }
        $data['Languages'] = $this->common_model->getLanguages();
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$this->session->userdata('language_slug'));
        $this->load->view(ADMIN_URL.'/restaurant_menu',$data);
    }
}
?>
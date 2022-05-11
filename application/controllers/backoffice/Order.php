<?php error_reporting(1);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once APPPATH."/third_party/excelclasses/PHPExcel.php";
require_once APPPATH."/third_party/excelclasses/PHPExcel/IOFactory.php";
class Order extends CI_Controller { 
    public $module_name = 'Order';
    public $controller_name = 'order';
    public $prefix = '_order';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->model('common_model');
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/order_model');
        $this->load->model(ADMIN_URL.'/users_model');
        $this->load->model('v1/gmap_api_model');
    }
    // view order
    public function view(){
    	$data['meta_title'] = $this->lang->line('title_admin_order').' | '.$this->lang->line('site_title');
        $data['restaurant'] = $this->order_model->getRestaurantList();
        $data['drivers'] = $this->order_model->getDrivers();
        $this->load->view(ADMIN_URL.'/order',$data);
    }
    // add order
    public function add(){
        $data['meta_title'] = $this->lang->line('title_admin_orderadd').' | '.$this->lang->line('site_title');
    	if($this->input->post('submit_page') == "Submit")
        {   
            $this->form_validation->set_rules('user_id', 'User', 'trim|required');
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('order_status','Order Status', 'trim|required');
            $this->form_validation->set_rules('order_date','Date Of Order', 'trim|required');
            $this->form_validation->set_rules('total_rate','Total', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
                $add_data = array(              
                    'user_id'=>$this->input->post('user_id'),
                    'restaurant_id' =>$this->input->post('restaurant_id'),
                    'coupon_id' =>$this->input->post('coupon_id'),
                    'order_status' =>$this->input->post('order_status'),
                    'order_date' =>date('Y-m-d H:i:s',strtotime($this->input->post('order_date'))),
                    'subtotal' =>($this->input->post('subtotal'))?$this->input->post('subtotal'):'',
                    'tax_rate'=>($this->input->post('tax_rate'))?$this->input->post('tax_rate'):'',
                    'tax_type'=>$this->input->post('tax_type'),
                    'delivery_charge' =>($this->input->post('delivery_charge'))?$this->input->post('delivery_charge'):'',
                    'total_rate' =>($this->input->post('total_rate'))?$this->input->post('total_rate'):'',
                    'coupon_type'=>$this->input->post('coupon_type'),
                    'coupon_amount'=>($this->input->post('coupon_amount'))?$this->input->post('coupon_amount'):'',
                    'created_by'=>$this->session->userdata("UserID"),
                    'status'=>1,
	                'order_delivery'=>'Delivery'
                );                                           
                $order_id = $this->order_model->addData('order_master',$add_data);
                //add items
                $items = $this->input->post('item_id');
                $add_item = array();
                if(!empty($items)){
                    foreach ($items as $key => $value) {
                        $itemName = $this->order_model->getItemName($this->input->post('item_id')[$key]);
                        $add_item[] = array(
                            "item_id"=>$this->input->post('item_id')[$key],
                            "item_name"=> $itemName->name,
                            "qty_no"=>$this->input->post('qty_no')[$key],
                            "rate"=>($this->input->post('rate')[$key])?$this->input->post('rate')[$key]:'',
                            "order_id"=>$order_id
                        );
                    }
                }
                // user_details
                $user = $this->db->get_where('users',array('entity_id'=>$this->input->post('user_id')))->first_row();
                // $address = $this->db->get_where('user_address',array('entity_id'=>$this->input->post('address_id')))->first_row();
                // $user_detail = array(
                //     'first_name'=>$user->first_name,
                //     'last_name'=>$user->last_name,
                //     'address'=>($address)?$address->address:'',
                //     'landmark'=>($address)?$address->landmark:'',
                //     'zipcode'=>($address)?$address->zipcode:'',
                //     'city'=>($address)?$address->city:'',
                //     'latitude'=>($address)?$address->latitude:'',
                //     'longitude'=>($address)?$address->longitude:'',
                // );

                $add_address = array(
                    'address'=> $this->input->post('add_address')." ".$this->input->post('add_address_area'),
                    'landmark'=> $this->input->post('landmark'),
                    'latitude'=> $this->input->post('add_latitude'),
                    'longitude'=> $this->input->post('add_longitude'),
                    'zipcode'=> $this->input->post('zipcode'),
                    'city'=> $this->input->post('city'),
                    'user_entity_id'=> $this->input->post('user_id')
                );
                $user_detail = array(
                    'first_name'=>$user->first_name,
                    'last_name'=>$user->last_name,
                    'address'=>$this->input->post('add_address'),
                    'landmark'=>$this->input->post('landmark'),
                    'zipcode'=>$this->input->post('zipcode'),
                    'city'=>$this->input->post('city'),
                    'latitude'=>$this->input->post('add_latitude'),
                    'longitude'=>$this->input->post('add_longitude'),
                );

                //get restaurant detail
                $rest_detail = $this->order_model->getRestaurantDetail($this->input->post('restaurant_id'));
                $order_detail = array(
                    'order_id'=>$order_id,
                    'user_detail' => serialize($user_detail),
                    'item_detail' => serialize($add_item),
                    'restaurant_detail' => serialize($rest_detail),
                ); 
                $this->order_model->addData('order_detail',$order_detail);
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                // send invoice to user
                $data['order_records'] = $this->order_model->getEditDetail($order_id);
                $data['menu_item'] = $this->order_model->getInvoiceMenuItem($order_id);
                $html = $this->load->view('backoffice/order_invoice',$data,true);
                if (!@is_dir('uploads/invoice')) {
                  @mkdir('./uploads/invoice', 0777, TRUE);
                } 
                $filepath = 'uploads/invoice/'.$order_id.'.pdf';
                $this->load->library('M_pdf'); 
                $mpdf=new mPDF('','Letter'); 
                $mpdf->SetHTMLHeader('');
                $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">Signature ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #000000;"><img src="'.base_url().'/assets/admin/img/logo.png" alt="" width="80" height="40"/></div>');
                $mpdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    0, // margin_left
                    0, // margin right
                    10, // margin top
                    23, // margin bottom
                    0, // margin header
                    0 //margin footer
                );
                $mpdf->autoScriptToLang = true;
                $mpdf->SetAutoFont();
                $mpdf->WriteHTML($html);
                $mpdf->output($filepath,'F');

                //send invoice as email
                // $user = $this->db->get_where('users',array('entity_id'=>$this->input->post('user_id')))->first_row();
                $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                $this->db->select('subject,message');
                $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'new-order-invoice','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                $arrayData = array('FirstName'=>$user->first_name,'Order_ID'=>$order_id);
                $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                if(!empty($EmailBody)){     
                    $this->load->library('email');  
                    $config['charset'] = 'iso-8859-1';  
                    $config['wordwrap'] = TRUE;  
                    $config['mailtype'] = 'html';  
                    $this->email->initialize($config);  
                    $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                    $this->email->to(trim($user->email)); 
                    $this->email->subject($Emaildata->subject);  
                    $this->email->message($EmailBody);
                    $this->email->attach($filepath);
                    $this->email->send(); 
                }
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');                 
            }
        }
        $data['restaurant'] = $this->order_model->getListData('restaurant');
        $data['user'] = $this->order_model->getListData('users');
        $data['coupon'] = $this->order_model->getListData('coupon');
    	$this->load->view(ADMIN_URL.'/order_add',$data);
    }
     //ajax view
    public function ajaxview() { 
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $order_status = ($this->uri->segment('4'))?$this->uri->segment('4'):''; 
        $sortfields = array(1=>'u.first_name','2'=>'o.total_rate','3'=>'o.order_status','4'=>'o.status','5'=>'o.entity_id','6'=>'driver.first_name','7'=>'o.created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->order_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,$order_status);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);
            $disabled = ($val->ostatus == 'delivered' || $val->ostatus == 'cancel')?'disabled':'';
            $assignDisabled = ($val->first_name != '' || $val->last_name != '' || strpos($val->order_delivery, "Delivery") === FALSE)?'disabled':'';
            $trackDriver = (($val->first_name != '' || $val->last_name != '') && $val->order_delivery == "Delivery")?'<a target="_blank" href="'.base_url().ADMIN_URL.'/order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'" title="Click here to view driver live position" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-eye"></i> '.$this->lang->line('track_driver').'</a>':'';            
            $assignDisabledStatus = ($val->status != 1)?'disabled':'';
            $ostatus = ($val->ostatus)?"'".$val->ostatus."'":'';
            $restaurant = ($val->restaurant_detail)?unserialize($val->restaurant_detail):'';
            $accept = ($val->status != 1 && $val->restaurant_id && $val->ostatus != 'delivered' && $val->ostatus != 'cancel')?'<button onclick="disableDetail('.$val->entity_id.','.$val->restaurant_id.','.$val->entity_id.')"  title="'.$this->lang->line('accept').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-check"></i> '.$this->lang->line('accept').'</button>':'';
            // $reject = ($val->ostatus != 'delivered' && $val->ostatus != 'cancel' && $val->status != 1)?'<button onclick="rejectOrder('.$val->user_id.','.$val->restaurant_id.','.$val->entity_id.')"  title="'.$this->lang->line('reject').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('reject').'</button>':'';
            $reject = ($val->ostatus != 'delivered' && $val->ostatus != 'cancel' && $val->status != 1)?'<button onclick="rejectOrder('.$val->user_id.','.$val->restaurant_id.','.$val->entity_id.')"  title="'.$this->lang->line('reject').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('reject').'</button>':'';
            $cancel = ($val->ostatus != 'delivered' && $val->ostatus != 'cancel' /*&& $val->status != 1*/)?'<button onclick="rejectOrder('.$val->user_id.','.$val->restaurant_id.','.$val->entity_id.')"  title="'.$this->lang->line('cancel').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('cancel').'</button>':'';
            
            $updateStatus = ($val->status == 1)?'<button onclick="updateStatus('.$val->entity_id.','.$ostatus.','.$val->user_id.')" '.$disabled.' title="Click here for update status" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-edit"></i> '.$this->lang->line('change_status').'</button>':''; 
            $viewComment = ($val->extra_comment != '')?'<button onclick="viewComment('.$val->entity_id.')" title="Click here to view comment" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-eye"></i> '.$this->lang->line('view_comment').'</button>':''; 
            if($val->ostatus == "placed"){
                $ostatuslng = $this->lang->line('placed');
            }
            if($val->ostatus == "delivered"){
                $ostatuslng = $this->lang->line('delivered');
            }
            if($val->ostatus == "onGoing"){
                $ostatuslng = $this->lang->line('onGoing');
            }
            if($val->ostatus == "cancel"){
                $ostatuslng = $this->lang->line('cancel');
            }
            if($val->ostatus == "preparing"){
                $ostatuslng = $this->lang->line('preparing');
            }
            if($val->ostatus == "pending"){
                $ostatuslng = $this->lang->line('pending');
            }
            if($val->order_delivery == "Delivery"){
                $order_delivery = $this->lang->line('delivery');
            }
            if($val->order_delivery == "24H Delivery"){
                $order_delivery = $this->lang->line('deliver_24');
            }
            
            $records["aaData"][] = array(
                '<input type="checkbox" name="ids[]" value="'.$val->entity_id.'">',
                $val->entity_id,
                ($restaurant)?$restaurant->name:$val->name,
                ($val->fname || $val->lname)?$val->fname.' '.$val->lname:'Order by Restaurant',
                ($val->rate)?$currency_symbol->currency_symbol.number_format_unchanged_precision($val->rate,$currency_symbol->currency_code):'',
                $val->first_name.' '.$val->last_name,
                $ostatuslng,
                ($val->created_date)?date('d-m-Y g:i A',strtotime($val->created_date)):'',
                '<p style="color: green">'.(($val->pre_order_delivery_date)?date('d-m-Y g:i A', strtotime($val->pre_order_delivery_date)) : '').'</p>',
                $order_delivery,
                ($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
                ' '.'<button onClick="showDetailModal('.$val->entity_id.')" class="delete btn btn-sm danger-btn margin-bottom" data-toggle="modal"><i class="fa fa-eye"></i> View</button>'.$accept.$reject.($reject == "" ?$cancel:"").'<button onclick="deleteDetail('.$val->entity_id.')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button> <button onclick="getInvoice('.$val->entity_id.')"  title="'.$this->lang->line('download_invoice').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('invoice').'</button>'.$updateStatus.'
                    <button onclick="statusHistory('.$val->entity_id.')" title="Click here for view status history" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-history"></i> '.$this->lang->line('status_history').'</button>'.$viewComment.'<button onclick="updateDriver('.$val->entity_id.')" '.$assignDisabled.' '.$assignDisabledStatus.' title="Click here to assign driver" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-user"></i> '.$this->lang->line('assign_driver').'</button>'.$trackDriver.'
                    '
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function track_order(){
        $data['meta_title'] = $this->lang->line('track_order').' | '.$this->lang->line('site_title');
        $order_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment('4'))):'';
        if (!empty($order_id)) {
            $data['latestOrder'] = $this->order_model->getLatestOrder($order_id);
            $data['order_id'] = $order_id;
            $this->load->view(ADMIN_URL.'/track_order',$data);
        } 
    }
    // ajax track user's order
    public function ajax_track_order(){
        $data['meta_title'] = $this->lang->line('track_order').' | '.$this->lang->line('site_title');
        $data['latestOrder'] = array();
        if (!empty($this->input->post('order_id'))) {
            $data['latestOrder'] = $this->order_model->getLatestOrder($this->input->post('order_id'));
        }
        $data['order_id'] = $this->input->post('order_id');
        $this->load->view(ADMIN_URL.'/ajax_track_order',$data);
    }
    // updating status to reject a order
    public function ajaxReject() { 
        $user_id = ($this->input->post('user_id') != '')?$this->input->post('user_id'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $order_id = ($this->input->post('order_id') != '')?$this->input->post('order_id'):'';
        if($user_id && $restaurant_id && $order_id){ 
            $this->db->set('order_status','cancel')->where('entity_id',$order_id)->update('order_master');
            $addData = array(
                'order_id'=>$order_id,
                'order_status'=>'cancel',
                'time'=>date('Y-m-d H:i:s'),
                'status_created_by'=>'Admin'
            );
            $this->order_model->addData('order_status',$addData);
            $userdata = $this->order_model->getUserDate($user_id);
            $message = $this->lang->line('order_canceled');
            $device_id = $userdata->device_id;
            $this->sendFCMRegistration($device_id,$message,'cancel',$restaurant_id);
        }
    }
    // assign driver
    public function assignDriver(){ 
        if (!empty($this->input->post('order_entity_id')) && !empty($this->input->post('driver_id'))) {
            $distance = $this->order_model->getOrderDetails($this->input->post('order_entity_id'));
            $comsn = 0;
            if($distance[0]->distance > 3){
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_more'))->first_row();
            }else{
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_less'))->first_row(); 
            }
            $order_detail = array(
                'driver_commission'=>$comsn->OptionValue,
                'commission'=>$comsn->OptionValue,
                'distance'=>$distance[0]->distance,
                'driver_id'=>$this->input->post('driver_id'),
                'order_id'=>$this->input->post('order_entity_id'),
                'is_accept'=>1
            );
            $driver_map_id = $this->order_model->addData('order_driver_map',$order_detail);
            if (!empty($driver_map_id)) {
            	// after assigning a driver need to update the order status
            	$order_status = "preparing";
            	$this->db->set('order_status',$order_status)->where('entity_id',$this->input->post('order_entity_id'))->update('order_master');
	            $addData = array(
	                'order_id'=>$this->input->post('order_entity_id'),
	                'order_status'=>$order_status,
	                'time'=>date('Y-m-d H:i:s'),
	                'status_created_by'=>'Admin'
	            );
	            $order_id = $this->order_model->addData('order_status',$addData);
	            // adding notification for website
	            $order_status = 'order_preparing';
	            if ($order_status != '') {
	                $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$this->input->post('order_entity_id'));
	                $notification = array(
	                    'order_id' => $this->input->post('order_entity_id'),
	                    'user_id' => $order_detail->user_id,
	                    'notification_slug' => $order_status,
	                    'view_status' => 0,
	                    'datetime' => date("Y-m-d H:i:s"),
	                );
	                $this->common_model->addData('user_order_notification',$notification);
	            }
                //notification to user
                $device = $this->order_model->getDevice($order_detail->user_id);
                $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                $this->lang->load('messages_lang', $languages->language_directory);
                $message = $this->lang->line($order_status);
                $device_id = $device->device_id;
                $restaurant = $this->order_model->orderDetails($this->input->post('order_entity_id'));
                $this->sendFCMRegistration($device_id,$message,'preparing',$restaurant[0]->restaurant_id);

            	//notification to driver
	            $device = $this->order_model->getDevice($this->input->post('driver_id'));
                if($device->device_id){  
                    //get langauge
                    $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                    $this->lang->load('messages_lang', $languages->language_directory);
                    #prep the bundle
                    $fields = array();            
                    $message = $this->lang->line('order_assigned');
                    $fields['to'] = $device->device_id; // only one user to send push notification
                    $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                    $fields['data'] = array ('screenType'=>'order');
                   
                    $headers = array (
                        'Authorization: key=' . FCM_KEY,
                        'Content-Type: application/json'
                    );
                    #Send Reponse To FireBase Server    
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                    $result = curl_exec($ch);
                    curl_close($ch);  
                } 
                echo 'success';
            }
            
        }
    }
    // view comment
    public function viewComment(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id){
            $comment = $this->order_model->getOrderComment($entity_id);
            echo $comment->extra_comment;
        }
    }
    // updating status and send request to driver
    public function ajaxdisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $order_id = ($this->input->post('order_id') != '')?$this->input->post('order_id'):'';
        if($entity_id != '' && $restaurant_id != '' && $order_id != ''){
            $this->order_model->UpdatedStatus('order_master',$entity_id,$restaurant_id,$order_id);
            // adding order status
            $addData = array(
                'order_id'=>$order_id,
                'order_status'=>'accepted_by_restaurant',
                'time'=>date('Y-m-d H:i:s'),
                'status_created_by'=>'Admin'
            );
            $status_id = $this->order_model->addData('order_status',$addData);
            // adding notification for website
            $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
            $notification = array(
                'order_id' => $order_id,
                'user_id' => $order_detail->user_id,
                'notification_slug' => 'order_accepted',
                'view_status' => 0,
                'datetime' => date("Y-m-d H:i:s"),
            );
            $this->common_model->addData('user_order_notification',$notification);
        }
    }
    // method for deleting
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->order_model->ajaxDelete('order_master',$entity_id);
    }
    //get item of restro
    public function getItem(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id){
           $result =  $this->order_model->getItem($entity_id);
                $html = '<option value="">'.$this->lang->line('select').'</option>';
           foreach ($result as $key => $value) {
                $html .= '<option value="'.$value->entity_id.'" data-id="'.$value->price.'">'.$value->name.'</option>';
           }
        }
        echo $html;
    }

    //get address
    public function getPhoneNumber(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id){
           $result =  $this->users_model->getSingleUserById($entity_id);
        }
        echo $result->phone_number;
    }


    //get address
    public function getAddress(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id){
           $result =  $this->order_model->getAddress($entity_id);
                $html = '<option value="">'.$this->lang->line('select').'</option>';
           foreach ($result as $key => $value) {
                $html .= '<option value="'.$value->entity_id.'">'.$value->address.' , '.$value->city.' , '.$value->state.' , '.$value->country.' '.$value->zipcode.'</option>';
           }
        }
        echo $html;
    }

    public function getDeliveryCharge(){
        $resto_id = ($this->input->post('resto_id') != '')?$this->input->post('resto_id'):'';
        $lat = ($this->input->post('lat') != '')?$this->input->post('lat'):'';
        $long = ($this->input->post('long') != '')?$this->input->post('long'):'';
        $fee = '';
        if($resto_id && $lat && $long) {
            $fee = $this->getDeliveryByDistance(null, $lat."~".$long, $resto_id);
        }
        echo $fee;
    }

    public function getDeliveryByDistance($originLatLong, $destinationLatLong, $restaurant_id)
    {
        if(!$originLatLong)
        {
            $address = $this->common_model->getRestoLatLong($restaurant_id);
            $originLatLong = $address->latitude."~".$address->longitude;
        }
        $origin = explode("~", $originLatLong);
		$destination = explode("~", $destinationLatLong);

		$distance = $this->gmap_api_model->getDistance($origin[1], $origin[0], $destination[1], $destination[0]);
		if($distance == null)
		{
			$distance = $this->_getDistance($origin[0], $origin[1], $destination[0], $destination[1]);
		}
        if($distance != null)
        {
            return $this->getFeeAccordingDistance($distance, $restaurant_id);
        }
        else
        {
            return null;
        }
    }

    function _getDistance($latitude1, $longitude1, $latitude2, $longitude2) {  
		$earth_radius = 6371;

		$dLat = deg2rad($latitude2 - $latitude1);  
		$dLon = deg2rad($longitude2 - $longitude1);  

		$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
		$c = 2 * asin(sqrt($a));  
		$d = $earth_radius * $c;  
		return $d;  
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

    public function getFeeAccordingDistance($distance, $restaurant_id)
    {
        $allDeliveryCharge = $this->common_model->getMultipleRows("delivery_charge", "restaurant_id", $restaurant_id);
        $deliveryFee = "";
        if(!empty($allDeliveryCharge))
        {
            foreach($allDeliveryCharge as $key => $delivery)
            {
                if($deliveryFee == "")
                {
                    $range = str_replace(array("km", " "), array("",""), $delivery->area_name);
                    $arrayRange = explode('-', $range);
                    if(($arrayRange[0] <= $distance) && ($distance <=  $arrayRange[1]))
                    {
                        $deliveryFee = $delivery->price_charge;
                    break;
                    }
                }
            }
        }

        return $deliveryFee;
    }


    //pending
    public function pending(){
        $data['meta_title'] = $this->lang->line('title_admin_pending').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/pending_order',$data);
    }
    //delivered
    public function delivered(){
        $data['meta_title'] = $this->lang->line('title_admin_delivered').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/delivered_order',$data);
    }
    //on going
    public function on_going(){
        $data['meta_title'] = $this->lang->line('title_admin_ongoing').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/ongoing_order',$data);
    }
    //cancel
    public function cancel(){
        $data['meta_title'] = $this->lang->line('title_admin_cancel').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/cancel_order',$data);
    }

    //Get order detail
    public function orderDetail($entity_id) {
        $data['order_records'] = $this->order_model->getEditDetail($entity_id);
        $data['menu_item'] = $this->order_model->getInvoiceMenuItem($entity_id);
        return $this->load->view('backoffice/order_detail',$data);
    }

    //create invoice
    public function getInvoice(){
        $entity_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):'';
        $data['order_records'] = $this->order_model->getEditDetail($entity_id);
        $data['menu_item'] = $this->order_model->getInvoiceMenuItem($entity_id);
        $html = $this->load->view('backoffice/order_invoice',$data,true);
        if (!@is_dir('uploads/invoice')) {
          @mkdir('./uploads/invoice', 0777, TRUE);
        } 
        $filepath = 'uploads/invoice/'.$entity_id.'.pdf';
        $this->load->library('M_pdf'); 
        $mpdf=new mPDF('','Letter'); 
        $mpdf->SetHTMLHeader('');
        $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">Signature ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #000000;"><img src="'.base_url().'/assets/admin/img/logo.png" alt="" width="80" height="40"/></div>');
        $mpdf->AddPage('', // L - landscape, P - portrait 
            '', '', '', '',
            0, // margin_left
            0, // margin right
            10, // margin top
            23, // margin bottom
            0, // margin header
            0 //margin footer
        );
        $mpdf->autoScriptToLang = true;
        $mpdf->SetAutoFont();
        $mpdf->WriteHTML($html);
        $mpdf->output($filepath,'F');
        echo $filepath;
          
    }
    //add status
    public function updateOrderStatus(){
        $entity_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):''; 
        $order_status = ($this->input->post('order_status'))?$this->input->post('order_status'):''; 
        $user_id = ($this->input->post('user_id'))?$this->input->post('user_id'):'';
        if($entity_id && $order_status){
            $this->db->set('order_status',$this->input->post('order_status'))->where('entity_id',$entity_id)->update('order_master');
            $addData = array(
                'order_id'=>$entity_id,
                'order_status'=>$this->input->post('order_status'),
                'time'=>date('Y-m-d H:i:s'),
                'status_created_by'=>'Admin'
            );
            $order_id = $this->order_model->addData('order_status',$addData);
            // adding notification for website
            $order_status = '';
            if ($this->input->post('order_status') == "complete") {
                $this->common_model->deleteData('user_order_notification','order_id',$entity_id);
            }
            else if ($this->input->post('order_status') == "preparing") {
                $order_status = 'order_preparing';
            }
            else if ($this->input->post('order_status') == "onGoing") {
                $order_status = 'order_ongoing';
            }
            else if ($this->input->post('order_status') == "delivered") {
                $order_status = 'order_delivered';
            }
            else if ($this->input->post('order_status') == "cancel") {
                $order_status = 'order_canceled';
            }
            if ($order_status != '') {
                $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$entity_id);
                $notification = array(
                    'order_id' => $entity_id,
                    'user_id' => $order_detail->user_id,
                    'notification_slug' => $order_status,
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->common_model->addData('user_order_notification',$notification);
            }
            
            $userdata = $this->order_model->getUserDate($user_id);
            //get langauge
            $device = $this->order_model->getDevice($user_id);
            $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
            $this->lang->load('messages_lang', $languages->language_directory);
            $message = $this->lang->line($order_status);
            $device_id = $userdata->device_id;
            $restaurant = $this->order_model->orderDetails($entity_id);
            $this->sendFCMRegistration($device_id,$message,$this->input->post('order_status'),$restaurant[0]->restaurant_id);
            if($userdata->bot_user_id) {
                $this->sendNotifToBot($userdata->bot_user_id, $user_id, $entity_id, $order_status, $restaurant[0]);
            }
            echo 'success';
        }
    }

    //Send to bot 
    function sendNotifToBot($bot_user_id, $user_id, $order_id, $order_status, $restaurant) {
        $headers = array (
            'Content-Type: application/json'
        );
        $data = array(
            'notif_bot' => true,
            'bot_user_id' => $bot_user_id, 
            'user_id' => $user_id, 
            'order_status' => $order_status,
            'track_order' => base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id)), 
            'restaurant' => $this->common_model->getSingleRow('restaurant', 'entity_id', $restaurant->restaurant_id),
            'order_detail' => $this->common_model->getSingleRow('order_master','entity_id',$order_id),
        );
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://bot.e-sakafo.mg/webhook/dialogflow' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $data ) );
        $result = curl_exec($ch);
        curl_close($ch);    

    }
    // Send notification
    function sendFCMRegistration($registrationIds,$message,$order_status,$restaurant_id) {   
        if($registrationIds){        
            #prep the bundle
            $fields = array();            
           
            $fields['to'] = $registrationIds; // only one user to send push notification
            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
            if ($order_status == "delivered") {
                $fields['data'] = array ('screenType'=>'delivery','restaurant_id'=>$restaurant_id);
            }
            else
            {
                $fields['data'] = array ('screenType'=>'order');
            }
            $headers = array (
                'Authorization: key=' . Driver_FCM_KEY,
                'Content-Type: application/json'
            );
            #Send Reponse To FireBase Server    
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            $result = curl_exec($ch);
            curl_close($ch);            
        } 
    }
    public function deleteMultiOrder(){
        $orderId = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
        if($orderId){
            $order_id = explode(',', $orderId);
            $data = $this->order_model->deleteMultiOrder($order_id);
            echo json_encode($data);
        }
    }
    //get status history
    public function statusHistory(){
        $entity_id = ($this->input->post('order_id'))?$this->input->post('order_id'):''; 
        if($entity_id){
            $data['history'] = $this->order_model->statusHistory($entity_id);
            $this->load->view(ADMIN_URL.'/view_status_history',$data);
        }
    }
    //generate report
    public function generate_report(){
        $restaurant_id = $this->input->post('restaurant_id');
        $order_type = $this->input->post('order_delivery');
        $order_date = $this->input->post('order_date');
        $results = $this->order_model->generate_report($restaurant_id,$order_type,$order_date); 
        if(!empty($results)){
            // export as an excel sheet
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('Reports');
            $headers = array("Restaurant","User Name","Order Total","Order Delivery","Order Date","Order Status","Status");

            for($h=0,$c='A'; $h<count($headers); $h++,$c++)
            {
                $this->excel->getActiveSheet()->setCellValue($c.'1', $headers[$h]);
                $this->excel->getActiveSheet()->getStyle($c.'1')->getFont()->setBold(true);
            }
            $row = 2;
            for($r=0; $r<count($results); $r++){ 
                $status = ($results[$r]->status)?'Active':'Deactive';
                $this->excel->getActiveSheet()->setCellValue('A'.$row, $results[$r]->name);
                $this->excel->getActiveSheet()->setCellValue('B'.$row, $results[$r]->first_name.' '.$results[$r]->last_name);
                $this->excel->getActiveSheet()->setCellValue('C'.$row, number_format_unchanged_precision($results[$r]->total_rate,$results[$r]->currency_code));
                $this->excel->getActiveSheet()->setCellValue('D'.$row, $results[$r]->order_delivery);
                $this->excel->getActiveSheet()->setCellValue('E'.$row, $results[$r]->order_date);
                $this->excel->getActiveSheet()->setCellValue('F'.$row, ucfirst($results[$r]->order_status));            
                $this->excel->getActiveSheet()->setCellValue('G'.$row, $status);                
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
<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(-1);
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Bot_api extends REST_Controller {
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('v1/api_model');                
        $this->load->model('checkout_model');                
        $this->load->model('common_model');                
        $this->load->model('v1/bot_api_model');
        $this->load->model('v1/gmap_api_model');                
        $this->load->library('form_validation');
    }

    public function getAllRecord_get($table)
    {
        $datas = $this->bot_api_model->getAllRecord($table);
        $this->response(['datas'=>$datas,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    
    public function getRecord_get($table, $fieldName, $where)
    {
        $data = $this->bot_api_model->getRecord($table, $fieldName, $where);
        $this->response(['data'=>$data,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    // RESTAURANT OPERATION
    public function getAllShopRecords_get($store_type, $lat, $long)
    {
        $datas = $this->bot_api_model->getShopRecords($store_type);
        $datas_with_fees = array();
        foreach($datas as $key => $data)
        {
            $datas[$key]->delivery_fee = $this->getDeliveryByDistance($data->latitude."~".$data->longitude, $lat."~".$long, $data->shop_id);
             // = $this->checkGeoFence($lat, $long, $price_charge = true, $data->shop_id);
        }
        $this->response(['datas'=>$datas,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function searchShops_post()
    {
        $requestBody = json_decode($this->input->raw_input_stream, true);
        $datas = $this->bot_api_model->searchShops($requestBody['searchText'], "fr", $requestBody["store_type"]);
        $this->response(['datas'=>$datas,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    public function getMenuRecords_get($shop_id)
    {
        $datas = $this->bot_api_model->getMenuRecords($shop_id, 1);
        $this->response(['datas'=>$datas,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    public function searchMenus_post()
    {
        $requestBody = json_decode($this->input->raw_input_stream, true);
        $datas = $this->bot_api_model->searchMenuRecords($requestBody['shop_id'], $requestBody['searchText']);
        $this->response(['datas'=>$datas,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function addQuarter_post()
    {
        $requestBody = json_decode($this->input->raw_input_stream, true);
        $id = $this->bot_api_model->addData('quarter', $requestBody);
        $this->response(['id'=>$id,'status' => 1,'message' => 'record stored'], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    public function addUserAddress_post()
    {
        $requestBody = json_decode($this->input->raw_input_stream, true);
        $address = $this->bot_api_model->getRecordMultipleWhere('user_address', array(
            'user_entity_id' => $requestBody['user_entity_id'],
            'address' => $requestBody['address'],
            'longitude' => $requestBody['longitude'],
            'latitude' => $requestBody['latitude']
        ));
        if(isset($address) && $address->entity_id)
        {
            $count = $this->bot_api_model->updateData('user_address', array('landmark' => $requestBody['landmark']), 'entity_id', $address->entity_id );
            $this->response(['id'=>$address->entity_id,'status' => 1,'message' => 'User address updated'], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $id = $this->bot_api_model->addData('user_address', $requestBody);
            $this->response(['id'=>$id,'status' => 1,'message' => 'User address stored'], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
    
    public function addDeliveryFees_post()
    {
        $requestBody = json_decode($this->input->raw_input_stream, true);
        $id = $this->bot_api_model->addData('delivery_fees', $requestBody);
        $this->response(['id'=>$id,'status' => 1,'message' => 'record stored'], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function addUser_post()
    {
        $requestBody = json_decode($this->input->raw_input_stream, true);
        if(!empty($requestBody['entity_id'])) 
        {
            $user = $this->bot_api_model->getRecord('users', 'entity_id', $requestBody['entity_id']);    
        } else {
            $user = $this->bot_api_model->getRecord('users', 'email', $requestBody['email']);
        }
        if(isset($user) && $user->entity_id) {
            if(empty($user->bot_user_id) || $user->bot_user_id != $requestBody['bot_user_id']) {
                $updatedUser = $this->bot_api_model->updateData('users', array('bot_user_id' => $requestBody['bot_user_id']), 'entity_id', $user->entity_id);
            } 
            
            if(empty($user->phone_number) || empty($user->mobile_number)) {
                $updatedUser = $this->bot_api_model->updateData('users', array('phone_number' => $requestBody['phone_number'], 'mobile_number' => $requestBody['mobile_number']), 'entity_id', $user->entity_id);
            } 
            
            if(empty($user->email) || empty($user->email)) {
                $updatedUser = $this->bot_api_model->updateData('users', array('email' => $requestBody['email']), 'entity_id', $user->entity_id);
                $this->sendEmail($id, $requestBody);
            } 
            
            $this->response(['id'=>$user->entity_id,'status' => 1,'message' => 'user exist'], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $addUser = array_merge($requestBody, array(
                'password'=>md5(SALT.'password')
            ));
            $id = $this->bot_api_model->addData('users', $addUser);

            $this->sendEmail($id, $requestBody);

            $this->response(['id'=>$id,'status' => 1,'message' => 'record stored'], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }

    public function talkToAgent_post()
    {
        $requestBody = json_decode($this->input->raw_input_stream, true);
        //$verificationCode = random_string('alnum', 20).$id.random_string('alnum', 5);
        //$confirmationLink = '<a href='.base_url().'user/reset/'.$verificationCode.'>lien</a>';   
        $email_template = $this->bot_api_model->getRecordMultipleWhere('email_template', array('email_slug'=>'parler-a-un-conseiller','language_slug'=> 'fr'));        
        $arrayData = array('FirstName'=>$requestBody['first_name'], 'Number' => $requestBody['phone_number'], 'Question' => $requestBody['question']);
        $EmailBody = generateEmailBody($email_template->message,$arrayData);

        //get System Option Data
        $this->db->select('OptionValue');
        $FromEmailID = $this->bot_api_model->getRecordMultipleWhere('system_option',array('OptionSlug'=>'From_Email_Address'));

        $this->db->select('OptionValue');
        $FromEmailName = $this->bot_api_model->getRecordMultipleWhere('system_option',array('OptionSlug'=>'Email_From_Name'));
      
        $this->load->library('email');  
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";      
        $this->email->initialize($config);  
        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
        $this->email->to($FromEmailID->OptionValue);      
        $this->email->subject($email_template->subject);  
        $this->email->message($EmailBody);            
        if(!$this->email->send()){
            show_error($this->email->print_debugger());
            die;
        }
        // update verification code
        //$addata = array('email_verification_code'=>$verificationCode);
        //$this->common_model->updateData('users',$addata,'entity_id',$id);
    }

    public function sendEmail($id, $requestBody) 
    {
        $verificationCode = random_string('alnum', 20).$id.random_string('alnum', 5);
        $confirmationLink = '<a href='.base_url().'user/reset/'.$verificationCode.'>lien</a>';   
        $email_template = $this->bot_api_model->getRecordMultipleWhere('email_template', array('email_slug'=>'update-password-from-bot','language_slug'=> 'fr'));        
        $arrayData = array('FirstName'=>$requestBody['first_name'],'ForgotPasswordLink'=>$confirmationLink, 'Number' => $requestBody['phone_number'], 'Password' => 'password');
        $EmailBody = generateEmailBody($email_template->message,$arrayData);

        //get System Option Data
        $this->db->select('OptionValue');
        $FromEmailID = $this->bot_api_model->getRecordMultipleWhere('system_option',array('OptionSlug'=>'From_Email_Address'));

        $this->db->select('OptionValue');
        $FromEmailName = $this->bot_api_model->getRecordMultipleWhere('system_option',array('OptionSlug'=>'Email_From_Name'));
      
        $this->load->library('email');  
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";      
        $this->email->initialize($config);  
        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
        $this->email->to($requestBody['email']);      
        $this->email->subject($email_template->subject);  
        $this->email->message($EmailBody);            
        if(!$this->email->send()){
            show_error($this->email->print_debugger());
            die;
        }
        // update verification code
        $addata = array('email_verification_code'=>$verificationCode);
        $this->common_model->updateData('users',$addata,'entity_id',$id);
    }

    // Get list of commande
    public function myOrder_get($bot_user_id)
    {
        $user = $this->bot_api_model->getRecord('users', 'bot_user_id', $bot_user_id);
        if(!$user) {
            return $this->response(['datas' => [], 'status' => 1]);
        }
        $data = $this->bot_api_model->getOrderDetail('process', $user->entity_id, '');
        return $this->response(['datas' => $data, 'status' => 1 ]);
    }

    //add order
    public function addOrder_post()
    {
        $requestBody = json_decode($this->input->raw_input_stream, true);
        $token = $requestBody['token'];
        $user_id = $requestBody['user_id'];
        $tokenres = $this->bot_api_model->checkToken($token, $user_id);
        if($tokenres){
            $taxdetail = $this->api_model->getShopTax('shop',$requestBody['shop_id'],$flag="order");
            $total = 0;
            $subtotal = $requestBody['subtotal'];   
            $add_data = array(              
                'user_id'=>$requestBody['user_id'],
                'shop_id' =>$requestBody['shop_id'],
                'address_id' =>$requestBody['address_id'],
                // 'coupon_id' =>$requestBody['coupon_id'],
                'order_status' =>'placed',
                'order_date' =>date('Y-m-d H:i:s',strtotime($requestBody['order_date'])),
                'pre_order_delivery_date' => $requestBody['pre_order_delivery_date'] ? date('Y-m-d H:i:s',strtotime($requestBody['pre_order_delivery_date'])) : null,
                'subtotal'=>$subtotal,
                'tax_rate'=>$taxdetail->amount,
                'tax_type'=>$taxdetail->amount_type,
                'coupon_type'=>$requestBody['coupon_type'],
                'coupon_amount'=>$requestBody['coupon_amount'],
                'total_rate' =>$requestBody['total'],
                'payment_option' => $requestBody['payment_option'],
                'status'=> 0,
                'delivery_charge' => $requestBody['delivery_charge'],
                'coupon_discount'=>$requestBody['coupon_discount'],
                'coupon_name'=>$requestBody['coupon_name'],
                'order_delivery' => $requestBody['order_delivery'],
                'extra_comment' => $requestBody['extra_comment']             
            );          
            $order_id = $this->api_model->addRecord('order_master',$add_data);  
            // add items
            // $items = $requestBody['items'];
            // $itemDetail = json_decode($items,true);
            $add_item = array();
            if(!empty($requestBody['items'])){
                foreach ($requestBody['items'] as $key => $value) {
                    $add_item[] = array(
                        "item_name"=>$value['name'],
                        "item_id"=>$value['menu_id'],
                        "qty_no"=>$value['quantity'],
                        "rate"=>$value['price'],
                        "order_id"=>$order_id
                    );
                }
            }
            $address = $this->api_model->getAddress('user_address','entity_id',$requestBody['address_id']);
            $user_detail = array(
                'first_name'=>$tokenres->first_name,
                'last_name'=>($tokenres->last_name)?$tokenres->last_name:'',
                'address'=>$address[0]->address,
                'landmark'=>$address[0]->landmark,
                'zipcode'=>$address[0]->zipcode,
                'city'=>$address[0]->city,
                'latitude'=>$address[0]->latitude,
                'longitude'=>$address[0]->longitude,
            );
            $order_detail = array(
                'order_id'=>$order_id,
                'user_detail' => serialize($user_detail),
                'item_detail' => serialize($add_item),
                'shop_detail' => serialize($taxdetail),
            );
            $this->api_model->addRecord('order_detail',$order_detail);
            $verificationCode = random_string('alnum',25);            
            // email message body
            $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-receive-alert','status'=>1))->first_row();                    
            $this->db->select('OptionValue');
            $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

            $this->db->select('OptionValue');
            $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();  
            if(!empty($email_template)){
                $this->load->library('email');  
                $config['charset'] = 'iso-8859-1';  
                $config['wordwrap'] = TRUE;  
                $config['mailtype'] = 'html';  
                $this->email->initialize($config);  
                $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                $this->email->to(trim($FromEmailID->OptionValue)); 
                $this->email->subject('Order Receive Alert');  
                $this->email->message($email_template->message);  
                if(!$this->email->send()){
                    show_error($this->email->print_debugger());
                    die;
                }
            }
            $order_status = 'placed';
            $message = $this->lang->line('success_add');
            
            $this->response(['shop_detail'=>$taxdetail,'order_id' => $order_id,'order_status'=>$order_status,'order_date'=>date('Y-m-d H:i:s',strtotime($requestBody['order_date'])),'status'=>1,'message' => $message], REST_Controller::HTTP_OK); // OK */
        }else{
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code      
        }
    }


    //check lat long exist in area
    public function checkGeoFence($latitude,$longitude,$price_charge,$shop_id)
    {
        $result = $this->checkout_model->checkGeoFence($shop_id); 
        $latlongs =  array($latitude,$longitude);
        $coordinatesArr = array();
        if (!empty($result)) {
        	if (!empty($result[0]->lat_long)) {
        		$lat_longs =  explode('~', $result[0]->lat_long);
        		foreach ($lat_longs as $key => $value) {
        			$val = str_replace(array('[',']'),array('',''),$value);
        			$coordinatesArr[] =  explode(',', $val);
        		}
	        }
        }
        return $output = $this->checkFence($latlongs, $coordinatesArr, $result[0]->price_charge);
    }
    // check geo fence area
    public function checkFence($point, $polygon, $price_charge)
	{
	    if($polygon[0] != $polygon[count($polygon)-1])
	            $polygon[count($polygon)] = $polygon[0];
	    $j = 0;
	    $oddNodes = '';
	    $x = $point[1];
	    $y = $point[0];
	    $n = count($polygon);
	    for ($i = 0; $i < $n; $i++)
	    {
	        $j++;
	        if ($j == $n)
	        {
	            $j = 0;
	        }
	        if ((($polygon[$i][0] <= $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] <= $y) && ($polygon[$i][0] >=
	            $y)))
	        {
	            if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
	                $polygon[$i][1]) < $x)
	            {
	                $oddNodes = 'true';
	            }
	        }
	    }
	    $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
	    return $oddNodes;
    }

    public function getDeliveryByDistance($originLatLong, $destinationLatLong, $shop_id)
    {
        if(!$originLatLong)
        {
            $address = $this->common_model->getShopLatLong($shop_id);
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
            return $this->getFeeAccordingDistance($distance, $shop_id);
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

    public function getFeeAccordingDistance($distance, $shop_id)
    {
        $allDeliveryCharge = $this->common_model->getMultipleRows("delivery_charge", "shop_id", $shop_id);
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

    // add review
	public function addReview_post(){
        $requestBody = json_decode($this->input->raw_input_stream, true);
		$add_data = array(                   
	        'shop_id'=>$requestBody['shop_id'],
	        'user_id'=>$requestBody['user_id'],
	        'review'=> $requestBody['review'],
	        'rating'=>$requestBody['note'],
	        'status'=>1,
	        'created_by' => $requestBody['user_id'],
	    ); 
	    $review_id = $this->common_model->addData('review',$add_data); 
	    echo 'success';
	}
}
?>
<?php
class Order_model extends CI_Model {
	function __construct()
	{
		parent::__construct();		        
	}	
	// method for getting all
	public function getGridList($sortFieldName = '', $sortOrder = 'DESC', $displayStart = 0, $displayLength = 10,$order_status)
	{
		if($this->input->post('page_title') != ''){
			$this->db->where("CONCAT(u.first_name,' ',u.last_name) like '%".$this->input->post('page_title')."%'");
		}
		if($this->input->post('order') != ''){
			$this->db->like('o.entity_id', $this->input->post('order'));
		}
		if($this->input->post('driver') != ''){
			$this->db->where("CONCAT(driver.first_name,' ',driver.last_name) like '%".$this->input->post('driver')."%'");
		}
		if($this->input->post('Status') != ''){
			$this->db->like('o.status', $this->input->post('Status'));
		}
		if($this->input->post('restaurant') != ''){
            $name = $this->input->post('restaurant');
            $where = "(restaurant.name LIKE '%".$this->input->post('restaurant')."%' OR (order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*'))";
            $this->db->or_where($where);
        }
		if($this->input->post('order_total') != ''){
			$this->db->like('o.total_rate', $this->input->post('order_total'));
		}
		if($this->input->post('order_status') != ''){
			$this->db->like('o.order_status', $this->input->post('order_status'));
		}
		if($this->input->post('order_date') != ''){
			$this->db->like('o.created_date', $this->input->post('order_date'));
		}
		if($this->input->post('order_delivery') != ''){
			$this->db->where('o.order_delivery',$this->input->post('order_delivery'));
		}
		$this->db->select('o.order_delivery,o.pre_order_delivery_date,o.total_rate as rate,o.order_status as ostatus,o.status,o.entity_id as entity_id,o.created_date,o.restaurant_id,u.first_name as fname,u.last_name as lname,u.entity_id as user_id,order_status.order_status as orderStatus,driver.first_name,driver.last_name,driver.entity_id as driver_id,order_detail.restaurant_detail,restaurant.name,restaurant.currency_id,o.address_id');
		$this->db->join('users as u','o.user_id = u.entity_id','left');
		$this->db->join('restaurant','o.restaurant_id = restaurant.entity_id','left');
		$this->db->join('order_status','o.entity_id = order_status.order_id','left');
		$this->db->join('order_driver_map','o.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
		$this->db->join('order_detail','o.entity_id = order_detail.order_id','left'); 
		$this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
		if($this->session->userdata('UserType') == 'Admin'){
			$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));
		}
		if($order_status){
			$this->db->where('o.order_status',$order_status);
		}
		$this->db->group_by('o.entity_id');
		$result['total'] = $this->db->count_all_results('order_master as o');

		if($sortFieldName != '')
			$this->db->order_by($sortFieldName, $sortOrder);
		if($this->input->post('page_title') != ''){
			$this->db->where("CONCAT(u.first_name,' ',u.last_name) like '%".$this->input->post('page_title')."%'");
		}
		if($this->input->post('driver') != ''){
			$this->db->where("CONCAT(driver.first_name,' ',driver.last_name) like '%".$this->input->post('driver')."%'");
		}
		if($this->input->post('Status') != ''){
			$this->db->like('o.status', $this->input->post('Status'));
		}
		if($this->input->post('restaurant') != ''){
            $name = $this->input->post('restaurant');
            $where = "(restaurant.name LIKE '%".$this->input->post('restaurant')."%' OR (order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*'))";
            $this->db->where($where);
        }
		if($this->input->post('order_total') != ''){
			$this->db->like('o.total_rate', $this->input->post('order_total'));
		}
		if($this->input->post('order_status') != ''){
			$this->db->like('o.order_status', $this->input->post('order_status'));
		}
		if($this->input->post('order') != ''){
			$this->db->like('o.entity_id', $this->input->post('order'));
		}
		if($this->input->post('order_date') != ''){
			$this->db->like('o.created_date', $this->input->post('order_date'));
		}
		if($this->input->post('order_delivery') != ''){
			$this->db->where('o.order_delivery',$this->input->post('order_delivery'));
		}
		if($displayLength>1)
			$this->db->limit($displayLength,$displayStart);  
		$this->db->select('o.order_delivery,o.pre_order_delivery_date,o.total_rate as rate,o.order_status as ostatus,o.status,o.restaurant_id,o.created_date,o.entity_id as entity_id,o.user_id,u.first_name as fname,u.last_name as lname,u.entity_id as user_id,order_status.order_status as orderStatus,driver.first_name,driver.last_name,driver.entity_id as driver_id,order_detail.restaurant_detail,restaurant.name,restaurant.currency_id,o.address_id');
		$this->db->join('users as u','o.user_id = u.entity_id','left');   
		$this->db->join('order_detail','o.entity_id = order_detail.order_id','left'); 
		$this->db->join('order_status','o.entity_id = order_status.order_id','left');
		$this->db->join('restaurant','o.restaurant_id = restaurant.entity_id','left');
		$this->db->join('order_driver_map','o.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
		$this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
		if($order_status){
			$this->db->where('o.order_status',$order_status);
		}  
		if($this->session->userdata('UserType') == 'Admin'){
			$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));
		}
		$this->db->group_by('o.entity_id');
		$result['data'] = $this->db->get('order_master as o')->result();    

		return $result;
	}		
	// method for adding 
	public function addData($tblName,$Data)
	{   
		$this->db->insert($tblName,$Data);            
		return $this->db->insert_id();
	} 
	// method for adding 
	public function addBatch($tblName,$Data)
	{   
		$this->db->insert_batch($tblName,$Data);            
		return $this->db->insert_id();
	}
	// get the drivers to asiign to the orders
	public function getDrivers(){
		$this->db->select('users.entity_id,users.first_name,users.last_name');
		$this->db->where('user_type','Driver');  
		if($this->session->userdata('UserType') == 'Admin'){
			$this->db->where('created_by',$this->session->userdata('UserID'));  
		}  
		return $this->db->get('users')->result();
	}
	// assign driver 
	public function getOrderDetails($order_id){ 
		$this->db->select("(6371 * acos ( cos ( radians(user_address.latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians(user_address.longitude) ) + sin ( radians(user_address.latitude) ) * sin( radians( address.latitude )))) as distance");
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->join('user_address','order_master.address_id = user_address.entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        return $distance = $this->db->get('order_master')->result();
	}
	
	// method to get details by id
	public function getEditDetail($entity_id)
	{
		$this->db->select('order.*');
		$this->db->select('res.name, address.address,address.landmark,address.city,address.zipcode,u.first_name as first_name,u.last_name as last_name,u.phone_number as phone_number, u.mobile_number as mobile_number ,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode');
		$this->db->join('restaurant as res','order.restaurant_id = res.entity_id','left');
		$this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
		$this->db->join('users as u','order.user_id = u.entity_id','left');
		$this->db->join('user_address as uaddress','u.entity_id = uaddress.user_entity_id','left');
		return  $this->db->get_where('order_master as order',array('order.entity_id'=>$entity_id))->first_row();
	}
	
	// method to get details by id
	public function __getEditDetail($entity_id)
	{
		$this->db->select('order.entity_id, order.user_id, order.restaurant_id, order.address_id, order.coupon_id, order.total_rate, order.subtotal, order.tax_rate, order.tax_type, order.coupon_discount, order.coupon_name, order.coupon_amount, order.coupon_type, order.order_delivery, order.pre_order_delivery_date, order.payment_option, order.status, order.accept_order_time, order.order_date, order.status, order.order_no, order.delivery_charge, order.extra_comment, order.created_by, order.create_date, order.updated_by,order.updated_date,res.name, address.address,address.landmark,address.city,address.zipcode,u.first_name as first_name,u.last_name as last_name,u.phone_number as phone_number, u.mobile_number as mobile_number ,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode');
		$this->db->join('restaurant as res','order.restaurant_id = res.entity_id','left');
		$this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
		$this->db->join('users as u','order.user_id = u.entity_id','left');
		$this->db->join('user_address as uaddress','u.entity_id = uaddress.user_entity_id','left');
		return  $this->db->get_where('order_master as order',array('order.entity_id'=>$entity_id))->first_row();
	}
	public function _getEditDetail($entity_id)
	{
		$this->db->select('order.*,res.name, address.address,address.landmark,address.city,address.zipcode,u.first_name as first_name,u.last_name as last_name,u.phone_number as phone_number, u.mobile_number as mobile_number ,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode');
		$this->db->join('restaurant as res','order.restaurant_id = res.entity_id','left');
		$this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
		$this->db->join('users as u','order.user_id = u.entity_id','left');
		$this->db->join('user_address as uaddress','u.entity_id = uaddress.user_entity_id','left');
		return  $this->db->get_where('order_master as order',array('order.entity_id'=>$entity_id))->first_row();
	}
	// update data common function
	public function updateData($Data,$tblName,$fieldName,$ID)
	{        
			$this->db->where($fieldName,$ID);
			$this->db->update($tblName,$Data);            
			return $this->db->affected_rows();
	}
	 // updating status and send request to driver
	public function UpdatedStatus($tblname,$entity_id,$restaurant_id,$order_id){
		$this->db->set('status',1)->where('entity_id',$order_id)->update('order_master');
		$this->db->set('accept_order_time',date("Y-m-d H:i:s"))->where('entity_id',$order_id)->update('order_master');
		//send notification to user
		$this->db->select('users.entity_id,users.device_id,order_delivery,users.language_slug');
        $this->db->join('users','order_master.user_id = users.entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        $device = $this->db->get('order_master')->first_row();
        
        if($device->device_id){  
        	//get langauge
        	$languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
        	$this->lang->load('messages_lang', $languages->language_directory);
            #prep the bundle
            $fields = array();            
            $message = $this->lang->line('push_order_accept');
            $fields['to'] = $device->device_id; // only one user to send push notification
            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
            $fields['data'] = array ('screenType'=>'order');
           
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
        //send notification to driver
	    // if($device->order_delivery == 'Delivery'){    
	    if(false){    
	        $this->db->select('users.entity_id');
	        $this->db->where('user_type','Driver');
	        $driver = $this->db->get('users')->result_array();
	          
	        $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
	        $this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
	        $this->db->where('users.status',1);
	        if(!empty($driver)){
	        	$this->db->where_in('driver_id',array_column($driver, 'entity_id'));
	        }
	        $this->db->where('driver_traking_map.created_date = (SELECT
		        driver_traking_map.created_date
		    FROM
		        driver_traking_map
		    WHERE
		        driver_traking_map.driver_id = users.entity_id
		    ORDER BY
		    	driver_traking_map.created_date desc
		    LIMIT 1)');
	        $detail = $this->db->get('driver_traking_map')->result();
	        
	        $flag = false;
	        if(!empty($detail)){
	            foreach ($detail as $key => $value) {
	                $longitude = $value->longitude;
	                $latitude = $value->latitude;
	                $this->db->select("(6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
	                $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
	                $this->db->where('restaurant.entity_id',$restaurant_id);
	                $this->db->having('distance <',NEAR_KM);
	                $result = $this->db->get('restaurant')->result();
	                if(!empty($result)){
	                    if($value->device_id){
	                    	//get langauge
	                    	$languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
        					$this->lang->load('messages_lang', $languages->language_directory); 
	                        $flag = true;   
	                        $array = array(
	                            'order_id'=>$order_id,
	                            'driver_id'=>$value->driver_id,
	                            'date'=>date('Y-m-d H:i:s'),
	                            'distance'=>$result[0]->distance
	                        );
	                        $id = $this->addData('order_driver_map',$array);
	                        #prep the bundle
	                        $fields = array();            
	                        $message = $this->lang->line('push_new_order');
	                        $fields['to'] = $value->device_id; // only one user to send push notification
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
	                }
	            }
	        }
	    }
	}
	// delete
	public function ajaxDelete($tblname,$entity_id)
	{
		$this->db->delete($tblname,array('entity_id'=>$entity_id));  
	}
	//get users detail
	public function getUsersDetail($user_id){
		$this->db->select('users.first_name');
		$this->db->where('entity_id',$user_id);
		return $this->db->get('users')->result();
	}
	//get list
	public function getListData($tblname){
		if($tblname == 'users'){
			$this->db->select('first_name,last_name,entity_id');
			$this->db->where('status',1);
			$this->db->where('user_type !=','MasterAdmin');
			if($this->session->userdata('UserType') == 'Admin'){
				$this->db->where('created_by',$this->session->userdata('UserID'));  
			}        
			return $this->db->get($tblname)->result();
		}else if($tblname == 'restaurant'){
			$this->db->select('name,entity_id,amount_type,amount');
			$this->db->where('status',1);
			if($this->session->userdata('UserType') == 'Admin'){
				$this->db->where('created_by',$this->session->userdata('UserID'));  
			}
			return $this->db->get($tblname)->result();
		}else{
		    $this->db->select('name,entity_id,amount_type,amount');
			$this->db->where('status',1);
			return $this->db->get($tblname)->result();
		}
	}
	//get items
	public function getItem($entity_id){
		$this->db->select('entity_id,name,price');
		$this->db->where('restaurant_id',$entity_id);
		$this->db->where('status',1);
		return $this->db->get('restaurant_menu_item')->result();
	}
	//get address
	public function getAddress($entity_id){
		$this->db->where('user_entity_id',$entity_id);
		return $this->db->get('user_address')->result();
	}
	//get invoice data
	public function getInvoiceMenuItem($entity_id){
		$this->db->where('order_id',$entity_id);
		return $this->db->get('order_detail')->first_row();
	}
	//get user data
	public function getUserDate($entity_id){
		$this->db->select('device_id,bot_user_id,language_slug');
		$this->db->where('entity_id',$entity_id);
		return $this->db->get('users')->first_row();
	}
	//delete multiple order
	public function deleteMultiOrder($order_id){
		$this->db->where_in('entity_id',$order_id);
		$this->db->delete('order_master');
		return $this->db->affected_rows();
	}
	//get item name
	public function getItemName($item_id){
		$this->db->where('entity_id',$item_id);
		return $this->db->get('restaurant_menu_item')->first_row();
	}
	//get order status history
	public function statusHistory($order_id){
		$this->db->where('order_id',$order_id);
		return $this->db->get('order_status')->result();
	}
	//get rest detail
	public function getRestaurantDetail($entity_id){
        $this->db->select('restaurant.name,restaurant.image,restaurant.phone_number,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,currencies.currency_symbol');
        $this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left'); 
        $this->db->where('restaurant.entity_id',$entity_id);
        return $this->db->get('restaurant')->first_row();
	}
	//get list of restaurant
	public function getRestaurantList(){
		if($this->session->userdata('UserType') == 'Admin'){
			$this->db->where('created_by',$this->session->userdata('UserID'));  
		}   
		return $this->db->get('restaurant')->result();
	}
	//generate report data
	public function generate_report($restaurant_id,$order_type,$order_date){
		$this->db->select('order_master.*,restaurant.name,users.first_name,users.last_name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
		$this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
		$this->db->join('users','order_master.user_id = users.entity_id','left');
		$this->db->where('restaurant_id',$restaurant_id);
		if($order_type){
			$this->db->where('order_delivery',$order_type);
		}
		if($order_date != ''){
			$this->db->like('order_master.created_date', date('Y-m-d',strtotime($order_date))); 
		}
		/*if($order_date){
			$monthsplit = explode("-",$order_date);         
			$this->db->where('MONTH(order_master.created_date)',$monthsplit[0]);
			$this->db->where('YEAR(order_master.created_date)',$monthsplit[1]);
		}*/
		return $this->db->get('order_master')->result();
	}
	
	public function getDevice($user_id){
	    $this->db->select('users.entity_id,users.device_id,users.language_slug');
        $this->db->where('users.entity_id',$user_id);
        return $this->db->get('users')->first_row(); 
	}
	
	//get order details
	public function orderDetails($entity_id){
		$this->db->where('order_master.entity_id',$entity_id);
		$this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
		return $this->db->get('order_master')->result();
	}


    // get latest order of logged in user
    public function getLatestOrder($order_id){
        $this->db->select('order_master.entity_id as master_order_id,order_master.*,order_detail.*,order_driver_map.driver_id,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant_address.address,restaurant.timings,restaurant.image as rest_image,restaurant.name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('driver_traking_map','users.entity_id = driver_traking_map.driver_id AND driver_traking_map.traking_id = (SELECT driver_traking_map.traking_id FROM driver_traking_map WHERE driver_traking_map.driver_id = users.entity_id ORDER BY created_date DESC LIMIT 1)','left');

        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel")');
        $this->db->where('order_master.entity_id',$order_id);
        
        $result = $this->db->get('order_master')->first_row();
        if (!empty($result)) {
            $result->placed = $result->created_date;
            $result->preparing = '';
            $result->onGoing = '';
            $result->delivered = '';
            // get order status
            $this->db->where('order_status.order_id',$result->master_order_id);
            $Ostatus = $this->db->get('order_status')->result_array();
            if (!empty($Ostatus)) {
                foreach ($Ostatus as $key => $ovalue) {
                    if ($ovalue['order_status'] == 'accepted_by_restaurant') {
                        $result->accepted_by_restaurant = $ovalue['time'];
                    }
                    if ($ovalue['order_status'] == 'preparing') {
                        $result->preparing = $ovalue['time'];
                    }
                    if ($ovalue['order_status'] == 'onGoing') {
                        $result->onGoing = $ovalue['time'];
                    }
                    if ($ovalue['order_status'] == 'delivered') {
                        $result->delivered = $ovalue['time'];
                    }
                }
            }
            $user_detail = unserialize($result->user_detail);
            if (!empty($user_detail)) {
                $result->user_first_name = $user_detail['first_name'];
                $result->user_address = $user_detail['address'];
                $result->user_latitude = $user_detail['latitude'];
                $result->user_longitude = $user_detail['longitude'];
                $result->image = ($result->image)?image_url.$result->image:'';
            }
        }
        return $result;
    }
}
?>
<?php
class Driver_api_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    }
     /***************** General API's Function *****************/
    public function getLanguages($current_lang){
        $result = $this->db->select('*')->get_where('languages',array('language_slug'=>$current_lang))->first_row();
        return $result;
    }
    public function getRecord($table,$fieldName,$where)
    {
        $this->db->where($fieldName,$where);
        return $this->db->get($table)->first_row();
    } 
    //get record with multiple where
    public function getRecordMultipleWhere($table,$whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }
  
    // Login
    public function getLogin($phone,$password)
    {        
        $enc_pass  = md5(SALT.$password);
        $this->db->select('entity_id,first_name,last_name,status,active,mobile_number,image,notification,driver_traking_map.latitude');
        $this->db->join('driver_traking_map','users.entity_id = driver_traking_map.driver_id','left');
        $this->db->where('mobile_number',$phone);
        $this->db->where('password',$enc_pass);
        $this->db->where('user_type','Driver');
        return $this->db->get('users')->first_row(); 
    }
    // Update User
    public function updateUser($tableName,$data,$fieldName,$UserID)
    {
        $this->db->where($fieldName,$UserID);
        $this->db->update($tableName,$data);
    }
    // check token for every API Call
    public function checkToken($token, $userid)
    {
        return $this->db->get_where('users',array('mobile_number'=>$token,' entity_id'=>$userid))->first_row();
    }
    // Common Add Records
    public function addRecord($table,$data)
    {
        $this->db->insert($table,$data);
        return $this->db->insert_id();
    }
    // Common Add Records Batch
    public function addRecordBatch($table,$data)
    {
        return $this->db->insert_batch($table, $data);
    }
    public function deleteRecord($table,$fieldName,$where)
    {
        $this->db->where($fieldName,$where);
        return $this->db->delete($table);
    }
    //get event
    public function getallOrder($user_id){
        $currentDateTime = date('Y-m-d H:i:s');
        //current
        $this->db->select('order_detail.shop_detail,order_detail.order_id,order_driver_map.driver_map_id,order_master.order_status,order_master.total_rate,order_master.subtotal,order_master.created_date,order_detail.user_detail,order_detail.item_detail,users.mobile_number,users.image,shop_address.latitude,shop_address.longitude,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->join('users','order_master.user_id = users.entity_id','left');
        $this->db->join('shop_address','order_master.shop_id = shop_address.shop_entity_id','left');
        $this->db->join('shop','order_master.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel")');
        $this->db->where('order_master.order_delivery','Delivery');
        $this->db->where('DATE(order_master.order_date)',date('Y-m-d'));
        $this->db->order_by('order_master.entity_id','desc');
        $current_order = $this->db->get('order_master')->result();
  
        $current = array();
        if(!empty($current_order)){
            foreach ($current_order as $key => $value) {
                if(!isset($value->order_id)){
                    $current[$value->order_id] = array();
                }
                if(isset($value->order_id)){
                    $shop_detail = unserialize($value->shop_detail);
                    $user_detail = unserialize($value->user_detail);
                    $item_detail = unserialize($value->item_detail);
                    $current[$value->order_id]['name'] = $shop_detail->name;
                    $current[$value->order_id]['res_phone_number'] = $shop_detail->phone_number;
                    $current[$value->order_id]['res_address'] = $shop_detail->address;
                    $current[$value->order_id]['image'] = ($shop_detail->image)?image_url.$shop_detail->image:'';
                    $current[$value->order_id]['res_latitude'] = $value->latitude;
                    $current[$value->order_id]['res_longitude'] = $value->longitude;
                    $current[$value->order_id]['order_id'] = $value->order_id;
                    $current[$value->order_id]['driver_map_id'] = $value->driver_map_id;
                    $current[$value->order_id]['subtotal'] = $value->subtotal;
                    $current[$value->order_id]['total_rate'] = $value->total_rate;
                    $current[$value->order_id]['currency_code'] = $value->currency_code;
                    $current[$value->order_id]['currency_symbol'] = $value->currency_symbol;
                    $current[$value->order_id]['order_status'] = $value->order_status;
                    $current[$value->order_id]['user_name'] = $user_detail['first_name'];
                    $current[$value->order_id]['latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                    $current[$value->order_id]['longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                    $current[$value->order_id]['address'] = $user_detail['address'].' '.$user_detail['landmark'].' '.$user_detail['zipcode'].' '.$user_detail['city'];
                    $current[$value->order_id]['phone_number'] = $value->mobile_number;
                    $current[$value->order_id]['user_image'] = ($value->image)?image_url.$value->image:'';
                    $current[$value->order_id]['date'] = date('Y-m-d H:i',strtotime($value->created_date));
                    $current[$value->order_id]['customer_details'] = $user_detail;
                    $current[$value->order_id]['order_details'] = $item_detail;
                }
            }
        }
        $finalArray = array();
        foreach ($current as $key => $val) {
           $finalArray[] = $val; 
        }
        $data['current'] = $finalArray;
        //past
        $this->db->select('order_detail.shop_detail,order_detail.order_id,order_driver_map.driver_map_id,order_master.order_status,order_driver_map.cancel_reason,order_master.total_rate,order_master.subtotal,order_master.created_date,order_detail.user_detail,order_detail.item_detail,users.mobile_number,users.image,shop_address.latitude,shop_address.longitude,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->join('users','order_master.user_id = users.entity_id','left');
		$this->db->join('shop_address','order_master.shop_id = shop_address.shop_entity_id','left');
        $this->db->join('shop','order_master.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
         $this->db->where('order_master.order_delivery','Delivery');
        $this->db->order_by('order_master.entity_id','desc');
        $past_order = $this->db->get('order_master')->result();
        $past = array();
        if(!empty($past_order)){
            foreach ($past_order as $key => $value) {
                if(!isset($value->order_id)){
                    $past[$value->order_id] = array();
                }
                if(isset($value->order_id)){
                    $shop_detail = unserialize($value->shop_detail);
                    $user_detail = unserialize($value->user_detail);
                    $item_detail = unserialize($value->item_detail);
                    $past[$value->order_id]['name'] = $shop_detail->name;
                    $past[$value->order_id]['res_phone_number'] = $shop_detail->phone_number;
                    $past[$value->order_id]['res_address'] = $shop_detail->address;
                    $past[$value->order_id]['image'] = ($shop_detail->image)?image_url.$shop_detail->image:'';
                    $past[$value->order_id]['res_latitude'] = $value->latitude;
                    $past[$value->order_id]['res_longitude'] = $value->longitude;
                    $past[$value->order_id]['order_id'] = $value->order_id;
                    $past[$value->order_id]['driver_map_id'] = $value->driver_map_id;
                    $past[$value->order_id]['subtotal'] = $value->subtotal;
                    $past[$value->order_id]['total_rate'] = $value->total_rate;
                    $past[$value->order_id]['currency_code'] = $value->currency_code;
                    $past[$value->order_id]['currency_symbol'] = $value->currency_symbol;
                    $past[$value->order_id]['order_status'] = $value->order_status;
                    $past[$value->order_id]['user_name'] = $user_detail['first_name'];
                    $past[$value->order_id]['latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                    $past[$value->order_id]['longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                    $past[$value->order_id]['address'] = $user_detail['address'].' '.$user_detail['landmark'].' '.$user_detail['zipcode'].' '.$user_detail['city'];
                    $past[$value->order_id]['phone_number'] = $value->mobile_number;
                    $past[$value->order_id]['user_image'] = ($value->image)?image_url.$value->image:'';
                    $past[$value->order_id]['date'] = date('Y-m-d H:i',strtotime($value->created_date));
                    $past[$value->order_id]['customer_details'] = $user_detail;
                    $past[$value->order_id]['order_details'] = $item_detail;
                }
            }

        }
        $final = array();
        foreach ($past as $key => $val) {
           $final[] = $val; 
        }
        $data['past'] = $final;
        return $data;
    } 
    //accept order
    public function acceptOrder($order_id,$driver_map_id,$user_id,$status = 'preparing')
    {
        $count = $this->db->set('is_accept',1)->where('driver_id',$user_id)->where('order_id', $order_id)->where('driver_map_id',$driver_map_id)->update('order_driver_map');
        if($count == 1){
            $this->db->where('order_id', $order_id);
            $this->db->where('is_accept !=',1);
            $this->db->where('driver_id !=',$user_id);
            $this->db->delete('order_driver_map');
        }
        $this->db->set('order_status',$status)->where('entity_id', $order_id)->update('order_master');
        //get users to send notifcation
        $this->db->select('users.entity_id,users.device_id,users.language_slug,users.first_name,users.last_name,users.mobile_number,order_detail.user_detail,shop_address.latitude,shop_address.longitude');
        $this->db->join('order_master','users.entity_id = order_master.user_id','left');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('shop_address','order_master.shop_id = shop_address.shop_entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        $device = $this->db->get('users')->first_row();
        
        // load language
        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
        $this->lang->load('messages_lang', $languages->language_directory);
        
        $info = array();
        if($device->device_id){  
            #prep the bundle
            $fields = array();            
            $message = $status == 'preparing' ? $this->lang->line('order_preparing') : $this->lang->line('order_ongoing');
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
        $user_detail = unserialize($device->user_detail);
        $info['address'] = $user_detail['address'].' '.$user_detail['landmark'].' '.$user_detail['zipcode'].' '.$user_detail['city'];
        $info['latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
        $info['longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
        $info['phone_number'] = $device->mobile_number;
        $info['res_latitude'] = $device->latitude;
        $info['res_longitude'] = $device->longitude;
        $info['name'] = $device->first_name.' '.$device->last_name; 
        $info['order_user_id'] = $device->entity_id;
        return $info;
    }
    //order delivered
    public function deliveredOrder($order_id,$status,$subtotal)
    {   
        $this->db->set('order_status',$status)->where('entity_id', $order_id)->update('order_master');
        if($status == 'delivered'){
            $this->db->select('order_driver_map.distance');
            $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
            $this->db->where('order_master.entity_id',$order_id);
            $distance = $this->db->get('order_master')->first_row();
            
            $comsn = '';
            if($distance->distance > 3){
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_more'))->first_row();
            }else{
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_less'))->first_row(); 
            }
            if($comsn){
                $data = array('driver_commission'=>$comsn->OptionValue,'commission'=>$comsn->OptionValue);
                $this->db->where('order_id', $order_id);
                $this->db->update('order_driver_map',$data);
            } 
        }

        $this->db->select('item_detail,user_detail,currencies.currency_symbol,currencies.currency_code,order_master.shop_id');
        $this->db->join('order_master','order_detail.order_id = order_master.entity_id','left');
        $this->db->join('shop','order_master.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('order_id',$order_id);
        $detail =  $this->db->get('order_detail')->first_row();
        $info = array();
        if(!empty($detail)){
            $order_detail = unserialize($detail->item_detail);
            $user_detail = unserialize($detail->user_detail);
            $info['order_detail'] = $order_detail;
            $info['currency_code'] = $detail->currency_code;
            $info['currency_symbol'] = $detail->currency_symbol;
            $info['address'] = $user_detail['address'].' '.$user_detail['landmark'].' '.$user_detail['zipcode'].' '.$user_detail['city'];
        }

        //get users to send notifcation
        $this->db->select('users.entity_id,users.device_id,users.language_slug');
        $this->db->join('order_master','users.entity_id = order_master.user_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        $device = $this->db->get('users')->first_row();
        // load language
        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
        $this->lang->load('messages_lang', $languages->language_directory);
 
        if($device->device_id){  
            #prep the bundle
            $fields = array();            
            $message = $this->lang->line('push_order_delived');
            $fields['to'] = $device->device_id; // only one user to send push notification
            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
            $fields['data'] = array ('screenType'=>'delivery','shop_id'=>$detail->shop_id);
           
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
        return $info;

    }
    //get commission list
    public function getCommissionList($user_id)
    {
        //last order
        $this->db->select('order_master.total_rate,order_master.order_status,order_status.time,order_detail.shop_detail,order_detail.user_detail,order_driver_map.order_id,order_driver_map.driver_id,order_driver_map.commission,order_master.order_status,order_master.total_rate,currencies.currency_symbol,currencies.currency_code');
        
        $this->db->join('shop','order_master.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->join('order_status','order_driver_map.order_id = order_status.order_id','left');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->order_by('order_master.entity_id','desc');
        $this->db->limit(1);
        $details =  $this->db->get('order_master')->result();
        $last_address = array();
        $last_user_id = '';
        $finalArray = array();
        if(!empty($details)){
            foreach ($details as $key => $value) {
                $last_user_id = $value->order_id;
                if(!isset($value->order_id)){
                    $last_address[$value->order_id] = array();
                }
                if(isset($value->order_id)){
                    $address = unserialize($value->user_detail);
                    $shop_detail = unserialize($value->shop_detail);
                    $last_address[$value->order_id]['time'] = ($value->time)?date('h:i A',strtotime($value->time)):'';
                    $last_address[$value->order_id]['date'] =  ($value->time)?date('l j M',strtotime($value->time)):'';
                    $last_address[$value->order_id]['order_status'] = ucfirst($value->order_status);
                    $last_address[$value->order_id]['total_rate'] = $value->total_rate;
                    $last_address[$value->order_id]['order_id'] = $value->order_id;
                    $last_address[$value->order_id]['commission'] = ($value->commission)?$value->commission:'';
                    $last_address[$value->order_id]['name'] = $shop_detail->name;
                    $last_address[$value->order_id]['image'] = ($shop_detail->image)?image_url.$shop_detail->image:'';
                    $last_address[$value->order_id]['address'] = $address['address'].' '.$address['landmark'].' '.$address['zipcode'].' '.$address['city'];
                    $last_address[$value->order_id]['currency_symbol'] = $shop_detail->currency_symbol;
                    $last_address[$value->order_id]['currency_code'] = $shop_detail->currency_code;
                }
            }
            foreach ($last_address as $key => $val) {
               $finalArray[] = $val; 
            }
        }
       
        $data['last'] = $finalArray;
        //previous order
        $this->db->select('order_master.total_rate,order_master.order_status,order_status.time,order_detail.shop_detail,order_detail.user_detail,order_driver_map.order_id,order_driver_map.driver_id,order_driver_map.commission,order_master.order_status,order_master.total_rate');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->join('order_status','order_driver_map.order_id = order_status.order_id','left');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        if($last_user_id){
             $this->db->where('order_driver_map.order_id !=',$last_user_id);
        }
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->order_by('order_master.entity_id','desc');
        $details =  $this->db->get('order_master')->result();
        $user_address = array();
        $final = array();
        if(!empty($details)){
            foreach ($details as $key => $value) {
                if(!isset($value->order_id)){
                    $user_address[$value->order_id] = array();
                }
                if(isset($value->order_id)){
                    $address = unserialize($value->user_detail);
                    $shop_detail = unserialize($value->shop_detail);
                    $user_address[$value->order_id]['time'] = ($value->time)?date('h:i A',strtotime($value->time)):'';
                    $user_address[$value->order_id]['date'] =  ($value->time)?date('l j M',strtotime($value->time)):'';
                    $user_address[$value->order_id]['order_status'] = ucfirst($value->order_status);
                    $user_address[$value->order_id]['total_rate'] = $value->total_rate;
                    $user_address[$value->order_id]['order_id'] = $value->order_id;
                    $user_address[$value->order_id]['commission'] = ($value->commission)?$value->commission:'';
                    $user_address[$value->order_id]['name'] = $shop_detail->name;
                    $user_address[$value->order_id]['image'] = ($shop_detail->image)?image_url.$shop_detail->image:'';
                    $user_address[$value->order_id]['address'] = $address['address'].' '.$address['landmark'].' '.$address['zipcode'].' '.$address['city'];
                    $user_address[$value->order_id]['currency_symbol'] = $shop_detail->currency_symbol;
                    $user_address[$value->order_id]['currency_code'] = $shop_detail->currency_code;
                }
            }
            foreach ($user_address as $key => $val) {
               $final[] = $val; 
            }
        }
        $data['previous'] = $final;
        return $data;
    }
    //get user of order
    public function getUserofOrder($order_id){
        $this->db->select('users.device_id,users.language_slug');
        $this->db->join('users','order_master.user_id = users.entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        return $this->db->get('order_master')->first_row();
    }
}
?>
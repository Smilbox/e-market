<?php
class Myprofile_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    //server side check email exist
    public function checkEmail($Email,$UserID)
    {
        $this->db->where('email',$Email);
        $this->db->where('entity_id !=',$UserID);
        return $this->db->get('users')->num_rows();   
    }
    //server side check email exist
    public function checkPhone($Phone,$UserID)
    {
        $this->db->where('mobile_number',$Phone);
        $this->db->where('entity_id !=',$UserID);
        return $this->db->get('users')->num_rows();  
    }
    //get order detail
    public function getOrderDetail($flag,$user_id,$order_id){
        $this->db->select('order_master.*,order_detail.*,order_driver_map.driver_id,status.order_status as ostatus,status.time,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,shop_address.latitude as resLat,shop_address.longitude as resLong,shop_address.address,shop.timings,shop.image as rest_image,shop.name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_status as status','order_master.entity_id = status.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('driver_traking_map','order_driver_map.driver_id = driver_traking_map.driver_id','left');
        $this->db->join('shop_address','order_master.shop_id = shop_address.shop_entity_id','left');
        $this->db->join('shop','order_master.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        if($flag == 'process'){
            $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel" AND order_master.order_status != "complete")');
        } 
        if($flag == 'past'){
            $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel" OR order_master.order_status = "complete")');
        }
        if ($user_id != '') {
            $this->db->where('order_master.user_id',$user_id);
        }
        if ($order_id != '') {
            $this->db->where('order_master.entity_id',$order_id);
        }
        $this->db->order_by('order_master.entity_id','desc');
        $result =  $this->db->get('order_master')->result();
     
        $items = array();
        foreach ($result as $key => $value) {
            $currency_symbol = $this->common_model->getCurrencySymbol($value->currency_id);
            
            if(!isset($items[$value->order_id])){
                $items[$value->order_id] = array();
                $items[$value->order_id]['preparing'] = '';
                $items[$value->order_id]['onGoing'] = '';
                $items[$value->order_id]['delivered'] = '';
            }
            if(isset($items[$value->order_id])) 
            {        
                $items[$value->order_id]['order_id'] = $value->order_id;
                $items[$value->order_id]['shop_id'] = $value->shop_id;
                $items[$value->order_id]['shop_name'] = $value->name;
                $items[$value->order_id]['shop_image'] = $value->rest_image;
                $items[$value->order_id]['shop_address'] = $value->address;
                if($value->coupon_name){
                    $discount = array('label'=>$this->lang->line('discount').'('.$value->coupon_name.')','value'=>$value->coupon_discount,'label_key'=>"Discount");
                }else{
                    $discount = '';
                }
                
                if($discount){
                $items[$value->order_id]['price'] = array(
                    array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                    $discount,
                   /* array('label'=>'Service Fee','value'=>$value->tax_rate.$type),*/
                    array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                    array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_discount,'label_key'=>"Coupon Amount"),
                    array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }else{
                    $items[$value->order_id]['price'] = array(
                    array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                   /* array('label'=>'Service Fee','value'=>$value->tax_rate.$type),*/
                    array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                    array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_discount,'label_key'=>"Coupon Amount"),
                    array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }
                $timing =  $value->timings;
                if($timing){
                   $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values) {
                        $day = date("l");
                        if($keys == strtolower($day)){
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?date('g:i A',strtotime($values['open'])):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?date('g:i A',strtotime($values['close'])):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']))?($values['close'] <= date('H:m'))?'close':'open':'close';
                        }
                    }
                    $items[$value->order_id]['timings'] = $newTimingArr[strtolower($day)];
                }
                $items[$value->order_id]['order_status'] = ucfirst($value->order_status);
                $items[$value->order_id]['total'] = $value->total_rate;
                $items[$value->order_id]['extra_comment'] =$value->extra_comment;
                $items[$value->order_id]['placed'] = date('g:i a',strtotime($value->order_date));
                if($value->ostatus == 'preparing')
                {
                    $items[$value->order_id]['preparing'] = ($value->time!="")?date('g:i A',strtotime($value->time)):'';                    
                }
                if($value->ostatus == 'onGoing')
                {
                    $items[$value->order_id]['onGoing'] = ($value->time!="")?date('g:i A',strtotime($value->time)):'';                    
                }
                if($value->ostatus == 'delivered')
                {
                    $items[$value->order_id]['delivered'] = ($value->time!="")?date('g:i A',strtotime($value->time)):'';                    
                }
                $items[$value->order_id]['order_date'] = date('Y-m-d H:i:s',strtotime($value->order_date));
                $item_detail = unserialize($value->item_detail);
                $value1 = array();
                if(!empty($item_detail)){
                    $data1 = array();
                    $count = 0;
                    foreach ($item_detail as $key => $valuee) {
                        $valueee = array();
                        $this->db->select('image,is_under_20_kg,status');
                        $this->db->where('entity_id',$valuee['item_id']);
                        $data = $this->db->get('shop_menu_item')->first_row();
                        // get order availability count
                        if (!empty($data)) {
                            if($data->status == 0) {
                                $count = $count + 1;
                            }
                        }
                        $data1['image'] = (!empty($data) && $data->image != '')?$data->image:'';
                        $data1['is_under_20_kg'] = (!empty($data) && $data->is_under_20_kg != '')?$data->is_under_20_kg:'';
                        $valueee['image'] = (!empty($data) && $data->image != '')?image_url.$data1['image']:'';
                        $valueee['is_under_20_kg'] = (!empty($data) && $data->is_under_20_kg != '')?$data1['is_under_20_kg']:'';
                        
                        if($valuee['is_customize'] == 1){
                            $customization = array();
                            foreach ($valuee['addons_category_list'] as $k => $val) {
                                $addonscust = array();
                                foreach ($val['addons_list'] as $m => $mn) {
                                    if($valuee['is_deal'] == 1){
                                        $addonscust[] = array(
                                            'add_ons_id'=>($mn['add_ons_id'])?$mn['add_ons_id']:'',
                                            'add_ons_name'=>$mn['add_ons_name'],
                                        );
                                    }else{
                                        $addonscust[] = array(
                                            'add_ons_id'=>($mn['add_ons_id'])?$mn['add_ons_id']:'',
                                            'add_ons_name'=>$mn['add_ons_name'],
                                            'add_ons_price'=>$mn['add_ons_price']
                                        );
                                       
                                    }
                                }
                                $customization[] = array(
                                    'addons_category_id'=>$val['addons_category_id'],
                                    'addons_category'=>$val['addons_category'],
                                    'addons_list'=>$addonscust
                                );
                            }
                            $valueee['addons_category_list'] = $customization;
                        }
                      
                        $valueee['menu_id'] = $valuee['item_id'];
                        $valueee['name'] = $valuee['item_name'];
                        $valueee['quantity'] = $valuee['qty_no'];
                        $valueee['price'] = ($valuee['rate'])?$valuee['rate']:'';
                        $valueee['is_customize'] = $valuee['is_customize'];
                        $valueee['is_deal'] = $valuee['is_deal'];
                        $valueee['offer_price'] = ($valuee['offer_price'])?$valuee['offer_price']:'';
                        $valueee['itemTotal'] = ($valuee['itemTotal'])?$valuee['itemTotal']:'';

                        $value1[] =  $valueee; 
                    } 
                }
         
                $user_detail = unserialize($value->user_detail);
                $items[$value->order_id]['user_latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                $items[$value->order_id]['user_longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                $items[$value->order_id]['resLat'] = $value->resLat;
                $items[$value->order_id]['resLong'] = $value->resLong;
                $items[$value->order_id]['items']  = $value1;
                $items[$value->order_id]['available'] = ($count == 0)?'true':'false';
                if($value->first_name && $value->order_delivery == 'Delivery'){
                    $driver['first_name'] =  $value->first_name;
                    $driver['last_name'] =  $value->last_name;
                    $driver['mobile_number'] =  $value->phone_code.$value->mobile_number;
                    $driver['latitude'] =  $value->latitude;
                    $driver['longitude'] =  $value->longitude;
                    $driver['image'] = ($value->image)?image_url.$value->image:'';
                    $driver['driver_id'] = ($value->driver_id)?$value->driver_id:'';
                    $items[$value->order_id]['driver'] = $driver;
                }
                $items[$value->order_id]['delivery_flag'] = ($value->order_delivery == 'Delivery')?'delivery':'pickup';
                $items[$value->order_id]['currency_symbol'] = $value->currency_symbol;
                $items[$value->order_id]['currency_code'] = $value->currency_code;
            }
        }
        $finalArray = array();
        foreach ($items as $nm => $va) {
            $finalArray[] = $va;
        }
        return $finalArray;
    }
    //get event
    public function getBooking($user_id,$event_flag,$event_id=NULL){
        $currentDateTime = date('Y-m-d H:i:s');
        //upcoming
        $this->db->select('event.entity_id as event_id,event.booking_date,event.no_of_people,event_detail.package_detail,event_detail.shop_detail,AVG (review.rating) as rating,currencies.currency_symbol,currencies.currency_code,shop.entity_id as shop_id,event.created_date');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id','left');
        $this->db->join('review','event.shop_id = review.shop_id','left');
        $this->db->join('shop','event.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('event.user_id',$user_id);

        if ($event_flag == "upcoming") {
            $this->db->where('event.booking_date >',$currentDateTime);
        }
        if ($event_flag == "past") {
            $this->db->where('event.booking_date <',$currentDateTime);
        }
        if ($event_id != '') {
            $this->db->where('event.entity_id',$event_id);
        }

        $this->db->group_by('event.entity_id');
        $this->db->order_by('event.entity_id','desc');
        $result = $this->db->get('event')->result();
        $events = array();
        foreach ($result as $key => $value) {
            $package_detail = '';
            $shop_detail = '';
            if(!isset($value->event_id)){
                $events[$value->event_id] = array();
            }
            if(isset($value->event_id)){
                $package_detail = unserialize($value->package_detail);
                $shop_detail = unserialize($value->shop_detail);
                $events[$value->event_id]['entity_id'] =  $value->event_id;
                $events[$value->event_id]['booking_date'] =  $value->booking_date;
                $events[$value->event_id]['no_of_people'] =  $value->no_of_people;
                $events[$value->event_id]['currency_code'] =  $value->currency_code;
                $events[$value->event_id]['currency_symbol'] =  $value->currency_symbol;

                $events[$value->event_id]['package_name'] =  (!empty($package_detail))?$package_detail['package_name']:'';
                $events[$value->event_id]['package_detail'] = (!empty($package_detail))?$package_detail['package_detail']:'';
                $events[$value->event_id]['package_price'] = (!empty($package_detail))?$package_detail['package_price']:'';
                
                $events[$value->event_id]['shop_id'] =  $value->shop_id;
                $events[$value->event_id]['name'] =  (!empty($shop_detail))?$shop_detail->name:'';
                $events[$value->event_id]['image'] =  (!empty($shop_detail) && $shop_detail->image != '')?image_url.$shop_detail->image:'';
                $events[$value->event_id]['address'] =  (!empty($shop_detail))?$shop_detail->address:'';
                $events[$value->event_id]['landmark'] =  (!empty($shop_detail))?$shop_detail->landmark:'';
                $events[$value->event_id]['city'] =  (!empty($shop_detail))?$shop_detail->city:'';
                $events[$value->event_id]['zipcode'] =  (!empty($shop_detail))?$shop_detail->zipcode:'';
                $events[$value->event_id]['rating'] =  $value->rating;
                $events[$value->event_id]['created_date'] =  $value->created_date;
            }
        }
        $finalArray = array();
        foreach ($events as $key => $val) {
           $finalArray[] = $val; 
        }
        return $finalArray;
    } 
    //get address
    public function getAddress($user_id,$address_id=NULL){
        $this->db->select('entity_id as address_id,user_entity_id,address,landmark,latitude,longitude,city,zipcode,is_main,search_area');
        $this->db->where('user_entity_id',$user_id);
        if ($address_id != '') {
            $this->db->where('entity_id',$address_id);
        }
        return $this->db->get('user_address')->result();
    }


}
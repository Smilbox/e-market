<?php
class Bot_api_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    }

    public function getAllRecord($table)
    {
        return $this->db->get($table)->result();
    }
    
    public function getQueryAllRecord($table)
    {
        $query = "SELECT * FROM " . $table;
        return $this->db->query($query)->result();
    }

    public function getRecord($table,$fieldName,$where)
    {
        $this->db->where($fieldName, urldecode($where));
        return $this->db->get($table)->first_row();
    }

    //get record with multiple where
    public function getRecordMultipleWhere($table,$whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }

    // check token for every API Call
    public function checkToken($token, $userid)
    {
        return $this->db->get_where('users',array('bot_user_id'=>$token,' entity_id'=>$userid))->first_row();
    }

    // RESTAURANT OPERATION
    public function getShopRecords($store_type)
    {
        $this->db->select("res.entity_id as shop_id,res.store_type_id, res.allow_24_delivery, res.flat_rate_24, res.shop_slug, res.language_slug ,res.name,res.timings,res.image,address.address,address.landmark,address.latitude, address.longitude,AVG(review.rating) as rating");
        $this->db->join('shop_address as address','res.entity_id = address.shop_entity_id','left');
        $this->db->join('review','res.entity_id = review.shop_id','left');
        $this->db->where('res.status', 1);
        $this->db->where('res.store_type_id',$store_type);
        $this->db->where('res.language_slug', 'fr');
        $this->db->group_by(array('res.entity_id', 'address.address', 'address.landmark'));
        $result =  $this->db->get('shop as res')->result();
        foreach ($result as $key => $value) {
            $timing = $value->timings;
            if($timing){
               $timing =  unserialize(html_entity_decode($timing));
               $newTimingArr = array();
               $allTimingArr = array();
                $day = date("l");
                foreach($timing as $keys=>$values) {
                    $day = date("l");
                    $hourNow = date('H:i');
                    if($keys == strtolower($day)){
                        $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?date('H:i',strtotime($values['open'])):'';
                        $newTimingArr[strtolower($day)]['close'] =(!empty($values['close']))?date('H:i',strtotime($values['close'])):'';
                        $newTimingArr[strtolower($day)]['openeve'] = (!empty($values['openeve']))?date('H:i',strtotime($values['openeve'])):'';
                        $newTimingArr[strtolower($day)]['closeeve'] = (!empty($values['closeeve']))?date('H:i',strtotime($values['closeeve'])):'';
                        $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                        $close = 'close';
                        if (!empty($values['open']) && !empty($values['close']) && $hourNow >= date('H:i', strtotime($values['open'])) && $hourNow <= date('H:i', strtotime($values['close']))) {
                            if (!empty($values['open'])) { 
                                $close = ($hourNow < date('H:i', strtotime($values['open'])))?'close':'open';
                            }
                            if (!empty($values['close'])) { 
                                $close = ($hourNow >= date('H:i', strtotime($values['close'])))?'close':'open';
                            }
                        } 
                        if (!empty($values['openeve']) && !empty($values['closeeve']) && $hourNow >= date('H:i', strtotime($values['openeve'])) && $hourNow <= date('H:i', strtotime($values['closeeve']))) {
                            if (!empty($values['openeve'])) {
                                $close = ($hourNow < date('H:i', strtotime($values['openeve'])))?'close':'open';
                            }
                            if (!empty($values['closeeve'])) { 
                                $close = ($hourNow >= date('H:i', strtotime($values['closeeve'])))?'close':'open';
                            }
                        }
                        $newTimingArr[strtolower($day)]['closing'] = $close;
                    }
                    $allTimingArr[strtolower($keys)]['open'] = (!empty($values['open']))?date('H:i',strtotime($values['open'])):'';
                    $allTimingArr[strtolower($keys)]['close'] = (!empty($values['close']))?date('H:i',strtotime($values['close'])):'';
                    $allTimingArr[strtolower($keys)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                    $allTimingArr[strtolower($keys)]['closing'] = (!empty($values['close']) && !empty($values['open']))?(((date("H:m") < date("H:m",strtotime($values['close']))) && (date("H:m") >= date("H:m",strtotime($values['open']))))?'Open':'Closed'):'Closed';
                }
            }
            $value->timings = $newTimingArr[strtolower($day)];
            $value->allTimings = $allTimingArr;
            $value->image = file_exists(FCPATH.'uploads/'.$value->image) && file_get_contents(FCPATH.'uploads/'.$value->image)?$value->image:'';
            $value->rating = ($value->rating)?number_format((float)$value->rating, 1, '.', ''):null;
    }
    return $result;
    }

    private function group_by_uniq($key, $data) {
        $result = array();
    
        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        $res = array_map(function($values) {
            $uniq = array_unique($values);
            return $uniq[0];
        }, $result);
        return array_values($res);
    }

    //search shop
    public function searchShops($searchItem,$language_slug = "fr", $store_type=NULL)
    {

        $this->db->select("res.content_id,res.entity_id,res.name,res.timings,res.image,res.featured_image,res.language_slug,res.store_type_id,res.status,res.sub_store_type_id,address.address,address.landmark");
        $this->db->join('shop_address as address','res.entity_id = address.shop_entity_id','left');
        $this->db->join('review','res.entity_id = review.shop_id','left');
        if($searchItem){
            $this->db->join('shop_menu_item as menu','res.entity_id = menu.shop_id','left');
            $this->db->join('category','menu.category_id = category.entity_id','left');
            $where = "(menu.name like '%".$searchItem."%' OR res.name like '%".$searchItem."%' OR category.name like '%".$searchItem."%')";
            $this->db->where($where);
        }
        // Filter by store type (service type)
        if($store_type !== 0 ) {
            $this->db->where('res.store_type_id', $store_type);
        }
        $this->db->where('res.language_slug',$language_slug);

        if (!empty($searchItem)) {
            $shopLng = $this->db->get('shop as res')->result_array();
            $shopFiltered = array_filter($shopLng, function($shop) use ($language_slug) {
                return $shop['language_slug'] == $language_slug && $shop['status'] == "1";
            });
            $result = $this->group_by_uniq('content_id', $shopFiltered);
        } else {
            $this->db->group_by('res.content_id');
            $result = $this->db->get_where('shop as res', array('res.status' => 1))->result_array();
        }

        // Another layer of filter
        if($store_type !== 0 ) {
            $temp = array_filter($result, function($res) use ($store_type) {
                return $res['store_type_id'] == $store_type;
            });

            $result = $temp;
        }
        return $result;
    }


    // MENU OPERATION
    public function searchMenuRecords($shop_id, $searchItem){
        $this->db->select('menu.entity_id as menu_id,menu.name,menu.price,menu.menu_detail,menu.image,menu.image_group,menu.is_under_20_kg,availability,c.name as category,c.entity_id as category_id');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->where('menu.status',1); 
        
        if(!empty($searchItem)) {
            $where = "(menu.name like '%".$searchItem."%' OR c.name like '%".$searchItem."%')";
            $this->db->where($where);
        }
        
        $this->db->where('menu.shop_id',$shop_id);
       
        $result = $this->db->get('shop_menu_item as menu')->result();
        
        return $result;
    }
    
    public function getMenuRecords($shop_id, $price, $food = '', $groupByCategory = false){
        $this->db->select('menu.entity_id as menu_id,menu.name,menu.price,menu.menu_detail,menu.image,menu.image_group,menu.is_under_20_kg,availability,c.name as category,c.entity_id as category_id');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->where('menu.shop_id',$shop_id);
        $this->db->where('menu.status',1); 
        if($price == 1){
            $this->db->order_by('menu.price','desc');
        }else{
            $this->db->order_by('menu.price','asc');
        }
        if($food != ''){
            $this->db->where('menu.is_under_20_kg',$food);
        }
        $result = $this->db->get('shop_menu_item as menu')->result();
        if($groupByCategory)
        {
            $menu = array();
            foreach ($result as $key => $value) {
                if (!isset($menu[$value->category_id])) 
                {
                    $menu[$value->category_id] = array();
                    $menu[$value->category_id]['category_id'] = $value->category_id;
                    $menu[$value->category_id]['category_name'] = $value->category;  
                }
                $image = file_exists(FCPATH.'uploads/'.$value->image) && file_get_contents(FCPATH.'uploads/'.$value->image)?$value->image:'';
                $menu[$value->category_id]['items'][]  = array('menu_id'=>$value->menu_id,'name' => $value->name,'price' => $value->price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'is_under_20_kg'=>$value->is_under_20_kg);
            }
            $finalArray = array();
            foreach ($menu as $nm => $va) {
                $finalArray[] = $va;
            }
            return $finalArray;     
        }
        foreach ($result as $key => $value) {
            $value->image = file_exists(FCPATH.'uploads/'.$value->image) && file_get_contents(FCPATH.'uploads/'.$value->image)?$value->image:'';
        }
        return $result;
    }

    // method for adding
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    }

    public function updateData($tablename,$data,$wherefieldname,$wherefieldvalue)
    {        
        $this->db->where($wherefieldname,$wherefieldvalue);
        $this->db->update($tablename,$data);
        return $this->db->affected_rows();
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
                $items[$value->order_id]['track_order_url'] = 'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value->order_id));
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
}
?>    
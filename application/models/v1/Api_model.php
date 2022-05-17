<?php
class Api_model extends CI_Model {
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

    private function group_by_uniq($key, $data) {
        $result = array();
    
        foreach($data as $val) {
            $v = json_decode(json_encode($val), true);
            if(array_key_exists($key, $v)){
                $result[$v[$key]][] = $val;
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

    //get home
    public function getHomeShop($latitude,$longitude,$searchItem,$food,$rating,$distance,$language_slug,$count,$page_no = 1, $store_type=NULL, $store_filter = []){
        
        $this->db->select("res.content_id,res.entity_id,res.name,res.timings,res.image,res.featured_image,res.language_slug,res.store_type_id,res.status,res.sub_store_type_id,address.address,address.landmark,AVG (review.rating) as rating, (6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code");
        $this->db->join('shop_address as address','res.entity_id = address.shop_entity_id','left');
        $this->db->join('review','res.entity_id = review.shop_id','left');
        $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        if($searchItem){
            $this->db->join('shop_menu_item as menu','res.entity_id = menu.shop_id','left');
            $this->db->join('category','menu.category_id = category.entity_id','left');
            $where = "(menu.name like '%".$searchItem."%' OR res.name like '%".$searchItem."%' OR category.name like '%".$searchItem."%')";
            $this->db->where($where);
        }
        if($food != ''){
            $this->db->where('res.is_under_20_kg',$food); 
            $this->db->or_where('res.is_under_20_kg',NULL);     
        }
        if($rating){
            $this->db->having('rating <=',$rating);
        }
        if($distance){
            $this->db->having('distance <=',$distance);
        }else{
            $this->db->having('distance <',NEAR_KM);
        }
        // Filter by store type (service type)
        if($store_type !== 0 ) {
            $this->db->where('res.store_type_id', $store_type);
        }
        $this->db->where('res.language_slug',$language_slug);
        // $this->db->group_by('res.entity_id');
        // $this->db->limit($count,$page_no*$count);

        if (!empty($searchItem)) {
            $shopLng = $this->db->get('shop as res')->result();
            $shopFiltered = array_filter($shopLng, function($shop) use ($language_slug) {
                return $shop->language_slug == $language_slug && $shop->status == "1";
            });
            $result = $this->group_by_uniq('content_id', $shopFiltered);
        } else {
            $this->db->group_by('res.content_id');
            $result = $this->db->get_where('shop as res', array('res.status' => 1))->result();
        }

        // Another layer of filter
        if($store_type !== 0 ) {
            $temp = array_filter($result, function($res) use ($store_type) {
                return $res->store_type_id == $store_type;
            });

            $result = $temp;
        }

        // Apply filter by sub store type
        if(!empty($store_filter)) {
            $filteredSubByStore = array_filter($result, function($shop) use ($store_filter) {
                if(isset($shop)) {
                    $asArray = explode(',', $shop->sub_store_type_id);
                    $exist = array_intersect($store_filter, $asArray);
                    return !empty($exist); 
                } else { return false; }
            });

            $result = $filteredSubByStore;
        }

        foreach ($result as $key => $value) {
            $timing = $value->timings;
            if($timing){
               $timing =  unserialize(html_entity_decode($timing));
               $newTimingArr = array();
                $day = date("l");
                foreach($timing as $keys=>$values) {
                    $day = date("l");
                    if($keys == strtolower($day)){
                        $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?date('g:i A',strtotime($values['open'])):'';
                        $newTimingArr[strtolower($day)]['close'] =(!empty($values['close']))?date('g:i A',strtotime($values['close'])):'';
                        $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                        $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']))?($values['close'] <= date('H:m'))?'close':'open':'close';
                    }
                }
            }
            $value->timings = $newTimingArr[strtolower($day)];
            $value->image = ($value->image)?image_url.$value->image:'';
            $value->rating = ($value->rating)?number_format((float)$value->rating, 1, '.', ''):null;
        }

        if (!empty($count)) {
            $result = array_slice($result, $page_no*$count, $count);
        }

        return $result;
    }
    //get banner
    public function getbanner(){
        $this->db->select('image');
        $images =  $this->db->get('slider_image')->result();
        foreach ($images as $key => $value) {
            $value->image = ($value->image)?image_url.$value->image:'';
        }
        return $images;
    }
    //get home page category
    public function getcategory($language_slug, $store_type_id ) {
        $this->db->select('category.content_id,category.entity_id as category_id, category.name,category.image');
        $this->db->where('category.language_slug',$language_slug);
        $this->db->where('category.store_type_id',$store_type_id);
        $this->db->order_by('category.entity_id','desc');
        //$this->db->limit(4, 0);
        $result =  $this->db->get('category')->result(); 
        foreach ($result as $key => $value) {
            $value->image = ($value->image)?image_url.$value->image:'';
        }
        return $result;
    }
    //get shop
    public function getShopDetail($content_id,$language_slug){
        $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.phone_number,res.timings,res.image,res.allow_24_delivery, res.flat_rate_24,res.featured_image,res.language_slug,res.store_type_id,res.status,res.sub_store_type_id,address.address,address.landmark,AVG(review.rating) as rating,currencies.currency_symbol,currencies.currency_code");
        $this->db->join('shop_address as address','res.entity_id = address.shop_entity_id','left');
        $this->db->join('review','res.entity_id = review.shop_id','left');
        $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        $this->db->where('res.content_id',$content_id);
        $this->db->where('res.language_slug',$language_slug);
        $this->db->group_by('res.entity_id');
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
                    if($keys == strtolower($day)){
                        $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?date('g:i A',strtotime($values['open'])):'';
                        $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?date('g:i A',strtotime($values['close'])):'';
                        $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                        $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']))?($values['close'] <= date('H:m'))?'close':'open':'close';
                    }
                    $allTimingArr[strtolower($keys)]['open'] = (!empty($values['open']))?date('H:i',strtotime($values['open'])):'';
                    $allTimingArr[strtolower($keys)]['close'] = (!empty($values['close']))?date('H:i',strtotime($values['close'])):'';
                    $allTimingArr[strtolower($keys)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                    $allTimingArr[strtolower($keys)]['closing'] = (!empty($values['close']) && !empty($values['open']))?(((date("H:m") < date("H:m",strtotime($values['close']))) && (date("H:m") >= date("H:m",strtotime($values['open']))))?'Open':'Closed'):'Closed';
                }
            }
            $value->timings = $newTimingArr[strtolower($day)];
            $value->image = ($value->image)?image_url.$value->image:'';
            $value->pre_order_timings = $allTimingArr;
            $value->rating = ($value->rating)?number_format((float)$value->rating, 1, '.', ''):null;
        }
        return $result;
    }
    
    public function getShopTimings($shop_id){
        $this->db->select("res.entity_id as restuarant_id,res.timings");
        $this->db->where('res.entity_id',$shop_id);
        $value =  $this->db->get('shop as res')->first_row();
        if($value) {
            $timing = $value->timings;
            if($timing){
               $timing =  unserialize(html_entity_decode($timing));
               $allTimingArr = array();
                foreach($timing as $keys=>$values) {
                    $allTimingArr[strtolower($keys)]['open'] = (!empty($values['open']))?date('H',strtotime($values['open'])):'';
                    $allTimingArr[strtolower($keys)]['close'] = (!empty($values['close']))?date('H',strtotime($values['close'])):'';
                    $allTimingArr[strtolower($keys)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                    $allTimingArr[strtolower($keys)]['closing'] = (!empty($values['close']) && !empty($values['open']))?(((date("H:m") < date("H:m",strtotime($values['close']))) && (date("H:m") >= date("H:m",strtotime($values['open']))))?'Open':'Closed'):'Closed';
                }
            }
            $value->pre_order_timings = $allTimingArr;
        }
        return $value;
    }


    //get populer item
    public function item_image($shop_id,$language_slug){
        $this->db->select('image');
        $this->db->where('popular_item !=',1);
        $this->db->where('image !=','');
        if($shop_id){
            $this->db->where('shop_id',$shop_id);
        }
        $this->db->where('language_slug',$language_slug);
        $this->db->limit(10, 0);
        $result = $this->db->get('shop_menu_item')->result();
        foreach ($result as $key => $value) {
            $value->image = ($value->image)?image_url.$value->image:'';
        }
        return $result;
    }
    //get items
    public function getMenuItem($shop_id,$food,$price,$language_slug,$popular){
        $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
        $couponAmount = $ItemDiscount['couponAmount'];
        $ItemDiscount = (!empty($ItemDiscount['itemDetail']))?array_column($ItemDiscount['itemDetail'], 'item_id'):array();

        $this->db->select('menu.is_deal,menu.entity_id as menu_id,menu.status,menu.name,menu.price,menu.menu_detail,menu.image,menu.image_group,menu.is_under_20_kg,menu.recipe_detail,availability,c.name as category,c.entity_id as category_id,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id,add_ons_master.is_multiple,deal_category.deal_category_name,add_ons_master.deal_category_id');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
        $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
        $this->db->join('deal_category','add_ons_master.deal_category_id = deal_category.deal_category_id','left');
        $this->db->where('menu.shop_id',$shop_id);
        if($popular == 1){
            $this->db->where('popular_item',1);
            $this->db->where('menu.image !=','');
        }else{
            if($price == 1){
                $this->db->order_by('menu.price','desc');
            }else{
                $this->db->order_by('menu.price','asc');
            }
            if($food != ''){
                $this->db->where('menu.is_under_20_kg',$food);
            }
        }
        $this->db->where('menu.language_slug',$language_slug);
        $result = $this->db->get('shop_menu_item as menu')->result();
       
        $menu = array();
        foreach ($result as $key => $value) {
            $offer_price = '';
            if(in_array($value->menu_id,$ItemDiscount)){
                if(!empty($couponAmount)){
                    if($couponAmount[0]['max_amount'] < $value->price){ 
                        if($couponAmount[0]['amount_type'] == 'Percentage'){
                            $offer_price = $value->price - round(($value->price * $couponAmount[0]['amount'])/100);
                        }else if($couponAmount[0]['amount_type'] == 'Amount'){
                            $offer_price = $value->price - $couponAmount[0]['amount'];
                        }
                    }
                }
            }
            $offer_price = ($offer_price)?$offer_price:'';
            if (!isset($menu[$value->category_id])) 
            {
                $menu[$value->category_id] = array();
                $menu[$value->category_id]['category_id'] = $value->category_id;
                $menu[$value->category_id]['category_name'] = $value->category;  
            }
            $image = ($value->image)?image_url.$value->image:'';
            if(empty($value->image_group))
            {
                $image_group = $value->image ? ' '.$value->image: '';
            }
            $total = 0;
            if($value->check_add_ons == 1){
                if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                   $menu[$value->category_id]['items'][$value->menu_id] = array();
                   $menu[$value->category_id]['items'][$value->menu_id] = array('menu_id'=>$value->menu_id,'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'recipe_detail'=>$value->recipe_detail,'availability'=>$value->availability,'is_under_20_kg'=>$value->is_under_20_kg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
                }
                if($value->is_deal == 1){
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id])){
                       $i = 0;
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id] = array();
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['addons_category'] = $value->deal_category_name;
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['addons_category_id'] = $value->deal_category_id;
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['is_multiple'] = $value->is_multiple;
                    }
                    $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name);
                    $i++;
                }else{
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id])){
                       $i = 0;
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id] = array();
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
                    }
                    $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name,'add_ons_price'=>$value->add_ons_price);
                    $i++;
                }
            }else{
                $menu[$value->category_id]['items'][]  = array('menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image, 'menu_images' => !empty($value->image_group) ? $value->image_group : $image_group,'recipe_detail'=>$value->recipe_detail,'availability'=>$value->availability,'is_under_20_kg'=>$value->is_under_20_kg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
            }
        }
        $finalArray = array();
        $final = array();
        $semifinal = array();
        $new = array();
        foreach ($menu as $nm => $va) 
        {
            $final = array();
            foreach ($va['items'] as $kk => $items) 
            {
                if(!empty($items['addons_category_list']))
                {
                    $semifinal = array();
                    foreach ($items['addons_category_list'] as $addons_cat_list) 
                    {
                        array_push($semifinal, $addons_cat_list);
                    }
                    $items['addons_category_list'] = $semifinal;                  
                }
                array_push($final, $items);
            }
            $va['items'] = $final;
            array_push($finalArray, $va);
        }
        return $finalArray;     
    }
    //get resutarant review
    public function getShopReview($shop_id){
        $this->db->select("review.rating,review.review,users.first_name,users.last_name,users.image,review.created_date");
        $this->db->join('users','review.user_id = users.entity_id','left');
        $this->db->where('review.status',1);
        $this->db->where('review.shop_id',$shop_id);
        $result =  $this->db->get('review')->result();
        
        foreach ($result as $key => $value) { 
            $value->last_name = ($value->last_name)?$value->last_name:'';
            $value->first_name = ($value->first_name)?$value->first_name:'';
            $value->image = ($value->image)?image_url.$value->image:'';
            $value->created_date = ($value->created_date)?date("d-m-Y",strtotime($value->created_date)):'';
        }
        return $result;
    }
    //get event restuarant
    public function getEventShop($latitude,$longitude,$searchItem,$language_slug,$count,$page_no = 1,$store_type,$store_filter = []){
        if($searchItem){
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,res.image,res.featured_image,res.language_slug,res.store_type_id,res.status,res.sub_store_type_id,address.address,address.landmark,address.city,address.zipcode,AVG (review.rating) as rating,currencies.currency_symbol,currencies.currency_code");
            $this->db->join('shop_address as address','res.entity_id = address.shop_entity_id','left');
            $this->db->join('review','res.entity_id = review.shop_id','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
            $where = "(res.name like '%".$searchItem."%')";
            $this->db->where($where);

        }else{
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,res.image,res.featured_image,res.language_slug,res.store_type_id,res.status,res.sub_store_type_id,address.address,address.landmark,address.city,address.zipcode,AVG (review.rating) as rating, (6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code");
            $this->db->join('shop_address as address','res.entity_id = address.shop_entity_id','left');
            $this->db->join('review','res.entity_id = review.shop_id','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        }
        $this->db->where('res.language_slug',$language_slug);
        // $this->db->limit($count,$page_no*$count);
        $this->db->group_by('res.content_id');
        //$result =  $this->db->get('shop as res')->result_array();
        $result = $this->db->get_where('shop as res', array('res.status' => 1))->result_array();

        if($store_type !== 0 ) {
            $temp = array_filter($result, function($res) use ($store_type) {
                return $res['store_type_id'] == $store_type;
            });

            $result = $temp;
        }

        // Apply filter by sub store type
        if(!empty($store_filter)) {
            $filteredSubByStore = array_filter($result, function($shop) use ($store_filter) {
                if(isset($shop)) {
                    $asArray = explode(',', $shop['sub_store_type_id']);
                    $exist = array_intersect($store_filter, $asArray);
                    return !empty($exist); 
                } else { return false; }
            });

            $result = $filteredSubByStore;
        }

        foreach ($result as $key => $value) {
            $timing = $value->timings;
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
            }
            $value->timings = $newTimingArr[strtolower($day)];
            $value->image = ($value->image)?image_url.$value->image:'';
            $value->rating = ($value->rating)?number_format((float)$value->rating, 1, '.', ''):null;
        }

        if (!empty($count)) {
            $result = array_slice($result, $page_no*$count, $count);
        }
        return $result;
    }
    // Login
    public function getLogin($phone,$password)
    {        
        $enc_pass  = md5(SALT.$password);
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.mobile_number,users.image,users.notification');
        $this->db->where('mobile_number',$phone);
        $this->db->where('password',$enc_pass);
        $this->db->where('user_type','User');
        return $this->db->get('users')->first_row();
    }
    //get rating of user
    public function getRatings($userid){
        $this->db->select('AVG(review.rating) as rating');
        $this->db->where('order_user_id',$userid);
        $this->db->group_by('review.order_user_id');
        return $this->db->get('review')->first_row();
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
    public function checkEmailExist($emailID,$UserID)
    {
        $this->db->where('Email',$emailID);
        $this->db->where('UserID !=',$UserID);
        $this->db->where('deleteStatus',0);
        return $this->db->get('users')->num_rows();
    }
    // get config
    public function getSystemOptoin($OptionSlug)
    {        
        $this->db->select('OptionValue');                
        $this->db->where('OptionSlug',$OptionSlug);        
        return $this->db->get('system_option')->first_row();
    }
    //get record after registration
    public function getRegisterRecord($tblname,$UserID){
        $this->db->select('entity_id,first_name,mobile_number');
        $this->db->where('entity_id',$UserID);
        return $this->db->get($tblname)->first_row();
    }
    //check email for user edit
    public function getExistingEmail($table,$fieldName,$where,$UserID)
    {
        $this->db->where($fieldName,$where);
        $this->db->where('UserID !=',$UserID);
        return $this->db->get($table)->first_row();
    } 
    //get cms detail 
    public function getCMSRecord($tblname,$cms_slug,$language_slug){
        $this->db->select('content_id,entity_id,name,description');
        $this->db->where('CMSSlug',$cms_slug);
        $this->db->where('status',1);
        $this->db->where('language_slug',$language_slug);
        return $this->db->get($tblname)->result();
    }
    //check booking availability
    public function getBookingAvailability($date,$people,$shop_id){
        return false;
    }
    //get package
    public function getPackage($shop_id,$language_slug){
        $this->db->select('entity_id as package_id,name,price,detail,availability');
        $this->db->where('shop_id',$shop_id);
        $this->db->where('language_slug',$language_slug);
        return $this->db->get('shop_package')->result();
    }
    //get event
    public function getBooking($user_id){
        $currentDateTime = date('Y-m-d H:i:s');
        //upcoming
        $this->db->select('event.entity_id as event_id,event.booking_date,event.no_of_people,event_detail.package_detail,event_detail.shop_detail,AVG (review.rating) as rating,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id','left');
        $this->db->join('review','event.shop_id = review.shop_id','left');
        $this->db->join('shop','event.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('event.user_id',$user_id);
        $this->db->where('event.booking_date >',$currentDateTime);
        $this->db->group_by('event.entity_id');
        $this->db->order_by('event.entity_id','desc');
        $result = $this->db->get('event')->result();
        $upcoming = array();
        foreach ($result as $key => $value) {
            $package_detail = '';
            $shop_detail = '';
            if(!isset($value->event_id)){
                $upcoming[$value->event_id] = array();
            }
            if(isset($value->event_id)){
                $package_detail = unserialize($value->package_detail);
                $shop_detail = unserialize($value->shop_detail);
                $upcoming[$value->event_id]['entity_id'] =  $value->event_id;
                $upcoming[$value->event_id]['booking_date'] =  $value->booking_date;
                $upcoming[$value->event_id]['no_of_people'] =  $value->no_of_people;
                $upcoming[$value->event_id]['currency_code'] =  $value->currency_code;
                $upcoming[$value->event_id]['currency_symbol'] =  $value->currency_symbol;

                $upcoming[$value->event_id]['package_name'] =  (!empty($package_detail))?$package_detail['package_name']:'';
                $upcoming[$value->event_id]['package_detail'] = (!empty($package_detail))?$package_detail['package_detail']:'';
                $upcoming[$value->event_id]['package_price'] = (!empty($package_detail))?$package_detail['package_price']:'';

                $upcoming[$value->event_id]['name'] =  (!empty($shop_detail))?$shop_detail->name:'';
                $upcoming[$value->event_id]['image'] =  (!empty($shop_detail) && $shop_detail->image != '')?image_url.$shop_detail->image:'';
                $upcoming[$value->event_id]['address'] =  (!empty($shop_detail))?$shop_detail->address:'';
                $upcoming[$value->event_id]['landmark'] =  (!empty($shop_detail))?$shop_detail->landmark:'';
                $upcoming[$value->event_id]['city'] =  (!empty($shop_detail))?$shop_detail->city:'';
                $upcoming[$value->event_id]['zipcode'] =  (!empty($shop_detail))?$shop_detail->zipcode:'';
                $upcoming[$value->event_id]['rating'] =  $value->rating;
            }
        }
        $finalArray = array();
        foreach ($upcoming as $key => $val) {
           $finalArray[] = $val; 
        }
        $data['upcoming'] = $finalArray;
        //past
        $this->db->select('event.entity_id as event_id,event.booking_date,event.no_of_people,event_detail.package_detail,event_detail.shop_detail,AVG (review.rating) as rating,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id','left');
        $this->db->join('review','event.shop_id = review.shop_id','left');
        $this->db->join('shop','event.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('event.user_id',$user_id);
        $this->db->where('event.booking_date <',$currentDateTime);
        $this->db->group_by('event.entity_id');
        $this->db->order_by('event.entity_id','desc');
        $resultPast = $this->db->get('event')->result();
        $past = array();
        foreach ($resultPast as $key => $value) {
            if(!isset($value->event_id)){
                $past[$value->event_id] = array();
            }
            if(isset($value->event_id)){
                $package_detail = unserialize($value->package_detail);
                $shop_detail = unserialize($value->shop_detail);
                $past[$value->event_id]['entity_id'] =  $value->event_id;
                $past[$value->event_id]['booking_date'] =  $value->booking_date;
                $past[$value->event_id]['no_of_people'] =  $value->no_of_people;
                $past[$value->event_id]['currency_code'] =  $value->currency_code;
                $past[$value->event_id]['currency_symbol'] =  $value->currency_symbol;

                $past[$value->event_id]['package_name'] =  (!empty($package_detail))?$package_detail['package_name']:'';
                $past[$value->event_id]['package_detail'] = (!empty($package_detail))?$package_detail['package_detail']:'';
                $past[$value->event_id]['package_price'] = (!empty($package_detail))?$package_detail['package_price']:'';

                $past[$value->event_id]['name'] =  (!empty($shop_detail))?$shop_detail->name:'';
                $past[$value->event_id]['image'] =  (!empty($shop_detail) && $shop_detail->image != '')?image_url.$shop_detail->image:'';
                $past[$value->event_id]['address'] =  (!empty($shop_detail))?$shop_detail->address:'';
                $past[$value->event_id]['landmark'] =  (!empty($shop_detail))?$shop_detail->landmark:'';
                $past[$value->event_id]['city'] =  (!empty($shop_detail))?$shop_detail->city:'';
                $past[$value->event_id]['zipcode'] =  (!empty($shop_detail))?$shop_detail->zipcode:'';
                $past[$value->event_id]['rating'] =  $value->rating;
            }
        }
        $final = array();
        foreach ($past as $key => $val) {
           $final[] = $val; 
        }
        $data['past'] = $final;
        return $data;
    } 
    //get recipe
    public function getRecipe($searchItem,$food,$timing,$language_slug)
    {
        $this->db->select('entity_id as item_id,name,image,recipe_detail,menu_detail,recipe_time,is_under_20_kg');
        if($searchItem){
            $this->db->where("name like '%".$searchItem."%'");
        }else if($food == '' && $timing == ''){
            $this->db->where("popular_item",1);
        }
        if($food != ''){
            $this->db->where('is_under_20_kg',$food);
        }
        if($timing){
            $this->db->where('recipe_time <=',$timing);
        }
        $this->db->where('language_slug',$language_slug);
        $result =  $this->db->get('shop_menu_item')->result();
        foreach ($result as $key => $value) {
           $value->image = ($value->image)?image_url.$value->image:'';
        }
        return $result;
    } 
    //check if item exist
    public function checkExist($item_id)
    {
        $this->db->select('price,image,name,is_under_20_kg');
        $this->db->where('entity_id',$item_id);
        return $this->db->get('shop_menu_item')->first_row();
    } 
    //get tax
    public function getShopTax($tblname,$shop_id,$flag){
        if($flag == 'order'){
            $this->db->select('shop.name,shop.image,shop.phone_number,shop.email,shop.amount_type,shop.amount,shop.flat_rate_24,shop_address.address,shop_address.landmark,shop_address.zipcode,shop_address.city,shop_address.latitude,shop_address.longitude,currencies.currency_symbol,currencies.currency_code');
            $this->db->join('shop_address','shop.entity_id = shop_address.shop_entity_id','left');
            $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        }else{
            $this->db->select('shop.name,shop.image,shop_address.address,shop_address.landmark,shop_address.zipcode,shop.flat_rate_24,shop_address.city,shop.amount_type,shop.amount,shop_address.latitude,shop_address.longitude');
            $this->db->join('shop_address','shop.entity_id = shop_address.shop_entity_id','left');
            $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        }
        $this->db->where('shop.entity_id',$shop_id);
        return $this->db->get($tblname)->first_row();
    }
    //get address
    public function getAddress($tblname,$fieldName,$user_id){
        $this->db->select('entity_id as address_id,address,landmark,latitude,longitude,city,zipcode');
        $this->db->where($fieldName,$user_id);
        return $this->db->get($tblname)->result();
    }
    //get order detail
    public function getOrderDetail($flag,$user_id,$count,$page_no = 1){
        $this->db->select('order_master.*,order_detail.*,order_driver_map.driver_id,status.order_status as ostatus,status.time,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,shop_address.latitude as resLat,shop_address.longitude as resLong,shop.timings,shop.allow_24_delivery,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_status as status','order_master.entity_id = status.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('driver_traking_map','order_driver_map.driver_id = driver_traking_map.driver_id','left');
        $this->db->join('shop_address','order_master.shop_id = shop_address.shop_entity_id','left');
        $this->db->join('shop','order_master.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        if($flag == 'process'){
            $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel")');
        } 
        if($flag == 'past'){
            $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        }
        $this->db->where('order_master.user_id',$user_id);
        $this->db->order_by('order_master.entity_id','desc');

        /*if($flag == 'past'){
            $this->db->group_by('order_master.entity_id');
            $this->db->limit($count,$page_no*$count);
        }*/
        
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
                /*$type = ($value->tax_type == 'Percentage')?'%':'';    */            
                $items[$value->order_id]['order_id'] = $value->order_id;
                $items[$value->order_id]['shop_id'] = $value->shop_id;
                $items[$value->order_id]['order_accepted'] = ($value->status == 1)?1:0;
                $items[$value->order_id]['accept_order_time'] = date('g:i A',strtotime($value->accept_order_time));
                $shop_detail = unserialize($value->shop_detail);
                $items[$value->order_id]['shop_name'] = (isset($shop_detail->name))?$shop_detail->name:'';
                $items[$value->order_id]['shop_address'] = (isset($shop_detail->address))?$shop_detail->address:'';
                $items[$value->order_id]['shop_allow_24_delivery'] = $this->common_model->getSingleRow('shop', 'entity_id', $value->shop_id)->allow_24_delivery;

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
                    array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_amount,'label_key'=>"Coupon Amount"),
                    array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }else{
                    $items[$value->order_id]['price'] = array(
                    array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                   /* array('label'=>'Service Fee','value'=>$value->tax_rate.$type),*/
                    array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                    array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_amount,'label_key'=>"Coupon Amount"),
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
                    $customization = array();
                    $count = 0;
                    foreach ($item_detail as $key => $valuee) {
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
                        }
                      
                        $valueee['menu_id'] = $valuee['item_id'];
                        $valueee['name'] = $valuee['item_name'];
                        $valueee['quantity'] = $valuee['qty_no'];
                        $valueee['price'] = ($valuee['rate'])?$valuee['rate']:'';
                        $valueee['is_customize'] = $valuee['is_customize'];
                        $valueee['is_deal'] = $valuee['is_deal'];
                        $valueee['offer_price'] = ($valuee['offer_price'])?$valuee['offer_price']:'';
                        $valueee['itemTotal'] = ($valuee['itemTotal'])?$valuee['itemTotal']:'';
                        
                       
                        if(!empty($customization)){
                            $valueee['addons_category_list'] = $customization;
                        }
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
                $items[$value->order_id]['delivery_flag'] = ($value->order_delivery == 'Delivery')?'delivery':'24H Delivery';
                $items[$value->order_id]['is_pre_order'] = isset($value->pre_order_delivery_date);
                $items[$value->order_id]['pre_order_delivery_date'] = $value->pre_order_delivery_date;
                $items[$value->order_id]['currency_symbol'] = $value->currency_symbol;
                $items[$value->order_id]['currency_code'] = $value->currency_code;
            }
        }
        $finalArray = array();
        foreach ($items as $nm => $va) {
            $finalArray[] = $va;
        }
        if($flag == 'process'){
            $res['in_process'] = $finalArray;
        }
        if($flag == 'past'){
            $res['past'] = $finalArray;
        }
        return $res;
    }
    //check coupon
    public function checkCoupon($coupon){
        $this->db->where('name',$coupon);
        $this->db->where('status',1);
        return $this->db->get('coupon')->first_row();
    }
    //get coupon list
    public function getcouponList($subtotal,$shop_id,$order_delivery){
        $this->db->select('coupon.name,coupon.entity_id as coupon_id,coupon.amount_type,coupon.amount,coupon.description,coupon.coupon_type,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('coupon_shop_map','coupon.entity_id = coupon_shop_map.coupon_id','left');
        $this->db->join('shop','coupon_shop_map.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('max_amount <=',$subtotal);
        $this->db->where('coupon_shop_map.shop_id',$shop_id);
        $this->db->where('DATE(end_date) >',date('Y-m-d H:i:s'));
        $this->db->where('coupon.status',1);
        //$this->db->where('(coupon_type = "discount_on_cart" OR coupon_type = "user_registration")');
        if($order_delivery == 'Delivery'){
            $this->db->where_or('coupon_type',"free_delivery");
        }
        return $this->db->get('coupon')->result();
    }
    //get notification
    public function getNotification($user_id,$count,$page_no = 1){
        $page_no = ($page_no > 0)?$page_no-1:0;
        $this->db->select('notifications.notification_title,notifications.notification_description,notifications_users.notification_id');
        $this->db->join('notifications','notifications_users.notification_id =  notifications.entity_id','left');
        $this->db->limit($count,$page_no*$count);
        $this->db->where('notifications_users.user_id',$user_id);
        $data['result'] =  $this->db->get('notifications_users')->result();

        $this->db->select('notifications.notification_title,notifications.notification_description,notifications_users.notification_id');
        $this->db->join('notifications','notifications_users.notification_id =  notifications.entity_id','left');
        $this->db->where('notifications_users.user_id',$user_id);
        $data['count'] =  $this->db->count_all_results('notifications_users');
        return $data;
    }
    //check delivery is available
    public function checkOrderDelivery($users_latitude,$users_longitude,$user_id,$shop_id,$request,$order_id,$user_km=NULL,$driver_km=NULL){ 
        $this->db->select('users.entity_id');
        $this->db->where('user_type','Driver');
        $driver = $this->db->get('users')->result_array();
        
        $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
        $this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
        $this->db->where('users.status',1);
        $this->db->where('driver_traking_map.created_date = (SELECT
            driver_traking_map.created_date
        FROM
            driver_traking_map
        WHERE
            driver_traking_map.driver_id = users.entity_id
        ORDER BY
            driver_traking_map.created_date desc
        LIMIT 1)');
        if(!empty($driver)){
            $this->db->where_in('driver_id',array_column($driver, 'entity_id'));
        }
        $detail = $this->db->get('driver_traking_map')->result();
        $flag = false;  
        if(!empty($detail)){
            foreach ($detail as $key => $value) {
                $longitude = $value->longitude;
                $latitude = $value->latitude;
                $this->db->select("(6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
                $this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
                $this->db->where('shop.entity_id',$shop_id);
                if (!empty($driver_km)) {
                    $this->db->having('distance <',$driver_km);
                }
                else
                {
                    $this->db->having('distance <',DRIVER_NEAR_KM);
                }
                $result = $this->db->get('shop')->result();
                if($request == 1){
                    if(!empty($result)){
                        if($value->device_id){ 
                            $flag = true;   
                            //get langauge
                            $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
                            $this->lang->load('messages_lang', $languages->language_directory);
                            
                            $array = array(
                                'order_id'=>$order_id,
                                'driver_id'=>$value->driver_id,
                                'date'=>date('Y-m-d H:i:s')
                            );
                            $id = $this->addRecord('order_driver_map',$array);
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
                if($request == ''){
                    if(!empty($result)){
                        if($value->device_id){ 
                            $flag = true;
                        }
                    }
                }
            }
        }
            
        
        if($flag == false && $request == 1){
            return true;
        }
        if($flag == true && $request == ''){
            return true;
        }
    }
    // check shop availability
    public function checkShopAvailability($users_latitude,$users_longitude,$user_id,$shop_id,$request,$order_id,$user_km=NULL,$driver_km=NULL){
        $this->db->select("(6371 * acos ( cos ( radians($users_latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($users_longitude) ) + sin ( radians($users_latitude) ) * sin( radians( address.latitude )))) as distance");
        $this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        $this->db->where('shop.entity_id',$shop_id);
        $user_result = $this->db->get('shop')->result();
        if (!empty($user_result)) {
            if (!empty($user_km)) {
                if ($user_result[0]->distance <= $user_km ) {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                if ($user_result[0]->distance <= USER_NEAR_KM ) {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else {
            return false;
        }
    }
    //get driver location for traking
    public function getdriverTracking($order_id,$user_id){
        $this->db->select('order_driver_map.order_id,order_master.total_rate,order_master.order_status,driver_traking_map.latitude as driverLatitude,driver_traking_map.longitude as driverLongitude,shop_address.latitude as resLat,shop_address.longitude as resLong,user_address.latitude as userLat,user_address.longitude as userLong,user_address.address,user_address.landmark,user_address.zipcode,user_address.state,user_address.city,driver.first_name,driver.last_name,driver.image,driver.mobile_number');
        $this->db->join('order_driver_map','driver_traking_map.driver_id = order_driver_map.driver_id','left');
        $this->db->join('order_master','order_driver_map.order_id = order_master.entity_id','left');
        $this->db->join('shop_address','order_master.shop_id = shop_address.shop_entity_id','left');
        $this->db->join('user_address','order_master.address_id = user_address.entity_id','left');
        $this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        $this->db->order_by('driver_traking_map.traking_id','desc');
        $detail = $this->db->get('driver_traking_map')->first_row();
        if(!empty($detail)){
            $detail->image = ($detail->image )?$detail->image :'';
        }
        return $detail;
    }
    //get addos data
    public function getAddonsPrice($add_ons_id){
        $this->db->where('add_ons_id',$add_ons_id);
        return $this->db->get('add_ons_master')->first_row();
    }
      //get item discount
    public function getItemDiscount($where){
        $this->db->where($where);
        $this->db->where('end_date >',date('Y-m-d H:i:s'));
        $result['couponAmount'] =  $this->db->get('coupon')->result_array();
        if(!empty($result['couponAmount'])){
            $res = array_column($result['couponAmount'], 'entity_id');
            $this->db->where_in('coupon_id',$res);
            $result['itemDetail'] = $this->db->get('coupon_item_map')->result_array();
        }
        return $result;
    }
    //get order count of user
    public function checkUserCountCoupon($UserID)
    {
        $this->db->where('user_id',$UserID);
        return $this->db->get('order_master')->num_rows();
    }
    //get delivery charfes by lat long
    public function checkGeoFence($tblname,$fldname,$id)
    {
        $this->db->where($fldname,$id);
        return $this->db->get($tblname)->result();
    }
    // get shop currency
    public function getShopCurrency($shop_id)
    {
        $this->db->select('currencies.currency_code,currencies.currency_symbol');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('shop.entity_id',$shop_id);
        return $this->db->get('shop')->result();
    }
    // method to get details by id
    public function getEditDetail($entity_id)
    {
        $this->db->select('order.*,res.name, address.address,address.landmark,address.city,address.zipcode,u.first_name,u.last_name,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode');
        $this->db->join('shop as res','order.shop_id = res.entity_id','left');
        $this->db->join('shop_address as address','res.entity_id = address.shop_entity_id','left');
        $this->db->join('users as u','order.user_id = u.entity_id','left');
        $this->db->join('user_address as uaddress','u.entity_id = uaddress.user_entity_id','left');
        return  $this->db->get_where('order_master as order',array('order.entity_id'=>$entity_id))->first_row();
    }
    //get invoice data
    public function getInvoiceMenuItem($entity_id){
        $this->db->where('order_id',$entity_id);
        return $this->db->get('order_detail')->first_row();
    }
}
?>
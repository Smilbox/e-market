<?php
class Shop_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // get shop details
    public function getShopDetail($content_id,$searchArray=NULL,$food=NULL,$price=NULL){ 
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
    	$this->db->select("shop.entity_id as shop_id,shop.name,shop.store_type_id, shop.object_fit, shop.allow_24_delivery, shop.flat_rate_24, address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.featured_image,shop.timings,shop.phone_number,shop.shop_slug,shop.content_id,currencies.currency_symbol,currencies.currency_code");
    	$this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('shop.language_slug',$language_slug);
        $this->db->where('shop.content_id',$content_id);
        $this->db->group_by('shop.entity_id');
    	$result['shop'] = $this->db->get_where('shop',array('status'=>1))->result_array();
    	if (!empty($result['shop'])) {
	    	foreach ($result['shop'] as $key => $value) {
	            $timing = $value['timings'];
	            if($timing){
	               $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                   $allTimingArr = array();
	                $day = date("l");
	                foreach($timing as $keys=>$values) {
	                    $day = date("l");
	                    if($keys == strtolower($day)){
	                        $newTimingArr[strtolower($day)]['open'] = !empty($values['open']) ? date('g:i A',strtotime($values['open'])) : '';
	                        $newTimingArr[strtolower($day)]['close'] = !empty($values['close']) ? date('g:i A',strtotime($values['close'])) : '';
	                        $newTimingArr[strtolower($day)]['off'] = !empty($values['open']) && !empty($values['close']) ? 'open' : 'close';
	                        $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']) && !empty($values['open']))?(((date("H:i") < date("H:i",strtotime($values['close']))) && (date("H:i") >= date("H:i",strtotime($values['open']))))?'Open':'Closed'):'Closed';
	                        // $newTimingArr[strtolower($day)]['closing'] = 'Closed';
                        }
                        $allTimingArr[strtolower($keys)]['open'] = (!empty($values['open']))?date('g:i A',strtotime($values['open'])):'';
	                    $allTimingArr[strtolower($keys)]['close'] = (!empty($values['close']))?date('g:i A',strtotime($values['close'])):'';
	                    $allTimingArr[strtolower($keys)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
	                    $allTimingArr[strtolower($keys)]['closing'] = (!empty($values['close']) && !empty($values['open']))?(((date("H:i") < date("H:i",strtotime($values['close']))) && (date("H:i") >= date("H:i",strtotime($values['open']))))?'Open':'Closed'):'Closed';
	                }
	            }
	            $result['shop'][$key]['timings'] = $newTimingArr[strtolower($day)];
	            $result['shop'][$key]['allTimings'] = $allTimingArr;
	            $result['shop'][$key]['image'] = ($value['image'])?image_url.$value['image']:'';
	            $result['shop'][$key]['featured_image'] = ($value['featured_image'])?image_url.$value['featured_image']:'';
	        }
    	} 
        $result['menu_items'] = array();
        $result['packages'] = array();
        $result['categories'] = array();
        if (!empty($result['shop'])) {
            $shop_id = $result['shop'][0]['shop_id'];
            $this->db->select('shop_menu_item.*');
            $this->db->where('shop_menu_item.shop_id',$shop_id);
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach($searchArray as $key => $value) {
                    $like_statementsOne[] = "shop_menu_item.name LIKE '%" . $value . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    $like_statementsTwo[] = "shop_menu_item.menu_detail LIKE '%" . $value . "%'";
                    $like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    $like_statementsThree[] = "shop_menu_item.availability LIKE '%" . $value . "%'";
                    $like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('('.$like_stringOne.' OR '.$like_stringTwo.' OR '.$like_stringThree.')');
            }
            if ($price == "low") {
                $this->db->order_by('shop_menu_item.price','asc');
            }
            else
            {
                $this->db->order_by('shop_menu_item.price','desc');
            }
            if ($food == "non_veg") {
                $this->db->where('shop_menu_item.is_under_20_kg',0);
            }
            else if ($food == "veg") {
                $this->db->where('shop_menu_item.is_under_20_kg',1);
            }
            $result['menu_items'] = $this->db->get_where('shop_menu_item',array('status'=>1))->result_array();
            if (!empty($result['menu_items'])) {
                foreach ($result['menu_items'] as $key => $value) {
                    $result['menu_items'][$key]['image'] = ($value['image'])?image_url.$value['image']:'';
                }
            }

            $this->db->select('shop_package.*');
            $this->db->where('shop_package.shop_id',$shop_id);
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach($searchArray as $key => $value) {
                    $like_statementsOne[] = "shop_package.name LIKE '%" . $value . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    $like_statementsTwo[] = "shop_package.detail LIKE '%" . $value . "%'";
                    $like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    $like_statementsThree[] = "shop_package.availability LIKE '%" . $value . "%'";
                    $like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('('.$like_stringOne.' OR '.$like_stringTwo.' OR '.$like_stringThree.')');
            }
            $result['packages'] = $this->db->get_where('shop_package',array('status'=>1))->result_array();
            if (!empty($result['packages'])) {
                foreach ($result['packages'] as $key => $value) {
                    $result['packages'][$key]['image'] = ($value['image'])?image_url.$value['image']:'';
                }
            }
            $this->db->select('shop_menu_item.category_id,category.name');
            $this->db->join('category','shop_menu_item.category_id = category.entity_id','left');
            $this->db->where('shop_menu_item.shop_id',$shop_id);
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach($searchArray as $key => $value) {
                    $like_statementsOne[] = "shop_menu_item.name LIKE '%" . $value . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    $like_statementsTwo[] = "shop_menu_item.menu_detail LIKE '%" . $value . "%'";
                    $like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    $like_statementsThree[] = "shop_menu_item.availability LIKE '%" . $value . "%'";
                    $like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('('.$like_stringOne.' OR '.$like_stringTwo.' OR '.$like_stringThree.')');
            }
            if ($price == "low") {
                $this->db->order_by('shop_menu_item.price','asc');
            }
            else
            {
                $this->db->order_by('shop_menu_item.price','desc');
            }
            if ($food == "non_veg") {
                $this->db->where('shop_menu_item.is_under_20_kg',0);
            }
            else if ($food == "veg") {
                $this->db->where('shop_menu_item.is_under_20_kg',1);
            }
            $this->db->group_by('shop_menu_item.category_id');
            $result['categories'] = $this->db->get_where('shop_menu_item',array('shop_menu_item.status'=>1))->result_array();
            if (!empty($result['categories'])) {
                foreach ($result['categories'] as $key => $value) {
                    $this->db->select('shop_menu_item.*');
                    $this->db->where('shop_menu_item.shop_id',$shop_id);
                    $this->db->where('shop_menu_item.category_id',$value['category_id']);
                    if ($price == "low") {
                        $this->db->order_by('shop_menu_item.price','asc');
                    }
                    else
                    {
                        $this->db->order_by('shop_menu_item.price','desc');
                    }
                    if ($food == "non_veg") {
                        $this->db->where('shop_menu_item.is_under_20_kg',0);
                    }
                    else if ($food == "veg") {
                        $this->db->where('shop_menu_item.is_under_20_kg',1);
                    }
                    $result[$value['name']] = $this->db->get_where('shop_menu_item',array('status'=>1))->result_array();
                    if (!empty($result[$value['name']])) {
                        foreach ($result[$value['name']] as $key => $mvalue) {
                            $result[$value['name']][$key]['image'] = ($mvalue['image'])?image_url.$mvalue['image']:'';
                            if(empty($mvalue['image_group']))
                            {
                                $result[$value['name']][$key]['image_group'] = ($mvalue['image'])?' '.$mvalue['image']:'';
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
    // get shop reviews
    public function getShopReview($shop_id){
        $this->db->select("review.shop_id,review.rating,review.review,users.first_name,users.last_name,users.image");
        $this->db->join('users','review.user_id = users.entity_id','left');
        $this->db->where('review.status',1);
        $this->db->where('review.shop_id',$shop_id);
        $result =  $this->db->get('review')->result();
        $avg_rating = 0;
        if (!empty($result)) {
            $rating = array_column($result, 'rating');
            $a = array_filter($rating);
            if(count($a)) {
                $average = array_sum($a)/count($a);
            }
            $avg_rating = number_format($average,1);
        }
        return $avg_rating;
    }
    // get shop id
    public function getShopID($shop_slug){
        $this->db->select('entity_id');
        return $this->db->get_where('shop',array('shop_slug'=>$shop_slug))->first_row();
    }
    // get content id from slug
    public function getContentID($shop_slug){
        $this->db->select('content_id');
        return $this->db->get_where('shop',array('shop_slug'=>$shop_slug))->first_row();
    }
    // get content id from shop id
    public function getRestContentID($shop_id){
        $this->db->select('content_id');
        return $this->db->get_where('shop',array('entity_id'=>$shop_id))->first_row();
    }
    // get All Shops
    public function getAllShops($limit,$offset,$search_item=NULL)
    {
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.featured_image,shop.timings,shop.shop_slug");
        $this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        $this->db->join('shop_menu_item','shop.entity_id = shop_menu_item.shop_id AND shop_menu_item.language_slug = "'.$language_slug.'"','left');
        $this->db->where('shop.language_slug',$language_slug);
        $this->db->group_by('shop.content_id');
        if (!empty($search_item)) {
            $this->db->where("shop.name LIKE '%".$search_item."%' OR shop_menu_item.name LIKE '%".$search_item."%'");
        }
        $this->db->limit($limit,$offset);
        $result['data'] = $this->db->get_where('shop',array('shop.status'=>1))->result_array();

        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $value) {
                $timing = $value['timings'];
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
                            $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']) && !empty($values['open']))?(((date("H:m") < date("H:m",strtotime($values['close']))) && (date("H:m") >= date("H:m",strtotime($values['open']))))?'Open':'Closed'):'Closed';
                        }
                    }
                }
                $result['data'][$key]['timings'] = $newTimingArr[strtolower($day)];
                $result['data'][$key]['image'] = ($value['image'])?image_url.$value['image']:'';
                $result['data'][$key]['featured_image'] = ($value['featured_image'])?image_url.$value['featured_image']:'';
            }
        } 
        // total count
        $this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.featured_image,shop.timings,shop.shop_slug");
        $this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        $this->db->join('shop_menu_item','shop.entity_id = shop_menu_item.shop_id AND shop_menu_item.language_slug = "'.$language_slug.'"','left');
        $this->db->where('shop.language_slug',$language_slug);
        $this->db->group_by('shop.content_id');
        if (!empty($search_item)) {
            $this->db->where("shop.name LIKE '%".$search_item."%' OR shop_menu_item.name LIKE '%".$search_item."%'");
        }
        $result['count'] =  $this->db->get_where('shop',array('shop.status'=>1))->num_rows();
        return $result;
    }
    //get ratings and reviews of a shop
    public function getReviewsRatings($shop_id){
        $this->db->select('review.*,users.first_name,users.last_name,users.image');
        $this->db->join('users','review.user_id = users.entity_id','left');
        $this->db->where('review.shop_id',$shop_id);
        return $this->db->get_where('review',array('review.status'=>1))->result_array();
    }
    //check booking availability
    public function getBookingAvailability($date,$people,$shop_id){
        return false;
    }
    //get tax
    public function getShopTax($tblname,$shop_id,$flag){
        if($flag == 'order'){
            $this->db->select('shop.name,shop.image,shop.phone_number,shop.email,shop.amount_type,shop.amount,shop_address.address,shop_address.landmark,shop_address.zipcode,shop_address.city,shop_address.latitude,shop_address.longitude,currencies.currency_symbol,currencies.currency_code');
            $this->db->join('shop_address','shop.entity_id = shop_address.shop_entity_id','left');
            $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        }else{
            $this->db->select('shop.name,shop.image,shop_address.address,shop_address.landmark,shop_address.zipcode,shop_address.city,shop.amount_type,shop.amount,shop_address.latitude,shop_address.longitude');
            $this->db->join('shop_address','shop.entity_id = shop_address.shop_entity_id','left');
            $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        }
        $this->db->where('shop.entity_id',$shop_id);
        return $this->db->get($tblname)->first_row();
    }
    // get number of shop reviews
    public function getReviewsNumber($shop_id,$rating){
        $this->db->where('shop_id',$shop_id);
        $ratingPlus = $rating + 1;
        $this->db->where('(rating >= '.$rating.' AND rating < '.$ratingPlus.')');
        $this->db->group_by('entity_id');
        return $this->db->get('review')->num_rows();
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

    // get shops with pagination
    public function getShopsForOrder($limit,$offset,$resdish=NULL,$latitude=NULL,$longitude=NULL,$minimum_range=NULL,$maximum_range=NULL,$food_veg=NULL,$food_non_veg=NULL,$pagination=NULL, $store_type=NULL, $store_filter = []){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.featured_image,shop.timings,shop.shop_slug,shop.status,shop.object_fit,shop.content_id,shop.language_slug,shop.store_type_id,shop.sub_store_type_id");
        $this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        $this->db->join('shop_menu_item','shop.entity_id = shop_menu_item.shop_id AND shop_menu_item.status = 1','left');
        if (!empty($resdish)) {
            $this->db->where("shop_menu_item.name LIKE '%".$resdish."%' OR shop.name LIKE '%".$resdish."%' OR address.address LIKE '%".$resdish."%' OR address.landmark LIKE '%".$resdish."%'");
        }
        if ($food_veg == 1 && $food_non_veg == 0) {
            $this->db->where('shop_menu_item.is_under_20_kg',1);
        }
        if ($food_veg == 0 && $food_non_veg == 1) {
            $this->db->where('shop_menu_item.is_under_20_kg',0);
        }
        if($store_type !== 0 ) {
            $this->db->where('shop.store_type_id', $store_type);
        }
        /*if(!empty($store_filter)) {
            $store_filter_text = implode(',', $store_filter);
            $this->db->where("shop.sub_store_type_id LIKE '%".$store_filter_text."%'");
        }*/
        // $this->db->where('shop.status',1);

        $this->db->group_by('shop.content_id');
        // $this->db->group_by('shop.name');
        $result = $this->db->get_where('shop', array('shop.status' => 1))->result_array();

        // Another layer of filter
        if($store_type !== 0 ) {
            $temp = array_filter($result, function($res) use ($store_type) {
                return $res['store_type_id'] == $store_type;
            });

            $result = $temp;
        }

        $finalData = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $timing = $value['timings'];
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
                            $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']) && !empty($values['open']))?(((date("H:m") < date("H:m",strtotime($values['close']))) && (date("H:m") >= date("H:m",strtotime($values['open']))))?'Open':'Closed'):'Closed';
                        }
                    }
                }
                $result[$key]['timings'] = $newTimingArr[strtolower($day)];
                $result[$key]['image'] = ($value['image'])?image_url.$value['image']:'';
                $shopLng[$key]['featured_image'] = ($value['featured_image'])?image_url.$value['featured_image']:'';
            }
            $content_id = array();
            $RestDataArr = array();
            foreach ($result as $key => $value) { 
                $content_id[] = $value['content_id'];
                $RestDataArr[$value['content_id']] = array(
                    'content_id' =>$value['content_id'],
                    'shop_slug' =>$value['shop_slug'],
                    'shop_id'=>$value['shop_id']
                );
            }    
            if(!empty($content_id)){
                $this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.featured_image,shop.timings,shop.status,shop.object_fit,shop.shop_slug,shop.content_id,shop.language_slug, shop.store_type_id, shop.sub_store_type_id");
                $this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
                $this->db->join('shop_menu_item','shop.entity_id = shop_menu_item.shop_id AND shop_menu_item.status = 1','left');
                $this->db->where_in('shop.content_id',$content_id);
                $this->db->where('shop.language_slug',$language_slug);
                if($store_type !== 0 ) {
                    $this->db->where('shop.store_type_id', $store_type);
                }
                if (!empty($resdish)) {
                    $this->db->where("shop_menu_item.name LIKE '%".$resdish."%' OR shop.name LIKE '%".$resdish."%' OR address.address LIKE '%".$resdish."%' OR address.landmark LIKE '%".$resdish."%'");
                }
                if ($food_veg == 1 && $food_non_veg == 0) {
                    $this->db->where('shop_menu_item.is_under_20_kg',1);
                }
                if ($food_veg == 0 && $food_non_veg == 1) {
                    $this->db->where('shop_menu_item.is_under_20_kg',0);
                }
                /*if(!empty($store_filter)) {
                    $store_filter_text = implode(',', $store_filter);
                    $this->db->where("shop.sub_store_type_id LIKE '%".$store_filter_text."%'");
                }*/
                $this->db->where('shop.status',1);
                
                $this->db->order_by('shop.entity_id');
                
                if (!empty($resdish)) {
                    $shopLng = $this->db->get('shop')->result_array();
                    $shopFiltered = array_filter($shopLng, function($shop) use ($language_slug) {
                        return $shop['language_slug'] == $language_slug && $shop['status'] == "1";
                    });
                    $shopLng = $this->group_by_uniq('content_id', $shopFiltered);
                } else {
                    $this->db->group_by('shop.content_id');
                    // $shopLng = $this->db->get('shop')->result_array();
                    $shopLng = $this->db->get_where('shop', array('shop.status' => 1))->result_array();
                }

                // Another layer of filter
                if($store_type !== 0 ) {
                    $shopLng = array_filter($shopLng, function($res) use ($store_type) {
                        return $res['store_type_id'] == $store_type;
                    });
                }
                
                if(!empty($store_filter)) {
                    // $store_filter_text = implode(',', $store_filter);
                    $filteredSubByStore = array_filter($shopLng, function($shop) use ($store_filter) {
                        if(isset($shop)) {
                            $asArray = explode(',', $shop['sub_store_type_id']);
                            $exist = array_intersect($store_filter, $asArray);
                            return !empty($exist); 
                        } else { return false; }
                    });

                    $shopLng = $filteredSubByStore;
                }
                foreach ($shopLng as $key => $value) {
                    $timing = $value['timings'];
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
                                $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']) && !empty($values['open']))?(((date("H:m") < date("H:m",strtotime($values['close']))) && (date("H:m") >= date("H:m",strtotime($values['open']))))?'Open':'Closed'):'Closed';
                            }
                        }
                    }
                    $shopLng[$key]['timings'] = $newTimingArr[strtolower($day)];
                    $shopLng[$key]['image'] = ($value['image'])?image_url.$value['image']:'';
                    $shopLng[$key]['featured_image'] = ($value['featured_image'])?image_url.$value['featured_image']:'';
                }                
                foreach ($shopLng as $key => $value) {
                    $finalData[$value['content_id']] = array(
                        'MainShopID'=> $value['shop_id'],
                        'name'=> $value['name'],
                        'address'=> $value['address'],
                        'object_fit'=>$value['object_fit'],
                        'landmark'=> $value['landmark'],
                        'latitude'=> $value['latitude'],
                        'longitude'=> $value['longitude'],
                        'image'=> $value['image'],                    
                        'featured_image'=> $value['featured_image'],                    
                        'timings'=> $value['timings'],                
                        'language_slug'=> $value['language_slug'],
                        'content_id' =>$RestDataArr[$value['content_id']]['content_id'],
                        'shop_slug' =>$RestDataArr[$value['content_id']]['shop_slug'],
                        'shop_id'=>$RestDataArr[$value['content_id']]['shop_id'],
                    );
                }
            }
        } 
        $finalArray = array();
        if (!empty($finalData) && !empty($latitude) && !empty($longitude)) { 
            foreach ($finalData as $key => $value) {
                $latitude1 = $latitude;
                $longitude1 = $longitude;
                $latitude2 = $value['latitude'];
                $longitude2 = $value['longitude'];
                $earth_radius = 6371;
                $dLat = deg2rad($latitude2 - $latitude1);  
                $dLon = deg2rad($longitude2 - $longitude1);  

                $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
                $c = 2 * asin(sqrt($a));  
                $d = intval($earth_radius * $c);
                $finalData[$key]['distance'] = $d;
                if (isset($minimum_range) && isset($maximum_range)) { 
                    if ($minimum_range <= $d && $d <= $maximum_range) {
                        $finalArray[] = $value;
                    }
                    else
                    { 
                        unset($finalData[$key]);
                    }
                }
                else 
                {
                    if (MINIMUM_RANGE <= $d && $d <= MAXIMUM_RANGE) { 
                        $finalArray[] = $value;
                    }
                    else
                    {
                        unset($finalData[$key]);
                    }
                }
            }
        }
        $finalShops = $finalData;
        if (!empty($pagination)) {
            $finalShops = array_slice($finalData, $offset, $limit);
        }
        return $finalShops;
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
    //get menu items
    public function getMenuItem($entity_id,$shop_id){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
        $couponAmount = $ItemDiscount['couponAmount'];
        $ItemDiscount = (!empty($ItemDiscount['itemDetail']))?array_column($ItemDiscount['itemDetail'], 'item_id'):array();

        $this->db->select('menu.shop_id,menu.is_deal,menu.entity_id as menu_id,menu.status,menu.name,menu.price,menu.menu_detail,menu.image,menu.is_under_20_kg,menu.recipe_detail,availability,c.name as category,c.entity_id as category_id,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id,add_ons_master.is_multiple,deal_category.deal_category_name,add_ons_master.deal_category_id');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
        $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
        $this->db->join('deal_category','add_ons_master.deal_category_id = deal_category.deal_category_id','left');
        $this->db->where('menu.shop_id',$shop_id);
        $this->db->where('menu.language_slug',$language_slug);
        $this->db->where('menu.entity_id',$entity_id);
        $result = $this->db->get('shop_menu_item as menu')->result();

        $menu = array();
        if (!empty($result)) {
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
                $total = 0;
                if($value->check_add_ons == 1){
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                       $menu[$value->category_id]['items'][$value->menu_id] = array();
                       $menu[$value->category_id]['items'][$value->menu_id] = array('shop_id'=>$value->shop_id,'menu_id'=>$value->menu_id,'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'recipe_detail'=>$value->recipe_detail,'availability'=>$value->availability,'is_under_20_kg'=>$value->is_under_20_kg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
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
                    $menu[$value->category_id]['items'][]  = array('shop_id'=>$value->shop_id,'menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'recipe_detail'=>$value->recipe_detail,'availability'=>$value->availability,'is_under_20_kg'=>$value->is_under_20_kg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
                }
            }
        }
        $finalArray = array();
        $final = array();
        $semifinal = array();
        $new = array();
        if (!empty($menu)) {
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
        }
        return $finalArray;     
    }
    // get total orders of a user
    public function getTotalOrders($user_id,$shop_id){
        $this->db->select('entity_id');
        $this->db->where('user_id',$user_id);
        $this->db->where('shop_id',$shop_id);
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "complete")');
        return $this->db->get('order_master')->num_rows();
    }
    // get total reviews of a user
    public function getTotalReviews($user_id,$shop_id){
        $this->db->select('entity_id');
        $this->db->where('user_id',$user_id);
        $this->db->where('shop_id',$shop_id);
        return $this->db->get('review')->num_rows();
    }
}
<?php
class Home_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    //server side check email exist
    public function checkEmail($Email)
    {
        $this->db->where('email',$Email);
        return $this->db->get('users')->num_rows();   
    }
    //server side check email exist
    public function checkPhone($Phone)
    {
        $this->db->where('mobile_number',$Phone);
        return $this->db->get('users')->num_rows();  
    }
    // validation for mobile number 
    public function mobileCheck($mobile_number){
        return $this->db->get_where('users',array('mobile_number'=>$mobile_number))->num_rows();
    }
    // get shop details
    public function getShops($storeTypeId = null){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
    	$this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.object_fit,shop.timings,shop.shop_slug,shop.content_id,shop.language_slug,shop.featured_image, shop.store_type_id");
    	$this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        $this->db->group_by('shop.content_id');
        $shopWhere = [
            "status" => 1,
        ];
        !is_null($storeTypeId) && $shopWhere["store_type_id"] = $storeTypeId;
    	$result = $this->db->get_where('shop', $shopWhere)->result_array();
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
                    $result[$key]['featured_image'] = ($value['featured_image'])?image_url.$value['featured_image']:'';
                    $result[$key]['store_type_id'] = $value['store_type_id'];
	        }
            $content_id = array();
            $RestDataArr = array();
            foreach ($result as $key => $value) { 
                $content_id[] = $value['content_id'];
                $RestDataArr[$value['content_id']] = array(
                    'content_id' =>$value['content_id'],
                    'shop_slug' =>$value['shop_slug'],
                    'shop_id'=>$value['shop_id'],
                    'store_type_id' => $value['store_type_id'],
                );
            }    
            if(!empty($content_id)){
                $this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.timings, shop.object_fit, shop.shop_slug,shop.content_id,shop.language_slug,shop.featured_image");
                $this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
                $this->db->where_in('shop.content_id',$content_id);
                $this->db->where('shop.language_slug',$language_slug);
                
                $this->db->where('shop.status',1);
                $this->db->group_by('shop.content_id');
                $this->db->order_by('shop.entity_id');
                $shopLng = $this->db->get('shop')->result_array();

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
                        'featured_image' => $value['featured_image'],
                        'timings'=> $value['timings'],                
                        'language_slug'=> $value['language_slug'],
                        'content_id' =>$RestDataArr[$value['content_id']]['content_id'],
                        'shop_slug' =>$RestDataArr[$value['content_id']]['shop_slug'],
                        'shop_id'=>$RestDataArr[$value['content_id']]['shop_id'],
                        'store_type_id' => $RestDataArr[$value['content_id']]['store_type_id'],
                    );
                }
            }
        } 
        return $finalData;
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
    // get all categories
    public function getAllCategories(){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $this->db->where('language_slug',$language_slug);
        $this->db->group_by('content_id');
    	return $this->db->get_where('category',array('status'=>1))->result();
    }
    // search shop details
    public function searchShops($category_id){
    	$this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.timings,shop.shop_slug,shop.featured_image");
    	$this->db->join('shop','shop_menu_item.shop_id = shop.entity_id','left');
    	$this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        $this->db->where('shop_menu_item.category_id',$category_id);
        $this->db->where('shop_menu_item.status',1);
        $this->db->group_by('shop.entity_id');
    	$result = $this->db->get('shop_menu_item')->result_array();
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
                    $result[$key]['featured_image'] = ($value['featured_image'])?image_url.$value['featured_image']:'';
	        }
    	} 
        return $result;
    }
    // get coupons
    public function getAllCoupons(){
    	$this->db->order_by('updated_date','desc');
    	$this->db->limit(6);
    	return $this->db->get_where('coupon',array('status'=>1))->result();
    }
}
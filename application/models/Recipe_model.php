<?php
class Recipe_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // get all recipies
    public function getAllRecipies($limit,$offset,$recipe=NULL){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
    	$this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.timings,shop.phone_number,shop.shop_slug,shop_menu_item.*");
        $this->db->join('shop','shop_menu_item.shop_id = shop.entity_id','left');
    	$this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        if (!empty($recipe)) {
            $this->db->where("shop_menu_item.name LIKE '%".$recipe."%'");
        }
        $this->db->where('shop_menu_item.language_slug',$language_slug);
        $this->db->group_by('shop_menu_item.content_id');
        $this->db->limit($limit,$offset);
    	$result['data'] = $this->db->get_where('shop_menu_item',array('shop_menu_item.status'=>1))->result_array();
        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $value) {
                $result['data'][$key]['image'] = ($value['image'])?image_url.$value['image']:'';
            }
        } 
        // total count
        $this->db->select("shop.entity_id as shop_id,shop.name,address.address,address.landmark,address.latitude,address.longitude,shop.image,shop.timings,shop.phone_number,shop.shop_slug,shop_menu_item.*");
        $this->db->join('shop','shop_menu_item.shop_id = shop.entity_id','left');
        $this->db->join('shop_address as address','shop.entity_id = address.shop_entity_id','left');
        if (!empty($recipe)) {
            $this->db->where("shop_menu_item.name LIKE '%".$recipe."%'");
        }
        $this->db->where('shop_menu_item.language_slug',$language_slug);
        $this->db->group_by('shop_menu_item.content_id');
        $result['count'] =  $this->db->get_where('shop_menu_item',array('shop_menu_item.status'=>1))->num_rows();
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
    // get shop menu details
    public function getMenuItemDetail($content_id){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $this->db->select("shop.image as shop_image,shop_menu_item.*");
        $this->db->join('shop','shop_menu_item.shop_id = shop.entity_id','left');
        $this->db->where('shop_menu_item.content_id',$content_id);
        $this->db->where('shop_menu_item.language_slug',$language_slug);
        $result = $this->db->get('shop_menu_item')->result_array();
        if (!empty($result)) {
            $result[0]['image'] = ($result[0]['image'])?image_url.$result[0]['image']:'';
            $result[0]['shop_image'] = ($result[0]['shop_image'])?image_url.$result[0]['shop_image']:'';
        } 
        return $result;
    }
    // get menu id
    public function getMenuItemID($item_slug){
        $this->db->select('entity_id');
        return $this->db->get_where('shop_menu_item',array('item_slug'=>$item_slug))->first_row();
    }
    // get content id
    public function getContentID($item_slug){
        $this->db->select('content_id');
        return $this->db->get_where('shop_menu_item',array('item_slug'=>$item_slug))->first_row();
    }
    
}
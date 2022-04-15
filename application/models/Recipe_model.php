<?php
class Recipe_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // get all recipies
    public function getAllRecipies($limit,$offset,$recipe=NULL){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
    	$this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.phone_number,restaurant.restaurant_slug,restaurant_menu_item.*");
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id','left');
    	$this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        if (!empty($recipe)) {
            $this->db->where("restaurant_menu_item.name LIKE '%".$recipe."%'");
        }
        $this->db->where('restaurant_menu_item.language_slug',$language_slug);
        $this->db->group_by('restaurant_menu_item.content_id');
        $this->db->limit($limit,$offset);
    	$result['data'] = $this->db->get_where('restaurant_menu_item',array('restaurant_menu_item.status'=>1))->result_array();
        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $value) {
                $result['data'][$key]['image'] = ($value['image'])?image_url.$value['image']:'';
            }
        } 
        // total count
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.phone_number,restaurant.restaurant_slug,restaurant_menu_item.*");
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id','left');
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        if (!empty($recipe)) {
            $this->db->where("restaurant_menu_item.name LIKE '%".$recipe."%'");
        }
        $this->db->where('restaurant_menu_item.language_slug',$language_slug);
        $this->db->group_by('restaurant_menu_item.content_id');
        $result['count'] =  $this->db->get_where('restaurant_menu_item',array('restaurant_menu_item.status'=>1))->num_rows();
        return $result;
    }
    // get restaurant reviews
    public function getRestaurantReview($restaurant_id){
        $this->db->select("review.restaurant_id,review.rating,review.review,users.first_name,users.last_name,users.image");
        $this->db->join('users','review.user_id = users.entity_id','left');
        $this->db->where('review.status',1);
        $this->db->where('review.restaurant_id',$restaurant_id);
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
    // get restaurant menu details
    public function getMenuItemDetail($content_id){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $this->db->select("restaurant.image as restaurant_image,restaurant_menu_item.*");
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id','left');
        $this->db->where('restaurant_menu_item.content_id',$content_id);
        $this->db->where('restaurant_menu_item.language_slug',$language_slug);
        $result = $this->db->get('restaurant_menu_item')->result_array();
        if (!empty($result)) {
            $result[0]['image'] = ($result[0]['image'])?image_url.$result[0]['image']:'';
            $result[0]['restaurant_image'] = ($result[0]['restaurant_image'])?image_url.$result[0]['restaurant_image']:'';
        } 
        return $result;
    }
    // get menu id
    public function getMenuItemID($item_slug){
        $this->db->select('entity_id');
        return $this->db->get_where('restaurant_menu_item',array('item_slug'=>$item_slug))->first_row();
    }
    // get content id
    public function getContentID($item_slug){
        $this->db->select('content_id');
        return $this->db->get_where('restaurant_menu_item',array('item_slug'=>$item_slug))->first_row();
    }
    
}
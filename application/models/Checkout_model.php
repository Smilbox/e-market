<?php
class Checkout_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // get users address
    public function getUsersAddress($UserID){
    	return $this->db->get_where('user_address',array('user_entity_id'=>$UserID))->result_array();
    }
    // get address latlong
    public function getAddressLatLng($entity_id){
    	$this->db->select('latitude,longitude');
    	return $this->db->get_where('user_address',array('entity_id'=>$entity_id))->first_row();
    }
    //get delivery charfes by lat long
    public function checkGeoFence($restaurant_id)
    {
        $this->db->where('restaurant_id',$restaurant_id);
        return $this->db->get('delivery_charge')->result();
    }
    //get coupon list
    public function getCouponsList($subtotal,$restaurant_id,$order_delivery){
        $this->db->select('coupon.name,coupon.entity_id as coupon_id,coupon.amount_type,coupon.amount,coupon.description,coupon.coupon_type,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->join('restaurant','coupon_restaurant_map.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('max_amount <=',$subtotal);
        $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_id);
        $this->db->where('DATE(end_date) >',date('Y-m-d H:i:s'));
        //$this->db->where('(coupon_type = "discount_on_cart" OR coupon_type = "user_registration")');
        /*if($order_delivery == 'delivery'){
            $this->db->where('coupon_type',"free_delivery");
        }
        else*/ 
        if ($order_delivery == 'pickup')
        {
            $this->db->where('coupon_type != "free_delivery"');
        }
        $this->db->where('coupon.status',1);
        return $this->db->get('coupon')->result_array();
    }
    // get coupon details
    public function getCouponDetails($entity_id){
    	return $this->db->get_where('coupon',array('entity_id'=>$entity_id))->first_row();
    }
    //get order count of user
    public function checkUserCountCoupon($UserID)
    {
        $this->db->where('user_id',$UserID);
        return $this->db->get('order_master')->num_rows();
    }
    //get tax
    public function getRestaurantTax($restaurant_id)
    {
        $this->db->select('restaurant.name,restaurant.image,restaurant.phone_number,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('restaurant.entity_id',$restaurant_id);
        return $this->db->get('restaurant')->first_row();
    }
    //get address
    public function getAddress($entity_id){
        $this->db->select('entity_id as address_id,address,landmark,latitude,longitude,city,zipcode');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get('user_address')->first_row();
    }

    //Get resto allow 24
    public function getAllow24Delivery($resto_id)
    {
        $this->db->select('restaurant.allow_24_delivery');
        $this->db->where('restaurant.entity_id', $resto_id);
        return $this->db->get('restaurant')->first_row();
    }
    
    public function get24DeliveryFlatRate($resto_id)
    {
        $this->db->select('restaurant.flat_rate_24');
        $this->db->where('restaurant.entity_id', $resto_id);
        return $this->db->get('restaurant')->first_row();
    }
    
}
?>
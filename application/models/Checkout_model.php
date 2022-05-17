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
    public function checkGeoFence($shop_id)
    {
        $this->db->where('shop_id',$shop_id);
        return $this->db->get('delivery_charge')->result();
    }
    //get coupon list
    public function getCouponsList($subtotal,$shop_id,$order_delivery){
        $this->db->select('coupon.name,coupon.entity_id as coupon_id,coupon.amount_type,coupon.amount,coupon.description,coupon.coupon_type,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('coupon_shop_map','coupon.entity_id = coupon_shop_map.coupon_id','left');
        $this->db->join('shop','coupon_shop_map.shop_id = shop.entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('max_amount <=',$subtotal);
        $this->db->where('coupon_shop_map.shop_id',$shop_id);
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
    public function getShopTax($shop_id)
    {
        $this->db->select('shop.name,shop.image,shop.phone_number,shop.email,shop.amount_type,shop.amount,shop_address.address,shop_address.landmark,shop_address.zipcode,shop_address.city,shop_address.latitude,shop_address.longitude,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('shop_address','shop.entity_id = shop_address.shop_entity_id','left');
        $this->db->join('currencies','shop.currency_id = currencies.currency_id','left');
        $this->db->where('shop.entity_id',$shop_id);
        return $this->db->get('shop')->first_row();
    }
    //get address
    public function getAddress($entity_id){
        $this->db->select('entity_id as address_id,address,landmark,latitude,longitude,city,zipcode');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get('user_address')->first_row();
    }

    //Get shop allow 24
    public function getAllow24Delivery($shop_id)
    {
        $this->db->select('shop.allow_24_delivery');
        $this->db->where('shop.entity_id', $shop_id);
        return $this->db->get('shop')->first_row();
    }
    
    public function get24DeliveryFlatRate($shop_id)
    {
        $this->db->select('shop.flat_rate_24');
        $this->db->where('shop.entity_id', $shop_id);
        return $this->db->get('shop')->first_row();
    }
    
}
?>
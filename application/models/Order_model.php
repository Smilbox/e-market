<?php
class Order_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // verify forgot password
    public function forgotpassowrdVerify($verificationCode){
        return $this->db->get_where('users',array('ActiveCode'=>$verificationCode))->first_row();
    }
    //Update password
    public function updatePassword($updatePassword,$verificationCode)
    {
        $this->db->where('ActiveCode',$verificationCode);
        $this->db->update('users',$updatePassword);
        
        $this->db->select('users.Password,users.Email');
        $this->db->where('ActiveCode',$verificationCode);
        return $this->db->get('users')->first_row();
    }
    // get latest order of logged in user
    public function getLatestOrder($user_id,$order_id=NULL){
        $this->db->select('order_master.entity_id as master_order_id,order_master.*,order_detail.*,order_driver_map.driver_id,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant_address.address,restaurant.timings,restaurant.image as rest_image,restaurant.name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('driver_traking_map','users.entity_id = driver_traking_map.driver_id AND driver_traking_map.traking_id = (SELECT driver_traking_map.traking_id FROM driver_traking_map WHERE driver_traking_map.driver_id = users.entity_id ORDER BY created_date DESC LIMIT 1)','left');

        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel")');
        if (!empty($user_id)) {
            $this->db->where('order_master.user_id',$user_id);
        }
        if (!empty($order_id)) {
            $this->db->where('order_master.entity_id',$order_id);
        }
        $this->db->order_by('order_master.entity_id','desc');
        $result = $this->db->get('order_master')->first_row();

        if (!empty($result)) {
            $result->placed = $result->created_date;
            $result->preparing = '';
            $result->onGoing = '';
            $result->delivered = '';
            // get order status
            $this->db->where('order_status.order_id',$result->master_order_id);
            $Ostatus = $this->db->get('order_status')->result_array();
            if (!empty($Ostatus)) {
                foreach ($Ostatus as $key => $ovalue) {
                    if ($ovalue['order_status'] == 'accepted_by_restaurant') {
                        $result->accepted_by_restaurant = $ovalue['time'];
                    }
                    if ($ovalue['order_status'] == 'preparing') {
                        $result->preparing = $ovalue['time'];
                    }
                    if ($ovalue['order_status'] == 'onGoing') {
                        $result->onGoing = $ovalue['time'];
                    }
                    if ($ovalue['order_status'] == 'delivered') {
                        $result->delivered = $ovalue['time'];
                    }
                }
            }
            $user_detail = unserialize($result->user_detail);
            if (!empty($user_detail)) {
                $result->user_first_name = $user_detail['first_name'];
                $result->user_address = $user_detail['address'];
                $result->user_latitude = $user_detail['latitude'];
                $result->user_longitude = $user_detail['longitude'];
                $result->image = ($result->image)?image_url.$result->image:'';
            }
        }
        return $result;
    }
}
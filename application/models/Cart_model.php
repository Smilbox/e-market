<?php
class Cart_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }

    public function getDeliveryCharges(){
    	/*echo '<pre>'; print_r($this->session->userdata('UserID')); echo '<br>';
    	print_r(get_cookie('cart_shop')); exit;*/
    	return 0;
    }
}
?>
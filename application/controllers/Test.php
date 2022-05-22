<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test extends CI_Controller
{
    public function __construct() {
		parent::__construct();  
		$this->load->model('/order_model');
	}


    public function index()
    {
        $order_id = "<a href='" . base_url() . 'order/track_order/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt(1)) . "' class='btn'>Track Order</a>";
        echo $order_id;
    }
}

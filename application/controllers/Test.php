<?php
defined('BASEPATH') or exit('No direct script access allowed');
// This class was only meant for dumb test, you can delete it if it bothers your
class Test extends CI_Controller
{
    public function __construct() {
		parent::__construct();  
		$this->load->model('/common_model');
	}

    public function mvola() {
        $jsonArray = json_decode(file_get_contents('php://input'),true);
        $transactionStatus = $jsonArray['transactionStatus'];
        $transactionReference = $jsonArray['transactionReference'];

        print_r($this->common_model->updateOrderStatusMvola($transactionStatus, $transactionReference));
    }


    public function index()
    {
        date_default_timezone_set("UTC");
        print_r(date("Y-m-d\TH:i:s.000", strtotime('now')));
    }
}

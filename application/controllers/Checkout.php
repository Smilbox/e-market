<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends CI_Controller {
  
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/shop_model');      
		$this->load->model('/cart_model');         
		$this->load->model('/home_model');         
		$this->load->model('/checkout_model');    
		$this->load->model('v1/gmap_api_model');
		if (empty($this->session->userdata('language_slug'))) {
			$data['lang'] = $this->common_model->getdefaultlang();
			$this->session->set_userdata('language_directory',$data['lang']->language_directory);
			$this->config->set_item('language', $data['lang']->language_directory);
			$this->session->set_userdata('language_slug',$data['lang']->language_slug);
  		}   
	}
	// index chechout page
	public function index()
	{
		$data['current_page'] = 'Checkout';
		$data['page_title'] = $this->lang->line('title_checkout'). ' | ' . $this->lang->line('site_title');
		$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$pre_order_date = get_cookie('pre_order_date');
		$order_mode = get_cookie('order_mode');
		$data['pre_order_date'] = $pre_order_date;
		$data['mode_24'] = !empty($order_mode);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);
		$data['currency_symbol'] = $this->common_model->getShopCurrencySymbol($cart_shop);
		$data['allow_24_delivery'] = $cart_shop ? $this->checkout_model->getAllow24Delivery($cart_shop)->allow_24_delivery : 0;
		if($this->input->post('submit_login_page') == "Login"){
			$this->form_validation->set_rules('login_phone_number', 'Phone Number', 'trim|required'); 
	        $this->form_validation->set_rules('login_password', 'Password', 'trim|required');        
	        if ($this->form_validation->run())
	        {  
	            $phone_number = trim($this->input->post('login_phone_number'));
	            $enc_pass = md5(SALT.trim($this->input->post('login_password')));

	            $this->db->where('mobile_number',$phone_number);
				$this->db->where('password',$enc_pass);
				$this->db->where("(user_type='User')");
				$val = $this->db->get('users')->first_row();  
				if(!empty($val))
				{       
					if($val->status!='0') 
					{
						$this->session->set_userdata(
							array(
							  'UserID' => $val->entity_id,
							  'userFirstname' => $val->first_name,                            
							  'userLastname' => $val->last_name,                            
							  'userEmail' => $val->email,                                   
							  'userPhone' => $val->mobile_number,                            
							  'is_admin_login' => 0,                           
							  'is_user_login' => 1,
							  'UserType' => $val->user_type,
							  'package_id' => array(),
							)
						);
						// remember ME
						$cookie_name = "adminAuth";
						if($this->input->post('rememberMe')==1)
						{                    
							$this->input->set_cookie($cookie_name, 'usr='.$phone_number.'&hash='.$password, 60*60*24*5); // 5 days
						} 
						else 
						{
							delete_cookie($cookie_name);
						}                
						redirect(base_url().'checkout');
					} 
					else if($val->status=='0')
					{                
						$data['loginError'] = $this->lang->line('front_login_deactivate');
					} 
					else 
					{
						$data['loginError'] = $this->lang->line('front_login_error');
					}
				}
				else
				{
					$data['loginError'] = $this->lang->line('front_login_error');
				}
				$this->session->set_flashdata('error_MSG', $data['loginError']);
				redirect(base_url().'checkout');
				exit;
	        }
			$data['page'] = "login";
		}
	    // $this->session->set_userdata(array('checkDelivery' => 'pickup','deliveryCharge' => 0));
		$this->load->view('checkout',$data);
	}
	// ajax checkout page for filters
	public function ajax_checkout(){
		$data['current_page'] = 'Checkout';
		$cart_details = get_cookie('cart_details');
		$arr_cart_details = json_decode($cart_details);
		$cart_shop = get_cookie('cart_shop');
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('shop_id'))) {
			if ($this->input->post('action') == "plus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_shop == $this->input->post('shop_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								$value->quantity = $value->quantity + 1;
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($key == $menukey) {
								$cookie = array(
						            'menu_id'   => $value->menu_id,  
						            'quantity' => ($value->quantity)?($value->quantity+1):1,
						            'addons'  => $value->addons,               
					            );
					 			$arrayDetails[] = $cookie;
				            }
				            else
				            {
				            	$oldcookie = $value;
					 			$arrayDetails[] = $oldcookie;
				            }
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_shop',$this->input->post('shop_id'),60*60*24*1); // 1 day
				}
			}
			else if ($this->input->post('action') == "minus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_shop == $this->input->post('shop_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								$value->quantity = $value->quantity - 1;
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($value->quantity > 1) {
						 		if ($key == $menukey) {
									$cookie = array(
							            'menu_id'   => $value->menu_id,  
							            'quantity' => ($value->quantity)?($value->quantity - 1):1,
							            'addons'  => $value->addons,               
						            );
						 			$arrayDetails[] = $cookie;
					            }
					            else
					            {
					            	$oldcookie = $value;
						 			$arrayDetails[] = $oldcookie;
					            }
					 		}
					 		else
					 		{
					 			if ($key != $menukey) {
					 				$oldcookie = $value;
						 			$arrayDetails[] = $oldcookie;
					 			}
					 		}
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
					$cart_details = $this->getcookie('cart_details');
		            if (empty(json_decode($cart_details))) {
		            	delete_cookie('cart_details');
						delete_cookie('cart_shop');
						delete_cookie('pre_order_date');
						delete_cookie('order_mode');
					}
					else
					{
		            	$this->input->set_cookie('cart_shop',$this->input->post('shop_id'),60*60*24*1); // 1 day
					}
				}
			}
			else if ($this->input->post('action') == "remove" && $this->input->post('cart_key') != '') { 
				$arrayDetails = array();
				if (!empty(json_decode($cart_details))) {
				 	foreach (json_decode($cart_details) as $key => $value) {
				 		if ($key != $this->input->post('cart_key')) {
					 		$oldcookie = $value;
				 			$arrayDetails[] = $oldcookie;
				 		}
				 	}
				}
				$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
				$cart_details = $this->getcookie('cart_details');
	            if (empty(json_decode($cart_details))) {
	            	delete_cookie('cart_details');
					delete_cookie('cart_shop');
					delete_cookie('pre_order_date');
					delete_cookie('order_mode');
				}
				else
				{
	            	$this->input->set_cookie('cart_shop',$this->input->post('shop_id'),60*60*24*1); // 1 day
				}
			} 
			$cart_details = $this->getcookie('cart_details');
			$cart_shop = $this->getcookie('cart_shop');
		}

		// if cart_details cookie has been deleted
		// if cart_details cookie has been deleted
		if($cart_details[1] == "deleted")
			$cart_details = array();

		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);
		$data['currency_symbol'] = $this->common_model->getShopCurrencySymbol($cart_shop);
		$data['order_mode'] = $this->session->userdata('order_mode');
		$ajax_your_items = $this->load->view('ajax_your_items',$data,true);
		$order_summary = $this->load->view('ajax_order_summary',$data,true);
		$array_view = array(
			'ajax_your_items'=>$ajax_your_items,
			'ajax_order_summary'=>$order_summary
		);
		echo json_encode($array_view);
	}
	// get the recently added cookies
	public function getcookie($name) { 
	    $cookies = [];
	    $headers = headers_list(); 
	    foreach($headers as $key => $header) { 
	        if (strpos($header, 'Set-Cookie: ') === 0) {
	            $value = str_replace('&', urlencode('&'), substr($header, 12));
	            parse_str(current(explode(';', $value)), $pair);
	            $cookies = array_merge_recursive($cookies, $pair);
	        }
	    }
	    return $cookies[$name];
	}
	// get Cart items
	public function getCartItems($cart_details,$cart_shop){
		$cartItems = array();
		$cartTotalPrice = 0;
		if (!empty($cart_details)) {
			foreach (json_decode($cart_details) as $key => $value) { 
				$details = $this->shop_model->getMenuItem($value->menu_id,$cart_shop);
				if (!empty($details)) {
					if ($details[0]['items'][0]['is_customize'] == 1) {
						$addons_category_id = array_column($value->addons, 'addons_category_id');
						$add_onns_id = array_column($value->addons, 'add_onns_id');
						
						if (!empty($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (!in_array($cat_value['addons_category_id'], $addons_category_id)) {
									unset($details[0]['items'][0]['addons_category_list'][$key]);
								}
								else
								{
									if (!empty($cat_value['addons_list'])) {
										foreach ($cat_value['addons_list'] as $addkey => $add_value) {
											if (!in_array($add_value['add_ons_id'], $add_onns_id)) {
												unset($details[0]['items'][0]['addons_category_list'][$key]['addons_list'][$addkey]);
											}
										}
									}
								}
							}
						}
					}
					// getting subtotal
					if ($details[0]['items'][0]['is_customize'] == 1) 
					{	$subtotal = 0;
						if (!empty($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (!empty($cat_value['addons_list'])) {
									foreach ($cat_value['addons_list'] as $addkey => $add_value) {
										$subtotal += $add_value['add_ons_price'];
									}
								}
							}
						}
					}
					else
					{	$subtotal = 0;
						if ($details[0]['items'][0]['is_deal'] == 1) {
							$price = ($details[0]['items'][0]['offer_price'])?$details[0]['items'][0]['offer_price']:(($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0);
						}
						else
						{
							$price = ($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0;
						}
						$subtotal = $subtotal + $price;
					}
					$cartTotalPrice = ($subtotal * $value->quantity) + $cartTotalPrice;
					$cartItems[] = array(
						'menu_id' => $details[0]['items'][0]['menu_id'],
						'shop_id' => $cart_shop,
						'name' => $details[0]['items'][0]['name'],
						'quantity' => $value->quantity,
						'is_customize' => $details[0]['items'][0]['is_customize'],
						'is_under_20_kg' => $details[0]['items'][0]['is_under_20_kg'],
						'is_deal' => $details[0]['items'][0]['is_deal'],
						'price' => $details[0]['items'][0]['price'],
						'offer_price' => $details[0]['items'][0]['offer_price'],
						'subtotal' => $subtotal,
						'totalPrice' => ($subtotal * $value->quantity),
						'cartTotalPrice' => $cartTotalPrice,
						'addons_category_list' => isset($details[0]['items'][0]['addons_category_list']) ? $details[0]['items'][0]['addons_category_list'] : array(),
					);
				}
			}
		}
		$cart_details = array(
			'cart_items' => $cartItems,
			'cart_total_price' => $cartTotalPrice,
		);
		return $cart_details;
	}
	// get lat long from the address
	public function getAddressLatLng(){
		$latlong = array();
		if (!empty($this->input->post('entity_id'))) {
			$latlong = $this->checkout_model->getAddressLatLng($this->input->post('entity_id'));
		}
		echo json_encode($latlong);
	}
	// get the delivery charges
	public function getDeliveryCharges(){ 
		$check = '';
		if (!empty($this->input->post('action')) && $this->input->post('action') == "get") { 
			if (!empty($this->input->post('latitude')) && !empty($this->input->post('longitude'))) {
				$cart_shop = 0;
				if(!empty($this->input->post('shop_id'))) {
					$cart_shop = $this->input->post('shop_id');
					$array_view = array(
						'check'=> $check ? $check : '',
						'ajax_order_summary'=>$order_summary
					);
					//echo '<pre>'; print_r($array_view); exit;
					return json_encode($array_view);
				} else {
					$cart_shop = get_cookie('cart_shop');
				}
				// $check = $this->checkGeoFence($this->input->post('latitude'),$this->input->post('longitude'),$price_charge = true,$cart_shop);
				$delivery_type = $this->input->post('mode_24') == "true" ? "24H Delivery" : "Express Delivery";
				$check = $this->getDeliveryByDistance(null, $this->input->post('latitude')."~".$this->input->post('longitude'), $cart_shop, $delivery_type);
				
				if ($check) {
					$this->session->set_userdata(array('checkDelivery' => 'available','deliveryCharge' => $check));
				}
				else
				{
					$this->session->set_userdata(array('checkDelivery' => 'notAvailable','deliveryCharge' => 0));
				}
				if ($this->input->post('mode_24') == "true" && ($check == "" || !$check)) {
					$check = $this->checkout_model->get24DeliveryFlatRate($cart_shop)->flat_rate_24;
					$this->session->set_userdata(array('checkDelivery' => 'available','deliveryCharge' => $check));
				}
			}
		}
		if (!empty($this->input->post('action')) && $this->input->post('action') == "remove") { 
			$check = 0;
			$this->session->set_userdata(array('checkDelivery' => 'pickup','deliveryCharge' => 0));
		}
		if ($check == '' || $check == 0) {
			$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));
		}
		/*if ($this->session->userdata('coupon_applied') == "yes" && $this->session->userdata('coupon_id') != '') {
			$checkCoupon = $this->checkout_model->getCouponDetails($this->session->userdata('coupon_id'));
    		if(!empty($checkCoupon)){
				$discount = $this->session->userdata('deliveryCharge');
                $this->session->set_userdata(
	            	array(
		            	'coupon_id' => $checkCoupon->entity_id,
		            	'coupon_type' => $checkCoupon->amount_type,
		            	'coupon_amount' => $checkCoupon->amount,
		            	'coupon_discount' => abs($discount),
		            	'coupon_name' => $checkCoupon->name
	            	)
	            );
    		}
		}*/ //echo '<pre>';
		//print_r($this->session->userdata());
    	$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);
		$data['currency_symbol'] = $this->common_model->getShopCurrencySymbol($cart_shop);
		$data['order_mode'] = $this->session->userdata('order_mode');
		$order_summary = $this->load->view('ajax_order_summary',$data,true);
		$array_view = array(
			'check'=> $check ? $check : '',
			'ajax_order_summary'=>$order_summary
		);
		//echo '<pre>'; print_r($array_view); exit;
		echo json_encode($array_view);


		//echo $check;
	}
	// remove the delivery charges
	public function removeDeliveryOptions(){
		$this->session->set_userdata(array('checkDelivery' => 'pickup','deliveryCharge' => 0));
    	$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);
		$data['currency_symbol'] = $this->common_model->getShopCurrencySymbol($cart_shop);
		$data['order_mode'] = $this->session->userdata('order_mode');
		$this->load->view('ajax_order_summary',$data);
	}
    //check lat long exist in area
    public function checkGeoFence($latitude,$longitude,$price_charge,$shop_id)
    {
        $result = $this->checkout_model->checkGeoFence($shop_id); 
        $latlongs =  array($latitude,$longitude);
        $coordinatesArr = array();
        if (!empty($result)) {
        	if (!empty($result[0]->lat_long)) {
        		$lat_longs =  explode('~', $result[0]->lat_long);
        		foreach ($lat_longs as $key => $value) {
        			$val = str_replace(array('[',']'),array('',''),$value);
        			$coordinatesArr[] =  explode(',', $val);
        		}
	        }
        }
        return $output = $this->checkFence($latlongs, $coordinatesArr, $result[0]->price_charge);
    }
    // check geo fence area
    public function checkFence($point, $polygon, $price_charge)
	{
	    if($polygon[0] != $polygon[count($polygon)-1])
	            $polygon[count($polygon)] = $polygon[0];
	    $j = 0;
	    $oddNodes = '';
	    $x = $point[1];
	    $y = $point[0];
	    $n = count($polygon);
	    for ($i = 0; $i < $n; $i++)
	    {
	        $j++;
	        if ($j == $n)
	        {
	            $j = 0;
	        }
	        if ((($polygon[$i][0] <= $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] <= $y) && ($polygon[$i][0] >=
	            $y)))
	        {
	            if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
	                $polygon[$i][1]) < $x)
	            {
	                $oddNodes = 'true';
	            }
	        }
	    }
	    $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
	    return $oddNodes;
	}

	public function getDeliveryByDistance($originLatLong, $destinationLatLong, $shop_id, $delivery_type = "Express Delivery")
    {
        if(!$originLatLong)
        {
            $address = $this->common_model->getShopLatLong($shop_id);
            $originLatLong = $address->latitude."~".$address->longitude;
        }
		
		$origin = explode("~", $originLatLong);
		$destination = explode("~", $destinationLatLong);

		$distance = $this->gmap_api_model->getDistance($origin[1], $origin[0], $destination[1], $destination[0]);
		if($distance == null)
		{
			$distance = $this->_getDistance($origin[0], $origin[1], $destination[0], $destination[1]);
		}

        if($distance != null)
        {
            return $this->getFeeAccordingDistance(round($distance, 2), $shop_id, $delivery_type);
        }
        else
        {
            return null;
        }
	}
	
	// get distance between two pair of coordinates
	function _getDistance($latitude1, $longitude1, $latitude2, $longitude2) {  
		$earth_radius = 6371;

		$dLat = deg2rad($latitude2 - $latitude1);  
		$dLon = deg2rad($longitude2 - $longitude1);  

		$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
		$c = 2 * asin(sqrt($a));  
		$d = $earth_radius * $c;  
		return $d;  
	}
    
    /* public function getDistance($originLatLong, $destinationLatLong)
    {
        $origin = str_replace('~', ',' ,$originLatLong);
        $destination = str_replace('~', ',' ,$destinationLatLong);
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$origin."&destinations=".$destination."&key=".GMAP_API_KEY;
        $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_all = json_decode($response);
        // $rows = (array) $response_all->rows;
        // $elem = (array) $rows[0];
        $textDistance = $response_all->rows[0]->elements[0]->distance->text;
        if(isset($textDistance) && $response_all->rows[0]->elements[0]->status != "ZERO_RESULTS")
        {
            if(strpos($textDistance, 'km')) 
            {
                return floatval(str_replace(array("km"," "), array("",""), $textDistance));
            }
            if(strpos($textDistance, 'm')) 
            {
                return null;
            }
        } 
        else {
            return null; 
        }
    } */

    public function getFeeAccordingDistance($distance, $shop_id, $delivery_type)
    {
        $allDeliveryCharge = $this->common_model->getRowsMultipleWhere("delivery_charge", array("shop_id" => $shop_id, "delivery_type" => $delivery_type));
        $deliveryFee = "";
        if(!empty($allDeliveryCharge))
        {
            foreach($allDeliveryCharge as $key => $delivery)
            {
                if($deliveryFee == "")
                {
                    $range = str_replace(array("km", " "), array("",""), $delivery->area_name);
                    // $range = str_replace("km", "", $delivery->area_name);
                    // $range = str_replace(" ", "", $range);
                    $arrayRange = explode('-', $range);
                    if(($arrayRange[0] <= $distance) && ($distance <=  $arrayRange[1]))
                    {
                        $deliveryFee = $delivery->price_charge;
                    break;
                    }
                }
            }
        }

        return $deliveryFee;
    }

	// get the coupons
    public function getCoupons(){
    	$html = '';
    	$cart_shop = get_cookie('cart_shop');
    	$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));
		$this->session->unset_userdata('coupon_type');
		$this->session->unset_userdata('coupon_amount');
		$this->session->unset_userdata('coupon_discount');
		$this->session->unset_userdata('coupon_name');
    	if (!empty($this->input->post('subtotal')) && !empty($this->input->post('order_mode'))) {
    		$coupons = $this->checkout_model->getCouponsList($this->input->post('subtotal'),$cart_shop,$this->input->post('order_mode'));
    		$order_mode = "'".$this->input->post('order_mode')."'";
    		if (!empty($coupons)) {
				$html = '<h5>'.$this->lang->line("choose_avail_coupons").'</h5>
				<form id="coupon_form" name="coupon_form" class="form-horizontal float-form">
						<div class="login-details">
                            <div id="coupons" class="form-group">
								<select class="form-control" name="add_coupon" id="add_coupon" onchange="getCouponDetails(this.value,'.$this->input->post('subtotal').','.$order_mode.')">
			                        <option value="">'.$this->lang->line("select").'</option>';
			                        foreach ($coupons as $key => $value) {
			                        	$html .= '<option value="'.$value["coupon_id"].'">'.$value["name"].'</option>';    
			                        } 
				                $html .= '</select>
				                <label>'.$this->lang->line("your_coupons").'</label>
							</div>
						</div>
				</form>';
    		}
    		else
    		{
    			$html = '<h5>'.$this->lang->line("no_coupons_available").'</h5>';
    			$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));
    		}
    	} 
    	$this->session->set_userdata(array('order_mode' => $this->input->post('order_mode')));
    	$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);
		$data['currency_symbol'] = $this->common_model->getShopCurrencySymbol($cart_shop);
		$data['order_mode'] = $this->input->post('order_mode');
		$order_summary = $this->load->view('ajax_order_summary',$data,true);
		$array_view = array(
			'html'=>$html,
			'ajax_order_summary'=>$order_summary
		);
		echo json_encode($array_view);
    }
    // add a coupon for a order
    public function addCoupon(){
		$data['page_title'] = $this->lang->line('add_coupon'). ' | ' . $this->lang->line('site_title');
    	if (!empty($this->input->post('coupon_id')) && !empty($this->input->post('subtotal'))) {
    		$this->session->set_userdata(array('coupon_id' => $this->input->post('coupon_id'),'coupon_applied' => 'yes'));
    		$check = $this->checkout_model->getCouponDetails($this->input->post('coupon_id'));
    		$status = 1;
    		if(!empty($check)){
                if($check->coupon_type == 'discount_on_cart'){
                    if($check->amount_type == 'Percentage'){
                        $discount = round(($this->input->post('subtotal') * $check->amount)/100);
                       
                    }else if($check->amount_type == 'Amount'){
                        $discount = $check->amount;
                        
                    }
                    
                    $coupon_id = $check->entity_id;  
                    $coupon_type = $check->amount_type;
                    $coupon_amount = $check->amount;  
                    $coupon_discount = abs($discount);
                    $name = $check->name;     
                }
                if($check->coupon_type == 'free_delivery'){
                   
                    $discount = $this->session->userdata('deliveryCharge');

                    $coupon_id = $check->entity_id;  
                    $coupon_type = $check->amount_type;
                    $coupon_amount = $check->amount;  
                    $coupon_discount = abs($discount);
                    $name = $check->name;     
                }
                if($check->coupon_type == 'user_registration'){
                    $checkOrderCount = $this->checkout_model->checkUserCountCoupon($this->session->userdata('UserID'));
                    if($checkOrderCount > 0){
                        $status = 2;
                    }else{
                        if($check->amount_type == 'Percentage'){
                            $discount = round(($this->input->post('subtotal') * $check->amount)/100);
                            
                        }else if($check->amount_type == 'Amount'){
                            $discount = $check->amount;
                        } 
                        $coupon_id = $check->entity_id;  
                        $coupon_type = $check->amount_type;
                        $coupon_amount = $check->amount;  
                        $coupon_discount = abs($discount);
                        $name = $check->name;     
                    }
                }
            }
            if ($status == 1) {
	            $this->session->set_userdata(
	            	array(
		            	'coupon_id' => $coupon_id,
		            	'coupon_type' => $coupon_type,
		            	'coupon_amount' => $coupon_amount,
		            	'coupon_discount' => $coupon_discount,
		            	'coupon_name' => $name
	            	)
	            );
            }
    	}
    	else
    	{
    		$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));
    		$this->session->unset_userdata('coupon_type');
    		$this->session->unset_userdata('coupon_amount');
    		$this->session->unset_userdata('coupon_discount');
    		$this->session->unset_userdata('coupon_name');
    	}
    	$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);
		$data['currency_symbol'] = $this->common_model->getShopCurrencySymbol($cart_shop);
		$data['order_mode'] = $this->input->post('order_mode');
    	$this->load->view('ajax_order_summary',$data);
    }
	//add order
    public function addOrder(){
		$data['page_title'] = $this->lang->line('add_order'). ' | ' . $this->lang->line('site_title');
    	$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$pre_order_date = get_cookie('pre_order_date');
		$cart_item_details = $this->getCartItems($cart_details,$cart_shop);
    	if ($this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('UserID')) && !empty($cart_shop)) {
			$shop_detail = $this->checkout_model->getShopTax($cart_shop);
			
			// Update user phone number
			$this->common_model->updateData('users', array(
				// 'mobile_number' => $this->input->post('phone_number'),
				'phone_number' => $this->input->post('phone_number'),
			), 'entity_id', $this->session->userdata('UserID'));

    		$add_data = array(              
                'user_id'=> $this->session->userdata('UserID'),
                'shop_id' => $cart_shop,
                'address_id' => ($this->input->post('your_address'))?$this->input->post('your_address'):'',
                'order_status' =>'placed',
				'order_date' =>date('Y-m-d H:i:s'),
				'pre_order_delivery_date' => ($pre_order_date == "Livraison 24H" || $pre_order_date == "24H delivery") ? null : $pre_order_date,
                'subtotal'=> ($this->input->post('subtotal'))?$this->input->post('subtotal'):0,
                'total_rate' => ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):'',
                'status'=>0,
                'delivery_charge'=> ($this->session->userdata('deliveryCharge'))?$this->session->userdata('deliveryCharge'):'',
                'extra_comment'=> ($this->input->post('extra_comment'))?$this->input->post('extra_comment'):'',
                'payment_option'=> ($this->input->post('payment_option'))?$this->input->post('payment_option'):'',
            ); 
            if ($this->session->userdata('coupon_applied') == "yes") {
            	$add_data['coupon_id'] = ($this->session->userdata('coupon_id'))?$this->session->userdata('coupon_id'):'';
            	$add_data['coupon_type'] = ($this->session->userdata('coupon_type'))?$this->session->userdata('coupon_type'):'';
            	$add_data['coupon_amount'] = ($this->session->userdata('coupon_amount'))?$this->session->userdata('coupon_amount'):'';
            	$add_data['coupon_discount'] = ($this->session->userdata('coupon_discount'))?$this->session->userdata('coupon_discount'):'';
            	$add_data['coupon_name'] = ($this->session->userdata('coupon_name'))?$this->session->userdata('coupon_name'):'';
            }
			if($this->input->post('choose_order')=='delivery'){
                $add_data['order_delivery'] = 'Delivery';
            } else if($this->input->post('choose_order')=='delivery_24') {
				$add_data['order_delivery'] = '24H Delivery';
			} else {
                $add_data['order_delivery'] = 'PickUp';
                $default_address = $this->common_model->getSingleRowMultipleWhere('user_address',array('user_entity_id'=>$this->session->userdata('UserID'),'is_main'=>1));
                if (!empty($default_address)) {
                	$add_data['address_id'] = $default_address->entity_id;
                }
            } 
            $order_id = $this->common_model->addData('order_master',$add_data); 
            // get user details array
            $user_detail = array();
            if ($this->input->post('choose_order')=='delivery' || $this->input->post('choose_order')=='delivery_24' ) {
	            if ($this->input->post('add_new_address') == "add_your_address" && !empty($this->input->post('your_address'))) {
		            $address = $this->checkout_model->getAddress($this->input->post('your_address'));
		            $user_detail = array(
		                'first_name'=>$this->session->userdata('userFirstname'),
		                'last_name'=>($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):'',
		                'address'=>($address)?$address->address:'',
		                'landmark'=>($address)?$address->landmark:'',
		                'zipcode'=>($address)?$address->zipcode:'',
		                'city'=>($address)?$address->city:'',
		                'latitude'=>($address)?$address->latitude:'',
		                'longitude'=>($address)?$address->longitude:'',
		            );
	            }
	            else if ($this->input->post('add_new_address') == "add_new_address") {
	            	$add_address = array(
		                'address'=> $this->input->post('add_address')." ".$this->input->post('add_address_area'),
		                'landmark'=> $this->input->post('landmark'),
		                'latitude'=> $this->input->post('add_latitude'),
		                'longitude'=> $this->input->post('add_longitude'),
		                'zipcode'=> $this->input->post('zipcode'),
		                'city'=> $this->input->post('city'),
		                'user_entity_id'=> $this->session->userdata('UserID')
		            );
		            $this->common_model->addData('user_address',$add_address);
		            $user_detail = array(
		                'first_name'=>$this->session->userdata('userFirstname'),
		                'last_name'=>($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):'',
		                'address'=> $this->input->post('add_address'),
		                'landmark'=> $this->input->post('landmark'),
		                'zipcode'=> $this->input->post('zipcode'),
		                'city'=> $this->input->post('city'),
		                'latitude'=> $this->input->post('add_latitude'),
		                'longitude'=> $this->input->post('add_longitude'),
		            );
	            }
            }
            else if (!empty($add_data['address_id'])) {
            	$address = $this->checkout_model->getAddress($add_data['address_id']);
            	$user_detail = array(
	                'first_name'=>$this->session->userdata('userFirstname'),
					'last_name'=>($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):'',
	                'address'=>($address)?$address->address:'',
	                'landmark'=>($address)?$address->landmark:'',
	                'zipcode'=>($address)?$address->zipcode:'',
	                'city'=>($address)?$address->city:'',
	                'latitude'=>($address)?$address->latitude:'',
	                'longitude'=>($address)?$address->longitude:'',
	            );
            }
            // get item details array
            $add_item = array();
            if (!empty($cart_details) && !empty($cart_item_details['cart_items'])) {
            	foreach ($cart_item_details['cart_items'] as $key => $value) {
            		if($value['is_customize'] == 1){
            			$customization = array();
                        foreach ($value['addons_category_list'] as $k => $val) {
                            $customization[] = array(
                                'addons_category_id'=>$val['addons_category_id'],
                                'addons_category'=>$val['addons_category'],
                                'addons_list'=>$val['addons_list']
                            );
                        }
                        $add_item[] = array(
                            "item_name"=>$value['name'],
                            "item_id"=>$value['menu_id'],
                            "qty_no"=>$value['quantity'],
                            "rate"=>($value['price'])?$value['price']:'',
                            "offer_price"=>($value['offer_price'])?$value['offer_price']:'',
                            "order_id"=>$order_id,
                            "is_customize"=>1,
                            "is_deal"=>$value['is_deal'],
                            "subTotal"=>$value['subtotal'],
                            "itemTotal"=>$value['totalPrice'],
                            "addons_category_list"=>$customization
                        );
            		}
            		else
            		{
						$add_item[] = array(
							"item_name"=>$value['name'],
							"item_id"=>$value['menu_id'],
							"qty_no"=>$value['quantity'],
							"rate"=>($value['price'])?$value['price']:'',
							"offer_price"=>($value['offer_price'])?$value['offer_price']:'',
							"order_id"=>$order_id,
							"is_customize"=>0,
                            "is_deal"=>$value['is_deal'],
                            "subTotal"=>$value['subtotal'],
                            "itemTotal"=>$value['totalPrice'],
                        );
            		}
            	}
            }
    	}
        $order_detail = array(
            'order_id'=>$order_id,
            'user_detail' => serialize($user_detail),
            'item_detail' => serialize($add_item),
            'shop_detail' => serialize($shop_detail),
        );
        $this->common_model->addData('order_detail',$order_detail);

        // $verificationCode = random_string('alnum',25);
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-receive-alert','language_slug'=>$language_slug,'status'=>1))->first_row();                    
       
        $this->db->select('OptionValue');
        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

        $this->db->select('OptionValue');
        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();  
        if(!empty($email_template)){
            $this->load->library('email');  
            $config['charset'] = 'iso-8859-1';  
            $config['wordwrap'] = TRUE;  
            $config['mailtype'] = 'html';  
            $this->email->initialize($config);  
            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
            $this->email->to(trim($FromEmailID->OptionValue)); 
            // $this->email->to(trim($shop_detail->email)); 
            $this->email->subject($email_template->subject);  
            $this->email->message($email_template->message);  
            if(!$this->email->send()){
				show_error($this->email->print_debugger());
				$arrdata = array('result'=> 'fail','order_id'=> '');
				echo json_encode($arrdata);
				die;
			}
        }
        if ($order_id) {
			$this->session->unset_userdata('checkDelivery');
			$this->session->unset_userdata('deliveryCharge');
			$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));
			$this->session->unset_userdata('coupon_type');
			$this->session->unset_userdata('coupon_amount');
			$this->session->unset_userdata('coupon_discount');
			$this->session->unset_userdata('coupon_name');
			delete_cookie('cart_details');
			delete_cookie('cart_shop');
			delete_cookie('pre_order_date');
			delete_cookie('order_mode');
        	//echo "success";
        	if ($this->input->post('choose_order')=='delivery' || $this->input->post('choose_order')=='delivery_24') {
        		$order_id = "<a href='".base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id))."' class='btn'>Track Order</a>";
        	}
        	else
        	{
        		// $order_id = "<a href='".base_url().'myprofile'."' class='btn'>View Details</a>";
				$order_id = "<a href = '". base_url() .'myprofile' . "' class = 'btn' >" . $this->lang->line('view_details') ."</a>";
        	}
        	$arrdata = array('result'=> 'success','order_id'=> $order_id, 'total_rate' => ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):'');
        }
        else
        {
        	$arrdata = array('result'=> 'fail','order_id'=> '');
        }
		// $arrdata = array('result'=> 'fail','order_id'=> '');

		if($this->input->post('mobile_money_option') !== null && !empty($this->input->post('mobile_money_option'))) {
			$mobileMoneyOption = $this->input->post('mobile_money_option');
			if($this->session->userdata('total_price') === null || $this->session->userdata('total_price') === 0 || empty($this->session->userdata('total_price'))) {
				return $this->output
							->set_content_type('application/json')
							->set_status_header(406)
							->set_output(json_encode(array(
								"error_message" => "No amount to be paid was received."
							)));
			}

			if($mobileMoneyOption === "MVOLA") {

			} else if($mobileMoneyOption === "AIRTEL_MONEY") {
				if($this->input->post('airtel_money_phone_number') === null || empty($this->input->post('airtel_money_phone_number'))) {
					return $this->output
							->set_content_type('application/json')
							->set_status_header(400)
							->set_output(json_encode(array(
								"error_message" => "No Airtel money phone number given"
							)));
				} 

				$bearerToken = $this->getAirtelMoneyBearerAccessToken();
				$headers = array(
					'Content-Type: application/json',
					'X-Country: MG',
					'X-Currency: MGA',
					'Authorization: Bearer ' . $bearerToken
				);
				$fields = array(
					"reference" => 'Achat EMarket',
					"subscriber" => array(
						"country" => "MG",
						"currency" => "MGA",
						"msisdn" => $this->input->post('airtel_money_phone_number')
					),
					"transaction" => array(
						"amount" => $this->session->userdata('total_price'),
						"country" => "MG",
						"currency" => "MGA",
						"id" => "emarket" . time()
					)
				);
				#Send Reponse To FireBase Server    
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, AIRTEL_URL_API.'/merchant/v1/payments/');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
				$result = curl_exec($ch);
				curl_close($ch);
				print_r($result);
				$json = json_decode($result);
			} else if($mobileMoneyOption === "ORANGE_MONEY") {

			}
		}
        echo json_encode($arrdata);	
    }

	private function getAirtelMoneyBearerAccessToken() {
		$headers = array (
			'Content-Type: application/json'
		);
		$fields = array(
			"client_id" => AIRTEL_MONEY_CLIENT_ID,
			"client_secret" => AIRTEL_MONEY_CLIENT_SECRET,
			"grant_type" => "client_credentials"
		);
		#Send Reponse To FireBase Server    
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://openapiuat.airtel.africa/auth/oauth2/token' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch);
		curl_close($ch);
		$json = json_decode($result);
		return $json->{'access_token'};
	}
}
?>
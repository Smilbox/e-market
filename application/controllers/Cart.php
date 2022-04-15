<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {
  
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/restaurant_model');      
		$this->load->model('/cart_model');    
		$this->load->model(ADMIN_URL.'/store_type_model');    
	}
	// index function
	public function index()
	{
		$data['current_page'] = 'Cart';
		$data['page_title'] = $this->lang->line('title_cart'). ' | ' . $this->lang->line('site_title');
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		$this->load->view('cart',$data);
	}
	// checkout page
	public function checkout()
	{
		$data['current_page'] = 'Checkout';
		$data['page_title'] = $this->lang->line('title_cart'). ' | ' . $this->lang->line('site_title');
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$pre_order_date = get_cookie('pre_order_date');
		$order_mode = get_cookie('order_mode');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		$this->load->view('checkout',$data);
	}

	public function storePreOrder(){
		if(!empty($this->input->post('pre_order_date'))) {
			$this->input->set_cookie('pre_order_date',$this->input->post('pre_order_date'),60*60*24*1); // 1 day	
		}
		if(!empty($this->input->post('order_mode'))) {
			$this->input->set_cookie('order_mode',$this->input->post('order_mode'),60*60*24*1); // 1 day	
		}
	}
	// add to cart
	public function addToCart(){ 
		$data['page_title'] = $this->lang->line('title_cart'). ' | ' . $this->lang->line('site_title');
		if (!empty($this->input->post('menu_id')) && !empty($this->input->post('add_ons_array'))) {
			$itemArray = array();
			$data['another_restaurant'] = '';
			$menuDetails = $this->restaurant_model->getMenuItem($this->input->post('menu_id'),$this->input->post('restaurant_id'));
			foreach ($menuDetails as $key => $value) {
				$itemArray['name'] = $value['items'][0]['name'];
				$itemArray['image'] = $value['items'][0]['image'];
				$itemArray['menu_id'] = $value['items'][0]['menu_id'];
				$itemArray['price'] = $value['items'][0]['price'];
				$itemArray['offer_price'] = $value['items'][0]['offer_price'];
				$itemArray['is_veg'] = $value['items'][0]['is_veg'];
				$itemArray['is_customize'] = $value['items'][0]['is_customize'];
				$itemArray['is_deal'] = $value['items'][0]['is_deal'];
				$itemArray['availability'] = $value['items'][0]['availability'];
			}
			$itemArray['restaurant_id'] = $this->input->post('restaurant_id');
			$itemArray['itemTotal'] = $this->input->post('totalPrice');
			$itemArray['addons_category_list'] = $this->input->post('add_ons_array');
			$addons = array();
			if (!empty($itemArray)) {
				if (!empty($itemArray['addons_category_list'])) { 
					foreach ($itemArray['addons_category_list'] as $key => $value) {
						if (!empty($value['addons_list'])) {
							if (is_array(reset($value['addons_list']))) {
								foreach ($value['addons_list'] as $key => $addvalue) {
									$addons[] = array(
										'addons_category_id'=> $value['addons_category_id'],
										'add_onns_id' => $addvalue['add_ons_id']
									);
								}
							}
							else
							{
								$addons[] = array(
									'addons_category_id'=> $value['addons_category_id'],
									'add_onns_id' => $value['addons_list']['add_ons_id']
								);
							}
						}
					}
				}
				$cart_details = get_cookie('cart_details');
				$cart_restaurant = get_cookie('cart_restaurant');
				$arrayDetails = array();
				if (!empty(json_decode($cart_details))) {
				 	foreach (json_decode($cart_details) as $key => $value) {
			            $oldcookie = $value;
			 			$arrayDetails[] = $oldcookie;
				 	}
				} 
				if (empty($cookie)) {
					$cookie = array(
			            'menu_id'   => $itemArray['menu_id'], 
		            	'quantity' => 1, 
			            'addons'  => $addons,          
		            );
				}
	            $arrayDetails[] = $cookie;
				if (empty($cart_details) && empty($cart_restaurant)) {
		            $this->input->set_cookie('',json_encode($arrayDetails),60*60*24*1); // 1 day 
		            $this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
		            $data['cart_details'] = $this->getcookie('cart_details');
					$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
				}
				else if ($cart_restaurant == $this->input->post('restaurant_id')) {
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
		            $data['cart_details'] = $this->getcookie('cart_details');
					$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
				}
				else
				{
					$data['another_restaurant'] = 'AnotherRestaurant';
					$data['cart_details'] = get_cookie('cart_details');
					$data['cart_restaurant'] = get_cookie('cart_restaurant');
				}
			}
		}  
		if (!empty($this->input->post('menu_item_id'))) {
			$cart_details = get_cookie('cart_details');
			$cart_restaurant = get_cookie('cart_restaurant');
			$arrayDetails = array();
			
			if (!empty(json_decode($cart_details))) {
				foreach (json_decode($cart_details) as $key => $value) {
		            if ($value->menu_id == $this->input->post('menu_item_id')) {
						$cookie = array(
				            'menu_id'   => $this->input->post('menu_item_id'),  
				            'quantity' => ($value->quantity)?($value->quantity+1):1,
				            'addons'  => '',               
			            );
		            }
		            else
		            {
		            	$oldcookie = $value;
			 			$arrayDetails[] = $oldcookie;
		            }
				}
			} 
			if (empty($cookie)) {
				$cookie = array(
		            'menu_id'   => $this->input->post('menu_item_id'), 
		            'quantity' => 1, 
		            'addons'  => '',               
	            );
			}
            $arrayDetails[] = $cookie;
			if (empty($cart_details) && empty($cart_restaurant)) {
	            $this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
	            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
	            $data['cart_details'] = $this->getcookie('cart_details');
				$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
			}
			else if ($cart_restaurant == $this->input->post('restaurant_id')) {
				$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
	            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
	            $data['cart_details'] = $this->getcookie('cart_details');
				$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
			}
			else
			{	
				$data['another_restaurant'] = 'AnotherRestaurant';
				$data['cart_details'] = get_cookie('cart_details');
				$data['cart_restaurant'] = get_cookie('cart_restaurant');
			}
		}
		
		$data['cart_details'] = $this->getCartItems($data['cart_details'],$data['cart_restaurant']);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($data['cart_restaurant']);
		$this->load->view('ajax_your_cart',$data);
	}
	// get Cart items
	public function getCartItems($cart_details,$cart_restaurant){
		$cartItems = array();
		$cartTotalPrice = 0;
		if (!empty($cart_details)) {
			foreach (json_decode($cart_details) as $key => $value) { 
				$details = $this->restaurant_model->getMenuItem($value->menu_id,$cart_restaurant);
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
						'restaurant_id' => $cart_restaurant,
						'name' => $details[0]['items'][0]['name'],
						'quantity' => $value->quantity,
						'is_customize' => $details[0]['items'][0]['is_customize'],
						'is_veg' => $details[0]['items'][0]['is_veg'],
						'is_deal' => $details[0]['items'][0]['is_deal'],
						'price' => $details[0]['items'][0]['price'],
						'offer_price' => $details[0]['items'][0]['offer_price'],
						'subtotal' => $subtotal,
						'totalPrice' => ($subtotal * $value->quantity),
						'cartTotalPrice' => $cartTotalPrice,
						'addons_category_list' => $details[0]['items'][0]['addons_category_list'],
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
	// get the cookies
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
	public function checkMenuItem()
	{
		$menuItemExist = 0;
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) { 
			$cart_details = get_cookie('cart_details');
			$cart_restaurant = get_cookie('cart_restaurant');
			if ($cart_restaurant == $this->input->post('restaurant_id')) {
				if (!empty(json_decode($cart_details))) {
					foreach (json_decode($cart_details) as $key => $value) {
						if ($value->menu_id == $this->input->post('entity_id')) {
							$menuItemExist = 1;
						}
					}
				}
			}
		}
		echo $menuItemExist;
	}
	// get the custom items count
	public function customItemCount()
	{
		$cart_details = get_cookie('cart_details');
		$arr_cart_details = json_decode($cart_details);
		$cart_restaurant = get_cookie('cart_restaurant');
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) {
			if ($this->input->post('action') == "plus" && $this->input->post('cart_key') == "") { 
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $key => $value) {
							if ($value->menu_id == $this->input->post('entity_id')) {
								$value->quantity = $value->quantity + 1;
								$menukey = $key;
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
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day 
				}
			}
			else if ($this->input->post('action') == "plus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
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
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
				}
			}
			else if ($this->input->post('action') == "minus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
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
						delete_cookie('cart_restaurant');
						delete_cookie('pre_order_date');
						delete_cookie('order_mode');
					}
					else
					{
		            	$this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
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
					delete_cookie('cart_restaurant');
					delete_cookie('pre_order_date');
					delete_cookie('order_mode');
				}
				else
				{
	            	$this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
				}
			} 
			$data['cart_details'] = $this->getcookie('cart_details');
			$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
			$data['cart_details'] = $this->getCartItems($data['cart_details'],$data['cart_restaurant']);
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($data['cart_restaurant']);
			// get if a item is still added in the cart or not
			$added = 0;
			if (!empty($data['cart_details']['cart_items'])) {
				foreach ($data['cart_details']['cart_items'] as $key => $value) {
					if ($value['menu_id'] == $this->input->post('entity_id')) {
						$added = 1;
					}
				}
			}
			if ($this->input->post('is_main_cart') == "yes") {
				$cart = $this->load->view('ajax_main_cart',$data,true);
			}
			else
			{
				$store = $this->common_model->getSingleRow('restaurant', 'entity_id', $cart_restaurant);
				if($store) {
					$store_type = $this->store_type_model->getById($store->store_type_id);
					if($store_type && $store_type->name_en == 'Restaurants') {
						$cart = $this->load->view('ajax_your_cart',$data,true);
					}else {
						$cart = $this->load->view('ajax_your_cart_product',$data,true);
					}
				}
			}
			$array_view = array(
				'cart'=>$cart,
				'added'=>$added
			);
			echo json_encode($array_view);
		}
	}
	// check cart's restaurant id
	public function checkCartRestaurant(){
		$restaurant = 0;
		if (!empty($this->input->post('restaurant_id'))) {
			$cart_restaurant = get_cookie('cart_restaurant');
			if (!empty($cart_restaurant)) {
				if ($this->input->post('restaurant_id') == $cart_restaurant) {
					$restaurant = 1; // same restaurant
				}
				else
				{
					$restaurant = 0;  // another restaurant
				}
			}
			else
			{
				$restaurant = 1;
			}
		}
		echo $restaurant;
	}
	// empty the cart items
	public function emptyCart(){
		delete_cookie('cart_details');
		delete_cookie('cart_restaurant');
		// delete_cookie('pre_order_date');
		// delete_cookie('order_mode');
	}
}
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends CI_Controller {
  
	public function __construct() {
		parent::__construct();  
		$this->load->library('form_validation');
        $this->load->library('ajax_pagination'); 
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/shop_model');
		$this->load->model(ADMIN_URL.'/store_type_model');   
		$this->load->model(ADMIN_URL.'/sub_store_type_model');   
		if (empty($this->session->userdata('language_slug'))) {
			$data['lang'] = $this->common_model->getdefaultlang();
			$this->session->set_userdata('language_directory',$data['lang']->language_directory);
			$this->config->set_item('language', $data['lang']->language_directory);
			$this->session->set_userdata('language_slug',$data['lang']->language_slug);
  		} 
	}
	// get the shops
	public function index()
	{
        // $data['page_title'] = $this->lang->line('order_food').' | '.$this->lang->line('site_title');
        $data['page_title'] = 'Order | '.$this->lang->line('site_title');
		// $data['current_page'] = 'OrderFood';
		$data['current_page'] = 'Order';
		$store_type = $this->uri->segment('2');
		$page = 0; 
		$result = $this->shop_model->getShopsForOrder(1000,$page,'','','','','','','','pagination',$store_type);
		// $result = $this->shop_model->getShopsForOrder(6,$page,'','','','','','','','',$store_type);
		if (!empty($result)) {
			foreach ($result as $key => $value) {
				$ratings = $this->shop_model->getShopReview($value['MainShopID']);
				$result[$key]['ratings'] = $ratings;
			}
		}
		$countResult = $this->shop_model->getShopsForOrder(1000,$page,'','','','','','','','', $store_type);
		$data['shops'] = $result;
		$data['store_type'] = $store_type;
        $data['TotalRecord'] = count($result);
        $config = array();
        $config["base_url"] = base_url() . "shop/index";        
        $config["total_rows"] = count($countResult);
        $config["per_page"] = 1000;
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'Previous';               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
		$data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
		$data['store_type_row'] =  $this->store_type_model->getById($store_type);
		$data['sub_store_type_row'] =  $this->sub_store_type_model->getByStoreType($data['store_type_row']->entity_id);
		$data['lang'] = $this->session->userdata('language_slug');
		$this->load->view('order_food',$data);
	}
	// ajax shops
	public function ajax_shops()
	{
        // $data['page_title'] = $this->lang->line('order_food').' | '.$this->lang->line('site_title');
        $data['page_title'] = 'Order | '.$this->lang->line('site_title');
		// $data['current_page'] = 'OrderFood';
		$data['current_page'] = 'Order';
		$page = ($this->input->post('page') !="")?$this->input->post('page'):0;
		$resdishes = ($this->input->post('resdishes'))?$this->input->post('resdishes'):'';
		$latitude = ($this->input->post('latitude'))?$this->input->post('latitude'):'';
		$longitude = ($this->input->post('longitude'))?$this->input->post('longitude'):'';
		$minimum_range = ($this->input->post('minimum_range'))?$this->input->post('minimum_range'):MINIMUM_RANGE;
		$maximum_range = ($this->input->post('maximum_range'))?$this->input->post('maximum_range'):MAXIMUM_RANGE;
		$food_veg = ($this->input->post('food_veg'))?$this->input->post('food_veg'):0;
		$food_non_veg = ($this->input->post('food_non_veg'))?$this->input->post('food_non_veg'):0;
		$store_type = ($this->input->post('store_type'))?$this->input->post('store_type'):0;
		$store_filter = ($this->input->post('store_filter'))?$this->input->post('store_filter'):[];
		$result = $this->shop_model->getShopsForOrder(1000,$page,$resdishes,$latitude,$longitude,$minimum_range,$maximum_range,$food_veg,$food_non_veg,'pagination', $store_type, array_unique($store_filter));
		if (!empty($result)) {
			foreach ($result as $key => $value) {
				$ratings = $this->shop_model->getShopReview($value['MainShopID']);
				$result[$key]['ratings'] = $ratings;
			}
		}
		$countResult = $this->shop_model->getShopsForOrder(1000,$page,$resdishes,$latitude,$longitude,$minimum_range,$maximum_range,$food_veg,$food_non_veg,'', $store_type, array_unique($store_filter));
		$data['shops'] = $result;
        $data['TotalRecord'] = count($result);
        $config = array();
        $config["base_url"] = base_url() . "shop/index";        
        $config["total_rows"] = count($countResult);
        $config["per_page"] = 1000;
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'Previous';               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
        //echo '<pre>'; print_r($data); exit;
		$this->load->view('ajax_order_food',$data);
	}

	public function getDetailData() {
		$data['page_title'] = $this->lang->line('shop_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'Shop Details';
		$data['shop_details'] = array();
		$shop_slug = $this->uri->segment('3');
		if (empty($shop_slug)) {
			$shop_slug = $this->uri->segment('1');
		}
		if (!empty($shop_slug)) {
			$shop_slug = strtolower($shop_slug);
			$content_id = $this->shop_model->getContentID($shop_slug);
			$data['shop_details'] = $this->shop_model->getShopDetail($content_id->content_id);
			$data['categories_count'] = count($data['shop_details']['categories']);

			if (!empty($data['shop_details']['shop'])) {
				$ratings = $this->shop_model->getShopReview($data['shop_details']['shop'][0]['shop_id']);
				$data['shop_reviews'] = $this->shop_model->getReviewsRatings($data['shop_details']['shop'][0]['shop_id']);
				$data['shop_details']['shop'][0]['ratings'] = $ratings;
			}
			$this->session->set_userdata(array('package_id' => ''));
		} 
		$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);

		$menu_arr = array();
		if (!empty($data['cart_details']['cart_items'])) {
			foreach ($data['cart_details']['cart_items'] as $key => $value) {
				$menu_arr[] = array(
					'menu_id' => $value['menu_id'],
					'quantity' => $value['quantity'],
				);
			}
		}
		$data['menu_arr'] = $menu_arr;
		// for adding review functionality
		$total_orders = $this->shop_model->getTotalOrders($this->session->userdata('UserID'),$data['shop_details']['shop'][0]['shop_id']);
		$total_reviews = $this->shop_model->getTotalReviews($this->session->userdata('UserID'),$data['shop_details']['shop'][0]['shop_id']);
		$data['remaining_reviews'] = $total_orders - $total_reviews;
		return $data;
	}
	// get shop details
	public function shop_detail()
	{	
		$data = $this->getDetailData();
		$store_id = $data['shop_details']['shop'][0]['store_type_id'];
		$data['store_type'] = $this->store_type_model->getById($store_id);
		if($data['store_type']->name_en != "Shops" && $data['store_type']->name_en != null) {
			$this->load->view('shop_details_product',$data);
		}else {
			$this->load->view('shop_details',$data);
		}
	}

	// get ajax shop details
	public function ajax_shop_details(){ 
        $data['page_title'] = $this->lang->line('shop_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'Shop Details';
		$searchDish = array();
		if (!empty($this->input->post('searchDish'))) {
			$searchDish = explode(",", $this->input->post('searchDish'));
		}
		$data['shop_details'] = array();
		if (!empty($this->input->post('content_id'))) {
			$data['shop_details'] = $this->shop_model->getShopDetail($this->input->post('content_id'),$searchDish,$this->input->post('food'),$this->input->post('price'));
			$data['categories_count'] = count($data['shop_details']['categories']);
			if (!empty($data['shop_details']['shop'])) {
				$ratings = $this->shop_model->getShopReview($data['shop_details']['shop'][0]['shop_id']);
				$data['shop_details']['shop'][0]['ratings'] = $ratings;
			}
		} 
		$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);
		$menu_arr = array();
		if (!empty($data['cart_details']['cart_items'])) {
			foreach ($data['cart_details']['cart_items'] as $key => $value) {
				$menu_arr[] = array(
					'menu_id' => $value['menu_id'],
					'quantity' => $value['quantity'],
				);
			}
		}
		$data['menu_arr'] = $menu_arr;
		$this->load->view('ajax_shop_detail',$data);
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
	// event booking page
	public function event_booking()
	{
        $data['page_title'] = $this->lang->line('book_event').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		$page = 0; 
		$result = $this->shop_model->getAllShops(8,$page);
		if (!empty($result['data'])) {
			foreach ($result['data'] as $key => $value) {
				$ratings = $this->shop_model->getShopReview($value['shop_id']);
				$result['data'][$key]['ratings'] = $ratings;
			}
		}
		$data['shops'] = $result['data'];
		$count = count($data['shops']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url() . "shop/event-booking";        
        $config["total_rows"] = $result['count'];
        $config["per_page"] = 8;
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'Previous';               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
		$this->load->view('event_booking',$data);
	}
	// ajax events page
	public function ajax_events()
	{
        $data['page_title'] = $this->lang->line('book_event').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		$page = ($this->input->post('page') !="")?$this->input->post('page'):0;
        $result = $this->shop_model->getAllShops(8,$page,$this->input->post('searchEvent'));
		if (!empty($result['data'])) {
			foreach ($result['data'] as $key => $value) {
				$ratings = $this->shop_model->getShopReview($value['shop_id']);
				$result['data'][$key]['ratings'] = $ratings;
			}
		}
		$data['shops'] = $result['data'];
		$count = count($data['shops']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url() . "shop/event-booking";        
        $config["total_rows"] = $result['count'];
        $config["per_page"] = 8;
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'Previous';               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
		$this->load->view('ajax_events',$data);
	}
	// get shop dishes
	public function getResturantsDish(){
        $data['page_title'] = $this->lang->line('menu_details').' | '.$this->lang->line('site_title');
		$searchDish = array();
		if (!empty($this->input->post('searchDish'))) {
			$searchDish = explode(",", $this->input->post('searchDish'));
		}
		$content_id = $this->shop_model->getRestContentID($this->input->post('shop_id'));
		$data['shop_details'] = $this->shop_model->getShopDetail($content_id->content_id,$searchDish,$this->input->post('food'),$this->input->post('price'));
		$data['categories_count'] = count($data['shop_details']['categories']);
		if (!empty($data['shop_details']['shop'])) {
			$ratings = $this->shop_model->getShopReview($data['shop_details']['shop'][0]['shop_id']);
			$data['shop_details']['shop'][0]['ratings'] = $ratings;
		}
		$cart_details = get_cookie('cart_details');
		$cart_shop = get_cookie('cart_shop');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_shop);
		$menu_arr = array();
		if (!empty($data['cart_details']['cart_items'])) {
			foreach ($data['cart_details']['cart_items'] as $key => $value) {
				$menu_arr[] = array(
					'menu_id' => $value['menu_id'],
					'quantity' => $value['quantity'],
				);
			}
		}
		$data['menu_arr'] = $menu_arr;
		$this->load->view('search_menu_details',$data);
	}
	// event booking detail page
	public function event_booking_detail(){
        $data['page_title'] = $this->lang->line('menu_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		$data['shop_details'] = array();
		if (!empty($this->uri->segment('3'))) {
			$content_id = $this->shop_model->getContentID($this->uri->segment('3'));
			$data['shop_details'] = $this->shop_model->getShopDetail($content_id->content_id);
			$data['categories_count'] = count($data['shop_details']['categories']);
			if (!empty($data['shop_details']['shop'])) {
				$ratings = $this->shop_model->getShopReview($data['shop_details']['shop'][0]['shop_id']);
				$data['shop_reviews'] = $this->shop_model->getReviewsRatings($data['shop_details']['shop'][0]['shop_id']);
				$data['shop_details']['shop'][0]['ratings'] = $ratings;
			}
			$this->session->set_userdata(array('package_id' => ''));
		} 
		$this->load->view('event_booking_detail',$data); 
	}
	// checkEventAvailability
	public function checkEventAvailability(){ 
		if (!empty($this->input->post('no_of_people')) && !empty($this->input->post('booking_date')) && !empty($this->input->post('dining_time'))) {
			$booking_date = date("Y-m-d H:i:s",strtotime($this->input->post('booking_date').' '.$this->input->post('dining_time')));
			$check = $this->shop_model->getBookingAvailability($booking_date,$this->input->post('no_of_people'),$this->input->post('shop_id'));
			if ($check) {
				echo 'success';
			}
			else
			{
				echo 'fail';
			}
			exit;
		}
		else
		{
			echo 'error';
		}
	}
	// add package item to book event
	public function add_package(){
		if ($this->input->post('action') == "add") {
			$this->session->set_userdata('package_id', $this->input->post('entity_id'));
			echo 'success';
		}
		else
		{
			$this->session->set_userdata('package_id','');
			echo 'success';
		}
		exit;
	}
	// book event
	public function bookEvent(){
		if($this->input->post('booking_date') != '' && $this->input->post('no_of_people') != ''){
			$booking_date = date("Y-m-d H:i:s",strtotime($this->input->post('booking_date').' '.$this->input->post('dining_time')));
            $add_data = array(                   
                'name'=>$this->input->post('name'),
                'no_of_people'=>$this->input->post('no_of_people'),
                'booking_date'=>$booking_date,
                'shop_id'=>$this->input->post('shop_id'),
                'user_id'=>$this->input->post('user_id'),
                'package_id'=>$this->session->userdata('package_id'),
                'status'=>1,
                'created_by' => $this->input->post('user_id'),
                'event_status'=>'pending'
            ); 
            $event_id = $this->common_model->addData('event',$add_data); 
            $users = array(
                'first_name'=>$this->session->userdata('userFirstname'),
                'last_name'=>($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):''
            );
            $taxdetail = $this->shop_model->getShopTax('shop',$this->input->post('shop_id'),$flag="order");
            $package = $this->common_model->getSingleRow('shop_package','entity_id',$this->session->userdata('package_id'));
            $package_detail = '';
            if(!empty($package)){
                $package_detail = array(
                    'package_price'=>$package->price,
                    'package_name'=>$package->name,
                    'package_detail'=>$package->detail,
                    'package_image'=>$package->image,
                );
            }
            $serialize_array = array(
                'shop_detail'=>(!empty($taxdetail))?serialize($taxdetail):'',
                'user_detail'=>(!empty($users))?serialize($users):'',
                'package_detail'=>(!empty($package_detail))?serialize($package_detail):'',
                'event_id'=>$event_id
            );
            $this->common_model->addData('event_detail',$serialize_array); 
            echo 'success';  
        }
	}
	// get Favourite Resturants
	public function getFavouriteResturants(){
        $data['page_title'] = $this->lang->line('fav_shops').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		$page = 0; 
		$store_type = $this->uri->segment('2'); 
		// $result = $this->shop_model->getAllShops(6,$page);
		$result = $this->shop_model->getShopsForOrder(6,$page,'','','','','','','','pagination',$store_type);
		if (!empty($result['data'])) {
			foreach ($result['data'] as $key => $value) {
				$ratings = $this->shop_model->getShopReview($value['shop_id']);
				$result['data'][$key]['ratings'] = $ratings;
			}
		}
		$data['shops'] = $result['data'];
		$count = count($data['shops']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url() . "shop/event-booking";        
        $config["total_rows"] = $result['count'];
        $config["per_page"] = 6;
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'Previous';               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
		$this->load->view('event_booking',$data);
	}
	// get add ons
	public function getCustomAddOns(){
		$data['page_title'] = $this->lang->line('custom_addons').' | '.$this->lang->line('site_title');
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('shop_id'))) {
			$data['result'] = $this->shop_model->getMenuItem($this->input->post('entity_id'),$this->input->post('shop_id'));
			$data['currency_symbol'] = $this->common_model->getShopCurrencySymbol($this->input->post('shop_id'));
			$this->load->view('ajax_custom_items',$data); 
		}
	}
	// add review
	public function addReview(){
		$add_data = array(                   
	        'shop_id'=>$this->input->post('review_shop_id'),
	        'user_id'=>$this->input->post('review_user_id'),
	        'review'=>$this->input->post('review_text'),
	        'rating'=>$this->input->post('rating'),
	        'status'=>1,
	        'created_by' => $this->input->post('review_user_id'),
	    ); 
	    $review_id = $this->common_model->addData('review',$add_data); 
	    $this->session->set_flashdata('review_added', $this->lang->line('review_added'));
	    echo 'success';
	}
}
?>
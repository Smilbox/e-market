<?php
class Common_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
        $this->db->query("set session sql_mode=''"); 		        
    }
    //get notification count 
    public function getNotificationCount(){
        $this->db->select('order_count');
        $this->db->where('admin_id',$this->session->userdata('UserID'));
        return $this->db->get('order_notification')->first_row();
    }	 
    public function getLanguages()
    {
        $this->db->where('active',1);
        return $this->db->get_where('languages')->result();
    }    
    public function getCmsPages($language_slug,$cms_slug=NULL)
    {
        if (!empty($cms_slug)) {
            $array = array('language_slug'=>$language_slug,'status'=>1,'CMSSlug'=>$cms_slug);
        }
        else {
            $array = array('language_slug'=>$language_slug,'status'=>1);
        } 
        return $this->db->get_where('cms',$array)->result();
    }
    public function getFirstLanguages($slug){
        return $this->db->get_where('languages',array('language_slug'=>$slug))->first_row();
    }
    //get default lang
    public function getdefaultlang()
    {
        return $this->db->get_where('languages',array('language_default'=>1))->first_row();
    }
    //get item discount
    public function getItemDiscount($where){
        $this->db->where($where);
        $this->db->where('end_date >',date('Y-m-d H:i:s'));
        $result['couponAmount'] =  $this->db->get('coupon')->result_array();
        if(!empty($result['couponAmount'])){
            $res = array_column($result['couponAmount'], 'entity_id');
            $this->db->where_in('coupon_id',$res);
            $result['itemDetail'] = $this->db->get('coupon_item_map')->result_array();
        }
        return $result;
    }
    //get table data
    public function getRestaurantinSession($tblname,$UserID)
    {
        $this->db->where('created_by',$UserID);
        return $this->db->get($tblname)->result_array();
    }

    // get all the currencies
    public function getCountriesCurrency(){
        return $this->db->get('currencies')->result_array();
    }
    // get the currency id from currency name
    public function getCurrencyID($currency_name){
        return $this->db->get_where('currencies',array('currency_name'=>$currency_name))->first_row();
    }
    // get currency symbol
    public function getCurrencySymbol($currency_id) {
        return $this->db->get_where('currencies',array('currency_id'=>$currency_id))->first_row();
    }
    // get currency symbol
    public function getRestaurantCurrency($content_id) {
        return $this->db->get_where('restaurant',array('content_id'=>$content_id))->first_row();
    }
    // get currency symbol
    public function getRestaurantCurrencySymbol($restaurant_id) {
        $this->db->select('currencies.currency_symbol');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left'); 
        return $this->db->get_where('restaurant',array('entity_id'=>$restaurant_id))->first_row();
    }
    // get currency symbol
    public function getEventCurrencySymbol($entity_id) {
        $this->db->select('currencies.currency_symbol');
        $this->db->join('restaurant','event.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left'); 
        return $this->db->get_where('event',array('event.entity_id'=>$entity_id))->first_row();
    }
    /****************************************
    Function: addData, Add record in table
    $tablename: Name of table    
    $data: array of data
    *****************************************/
    public function addData($tablename,$data)
    {   
        $this->db->insert($tablename,$data);            
        return $this->db->insert_id();
    }

    /****************************************
    Function: updateData, Update records in table
    $tablename: Name of table    
    $data: array of data
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function updateData($tablename,$data,$wherefieldname,$wherefieldvalue)
    {        
        $this->db->where($wherefieldname,$wherefieldvalue);
        $this->db->update($tablename,$data);
        return $this->db->affected_rows();
    }

    /****************************************
    Function: updateData, Delete records from table
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function deleteData($tablename,$wherefieldname,$wherefieldvalue)
    {        
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->delete($tablename);        
    }

    /****************************************
    Function: getSingleRow, get first row from table in Object format using single WHERE clause
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function getSingleRow($tablename,$wherefieldname,$wherefieldvalue)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->get($tablename)->first_row();
    }

    /****************************************
    Function: getMultipleRows, get multiple row from table in Object format using single WHERE clause
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function getMultipleRows($tablename,$wherefieldname,$wherefieldvalue)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->get($tablename)->result();
    }

    /****************************************
    Function: getRowsMultipleWhere, get row from table in Object format using multiple WHERE clause
    $tablename: Name of table        
    $wherearray: where field array    
    ****************************************/
    public function getRowsMultipleWhere($tablename,$wherearray)
    {
        $this->db->where($wherearray);
        return $this->db->get($tablename)->result();
    }

    public function getSingleRowMultipleWhere($tablename,$wherearray)
    {
        $this->db->where($wherearray);
        return $this->db->get($tablename)->first_row();
    }

      /****************************************
    Function: getAllRows, get row from table in array object format 
    $tablename: Name of table        
    $wherearray: where field array    
    ****************************************/
    public function getAllRows($tablename)
    {
        return $this->db->get($tablename)->result();
    }
        /****************************************
    Function: getAllRecordArray, get row from table in array format 
    $tablename: Name of table        
    ****************************************/
    public function getAllRecordArray($tablename)
    {
        return $this->db->get($tablename)->result_array();
    }
    /****************************************
    Function: deleteInsertRecord, Delete existing records and insert new records
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    $data: array of data that need to insert
    ****************************************/
    public function deleteInsertRecord($tablename,$wherefieldname,$wherefieldvalue,$data)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        $this->db->delete($tablename);
        
        return $this->db->insert_batch($tablename,$data);
    }

    /****************************************
    Function: insertBatch, Bulk insert new records
    $tablename: Name of table        
    $data: array of data that need to insert
    ****************************************/
    public function insertBatch($tablename,$data)
    {
        return $this->db->insert_batch($tablename,$data);
    }

    /****************************************
    Function: updateBatch, Bulk update records
    $tablename: Name of table        
    $data: array of data that need to insert
    $fieldname: Field name used as WHERE Clause
    ****************************************/
    public function updateBatch($tablename,$data,$fieldname)
    {
        return $this->db->update_batch($tablename, $data, $fieldname);
    }
    
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

    public function getLang($language_slug){
        $this->db->select('language_name,language_slug');
        $this->db->where('language_slug',$language_slug);
        return $this->db->get('languages')->first_row();
    }

    public function getUsersNotification($user_id,$status=NULL)
    {
        $this->db->where('user_id',$user_id);
        if ($status == 'unread') {
            $this->db->where('view_status',0);
        }
        $this->db->order_by('datetime','desc');
        return $this->db->get('user_order_notification')->result();
    }
    
    //get menu items
    public function getMenuItem($entity_id,$restaurant_id){
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
        $couponAmount = $ItemDiscount['couponAmount'];
        $ItemDiscount = (!empty($ItemDiscount['itemDetail']))?array_column($ItemDiscount['itemDetail'], 'item_id'):array();

        $this->db->select('menu.restaurant_id,menu.is_deal,menu.entity_id as menu_id,menu.status,menu.name,menu.price,menu.menu_detail,menu.image,menu.is_veg,menu.recipe_detail,availability,c.name as category,c.entity_id as category_id,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id,add_ons_master.is_multiple,deal_category.deal_category_name,add_ons_master.deal_category_id');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
        $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
        $this->db->join('deal_category','add_ons_master.deal_category_id = deal_category.deal_category_id','left');
        $this->db->where('menu.restaurant_id',$restaurant_id);
        $this->db->where('menu.language_slug',$language_slug);
        $this->db->where('menu.entity_id',$entity_id);
        $result = $this->db->get('restaurant_menu_item as menu')->result();

        $menu = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $offer_price = '';
                if(in_array($value->menu_id,$ItemDiscount)){
                    if(!empty($couponAmount)){
                        if($couponAmount[0]['max_amount'] < $value->price){ 
                            if($couponAmount[0]['amount_type'] == 'Percentage'){
                                $offer_price = $value->price - round(($value->price * $couponAmount[0]['amount'])/100);
                            }else if($couponAmount[0]['amount_type'] == 'Amount'){
                                $offer_price = $value->price - $couponAmount[0]['amount'];
                            }
                        }
                    }
                }
                $offer_price = ($offer_price)?$offer_price:'';
                if (!isset($menu[$value->category_id])) 
                {
                    $menu[$value->category_id] = array();
                    $menu[$value->category_id]['category_id'] = $value->category_id;
                    $menu[$value->category_id]['category_name'] = $value->category;  
                }
                $image = ($value->image)?image_url.$value->image:'';
                $total = 0;
                if($value->check_add_ons == 1){
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                       $menu[$value->category_id]['items'][$value->menu_id] = array();
                       $menu[$value->category_id]['items'][$value->menu_id] = array('restaurant_id'=>$value->restaurant_id,'menu_id'=>$value->menu_id,'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'recipe_detail'=>$value->recipe_detail,'availability'=>$value->availability,'is_veg'=>$value->is_veg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
                    }
                    if($value->is_deal == 1){
                        if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id])){
                           $i = 0;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id] = array();
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['addons_category'] = $value->deal_category_name;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['addons_category_id'] = $value->deal_category_id;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['is_multiple'] = $value->is_multiple;
                        }
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name);
                        $i++;
                    }else{
                        if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id])){
                           $i = 0;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id] = array();
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
                        }
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name,'add_ons_price'=>$value->add_ons_price);
                        $i++;
                    }
                }else{
                    $menu[$value->category_id]['items'][]  = array('restaurant_id'=>$value->restaurant_id,'menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'recipe_detail'=>$value->recipe_detail,'availability'=>$value->availability,'is_veg'=>$value->is_veg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
                }
            }
        }
        $finalArray = array();
        $final = array();
        $semifinal = array();
        $new = array();
        if (!empty($menu)) {
            foreach ($menu as $nm => $va) 
            {
                $final = array();
                foreach ($va['items'] as $kk => $items) 
                {
                    if(!empty($items['addons_category_list']))
                    {
                        $semifinal = array();
                        foreach ($items['addons_category_list'] as $addons_cat_list) 
                        {
                            array_push($semifinal, $addons_cat_list);
                        }
                        $items['addons_category_list'] = $semifinal;                  
                    }
                    array_push($final, $items);
                }
                $va['items'] = $final;
                array_push($finalArray, $va);
            }
        }
        return $finalArray;     
    }
    // get Cart items
    public function getCartItems($cart_details,$cart_restaurant){
        $cartItems = array();
        $cartTotalPrice = 0;
        if (!empty($cart_details)) {
            foreach (json_decode($cart_details) as $key => $value) { 
                $details = $this->getMenuItem($value->menu_id,$cart_restaurant);
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
                    {   $subtotal = 0;
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
                    {   $subtotal = 0;
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
    
    //get country
    public function getSelectedPhoneCode(){
        $this->db->where('OptionSlug','phone_code');
        return $this->db->get('system_option')->first_row();
    }

    // get system options
    public function getSystemOptions(){
        return $this->db->get('system_option')->result_array();
    }

    //Get restaurant lat long
    public function getRestoLatLong($restaurant_id)
    {
        $this->db->select('restaurant_address.latitude, restaurant_address.longitude');
        return $this->db->get_where('restaurant_address',array('resto_entity_id'=>$restaurant_id))->first_row();
    }

    // Get latest driver know position
    public function getLatestDriverPosition() {
        // SELECT `driver_id`, `longitude`, `latitude`, MAX(created_date) AS latest_date FROM `driver_traking_map` GROUP BY driver_id ASC
        $this->db->select("map.driver_id, map.longitude, map.latitude, users.first_name, users.last_name, MAX(map.created_date) as latest_date");
        $this->db->join('users','map.driver_id = users.entity_id','left');
        $this->db->group_by("map.driver_id");
        return $this->db->get('driver_traking_map as map')->result();
    }
}
?>
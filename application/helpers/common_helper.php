<?php
// create slug based on title
function slugify($text,$tablename,$fieldname,$primaryField=NULL,$primaryValue=NULL)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);
  // trim
  $text = trim($text, '-');
  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);
  // lowercase
  $text = strtolower($text);
  if (empty($text)) {
    return 'n-a';
  }
  $i = 1; 
  $baseSlug = $text;
  while(slug_exist($text,$tablename,$fieldname,$primaryField,$primaryValue))
  {
    $text = $baseSlug . "-" . $i++;        
  }
  return $text;
}
function slug_exist($text,$tablename,$fieldname,$primaryField,$primaryValue)
{ 
  //check slug is uniquee or not.
  $CI =& get_instance();
  $where = array(
    $fieldname=>$text
  );
  $array = $where;
  if (!empty($primaryField) && !empty($primaryValue)) {
    $whereA = array(
      $primaryField.'!='=>$primaryValue
    );
    $array = array_merge($where,$whereA);
  }
  $checkSlug = $CI->db->get_where($tablename,$array)->num_rows(); 
  if($checkSlug > 0)
  {
    return true;
  }
}
function getUserTypeList($lang_slug){
    if($lang_slug == "bn"){
        $usertype = array(
            'Admin' =>'অ্যাডমিন', 
            'User' =>'ব্যবহারকারী',
        );
    }
    else if($lang_slug == "fr"){
        $usertype = array(
            'Admin' =>'Admin', 
            'User' =>'Utilisateur',
        );
    }
    else if($lang_slug == "ar"){
        $usertype = array(
            'Admin' =>'مشرف', 
            'User' =>'المستعمل',
        );
    }
    else
    {
        $usertype = array(
            'Admin' =>'Admin', 
            'User' =>'User',
        );
    }

  return $usertype;
}
function generateEmailBody($body,$arrayVal){
  // replace # email body variable's
  if($arrayVal['FirstName']==""){
    $arrayVal['FirstName'] = 'Unknown';
  }  
  $CI =& get_instance();
  if($CI->session->userdata('CompanyName'))
  {
    $body = str_replace("#Company_Name#",$CI->session->userdata('CompanyName'),$body);  
  }
  else
  {
    $body = str_replace("#Company_Name#",$CI->session->userdata('site_title'),$body);  
  }

  $body = str_replace("#firstname#",$arrayVal['FirstName'],$body);  
  $body = str_replace("#lastname#",$arrayVal['LastName'],$body);
  $body = str_replace("#s_firstname#",$arrayVal['SFirstName'],$body);  
  $body = str_replace("#s_lastname#",$arrayVal['SLastName'],$body);
  $body = str_replace("#forgotlink#",$arrayVal['ForgotPasswordLink'],$body);  
  $body = str_replace("#email#",$arrayVal['Email'],$body);  
  $body = str_replace("#password#",$arrayVal['Password'],$body);  
  $body = str_replace("#s_email#",$arrayVal['Sender_Email'],$body);
  $body = str_replace("#s_utype#",$arrayVal['Sender_Utype'],$body);
  $body = str_replace("#Site_Name#",$arrayVal['Site_Name'],$body);
  $body = str_replace("#loginlink#",$arrayVal['LoginLink'],$body);
  $body = str_replace("#restaurant#",$arrayVal['restaurant_name'],$body);
  $body = str_replace("#status#",$arrayVal['Status'],$body);
  $body = str_replace("#message#",$arrayVal['Message'],$body);
  $body = str_replace("#number#",$arrayVal['Number'],$body);
  $body = str_replace("#password#",$arrayVal['Password'],$body);
  $body = str_replace("#question#",$arrayVal['Question'],$body);
  $body = str_replace("#order_id#",$arrayVal['Order_ID'],$body);
  $body = str_replace("#year#",date('Y'),$body);
  return $body;
}
function event_status($lang_slug){
  if($lang_slug == "bn"){
        $event_status = array(
            'pending'=>'বিচারাধীন',
            /*'onGoing'=>'চলছে',
            'completed'=>'নিষ্কৃত',*/
            'cancel'=>'বাতিল',
            'paid'=>'পেইড'
        );
    }
    else if($lang_slug == "fr"){
        $event_status = array(
            'pending'=>'En attente',
            /*'onGoing'=>'En cours',
            'completed'=>'Livré',*/
            'cancel'=>'Annuler',
            'paid'=>'Payé'
        );
    }
    else if($lang_slug == "ar"){
        $event_status = array(
            'pending'=>'قيد الانتظار',
            /*'onGoing'=>'جاري التنفيذ',
            'completed'=>'تم التوصيل',*/
            'cancel'=>'إلغاء',
            'paid'=>'دفع'
        );
    }
    else
    {
        $event_status = array(
            'pending'=>'Pending',
            /*'onGoing'=>'On Going',
            'completed'=>'Delivered',*/
            'cancel'=>'Cancel',
            'paid'=>'Paid'
        );
    }
  return $event_status;
}
function order_status($lang_slug){
    if($lang_slug == "bn"){
        $order_status = array(
            'placed'=>'স্থাপিত',
            'preparing'=>'প্রস্তুতি',
            'delivered'=>'নিষ্কৃত',
            'onGoing'=>'চলছে',
            'complete'=>'সম্পূর্ণ',
            'cancel'=>'বাতিল'
        );
    }
    else if($lang_slug == "fr"){
        $order_status = array(
            'placed'=>'Mis',
            'preparing'=>'En train de préparer',
            'delivered'=>'Livré',
            'onGoing'=>'En cours',
            'complete'=>'Achevée',
            'cancel'=>'Annuler'
        );
    }
    else if($lang_slug == "ar"){
        $order_status = array(
            'placed'=>'وضعت',
            'preparing'=>'خطة',
            'delivered'=>'تم التوصيل',
            'onGoing'=>'جاري التنفيذ',
            'complete'=>'اكتمال',
            'cancel'=>'إلغاء'
        );
    }
    else
    {
        $order_status = array(
            'placed'=>'Placed',
            'preparing'=>'Preparing',
            'delivered'=>'Delivered',
            'onGoing'=>'On Going',
            'complete'=>'Complete',
            'cancel'=>'Cancel'
        );
    }
  return $order_status;
}
function number_format_unchanged_precision($number, $currency_code=NULL, $dec_point=',', $thousands_sep='.'){
  if (!empty($currency_code) && $currency_code == "EUR") {
    if($dec_point==$thousands_sep){
      trigger_error('2 parameters for ' . METHOD . '() have the same value, that is "' . $dec_point . '" for $dec_point and $thousands_sep', E_USER_WARNING);
    }
    if(preg_match('{\.\d+}', $number, $matches)===1){
      $decimals = strlen($matches[0]) - 1;
    }else{
      $decimals = 0;
    }
    return number_format($number, $decimals, $dec_point, $thousands_sep);
  }
  else
  {
    return number_format($number, 2);
  }
}
//coupon type
function coupon_type(){
  return array(
    'free_delivery'=>'Free Delivery',
    'discount_on_items'=>'Discount on Items',
    'discount_on_cart'=>'Discount on Cart Items',
    'discount_on_combo'=>'Discount on combo deal',
    'user_registration'=>'User Registration',
  );
}
?>
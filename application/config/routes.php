<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( BASEPATH .'database/DB.php' );

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = 'error404';
$route[ADMIN_URL] = ADMIN_URL.'/home';
$route['translate_uri_dashes'] = TRUE;
$route['privacy-policy'] = 'cms/privacy_policy';
$route['about-us'] = 'cms/about_us';
$route['how-to-order'] = 'cms/how_to_order';
$route['legal-notice'] = 'cms/legal_notice';
$route['terms-and-conditions'] = 'cms/terms_and_conditions';

// Custom shop routes

$db =& DB();
$shop_query = $db->select('shop_slug')->get( 'shop' );
$result = $shop_query->result();
foreach( $result as $res )
{
    $route[$res->shop_slug] = 'shop/shop-detail/'.$res->shop_slug;
}

// Custom store type routes

$route['shop'] = '/';
$route['mobile.php'] = '/';
$route['shops'] = 'home/shops';

$store_query = $db->select('entity_id')->get('store_type');
$result = $store_query->result();
foreach($result as $res)
{
    $key = "order/".$res->entity_id;
    $route[$key] = 'shop/index/'.$res->entity_id;
} 

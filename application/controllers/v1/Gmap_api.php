<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(-1);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Gmap_api extends REST_Controller {
  
	public function __construct() {
		parent::__construct();    
		$this->load->model('v1/gmap_api_model');   
		$this->load->model(ADMIN_URL.'/common_model'); 
    }

    public function geocode_post() 
    {

		$search = $this->post('search');
		$quarters = QUARTERS;
		$q_target = '';
		$geocode = [];
		foreach($quarters as $key => $q)
		{
			$pattern = "/".$q."/i";
			if(preg_match($pattern, $search)) {
				$q_target = $q; 
				break;
			}
		}

		if($q_target != '')
		{
			$res = $this->gmap_api_model->getUserAddressGeocode($q_target);
			if($res != null)
			{
				$response_all = array(
					"latitude" => $res->latitude,
					"longitude" => $res->longitude,
				);
	
				return $this->response(['results' => $response_all], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code 
			}

			$q_target = $q_target.", Antananarivo, Madagascar";
		
		} else {

			if(!preg_match("/Antananarivo/i", $search))
			{
				$q_target = $search.", Antananarivo, Madagascar";
			}
		}
		$q_target=urlencode($q_target);
		$url = "https://api.openrouteservice.org/geocode/search?api_key=".OPEN_ROUTE_KEY."&text=".$q_target."&boundary.gid=whosonfirst:region:85673965&boundary.country=MG&layers=neighbourhood";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			'Accept: application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8'
		));
		$response = curl_exec($ch);
		curl_close($ch);
		$response_all = json_decode($response);

		$target = null;
		if($response_all && !empty($response_all->features))
		{
			foreach($response_all->features as $key => $val)
			{
				if($val->properties && preg_match("/([a-zA-Z0-9]+)\,[\s]+Antananarivo, Madagascar/i", $val->properties->label))
				{
					$latitude = $val->geometry->coordinates[1];
					$longitude = $val->geometry->coordinates[0];
					
					$target = array(
						"latitude" => $latitude,
						"longitude" => $longitude,
					);

					$data = array(
						"user_entity_id" => 1,
						"address" => $val->properties->label,
						"latitude" => $latitude,
						"longitude" => $longitude,
						"zipCode" => 101,
						"city" => "Antananarivo"
					);

					$this->common_model->addData('user_address', $data);
				break;
				}
			}
		}

       $this->response(['results' => $target], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code 
	}

	public function reversegeocode_post()
	{
		$latitude = $this->post('latitude');
		$longitude = $this->post('longitude');

		$url = "https://api.openrouteservice.org/geocode/reverse?api_key=".OPEN_ROUTE_KEY."&point.lon=".$longitude."&point.lat=".$latitude;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			'Accept: application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8'
		));
		$response = curl_exec($ch);
		curl_close($ch);
		$response_all = json_decode($response);

		$res = [];
		if($response_all)
		{
			$features = $response_all->features;
			foreach($features as $k => $f)
			{
				$res[$k] = $f->properties->label;
			}
		}

		$this->response(['results' => $res], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code 
	}
	
	public function storegeocode_post()
	{
		$address = $this->post('address');
		$latitude = $this->post('latitude');
		$longitude = $this->post('longitude');

		$data = array(
			"user_entity_id" => 1,
			"address" => $address,
			"latitude" => $latitude,
			"longitude" => $longitude,
			"zipCode" => 101,
			"city" => "Antananarivo"
		);

		return $this->common_model->addData('user_address', $data);
	}

}   
?>
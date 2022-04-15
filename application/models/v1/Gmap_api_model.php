<?php
class Gmap_api_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    }

    public function getUserAddressGeocode($quarter)
    {
        $this->db->select("address, latitude, longitude");
        $where = "address like '%".$quarter."%'";
        $this->db->where($where);
        $value =  $this->db->get('user_address')->first_row();
        
        return $value;
    }

    public function getDistance($originLong, $originLat, $destinationLong, $destinationLat)
    {
        $data = '{"locations":[['.$originLong.','.$originLat.'],['.$destinationLong.','.$destinationLat.']],"metrics":["distance"],"units":"km"}';

        $url = "https://api.openrouteservice.org/v2/matrix/driving-car";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
            'Accept: application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
            'Authorization: '.OPEN_ROUTE_KEY
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);
        $response_all = json_decode($response);

        $distance = null;
        if($response_all && $response_all->distances)
        {
            $distance = $response_all->distances[0][1];
        }

        return $distance;
    }
}
?>
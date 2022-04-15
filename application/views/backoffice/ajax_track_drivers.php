<?php if (!empty($this->session->userdata('UserID')) && !empty($drivers_position)) {?>
									<div class="track-order-main">
										<div class="track-order-map">
											<div class="row">
								                <div class="col-md-12 modal_body_map">
								                    <div class="location-map" id="location-map">
								                        <div id="map_canvas" style="min-height: 700px;"></div>
								                    </div>
								                </div>
								            </div>
										</div>
									</div>
								<?php } else if (!empty($this->session->userdata('UserID')) && empty($drivers_position)) {?>
									<h2>Hey there!</h2>
									<p>No latest drivers position found to track!</p>
								<?php } else {?>
									<h2>Hey there!</h2>
									<p>Please login to track latest drivers position!</p>
								<?php }?>

<script type="text/javascript">
	initMap();
    function initMap(){
        map = new google.maps.Map(document.getElementById('map_canvas'),
        {
            center: {
              lat: 20.055,
              lng: 20.968
            },
            zoom: 5
        });
		var directionsService = new google.maps.DirectionsService;
        var infowindow = new google.maps.InfoWindow();
        //var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsDisplay = new google.maps.DirectionsRenderer({
		    polylineOptions: {
		      strokeColor: "#FFB300"
		    }
		  });
        directionsDisplay.setOptions( { suppressMarkers: true } );
        directionsDisplay.setMap(map);

        var bounds = new google.maps.LatLngBounds();
		var waypoints = Array();
		
        <?php 
		if(!empty($drivers_position)) {
			foreach($drivers_position as $driver_pos) {
				if (!empty($driver_pos->latitude) && !empty($driver_pos->longitude)): ?>
					// driver location
					var position = {lat: <?php echo $driver_pos->latitude; ?>,lng: <?php echo $driver_pos->longitude; ?>};
	        		var icon = '<?php echo base_url(); ?>'+'assets/front/images/driver.png';
					marker = new google.maps.Marker({
						position: position,
						map: map,
						animation: google.maps.Animation.DROP,
						icon: icon
					});
					google.maps.event.addListener(marker, 'click', (function(marker, i) {
					return function() {
						infowindow.setContent('<?php echo $driver_pos->first_name . " " . $driver_pos->last_name; ?>');
						infowindow.open(map, marker);
						}
					})(marker));
					bounds.extend(marker.position);
					waypoints.push({
						location: marker.position,
						stopover: true
					});
				<?php endif ?>
			<?php }
		} ?>
	        
        
        map.fitBounds(bounds);
    }
</script>
<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php $this->load->view('header');?>


<section class="inner-pages-section">
	<div class="container">
		<div class="row" id="track_order_content">
			<?php if ($this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('UserID')) && !empty($latestOrder)) {?>
				<div class="col-lg-12">
					<div class="heading-title">
						<h2><?php echo $this->lang->line('track_order') ?></h2>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="track-order-main">
						<div class="track-order-map">
							<!-- <img src="<?php //echo base_url();?>assets/front/images/map.jpg"> -->
							<div class="row">
			                    <div class="col-md-12 modal_body_map">
			                        <div class="location-map" id="location-map">
			                            <div id="map_canvas"></div>
			                        </div>
			                    </div>
			                </div>
						</div>
						<div class="track-order-content">
							<div class="track-order-text">
								<div class="track-order-head">
									<h2><?php echo $this->lang->line('hey') ?> <?php echo $this->session->userdata('userFirstname') . ' ' . $this->session->userdata('userLastname'); ?>!</h2>
									<p><?php echo $this->lang->line('order_msg') ?></p>
									<!-- <p><?php //echo ($latestOrder->status == 1)?'Your order has been accepted!':''; ?></p> -->
								</div>
								<div class="order-id-details">
									<div class="order-id">
										<strong><?php echo $this->lang->line('orderid') ?> : #<?php echo $latestOrder->master_order_id; ?></strong>
									</div>
									<div class="details-id">
										<div class="details-id-content">
											<div class="details-id-text">
												<p><?php echo ($latestOrder->driver_id) ? $latestOrder->first_name . $this->lang->line('order_msg2') : $this->lang->line('order_msg3'); ?></p>
												<div class="detail-list">
													<i class="iicon-icon-34"></i>
													<label><?php echo $this->lang->line('delivery_address') ?></label>
													<p><?php echo $latestOrder->user_address; ?> </p>
												</div>
												<div class="detail-list">
													<i class="iicon-icon-33"></i>
													<label><?php echo $this->lang->line('cash_to_collect') ?></label>
													<p><?php echo $latestOrder->currency_symbol; ?> <?php echo $latestOrder->total_rate; ?></p>
												</div>
											</div>
											<div class="details-id-img">
					                            <?php $image = ($latestOrder->image) ? ($latestOrder->image) : (default_img);?>
					                            <img src="<?php echo $image; ?>">
											</div>
										</div>
										<?php if ($latestOrder->driver_id) {?>
											<div class="call-btn">
												<button class="btn"><i class="iicon-icon-12"></i><?php echo $this->lang->line('call') .' '. $latestOrder->first_name.' '; ?><br><?php echo $latestOrder->mobile_number; ?></button>
											</div>
										<?php }?>
									</div>
								</div>
							</div>
							<div class="order-status-main">
								<div class="order-status-title">
									<h4><?php echo $this->lang->line('order_status') ?></h4>
								</div>
								<div class="order-status-box">
									<div class="status-step-box">
										<?php $active = ($latestOrder->placed) ? "active" : "";?>
										<div class="status-step <?php echo $active; ?>">
											<div class="status-step-img">
												<div class="step-img">
													<img src="<?php echo base_url(); ?>assets/front/images/order-placed.svg">
												</div>
											</div>
											<div class="status-step-name">
												<label><?php echo $this->lang->line('order_placed') ?></label>
												<p><?php echo ($latestOrder->placed) ? date("d M Y G:i A", strtotime($latestOrder->placed)) : ''; ?></p>
											</div>
										</div>
										<?php $active = isset($latestOrder->accepted_by_shop) ? "active" : "";?>
										<div class="status-step <?php echo $active; ?>">
											<div class="status-step-img">
												<div class="step-img">
													<img src="<?php echo base_url(); ?>assets/front/images/accepted-by-shop.png">
												</div>
											</div>
											<div class="status-step-name">
												<label><?php echo $this->lang->line('order_accepted') ?></label>
												<p><?php echo isset($latestOrder->accepted_by_shop) ? date("d M Y G:i A", strtotime($latestOrder->accepted_by_shop)) : ''; ?></p>
											</div>
										</div>
										<?php $active = ($latestOrder->preparing) ? "active" : "";?>
										<div class="status-step <?php echo $active; ?>">
											<div class="status-step-img">
												<div class="step-img">
													<img src="<?php echo base_url(); ?>assets/front/images/preparing.svg">
												</div>
											</div>
											<div class="status-step-name">
												<label><?php echo $this->lang->line('preparing') ?></label>
												<p><?php echo ($latestOrder->preparing) ? date("d M Y G:i A", strtotime($latestOrder->preparing)) : ''; ?></p>
											</div>
										</div>
										<?php $active = ($latestOrder->onGoing) ? "active" : "";?>
										<div class="status-step <?php echo $active; ?>">
											<div class="status-step-img">
												<div class="step-img">
													<img src="<?php echo base_url(); ?>assets/front/images/on-the-way.svg">
												</div>
											</div>
											<div class="status-step-name">
												<label><?php echo $this->lang->line('on_the_way') ?></label>
												<p><?php echo ($latestOrder->onGoing) ? date("d M Y G:i A", strtotime($latestOrder->onGoing)) : ''; ?></p>
											</div>
										</div>
										<?php $active = ($latestOrder->delivered) ? "active" : "";?>
										<div class="status-step <?php echo $active; ?>">
											<div class="status-step-img">
												<div class="step-img">
													<img src="<?php echo base_url(); ?>assets/front/images/order-delivered.svg">
												</div>
											</div>
											<div class="status-step-name">
												<label><?php echo $this->lang->line('order_delivered') ?></label>
												<p><?php echo ($latestOrder->delivered) ? date("d M Y G:i A", strtotime($latestOrder->delivered)) : ''; ?></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } else if ($this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('UserID')) && empty($latestOrder)) {?>
				<div class="col-lg-12">
					<div class="track-order-text">
						<div class="track-order-head">
							<h2><?php echo $this->lang->line('hey_there') ?></h2>
							<p><?php echo $this->lang->line('no_latest_order') ?></p>
						</div>
					</div>
				</div>
			<?php } else {?>
				<div class="col-lg-12">
					<div class="track-order-text">
						<div class="track-order-head">
							<h2><?php echo $this->lang->line('hey_there') ?></h2>
							<p><?php echo $this->lang->line('login_to_track') ?></p>
						</div>
					</div>
				</div>
			<?php }?>
		</div>

	</div>
</section>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GMAP_API_KEY ?>&libraries=places"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    initMap();
    function initMap(){
        map = new google.maps.Map(document.getElementById('map_canvas'),
        {
            center: {
              lat: 20.055,
              lng: 20.968
            },
            zoom: 2
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
        <?php if (!empty($latestOrder->user_latitude) && !empty($latestOrder->user_longitude)): ?>
	        //users location
	        var position = {lat: <?php echo $latestOrder->user_latitude; ?>,lng: <?php echo $latestOrder->user_longitude; ?>};
	        var icon = '<?php echo base_url(); ?>'+'assets/front/images/user-home.png';
	        marker = new google.maps.Marker({
	            position: position,
	            map: map,
				animation: google.maps.Animation.DROP,
				icon: icon
	        });
	        google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infowindow.setContent("<?php echo $latestOrder->user_first_name . "<br>" . $latestOrder->user_address; ?>");
					infowindow.open(map, marker);
				}
			})(marker));
	        bounds.extend(marker.position);
	        waypoints.push({
	            location: marker.position,
	            stopover: true
	        });
        <?php endif ?>

        <?php if (!empty($latestOrder->resLat) && !empty($latestOrder->resLong)): ?>
	        // shop location
	        var position = {lat: <?php echo $latestOrder->resLat; ?>,lng: <?php echo $latestOrder->resLong; ?>};
	        var icon = '<?php echo base_url(); ?>'+'assets/front/images/shop.png';
	        marker = new google.maps.Marker({
	            position: position,
	            map: map,
				animation: google.maps.Animation.DROP,
				icon: icon
	        });
	        google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infowindow.setContent("<?php echo $latestOrder->name . "<br>" . $latestOrder->address; ?>");
					infowindow.open(map, marker);
				}
			})(marker));
	        bounds.extend(marker.position);
	        waypoints.push({
	            location: marker.position,
	            stopover: true
	        });
        <?php endif ?>

        <?php if (!empty($latestOrder->latitude) && !empty($latestOrder->longitude)): ?>
	        // driver location
	        var position = {lat: <?php echo $latestOrder->latitude; ?>,lng: <?php echo $latestOrder->longitude; ?>};
	        var icon = '<?php echo base_url(); ?>'+'assets/front/images/driver.png';
	        marker = new google.maps.Marker({
	            position: position,
	            map: map,
				animation: google.maps.Animation.DROP,
				icon: icon
	        });
	        bounds.extend(marker.position);
	        waypoints.push({
	            location: marker.position,
	            stopover: true
	        });
        <?php endif ?>
        
        map.fitBounds(bounds);
        var locationCount = waypoints.length;
        if(locationCount > 0) {
            var start = waypoints[0].location;
            var end = waypoints[locationCount-1].location;
	        directionsService.route({
				origin: start,
				destination: end,
				waypoints: waypoints,
				optimizeWaypoints: true,
				travelMode: google.maps.TravelMode.DRIVING
			}, function(response, status) {
			if (status === 'OK') {
				directionsDisplay.setDirections(response);
			} else {
				window.alert('Problem in showing direction due to ' + status);
			}
			});
        }
    }

    var i = setInterval(function(){
    	var order_id = '<?php echo $order_id; ?>';
		jQuery.ajax({
			type : "POST",
			dataType : "html",
			async: false,
			url : '<?php echo base_url().'order/ajax_track_order'?>',
			data : {"order_id":order_id},
			success: function(response) {
				$('#track_order_content').html(response);
			},
				error: function(XMLHttpRequest, textStatus, errorThrown) {           
			}
		});
    },10000);  

});
</script>
<?php $this->load->view('footer');?>

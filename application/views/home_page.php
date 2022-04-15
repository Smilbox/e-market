<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>

<section class="home-banner">
	<div class="container">
		<div class="your-doorstep">
			<h1><?php echo $this->lang->line('always_at_door'); ?></h1>
			<p><?php echo $this->lang->line('order_fav_rest'); ?></p>
			<form id="home_search_form" class="search-form">
				<div class="form-group">
					<input type="text" name="address" id="address" onFocus="geolocate('')" placeholder = "<?php echo $this->lang->line('enter_address'); ?>" value="">
					<input type="button" name="Search" value="<?php echo $this->lang->line('search'); ?>" class="btn" onclick="fillInAddress('home_page')">
				</div>
			</form>
		</div>
	</div>
</section>

<section class="quick-searches">
	<div class="container">
		<div class="heading-title">
			<h2><?php echo $this->lang->line('quick_search'); ?></h2>
			<div class="slider-arrow">
				<div id="customNav" class="arrow"></div>
			</div>
		</div>
		<div class="quick-searches-slider owl-carousel">
			<?php if (!empty($categories)) {
				foreach ($categories as $key => $value) { ?>
					<div class="quick-searches-box" onclick="quickSearch(<?php echo $value->entity_id; ?>)">
						<img src="<?php echo ($value->image)?base_url().'uploads/'.$value->image:default_img;?>" alt="Chinese">
						<h5><?php echo $value->name ?></h5>
					</div>					
				<?php }
			} ?>				
		</div>
	</div>
</section>
<?php if (!empty($coupons)) { ?>
	<section class="best-offers">
		<div class="container">
			<div class="heading-title">
				<h2><?php echo $this->lang->line('latest_coupons'); ?></h2>
					<div class="slider-arrow">
						<div id="customNav2" class="arrow"></div>
					</div>
			</div>
			<div class="best-offers-slider owl-carousel">
				<?php foreach ($coupons as $key => $value) { ?>
					<div class="best-offers-box">
						<img src="<?php echo ($value->image)?base_url().'uploads/'.$value->image:default_img;?>" alt="Coupon" >
					</div>
				<?php } ?>
			</div>
		</div>
	</section>
<?php } ?>

<div id="popular-restaurants">
	<section class="popular-restaurants">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="heading-title">
						<h2><?php echo $this->lang->line('nearby_restaurants') ?></h2>
						<?php if (!empty($restaurants)) {
						    if(count($restaurants) > 9){ ?>
						        <a href="<?php echo base_url() . 'restaurant'; ?>"><div class="view-all btn"> <?php echo $this->lang->line('view_all'); ?></div> </a>
						    <?php }
						}?>
					</div>
				</div>
			</div>
			<div class="row rest-box-row">
				<?php if (!empty($restaurants)) {
				foreach ($restaurants as $key => $value) { 
					if ($key <= 8) { ?>
						<div class="col-sm-12 col-md-6 col-lg-3">
							<div class="popular-rest-box">
								<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>">
									<div class="popular-rest-img">
										<img src="<?php echo ($value['image'])?$value['image']:default_img;?>" alt="<?php echo $value['name']; ?>">
										<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
										<div class="openclose-btn">
												<a href="javascript:void(0)" class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </a>
												<!-- <?php //echo $value['timings']['closing']; ?> -->
											</div>
									</div>
									<div class="popular-rest-content">
										<h3><?php echo $value['name']; ?></h3>
										<div class="popular-rest-text">
											<p class="address-icon"><?php echo $value['address']; ?> </p>
										</div>
									</div>
								</a>
							</div>
						</div>
					<?php } 
				}
				} else { ?>
					<div class="row">
						<div class="col-lg-12">
							<div><h6><?php echo $this->lang->line('no_such_res_found') ?></h6></div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</section>
</div>

<section class="restaurant-app">
	<div class="container">	
		<div class="restaurant-app-content">
			<div class="row">
				<div class="col-md-6 col-sm-12">
					<div class="restaurant-app-img wow pulse">
						<img src="<?php echo base_url();?>assets/front/images/restaurant-app.png" alt="Restaurant app">
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="restaurant-app-text">
						<div class="heading-title-02">
							<h4><?php echo $this->lang->line('welcome_to') ?> <br><span><?php echo $this->lang->line('site_title'); ?> <?php echo $this->lang->line('res_app') ?></span></h4>
						</div>	
						<p><?php echo $this->lang->line('home_text1') ?></p>
						<div class="app-download">
							<a href="https://play.google.com/store/apps/details?id=com.eatance"><img src="<?php echo base_url();?>assets/front/images/google-play.png" alt="Google play"></a>
							<a href="https://apps.apple.com/us/app/eatance/id1456080440?ls=1"><img src="<?php echo base_url();?>assets/front/images/app-store.png" alt="App store"></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GMAP_API_KEY ?>&libraries=places"></script>
<script>
$(document).on('ready', function() { 
	initAutocomplete('address');
	// auto detect location if even searched once.
	if (SEARCHED_LAT == '' && SEARCHED_LONG == '' && SEARCHED_ADDRESS == '') {
		getLocation('home_page');
	}
	else
	{
		getSearchedLocation(SEARCHED_LAT,SEARCHED_LONG,SEARCHED_ADDRESS,'home_page');
	}

	$(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	});		
});
</script>
<?php $this->load->view('footer'); ?>
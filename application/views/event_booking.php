<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>

<section class="inner-banner event-booking-banner">
	<div class="container">
		<div class="inner-pages-banner">
			<h1><?php echo $this->lang->line('book_venue') ?></h1>
			<form id="event_search_form" class="inner-pages-form">
				<div class="form-group search-restaurant">
					<input type="text" name="searchEvent" id="searchEvent" placeholder="Search your Restaurant" value="">
					<input type="button" name="Search" value="<?php echo $this->lang->line('search'); ?>" class="btn" onclick="searchEvents()">
				</div>
			</form>
		</div>
	</div>
</section>

<section class="inner-pages-section order-food-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('book_restaurant') ?></h2>
				</div>
			</div>
		</div>
		<div class="row rest-box-row" id="sort_events">
			<?php if (!empty($restaurants)) {
				foreach ($restaurants as $key => $value) { ?>
					<div class="col-sm-12 col-md-6 col-lg-4">
						<div class="popular-rest-box">
							<a href="<?php echo base_url().'restaurant/event-booking-detail/'.$value['restaurant_slug'];?>">
								<div class="popular-rest-img">
									<img src="<?php echo ($value['image'])?$value['image']:default_img;?>" alt="<?php echo $value['name']; ?>">
									<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> 
									<div class="openclose-btn">
										<a href="javascript:void(0)" class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"><?php echo $value['timings']['closing']; ?></a>
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
			} ?>
			<div class="col-sm-12 col-md-12 col-lg-12">
				<div class="pagination" id="#pagination"><?php echo $PaginationLinks; ?></div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
// pagination function
function getData(page=0, noRecordDisplay=''){
	var searchEvent = $('#searchEvent').val();
	var page = page ? page : 0;
	$.ajax({
		url: "<?php echo base_url().'restaurant/ajax_events'; ?>/"+page,
		data: {'searchEvent':searchEvent,'page':page},
		type: "POST",
		success: function(result){
			$('#sort_events').html(result);
			$('html, body').animate({
		        scrollTop: $("#order-food-section").offset().top
		    }, 800);
		}
	});
}
</script>
<?php $this->load->view('footer'); ?>
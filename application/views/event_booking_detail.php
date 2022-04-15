<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datepicker/css/datepicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/plugins/clockface/css/clockface.css"/>

<section class="inner-banner booking-detail-banner">
	<div class="container">
		<div class="inner-pages-banner">
			
		</div>
	</div>
</section>

<section class="inner-pages-section rest-detail-section">
	<div class="rest-detail-main">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="rest-detail">
						<div class="rest-detail-img-main">
							<div class="rest-detail-img">
								<img src="<?php echo ($restaurant_details['restaurant'][0]['image'])?$restaurant_details['restaurant'][0]['image']:default_img;?>">
							</div>
						</div>
						<div class="rest-detail-content">
							<h2><?php echo $restaurant_details['restaurant'][0]['name']; ?> </h2>
							<p><i class="iicon-icon-20"></i><?php echo $restaurant_details['restaurant'][0]['address']; ?></p>
							<ul>
								<li><i class="iicon-icon-05"></i><?php echo ($restaurant_details['restaurant'][0]['ratings'] > 0)?$restaurant_details['restaurant'][0]['ratings']:'<strong class="newres">NEW</strong>'; ?></li>
								<li><i class="iicon-icon-18"></i><?php echo $restaurant_details['restaurant'][0]['timings']['open'].'-'.$restaurant_details['restaurant'][0]['timings']['close']; ?></li>
								<li><i class="iicon-icon-19"></i><?php echo $restaurant_details['restaurant'][0]['phone_number']; ?></li>
							</ul>
							<?php $closed = ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'closed':''; ?>
							<a href="#" class="openclose <?php echo $closed; ?>"><?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('select_package') ?></h2>
				</div>
			</div>
		</div>
		<div class="row restaurant-detail-row">	
			<div class="col-sm-12 col-md-5 col-lg-8">					
				<div class="detail-list-box-main">
					<!-- <div class="detail-list-title">
						<h3>Gold Packages</h3>
					</div> -->
					<div class="detail-list-box">
						<?php if (!empty($restaurant_details['packages'])) { 
							foreach ($restaurant_details['packages'] as $key => $value) { ?>
								<div class="detail-list">
									<div class="detail-list-img">
										<div class="list-img">	
											<img src="<?php echo ($value['image'])?$value['image']:default_img; ?>">
										</div>
									</div>
									<div class="detail-list-content"> 
										<div class="detail-list-text">
											<h4><?php echo $value['name']; ?></h4>
											<p><?php echo $value['detail']; ?></p>
											<strong>$<?php echo $value['price']; ?></strong>
										</div>
										<div class="add-btn">
											<div class="addpackage btn" id="addpackage-<?php echo $value['entity_id']; ?>" onclick="AddPackage('<?php echo $value['entity_id']; ?>')"><?php echo $this->lang->line('add') ?></div>
										</div>
									</div>
								</div>
							<?php } ?>
						<?php } 
						else { ?>
							<div class="detail-list-title">
								<h3 class="no-results"><?php echo $this->lang->line('no_results_found') ?></h3>
							</div>
						<?php }?>
					</div>
				</div>
				<div class="detail-list-box-main">
					<div class="detail-list-title">
						<h3><?php echo $this->lang->line('ratings_reviews') ?></h3>
					</div>
					<div class="rating-review-main">
						<div class="review-progress">
							<div class="progress-main">
								<div class="review-all"><p class="text-center"><?php echo (!empty($restaurant_reviews))?count($restaurant_reviews):0; ?> <?php echo (!empty($restaurant_reviews))?((count($restaurant_reviews) > 1)?$this->lang->line('reviews'):$this->lang->line('review')):$this->lang->line('review'); ?></p></div>
								<?php for ($i=5; $i > 0 ; $i--) { ?>
									<div class="progress-box">
										<span class="star-icon"><?php echo $i; ?></span>
										<div class="progress">		
											<?php 
											$noOfReviews = $this->restaurant_model->getReviewsNumber($restaurant_details['restaurant'][0]['restaurant_id'],$i);
											$percentage = $noOfReviews * 100 / count($restaurant_reviews); ?>					
											<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percentage.'%'; ?>">									
										  </div>								  
										</div> 
										<span><?php echo $noOfReviews; ?></span>
									</div>
								<?php } ?>
							</div>	
						</div>	
						<div class="rate-restaurant">	
							<div class="star-rating-main">					
								<div class="star-rating">
									<?php for ($i=1; $i < 6; $i++) { 
										$activeClass = ''; 
										if ($i <= $restaurant_details['restaurant'][0]['ratings']) {
										 	$activeClass = 'active'; ?>
										<?php } ?>
										<button class="<?php echo $activeClass; ?>"><i class="iicon-icon-28"></i></button>
									<?php } ?>
								</div>
								<div class="review-all">
									<span><i class="iicon-icon-05"></i><?php echo $restaurant_details['restaurant'][0]['ratings']; ?></span>
								</div>
							</div>								
						</div>
					</div>
					<div class="review-box-main">
						<div id="limited-reviews">
							<?php if (!empty($restaurant_reviews)) {
								foreach ($restaurant_reviews as $key => $value) { 
									if ($key <= 4) { ?>
										<div class="review-list">
											<div class="review-img">
												<div class="user-images">
													<img src="<?php echo ($value['image'])?$value['image']:default_img; ?>">
												</div>
											</div>
											<div class="review-content">
												<div class="user-name-date">
													<h3><?php echo $value['first_name'].' '.$value['last_name']; ?></h3>
													<div class="review-star">
														<span><i class="iicon-icon-05"></i><?php echo number_format($value['rating'],1); ?></span>
													</div>
													<div class="review-date">
														<span><?php echo date("d M Y",strtotime($value['created_date'])); ?></span>
													</div>
												</div>
												<p>"<?php echo ucfirst($value['review']); ?>"</p>
											</div>
										</div>
								<?php }
								}
							} ?>
						</div>
						<div id="all_reviews" class="display-no" >
							<?php if (!empty($restaurant_reviews)) {
								foreach ($restaurant_reviews as $key => $value) {
									if ($key > 4) { ?>
										<div class="review-list">
											<div class="review-img">
												<div class="user-images">
													<img src="<?php echo ($value['image'])?$value['image']:default_img; ?>">
												</div>
											</div>
											<div class="review-content">
												<div class="user-name-date">
													<h3><?php echo $value['first_name'].' '.$value['last_name']; ?></h3>
													<div class="review-star">
														<span><i class="iicon-icon-05"></i><?php echo number_format($value['rating'],1); ?></span>
													</div>
													<div class="review-date">
														<span><?php echo date("d M Y",strtotime($value['created_date'])); ?></span>
													</div>
												</div>
												<p>"<?php echo ucfirst($value['review']); ?>"</p>
											</div>
										</div>
								<?php }
								}
							} ?>
						</div>
						<?php if (count($restaurant_reviews) > 4) { ?>
							<button id="review_button" class="btn btn-success danger-btn" onclick="showAllReviews()"><?php echo $this->lang->line('all_reviews') ?></button>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-5 col-lg-4">
				<div class="your-booking-main">
					<div class="your-booking-title">
						<h3><i class="iicon-icon-27"></i><?php echo $this->lang->line('your_booking') ?></h3>
					</div>
					<form id="check_event_availability" name="check_event_availability" method="post" class="form-horizontal float-form">
						<input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>">
						<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->session->userdata('UserID'); ?>">
						<input type="hidden" name="name" id="name" value="<?php echo $this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'); ?>">
						<div class="booking-option-main">
							<div class="booking-option how-many-people">
								<div class="booking-option-cont">
									<div class="option-img">
										<img src="<?php echo base_url();?>assets/front/images/avatar-man.png">
									</div>
									<div class="booking-option-text">
										<span><?php echo $this->lang->line('how_many_people') ?></span>
										<span id="peepid"><strong>1 <?php echo $this->lang->line('people') ?></strong></span>
									</div>
								</div>
								<div class="add-cart-item">
									<div class="number">
										<span class="minus variant"><i class="iicon-icon-22"></i></span>
										
										<input type="text" name="no_of_people" id="no_of_people" value="1" onkeyup="getPeople(this.value)">
										<span class="plus variant"><i class="iicon-icon-21"></i></span>
									</div>
								</div>
							</div>
							<div class="booking-option dining-time">
								<div class="booking-option-cont">
									<div class="option-img">
										<img src="<?php echo base_url();?>assets/front/images/dining-time.png">
									</div>
									<div class="booking-option-text">
										<span><?php echo $this->lang->line('dining_time') ?></span>
										<input type="text" name="dining_time" id="dining_time" class="form-control clockface_1" readonly="" placeholder="Dining Time" value="<?php echo date("H:m"); ?>" />
									</div>
								</div>
								<div class="add-cart-item">
									
								</div>
							</div>
							<div class="booking-option pick-date">
								<div class="booking-option-cont">
									<div class="option-img">
										<img src="<?php echo base_url();?>assets/front/images/pick-date.png">
									</div>
									<div class="booking-option-text">
										<span><?php echo $this->lang->line('pick_date') ?></span>
										<input type="text" class="form-control date-picker" readonly name="booking_date" id="booking_date"  placeholder="Pick a Date" value="<?php echo date("d-M-Y"); ?>">
									</div>
								</div>
								<div class="add-cart-item">
									
								</div>
							</div>
							<div class="continue-btn">
                                <button type="submit" name="submit_page" id="submit_page" value="Check Availability" class="btn btn-success danger-btn"><?php echo $this->lang->line('check_avail') ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section><!--/ end content-area section -->

<!-- booking_availability -->
<div class="modal modal-main" id="booking-available">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_availability') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-availability.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('booking_available') ?></h2>
      		<?php if (!empty($this->session->userdata('UserID')) && ($this->session->userdata('is_user_login') == 1)) { ?>
      			<p><?php echo $this->lang->line('proceed_further') ?></p>
      			<button class="btn" data-dismiss="modal" data-toggle="modal" onclick="confirmBooking()"><?php echo $this->lang->line('confirm') ?></button>
      			<button class="btn" data-dismiss="modal" data-toggle="modal"><?php echo $this->lang->line('cancel') ?></button>
      		<?php } 
      		else { ?>
      			<p><?php echo $this->lang->line('please') ?> <a href="<?php echo base_url();?>home/login"><u><?php echo $this->lang->line('title_login') ?></u></a> <?php echo $this->lang->line('book_avail_text') ?></p>
      		<?php }?>
      		
      	</div>
      </div>
    </div>
  </div>
</div>

<!-- Booking Not Availability -->
<div class="modal modal-main" id="booking-not-available">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_availability') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-availability.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('booking_not_available') ?></h2>
      		<p><?php echo $this->lang->line('no_bookings_avail') ?></p>
      		<button class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel') ?></button>
      	</div>
      </div>
    </div>
  </div>
</div>

<!-- Booking Confirmation -->
<div class="modal modal-main" id="booking-confirmation">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_confirmation') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-confirmation.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('booking_confirmed_text1') ?></h2>
      		<p><?php echo $this->lang->line('booking_confirmed_text2') ?></p>
      		<a href="<?php echo base_url().'myprofile/view-my-bookings'; ?>" class="btn"><?php echo $this->lang->line('view_bookings') ?></a>
      	</div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script src="<?php echo base_url();?>assets/front/js/scripts/admin-management-front.js"></script>
<script type="text/javascript">
$(document).ready(function(){   
  var dt = new Date();
  var time = dt.getHours() + ":" + dt.getMinutes();
    $('#dining_time').clockface({
        format: 'HH:mm',
        trigger: 'manual'
    });
    $('.clockface_1').click(function (e) {
        e.stopPropagation();
        $(this).clockface('toggle');
    });
    var date = new Date();
    $('.date-picker').datepicker({
      format: "dd-M-yyyy",
        startDate:date
    });
});
</script>
<?php $this->load->view('footer'); ?>

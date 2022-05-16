<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>

<section class="home-banner">
	<div class="container">
	<div class="your-doorstep">
			<h1><?php echo $this->lang->line('delivery_everything') ?></h1>
			<p><?php //echo $this->lang->line('order_fav_rest'); ?></p>
			<form id="home_search_form" class="search-form">
				<div class="form-group">
					<input type="text" name="address" id="address" onFocus="geolocate('')" placeholder = "<?php echo $this->lang->line('enter_address'); ?>" value="">
					<input type="button" name="Search" value="<?php echo $this->lang->line('search'); ?>" class="btn" onclick="fillInAddress('home_page')">
				</div>
			</form>
		</div>
	</div>
</section>

<div id="choose-service">
	<section class="popular-restaurants">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="heading-title">
						<h2><?php echo $this->lang->line('choose_store_type') ?></h2>
					</div>
				</div>
			</div>
			<div class="row rest-box-row-store">
				<?php if (!empty($store_types)) {
				foreach ($store_types as $key => $value) { 
					//if ($key <= 7) { ?>
						<div class="col-sm-12 col-md-6 col-lg-3">
							<div class="popular-rest-box">
								<a href="<?php echo $value->link ?>">
									<div class="popular-rest-img store-type-box">
									<div class="features-item">
										
                            			<span><img class="<?php echo strtolower($value->name_en) ?>" src="<?php echo base_url();?>uploads/<?php echo $value->icon ?>" alt="icon"></span>
										<h3><?php echo ($this->lang->line('current_lang') == 'en' ? $value->name_en : $value->name_fr); ?></h3>
                        			</div>
									</div>
								</a>
							</div>
						</div>
					<?php //} 
				}
				} else { ?>
					
				<?php } ?>
			</div>
		</div>
	</section>
</div>

<?php /*
if(!empty($store_type_variables)) {
    foreach($store_type_variables as $typeName => $typeId) {
        ?>
        <div id="popular-restaurants-<?php echo $typeId?>">
            <section class="popular-restaurants">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="heading-title">
								<?php $title = $this->lang->line('current_lang') == "fr" ? $typeName.' '.$this->lang->line('nearby') : $this->lang->line('nearby').' '.$typeName; ?>
                                <h2><?php echo $title ?></h2>
                                <?php if (!empty($restaurants)) {
                                    if(/*count($restaurants) > 9 false){ ?>
                                        <a href="<?php echo base_url() . 'restaurant'; ?>"><div class="view-all btn"> <?php echo $this->lang->line('view_all'); ?></div> </a>
                                    <?php }
                                }?>
                            </div>
                        </div>
                    </div>
                    <div class="row rest-box-row main-rest-box-row">
                    </div>
                </div>
            </section>
        </div>
        <?php
    }
} */
?>

<!--    <div id="popular-restaurants">-->
<!--        <section class="popular-restaurants">-->
<!--            <div class="container">-->
<!--                <div class="row">-->
<!--                    <div class="col-lg-12">-->
<!--                        <div class="heading-title">-->
<!--                            <h2>--><?php //echo $this->lang->line('nearby_store') ?><!--</h2>-->
<!--                            --><?php //if (!empty($restaurants)) {
//                                if(/*count($restaurants) > 9*/ false){ ?>
<!--                                    <a href="--><?php //echo base_url() . 'restaurant'; ?><!--"><div class="view-all btn"> --><?php //echo $this->lang->line('view_all'); ?><!--</div> </a>-->
<!--                                --><?php //}
//                            }?>
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="row rest-box-row main-rest-box-row">-->
<!--                    --><?php //if (!empty($restaurants)) {
//                        foreach ($restaurants as $key => $value) {
//                            $featured_image = $value('featured_image') ? $value['featured_image'] : ($value('image') ? $value['image']: default_img);
//                            if ($key <= 8) { ?>
<!--                                <div class="col-sm-12 col-md-6 col-lg-4">-->
<!--                                    <div class="popular-rest-box">-->
<!--                                        <a href="--><?php //echo base_url().'restaurant/restaurant-detail/'.$value['shop_slug'];?><!--">-->
<!--                                            <div class="popular-rest-img">-->
<!--                                                <img style="object-fit:--><?php //echo $value['object_fit'] ?><!--" src="--><?php //echo $featured_image ?><!--" alt="--><?php //echo $value['name']; ?><!--">-->
<!--                                                <div class="middle">-->
<!--                                                    --><?php //if(!empty($value['image'])) { ?>
<!--                                                        <img  style="object-fit:--><?php //echo $value['object_fit'] ?><!--" src="--><?php //echo ($value['image'])?$value['image']:default_img;?><!--" alt="--><?php //echo $value['name']; ?><!--">-->
<!--                                                    --><?php //} ?>
<!--                                                </div>-->
<!--                                                --><?php //echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
<!--                                                <div class="openclose-btn">-->
<!--                                                    <a href="javascript:void(0)" class="openclose --><?php //echo ($value['timings']['closing'] == "Closed")?"closed":""; ?><!--"> --><?php //echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?><!-- </a>-->
<!--                                                    --><?php ////echo $value['timings']['closing']; ?><!-- -->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <div class="popular-rest-content">-->
<!--                                                <h3>--><?php //echo $value['name']; ?><!--</h3>-->
<!--                                                <div class="popular-rest-text">-->
<!--                                                    <p class="address-icon">--><?php //echo $value['address']; ?><!-- </p>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </a>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            --><?php //}
//                        }
//                    } else { ?>
<!--                        <div class="row">-->
<!--                            <div class="col-lg-12">-->
<!--                                <div><h6>--><?php //echo $this->lang->line('no_such_res_found') ?><!--</h6></div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    --><?php //} ?>
<!--                </div>-->
<!--            </div>-->
<!--        </section>-->
<!--    </div>-->


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
							<h4><?php echo $this->lang->line('download-app') ?></span></h4>
						</div>	
						<p><?php echo $this->lang->line('home_text1') ?></p>
						<div class="app-download">
							<a href="https://play.google.com/store/apps/details?id=com.esakafo.app"><img src="<?php echo base_url();?>assets/front/images/google-play.png" title="<?php echo $this->lang->line('download-app') ?>" alt="Google play"></a>
							<a href="https://apps.apple.com/us/app/e-sakafo/id1546248347"><img src="<?php echo base_url();?>assets/front/images/app-store.png" title="<?php echo $this->lang->line('download-app') ?>" alt="App store"></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-main" id="update-app">
		  <div class="modal-dialog modal-dialog-centered">
		    <div class="modal-content">
		      <!-- Modal Header -->
		      <div class="modal-header">
		        <h4 class="modal-title"><?php echo $this->lang->line('welcome') ?></h4>
		        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
		      </div>

		      <!-- Modal body -->
		      <div class="modal-body">
		      	<div class="availability-popup">
		      		<div class="availability-images">
		      			<img src="<?php echo base_url();?>assets/front/images/delivery.png" alt="Booking availability">
		      		</div>
		      		<h2><?php echo $this->lang->line('go-to-guide') ?></h2>
		      	</div>
		      </div>
		    </div>
		  </div>
</div>


<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GMAP_API_KEY ?>&libraries=places"></script>
<script>
$(document).on('ready', function() {
	$('#promotionModal').modal('show'); 
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
	
	$('.carousel').carousel({
  		interval: 5000
	})

	$('#close-promote').on('click', function() {
		$('#popup-content').attr('style','display: none;');
	});
});
</script>
<?php $this->load->view('footer'); ?>
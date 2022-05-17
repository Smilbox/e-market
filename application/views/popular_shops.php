<?php
$langKey = 'nearby_store';
//!empty($storeTypeId) && $langKey = "{$langKey}_{$storeTypeId}";
$nearbyTitle = !empty($storeType) && ($this->lang->line('current_lang') == "en") ? $this->lang->line('nearby').' '.$storeType->name_en : $storeType->name_fr.' '.$this->lang->line('nearby');
?>
<section class="popular-shops">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
                    <h2><?php echo $nearbyTitle ?></h2>
                    <?php if (!empty($nearbyShops)) {
					    if(count($nearbyShops) > 5){ ?>
					        <a href="<?php echo base_url() . 'order/'.$storeType->entity_id; ?>"><div class="view-all btn"> <?php echo $this->lang->line('view_all'); ?></div> </a>
					    <?php }
					}?>
				</div>
			</div>
		</div>
		<div class="row rest-box-row main-rest-box-row">
			<?php if (!empty($nearbyShops)) {
				foreach ($nearbyShops as $key => $value) {
                    $featured_image = !empty($value['featured_image']) ? $value['featured_image'] : (!empty($value['image']) ? $value['image']: default_img);
                    if($key > 5) {
                        break;
                    } ?>
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="popular-rest-box">
                            <a href="<?php echo base_url().'shop/shop-detail/'.$value['shop_slug'];?>">
                                <div class="popular-rest-img">
                                    <img style="object-fit:<?php echo $value['object_fit'] ?>" src="<?php echo $featured_image;?>" alt="<?php echo $value['name']; ?>">
                                    <div class="middle">
                                        <?php if(!empty($value['image'])) { ?>
                                            <img  style="object-fit:<?php echo $value['object_fit'] ?>" src="<?php echo ($value['image'])?$value['image']:default_img;?>" alt="<?php echo $value['name']; ?>">
                                        <?php } ?>
                                    </div>
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
				<?php  }
			} else { ?>
				<div class="">
					<div class="col-lg-12">
						<div><h6 class="h6-title"><?php echo $this->lang->line('no_such_res_found') ?></h6></div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
<?php if (!empty($shops)) {
	foreach ($shops as $key => $value) { ?>
		<div class="col-lg-6">
			<div class="shop-box">
				<div class="popular-rest-box">
					<div class="popular-rest-img">
						<a href="<?php echo base_url().'shop/shop-detail/'.$value['shop_slug'];?>">
						<img  style="object-fit:<?php echo $value['object_fit'] ?>" src="<?php echo ($value['featured_image'])?$value['featured_image']:default_img;?>" alt="<?php echo $value['name']; ?>">
						<div class="middle">
							<?php if(!empty($value['image'])) { ?>
							<img  style="object-fit:<?php echo $value['object_fit'] ?>" src="<?php echo ($value['image'])?$value['image']:default_img;?>" alt="<?php echo $value['name']; ?>">
						    <?php } ?>
						</div>
						</a>
						<a href="javascript:void(0)" class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?></a>
						<!-- <?php //echo $value['timings']['closing']; ?> -->
						<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> 
						
					</div>
					<div class="popular-rest-content">
						<h3><?php echo $value['name']; ?></h3>
						<div class="popular-rest-text">
							<p class="address-icon"><?php echo $value['address']; ?> </p>	
							<div class="order-btn">
							<a href="<?php echo base_url().'shop/shop-detail/'.$value['shop_slug'];?>" class="btn"><?php echo ($value['timings']['closing'] == "Closed" ? $this->lang->line('pre-order') : $this->lang->line('order') )  ?></a>
						</div>					
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="col-sm-12 col-md-12 col-lg-12">
		<div class="pagination" id="#pagination"><?php echo $PaginationLinks; ?></div>
	</div>
<?php } 
else { ?>
	<div class="no-found"><h4><?php echo $this->lang->line('no_res_found'); ?></h4></div>
<?php } ?>
<?php if (!empty($recipies)) {
	foreach ($recipies as $key => $value) { ?>
		<div class="col-sm-12 col-md-6 col-lg-3">
			<div class="popular-rest-box">
				<a href="<?php echo base_url().'recipe/recipe-detail/'.$value['item_slug'];?>">
					<div class="popular-rest-img">
						<img src="<?php echo ($value['image'])?$value['image']:default_img; ?>" alt="<?php echo $value['name']; ?>">
					</div>
					<div class="popular-rest-content type-food-option">
						<div class="detail-list <?php echo ($value['is_veg'] == 1)?'veg':'non-veg'; ?>"><h3><?php echo $value['name']; ?></h3></div>
					</div>
				</a>
			</div>
		</div>
	<?php } ?>
	<div class="col-sm-12 col-md-12 col-lg-12">
		<div class="pagination" id="#pagination"><?php echo $PaginationLinks; ?></div>
	</div>
<?php } 
else { ?>
	<div class="col-sm-12 col-md-6 col-lg-4">
		<h5><?php echo $this->lang->line('no_recipe_found') ?></h5>
	</div>
<?php }?>
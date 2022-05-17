<?php $menu_ids = array();
if (!empty($menu_arr)) {
	$menu_ids = array_column($menu_arr, 'menu_id');
}
if (!empty($shop_details['menu_items']) || !empty($shop_details['packages']) || !empty($shop_details['categories']))
{
	if (!empty($shop_details['categories'])) {?>
		<div class="slider-checkbox-main">
			<div class="pn-ProductNav_Wrapper wrapper">
			 	<button id="pnAdvancerLeft" class="pn-Advancer pn-Advancer_Left scroller-left" type="button"><i class="iicon-icon-16"></i></button>
					<nav id="pnProductNav" class="pn-ProductNav">
					    <div id="pnProductNavContents" class="pn-ProductNav_Contents list">
				  			<?php foreach ($shop_details['categories'] as $key => $value) {?>
				   				<div class="slider-checkbox" aria-selected="true">
						    		<label>
					    			<input class="check-menu" type="checkbox" name="checkbox-option" id="checkbox-option-<?php echo $value['category_id']; ?>" onclick="menuSearch(<?php echo $value['category_id']; ?>)">
					    			<span><?php echo $value['name']; ?></span>
						    		</label>
						    	</div>
				   			<?php }?>
							<span id="pnIndicator" class="pn-ProductNav_Indicator"></span>
					    </div>
					</nav>
				<button id="pnAdvancerRight" class="pn-Advancer pn-Advancer_Right scroller-right" type="button"><i class="iicon-icon-17"></i></button>
			</div>
		</div>
	<?php }?>
	<div class="option-filter-tab">
		<div class="custom-control custom-checkbox">  
			<input type="radio" name="filter_food" class="custom-control-input" id="filter_veg" value="filter_veg" onclick="menuFilter(<?php echo $shop_details['shop'][0]['content_id']; ?>)">
			<label class="custom-control-label" for="filter_veg"><?php echo $this->lang->line('veg') ?></label>
		</div>
		<div class="custom-control custom-checkbox">
			<input type="radio" name="filter_food" class="custom-control-input" id="filter_non_veg" value="filter_non_veg" onclick="menuFilter(<?php echo $shop_details['shop'][0]['content_id']; ?>)">
			<label class="custom-control-label" for="filter_non_veg"><?php echo $this->lang->line('non_veg') ?></label>
		</div>
		<div class="custom-control custom-checkbox">
			<input type="radio" checked="checked" name="filter_food" class="custom-control-input" id="all" value="all" onclick="menuFilter(<?php echo $shop_details['shop'][0]['content_id']; ?>)">
			<label class="custom-control-label" for="all"><?php echo $this->lang->line('view_all') ?></label>
		</div>
		<div class="custom-control custom-checkbox">
		    <input type="radio" checked="checked" name="filter_price" class="custom-control-input" id="filter_high_price" value="filter_high_price" onclick="menuFilter(<?php echo $shop_details['shop'][0]['content_id']; ?>)">
		    <label class="custom-control-label" for="filter_high_price"><?php echo $this->lang->line('sort_by_price_low') ?></label>
	  	</div>
		<div class="custom-control custom-checkbox">
			<input type="radio" name="filter_price" class="custom-control-input" id="filter_low_price" value="filter_low_price" onclick="menuFilter(<?php echo $shop_details['shop'][0]['content_id']; ?>)">
			<label class="custom-control-label" for="filter_low_price"><?php echo $this->lang->line('sort_by_price_high') ?></label>
		</div>
	</div>
	<div class="is_close fix-content">
			<?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed") ? 
				'<div>'.$this->lang->line('pre-order-text').'</div><br/><button class="btn" style="margin-bottom: 10px" id="pre-order-btn" onClick="openPreOrderDateModal()">'.$this->lang->line('pre-order').'</button>' 
				: ''; ?>
				<div id="pre-order-error" class="hide" style="color: red"><?php echo $this->lang->line('pre-order-error')?></div>				
				<span id="delivery_text_date" class="hide">
					<h5><span class="hide" id="delivery_val"></span><span id="delivery_info"></span></h5>					
				<span>
	</div>
	<div id="res_detail_content">
		<?php if (!empty($shop_details['menu_items'])) {
	        $popular_count = 0;
	        foreach ($shop_details['menu_items'] as $key => $value) {
	            if ($value['popular_item'] == 1) {
	                $popular_count = $popular_count + 1;
	            }
	        }
	        if ($popular_count > 0) { ?>
				<div class="detail-list-box-main">
					<div class="detail-list-title">
						<h3><?php echo $this->lang->line('popular_items') ?></h3>
					</div>
					<?php foreach ($shop_details['menu_items'] as $key => $value) {
						if ($value['popular_item'] == 1) { ?>
							<div class="detail-list-box">
							 	<div class="detail-list">
									<div class="detail-list-img">
										<div class="list-img">
											<img src="<?php echo ($value['image']) ? $value['image'] : default_img; ?>">
											<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div>
										</div>
									</div>
									<div class="detail-list-content">
										<div class="detail-list-text">
											<h4><?php echo $value['name']; ?></h4>
											<p><?php echo $value['menu_detail']; ?></p>
											<strong><?php echo ($value['check_add_ons'] != 1)?$shop_details['shop'][0]['currency_symbol'].' '.$value['price']:''; ?></strong>
										</div>
										<?php if ($value['check_add_ons'] == 1) {?>
											<div class="add-btn">
												<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
												<button class="btn pre-order <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" <?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed")?'disabled':''; ?>  onclick="checkCartShop(<?php echo $value['entity_id']; ?>,<?php echo $shop_details['shop'][0]['shop_id']; ?>,'addons',this.id)"> <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
												<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
											</div>
										<?php } else {?>
											<div class="add-btn">
												<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
												<button class="btn pre-order <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartShop(<?php echo $value['entity_id']; ?>,<?php echo $shop_details['shop'][0]['shop_id']; ?>,'',this.id)" <?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> > <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
											</div>
										<?php }?>
									</div>
								</div>
							</div>
						<?php }
					}?>
				</div>
			<?php }?>
		<?php }?>
		<?php if (!empty($shop_details['categories'])) {
	        foreach ($shop_details['categories'] as $key => $value) { ?>
				<div class="detail-list-box-main categories" id="category-<?php echo $value['category_id']; ?>" >
					<div class="detail-list-title">
						<h3><?php echo $value['name']; ?></h3>
					</div>
					<div class="detail-list-box type-food-option">
						<?php if ($shop_details[$value['name']]) {
    						foreach ($shop_details[$value['name']] as $key => $mvalue) {?>
								<div class="detail-list <?php echo ($mvalue['is_under_20_kg'] == 1) ? 'veg' : 'non-veg'; ?>">
									<div class="detail-list-content">
										<div class="detail-list-text">
											<h4><?php echo $mvalue['name']; ?></h4>
											<p><?php echo $mvalue['menu_detail']; ?></p>
											<strong><?php echo ($mvalue['check_add_ons'] != 1)?$shop_details['shop'][0]['currency_symbol'].' '.$mvalue['price']:''; ?></strong>
										</div>
										<?php if ($mvalue['check_add_ons'] == 1) {?>
											<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
											<div class="add-btn">
												<button class="btn pre-order <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" <?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> onclick="checkCartShop(<?php echo $mvalue['entity_id']; ?>,<?php echo $shop_details['shop'][0]['shop_id']; ?>,'addons',this.id)"> <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
												<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
											</div>
										<?php } else {?>
											<div class="add-btn">
											<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
												<button class="btn pre-order <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartShop(<?php echo $mvalue['entity_id']; ?>,<?php echo $shop_details['shop'][0]['shop_id']; ?>,'',this.id)" <?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> > <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
											</div>
										<?php }?>
									</div>
								</div>
							<?php }
						}?>
					</div>
				</div>
			<?php }
		} ?>
	</div>
<?php } 
else {?>
<div class="slider-checkbox-main">
	<div class="detail-list-title">
		<h3><?php echo $this->lang->line('no_results_found') ?></h3>
	</div>
</div>
<?php }?>

<script type="text/javascript">
	menuFilter(<?php echo $shop_details['shop'][0]['content_id']; ?>);
</script>
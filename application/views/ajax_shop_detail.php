<?php $menu_ids = array();
if (!empty($menu_arr)) {
	$menu_ids = array_column($menu_arr, 'menu_id');
}
if (!empty($shop_details['menu_items']) && !empty($shop_details['categories'])) {
	if (!empty($shop_details['menu_items'])) {
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
										<div class="add-btn" id="cart_item_<?php echo $value['entity_id']; ?>">
											<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
											<button class="btn pre-order <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" <?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed")?'disabled':''; ?>  onclick="checkCartShop(<?php echo $value['entity_id']; ?>,<?php echo $shop_details['shop'][0]['shop_id']; ?>,'addons',this.id)"> <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?> </button>
											<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
										</div>
									<?php } else {?>
										<div class="add-btn" id="cart_item_<?php echo $value['entity_id']; ?>">
											<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
											<button class="btn pre-order <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartShop(<?php echo $value['entity_id']; ?>,<?php echo $shop_details['shop'][0]['shop_id']; ?>,'',this.id)" <?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> > <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?> </button>
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
										<div class="add-btn" id="cart_item_<?php echo $mvalue['entity_id']; ?>">
											<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
											<button class="btn pre-order <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" <?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed")?'disabled':''; ?>  onclick="checkCartShop(<?php echo $mvalue['entity_id']; ?>,<?php echo $shop_details['shop'][0]['shop_id']; ?>,'addons',this.id)"> <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?> </button>
											<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
										</div>
									<?php } else {?>
										<div class="add-btn" id="cart_item_<?php echo $mvalue['entity_id']; ?>">
											<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
											<button class="btn pre-order <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartShop(<?php echo $mvalue['entity_id']; ?>,<?php echo $shop_details['shop'][0]['shop_id']; ?>,'',this.id)" <?php echo ($shop_details['shop'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> > <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?> </button>
										</div>
									<?php }?>
								</div>
							</div>
						<?php }
					}?>
				</div>
			</div>
		<?php }
	} 
}
else
{ ?>
	<div><?php echo $this->lang->line('no_items_found') ?></div>
<?php } ?>
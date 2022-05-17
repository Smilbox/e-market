<div class="your-cart-main">
					<div class="your-cart-title">
						<h3><i class="iicon-icon-02"></i><?php echo $this->lang->line('your_cart') ?></h3>
						<h6><?php echo count($cart_details['cart_items']); ?> <?php echo $this->lang->line('items') ?></h6>
					</div>
					<?php if (!empty($cart_details['cart_items'])) { ?>
					<div class="add-cart-list-main type-food-option">
					    <?php foreach ($cart_details['cart_items'] as $cart_key => $value) { ?>
							<div class="add-cart-list">
								<div class="cart-list-content <?php echo ($value['is_under_20_kg'] == 1) ? 'veg' : 'non-veg'; ?>">
									<h5><?php echo $value['name']; ?></h5>
									<ul class="ul-disc">
										<?php if (!empty($value['addons_category_list'])) {
        									foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
												<li><h6><?php echo $cat_value['addons_category']; ?></h6></li>
												<ul class="ul-cir">
												<?php if (!empty($cat_value['addons_list'])) {
                									foreach ($cat_value['addons_list'] as $key => $add_value) {?>
														<li><?php echo $add_value['add_ons_name']; ?>  <?php echo $shop_details['shop'][0]['currency_symbol']; ?> <?php echo $add_value['add_ons_price']; ?></li>
													<?php }
    											}?>
												</ul>
											<?php }
    									}?>
									</ul>
									
								</div>
								<div class="add-cart-item">
									<strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $value['totalPrice']; ?></strong>
									<div class="number">
										<span class="minus" id="minusQuantity" onclick="customItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['shop_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
										<input type="text" value="<?php echo $value['quantity']; ?>" class="pointer-none" />
										<span class="plus" id="plusQuantity" onclick="customItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['shop_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
									</div>
								</div>
							</div>
						<?php }?>
					</div>
					<div class="cart-subtotal">
						<strong><?php echo $this->lang->line('sub_total') ?></strong>
						<strong class="price"><?php echo $currency_symbol->currency_symbol; ?> <?php echo $cart_details['cart_total_price']; ?></strong>
					</div>
					<div class="continue-btn" id="btn-continue">
					</div>			
					<?php } else { ?>
						<div class="cart-empty text-center">
						<img src="<?php echo base_url();?>assets/front/images/empty-cart-product.png">
							<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_item') ?></h6>
						</div>	
					<?php } ?>		
				</div>
<script type="text/javascript">
	var count = '<?php echo count($cart_details['cart_items']); ?>'; 
	$('#cart_count').html(count);
</script>


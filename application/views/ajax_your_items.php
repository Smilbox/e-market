<?php if (!empty($cart_details['cart_items'])) { ?>
    <div class="card-header" id="headingOne">
        <div class="card-header-title" data-toggle="collapse" data-target="#collapseOne">
        	<img src="<?php echo base_url();?>assets/front/images/picnic-basket.svg">
        	<h3><?php echo $this->lang->line('your_items') ?></h3>
    	</div>	
    </div>
    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExampleOne">
        <div class="card-body">
        	<div class="cart-content-table">
                <table>
					<tbody>
						<?php if (!empty($cart_details['cart_items'])) {
							foreach ($cart_details['cart_items'] as $cart_key => $value) { ?>
								<tr>
									<td class="item-img-main"><div><i class="iicon-icon-15 <?php echo ($value['is_under_20_kg'] == 1)?'veg':'non-veg'; ?>"></i></div></td>
									<td class="item-name">
										<?php echo $value['name']; ?>
										<ul class="ul-disc">
											<?php if (!empty($value['addons_category_list'])) {
												foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
													<li><h6><?php echo $cat_value['addons_category']; ?></h6></li>
													<ul class="ul-cir">
													<?php if (!empty($cat_value['addons_list'])) {
														foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
															<li><?php echo $add_value['add_ons_name']; ?> <?php echo $currency_symbol->currency_symbol; ?> <?php echo $add_value['add_ons_price']; ?></li>
														<?php }
													} ?>
													</ul>
												<?php }
											} ?>
										</ul>
									</td>
									<td><strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $value['totalPrice']; ?></strong></td>
									<td>
										<div class="add-cart-item">
											<div class="number">
												<input type="hidden" name="total_cart_items" id="total_cart_items" value="<?php echo count($cart_details['cart_items']); ?>">
												<span class="minus" id="minusQuantity" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['shop_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
												<input type="text" name="item_count_check" id="item_count_check" value="<?php echo $value['quantity']; ?>" class="pointer-none" />
												<span class="plus" id="plusQuantity" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['shop_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
											</div>
										</div>
									</td>
									<td class="close-btn-cart"><button class="close-btn" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['shop_id']; ?>,'remove',<?php echo $cart_key; ?>)"><i class="iicon-icon-38"></i></button></td>
								</tr>
							<?php }
						} 
						else
						{ ?>
							<div class="cart-empty text-center">
								<img src="<?php echo base_url();?>assets/front/images/empty-cart-product.png">
								<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_products') ?></h6>
							</div>	
						<?php }?>
					</tbody>
				</table>
			</div>
        </div>
    </div>
<?php } else { ?>
    <div class="card-header" id="headingOne">
        <div class="card-header-title" data-toggle="collapse" data-target="#collapseOne">
        	<img src="<?php echo base_url();?>assets/front/images/picnic-basket.svg">
        	<h3><?php echo $this->lang->line('your_items') ?></h3>
    	</div>	
    </div>
    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExampleOne">
        <div class="card-body">
        	<div class="cart-content-table">
                <table>
					<tbody>
						<div class="cart-empty text-center" >
							<img src="<?php echo base_url();?>assets/front/images/empty-cart-product.png">
							<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_products') ?></h6>
						</div>	
					</tbody>
				</table>
			</div>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
	var count = '<?php echo count($cart_details['cart_items']); ?>'; 
	$('#cart_count').html(count);
</script>
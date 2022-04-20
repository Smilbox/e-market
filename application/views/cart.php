<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>
<section class="inner-pages-section cart-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('cart') ?></h2>
				</div>
			</div>
		</div>
		<div class="row cart-row" id="your_main_cart">
			<?php if (!empty($cart_details['cart_items'])) { ?>
				<div class="col-lg-8">
					<div class="cart-content">
						<div class="your-item-title">
							<h3><?php echo $this->lang->line('your_items') ?></h3>
						</div>
						<div class="cart-content-table">
							<table>
								<tbody>
									<?php if (!empty($cart_details['cart_items'])) {
										foreach ($cart_details['cart_items'] as $cart_key => $value) { ?>
											<tr>
												<td class="item-img-main"><div><i class="iicon-icon-15 <?php echo ($value['is_veg'] == 1)?'veg':'non-veg'; ?>"></i></div></td>
												<td class="item-name">
													<?php echo $value['name']; ?>
													<ul  class="ul-disc">
														<?php if (!empty($value['addons_category_list'])) {
															foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
																<li><h6><?php echo $cat_value['addons_category']; ?></h6></li>
																<ul class="ul-cir">
																<?php if (!empty($cat_value['addons_list'])) {
																	foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
																		<li><?php echo $add_value['add_ons_name']; ?>  <?php echo $currency_symbol->currency_symbol; ?> <?php echo $add_value['add_ons_price']; ?></li>
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
															<span class="minus" id="minusQuantity" onclick="customCartItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
															<input type="text" value="<?php echo $value['quantity']; ?>" class="pointer-none" />
															<span class="plus" id="plusQuantity" onclick="customCartItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
														</div>
													</div>
												</td>
												<td class="close-btn-cart"><button class="close-btn" onclick="customCartItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'remove',<?php echo $cart_key; ?>)"><i class="iicon-icon-38"></i></button></td>
											</tr>
										<?php }
									}
									else { ?>
										<div class="cart-empty text-center">
											<img src="<?php echo base_url();?>assets/front/images/empty-cart-product.png">
											<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_item') ?></h6>
										</div>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php if (!empty($cart_details['cart_items'])) { ?>
					<div class="col-lg-4">
						<div class="order-summary">
							<div class="order-summary-title">
								<h3><i class="iicon-icon-02"></i><?php echo $this->lang->line('order_summary') ?></h3>
							</div>
							<div class="order-summary-content">
								<table>
									<tbody>
										<tr>
											<td><?php echo $this->lang->line('no_of_items') ?></td>
											<td><strong><?php echo count($cart_details['cart_items']); ?></strong></td>
										</tr>
										<tr>
											<td><?php echo $this->lang->line('sub_total') ?></td>
											<td><strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $cart_details['cart_total_price']; ?></strong></td>
										</tr>
										<tr>
											<td><?php //echo $this->lang->line('delivery_charges') ?></td>
											<?php //$delivery_charges = $this->cart_model->getDeliveryCharges(); ?>
											<td><strong><?php //echo $currency_symbol->currency_symbol; ?> <?php //echo $delivery_charges; ?></strong></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td><?php echo $this->lang->line('to_pay') ?></td>
											<?php $to_pay = $cart_details['cart_total_price'] + (isset($delivery_charges) ? $delivery_charges : 0); ?>
											<td><strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $to_pay; ?></strong></td>
										</tr>
									</tfoot>
								</table>
								<div class="continue-btn">
									<a href="<?php echo base_url().'checkout'; ?>"><button class="btn"><?php echo $this->lang->line('continue') ?></button></a>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } 
			else { ?>
			<div class="col-lg-12">
				<div class="cart-content">
					<!-- <div class="your-item-title">
						<h3><?php //echo $this->lang->line('your_items') ?></h3>
					</div> -->
					<div class="cart-content-table">
						<table>
							<tbody>
								<div class="cart-empty text-center">
									<img src="<?php echo base_url();?>assets/front/images/empty-cart-product.png">
									<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_products') ?></h6>
								</div>	
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</section>

<script type="text/javascript">
$(document).ready(function(){ 
	var count = '<?php echo count($cart_details['cart_items']); ?>'; 
	$('#cart_count').html(count);
});
function customCartItemCount(entity_id,restaurant_id,action,cart_key){ 
	jQuery.ajax({
		type : "POST",
		dataType : "json",
		url : '<?php echo base_url().'cart/customItemCount' ?>',
		data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"action":action,"cart_key":cart_key,'is_main_cart':'yes'},
		success: function(response) {
			$('#your_main_cart').html(response.cart);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {           
			alert(errorThrown);
		}
    });
}
</script>
<?php $this->load->view('footer'); ?>
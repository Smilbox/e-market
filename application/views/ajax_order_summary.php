<?php if (!empty($cart_details['cart_items'])) { ?>
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
					<?php if ($order_mode != 'pickup') { ?>
						<tr>
							<td><?php echo $this->lang->line('delivery_charges') ?></td>
							<?php $delivery_charges = ($this->session->userdata('deliveryCharge'))?$this->session->userdata('deliveryCharge'):0; ?>
							<td><span id="delivery_charges"><strong><?php echo ($delivery_charges > 0)?'+':''; ?> <?php echo $currency_symbol->currency_symbol; ?> <?php echo $delivery_charges; ?></strong></span></td>
						</tr>
					<?php } ?>
					<?php if ($this->session->userdata('coupon_applied') == "yes") {  ?>
						<tr>
							<td><?php echo $this->lang->line('coupon_applied') ?></td>
							<td><strong><?php echo $this->session->userdata('coupon_name'); ?></strong></td>
						</tr>
						<tr>
							<td><?php echo $this->lang->line('coupon_discount') ?></td>
							<?php $coupon_discount = ($this->session->userdata('coupon_discount'))?$this->session->userdata('coupon_discount'):0; ?>
							<td><strong><?php echo ($coupon_discount > 0)?'-':''; ?> <?php echo $currency_symbol->currency_symbol; ?> <?php echo $coupon_discount; ?></strong></td>
						</tr>
					<?php }
					else {
						$coupon_discount = 0;
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td><?php echo $this->lang->line('to_pay') ?></td>
						<?php $to_pay = ($cart_details['cart_total_price'] + $delivery_charges) - $coupon_discount;  
						$this->session->set_userdata(array('total_price' => $to_pay)); ?>
						<td><strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $to_pay; ?></strong></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
<?php } ?>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('order_details') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="order-detail-head">      
            <div class="order-detail-img-main">
                <div class="order-detail-img">
                    <?php $image = ($order_details[0]['restaurant_image'])?(base_url().'uploads/'.$order_details[0]['restaurant_image']):(default_img); ?>
                    <img src="<?php echo $image;?>">  
                </div>
            </div>
            <div class="detail-content">
                <h6><?php echo $order_details[0]['restaurant_name']; ?> <?php echo ($order_details[0]['ratings'] > 0)?'<strong>'.$order_details[0]['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> </h6>
                <span>#<?php echo $this->lang->line('orderid') ?> - <?php echo $order_details[0]['order_id']; ?></span>
                <p><?php echo $order_details[0]['restaurant_address']; ?> </p>
            </div>
        </div>
        <div class="detail-content-middel">
            <div class="content-middel-title">
                <h5><?php echo $this->lang->line('order_items') ?></h5>
            </div>
            <div class="detail-list-box type-food-option">
                <?php if (!empty($order_details[0]['items'])) {
                    foreach ($order_details[0]['items'] as $key => $item_value) {
                    $is_veg = ($item_value['is_veg'] == 1)?'veg':'non-veg'; ?>
                        <div class="detail-list <?php echo $is_veg; ?>">
                            <div class="detail-list-content"> 
                                <div class="detail-list-text">
                                    <h4><?php echo $item_value['name']; ?></h4>
                                </div>
                                <div class="right-price">
                                    <strong><?php echo $order_details[0]['currency_symbol']; ?> <?php echo $item_value['itemTotal']; ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
        <?php $subtotal = 0;
        $delivery_charges = 0;
        $total = 0;
        $coupon_amount = 0;
        if (!empty($order_details[0]['price'])) {
            foreach ($order_details[0]['price'] as $pkey => $pvalue) {
                if ($pvalue['label_key'] == "Sub Total") {
                    $subtotal = $pvalue['value'];
                }
                if ($pvalue['label_key'] == "Delivery Charge") {
                    $delivery_charges = $pvalue['value'];
                }
                if ($pvalue['label_key'] == "Coupon Amount") {
                    $coupon_amount = $pvalue['value'];
                }
                if ($pvalue['label_key'] == "Total") {
                    $total = $pvalue['value'];
                }
            }
        } ?>
        <div class="order-summary-content">
            <table>
                <tbody>
                    <tr>
                        <td><?php echo $this->lang->line('sub_total') ?></td>
                        <td><strong><?php echo $order_details[0]['currency_symbol']; ?> <?php echo $subtotal; ?></strong></td>
                    </tr>
                    <?php if ($order_details[0]['delivery_flag'] == "delivery") { ?>
                        <tr>
                            <td><?php echo $this->lang->line('delivery_charges') ?></td>
                            <td><strong><?php echo $order_details[0]['currency_symbol']; ?> <?php echo $delivery_charges; ?></strong></td>
                        </tr>
                    <?php } ?>
                    <?php if ($coupon_amount > 0) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('coupon_amount') ?></td>
                            <td><strong>-<?php echo $order_details[0]['currency_symbol']; ?> <?php echo $coupon_amount; ?></strong></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><?php echo $this->lang->line('total_paid') ?></td>
                        <td><strong><?php echo $order_details[0]['currency_symbol']; ?> <?php echo $total; ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
      </div>
    </div>
  </div>
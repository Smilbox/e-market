
<div class="pdf_main">
    <div class="head-main">
	    <div class="logo"> <img src="<?php echo base_url();?>assets/admin/img/logo.png" alt="" width="240" height="122"/> </div>
	    <div class="head-right fright" >
	      <div class="col-li full-width100" > <span class="icon"><img src="<?php echo base_url();?>assets/admin/img/note-icon.png" width="50" alt="" /></span>
	        <p>Order NO. <br>
	          <span><?php echo $order_records->entity_id; ?></span></p>
	      </div>
	      <div class="col-li"> <span class="icon"><img src="<?php echo base_url();?>assets/admin/img/calender.png" width="50"  alt="" /></span>
	        <p>DATE <br>
	          <span>
	          <?php $date = date("d-m-Y h:i A",strtotime($order_records->order_date)); echo $date; ?>
	          </span></p>
	      </div>
	    </div>
    </div>
	<div class="main-container">
		<div class="bill-ship-details">
	      <div class="colm fleft">
		  <h3>From</h3>
	        <?php $shop_detail = unserialize($menu_item->shop_detail);
	        if(!empty($shop_detail)){ ?>
	        <p><?php echo $shop_detail->name.'<br>' .$shop_detail->address.'<br> '.$shop_detail->landmark.'<br>'.$shop_detail->city.' '.$shop_detail->zipcode ?></p>
	  	    <?php }else{ ?>
	  	    <p><?php echo $order_records->name ?></p>
	  	    <?php	} ?>
	      </div>
	      <div class="colm last">
		  <h3>To</h3>
	        <?php $user_detail = unserialize($menu_item->user_detail);
	        if(!empty($user_detail)){ ?>
	        <p><?php echo $user_detail['first_name'].' '.$user_detail['last_name'].'<br>' . ($order_records->phone_number ? $order_records->phone_number : $order_records->mobile_number) .'<br>' .$user_detail['address'].'<br> '.$user_detail['landmark'].'<br>'.$user_detail['city'].' '.$user_detail['zipcode'] ?></p>
	       	<?php }else{ ?>
	       		<p>Order By Shop</p>
	       	<?php } ?>
	      </div>
	    </div>
	    <div class="clearfix clr"></div>
	</div>
	<div class="segment-main">
		<!-- Header -->
        <div class="div-thead">
          	<div>
          		<div class="div_1">#</div>
	            <div class="div_2">Item</div>
	            <div class="div_3">Price</div>
	            <div class="div_4">Qty</div>
	            <div class="div_5">Total</div>
            </div>
        </div>
		<!-- body -->
	</div>
	<div>
        	<?php $item_detail = unserialize($menu_item->item_detail);
        	 if(!empty($item_detail)){ $Subtotal = 0; $i = 1;
        	 	$addons_name_list = '';
        		foreach($item_detail as $key => $value){ 
        			if($value['is_customize'] == 1){
			            foreach ($value['addons_category_list'] as $k => $val) {
			                $addons_name = '';
			                foreach ($val['addons_list'] as $m => $mn) {
			                    $addons_name .= $mn['add_ons_name'].', ';
			                    if($value['is_deal'] != 1){
			                    	$Subtotal = $Subtotal + $mn['add_ons_price'];
			                    }
			                }
			                if($value['is_deal'] != 1){
			                	$addons_name_list .= '<p><b>'.$val['addons_category'].'</b>:'.substr($addons_name, 0, -2).'</p>';
			            	}else{
			            		$addons_name_list .= '<p>'.substr($addons_name, 0, -2).'</p>';
			            	}
			            }
			    	}
			    	$price = ($value['offer_price'])?$value['offer_price']:$value['rate']; ?>
	            <div  class="b0">
	            	<div  class="div_1"><?php echo $i ?></div>
		            <div class="div_2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" role="text" title="<?php echo $value['item_name']; ?><?php echo $addons_name_list; ?>"><?php echo $value['item_name']; ?><?php echo $addons_name_list; ?></div>
		            <div class="center div_3"><?php echo $shop_detail->currency_symbol; ?><?php echo ($Subtotal)?number_format_unchanged_precision($Subtotal,$shop_detail->currency_code):number_format_unchanged_precision($price,$shop_detail->currency_code) ?></div>
		            <div class="center div_4"><?php echo $value['qty_no'] ?></div>
		            <div  class="center div_5"><?php echo $shop_detail->currency_symbol; ?><?php echo ($Subtotal)?number_format_unchanged_precision($Subtotal * $value['qty_no'],$shop_detail->currency_code):number_format_unchanged_precision($price * $value['qty_no'],$shop_detail->currency_code); ?></div>
	           </div>
           <?php }  } ?>
        </div>
	<!-- Footer part for Price -->
    <table border="3" cellpadding="10" cellspacing="0" width="100%" class="table-style">
          <tr>
            <td rowspan="4"  class="width60"><?php echo ($order_records->extra_comment)?'Preferences: '.$order_records->extra_comment:'' ?></td>
            <td class="align-right" class="width15"><strong>Subtotal</strong></td>
            <td class="align-left"  class="width20"><?php echo $shop_detail->currency_symbol; ?><?php echo number_format_unchanged_precision($order_records->subtotal,$shop_detail->currency_code) ?></td>
          </tr>
          <tr>
            <td class="align-right"><strong>Delivery Charge</strong></td>
            <td class="align-left"><?php echo ($order_records->delivery_charge)?$shop_detail->currency_symbol.number_format_unchanged_precision($order_records->delivery_charge,$shop_detail->currency_code):'-'; ?></td>
          </tr>
          <tr>
            <td class="align-right"><strong>Discount</strong></td>
            <td class="align-left"><?php echo ($order_records->coupon_amount)?(($order_records->coupon_type == 'Amount')?$shop_detail->currency_symbol:'').number_format_unchanged_precision($order_records->coupon_amount,$shop_detail->currency_code):'-'; ?><?php echo ($order_records->coupon_type == 'Percentage')?'% ('.$order_records->coupon_discount.')':'' ?></td>
          </tr>
          <!-- <tr>
            <td class="align-right"><strong>Sales Tax</strong></td>
            <td class="align-left"><?php echo ($order_records->tax_rate)?number_format_unchanged_precision($order_records->tax_rate):'-'; ?><?php echo ($order_records->tax_type == 'Percentage')?'%':'' ?></td>
          </tr> -->
          <tr>
            <td class="align-right grand-total"><strong>TOTAL</strong></td>
            <td class="align-left grand-total"><?php echo $shop_detail->currency_symbol; ?><?php echo number_format_unchanged_precision($order_records->total_rate,$shop_detail->currency_code); ?></td>
          </tr>
    </table>
    <!-- Footer part for Price end -->
</div>
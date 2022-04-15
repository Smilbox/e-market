<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title"><?php echo $this->lang->line('booking_details') ?></h4>
            <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <?php if (!empty($booking_details[0])) {?>
                <div class="order-detail-head">      
                    <div class="order-detail-img-main">
                        <div class="order-detail-img"> 
                            <?php $image = ($booking_details[0]['image'])?($booking_details[0]['image']):(default_img); ?>
                            <img src="<?php echo $image;?>"> 
                        </div>
                    </div>
                    <div class="detail-content">
                        <h6><?php echo $booking_details[0]['name']; ?>  <?php echo ($booking_details[0]['ratings'] > 0)?'<strong>'.$booking_details[0]['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> </h6>                      
                        <p><?php echo $booking_details[0]['address']; ?> </p>
                    </div>
                </div>
                <div class="detail-content-middel">
                    <?php if (!empty($booking_details[0]['package_name'])) { ?>
                        <div class="content-middel-title">
                            <h5><?php echo $this->lang->line('packages') ?></h5>
                        </div>
                        <div class="detail-list-box">
                            <div class="detail-list">
                                <div class="detail-list-img">
                                    <div class="list-img">  
                                        <?php $image = ($booking_details[0]['package_image'])?(base_url().'uploads/'.$booking_details[0]['package_image']):(default_img); ?>
                                        <img src="<?php echo $image;?>"> 
                                    </div>
                                </div>
                                <div class="detail-list-content"> 
                                    <div class="detail-list-text">
                                        <h4><?php echo $booking_details[0]['package_name']; ?></h4>
                                        <p><?php echo $booking_details[0]['package_detail']; ?></p>
                                        <strong>$<?php echo $booking_details[0]['package_price']; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="booking-option-main">
                        <div class="booking-option">
                            <div class="booking-option-cont">
                                <div class="option-img">
                                    <img src="<?php echo base_url();?>assets/front/images/avatar-man.png">
                                </div>
                                <div class="booking-option-text">
                                    <span><?php echo $this->lang->line('no_of_people') ?></span>
                                    <strong><?php echo $booking_details[0]['no_of_people']; ?> People</strong>
                                </div>
                            </div>
                        </div>
                        <div class="booking-option">
                            <div class="booking-option-cont">
                                <div class="option-img">
                                    <img src="<?php echo base_url();?>assets/front/images/dining-time.png">
                                </div>
                                <div class="booking-option-text">
                                    <span><?php echo $this->lang->line('dining_time') ?></span>
                                    <strong><?php echo (date("G:i A",strtotime($booking_details[0]['booking_date'])));?></strong>
                                </div>
                            </div>
                            <div class="add-cart-item">

                            </div>
                        </div>
                        <div class="booking-option">
                            <div class="booking-option-cont">
                                <div class="option-img">
                                    <img src="<?php echo base_url();?>assets/front/images/pick-date.png">
                                </div>
                                <div class="booking-option-text">
                                    <span><?php echo $this->lang->line('event_date') ?></span>
                                    <strong><?php echo (date("d M Y",strtotime($booking_details[0]['booking_date'])));?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="booking-option">
                            <div class="booking-option-cont">
                                <div class="option-img">
                                    <img src="<?php echo base_url();?>assets/front/images/pick-date.png">
                                </div>
                                <div class="booking-option-text">
                                    <span><?php echo $this->lang->line('booking_date') ?></span>
                                    <strong><?php echo (date("d M Y",strtotime($booking_details[0]['created_date'])));?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
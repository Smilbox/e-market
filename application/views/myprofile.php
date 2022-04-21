<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php $this->load->view('header');?> 
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<section class="inner-pages-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="heading-title">
                    <h2><?php echo $this->lang->line('my_profile') ?></h2>
                </div>
            </div>
            <div class="col-lg-12">
            <?php if($this->session->flashdata('myProfileMSG')) {?>
                <div class="alert alert-success">
                     <?php echo $this->session->flashdata('myProfileMSG');?>
                </div>
            <?php } ?>
            </div>
            <div class="col-lg-12">
                <div class="my-profile-head">
                    <div class="profile-img-main">
                        <div class="profile-img">
                            <?php $image = ($profile->image) ? ($profile->image) : (default_user_img);?>
                            <img src="<?php echo $image; ?>">
                        </div>
                    </div>
                    <div class="my-profile-detail">
                        <div class="my-profile-info">
                            <h3><?php echo $profile->first_name . ' ' . $profile->last_name; ?></h3>
                            <?php if (!empty($addresses)) {
                                foreach ($addresses as $key => $value) {
                                    if ($value->is_main == 1) {?>
                                        <p><i class="iicon-icon-20"></i> <?php echo $value->address . ', ' . $value->city . ', ' . $value->zipcode; ?></p>
                                    <?php break;}
                                }
                            }?>
                            <p><i class="iicon-icon-35"></i> <?php echo $profile->mobile_number; ?></p>
                        </div>
                        <div class="edit-pro-btn">
                            <button class="btn" data-toggle="modal" data-target="#edit-profile"><?php echo $this->lang->line('edit_profile') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="profile_page_content">
            <div class="col-xl-3 col-lg-4">
                <div class="sidebar-menu-main">
                    <div class="sidebar-menu">
                        <div class="ordering-title">
                            <h6><?php echo $this->lang->line('ordering') ?></h6>
                        </div>
                       
                        <ul id="myTab" class="nav nav-tabs">
                            <li id="tab_order_history" class="tabs <?php echo ($selected_tab == '')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#order_history" data-toggle="tab"><?php echo $this->lang->line('order_history') ?></a></li>
                            <li id="tab_addresses" class="tabs <?php echo ($selected_tab == 'addresses')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#addresses" data-toggle="tab"><?php echo $this->lang->line('my_addresses') ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-lg-8">
                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade <?php echo ($selected_tab == "")?"in active show":"";?>" id="order_history">
                        <div class="profile-content-area">
                            <div class="profile-page-title">
                                <h5><?php echo $this->lang->line('order_history') ?></h5>
                                <ul class="nav nav-tabs" role="tablist">
                                  <li class="nav-item">
                                    <a href="#current-orders" class="nav-link active" data-toggle="tab"><?php echo $this->lang->line('current_orders') ?></a>
                                  </li>
                                  <li class="nav-item">
                                    <a href="#past-orders" class="nav-link" data-toggle="tab"><?php echo $this->lang->line('past_orders') ?></a>
                                  </li>
                                </ul>
                            </div>
                            <div class="profile-content-main">
                                <div class="tab-content">
                                    <div id="past-orders" class="tab-pane">
                                        <?php if (!empty($past_orders)) { ?>
                                            <div class="row orders-box-row">
                                                <?php if (!empty($past_orders)) {
                                                    foreach ($past_orders as $key => $value) {
                                                        if ($key <= 7) {?>
                                                            <?php $subtotal = 0;
                                                            $delivery_charges = 0;
                                                            $total = 0;
                                                            $coupon_amount = 0;
                                                            if (!empty($value['price'])) {
                                                                foreach ($value['price'] as $pkey => $pvalue) {
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
                                                            <div class="col-xl-6 col-lg-12">
                                                                <div class="ordering-box-main">
                                                                    <div class="ordering-box-top">
                                                                        <div class="ordering-box-img">
                                                                            <div class="ordering-img">
                                                                                <?php $image = ($value['restaurant_image']) ? (base_url() . 'uploads/' . $value['restaurant_image']) : (default_user_img);?>
                                                                                <img src="<?php echo $image; ?>">
                                                                                <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-text">
                                                                            <h6><?php echo $value['restaurant_name']; ?></h6>
                                                                            <p>#<?php echo $this->lang->line('orderid') ?> - <?php echo $value['order_id']; ?></p>
                                                                            <strong><?php echo $this->lang->line('price') ?> : <span><?php echo $value['currency_symbol'];?> <?php echo $total; ?></span></strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-bottom">
                                                                        <span class="date-icon"><?php echo date("d M Y", strtotime($value['order_date'])); ?></span>
                                                                        <span class="relivered-icon"><?php echo $value['order_status']; ?></span>
                                                                        <div class="ordering-btn">
                                                                            <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }?>
                                                    <?php }
                                                }?>
                                            </div>
                                            <?php if (count($past_orders) > 8) {?>
                                                <div class="row orders-box-row display-flex" id="all_past_orders">
                                                    <?php foreach ($past_orders as $key => $value) {
                                                        if ($key > 7) {?>
                                                            <?php $subtotal = 0;
                                                            $delivery_charges = 0;
                                                            $total = 0;
                                                            $coupon_amount = 0;
                                                            if (!empty($value['price'])) {
                                                                foreach ($value['price'] as $pkey => $pvalue) {
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
                                                            <div class="col-xl-6 col-lg-12">
                                                                <div class="ordering-box-main">
                                                                    <div class="ordering-box-top">
                                                                        <div class="ordering-box-img">
                                                                            <div class="ordering-img">
                                                                                <?php $image = ($value['restaurant_image']) ? (base_url() . 'uploads/' . $value['restaurant_image']) : (default_user_img);?>
                                                                                <img src="<?php echo $image; ?>">
                                                                                <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-text">
                                                                            <h6><?php echo $value['restaurant_name']; ?></h6>
                                                                            <p>#<?php echo $this->lang->line('orderid') ?> - <?php echo $value['order_id']; ?></p>
                                                                            <strong><?php echo $this->lang->line('price') ?> : <span><?php echo $value['currency_symbol'];?> <?php echo $total; ?></span></strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-bottom">
                                                                        <span class="date-icon"><?php echo date("d M Y", strtotime($value['order_date'])); ?></span>
                                                                        <span class="relivered-icon"><?php echo $value['order_status']; ?></span>
                                                                        <div class="ordering-btn">
                                                                            <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    }?>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div id="more_past_orders" class="load-more-btn">
                                                        <button class="btn" onclick="moreOrders('past')"><?php echo $this->lang->line('load_more') ?></button>
                                                    </div>
                                                </div>
                                            <?php }?>
                                        <?php }
                                        else { ?>
                                            <div class="col-xl-6 col-lg-12">
                                                <p><?php echo $this->lang->line('no_past_orders') ?></p>
                                            </div>
                                        <?php }?>
                                    </div>
                                    <div id="current-orders" class="tab-pane show active">
                                        <?php if (!empty($in_process_orders)) { ?>
                                        <div class="row orders-box-row">
                                            <?php if (!empty($in_process_orders)) {
                                                foreach ($in_process_orders as $key => $value) {
                                                    if ($key <= 7) {?>
                                                        <?php $subtotal = 0;
                                                        $delivery_charges = 0;
                                                        $total = 0;
                                                        $coupon_amount = 0;
                                                        if (!empty($value['price'])) {
                                                            foreach ($value['price'] as $pkey => $pvalue) {
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
                                                        <div class="col-xl-6 col-lg-12">
                                                            <div class="ordering-box-main">
                                                                <div class="ordering-box-top">
                                                                    <div class="ordering-box-img">
                                                                        <div class="ordering-img">
                                                                            <?php $image = ($value['restaurant_image']) ? (base_url() . 'uploads/' . $value['restaurant_image']) : (default_user_img);?>
                                                                            <img src="<?php echo $image; ?>">
                                                                            <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> 
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-text">
                                                                        <h6><?php echo $value['restaurant_name']; ?></h6>
                                                                        <p>#<?php echo $this->lang->line('orderid') ?> - <?php echo $value['order_id']; ?></p>
                                                                        <strong><?php echo $this->lang->line('price') ?> : <span><?php echo $value['currency_symbol'];?> <?php echo $total; ?></span></strong>
                                                                    </div>
                                                                </div>
                                                                <div class="ordering-box-bottom">
                                                                    <span class="date-icon"><?php echo date("d M Y", strtotime($value['order_date'])); ?></span>
                                                                    <span class="relivered-icon"><?php echo $value['order_status']; ?></span>
                                                                    <div class="ordering-btn">
                                                                        <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                        <?php if ($value['delivery_flag'] == "delivery") { ?>
                                                                            <a href="<?php echo base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['order_id'])); ?>" class="btn"><?php echo $this->lang->line('track_order') ?></a>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                <?php }
                                            }?>
                                        </div>
                                        <?php if (count($in_process_orders) > 8) { ?>
                                            <div class="row orders-box-row display-flex" id="all_current_orders" >
                                                <?php foreach ($in_process_orders as $key => $value) {
                                                    if ($key > 7) {?>
                                                        <?php $subtotal = 0;
                                                        $delivery_charges = 0;
                                                        $total = 0;
                                                        $coupon_amount = 0;
                                                        if (!empty($value['price'])) {
                                                            foreach ($value['price'] as $pkey => $pvalue) {
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
                                                        <div class="col-xl-6 col-lg-12">
                                                            <div class="ordering-box-main">
                                                                <div class="ordering-box-top">
                                                                    <div class="ordering-box-img">
                                                                        <div class="ordering-img">
                                                                            <?php $image = ($value['restaurant_image']) ? (base_url() . 'uploads/' . $value['restaurant_image']) : (default_user_img);?>
                                                                            <img src="<?php echo $image; ?>">
                                                                            <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-text">
                                                                        <h6><?php echo $value['restaurant_name']; ?></h6>
                                                                        <p>#<?php echo $this->lang->line('orderid') ?> - <?php echo $value['order_id']; ?></p>
                                                                        <strong><?php echo $this->lang->line('price') ?> : <span><?php echo $value['currency_symbol'];?> <?php echo $total; ?></span></strong>
                                                                    </div>
                                                                </div>
                                                                <div class="ordering-box-bottom">
                                                                    <span class="date-icon"><?php echo date("d M Y", strtotime($value['order_date'])); ?></span>
                                                                    <span class="relivered-icon"><?php echo $value['order_status']; ?></span>
                                                                    <div class="ordering-btn">
                                                                        <button class="btn" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                        <?php if ($value['delivery_flag'] == "delivery") { ?>
                                                                            <a href="<?php echo base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['order_id'])); ?>" class="btn"><?php echo $this->lang->line('track_order') ?></a>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }?>
                                            </div>
                                            <div class="col-lg-12">
                                                <div id="more_in_process_orders" class="load-more-btn">
                                                    <button class="btn" onclick="moreOrders('process')"><?php echo $this->lang->line('load_more') ?></button>
                                                </div>
                                            </div>
                                        <?php }?>
                                        <?php }
                                        else { ?>
                                            <div class="col-xl-6 col-lg-12">
                                                <p><?php echo $this->lang->line('no_current_orders') ?></p>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade <?php echo ($selected_tab == "bookings")?"in active show":"";?>" id="bookings">
                        <div class="profile-content-area">
                            <div class="profile-page-title">
                                <h5><?php echo $this->lang->line('my_bookings') ?></h5>
                                <ul class="nav nav-tabs" role="tablist">
                                  <li class="nav-item">
                                    <a href="#current-bookings" class="nav-link active" data-toggle="tab"><?php echo $this->lang->line('upcoming_bookings') ?></a>
                                  </li>
                                  <li class="nav-item">
                                    <a href="#past-bookings" class="nav-link" data-toggle="tab"><?php echo $this->lang->line('past_bookings') ?></a>
                                  </li>
                                </ul>
                            </div>
                            <div class="profile-content-main">
                                <div class="tab-content">
                                    <div id="past-bookings" class="tab-pane">
                                        <?php if (!empty($past_events)) { ?>
                                            <div class="row orders-box-row">
                                                <?php if (!empty($past_events)) {
                                                    foreach ($past_events as $key => $value) {
                                                        if ($key <= 7) { ?>
                                                            <div class="col-xl-6 col-lg-12">
                                                                <div class="ordering-box-main">
                                                                    <div class="ordering-box-top">
                                                                        <div class="ordering-box-img">
                                                                            <div class="ordering-img">
                                                                                <?php $image = ($value['image'])?($value['image']):(default_user_img); ?>
                                                                                <img src="<?php echo $image;?>"> 
                                                                                <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-text">
                                                                            <h6><?php echo $value['name'];?></h6>
                                                                            <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-bottom">
                                                                        <ul>
                                                                            <li><i class="iicon-icon-18"></i><?php echo (date("G:i A",strtotime($value['booking_date'])));?></li>
                                                                            <li><i class="iicon-icon-26"></i><?php echo (date("d M Y",strtotime($value['booking_date'])));?></li>
                                                                            <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                        </ul>
                                                                        <div class="ordering-btn">
                                                                            <button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } 
                                                    }
                                                } ?>
                                            </div>
                                            <?php if (count($past_events) > 8) { ?>
                                                <div class="row orders-box-row display-flex" id="all_past_events">
                                                    <?php foreach ($past_events as $key => $value) {
                                                        if ($key > 7) { ?>
                                                            <div class="col-xl-6 col-lg-12">
                                                                <div class="ordering-box-main">
                                                                    <div class="ordering-box-top">
                                                                        <div class="ordering-box-img">
                                                                            <div class="ordering-img">
                                                                                <?php $image = ($value['image'])?($value['image']):(default_user_img); ?>
                                                                                <img src="<?php echo $image;?>"> 
                                                                                 <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-text">
                                                                            <h6><?php echo $value['name'];?></h6>
                                                                            <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                            <strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-bottom">
                                                                        <ul>
                                                                            <li><i class="iicon-icon-18"></i><?php echo (date("G:i A",strtotime($value['booking_date'])));?></li>
                                                                            <li><i class="iicon-icon-26"></i><?php echo (date("d M Y",strtotime($value['booking_date'])));?></li>
                                                                            <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                        </ul>
                                                                        <div class="ordering-btn">
                                                                            <button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    } ?>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div id="more_past_events" class="load-more-btn">
                                                        <button class="btn" onclick="moreEvents('past')"><?php echo $this->lang->line('load_more') ?></button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } 
                                        else { ?>
                                            <div class="col-xl-6 col-lg-12">
                                                <p>No past bookings found.</p>
                                            </div>
                                        <?php }?>
                                    </div>
                                    <div id="current-bookings" class="tab-pane show active">
                                        <?php if (!empty($upcoming_events)) { ?>
                                            <div class="row orders-box-row">
                                                <?php if (!empty($upcoming_events)) {
                                                    foreach ($upcoming_events as $key => $value) {
                                                        if ($key <= 7) { ?>
                                                            <div class="col-xl-6 col-lg-12">
                                                                <div class="ordering-box-main">
                                                                    <div class="ordering-box-top">
                                                                        <div class="ordering-box-img">
                                                                            <div class="ordering-img">
                                                                                <?php $image = ($value['image'])?($value['image']):(default_user_img); ?>
                                                                                <img src="<?php echo $image;?>"> 
                                                                                <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-text">
                                                                            <h6><?php echo $value['name'];?></h6>
                                                                            <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                            <strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-bottom">
                                                                        <ul>
                                                                            <li><i class="iicon-icon-18"></i><?php echo (date("G:i A",strtotime($value['booking_date'])));?></li>
                                                                            <li><i class="iicon-icon-26"></i><?php echo (date("d M Y",strtotime($value['booking_date'])));?></li>
                                                                            <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                        </ul>
                                                                        <div class="ordering-btn">
                                                                            <button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } 
                                                    }
                                                } ?>
                                            </div>
                                            <?php if (count($upcoming_events) > 8) { ?>
                                                <div class="row orders-box-row display-no" id="all_upcoming_events">
                                                    <?php foreach ($upcoming_events as $key => $value) {
                                                        if ($key > 7) { ?>
                                                            <div class="col-xl-6 col-lg-12">
                                                                <div class="ordering-box-main">
                                                                    <div class="ordering-box-top">
                                                                        <div class="ordering-box-img">
                                                                            <div class="ordering-img">
                                                                                <?php $image = ($value['image'])?($value['image']):(default_user_img); ?>
                                                                                <img src="<?php echo $image;?>"> 
                                                                                <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-text">
                                                                            <h6><?php echo $value['name'];?></h6>
                                                                            <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                            <strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-bottom">
                                                                        <ul>
                                                                            <li><i class="iicon-icon-18"></i><?php echo (date("G:i A",strtotime($value['booking_date'])));?></li>
                                                                            <li><i class="iicon-icon-26"></i><?php echo (date("d M Y",strtotime($value['booking_date'])));?></li>
                                                                            <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                        </ul>
                                                                        <div class="ordering-btn">
                                                                            <button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    } ?>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div id="more_upcoming_events" class="load-more-btn">
                                                        <button class="btn" onclick="moreEvents('past')"><?php echo $this->lang->line('load_more') ?></button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php }  
                                        else { ?>
                                            <div class="col-xl-6 col-lg-12">
                                                <p><?php echo $this->lang->line('no_upcoming_bookings') ?></p>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade <?php echo ($selected_tab == "addresses")?"in active show":"";?>" id="addresses">
                        <div class="profile-content-area">
                            <div class="profile-page-title">
                                <h5><?php echo $this->lang->line('my_addresses') ?></h5>
                                <div class="add-address-btn">
                                    <button class="btn" data-toggle="modal" data-target="#add-address"><?php echo $this->lang->line('add_address') ?></button>
                                </div>
                            </div>
                            <div class="profile-content-main">
                                <?php if (!empty($users_address)) { ?>
                                    <div class="row orders-box-row">
                                        <?php if (!empty($users_address)) {
                                            foreach ($users_address as $key => $value) { 
                                                $class = ($value->is_main == 1)?"primary-address":""; ?>
                                                <div class="col-xl-6 col-lg-12">
                                                    <div class="my-address-main <?php echo $class; ?>">
                                                        <div class="my-address-box">
                                                            <div class="my-address-list">
                                                                <h6>Address <?php echo $key+1; ?></h6> <?php echo ($value->is_main == 1)?"<span class='default-address'>Default</span>":""; ?>
                                                                <p><?php echo $value->address.','.$value->landmark.','.$value->city.','.$value->search_area.','.$value->zipcode; ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="address-btn">
                                                            <button class="btn" data-toggle="modal"  onclick="editAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('edit_address') ?></button>
                                                            <button class="btn" data-toggle="modal" onclick="showDeleteAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('delete_address') ?></button>
                                                            <?php if ($value->is_main == 0) { ?>
                                                                <button class="btn" data-toggle="modal" onclick="showMainAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('set_as_primary') ?></button>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>  
                                            <?php }
                                        } ?>                    
                                    </div>
                                <?php } 
                                else { ?>
                                    <div class="col-xl-6 col-lg-12">
                                        <p><?php echo $this->lang->line('no_address_found') ?></p>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end content-area section -->

<!-- Modal -->
<!-- Edit Profile -->
<div class="modal modal-main edit-profile" id="edit-profile">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('edit_profile') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form id="form_my_profile" name="form_my_profile" method="post" class="form-horizontal float-form" enctype="multipart/form-data">
            <div id="error-msg" class="error display-no"></div>
            <div class="edit-profile-img">
                <div class="edit-img">
                    <?php $image = ($profile->image) ? ($profile->image) : (base_url() . 'assets/front/images/user-login.png');?>
                    <img id='old' src="<?php echo $image; ?>">
                    <img id="preview" class="display-no"/>
                    <label>
                        <input type="file" name="image" id="image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)"/>
                        <i class="iicon-icon-37"></i>
                    </label>
                    <span class="error display-no" id="errormsg"></span>
                </div>
            </div>
            <div class="form-group">
                <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $profile->entity_id; ?>">
                <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($profile->image) ? $profile->image : ''; ?>" />
                <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " value="<?php echo $profile->first_name; ?>">
                <label><?php echo $this->lang->line('first_name') ?></label>
            </div>
            <div class="form-group">
                <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " value="<?php echo $profile->last_name; ?>">
                <label><?php echo $this->lang->line('last_name') ?></label>
            </div>
            <div class="form-group">
                <input type="text" name="email" id="email" class="form-control email" placeholder=" " value="<?php echo $profile->email; ?>">
                <label><?php echo $this->lang->line('email') ?></label>
            </div>
            <div class="form-group">
                <input type="text" name="phone_number" id="phone_number" class="form-control digits required" placeholder=" " readonly value="<?php echo $profile->mobile_number; ?>">
                <label><?php echo $this->lang->line('phone_number') ?></label>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder=" ">
                <label><?php echo $this->lang->line('password') ?></label>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder=" ">
                <label><?php echo $this->lang->line('confirm_pass') ?></label>
            </div>
            <div class="save-btn">
                <button type="submit" name="submit_profile" id="submit_profile" value="Save" class="btn btn-primary"><?php echo $this->lang->line('save') ?></button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Add Address -->
<div class="modal modal-main add-address_" id="add-address">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><span id="address-form-title"><?php echo $this->lang->line('add') ?></span> <?php echo $this->lang->line('address') ?></h4>
                <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 modal_body_map">
                        <div class="location-map" id="location-map">
                            <div  id="map_canvas"></div>
                        </div>
                    </div>
                </div>
                <form id="form_add_address" name="form_add_address" method="post" class="form-horizontal float-form" enctype="multipart/form-data" >
                    <div id="error-msg" class="alert alert-danger display-no"></div>
                    <div class="form-group">
                        <input type="text" name="add_address_area" id="add_address_area" onFocus="geolocate('')" placeholder=" " onchange="getMarker('');" class="form-control">
                        <label><?php echo $this->lang->line('search_delivery_area') ?></label>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="user_entity_id" id="user_entity_id" value="<?php echo $this->session->userdata('UserID'); ?>">
                        <input type="hidden" name="add_entity_id" id="add_entity_id" value="">
                        <input type="hidden" name="latitude" id="latitude" value="">
                        <input type="hidden" name="longitude" id="longitude" value="">
                        <input type="text" name="address_field" id="address_field" class="form-control" placeholder=" " onchange="getMarker(this.value)">
                        <label><?php echo $this->lang->line('complete_address') ?></label>
                    </div>
                    <div class="form-group">
                        <input type="text" name="landmark" id="landmark" class="form-control" placeholder=" ">
                        <label><?php echo $this->lang->line('landmark') ?></label>
                    </div>
                    <div class="address-add-btn">
                        <input type="hidden" name="submit_address" id="submit_address" value="Add" class="btn btn-primary">
                        <button type="submit" name="save_address" id="save_address" value="Save" class="btn btn-primary"><?php echo $this->lang->line('save') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-main delete-address_" id="delete-address">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?php echo $this->lang->line('delete_address') ?>?</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <p><?php echo $this->lang->line('delete_module'); ?><p>
                <input type="hidden" name="delete_address_id" id="delete_address_id" value="">
                <div class="action-btn">
                    <input type="button" name="delete_address" id="delete_address" value="Delete" class="btn btn-primary" onclick="deleteAddress()">
                    <input type="button" name="cancel" id="cancel" value="Cancel" class="btn btn-primary" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-main main-address_" id="main-address">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?php echo $this->lang->line('set_main_address') ?>?</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <p><?php echo $this->lang->line('set_main_address_confirm') ?></p>
                <input type="hidden" name="main_address_id" id="main_address_id" value="">
                <div class="action-btn">
                    <input type="button" name="main_address" id="main_address" value="Ok" class="btn" onclick="setMainAddress()">
                    <input type="button" name="cancel" id="cancel" value="Cancel" class="btn" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dialog-confirm" title="Delete this address?" class="display-no">
  <p><span class="ui-icon ui-icon-alert"></span><?php echo $this->lang->line('delete_module'); ?></p>
</div>

<div id="dialog-confirm-setmain" title="Set Main Address?" class="display-no">
  <p><span class="ui-icon ui-icon-alert"></span><?php echo $this->lang->line('set_main_address_confirm') ?></p>
</div>

<!-- Booking Details -->
<div class="modal modal-main order-detail-popup" id="booking-details"></div>
<!-- Order Details -->
<div class="modal modal-main order-detail-popup" id="order-details"></div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GMAP_API_KEY ?>&libraries=places"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script src="<?php echo base_url(); ?>assets/front/js/scripts/admin-management-front.js"></script>
<script type="text/javascript">
var map, marker;
jQuery(document).ready(function() {
    initMap();
    getLocation('my_profile');
    initAutocomplete('add_address_area');
    function initMap(){
        map = new google.maps.Map(document.getElementById('map_canvas'),
        {
            center: {
              lat: 20.055,
              lng: 20.968
            },
            zoom: 2
        });
        geocoder = new google.maps.Geocoder();
        var position = {lat: 20.055,lng: 20.968};
        marker = new google.maps.Marker({
            position: position,
            draggable: true,
            map: map,
        });
        infowindow = new google.maps.InfoWindow({
          size: new google.maps.Size(150, 50)
        });
        google.maps.event.addListener(marker, 'dragend', function(evt) {
            geocodePosition(marker.getPosition());
            $('#latitude').val(evt.latLng.lat());
            $('#longitude').val(evt.latLng.lng());
        });
    }
});

function readURL(input){
    var fileInput = document.getElementById('image');
    var filePath = fileInput.value;
    var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    if(input.files[0].size <= 10506316){ // 10 MB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview').attr('src', e.target.result).attr('style','display: inline-block;');
                $("#old").hide();
                $('#errormsg').html('').hide();
            }
            reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#preview').attr('src', '').attr('style','display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('file_extenstion'); ?>").show();
            $('#image').val('');
            $("#old").show();
        }
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg'); ?>").show();
        $('#image').val('');
        $("#old").show();
    }
}

</script>
<?php $this->load->view('footer');?>

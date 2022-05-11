<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<style>
    .account-accordion .card {
        border: 0px !important;
        background: #fff;
        margin-bottom: 20px;
    }


    .account-accordion .accordion .card .card-header {
        margin: 0px;
        padding: 0;
    }

    .account-accordion .card-header {
        border: 0px;
        background: #fff;
    }

    .account-accordion .accordion .card .card-header .card-header-title {
        width: 100%;
        padding: 15px;
        display: -webkit-box;
        display: -moz-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        align-items: center;
        position: relative;
        cursor: pointer;
    }

    .account-accordion .card-body {
        padding: 0 20px;
    }


    .choose-order-mode,
    .payment-mode {
        border-top: 1px solid #E4E4E4;
        padding-top: 20px;
        margin-bottom: 20px;
    }

    .choose-order-title>h6 {
        font-size: 18px;
        color: #121212;
        margin-bottom: 8px;
    }

    .radio-btn-list {
        margin-bottom: 7px;
    }

    #your_coupons>h5,
    .current-location>h5 {
        margin-bottom: 12px;
        font-size: 18px;
    }

    small {
        font-size: 80%;
    }

    .cart-section .float-form .form-control {
        height: 45px;
        border-radius: 5px;
        font-size: 16px;
    }

    .float-form .form-control {
        height: 50px;
        border-radius: 10px;
        padding: 0 31px;
        font-size: 16px;
        color: #161212;
        letter-spacing: 0.02em;
    }

    .form-control {
        border: 1px solid #E4E4E4 !important;
    }

    .card.card2 {
        border-top: 1px solid #E4E4E4 !important;
        border-radius: 0;
        border-bottom: 1px solid #E4E4E4 !important;
        padding: 20px 0 10px;
    }

    .gmaps {
        height: 300px !important;
        width: 100% !important;
    }
</style>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar');

    if ($this->input->post()) {
        foreach ($this->input->post() as $key => $value) {
            $$key = @htmlspecialchars($this->input->post($key));
        }
    } else {
        $FieldsArray = array('entity_id', 'user_id', 'restaurant_id', 'coupon_id', 'tax_rate', 'order_status', 'order_date', 'total_rate', 'coupon_amount', 'coupon_type', 'tax_type', 'subtotal');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($edit_records->$key);
        }
    }
    if (isset($edit_records) && $edit_records != "") {
        $add_label     = $this->lang->line('title_admin_orderedit');
        $form_action   = base_url() . ADMIN_URL . '/' . $this->controller_name . "/edit/" . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
        $address = $this->order_model->getAddress($user_id);
    } else {
        $add_label    = $this->lang->line('title_admin_orderadd');
        $form_action      = base_url() . ADMIN_URL . '/' . $this->controller_name . "/add";
        $menu_item = 1;
    }
    $restaurant_id = isset($_POST['restaurant_id']) ? $_POST['restaurant_id'] : $restaurant_id;
    $menu_detail     = $this->order_model->getItem($restaurant_id);
    ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('order') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view"><?php echo $this->lang->line('order') ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $add_label; ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $add_label; ?></div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $form_action; ?>" id="form_add<?php echo $this->prefix ?>" name="form_add<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <div id="iframeloading" style="display: none;" class="frame-load">
                                    <img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif" alt="loading" />
                                </div>
                                <div class="form-body account-accordion">
                                    <?php if (!empty($Error)) { ?>
                                        <div class="alert alert-danger"><?php echo $Error; ?></div>
                                    <?php } ?>
                                    <?php if (validation_errors()) { ?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors(); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('users') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id; ?>">
                                            <select name="user_id" class="form-control" id="user_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if (!empty($user)) {
                                                    foreach ($user as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $user_id) ? "selected" : "" ?>><?php echo $value->first_name . ' ' . $value->last_name ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="restaurant_id" class="form-control" id="restaurant_id" onchange="getItemDetail(this.id,this.value);updateRestaurantId(this.value)">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if (!empty($restaurant)) {
                                                    foreach ($restaurant as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $restaurant_id) ? "selected" : "" ?> amount="<?php echo $value->amount ?>" type="<?php echo $value->amount_type ?>"><?php echo $value->name ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if (isset($_POST['item_id'])) { ?>
                                        <div class="form-group">
                                            <?php for ($i = 1, $inc = 1; $i <= count($_POST['item_id']); $inc++, $i++) { ?>
                                                <div class="clone" id="cloneItem<?php echo $inc ?>">
                                                    <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?><span class="required">*</span></label>
                                                    <div class="col-md-2">
                                                        <select name="item_id[<?php echo $inc ?>]" class="form-control item_id validate-class" id="item_id<?php echo $inc ?>" onchange="getItemPrice(this.id,<?php echo $inc ?>)">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php if ($_POST['restaurant_id']) {
                                                                if (!empty($menu_detail)) {
                                                                    foreach ($menu_detail as $key => $value) { ?>
                                                                        <option value="<?php echo $value->entity_id ?>" data-id="<?php echo $value->price ?>" <?php echo ($value->entity_id == $_POST['item_id'][$i]) ? "selected" : "" ?>><?php echo $value->name ?></option>
                                                            <?php }
                                                                }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="qty_no[<?php echo $inc ?>]" id="qty_no<?php echo $inc ?>" value="<?php echo isset($_POST['qty_no'][$i]) ? $_POST['qty_no'][$i] : '' ?>" maxlength="3" data-required="1" onkeyup="qty(this.id,<?php echo $inc ?>)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" name="rate[<?php echo $inc ?>]" id="rate<?php echo $inc ?>" value="<?php echo isset($_POST['rate'][$i]) ? $_POST['rate'][$i] : '' ?>" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                    </div>
                                                    <div class="col-md-1 remove"><?php if ($inc > 1) { ?><div class="item-delete" onclick="deleteItem(<?php echo $inc ?>)"><i class="fa fa-remove"></i></div><?php } ?></div>
                                                </div>
                                            <?php } ?>
                                            <div id="Optionplus" onclick="cloneItem()">
                                                <div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="form-group">
                                            <?php for ($i = 0, $inc = 1; $i < count($menu_item); $inc++, $i++) { ?>
                                                <div class="clone" id="cloneItem<?php echo $inc ?>">
                                                    <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?><span class="required">*</span></label>
                                                    <div class="col-md-2">
                                                        <select name="item_id[<?php echo $inc ?>]" class="form-control item_id validate-class" id="item_id<?php echo $inc ?>" onchange="getItemPrice(this.id,<?php echo $inc ?>)">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php if ($entity_id) {
                                                                if (!empty($menu_detail)) {
                                                                    foreach ($menu_detail as $key => $value) { ?>
                                                                        <option value="<?php echo $value->entity_id ?>" data-id="<?php echo $value->price ?>" <?php echo ($value->entity_id == $menu_item[$i]->item_id) ? "selected" : "" ?>><?php echo $value->name ?></option>
                                                            <?php }
                                                                }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="qty_no[<?php echo $inc ?>]" id="qty_no<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->qty_no) ? $menu_item[$i]->qty_no : '' ?>" maxlength="3" data-required="1" onkeyup="qty(this.id,<?php echo $inc ?>)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" name="rate[<?php echo $inc ?>]" id="rate<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->rate) ? $menu_item[$i]->rate : '' ?>" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                    </div>
                                                    <div class="col-md-1 remove"><?php if ($entity_id && $inc > 1) { ?><div class="item-delete" onclick="deleteItem(<?php echo $inc ?>)"><i class="fa fa-remove"></i></div><?php } ?></div>
                                                </div>
                                            <?php } ?>
                                            <div id="Optionplus" onclick="cloneItem()">
                                                <div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div>
                                            </div>
                                        </div>
                                    <?php } ?>



                                    <div class="accordion" id="accordionExampleTwo">
                                        <div class="card" id="order_mode_content">
                                            <div class="card-header" id="headingTwo">
                                                <div class="card-header-title" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true">
                                                    <img src="<?php echo base_url(); ?>assets/front/images/order-mode.svg">
                                                    <h3><?php echo $this->lang->line('order_mode') ?></h3>
                                                </div>
                                            </div>
                                            <div id="collapseTwo" class="collapse in show" aria-labelledby="headingTwo" data-parent="#accordionExampleTwo">
                                                <div class="card-body">
                                                    <div class="choose-order-mode">
                                                        <div class="choose-order-title">
                                                            <h6 class="<?php echo ($mode_24 == '' && $pre_order_date) ? " hide" : "" ?>"><?php echo $this->lang->line('choose_order_mode') ?></h6>
                                                        </div>
                                                        <div class="order-mode">
                                                            <div class="card">
                                                                <div class="radio-btn-list">
                                                                    <label>
                                                                        <input type="hidden" name="subtotal" id="subtotal" value="<?php echo $cart_details['cart_total_price']; ?>">

                                                                        <input type="radio" name="choose_order" id="delivery_24" value="delivery_24" onclick="showDelivery24({lat:null,lng:null},<?php echo $cart_details['cart_total_price']; ?>);">
                                                                        <span class="<?php echo ($allow_24_delivery == "0") ? "hide" : ""  ?><?php echo ($mode_24 == '' && $pre_order_date) ? " hide" : "" ?> "><?php echo $this->lang->line('deliver_24') ?></span>
                                                                    </label>
                                                                    <!-- </div>
											                    	<div class="radio-btn-list"> -->
                                                                    <label>
                                                                        <input type="radio" name="choose_order" id="delivery" value="delivery" onclick="showDelivery({lat:null,lng:null},<?php echo $cart_details['cart_total_price']; ?>);">
                                                                        <span class="<?php echo ($mode_24 == true ? "hide" : "") ?><?php echo ($mode_24 == '' && $pre_order_date) ? " hide" : "" ?>"><?php echo $this->lang->line('delivery') ?></span>
                                                                    </label>
                                                                </div>
                                                                <div class="delivery-form display-no" id="delivery-form">
                                                                    <div class="current-location">
                                                                        <p><img src="<?php echo base_url(); ?>assets/front/images/current-location.svg"> <?php echo $this->lang->line('choose_delivery_address') ?></p>
                                                                    </div>
                                                                    <div class="radio-btn-list">
                                                                        <label>
                                                                            <input type="radio" name="add_new_address" value="add_new_address" class="add_new_address" onclick="showAddAdress();">
                                                                            <span><?php echo $this->lang->line('add_address') ?></span>
                                                                        </label>
                                                                    </div>
                                                                    <div id="add_address_content" class="display-no">
                                                                        <h5><?php echo $this->lang->line('add_address') ?></h5>
                                                                        <div class="login-details">
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="add_latitude" id="add_latitude">
                                                                                <input type="hidden" name="add_longitude" id="add_longitude">
                                                                                <span style="color: red; font-weight: bold; margin: 0 20px 0px 20px;"><?php echo $this->lang->line('delivery_area') ?></span>
                                                                                <input type="text" name="add_address_area" id="add_address_area" placeholder="ex: Analakely, Antananarivo, Madagascar" autocomplete="off" autofocus class="form-control">
                                                                                <span style="color: red; display: none" id="error-quarter"><?php echo $this->lang->line('valid_quarter') ?></span>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <div id="gmap_geocoding_adr" class="gmaps"></div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="add_address" id="add_address" class="form-control" placeholder=" " value="-">
                                                                                <label style="display: none;"><?php echo $this->lang->line('complete_address') ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <input type="text" name="landmark" id="landmark" class="form-control" placeholder="">
                                                                                <label><?php echo $this->lang->line('landmark') ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="zipcode" id="zipcode" class="form-control" placeholder=" " value="101">
                                                                                <label style="display: none;"><?php echo $this->lang->line('zipcode') ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="city" id="city" class="form-control" placeholder=" " value="Antananarive">
                                                                                <label style="display: none;"><?php echo $this->lang->line('city') ?></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('extra_comment') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="extra_comment" id="extra_comment" value="" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon') ?></label>
                                        <div class="col-md-4">
                                            <select name="coupon_id" class="form-control coupon_id" id="coupon_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if (!empty($coupon)) {
                                                    foreach ($coupon as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $coupon_id) ? "selected" : "" ?> amount="<?php echo $value->amount ?>" type="<?php echo $value->amount_type ?>"><?php echo $value->name ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_discount') ?></label>
                                        <div class="col-md-4">
                                            <input type="text" data-value="" name="coupon_amount" id="coupon_amount" value="<?php echo ($coupon_amount) ? $coupon_amount : '' ?>" maxlength="10" data-required="1" class="form-control" readonly="" /><label class="coupon-type"><?php echo ($coupon_type == 'Percentage') ? '%' : '' ?></label>
                                            <input type="hidden" name="coupon_type" id="coupon_type" value="<?php echo $coupon_type; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_tax_rate') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" data-value="" name="tax_rate" id="tax_rate" value="<?php echo $tax_rate ?>" maxlength="10" data-required="1" class="form-control" readonly="" /><label class="amount-type"><?php echo ($tax_rate == 'Percentage') ? '%' : '' ?></label>
                                            <input type="hidden" name="tax_type" id="tax_type" value="<?php echo $tax_type; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('sub_total') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="subtotal" id="subtotal" value="<?php echo ($subtotal) ? $subtotal : ''; ?>" maxlength="10" data-required="1" class="form-control" readonly="" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('delivery_charge') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="delivery_charge" id="delivery_charge" value="" onchange="calculation()" maxlength="10" data-required="1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('total_rate') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="total_rate" id="total_rate" value="<?php echo ($total_rate) ? $total_rate : ''; ?>" maxlength="10" data-required="1" class="form-control" readonly="" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('order_status') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="order_status" class="form-control" id="order_status">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php $order_status = order_status($this->session->userdata('language_slug'));
                                                foreach ($order_status as $key => $value) { ?>
                                                    <option value="<?php echo $key ?>" <?php echo ($order_status == $key) ? "selected" : "" ?>><?php echo $value ?></option>
                                                <?php  } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('date_of_order') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class='input-group date' id='datetimepicker' data-date-format="mm-dd-yyyy HH:ii P">
                                                <input size="16" type="text" name="order_date" class="form-control" id="order_date" value="<?php echo ($order_date) ? date('Y-m-d H:i', strtotime($order_date)) : '' ?>" readonly="">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name; ?>/view"><?php echo $this->lang->line('cancel') ?></a>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END VALIDATION STATES-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GMAP_API_KEY ?>&libraries=places"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/gmaps/gmaps.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
    const cart_total = '0';
    var geocoder = new google.maps.Geocoder();
    const coordinates = {
        lat: null,
        lng: null
    };
    var map = new GMaps({
        el: '#gmap_geocoding_adr',
        lat: -18.8876653,
        lng: 47.4423024,
        click: function(e) {
            updateMarker(e);
        }
    });

    function postGeocode(latitude, longitude, address) {
        return jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: BASEURL + 'v1/gmap_api/storegeocode',
            data: {
                'latitude': latitude,
                'longitude': longitude,
                'address': address
            },
        });
    }

    function geocodePositionAdr(pos, resolve) {
        // var geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            latLng: pos
        }, function(responses) {
            if (responses && responses.length > 0) {
                resolve(responses[1].formatted_address);
            } else {
                resolve(false);
                // return 'Cannot determine address at this location.';
            }
        });
    }

    // show delivery options
    function showDelivery(coordinates, cart_total) {
        handleDisplay('delivery-form', 'block');
        initAutocomplete('add_address_area', coordinates, cart_total);
        handleActionMap(-18.8876653, 47.4423024);
        jQuery(".add_new_address").prop('required', true);
        //getCoupons(cart_total_price,'delivery');
        $("#submit_order").attr("disabled", false);
        $("#delivery").prop("checked", true);
        $('#add_address_content').hide();
        $('#your_address_content').hide();
    }
    // shwo delivery 24 options
    function showDelivery24(coordinates, cart_total) {
        handleDisplay('delivery-form', 'block');
        initAutocomplete('add_address_area', coordinates, cart_total);
        handleActionMap(-18.8876653, 47.4423024);
        jQuery(".add_new_address").prop('required', true);
        // getCoupons(cart_total_price,'delivery');
        $("#submit_order").attr("disabled", false);
        $("#delivery_24").prop("checked", true);
        $('#add_address_content').hide();
        $('#your_address_content').hide();
    }

    function handleDisplay(id, val) {
        const elem = document.getElementById(id);
        if (elem) {
            elem.style.display = val;
        }
    }

    function initAutocomplete(id, coordinates, cart_total) {
        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById(id), {
                types: ['geocode'] //'geocode','address','establishment','regions','cities'
            });
        autocomplete.setComponentRestrictions({
            'country': ['mg']
        });
        autocomplete.setFields(['geometry']);

        let address = document.getElementById("add_address_area").value;
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            // var address = document.getElementById("add_address_area").value;
            // var test = /([a-zA-Z0-9]+)\,[\s]+Antananarivo, Madagascar/i.test(address);
            // if(!test) {
            //   // $('#error-quarter').css('display', 'inherit');
            // }else {
            //   $('#error-quarter').css('display', 'none'); 
            //  }
            if (place.geometry !== undefined) {
                coordinates.lat = place.geometry.location.lat();
                coordinates.lng = place.geometry.location.lng();
                _getLatLongCb(coordinates.lat, coordinates.lng, address, cart_total);
            } else {
                geocoder.geocode({
                    'address': address
                }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        _getLatLongCb(results[0].geometry.location.lat(), results[0].geometry.location.lng(), address, cart_total, true);
                    }
                });
            }
        });
    }

    var handleActionMap = function(lat, lng) {
        map.removeMarkers();
        map.setCenter(lat, lng);
        map.addMarker({
            lat: lat,
            lng: lng,
            draggable: true,
            dragend: function(event) {
                updateMarker(e);
            }
        });
    };



    function showAddAdress() {
        handleDisplay('add_address_content', 'block');
        jQuery("#add_address_area").prop('required', true);
        jQuery("#add_address").prop('required', true);
        jQuery("#landmark").prop('required', true);
        jQuery("#zipcode").prop('required', true);
        jQuery("#city").prop('required', true);
        handleDisplay('your_address_content', 'none');
    }

    function _getLatLongCb(latitude, longitude, address, cart_total, store = false) {
        // Get Delivery Charge
        getDeliveryCharges(latitude, longitude, "get", cart_total);
        $('#add_latitude').val(latitude);
        $('#add_longitude').val(longitude);
        if (typeof handleActionMap == "function") {
            handleActionMap(latitude, longitude);
        }
        if (store) {
            postGeocode(latitude, longitude, address);
        }
    }

    function getDeliveryCharges(latitude, longitude, action, cart_total) {
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getDeliveryCharge',
            data: {
                "lat": latitude,
                "long": longitude,
                "resto_id": restaurant.id
            },
            beforeSend: function() {
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#delivery_charge').val(response);
                calculation();
                $('#quotes-main-loader').hide();
                //   getCoupons(cart_total,'delivery');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(XMLHttpRequest);
                alert(errorThrown);
            }
        });
    }

    var updateMarker = function(e) {
        var adr = new Promise(function(resolve, reject) {
            coordinates.lat = e.latLng.lat();
            coordinates.lng = e.latLng.lng();
            geocodePositionAdr(e.latLng, resolve);
        });
        adr.then(function(value) {
            if (value) {
                $('#add_address_area').val(value);
                let address = document.getElementById("add_address_area").value;

                if (coordinates.lat === undefined || coordinates.lng === undefined) {
                    _getLatLongCb(coordinates.lat, coordinates.lng, address, cart_total);
                } else {
                    geocoder.geocode({
                        'address': address
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            _getLatLongCb(results[0].geometry.location.lat(), results[0].geometry.location.lng(), address, cart_total, true);
                        }
                    });
                }
            }
        });
    };

    jQuery(document).ready(function() {
        Layout.init(); // init current layout
    });
    $(function() {
        var date = new Date();
        $('#order_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            autoclose: true,
            startDate: date
        });
    });


    const restaurant = {
        id: 0
    };

    function updateRestaurantId(id) {
        console.log(id);
        restaurant.id = id;
    }

    //clone items
    function cloneItem() {
        var divid = $(".clone:last").attr('id');
        var getnum = divid.split('cloneItem');
        var oldNum = parseInt(getnum[1]);
        var newNum = parseInt(getnum[1]) + 1;
        newElem = $('#' + divid).clone().attr('id', 'cloneItem' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
        newElem.find('#item_id' + oldNum).attr('id', 'item_id' + newNum).attr('name', 'item_id[' + newNum + ']').attr('onchange', 'getItemPrice(this.id,' + newNum + ')').prop('selected', false).attr('selected', false).val('').removeClass('error');
        newElem.find('#rate' + oldNum).attr('id', 'rate' + newNum).attr('name', 'rate[' + newNum + ']').val('').removeClass('error');
        newElem.find('#qty_no' + oldNum).attr('id', 'qty_no' + newNum).attr('name', 'qty_no[' + newNum + ']').attr('onkeyup', 'qty(this.id,' + newNum + ')').val(1).removeClass('error');
        newElem.find('.error').remove();
        newElem.find('.clone-label').css('visibility', 'hidden');
        $(".clone:last").after(newElem);
        $('#cloneItem' + newNum + ' .remove').html('<div class="item-delete" onclick="deleteItem(' + newNum + ')"><i class="fa fa-remove"></i></div>');
    }

    function deleteItem(id) {
        $('#cloneItem' + id).remove();
        calculation();
    }
    //change coupon
    $('#coupon_id').change(function() {
        calculation();
    });
    //get items
    function getItemDetail(id, entity_id) {
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getItem',
            data: {
                'entity_id': entity_id,
            },
            success: function(response) {
                $('.item_id').empty().append(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        var element = $('#' + id).find('option:selected');
        var amount = element.attr("amount");
        var amount_type = element.attr("type");
        $('#tax_rate').val(amount).attr('data-value', amount_type);
        var sing = (amount_type == "Percentage") ? "%" : '';
        $('.amount-type').html(sing);
        $('.tax_type').val(amount_type);
        getCurrency(entity_id);
    }
    //get item price
    function getItemPrice(id, num) {
        var element = $('#' + id).find('option:selected');
        var myTag = element.attr("data-id");
        $('#rate' + num).val(myTag);
        calculation();
    }

    function qty(id, num) {
        $('#' + id).keyup(function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        var element = $('#item_id' + num).find('option:selected');
        var myTag = element.attr("data-id").replace('.', '');
        var qtydata = parseInt($('#qty_no' + num).val());
        if (isNaN(qtydata)) {
            qtydata = 0;
        }
        var total = parseInt(qtydata * myTag);
        if (!isNaN(total)) {
            $('#rate' + num).val(total);
        }
        calculation();
    }
    //calculate total rate
    function calculation() {
        var element = $('#coupon_id').find('option:selected');
        var type = element.attr("type");
        var amount = element.attr("amount");
        $('#coupon_amount').val(amount);
        $('#coupon_type').val(type);
        var sing = (type == "Percentage") ? "%" : '';
        $('.coupon-type').html(sing);
        var sum = 0;
        $('.rate').each(function() {
            if (!isNaN($(this).val()) && $(this).val() != '') {
                sum += parseInt($(this).val().replace('.', ''));
            }
        });
        $('#subtotal').val(sum);
        //tax
        var tax = $('#tax_rate').val();
        if ($('.amount-type').html() == '' && !isNaN(tax) && tax != '') {
            sum += parseInt(tax.replace('.', ''));
        } else if (!isNaN(tax) && tax != '') {
            var taxs = Math.round(parseInt(sum * tax) / 100);
            sum += parseInt(taxs);
        }
        //coupon
        if (type == 'Percentage' && amount != '') {
            var cpn = Math.round(parseInt(sum * amount) / 100);
            sum = sum - cpn;
        } else if (type == 'Amount' && amount != '') {
            sum = sum - amount;
        }
        if (!isNaN(sum)) {
            var fee = $('#delivery_charge').val();
            if (fee) {
                sum += parseInt(fee);
            }
            $('#total_rate').val(sum);
        }
    }
    //get address
    function getAddress(entity_id) {
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getAddress',
            data: {
                'entity_id': entity_id,
            },
            success: function(response) {
                console.log(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }


    //get delivery charge
    function getDeliveryCharge() {
        var resto_id = $('#restaurant_id option:selected').val();
        var address = $('#address_id option:selected').text();
        if (address != '') {
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode({
                'address': address
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var lat = results[0].geometry.location.lat();
                    var long = results[0].geometry.location.lng();

                    jQuery.ajax({
                        type: "POST",
                        dataType: "html",
                        url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getDeliveryCharge',
                        data: {
                            'resto_id': resto_id,
                            'lat': lat,
                            'long': long
                        },
                        success: function(response) {
                            $('#delivery_charge').val(response);
                            calculation();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                }
            });

        }
    }
    //validation for menu item
    $('#form_add_order').bind('submit', function(e) {
        $('.validate-class').each(function() {
            var id = $(this).attr('id');
            if ($('#' + id).val() == '') {
                $('#' + id).attr('required', true);
                $('#' + id).addClass('error');
            }
        });
    });

    function format_indonesia_currency(amt) {
        var number = amt;
        return n = number.toLocaleString('id-ID', {
            currency: 'IDR'
        });
    }
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>
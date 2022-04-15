<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','user_id','restaurant_id','address_id','coupon_id','tax_rate','order_status','order_date','total_rate','coupon_amount','coupon_type','tax_type','subtotal');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label     = $this->lang->line('title_admin_orderedit');        
    $form_action   = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    $address = $this->order_model->getAddress($user_id); 
}
else
{
    $add_label    = $this->lang->line('title_admin_orderadd');       
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
    $menu_item = 1;
}
$restaurant_id = isset($_POST['restaurant_id'])?$_POST['restaurant_id']:$restaurant_id;
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
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('order') ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $add_label;?> 
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
                            <div class="caption"><?php echo $add_label;?></div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->prefix ?>" name="form_add<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" style= "display: none;" class="frame-load">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading" />
                                </div>
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger"><?php echo $Error;?></div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <?php if($this->session->userdata('UserType') == 'MasterAdmin'){ ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('users') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id; ?>">
                                            <select name="user_id" class="form-control" id="user_id" onchange="getAddress(this.value)">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($user)){
                                                    foreach ($user as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $user_id)?"selected":"" ?>><?php echo $value->first_name.' ' .$value->last_name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div> 
                                    <?php }else{ ?>
                                        <input type="hidden" name="user_id" value="0" id="user_id">
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="restaurant_id" class="form-control" id="restaurant_id" onchange="getItemDetail(this.id,this.value)">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $restaurant_id)?"selected":"" ?> amount="<?php echo $value->amount ?>" type="<?php echo $value->amount_type ?>"><?php echo $value->name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div> 
                                    <?php if(isset($_POST['item_id'])){ ?>
                                        <div class="form-group">
                                            <?php for($i=1,$inc=1;$i<=count($_POST['item_id']);$inc++,$i++){ ?>
                                                <div class="clone" id="cloneItem<?php echo $inc ?>">
                                                    <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?><span class="required">*</span></label>
                                                    <div class="col-md-2">
                                                        <select name="item_id[<?php echo $inc ?>]" class="form-control item_id validate-class" id="item_id<?php echo $inc ?>" onchange="getItemPrice(this.id,<?php echo $inc ?>)">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option> 
                                                            <?php if($_POST['restaurant_id']){
                                                                if(!empty($menu_detail)){
                                                                foreach ($menu_detail as $key => $value) { ?>
                                                                    <option value="<?php echo $value->entity_id ?>" data-id="<?php echo $value->price ?>" <?php echo ($value->entity_id == $_POST['item_id'][$i])?"selected":"" ?>><?php echo $value->name ?></option>    
                                                            <?php } } }?> 
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="qty_no[<?php echo $inc ?>]" id="qty_no<?php echo $inc ?>" value="<?php echo isset($_POST['qty_no'][$i])?$_POST['qty_no'][$i]:'' ?>" maxlength="3" data-required="1" onkeyup="qty(this.id,<?php echo $inc ?>)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>"/>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" name="rate[<?php echo $inc ?>]" id="rate<?php echo $inc ?>" value="<?php echo isset($_POST['rate'][$i])?$_POST['rate'][$i]:'' ?>" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                    </div>
                                                    <div class="col-md-1 remove"><?php if($inc > 1){ ?><div class="item-delete" onclick="deleteItem(<?php echo $inc ?>)"><i class="fa fa-remove"></i></div><?php } ?></div>
                                                </div>
                                            <?php } ?>
                                            <div id="Optionplus" onclick="cloneItem()"><div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div></div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="form-group">
                                            <?php for($i=0,$inc=1;$i<count($menu_item);$inc++,$i++){ ?>
                                                <div class="clone" id="cloneItem<?php echo $inc ?>">
                                                    <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?><span class="required">*</span></label>
                                                    <div class="col-md-2">
                                                        <select name="item_id[<?php echo $inc ?>]" class="form-control item_id validate-class" id="item_id<?php echo $inc ?>" onchange="getItemPrice(this.id,<?php echo $inc ?>)">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option> 
                                                            <?php if($entity_id){
                                                                if(!empty($menu_detail)){
                                                                foreach ($menu_detail as $key => $value) { ?>
                                                                    <option value="<?php echo $value->entity_id ?>" data-id="<?php echo $value->price ?>" <?php echo ($value->entity_id == $menu_item[$i]->item_id)?"selected":"" ?>><?php echo $value->name ?></option>    
                                                            <?php } } }?> 
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="qty_no[<?php echo $inc ?>]" id="qty_no<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->qty_no)?$menu_item[$i]->qty_no:'' ?>" maxlength="3" data-required="1" onkeyup="qty(this.id,<?php echo $inc ?>)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>"/>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" name="rate[<?php echo $inc ?>]" id="rate<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->rate)?$menu_item[$i]->rate:'' ?>" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                    </div>
                                                    <div class="col-md-1 remove"><?php if($entity_id && $inc > 1){ ?><div class="item-delete" onclick="deleteItem(<?php echo $inc ?>)"><i class="fa fa-remove"></i></div><?php } ?></div>
                                                </div>
                                            <?php } ?>
                                            <div id="Optionplus" onclick="cloneItem()"><div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div></div>
                                        </div>
                                    <?php } ?>
                                     <?php if($this->session->userdata('UserType') == 'MasterAdmin'){ ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('address') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="address_id" class="form-control address-line" id="address_id" onChange="getDeliveryCharge()">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>  
                                                <?php if($entity_id){
                                                        if(!empty($address)){
                                                            foreach ($address as $key => $value) { ?>
                                                                <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $address_id)?"selected":"" ?>><?php echo $value->address.' , '.$value->city.' , '.$value->state.' , '.$value->country.' '.$value->zipcode ?></option>    
                                                <?php } } }?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php }else{ ?>
                                         <input type="hidden" name="address_id" value="0" id="address_id">
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon') ?></label>
                                        <div class="col-md-4">
                                            <select name="coupon_id" class="form-control coupon_id" id="coupon_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>  
                                                <?php if(!empty($coupon)){
                                                    foreach ($coupon as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $coupon_id)?"selected":"" ?> amount="<?php echo $value->amount ?>" type="<?php echo $value->amount_type ?>"><?php echo $value->name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_discount') ?></label>
                                        <div class="col-md-4">
                                            <input type="text" data-value="" name="coupon_amount" id="coupon_amount" value="<?php echo ($coupon_amount)?$coupon_amount:'' ?>" maxlength="10" data-required="1" class="form-control" readonly=""/><label class="coupon-type"><?php echo ($coupon_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="coupon_type" id="coupon_type" value="<?php echo $coupon_type; ?>">
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_tax_rate') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" data-value="" name="tax_rate" id="tax_rate" value="<?php echo $tax_rate ?>" maxlength="10" data-required="1" class="form-control" readonly=""/><label class="amount-type"><?php echo ($tax_rate == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="tax_type" id="tax_type" value="<?php echo $tax_type; ?>">
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('sub_total') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="subtotal" id="subtotal" value="<?php echo ($subtotal)?$subtotal:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('delivery_charge') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="delivery_charge" id="delivery_charge" value="" onchange="calculation()" maxlength="10" data-required="1" class="form-control"/>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('total_rate') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="total_rate" id="total_rate" value="<?php echo ($total_rate)?$total_rate:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('order_status') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="order_status" class="form-control" id="order_status">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php $order_status = order_status($this->session->userdata('language_slug'));
                                                foreach ($order_status as $key => $value) { ?>
                                                     <option value="<?php echo $key ?>" <?php echo ($order_status == $key)?"selected":"" ?>><?php echo $value ?></option>
                                                <?php  } ?>               
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('date_of_order') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class='input-group date' id='datetimepicker' data-date-format="mm-dd-yyyy HH:ii P">
                                            <input size="16" type="text" name="order_date" class="form-control" id="order_date" value="<?php echo ($order_date)?date('Y-m-d H:i',strtotime($order_date)):'' ?>" readonly="">
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
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"><?php echo $this->lang->line('cancel') ?></a>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GMAP_API_KEY ?>&libraries=places"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
$(function() {
    var date = new Date();
    $('#order_date').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        startDate : date
    });
});
//clone items
function cloneItem(){
    var divid = $(".clone:last").attr('id'); 
    var getnum = divid.split('cloneItem');
    var oldNum = parseInt(getnum[1]);
    var newNum = parseInt(getnum[1]) + 1;
    newElem = $('#' + divid).clone().attr('id', 'cloneItem' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
    newElem.find('#item_id'+oldNum).attr('id', 'item_id' + newNum).attr('name', 'item_id[' + newNum +']').attr('onchange','getItemPrice(this.id,'+newNum+')').prop('selected',false).attr('selected',false).val('').removeClass('error');
    newElem.find('#rate'+oldNum).attr('id', 'rate' + newNum).attr('name', 'rate[' + newNum +']').val('').removeClass('error');
    newElem.find('#qty_no'+oldNum).attr('id','qty_no'+newNum).attr('name','qty_no['+newNum+']').attr('onkeyup','qty(this.id,'+newNum+')').val(1).removeClass('error');
    newElem.find('.error').remove();
    newElem.find('.clone-label').css('visibility','hidden');
    $(".clone:last").after(newElem);
    $('#cloneItem' + newNum +' .remove').html('<div class="item-delete" onclick="deleteItem('+newNum+')"><i class="fa fa-remove"></i></div>');  
}
function deleteItem(id){
    $('#cloneItem'+id).remove();
    calculation();
}
//change coupon
$('#coupon_id').change(function(){
    calculation();
});
//get items
function getItemDetail(id,entity_id){
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getItem',
      data : {'entity_id':entity_id,},
      success: function(response) {
        $('.item_id').empty().append(response);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
    var element = $('#'+id).find('option:selected'); 
    var amount = element.attr("amount");
    var amount_type = element.attr("type");
    $('#tax_rate').val(amount).attr('data-value',amount_type);
    var sing = (amount_type == "Percentage") ? "%" : '';
    $('.amount-type').html(sing);
    $('.tax_type').val(amount_type);
    getCurrency(entity_id);
}
//get item price
function getItemPrice(id,num){
    var element = $('#'+id).find('option:selected'); 
    var myTag = element.attr("data-id"); 
    $('#rate'+num).val(myTag);
    calculation();
}
function qty(id,num) {
    $('#'+id).keyup(function(){
        this.value = this.value.replace(/[^0-9]/g,'');
    });
    var element = $('#item_id'+num).find('option:selected'); 
    var myTag = element.attr("data-id").replace('.','');
    var qtydata = parseInt($('#qty_no' + num).val());
    if(isNaN(qtydata)){    
        qtydata = 0;
    }
    var total = parseInt(qtydata * myTag);
    if(!isNaN(total)){    
        $('#rate'+num).val(total);
    }
    calculation();
}
//calculate total rate
function calculation()
{
    var element = $('#coupon_id').find('option:selected');
    var type = element.attr("type"); 
    var amount = element.attr("amount"); 
    $('#coupon_amount').val(amount);
    $('#coupon_type').val(type);
    var sing = (type == "Percentage") ? "%" : '';
    $('.coupon-type').html(sing);
    var sum = 0;
    $('.rate').each(function(){
        if(!isNaN($(this).val()) && $(this).val() != ''){
            sum += parseInt($(this).val().replace('.','')); 
        }
    });
    $('#subtotal').val(sum);
    //tax
    var tax = $('#tax_rate').val();
    if($('.amount-type').html() == '' && !isNaN(tax) && tax != ''){
        sum += parseInt(tax.replace('.','')); 
    }else if(!isNaN(tax) && tax != ''){
        var taxs = Math.round(parseInt(sum*tax)/100);
        sum += parseInt(taxs);
    }
    //coupon
    if(type == 'Percentage' && amount != ''){
        var cpn = Math.round(parseInt(sum*amount)/100);
        sum = sum - cpn;
    }else if(type == 'Amount' && amount != ''){
        sum = sum - amount;
    }
    if(!isNaN(sum)){
        var fee = $('#delivery_charge').val();
        if(fee) {
            sum += parseInt(fee);
        }
        $('#total_rate').val(sum);
    }
}
//get address
function getAddress(entity_id){
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getAddress',
      data : {'entity_id':entity_id,},
      success: function(response) {
        $('.address-line').empty().append(response);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}

//get delivery charge
function getDeliveryCharge(){
    var resto_id = $('#restaurant_id option:selected').val();
    var address = $('#address_id option:selected').text();
    if(address != '') {
        var geocoder = new google.maps.Geocoder();
    
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var lat = results[0].geometry.location.lat();
                var long = results[0].geometry.location.lng();

                jQuery.ajax({
                    type : "POST",
                    dataType :"html",
                    url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getDeliveryCharge',
                    data : {'resto_id':resto_id, 'lat': lat, 'long': long},
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
$('#form_add_order').bind('submit',function(e){
    $('.validate-class').each(function(){
        var id = $(this).attr('id');
        if($('#'+id).val() == ''){
            $('#'+id).attr('required',true);
            $('#'+id).addClass('error');
        }
    });
});
function format_indonesia_currency(amt) {
    var number = amt;       
    return  n =  number.toLocaleString('id-ID', {currency: 'IDR'});
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
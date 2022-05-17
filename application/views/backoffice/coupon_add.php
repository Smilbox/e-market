<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','shop_id','name','description','amount_type','amount','start_date','end_date','max_amount','coupon_type','image');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('title_admin_couponedit');        
    $user_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    $shop_map = array_column($shop_map, 'shop_id');
    $item_map = ($coupon_type == 'discount_on_combo')?array_column($item_map,'package_id'):array_column($item_map,'item_id');
    $itemDetail = $this->coupon_model->getItem($shop_map,$coupon_type);
}
else
{
    $add_label    = $this->lang->line('title_admin_couponadd');       
    $user_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
    $shop_map = array();
    $item_map = array();
    $itemDetail = array();
}

?>

<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('title_admin_coupon'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('title_admin_coupon'); ?></a>
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
                            <form action="<?php echo $user_action;?>" id="form_add<?php echo $this->prefix; ?>" name="form_add<?php echo $this->prefix; ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" class="display-no frame-load" style= "display: none;">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading"   />
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
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_type'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select name="coupon_type" class="form-control <?php echo ($entity_id)?'coupon_id_wrap':'' ?>" id="coupon_type" onchange="getCouponType(this.value)">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>  
                                                 <?php $coupon_types = coupon_type();
                                                    if(!empty($coupon_types)){
                                                    foreach ($coupon_types as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>" <?php echo ($key == $coupon_type)?'selected':'' ?>><?php echo $value ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="coupon_area enable_coupon">
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon'); ?><span class="required">*</span></label>
                                          <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id ?>">
                                          <input type="hidden" name="uploaded_image" value="<?php echo isset($image)?$image:''; ?>" />
                                          <div class="col-md-8">
                                               <input type="text" maxlength="249" onblur="checkExist(this.value)" class="form-control upper-text"  name="name" id="name" value="<?php echo $name ?>"/>
                                                <div id="phoneExist"></div>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('shop'); ?><span class="required">*</span></label>
                                          <div class="col-md-8">
                                              <select name="shop_id[]" multiple="" class="form-control" id="shop_id">
                                                   <?php if(!empty($shop)){
                                                      foreach ($shop as $key => $value) { ?>
                                                          <option value="<?php echo $value['entity_id'] ?>" <?php echo in_array($value['entity_id'], $shop_map)?'selected':'' ?>><?php echo $value['name'] ?></option>    
                                                  <?php } } ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group hidden-row" style="<?php echo ($coupon_type == 'free_delivery' || $coupon_type == 'user_registration' || $coupon_type == 'discount_on_cart')?'display:none':'' ?>">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('menu_item'); ?><span class="required">*</span></label>
                                          <div class="col-md-8">

                                              <select name="item_id[]" multiple="" class="form-control" id="item_id">
                                                  <?php if(!empty($itemDetail)){
                                                      foreach ($itemDetail as $key => $value) { ?>
                                                          <optgroup label="<?php echo $value[0]->shop_name ?>">
                                                          <?php foreach ($value as $k => $val) { ?>
                                                              <option value="<?php echo $val->entity_id ?>" <?php echo in_array($val->entity_id,$item_map)?'selected':'' ?>><?php echo $val->name ?></option>    
                                                          <?php } ?>
                                                          </optgroup>
                                                  <?php } } ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('description'); ?></label>
                                          <div class="col-md-8">
                                             <textarea name="description" id="description" class="form-control ckeditor"><?php echo $description ?></textarea>
                                          </div>
                                      </div>                      
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('image')  ?></label>
                                          <div class="col-md-4">
                                              <input type="file" name="image" id="image"/>
                                              <span class="help-block"><?php echo $this->lang->line('img_allow')  ?></span>
                                          </div>
                                          <div class="col-md-1">
                                              <?php if(isset($image) && $image != '') {?>
                                                  <img class="img-responsive" src="<?php echo base_url().'uploads/'.$image;?>">
                                              <?php } ?>
                                          </div>
                                      </div>
                                      <div class="form-group show-hidden-row" style="<?php echo ($coupon_type == 'free_delivery')?'display:none':'' ?>">  
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('discount_type'); ?></label>                        
                                          <div class="col-sm-4">
                                                  <p>
                                                    <input type="radio" name="amount_type" id="MPercentage"
                                                    <?php if (isset($amount_type) && $amount_type=="Percentage") echo "checked";?>
                                                    value="Percentage" checked="checked">&nbsp;&nbsp;<?php echo $this->lang->line('percentage'); ?>
                                                  </p>
                                                  <p>
                                                    <input type="radio" name="amount_type" id="MAmount"
                                                    <?php if (isset($amount_type) && $amount_type=="Amount") echo "checked";?>
                                                    value="Amount">&nbsp;&nbsp;<?php echo $this->lang->line('amount'); ?>
                                                  </p>
                                          </div>
                                      </div>
                                      <div class="form-group show-hidden-row" style="<?php echo ($coupon_type == 'free_delivery')?'display:none':'' ?>"> 
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('amount'); ?><span class="required">*</span></label>
                                          <div class="col-sm-8 form-markup">
                                                <label id="Percentage"><?php echo $this->lang->line('percentage'); ?> (%)</label>
                                                <label id="Amount" style="display:none"><?php echo $this->lang->line('amount'); ?> (Ar) </label>
                                                <br>
                                                <input type="text" name="amount" id="amount" value="<?php echo ($amount)?$amount:'' ?>" maxlength="19" data-required="1" class="form-control"/>  
                                          </div>  
                                      </div> 
                                      <div class="form-group"> 
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('min_amount'); ?><span class="required">*</span></label>
                                          <div class="col-sm-8 form-markup">
                                                <input type="text" name="max_amount" id="max_amount" value="<?php echo ($max_amount)?$max_amount:'' ?>" maxlength="19" data-required="1" class="form-control"/>  
                                          </div>  
                                      </div> 
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('start_date'); ?><span class="required">*</span></label>
                                          <div class="col-md-8">
                                                <input size="16" type="text" name="start_date" class="form-control" id="start_date" value="<?php echo ($start_date)?date('Y-m-d H:i',strtotime($start_date)):"" ?>" readonly="">
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('end_date'); ?><span class="required">*</span></label>
                                          <div class="col-md-8">
                                              <input size="16" type="text" name="end_date" class="form-control" id="end_date" value="<?php echo ($end_date)?date('Y-m-d H:i',strtotime($end_date)):"" ?>" readonly="">
                                          </div>
                                      </div> 
                                    </div>
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit'); ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL?>/coupon/view"><?php echo $this->lang->line('cancel'); ?></a>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});

$('#item_id').SumoSelect({selectAll:true});
$('#shop_id').SumoSelect({selectAll:true});

//check coupon exist
function checkExist(coupon){
    var entity_id = $('#entity_id').val();
    $.ajax({
    type: "POST",
    url: BASEURL+"backoffice/coupon/checkExist",
    data: 'coupon=' + coupon +'&entity_id='+entity_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#phoneExist').show();
        $('#phoneExist').html("<?php echo $this->lang->line('coupon_exist'); ?>");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#phoneExist').html("");
        $('#phoneExist').hide();        
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#phoneExist').show();
      $('#phoneExist').html(errorThrown);
    }
  });
}
// for datepicker
$(function() {
    $('#start_date').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
    });
     $('#end_date').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
    });
});
$("#amount,#max_amount").each(function(){
    $(this).keyup(function(){
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });
});
// Markup Radio Button Validation
function markup () {
  if($("input[name=amount_type]:checked").val() == "Percentage" ){
          $("#Amount").hide();
          $("#Percentage").show();     
  }else if($("input[name=amount_type]:checked").val() == "Amount" ){
          $("#Percentage").hide();
          $("#Amount").show();
  }
}
$(document).ready(function(){
   markup();
});
$("input[name=amount_type]:radio").click(function(){
  markup();
  if($("input[name=amount_type]:checked").val() == "Percentage" ){    
    $("#amount").val('');      
    $("#max_amount").attr('greater','');    
  }else if($("input[name=amount_type]:checked").val() == "Amount" ){
    $("#amount").val(''); 
    $("#max_amount").attr('greater','#amount');              
  }
});
//get coupon type
function getCouponType(value){
  $('.coupon_area').removeClass('enable_coupon');
  if(value == 'free_delivery'){
    $('.hidden-row').hide();
    $('.show-hidden-row').hide();
    $('#shop_id').val('');
    $('#shop_id')[0].sumo.reload();
    $('#shop_id').SumoSelect({selectAll:true});
  }else if(value == 'discount_on_cart' || value == 'user_registration'){
    $('.hidden-row').hide();
    $('.show-hidden-row').show();
    $('#shop_id').val('');
    $('#shop_id')[0].sumo.reload();
    $('#shop_id').SumoSelect({selectAll:true});
  }else{
    $('.hidden-row').show();
    $('.show-hidden-row').show();
    $('#amount').attr('required',true);
    $('#shop_id').val('');
    $('#shop_id')[0].sumo.reload();
    $('#shop_id').SumoSelect({search: true, searchText: 'Enter here.'});
  }
}
//get items of shop
$( document ).ready(function() {
  $('#shop_id').change(function (event) {
    var coupon_type = $('#coupon_type').val();
    var items = [];
    if($(this).val() != null){
      items.push($(this).val());
    }
    jQuery.ajax({
        type : "POST",
        dataType :"html",
        url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getItem',
        data : {'entity_id':items,'coupon_type':coupon_type},
        success: function(response) {
          $('#item_id').empty().append(response);
          $('#item_id')[0].sumo.reload();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
          alert(errorThrown);
        }
    });
  });
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
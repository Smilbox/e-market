<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('content_id','entity_id','restaurant_id','name','price','detail','availability','image');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('package');          
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit_package/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
  
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('package');          
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add_package/".$this->uri->segment('4');
  
}
?>

<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('package') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('package') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL?>/restaurant/view_package"><?php $this->lang->line('package') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->package_prefix ?>" name="form_add<?php echo $this->package_prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" class="frame-load display-no" style= "display: none;">
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
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                        <select name="restaurant_id" class="form-control" id="restaurant_id" onchange="getCurrency(this.value)">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php if(!empty($restaurant)){
                                                foreach ($restaurant as $key => $value) { ?>
                                                   <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $restaurant_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                            <?php } } ?>  
                                        </select></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('name') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                            <input type="text" name="name" id="name" value="<?php echo $name;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('price') ?> <span id="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="price" id="price" value="<?php echo ($price)?$price:'' ?>" maxlength="19" min="0" data-required="1" class="form-control"/>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('detail') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                           <textarea name="detail" id="detail" class="form-control ckeditor"><?php echo $detail ?></textarea>
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('availability') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <?php $availability = explode(',', @$availability); ?>
                                            <select name="availability[]" class="form-control" id="availability" multiple="">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>  
                                                <option value="Morning" <?php echo @in_array('Morning',$availability)?'selected':''; ?>><?php echo $this->lang->line('morning') ?></option>
                                                <option value="Lunch" <?php echo @in_array('Lunch',$availability)?'selected':''; ?>><?php echo $this->lang->line('lunch') ?></option>  
                                                <option value="Dinner" <?php echo @in_array('Dinner',$availability)?'selected':''; ?>><?php echo $this->lang->line('dinner') ?></option>  
                                            </select>
                                        </div>
                                    </div> 
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view_package"><?php echo $this->lang->line('cancel') ?></a>
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
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/repeater/jquery.repeater.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
<?php
$date = new DateTime();
?>
//add ons functionality
$('.add_ons').change(function(){
    if($(this).is(':checked')){
        $('.category_wrap').show();
        $('.repeater_field').attr('required',true);
        $('.repeater_field').addClass('error');
    }else{
        $('.category_wrap').hide();
        $('.repeater_field').val('');
        $('.delete_repeater').trigger('click');
        $('.category_checkbox').attr('checked',false);
        $('.repeater_wrap').hide();
        $('.repeater_field').attr('required',false);
        $('.repeater_field').removeClass('error');
        $('label.error').remove();
        $('.is_multiple').attr('checked',false);
    }
});
/*$('.form-horizontal').repeater({
    isFirstItemUndeletable: true,
    show: function () {
        var count = $('.outer-repeater').length;
        $(this).slideDown();
        $(this).find('.repeater_field').attr('required',true);
        $(this).find('.repeater_field').addClass('error');
        $(this).find('.title_repeater').attr('id','add_ons_title'+count);
        $(this).find('.add-inner-loop').attr('onclick','addinnerloop('+count+')');
    },
    repeaters: [{
        selector: '.inner-repeater'
    }],
});*/
window.outerRepeater = $('.category_wrap').repeater({
    isFirstItemUndeletable: true,
    show: function() {
        var count = $('.outer-repeater').length;
        $(this).slideDown();
        $(this).find('.repeater_field').attr('required',true);
        $(this).find('.repeater_field').addClass('error');
        $(this).find('.title_repeater').attr('id','add_ons_title'+count);
        $(this).find('.name_repeater').attr('required',true);
        $(this).find('.name_repeater').addClass('error');
        var time = $.now();
        $(this).find('.name_repeater').attr('id','add_ons_name'+time);
    },
    hide: function(deleteElement) {
      $(this).slideUp(deleteElement);
    },
    repeaters: [{
      isFirstItemUndeletable: true,
      selector: '.inner-repeater',
      show: function() {
        $(this).slideDown();
        $(this).find('.name_repeater').attr('required',true);
        $(this).find('.name_repeater').addClass('error');
        var times = $.now();
        $(this).find('.name_repeater').attr('id','add_ons_name'+times);
      },
      hide: function(deleteElement) {
        $(this).slideUp(deleteElement);
      }
    }]
  });
//add add ons
function addAddons(key,entity_id,id){
    if($('#'+id).is(':checked')){
        $('#add_ons_category'+key).show();
        $('.add_ons_category'+entity_id).find('.repeater_field').attr('required',true);
        $('.add_ons_category'+entity_id).find('.repeater_field').addClass('error');
    }else{
        $('#add_ons_category'+key).hide();
        $('.add_ons_category'+entity_id).find('.repeater_field').val('');
        $('.add_ons_category'+entity_id).find('.delete_repeater').trigger('click');
        $('.add_ons_category'+entity_id).find('.repeater_field').attr('required',false);
        $('.add_ons_category'+entity_id).find('.repeater_field').removeClass('error');
        $('#is_multiple'+entity_id).attr('checked',false);
        $('label.error').remove();
    }
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
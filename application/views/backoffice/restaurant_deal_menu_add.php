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
  $FieldsArray = array('content_id','entity_id','name','restaurant_id','category_id','price','menu_detail','availability','image','is_veg','check_add_ons');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('deal');        
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit_deal/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    $deal_count = count($add_ons_detail);
    $deal_detail = $add_ons_detail;
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('deal');       
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add_deal/".$this->uri->segment('4');
    $deal_count = 1;
    $deal_detail = array();
}

?>

<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php  echo $this->lang->line('deal') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL?>/restaurant/view_menu"><?php echo $this->lang->line('deal') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add_deal" name="form_add_deal" method="post" class="form-horizontal horizontal-form-deal" enctype="multipart/form-data" >
                                <div id="iframeloading" class="display-no frame-load">
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select name="restaurant_id" class="form-control" id="restaurant_id" onchange="getCurrency(this.value)">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                       <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $restaurant_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('category') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select name="category_id" class="form-control" id="category_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                 <?php if(!empty($category)){
                                                    foreach ($category as $key => $value) { ?>
                                                       <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $category_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('add_deals') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="check_add_ons" id="check_add_ons" value="1" <?php echo ($check_add_ons == 1)?'checked':'' ?> class="add_ons">
                                        </div>
                                    </div> 
                                    <div class="form-group category_wrap <?php echo ($check_add_ons == 1)?'display-yes':'display-no' ?>">
                                        <div data-repeater-list="outer-list">
                                          <?php if(!empty($deal_detail)){
                                           foreach($deal_detail as $key => $value){?>
                                            <div data-repeater-item class="outer-repeater">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('title') ?><span class="required">*</span></label>
                                                <div class="col-md-7">
                                                    <input type="text" name="add_ons_title[1]" id="add_ons_title1" value="<?php echo $value[0]->deal_category_name ?>" maxlength="249" class="form-control repeater_field title_repeater"/>
                                                    <input type="hidden" name="deal_category_id[2]" id="deal_category_id2" value="<?php echo $key ?>"/>
                                                    <!-- innner repeater -->
                                                    <div class="inner-repeater">
                                                      <div data-repeater-list="inner-list" class="inner-list">
                                                        <?php if(!empty($value)){
                                                                foreach ($value as $k => $val) { ?>
                                                                <div data-repeater-item >
                                                                    <div class="form-group">
                                                                        <label class="control-label col-md-3"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                        <div class="col-md-4">
                                                                            <input type="text" name="add_ons_name[]" id="add_ons_name" value="<?php echo $val->add_ons_name ?>" class="form-control repeater_field name_repeater" maxlength="249">
                                                                        </div> 
                                                                        <input data-repeater-delete class="btn btn-danger inner_delete" type="button" value="Delete"/> 
                                                                    </div> 
                                                                </div>
                                                        <?php } }else{ ?>
                                                            <div data-repeater-item >
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                    <div class="col-md-4">
                                                                        <input type="text" name="add_ons_name[]" id="add_ons_name" value="" class="form-control repeater_field name_repeater" maxlength="249">
                                                                    </div> 
                                                                    <input data-repeater-delete class="btn btn-danger inner_delete" type="button" value="Delete"/> 
                                                                </div> 
                                                            </div>
                                                        <?php } ?>
                                                      </div>
                                                      <input data-repeater-create class="btn btn-green add-inner-loop" type="button" value="Add"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <input data-repeater-delete class="btn btn-danger delete_repeater" type="button" value="Delete"/>
                                                </div>
                                            </div>
                                          <?php } }else{ ?>
                                            <div data-repeater-item class="outer-repeater">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('title') ?><span class="required">*</span></label>
                                                <div class="col-md-7">
                                                    <input type="text" name="add_ons_title[1]" id="add_ons_title1" value="" maxlength="249" class="form-control repeater_field title_repeater"/>
                                                    <!-- innner repeater -->
                                                    <div class="inner-repeater">
                                                      <div data-repeater-list="inner-list" class="inner-list">
                                                      
                                                        <div data-repeater-item >
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                <div class="col-md-4">
                                                                    <input type="text" name="add_ons_name[]" id="add_ons_name" value="" class="form-control repeater_field name_repeater" maxlength="249">
                                                                </div> 
                                                                <input data-repeater-delete class="btn btn-danger inner_delete" type="button" value="Delete"/> 
                                                            </div> 
                                                        </div>
                                               
                                                      </div>
                                                      <input data-repeater-create class="btn btn-green add-inner-loop" type="button" value="Add"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <input data-repeater-delete class="btn btn-danger delete_repeater" type="button" value="Delete"/>
                                                </div>
                                            </div>
                                          <?php } ?>
                                        </div>
                                        <input data-repeater-create class="btn btn-green" type="button" value="Add"/>
                                    </div>
                                    <div class="form-group price_tag">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('price') ?> <span id="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="price" id="price" value="<?php echo ($price)?$price:'' ?>" maxlength="19" data-required="1" class="form-control"/>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('detail') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="menu_detail" id="menu_detail" value="<?php echo $menu_detail;?>" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('image') ?></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="Image" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                </label>
                                                <input type="file" name="Image" id="Image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)"/>
                                            </div>
                                            <p class="help-block"><?php echo $this->lang->line('img_allow'); ?><br /> <?php echo $this->lang->line('max_file_size'); ?></p>
                                            <span class="error display-no" id="errormsg" ></span>
                                            <div id="img_gallery"></div>
                                            <img id="preview" height='100' width='150' class="display-no"/>
                                            <video controls class="display-no" id="v-control">
                                                <source id="source" src="" type="video/mp4">
                                            </video>
                                            <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image)?$image:''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($image) && $image != '') {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image'); ?></span>
                                                            <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$image;?>">
                                            <?php }  ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('popular_item'); ?></label>
                                        <div class="col-md-1">
                                           <input type="checkbox" name="popular_item" id="popular_item" value="1" <?php echo (isset($popular_item) && $popular_item == 1)?'checked':'' ?>/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('food_type'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="radio" name="is_veg" id="is_veg" value="1" checked="" <?php echo ($is_veg)?($is_veg== '1')?'checked':'':'checked' ?>>Veg
                                            <input type="radio" name="is_veg" id="non-veg" value="0" <?php echo ($is_veg == '0')?'checked':'' ?>>Non veg
                                        </div>
                                    </div>    
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('availability'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <?php $availability = explode(',', @$availability); ?>
                                            <select name="availability[]" class="form-control" id="availability" multiple="">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>  
                                                <option value="Morning" <?php echo @in_array('Morning',$availability)?'selected':''; ?>><?php echo $this->lang->line('morning'); ?></option>
                                                <option value="Lunch" <?php echo @in_array('Lunch',$availability)?'selected':''; ?>><?php echo $this->lang->line('lunch'); ?></option>  
                                                <option value="Dinner" <?php echo @in_array('Dinner',$availability)?'selected':''; ?>><?php echo $this->lang->line('dinner'); ?></option>  
                                            </select>
                                        </div>
                                    </div> 
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit'); ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view_menu"><?php echo $this->lang->line('cancel'); ?></a>
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
function readURL(input){
    var fileInput = document.getElementById('Image');
    var filePath = fileInput.value;
    var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    if(input.files[0].size <= 10506316){ // 10 MB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if(extension == 'mp4'){
                    $('#source').attr('src', e.target.result);
                    $('#v-control').show();
                    $('#preview').attr('src','').hide();
                }else{
                    $('#preview').attr('src', e.target.result).attr('style','display: inline-block;');
                    $('#v-control').hide();
                    $('#source').attr('src', '');
                }
                $("#old").hide();
                $('#errormsg').html('').hide();
            }
            reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#preview').attr('src', '').attr('style','display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('file_extenstion'); ?>").show();
            $('#Slider_image').val('');
            $("#old").show();
        }
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg'); ?>").show();
        $('#Slider_image').val('');
        $('#source').attr('src', '');
        $('#v-control').hide();
        $("#old").show();
    }
}
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
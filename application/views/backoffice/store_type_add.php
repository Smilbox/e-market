<?php $this->load->view(ADMIN_URL.'/header');?>
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');?>
<!-- END sidebar -->
<?php
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','name_fr','name_en','icon', 'banner');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('store-type');        
    $form_action      = base_url().ADMIN_URL."/".$this->controller_name."/edit/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('store-type');        
    $form_action      = base_url().ADMIN_URL."/".$this->controller_name."/add/".$this->uri->segment('4');
}?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('store-type') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('store-type') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add_<?php echo $this->prefix ?>" name="form_add_<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div class="form-body">                                     
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id;?>" />
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('name'); ?> EN<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="name_en" id="name_en" value="<?php echo $name_en;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('name'); ?> FR<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="name_fr" id="name_fr" value="<?php echo $name_fr;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Icon<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="Icon" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                </label>
                                                <input type="file" name="Icon" id="Icon" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL_icon(this)"/>
                                            </div>
                                            
                                            <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /> <?php echo $this->lang->line('max_file_size') ?></p>
                                            <span class="error display-no" id="errormsg_icon"><?php echo $this->lang->line('file_extenstion') ?></span>
                                            <div id="icon_img_gallery"></div>
                                            <img id="preview_icon" height='100' width='150' class="display-no"/>
                                            <input type="hidden" name="uploaded_icon_image" id="uploaded_icon_image" value="<?php echo isset($icon)?$icon:''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old_icon">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($icon) && $icon != '') {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                            <img id='oldpic_icon' height='100' width='150' class="img-responsive" src="<?php echo base_url().'uploads/'.$icon;?>">
                                            <?php }  ?>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Banner<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="Banner" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                </label>
                                                <input type="file" name="Banner" id="Banner" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL_banner(this)"/>
                                            </div>
                                            
                                            <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /> <?php echo $this->lang->line('max_file_size') ?></p>
                                            <span class="error display-no" id="errormsg_banner"><?php echo $this->lang->line('file_extenstion') ?></span>
                                            <div id="banner_img_gallery"></div>
                                            <img id="preview_banner" height='200' width='650' class="display-no"/>
                                            <input type="hidden" name="uploaded_banner_image" id="uploaded_banner_image" value="<?php echo isset($banner)?$banner:''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old_banner">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($banner) && $banner != '') {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                            <img id='oldpic_banner' class="img-responsive" src="<?php echo base_url().'uploads/'.$banner;?>">
                                            <?php }  ?>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="form-actions right">
                                    <a class="btn default" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('cancel') ?></a>
                                    <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn red"><?php echo $this->lang->line('submit') ?></button>
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
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
function readURL_icon(input) {
    var fileInput = document.getElementById('Icon');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1));
    if(input.files[0].size <= 5242880){ // 5 MB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview_icon').attr('src', e.target.result).attr('style','display: inline-block; object-fit: contain;');
                $("#old_icon").hide();
                $('#errormsg_icon').html('').hide();
            }
            reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#preview_icon').attr('src', '').attr('style','display: none;');
            $('#errormsg_icon').html('Invalid extention').show();
            $("#old_icon").show();
        }
    }else{
        $('#preview_icon').attr('src', '').attr('style','display: none;');
        $('#errormsg_icon').html('File size to large').show();
        $("#old_icon").show();
    }
}
function readURL_banner(input) {
    var fileInput = document.getElementById('Banner');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1));
    if(input.files[0].size <= 5242880){ // 5 MB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview_banner').attr('src', e.target.result).attr('style','display: inline-block; object-fit: contain');
                $("#old_banner").hide();
                $('#errormsg_banner').html('').hide();
            }
            reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#preview_banner').attr('src', '').attr('style','display: none;');
            $('#errormsg_banner').html('Invalid extension').show();
            $("#old_banner").show();
        }
    }else{
        $('#preview_banner').attr('src', '').attr('style','display: none;');
        $('#errormsg_banner').html('File size to large').show();
        $("#old_icon").show();
    }
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
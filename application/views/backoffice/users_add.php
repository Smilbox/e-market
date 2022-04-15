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
  $FieldsArray = array('entity_id','first_name','last_name','email','mobile_number','phone_number','user_type');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
$module =  ($user_type != 'Driver' && $this->uri->segment(4) != 'driver')?$this->lang->line('users'):$this->lang->line('driver');

if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$module;        
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label    = $this->lang->line('add').' '.$module;       
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
}
$usertypes = getUserTypeList($this->session->userdata('language_slug'));
?>

<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo ($user_type != 'Driver' && $this->uri->segment(4) != 'driver')?$this->lang->line('users'):$this->lang->line('driver') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo ($user_type != 'Driver' && $this->uri->segment(4) != 'driver')?'<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/view>'.$this->lang->line('users').'</a>':'<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/driver/>'.$this->lang->line('driver').'</a>' ?>
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
                                <div id="iframeloading" class="frame-load display-no" style= "display: none;">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading"/>
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
                                    <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" />
                                    <?php if($user_type != 'Driver' && $this->uri->segment(4) != 'driver'){ ?> 
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('first_name')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="first_name" id="first_name" value="<?php echo $first_name;?>" maxlength="249" data-required="1" class="form-control"/>
                                            </div>
                                        </div>      
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('last_name')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="last_name" id="last_name" value="<?php echo $last_name;?>" maxlength="249" data-required="1" class="form-control"/>
                                            </div>
                                        </div>  
                                    <?php }else{ ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('name')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="first_name" id="first_name" value="<?php echo $first_name;?>" maxlength="249" data-required="1" class="form-control"/>
                                            </div>
                                        </div>     
                                    <?php  } ?>
                                    <!-- <div class="form-group">
                                        <label class="control-label col-md-3"><?php //echo $this->lang->line('phone_number')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="phone_number" id="phone_number" value="<?php //echo $phone_number;?>" maxlength="20" data-required="1" class="form-control"/>
                                        </div>
                                    </div>   -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('phone_number')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" onblur="checkExist(this.value)" name="mobile_number" id="mobile_number" value="<?php echo $mobile_number;?>" data-required="1" class="form-control"/>
                                        </div>
                                        <div id="phoneExist"></div>
                                    </div> 
                                    <?php if($user_type != 'Driver' && $this->uri->segment(4) != 'driver'){ ?>  
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('email')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="email" name="email" id="email" onblur="checkEmail(this.value,'<?php echo $entity_id ?>')" value="<?php echo $email;?>" maxlength="99" data-required="1" class="form-control"/>
                                            </div>
                                            <div id="EmailExist"></div>
                                        </div>
                                    <?php } ?>
                                    <?php if($user_type == 'Driver' || $this->uri->segment(4) == 'driver'){ ?> 
                                        <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('user_type')?> <span class="required">*</span></label>
                                        <div class="col-md-4">
                                             <input type="text" name="user_type" id="user_type" value="Driver" readonly="" class="form-control">
                                        </div>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="form-group">   
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('user_type')?> <span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="user_type" id="user_type">
                                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                                    <?php foreach ($usertypes as $key => $value) {?>                                  
                                                        <option value="<?php echo $key;?>" <?php echo ($user_type==$key)?"selected":""?>><?php echo $value;?></option>    
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div> 
                                    <?php } ?>
                                    <?php if($entity_id){ ?>
                                        <h3><?php echo $this->lang->line('change_pass')?></h3>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('password')?> <?php echo ($entity_id)?'':'<span class="required">*</span>' ?></label>
                                        <div class="col-md-4">
                                            <input type="password" name="password" id="password" value="" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('confirm_pass')?><?php echo ($entity_id)?'':'<span class="required">*</span>' ?></label>
                                        <div class="col-md-4">
                                            <input type="password" name="confirm_password" id="confirm_password" value="" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    </div>    
                                    <div class="form-actions fluid">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                            <?php if($user_type != '' && $user_type != 'Driver'){?>
                                                <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"><?php echo $this->lang->line('cancel') ?></a>
                                            <?php }else{ ?>
                                                <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/driver"><?php echo $this->lang->line('cancel') ?></a>
                                            <?php } ?>
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
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
//check phone number exist
function checkExist(mobile_number){
    var entity_id = $('#entity_id').val();
    $.ajax({
    type: "POST",
    url: BASEURL+"<?php echo ADMIN_URL ?>/users/checkExist",
    data: 'mobile_number=' + mobile_number +'&entity_id='+entity_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#phoneExist').show();
        $('#phoneExist').html("<?php echo $this->lang->line('phone_exist'); ?>");        
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
// admin email exist check
function checkEmail(email,entity_id){
  $.ajax({
    type: "POST",
    url: BASEURL+"<?php echo ADMIN_URL ?>/users/checkEmailExist",
    data: 'email=' + email +'&entity_id='+entity_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#EmailExist').show();
        $('#EmailExist').html('<?php echo $this->lang->line('alredy_exist'); ?>');        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#EmailExist').html("");
        $('#EmailExist').hide();        
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#EmailExist').show();
      $('#EmailExist').html(errorThrown);
    }
  });
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
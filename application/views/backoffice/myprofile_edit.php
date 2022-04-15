<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<!-- END PAGE LEVEL STYLES -->
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
  $FieldsArray = array('entity_id','first_name','last_name','email','mobile_number','phone_number','user_type','phone_code');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($editUserDetail->$key);
  }
  $country = $this->common_model->getSelectedPhoneCode();
}?>   
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('my_profile'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard"><?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>                        
                        <li><?php echo $this->lang->line('my_profile'); ?></li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE header-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <ul id="myTab" class="nav nav-tabs">
                        <li <?php echo ($selected_tab == "" || $selected_tab == "UserInfo")?"class='active'":"";?>><a href="#UserInfo" data-toggle="tab"> <?php echo $this->lang->line('my_profile'); ?> </a></li>
                        <li <?php echo ($selected_tab == "ChangePassword")?"class='active'":"";?>><a href="#ChangePass" data-toggle="tab">  <?php echo $this->lang->line('change_pass'); ?> </a></li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade <?php echo ($selected_tab == "" || $selected_tab == "UserInfo")?"in active":"";?>" id="UserInfo">
                    <!-- BEGIN VALIDATION STATES-->
                        <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"> <?php echo $this->lang->line('my_profile'); ?> </div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo base_url().ADMIN_URL."/myprofile/getUserProfile";?>" id="form_edit_editor" name="form_edit_editor" method="post" class="form-horizontal isautovalid" enctype="multipart/form-data">
                                <div class="form-body">
                                    <?php if($this->session->flashdata('myProfileMSG')){?>
                                    <div class="alert alert-success">
                                        <strong><?php echo $this->lang->line('success');?></strong> <?php echo $this->session->flashdata('myProfileMSG');?>
                                    </div>
                                    <?php } ?>
                                    <?php if(validation_errors()){?>
                                        <div class="alert alert-danger"><?php echo validation_errors();?></div>
                                    <?php } ?>
                                    <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" />  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('first_name');?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="first_name" id="first_name" value="<?php echo $first_name;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>      
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('last_name');?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="last_name" id="last_name" value="<?php echo $last_name;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>    
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('phone_number');?></label>
                                        <div class="col-md-4">
                                            <input type="text" name="phone_number" id="phone_number" value="<?php echo $phone_number;?>" maxlength="20" data-required="1" class="form-control"/>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('mobile_number');?><span class="required">*</span></label>
                                        <div class="col-md-1">
                                            <input type="text" readonly="" name="phone_code" id="phone_code" value="<?php echo ($phone_code)?$phone_code:$country->OptionValue;?>" data-required="1" class="form-control phone_code_wrap"/>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" onblur="checkExist(this.value)" name="mobile_number" id="mobile_number" value="<?php echo $mobile_number;?>" maxlength="12" data-required="1" class="form-control"/>
                                        </div>
                                        <div id="phoneExist"></div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('email');?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="email" name="email" id="email" onblur="checkEmail(this.value,'<?php echo $entity_id ?>')" value="<?php echo $email;?>" maxlength="99" data-required="1" class="form-control"/>
                                        </div>
                                        <div id="EmailExist"></div>
                                    </div>      
                                </div>
                                <div class="form-actions fluid"> 
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submitEditUser" id="submitEditUser" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL?>/dashboard"><?php echo $this->lang->line('cancel') ?></a>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                        </div>
                    </div>
                        <div class="tab-pane fade <?php echo ($selected_tab == "ChangePassword")?"in active":"";?>" id="ChangePass">
                            <div class="portlet box red">
                                <div class="portlet-title">
                                    <div class="caption"> <?php echo $this->lang->line('change_pass') ?> </div>
                                </div>
                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->
                                    <form action="<?php echo base_url().ADMIN_URL."/myprofile/getUserProfile";?>" method="post" name="userChangePass" id="userChangePass" class="form-horizontal isautovalid" enctype="multipart/form-data">
                                        <div class="form-body">
                                            <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" /> 
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('password') ?><span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <input type="password" name="password" id="password" value="" maxlength="249" data-required="1" class="form-control"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('confirm_pass') ?><span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <input type="password" name="confirm_password" id="confirm_password" value="" maxlength="249" data-required="1" class="form-control"/>
                                                </div>
                                            </div>         
                                        </div>
                                        <div class="form-actions fluid">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="submit" name="ChangePassword" id="ChangePassword" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                                <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL?>/dashboard"><?php echo $this->lang->line('cancel') ?></a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END FORM-->
                                </div>
                            </div>                              
                        </div>
                    <!-- END VALIDATION STATES-->
                    </div>
                </div>
            </div>

            
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/pages/scripts/pwstrength.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script>
jQuery(document).ready(function() {           
    Layout.init(); // init current layout
    var options = {
        onLoad: function () {
            $('#messages').text('Start typing password');
        }
    };
    $('#Newpass').pwstrength(options);    
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
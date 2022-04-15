<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header_login');
parse_str(get_cookie('adminAuth'), $adminCook); // get Cookies ?>

<section class="content-area user-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 user-form">
                <div class="content-wrapper">
                    <div class="logo"> <a href="<?php echo base_url();?>"><img src="<?php echo base_url();?>assets/front/images/logo.png" alt="Logo"></a>
                    </div>
                    <h3><?php echo $this->lang->line('lets_get_started') ?></h3>
                    <div style="text-align: center;">
                    <div class="fb-login-button" data-size="large" data-button-type="login_with" data-layout="default" data-auto-logout-link="false" data-use-continue-as="true" data-width="" data-scope="email,public_profile" data-onlogin="checkLoginState()" ></div>
                        <p></p>
                        <h6>- <?php echo $this->lang->line('or') ?> -</h6>
                        <br/>
                    </div>
                    <form action="<?php echo base_url().'home/login';?>" id="form_front_login" name="form_front_login" method="post" class="form-horizontal float-form">
                        <div class="form-body">
                            <?php if(!empty($this->session->flashdata('error_MSG'))) {?>
                            <div class="alert alert-danger">
                                <?php echo $this->session->flashdata('error_MSG');?>
                            </div>
                            <?php } ?>
                            <?php if(validation_errors()){?>
                            <div class="alert alert-danger">
                                <?php echo validation_errors();?>
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <input type="number" name="phone_number" id="phone_number" class="form-control" placeholder=" " value="<?php echo $adminCook['usr'];?>">
                                <label><?php echo $this->lang->line('phone_number') ?></label>
                            </div>
                            <div class="form-group mb-0">
                                <input type="password" name="password" id="password" class="form-control" placeholder=" " value="<?php echo $adminCook['hash'];?>" >
                                <label><?php echo $this->lang->line('password') ?></label>
                            </div>
                            <div class="links text-right">
                                <div class="check-box">
                                    <label>
                                        <input type="checkbox" name="rememberMe" id="rememberMe" value="1" <?php echo ($adminCook)?"checked":""?> />
                                        <span><?php echo $this->lang->line('remember') ?></span>
                                    </label>
                                </div>
                                 <a href="" class="link" data-toggle="modal" data-target="#forgot-pass-modal"><?php echo $this->lang->line('forgot_pass') ?></a> 
                            </div>
                            <div class="action-button">
                                <button type="submit" name="submit_page" id="submit_page" value="Login" class="btn btn-primary"><?php echo $this->lang->line('title_login') ?></button>
                                <!-- <input type="submit" name="submit_page" id="submit_page" value="Login" class="btn btn-primary"> --> 
                                <a href="<?php echo base_url().'home/registration';?>" class="btn btn-secondary"><?php echo $this->lang->line('register') ?></a>
                                <!-- title_login -->
                            </div>                         
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 login-bg"></div>
        </div>
    </div>
</section>
<!--/ end content-area section -->
<!-- Modal -->
<div class="modal std-modal" tabindex="-1" role="dialog" id="forgot-pass-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $this->lang->line('forgot_password') ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span> </button>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-5 col-sm-12">
                    <div class="modal-body">
                        <div class="text-center forgot-image"> <img src="<?php echo base_url();?>assets/front/images/fp-popup-image.png" alt="Forgot Password Image"> </div>
                    </div>
                </div>
                <div class="col-md-7 col-sm-12">
                    <div class="modal-form">
                        <h2 class="text-left"><?php echo $this->lang->line('enter_reg_email') ?></h2>
                        <!-- action="<?php //echo base_url().'home/forgot_password';?>" -->
                        <form  id="form_front_forgotpass" name="form_front_forgotpass" method="post" class="form-horizontal float-form">
                            <div class="form-body">
                            <div class="alert alert-success display-no" id="forgot_success"></div>
                            <div class="alert alert-danger display-no" id="forgot_error"></div>
                            <?php if(validation_errors()){?>
                            <div class="alert alert-danger">
                                <?php echo validation_errors();?>
                            </div>
                            <?php } ?>
                                <div class="form-group">
                                    <input type="email" name="email_forgot" id="email_forgot" class="form-control" placeholder=" ">
                                    <label><?php echo $this->lang->line('email') ?></label>
                                </div>
                                <div class="action-button">                                    
                                    <button type="submit" name="forgot_submit_page" id="forgot_submit_page" value="Submit" class="btn red"><?php echo $this->lang->line('submit') ?></button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script src="<?php echo base_url();?>assets/front/js/scripts/admin-management-front.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/scripts/front-validations.js"></script>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
</body>
</html>
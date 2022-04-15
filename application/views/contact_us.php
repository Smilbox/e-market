<?php $this->load->view('header'); ?>

<section class="page-wrapper contact-us-wrapper">
    <div class="container">
        <div class="heading-title text-center">
            <h2><?php echo $this->lang->line('contact_us') ?></h2>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="contact-us-image">
                	<img src="<?php echo image_url.$contact_us[0]->image;?>" alt="Contact Us Image">
                </div>
                <?php if (!empty($contact_us)) { ?>
                    <div class=""><?php echo $contact_us[0]->description; ?></div>
                <?php } ?>
            </div>
            <div class="col-md-5">
            	<div class="contact-form">
            		<h2 class="text-center"><?php echo $this->lang->line('send_us_msg') ?></h2>
            		<form action="<?php echo base_url().'contact-us ';?>" id="form_front_contact_us" name="form_front_contact_us" method="post" class="form-horizontal float-form">
                        <div class="form-body"> 
                            <div class="contact-us-text">
                                <?php if(!empty($this->session->flashdata('contactUsMSG'))) {?>
                                <div class="alert alert-success">
                                    <?php echo $this->session->flashdata('contactUsMSG');?>
                                </div>
                                <?php } ?>
                                <?php if(!empty($success_msg)){?>
                                <div class="alert alert-success"><?php echo $success_msg;?></div>
                                <?php } ?>            
                                <?php if(!empty($Error)){?>
                                <div class="alert alert-danger"><?php echo $Error;?></div>
                                <?php } ?>                                  
                                <?php if(validation_errors()){?>
                                <div class="alert alert-danger">
                                    <?php echo validation_errors();?>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <input type="text" name="name" id="name" class="form-control" placeholder=" ">
                                <label><?php echo $this->lang->line('name') ?></label>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" id="email" class="form-control" placeholder=" ">
                                <label><?php echo $this->lang->line('email') ?></label>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="message" id="message" placeholder=" "></textarea>
                                <label><?php echo $this->lang->line('your_msg') ?></label>
                            </div>
                            <div class="action-button">
                                <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-primary btn-block"><?php echo $this->lang->line('submit') ?></button>
                            </div>
                        </div>
                    </form>
            	</div>
            </div>
        </div>
    </div>
</section>


<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script src="<?php echo base_url();?>assets/front/js/scripts/admin-management-front.js"></script>
<?php $this->load->view('footer'); ?>

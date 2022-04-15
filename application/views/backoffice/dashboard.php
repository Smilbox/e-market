<?php $this->load->view(ADMIN_URL.'/header');?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content admin-dashboard">          
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('dashboard') ?> <small><?php echo $this->lang->line('statistics') ?></small></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li><?php echo $this->lang->line('dashboard') ?> </li>                        
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div> 
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat red-intense">
                        <div class="visual">
                            <i class="fa fa-cutlery" aria-hidden="true"></i>
                        </div>
                        <div class="details">
                            <div class="number"><?php echo $restaurantCount ?></div>                           
                            <div class="desc"><?php echo $this->lang->line('total_restaurant') ?></div>
                        </div>
                        <a class="more" href="<?php echo base_url().ADMIN_URL ?>/restaurant/view">
                            <?php echo $this->lang->line('view_more') ?> <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div> 
                <?php if($this->session->userdata('UserType') == 'MasterAdmin'){?> 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat purple-plum">
                        <div class="visual">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="details">
                            <div class="number"> <?php echo $user['user_count'] ?></div>                           
                            <div class="desc"><?php echo $this->lang->line('total_user') ?></div>
                        </div>
                        <a class="more" href="<?php echo base_url().ADMIN_URL ?>/users/view">
                            <?php echo $this->lang->line('view_more') ?> <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
                <?php } ?> 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue-madison">
                        <div class="visual">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        </div>
                        <div class="details">
                            <div class="number"><?php echo $totalOrder ?></div>                           
                            <div class="desc"><?php echo $this->lang->line('total_order') ?></div>
                        </div>
                        <a class="more" href="<?php echo base_url().ADMIN_URL ?>/order/view">
                            <?php echo $this->lang->line('view_more') ?> <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div> 
            </div> 
            <?php if($this->session->userdata('UserType') == 'MasterAdmin'){?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption"><?php echo $this->lang->line('promotional_email') ?></div>
                                <div class="actions"></div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <form method="post" action="<?php echo base_url().ADMIN_URL ?>/dashboard" name="send_email" id="send_email">
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <select name="template_id" placeholder="<?php echo $this->lang->line('select_template') ?>" class="form-control" id="template_id">
                                                    <option value=""><?php echo $this->lang->line('select_template') ?></option>
                                                    <?php if(!empty($template)){
                                                        foreach ($template as $key => $value) { ?>
                                                           <option value="<?php echo $value->entity_id ?>"><?php echo $value->title ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <select name="user_id[]" placeholder="<?php echo $this->lang->line('select_users') ?>" multiple="multiple" class="form-control" id="user_id">
                                                    <?php if(!empty($user['users'])){
                                                        foreach ($user['users'] as $key => $value) { ?>
                                                           <option value="<?php echo $value->entity_id ?>"><?php echo $value->first_name.' '.$value->last_name ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-offset-3">
                                                <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                            </div>
                                        </div>       
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('restaurant') ?></div>
                            <div class="actions">
                                <a href="<?php echo base_url().ADMIN_URL?>/restaurant/view" class="btn default btn-xs purple-stripe"><?php echo $this->lang->line('view_all') ?></a>                                
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <table class="table table-hover">
                                    <thead>
                                    <tr> 
                                        <th>#</th>                                       
                                        <th><?php echo $this->lang->line('name') ?></th>
                                        <th><?php echo $this->lang->line('phone_number') ?></th>
                                        <th><?php echo $this->lang->line('email') ?></th>                                        
                                    </tr>                                    
                                    </thead>
                                    <tbody>
                                    <?php if(!empty($restaurant)){
                                        $i = 1;
                                        foreach  ($restaurant as $key => $value) { ?>
                                             <tr>
                                                 <td><?php echo $i; ?></td>
                                                 <td><?php echo $value->name; ?></td>
                                                 <td><?php echo $value->phone_number; ?></td>
                                                 <td><?php echo $value->email; ?></td>
                                             </tr>
                                         
                                    <?php $i++; } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('order') ?></div>
                            <div class="actions">
                                <a href="<?php echo base_url().ADMIN_URL?>/order/view" class="btn default btn-xs purple-stripe"><?php echo $this->lang->line('view_all') ?></a>                                
                            </div>                            
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('user') ?></th>
                                <th><?php echo $this->lang->line('order_total') ?></th>
                                <th><?php echo $this->lang->line('status') ?></th>
                                <th><?php echo $this->lang->line('date') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($orders)){
                                $i = 1;
                                foreach  ($orders as $key => $val) { 
                                    $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);                         ?>
                                     <tr>
                                         <td><?php echo $i; ?></td>
                                         <td><?php echo $val->fname.' '.$val->lname ?></td>
                                         <td><?php echo $currency_symbol->currency_symbol.number_format_unchanged_precision($val->rate,$currency_symbol->currency_code); ?></td>
                                         <td><?php echo ucfirst($val->ostatus); ?></td>
                                         <td><?php echo ($val->order_date)?date('d-m-Y',strtotime($val->order_date)):''; ?></td>
                                     </tr>
                                 
                            <?php $i++; } } ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>  
            </div>                          
        </div>            
        <div class="clearfix">
        </div>
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/index.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {    
    Metronic.init();
    Layout.init(); // init layout 
    $( "#user_id" ).SumoSelect({selectAll:true});
    $( "#template_id" ).SumoSelect();
});
</script>
<!-- END JAVASCRIPTS -->
<?php $this->load->view(ADMIN_URL.'/footer');?>
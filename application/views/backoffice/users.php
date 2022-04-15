<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                    <?php echo $this->lang->line('users_system')?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home')?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                           <?php echo $this->lang->line('users')?> </a>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->   
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('generate_report') ?></div>
                        </div>
                        <div class="portlet-body form">
                            <div class="form-body">
                                 <?php 
                                    if($this->session->flashdata('not_found'))
                                    {?>
                                        <div class="alert alert-danger">
                                             <?php echo $this->session->flashdata('not_found');?>
                                        </div>
                                    <?php } ?>
                                    <form action="<?php echo base_url().ADMIN_URL ?>/users/generate_report" id="users_generate_report" name="users_generate_report" method="post" class="horizontal-form" enctype="multipart/form-data" >
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo $this->lang->line('user_type') ?><span class="required">*</span></label>
                                                <select name="user_type" id="user_type" class="form-control required">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="Admin"><?php echo $this->lang->line('admin') ?></option>
                                                    <option value="User"><?php echo $this->lang->line('user') ?></option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" name="submitPage" id="submitPage" value="Generate" class="btn btn-success danger-btn btn-genrate"><?php echo $this->lang->line('submit') ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>           
            <div class="row">
                <div class="col-md-12">
                    <ul id="myTab" class="nav nav-tabs">
                        <li class="<?php echo ($this->uri->segment('4') != 'user_address')?'active':'' ?>"><a href="#user" data-toggle="tab"> <?php echo $this->lang->line('users')?></a></li>
                        <li class="<?php echo ($this->uri->segment('4') == 'user_address')?'active':'' ?>"><a href="#address" data-toggle="tab"> <?php echo $this->lang->line('address')?></a></li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade <?php echo ($this->uri->segment('4') != 'user_address')?'in active':'' ?>" id="user">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="page-content-wrapper">
                        <!-- BEGIN PAGE header-->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                                <h3 class="page-title">
                                <?php echo $this->lang->line('users')?>
                                </h3>
                                <!-- END PAGE TITLE & BREADCRUMB-->
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-md-12">    
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                <div class="portlet box red">
                                    <div class="portlet-title">
                                        <div class="caption"><?php echo $this->lang->line('users')?> <?php echo $this->lang->line('list') ?></div>
                                        <div class="actions">
                                            <a class="btn danger-btn btn-sm" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-container">
                                        <?php 
                                        if($this->session->flashdata('page_MSG'))
                                        {?>
                                            <div class="alert alert-success">
                                                 <?php echo $this->session->flashdata('page_MSG');?>
                                            </div>
                                        <?php } ?>
                                        <div id="delete-msg" class="alert alert-success hidden">
                                             <?php echo $this->lang->line('success_delete');?>
                                        </div>
                                            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th class="table-checkbox">#</th>
                                                        <th><?php echo $this->lang->line('users')?></th>
                                                        <th><?php echo $this->lang->line('phone_number')?></th>
                                                        <th><?php echo $this->lang->line('user_type')?></th>
                                                        <th><?php echo $this->lang->line('status')?></th>
                                                        <th><?php echo $this->lang->line('action')?></th>
                                                    </tr>
                                                    <tr role="row" class="filter">
                                                        <td></td>                                       
                                                        <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                                        <td><input type="text" class="form-control form-filter input-sm" name="phone"></td>
                                                        <td><input type="text" class="form-control form-filter input-sm" name="user_type"></td>
                                                        <td>
                                                            <select name="Status" class="form-control form-filter input-sm">
                                                                <option value=""><?php echo $this->lang->line('select')?></option>
                                                                <option value="1"><?php echo $this->lang->line('active')?></option>
                                                                <option value="0"><?php echo $this->lang->line('inactive')?></option>                                                
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="margin-bottom-5">
                                                                <button class="btn btn-sm  danger-btn filter-submit margin-bottom"><i class="fa fa-search"></i> <?php echo $this->lang->line('search')?></button>
                                                            </div>
                                                            <button class="btn btn-sm danger-btn filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset')?></button>
                                                        </td>
                                                    </tr>
                                                    </thead>                                        
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="tab-pane fade <?php echo ($this->uri->segment('4') == 'user_address')?'in active':'' ?>" id="address">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="page-content-wrapper">
                        <!-- BEGIN PAGE header-->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                                <h3 class="page-title">
                                    <?php echo $this->lang->line('address')?>
                                </h3>
                                <!-- END PAGE TITLE & BREADCRUMB-->
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-md-12">    
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                <div class="portlet box red">
                                    <div class="portlet-title">
                                        <div class="caption"><?php echo $this->lang->line('address')?> <?php echo $this->lang->line('list') ?></div>
                                        <div class="actions">
                                            <a class="btn danger-btn btn-sm" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/add_address"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-container">
                                        <?php 
                                        if($this->session->flashdata('add_page_MSG'))
                                        {?>
                                            <div class="alert alert-success">
                                                <strong>Success!</strong> <?php echo $this->session->flashdata('add_page_MSG');?>
                                            </div>
                                        <?php } ?>
                                        <div id="delete-msg" class="alert alert-success hidden">
                                            <strong>Success!</strong> <?php echo $this->lang->line('success_delete');?>
                                        </div>
                                            <table class="table table-striped table-bordered table-hover" id="address_ajax">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th class="table-checkbox">#</th>
                                                        <th><?php echo $this->lang->line('user') ?></th>
                                                        <th><?php echo $this->lang->line('address') ?></th>
                                                        <th><?php echo $this->lang->line('action') ?></th>
                                                    </tr>
                                                    <tr role="row" class="filter">
                                                        <td></td>                                       
                                                        <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                                        <td><input type="text" class="form-control form-filter input-sm" name="address"></td>
                                                        <td><div class="margin-bottom-5">
                                                                <button class="btn btn-sm  danger-btn filter-submit margin-bottom"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                                                            </div>
                                                            <button class="btn btn-sm danger-btn filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset') ?></button>
                                                        </td>
                                                    </tr>
                                                    </thead>                                        
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script>
var grid;
var gridaddress;
jQuery(document).ready(function() {           
    Layout.init(); // init current layout    
    //for address datatable
    gridaddress = new Datatable();
    grid = new Datatable();
    gridaddress.init({
        src: $("#address_ajax"),
        onSuccess: function(gridaddress) {
            // execute some code after table records loaded
        },
        onError: function(gridaddress) {
            // execute some code on network or other general error  
        },
        dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
            "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
           "aoColumns": [
                { "bSortable": false },
                null,
                null,
                { "bSortable": false }
              ],
            "sPaginationType": "bootstrap_full_number",
            <?php if($this->session->userdata("language_slug")=='ar'){ ?>
              "oLanguage":{
                "sProcessing": '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;جارٍ التحميل...</span>',
                "sLengthMenu": "أظهر _MENU_ مدخلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                "sInfoEmpty": "لم يتم العثور على أي سجلات",
                "sGroupActions": "_TOTAL_ records selected:  ",
                "sAjaxRequestGeneralError": "لا يمكن إكمال الطلب. الرجاء التحقق من اتصال الانترنت الخاص بك",
                "sEmptyTable":  "لا توجد بيانات متاحة في الجدول",
                "sZeroRecords": "لم يتم العثور على سجلات متطابقة",
                "oPaginate": {
                   "sFirst":    "الأول",
                    "sPrevious": "السابق",
                    "sNext":     "التالي",
                    "sLast":     "الأخير"
                }
              },
             <?php } else if($this->session->userdata("language_slug")=='fr') { ?>
            "oLanguage":{
                "sProcessing": '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Chargement...</span>',
                "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
                "sInfo": "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                "sInfoEmpty": "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
                "sGroupActions": "_TOTAL_ records selected:  ",
                "sAjaxRequestGeneralError": "Impossible de terminer la demande. S'il vous plait, vérifiez votre connexion internet",
                "sEmptyTable":  "Aucune donn&eacute;e disponible dans le tableau",
                "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
                "oPaginate": {
                   "sFirst":    "Premier",
                    "sPrevious": "Pr&eacute;c&eacute;dent",
                    "sNext":     "Suivant",
                    "sLast":     "Dernier"
                }
            },
            <?php }else{ ?>
            "oLanguage": {  // language settings
                "sProcessing": '<img src="' + Metronic.getGlobalImgPath() + 'loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Loading...</span>',
                "sLengthMenu": "_MENU_ records",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                "sInfoEmpty": "No records found to show",
                "sGroupActions": "_TOTAL_ records selected:  ",
                "sAjaxRequestGeneralError": "Could not complete request. Please check your internet connection",
                "sEmptyTable":  "No data available in table",
                "sZeroRecords": "No matching records found",
                "oPaginate": {
                    "sPrevious": "Prev",
                    "sNext": "Next",
                    "sPage": "Page",
                    "sPageOf": "of"
                }
            },
            <?php } ?>
            "bServerSide": true, // server side processing
            "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/ajaxViewAddress", // ajax source
            "aaSorting": [[ 3, "desc" ]] // set first column as a default sort by asc
        }
    });            
    
    
    grid.init({
        src: $("#datatable_ajax"),
        onSuccess: function(grid) {
            // execute some code after table records loaded
        },
        onError: function(grid) {
            // execute some code on network or other general error  
        },
        dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
            "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
           "aoColumns": [
                { "bSortable": false },
                null,
                null,
                null,
                null,
                { "bSortable": false }
              ],
            "sPaginationType": "bootstrap_full_number",
            "oLanguage":{
                "sProcessing": sProcessing,
                "sLengthMenu": sLengthMenu,
                "sInfo": sInfo,
                "sInfoEmpty":sInfoEmpty,
                "sGroupActions":sGroupActions,
                "sAjaxRequestGeneralError": sAjaxRequestGeneralError,
                "sEmptyTable": sEmptyTable,
                "sZeroRecords":sZeroRecords,
                "oPaginate": {
                    "sPrevious": sPrevious,
                    "sNext": sNext,
                    "sPage": sPage,
                    "sPageOf":sPageOf,
                    "sFirst": sFirst,
                    "sLast": sLast
                }
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/ajaxview", // ajax source
            "aaSorting": [[ 3, "desc" ]] // set first column as a default sort by asc
        }
    });            
    $('#datatable_ajax_filter').addClass('hide');
    $('#datatable_ajax input.form-filter, select.form-filter').keydown(function(e) 
    {
        if (e.keyCode == 13) 
        {
            grid.addAjaxParam($(this).attr("name"), $(this).val());
            grid.getDataTable().fnDraw(); 
        }
    });
    
    $('#address_ajax_filter').addClass('hide');
    $('#address_ajax input.form-filter, select.form-filter').keydown(function(e) 
    {
        if (e.keyCode == 13) 
        {
            gridaddress.addAjaxParam($(this).attr("name"), $(this).val());
            gridaddress.getDataTable().fnDraw(); 
        }
    });
    
});

// method for active/deactive 
function disableDetail(entity_id,status)
{
    var StatusVar = (status==0)?"<?php echo $this->lang->line('active_module'); ?>":"<?php echo $this->lang->line('deactive_module'); ?>";
    bootbox.confirm({
        message: StatusVar,
        buttons: {
            confirm: {
                label: '<?php echo $this->lang->line('ok'); ?>',
            },
            cancel: {
                label: '<?php echo $this->lang->line('cancel'); ?>',
            }
        },
        callback: function (disableConfirm) { 
            if (disableConfirm) {
                jQuery.ajax({
                  type : "POST",
                  dataType : "json",
                  url : 'ajaxdisable',
                  data : {'entity_id':entity_id,'status':status},
                  success: function(response) {
                       grid.getDataTable().fnDraw(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
// method for deleting user
function deleteDetail(entity_id)
{   
    bootbox.confirm({
        message: "<?php echo $this->lang->line('delete_module'); ?>",
        buttons: {
            confirm: {
                label: '<?php echo $this->lang->line('ok'); ?>',
            },
            cancel: {
                label: '<?php echo $this->lang->line('cancel'); ?>',
            }
        },
        callback: function (deleteConfirm) {         
            if (deleteConfirm) {
                jQuery.ajax({
                  type : "POST",
                  dataType : "html",
                  url : 'ajaxDelete',
                  data : {'entity_id':entity_id,'table':'users'},
                  success: function(response) {
                    grid.getDataTable().fnDraw();
                    gridaddress.getDataTable().fnDraw();  
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
// method for deleting user address
function deleteAddress(entity_id)
{   
    bootbox.confirm({
        message: "<?php echo $this->lang->line('delete_module'); ?>",
        buttons: {
            confirm: {
                label: '<?php echo $this->lang->line('ok'); ?>',
            },
            cancel: {
                label: '<?php echo $this->lang->line('cancel'); ?>',
            }
        },
        callback: function (deleteConfirm) {         
            if (deleteConfirm) {
                jQuery.ajax({
                  type : "POST",
                  dataType : "html",
                  url : 'ajaxDelete',
                  data : {'entity_id':entity_id,'table':'user_address'},
                  success: function(response) {
                    gridaddress.getDataTable().fnDraw(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
                });
            }
        }
    });
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
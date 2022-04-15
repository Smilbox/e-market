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
                        <?php echo $this->lang->line('menu') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('menu') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>       

            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('menu') ?></div>
                        </div>
                        <div class="portlet-body form">
                            <div class="form-body">
                                    <?php if($this->session->flashdata('Import_Error')) {?>
                                        <div class="alert alert-danger">
                                             <?php echo $this->session->flashdata('Import_Error');?>
                                        </div>
                                    <?php } ?>
                                    <?php if(validation_errors()){?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors();?>
                                        </div>
                                    <?php } ?>
                                    <form action="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/import_menu" id="form_add_import" name="form_add_import" method="post" class="horizontal-form" enctype="multipart/form-data" >
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo $this->lang->line('menu_file') ?><span class="required">*</span></label>
                                                <input type="file" name="import_tax" id="import_tax" accept=".xlsx, .rtf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"> 
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('import_menu'); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>    
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('menu') ?></div>
                            <div class="actions c-dropdown">                                     
                                <button type="button" class="btn btn-sm danger-btn dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false"><?php echo $this->lang->line('add') ?><span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php foreach ($Languages as $lang) {?>
                                    <li><a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name.'/add_menu/'.$lang->language_slug?>"><?php echo $lang->language_name?></a></li>
                                    <?php } ?>
                                </ul>
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
                                            <th><?php echo $this->lang->line('title') ?></th>
                                            <?php foreach ($Languages as $lang) {?>
                                                <th><?php echo $lang->language_slug;?></th>
                                            <?php } ?>
                                            <th><?php echo $this->lang->line('price') ?></th>
                                            <th><?php echo $this->lang->line('res_name') ?></th>
                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>                                       
                                            <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                            
                                            <?php foreach ($Languages as $lang) {?>
                                                <td><input type="text" class="form-control form-filter input-sm" disabled="" name="<?php echo $lang->language_slug;?>"></td>
                                            <?php } ?> 
                                            <td><input type="text" class="form-control form-filter input-sm" name="price"></td>   
                                            <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td> 
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
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
var grid;
jQuery(document).ready(function() {           
    Layout.init(); // init current layout   
    
    grid = new Datatable();
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
                { "bSortable": false },
                <?php foreach ($Languages as $lang) {?>
                { "bSortable": false },
                <?php } ?>
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
            "sAjaxSource": "ajaxviewMenu", // ajax source
            "aaSorting": [[ 6, "desc" ]] // set first column as a default sort by asc
        }
    });            
    $('#datatable_ajax_filter').addClass('hide');
    $('input.form-filter, select.form-filter').keydown(function(e) 
    {
        if (e.keyCode == 13) 
        {
            grid.addAjaxParam($(this).attr("name"), $(this).val());
            grid.getDataTable().fnDraw(); 
        }
    });
});

// method for deleting
function deleteDetail(entity_id,content_id)
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
                  data : {'tblname':'restaurant_menu_item','entity_id':entity_id,'content_id':content_id},
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
function deleteAll(content_id)
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
                dataType : "json",
                url : 'ajaxDeleteAll',
                data : {'tblname':'restaurant_menu_item','content_id':content_id},
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

function disable_record(entity_id,Status)
{
    var StatusVar = (Status==0)?'<?php echo $this->lang->line('active'); ?>':'<?php echo $this->lang->line('inactive'); ?>';
    bootbox.confirm({
        message: "<?php echo $this->lang->line('alert_msg'); ?> "+StatusVar+"?",
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
                url : 'ajaxDisable',
                data : {'entity_id':entity_id,'status':Status,'tblname':'restaurant_menu_item'},
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

function disableAll(ContentID,Status)
{
    var StatusVar = (Status==0)?'<?php echo $this->lang->line('active'); ?>':'<?php echo $this->lang->line('inactive'); ?>';
    bootbox.confirm({
        message: "<?php echo $this->lang->line('alert_msg'); ?> "+StatusVar+"?",
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
                url : 'ajaxDisableAll',
                data : {'content_id':ContentID,'status':Status,'tblname':'restaurant_menu_item'},
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
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
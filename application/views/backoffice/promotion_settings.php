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
                       <?php echo $this->lang->line('promotion_settings') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url()?>dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                                <i class="fa fa-angle-right"></i>
                            </li>
                            <li>
                                <?php echo $this->lang->line('promotion_settings') ?>
                            </li>
                        </ul>
                        <!-- END PAGE TITLE & BREADCRUMB-->
                    </div>
                    <?php if($this->session->flashdata('page_MSG')){?>
                        <div class="alert alert-success">
                            <?php echo $this->session->flashdata('page_MSG');?>
                        </div>
                    <?php } ?>
                </div>  
                
                <div class="row">
                    <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet box red">
                                <div class="portlet-title">
                                    <div class="caption">Banner</div>
                                </div>
                                <div class="portlet-body form">
                                <form action="<?php echo base_url().ADMIN_URL."/".$this->controller_name."/editbanner";?>" id="form_add_editbanner" name="form_edi_banner" method="post" class="form">
                                    <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label class="control-label"><?php echo $this->lang->line('show_banner') ?></label>
                                                        <br />
                                                        <input type="radio" <?php echo ($banner_settings->show_banner)?($banner_settings->show_banner == '1')?'checked':'':'checked' ?>  name="show_banner" class="show_banner" id="radioTrue" value="1"> <label for="radioTrue"><?php echo $this->lang->line('yes') ?></label>
                                                        <input type="radio" <?php echo ($banner_settings->show_banner == '0')?'checked':'' ?>  name="show_banner" class="show_banner" id="radioFalse" value="0"> <label for="radioFalse"><?php echo $this->lang->line('no') ?></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Banner text (EN)</label>
                                                        <input  type="text" name="text_banner_en" id="text_banner_en" value="<?php echo $banner_settings->text_en ?>" class="form-control"> 
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Banner text (FR)</label>
                                                        <input  type="text" name="text_banner_fr" id="text_banner_fr" value="<?php echo $banner_settings->text_fr ?>" class="form-control"> 
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Banner link</label>
                                                        <input type="text" name="link_banner" id="link_banner" value="<?php echo $banner_settings->link ?>" class="form-control"> 
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                <button type="submit" class="btn btn-success danger-btn btn-genrate"><?php echo $this->lang->line('submit') ?></button>
                                                </div>
                                            </div>
                                    </div>
                                </form>    
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
                                <div class="caption"><?php echo $this->lang->line('promotion_settings') ?></div>
                                <div class="actions c-dropdown">                                     
                                    <a class="btn danger-btn btn-sm" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                   
                                    <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                        <thead>
                                            <tr role="row" class="heading">
                                                <th class="table-checkbox">#</th>
                                                <th><?php echo $this->lang->line('res_name') ?></th>
                                                <th>Display on</th>
                                                <th>Priority order</th>
                                                <th>Image</th>
                                                <th><?php echo $this->lang->line('action') ?></th>
                                            </tr>
                                            <tr role="row" class="filter">
                                                <td></td>  
                                                <td><input type="text" class="form-control form-filter input-sm" name="restaurant_name"></td>                                     
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                
                                                <td>                                                
                                                    <button class="btn btn-sm red filter-submit"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                                                    <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset') ?></button>                                                
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
            "sAjaxSource": "ajaxview", // ajax source
            "aaSorting": [[ 3, "desc" ]] // set first column as a default sort by asc
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
                  url : 'ajaxDeleteAll',
                  data : {'entity_id':entity_id},
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
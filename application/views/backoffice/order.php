<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/datepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/order_detail.css"/>

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
                        <?php echo $this->lang->line('order') ?> <?php echo $this->lang->line('list') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('order') ?>
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
                            <div class="caption"><?php echo $this->lang->line('generate_report') ?></div>
                        </div>
                        <div class="portlet-body form">
                            <div class="form-body">
                                <?php if($this->session->flashdata('not_found')) {?>
                                    <div class="alert alert-danger">
                                         <?php echo $this->session->flashdata('not_found');?>
                                    </div>
                                <?php } ?>
                                    <form action="<?php echo base_url().ADMIN_URL ?>/order/generate_report" id="generate_report" name="generate_report" method="post" class="horizontal-form" enctype="multipart/form-data" >
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo $this->lang->line('shop') ?><span class="required">*</span></label>
                                                <select name="shop_id" id="shop_id" class="form-control required">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>

                                                    <?php if(!empty($shop)){
                                                    foreach ($shop as $key => $value) { ?>
                                                         <option value="<?php echo $value->entity_id ?>"><?php echo $value->name ?></option>
                                                    <?php  } } ?>                           
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo $this->lang->line('order_type') ?></label>
                                                <select name="order_delivery" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="Delivery"><?php echo $this->lang->line('delivery') ?></option>
                                                    <option value="24H Delivery"><?php echo $this->lang->line('deliver_24') ?></option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo $this->lang->line('order_date') ?></label>
                                                <input type="text" class="form-control date-picker" readonly name="order_date" id="order_date" placeholder="<?php echo $this->lang->line('order_date') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
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
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('order') ?> <?php echo $this->lang->line('list') ?></div>
                            <div class="actions">
                                <a class="btn danger-btn btn-sm" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                <button class="btn danger-btn btn-sm" id="delete_order"><i class="fa fa-times"></i> <?php echo $this->lang->line('delete') ?></button>
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
                                            <th class="table-checkbox"><input type="checkbox" class="group-checkable"></th>
                                            <th><?php echo $this->lang->line('order') ?>#</th>
                                            <th><?php echo $this->lang->line('shop') ?></th>
                                            <th><?php echo $this->lang->line('user') ?></th>
                                            <th><?php echo $this->lang->line('order_total') ?></th>
                                            <th><?php echo $this->lang->line('order_assign') ?></th>
                                            <th><?php echo $this->lang->line('order_status') ?></th>
                                            <th><?php echo $this->lang->line('order_date') ?></th>
                                            <th><?php echo $this->lang->line('pre-order-date') ?></th>
                                            <th><?php echo $this->lang->line('order_type') ?></th>
                                            <th><?php echo $this->lang->line('status') ?></th>
                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>  
                                            <td><input type="text" class="form-control form-filter input-sm" name="order"></td>                                
                                            <td><input type="text" class="form-control form-filter input-sm" name="shop"></td>                                    
                                            <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="order_total"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="driver"></td>
                                            <td>
                                                <select name="order_status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php $order_status = order_status($this->session->userdata('language_slug'));
                                                    foreach ($order_status as $key => $value) { ?>
                                                         <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php  } ?>                                   
                                                </select>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td><select name="order_delivery" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="Delivery"><?php echo $this->lang->line('delivery') ?></option>
                                                    <option value="24H Delivery"><?php echo $this->lang->line('deliver_24') ?></option>                     
                                                </select> 
                                            </td>
                                            <td></td>
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
<!-- Modal -->
<div id="add_status" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('update_status') ?></h4>
      </div>
      <div class="modal-body">
        <form id="form_add_status" name="form_add_status" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="entity_id" id="entity_id" value="">
                        <input type="hidden" name="user_id" id="user_id" value="">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('status') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <select name="order_status" id="order_status" class="form-control form-filter input-sm">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php $order_status = order_status($this->session->userdata('language_slug'));
                                foreach ($order_status as $key => $value) { ?>
                                     <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php  } ?>                            
                            </select>                                               
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-12 text-center">
                         <div id="loadingModal" class="loader-c display-no"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                         <button type="submit" class="btn btn-sm  danger-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->

<div class="modal fade" id="show-order-detail" tabindex="-1" role="show-order-detail" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0706a9; color: white">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4>Details</h4>
            </div>
            <div class="modal-body">                                               
               <div id="modal-body-order-detail"></div>
            </div>
            <div class="modal-footer">
                   
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div id="assign_driver" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('assign_driver') ?></h4>
      </div>
      <div class="modal-body">
        <form id="form_assign_driver" name="form_assign_driver" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="order_entity_id" id="order_entity_id" value="">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('driver') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <select name="driver_id" id="driver_id" class="form-control required">
                                <option value=""><?php echo $this->lang->line('select') ?></option>

                                <?php if(!empty($drivers)){
                                foreach ($drivers as $key => $value) { ?>
                                     <option value="<?php echo $value->entity_id ?>"><?php echo $value->first_name.' '.$value->last_name; ?></option>
                                <?php  } } ?>                           
                            </select>                                               
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-12 text-center">
                         <div id="loadingModal" class="loader-c" style="display: none;"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                         <button type="submit" class="btn btn-sm  danger-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="view_comment" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('view_comment') ?></h4>
      </div>
      <div class="modal-body">
        <form id="form_view_comment" name="form_view_comment" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('comment') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <textarea disabled class="form-control txt-extra-commment" name="extra_comment" id="extra_comment" rows="6" data-required="1"  ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div id="view_status_history" class="modal fade" role="dialog">
</div>
 <div class="wait-loader display-no" id="quotes-main-loader"><img  src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"  ></div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script type="text/javascript" src="<?php echo base_url() ?>/assets/admin/plugins/uniform/jquery.uniform.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>/assets/admin/plugins/uniform/css/uniform.default.min.css"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<script>
var grid;
jQuery(document).ready(function() { 
    $(".date-picker").datepicker( {
        format: "dd-mm-yyyy",
        endDate: '+0d',
        /*startView: "months", 
        minViewMode: "months",*/
        autoclose: true    
    });       
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
                null,
                null,
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
            "aaSorting": [[ 5, "desc" ]] // set first column as a default sort by asc
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

function showDetailModal(entity_id) {
    $.ajax({
        type: "GET",
        url: BASEURL+"backoffice/order/orderDetail/"+entity_id,
        beforeSend: function(){
            $('#quotes-main-loader').show();
          },   
          success: function(html) { 
            //if (html == "success") {
                $('#show-order-detail').modal('show');
                $('#modal-body-order-detail').children().remove();
                $('#modal-body-order-detail').append(html);
                $('#quotes-main-loader').hide();
            // }
            return false;
          }
    })
   
}

// update driver for a order
function updateDriver(entity_id){
    $('#order_entity_id').val(entity_id);
    $('#assign_driver').modal('show');
}
// submitting the assigning driver popup
$('#form_assign_driver').submit(function(){ 
    var driver_id = $('#driver_id').val();
    if (driver_id != '') { 
        $.ajax({
          type: "POST",
          dataType : "html",
          url: BASEURL+"backoffice/order/assignDriver",
          data: $('#form_assign_driver').serialize(),
          cache: false, 
          beforeSend: function(){
            $('#quotes-main-loader').show();
          },   
          success: function(html) { 
            if (html == "success") {
                $('#quotes-main-loader').hide();
                $('#assign_driver').modal('hide');
                grid.getDataTable().fnDraw();
            }
            return false;
          }
        }); 
    }
    return false;
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
                  url : 'ajaxDelete',
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

// method for reject order 
function rejectOrder(user_id,shop_id,order_id)
{
    bootbox.confirm({
        message: "<?php echo $this->lang->line('reject_module'); ?>",
        buttons: {
            confirm: {
                label: '<?php echo $this->lang->line('ok'); ?>',
            },
            cancel: {
                label: '<?php echo $this->lang->line('cancel'); ?>',
            }
        },
        callback: function (rejectConfirm) {       
            if (rejectConfirm) {
                jQuery.ajax({
                  type : "POST",
                  dataType : "json",
                  url : 'ajaxReject',
                  data : {'user_id':user_id,'shop_id':shop_id,'order_id':order_id},
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
//get invoice
function getInvoice(entity_id){
    $.ajax({
      type: "POST",
      dataType : "html",
      url: BASEURL+"backoffice/order/getInvoice",
      data: {'entity_id': entity_id},
      cache: false, 
      beforeSend: function(){ 
        $('#quotes-main-loader').show();
      },   
      success: function(html) { 
            $('#quotes-main-loader').hide();
            var WinPrint = window.open('<?php echo base_url() ?>'+html, '_blank', 'left=0,top=0,width=650,height=630,toolbar=0,status=0');
      }
    });
}
//add status
function updateStatus(entity_id,status,user_id){
    $('#entity_id').val(entity_id);
    $('#user_id').val(user_id);
    if(status == 'preparing'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="delivered">Delivered</option><option value="onGoing">On Going</option>'
        );
    }
    if(status == 'onGoing'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="delivered">Delivered</option>'
        );
    }
    if(status == 'placed'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="preparing">Preparing</option><option value="delivered">Delivered</option><option value="onGoing">On Going</option><option value="cancel">Cancel</option>'
        );
    }
    $('#add_status').modal('show');
}
//view comment
function viewComment(entity_id){
    $.ajax({
      type: "POST",
      url: BASEURL+"backoffice/order/viewComment",
      data: {"entity_id":entity_id},
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },   
      success: function(response) {
        $('#quotes-main-loader').hide();
        $('textarea#extra_comment').val(response);
        $('#view_comment').modal('show');
      }
    });
    return false;
}
$('#form_add_status').submit(function(){
    $.ajax({
      type: "POST",
      dataType : "html",
      url: BASEURL+"backoffice/order/updateOrderStatus",
      data: $('#form_add_status').serialize(),
      cache: false, 
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },   
      success: function(html) {
        $('#quotes-main-loader').hide();
        $('#add_status').modal('hide');
        grid.getDataTable().fnDraw();
      }
    });
    return false;
});
//delete multiple
$('#delete_order').click(function(e){
    e.preventDefault();
    var records = grid.getSelectedRows();  
    if(!jQuery.isEmptyObject(records)){            
        var CommissionIds = Array();
        var amount = '0.00';
        for (var i in records) {  
            var val = records[i]["value"];            
            CommissionIds.push(val);                        
        }
        var CommissionIdComma = CommissionIds.join(",");
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
                      url : 'deleteMultiOrder',
                      data : {'arrayData':CommissionIdComma},
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
    }else{
        bootbox.alert({
            message: "<?php echo $this->lang->line('checkbox'); ?>",
            buttons: {
                ok: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                }
            }
        });
    }        
});
function statusHistory(order_id){
    jQuery.ajax({
      type : "POST",                      
      url : 'statusHistory',
      data : {'order_id':order_id},
      cache: false,
      success: function(response) {      
        $('#view_status_history').html(response);
        $('#view_status_history').modal('show');      
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
}
// method for update status 
function disableDetail(entity_id,shop_id,order_id)
{
    bootbox.confirm({
        message: "<?php echo $this->lang->line('accept_order'); ?>",
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
                  url : 'ajaxdisable',
                  data : {'entity_id':entity_id,'shop_id':shop_id,'order_id':order_id},
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

$('#assign_driver').on('hidden.bs.modal', function (e) {
  $(this).find("input[type=select]").val('').end();
  $('#form_assign_driver').validate().resetForm();
});

</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
<?php $this->load->view(ADMIN_URL.'/header');?>

<!-- BEGIN PAGE LEVEL STYLES -->

<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/datepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>

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

                        <?php echo $this->lang->line('title_admin_event')?> <?php echo $this->lang->line('list')?>

                    </h3>

                    <ul class="page-breadcrumb breadcrumb">

                        <li>

                            <i class="fa fa-home"></i>

                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">

                            <?php echo $this->lang->line('home')?> </a>

                            <i class="fa fa-angle-right"></i>

                        </li>

                        <li>

                            <?php echo $this->lang->line('title_admin_event')?>

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
                                 <?php 
                                    if($this->session->flashdata('not_found'))
                                    {?>
                                        <div class="alert alert-danger">
                                             <?php echo $this->session->flashdata('not_found');?>
                                        </div>
                                    <?php } ?>
                                    <form action="<?php echo base_url().ADMIN_URL ?>/event/generate_report" id="event_generate_report" name="event_generate_report" method="post" class="horizontal-form" enctype="multipart/form-data" >
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
                                                <label class="control-label"><?php echo $this->lang->line('booking_date') ?></label>
                                                <input type="text" class="form-control date-picker" readonly name="booking_date_export" id="booking_date_export" placeholder="<?php echo $this->lang->line('booking_date') ?>">
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

                            <div class="caption"><?php echo $this->lang->line('title_admin_event')?> <?php echo $this->lang->line('list')?></div>

                          
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

                                            <th><?php echo $this->lang->line('user')?></th>

                                            <th><?php echo $this->lang->line('shop')?></th>

                                            <th><?php echo $this->lang->line('no_of_people')?></th>

                                            <th><?php echo $this->lang->line('event_date')?></th>

                                            <th><?php echo $this->lang->line('amount')?></th>

                                            <th><?php echo $this->lang->line('payment_status')?></th>

                                            <!-- <th><?php //echo $this->lang->line('status')?></th> -->

                                            <th><?php echo $this->lang->line('action')?></th>

                                        </tr>

                                        <tr role="row" class="filter">

                                            <td></td>     

                                            <td><input type="text" class="form-control form-filter input-sm" name="user_name"></td>                                  

                                           

                                            <td><input type="text" class="form-control form-filter input-sm" name="shop"></td>

                                            <td><input type="text" class="form-control form-filter input-sm" name="no_of_people"></td>

                                            <td><input type="text" class="form-control form-filter input-sm date" name="booking_date" id="booking_date"></td>

                                            

                                            <td><input type="text" class="form-control form-filter input-sm" name="amount"></td>                                  

                                            <td> 

                                                <select name="event_status" class="form-control form-filter input-sm">
                                                    <?php $event_status = event_status($this->session->userdata('language_slug'));
                                                    foreach ($event_status as $key => $value) { ?>
                                                         <option value="<?php echo $key ?>"><?php echo $value; ?></option>
                                                    <?php  } ?>                           
                                                </select>

                                            </td>

                                            <td><div class="margin-bottom-5">

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

                    <!-- END EXAMPLE TABLE PORTLET-->

                </div>

            </div>

            <!-- END PAGE CONTENT-->

        </div>

    </div>

    <!-- END CONTENT -->

</div>

<!-- Modal -->

<div id="add_amount" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title"><?php echo $this->lang->line('add')?> <?php echo $this->lang->line('amount')?></h4>

      </div>

      <div class="modal-body">

        <form id="form_add_amount" name="form_add_amount" method="post" class="form-horizontal" enctype="multipart/form-data">

            <div class="row">

                <div class="col-sm-12">

                <div class="form-group">

                  <label class="control-label col-md-4"><?php echo $this->lang->line('amount')?> <span class="currency-symbol"></span><span class="required">*</span></label>

                  <div class="col-sm-8">

                    <input type="text" class="form-control format-val" name="subtotal" id="subtotal" value="" maxlength="10" onkeyup="calculation(this.value)" />

                  </div>

                </div>  

                <div class="form-group">

                    <label class="control-label col-md-4"><?php echo $this->lang->line('coupon_discount')?></label>

                    <div class="col-md-8">
                        <input type="hidden" name="entity_id" id="entity_id" value="">
                        <input type="text" data-value="" name="coupon_amount" id="coupon_amount" value="" maxlength="10" data-required="1" class="form-control" readonly=""/><label class="coupon-type"></label>

                       

                    </div>

                </div> 

                

                <!--<div class="form-group">

                    

                    <label class="control-label col-md-4"><?php echo $this->lang->line('status')?><span class="required">*</span></label>

                    <div class="col-sm-8">

                        <select name="event_status" id="event_status" class="form-control form-filter input-sm">

                            <option value=""><?php echo $this->lang->line('select')?></option>

                            <?php $event_status = event_status($this->session->userdata('language_slug'));
                            foreach ($event_status as $key => $value) { ?>
                                 <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php  } ?>                                          

                        </select>

                    </div>

                </div>-->

                <div class="form-group">

                  <label class="control-label col-md-4"><?php echo $this->lang->line('total')?> <span class="currency-symbol"></span><span class="required">*</span></label>

                  <div class="col-sm-8">

                    <input type="text" class="form-control format-val" name="amount" id="amount" value="" maxlength="10" readonly="" />

                  </div>

                </div>

                <div class="form-actions fluid">

                    <div class="col-md-12 text-center">

                     <div id="loadingModal" class="loader-c display-no" ><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>

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
<div id="add_status" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Status</h4>
      </div>
      <div class="modal-body">
        <form id="form_event_status" name="form_event_status" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="event_entity_id" id="event_entity_id" value="">
                        <label class="control-label col-md-4">Status<span class="required">*</span></label>
                        <div class="event-status col-sm-8">
                            <select name="event_status" id="event_status" class="form-control form-filter input-sm">
                                <option value="">Select...</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="cancel">Cancel</option>                                            
                            </select>                                               
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-12 text-center">
                         <div id="loadingModal" class="loader-c" style="display: none;"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                         <button type="submit" class="btn btn-sm  danger-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span>Save</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="wait-loader display-no" id="quotes-main-loader" ><img  src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"  ></div>

<!-- BEGIN PAGE LEVEL PLUGINS -->

<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>

<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->

<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-datepicker.js"></script>

<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>

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



//update status
function updateStatus(entity_id,event_status){
    $('#event_entity_id').val(entity_id);
    if(status == 'paid'){
        $('#event_status').empty().append(
            '<option value="">Select...</option><option value="cancel">Cancel</option>'
        );
    }
    $('#add_status').modal('show');
}
$('#form_event_status').submit(function(){
    $("#form_event_status").validate();

    if (!$("#form_event_status").valid()) return false;

    $.ajax({
      type: "POST",
      dataType : "html",
      url: BASEURL+"backoffice/event/updateEventStatus",
      data: $('#form_event_status').serialize(),
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

//add amount

function addAmount(entity_id,tax,coupon,tax_type,coupon_type){

    $('#add_amount #entity_id').val(entity_id);

   

    coupon = (coupon == 0)?'':coupon;

    $('#add_amount #coupon_amount').val(coupon);

    coupon_type = (coupon_type == 'Percentage')?'%':'';

    $('#add_amount .coupon-type').html(coupon_type);

   

    $('#add_amount').modal('show');
    getEventCurrency(entity_id);

}

//submit add amount form

$("#form_add_amount").submit(function(event) {

    $("#form_add_amount").validate();

    if (!$("#form_add_amount").valid()) return false;

    var url = BASEURL+"backoffice/event/addAmount";

    var form = $("#form_add_amount").serialize();

    $.ajax({

      type: "POST",

      url: url,

      data: form,

      dataType: 'json',

      beforeSend: function(){

        jQuery('#add_amount #loadingModal').show();

      },

      success: function(html) {

        jQuery('#add_amount #loadingModal').hide();

        grid.getDataTable().fnDraw(); 

        $('#add_amount').modal('hide');

      }

    });

    return false;

});

function calculation(sum){

    //tax

    var amount = $('#coupon_amount').val(); 

    var type = $('.coupon-type').html();


    //coupon

    if(type == 'Percentage' && amount != '' && amount != 0){

        var cpn = (sum*amount)/100;

        sum = sum - cpn;

    }else if(type == 'Amount' && amount != '' && amount != 0){

        sum = sum - amount;

    }

    if(!isNaN(sum)){

        $('#amount').val(sum);

    }else{

        $('#amount').val(0);

    }

}

$('#add_amount').on('hidden.bs.modal', function () {

    $(".modal-dialog .form-control").removeClass("error");

    $(".modal-dialog label.error").remove();

    $('#form_add_amount option').prop('selected', false);

    $('#form_add_amount input').val('');

});

$('#booking_date').datetimepicker({

    format: 'yyyy-mm-dd hh:ii',

    autoclose: true,

});

$('#booking_date_export').datepicker({

    format: 'dd-mm-yyyy',

    autoclose: true,

});

$('#end_date').datetimepicker({

    format: 'yyyy-mm-dd hh:ii',

    autoclose: true,

});

//get invoice

function getInvoice(entity_id){

 

      $.ajax({

      type: "POST",

      dataType : "html",

      url: BASEURL+"backoffice/event/getInvoice",

      data: {'entity_id': entity_id},

      cache: false, 

      beforeSend: function(){

        $('#quotes-main-loader').show();

      },   

      success: function(html) {

            $('#quotes-main-loader').hide();

            var WinPrint = window.open('<?php echo base_url() ?>'+html, '_blank', 'left=0,top=0,width=650,height=630,toolbar=0,status=0');

            /*deletefile(html);*/

      }

      });

  

}

</script>

<?php $this->load->view(ADMIN_URL.'/footer');?>
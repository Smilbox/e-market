<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/jquery.timepicker.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('content_id','entity_id','branch_entity_id','name','phone_number','email','address','landmark','latitude','longitude','state','country','city','zipcode','amount_type','amount','enable_hours','timings','image');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('branch');        
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('branch');    
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add/".$this->uri->segment('4');
}
$usertypes = getUserTypeList($this->session->userdata('language_slug'));
?>

<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('branch') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('branch') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->prefix; ?>" name="form_add<?php echo $this->prefix; ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" class="load-iframe load-iframe-img ">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading"  />
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
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('branch_name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id ?>">
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                            <input type="text" name="name" id="name"  value="<?php echo $name; ?>" maxlength="100" data-required="1" class="form-control"/>
                                        </div>
                                    </div>   
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="branch_entity_id" id="branch_entity_id" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php if(!empty($shop)){
                                                    foreach ($shop as $key => $value) { ?>
                                                       <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $branch_entity_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>        
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('phone_number'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="phone_number"  id="phone_number" value="<?php echo $phone_number;?>" maxlength="20" data-required="1" class="form-control"/>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('email'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="email" name="email" id="email"  value="<?php echo $email;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('image'); ?></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="Image" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                </label>
                                                <input type="file" name="Image" id="Image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)"/>
                                            </div>
                                            <p class="help-block"><?php echo $this->lang->line('img_allow'); ?><br /> <?php echo $this->lang->line('max_file_size'); ?></p>
                                            <span class="error display-no" id="errormsg"><?php echo $this->lang->line('file_extenstion'); ?></span>
                                            <div id="img_gallery"></div>
                                            <img id="preview" height='100' width='150' class="display-no" />
                                            <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image)?$image:''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($image) && $image != '') {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image'); ?></span>
                                                            <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$image;?>">
                                            <?php }  ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('address'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control"  name="address" id="address" value="<?php echo $address ?>" maxlenght="255"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('landmark'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control"  name="landmark" id="landmark" value="<?php echo $landmark ?>" maxlenght="255"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('latitude'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="latitude" id="latitude" value="<?php echo $latitude ?>" maxlenght="50"/>
                                        </div>
                                        <a href="#basic" data-toggle="modal" class="btn red default"> Pick Latitude / Longitude </a>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('longitude'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="longitude" id="longitude" value="<?php echo $longitude ?>" maxlenght="50"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('zipcode'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="zipcode" id="zipcode" value="<?php echo $zipcode ?>" maxlenght="10"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('country'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="country" id="country" value="<?php echo $country; ?>" maxlenght="40"/>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('state'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                             <input type="text" class="form-control" name="state" id="state" value="<?php echo $state ?>" maxlenght="40"/>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('city'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="city" id="city" value="<?php echo $city ?>" maxlenght="40"/>
                                        </div>
                                    </div>
                                    <div class="form-group">  
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('tax_amount'); ?><span class="required">*</span></label>                        
                                        <div class="col-sm-4">
                                                <p>
                                                  <input type="radio" name="amount_type" id="MPercentage"
                                                  <?php if (isset($amount_type) && $amount_type=="Percentage") echo "checked";?>
                                                  value="Percentage" checked="checked">&nbsp;&nbsp;<?php echo $this->lang->line('percentage'); ?>
                                                </p>
                                                <p>
                                                  <input type="radio" name="amount_type" id="MAmount"
                                                  <?php if (isset($amount_type) && $amount_type=="Amount") echo "checked";?>
                                                  value="Amount">&nbsp;&nbsp;<?php echo $this->lang->line('amount'); ?>
                                                </p>
                                        </div>
                                    </div>
                                    <div class="form-group"> 
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('tax'); ?><span class="required">*</span></label>
                                        <div class="col-sm-8 form-markup">
                                              <label id="Percentage"><?php echo $this->lang->line('Percentage'); ?> (%)</label>
                                              <label id="Amount" style="display:none" class="display-no"><?php echo $this->lang->line('amount'); ?> ($)</label>
                                              <br>
                                              <input type="text" name="amount" id="amount" value="<?php echo ($amount)?$amount:''; ?>" maxlength="19" data-required="1" class="form-control"/>  
                                        </div>  
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('enable_hours'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="radio" <?php echo ($enable_hours)? (($enable_hours == '1')?'checked':''):'checked' ?>  name="enable_hours" id="radioTrue" value="1" class="company-hours"> <label for="radioTrue"><?php echo $this->lang->line('yes'); ?></label>
                                            <input type="radio" <?php echo ($enable_hours == '0')?'checked':'' ?>  name="enable_hours" id="radioFalse" value="0" class="company-hours"> <label for="radioFalse"><?php echo $this->lang->line('no'); ?></label>
                                        </div>
                                    </div> 
                                    <div class="form-group company-timing" style="<?php echo ($enable_hours == '0')?'display:none':'display:block' ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_time'); ?></label>
                                        <?php if(empty($_POST['timings'])){
                                            $business_timings = unserialize(html_entity_decode($timings));
                                        }else{
                                            $timingsArr = $_POST['timings'];
                                            $newTimingArr = array();
                                            foreach($timingsArr as $key=>$value) {
                                                if(isset($value['off'])) {
                                                    $newTimingArr[$key]['open'] = '';
                                                    $newTimingArr[$key]['close'] = '';
                                                    $newTimingArr[$key]['off'] = '0';
                                                } else {
                                                    if(!empty($value['open']) && !empty($value['close'])) {
                                                        $newTimingArr[$key]['open'] = $value['open'];
                                                        $newTimingArr[$key]['close'] = $value['close'];
                                                        $newTimingArr[$key]['off'] = '1';
                                                    } else {
                                                        $newTimingArr[$key]['open'] = '';
                                                        $newTimingArr[$key]['close'] = '';
                                                        $newTimingArr[$key]['off'] = '0';
                                                    }
                                                }
                                            }
                                            $business_timings = $newTimingArr;
                                        }  ?>
                                        <div class="col-md-12">
                                             <table class="timingstable" width="100%" cellpadding="2" cellspacing="2">
                                                <tr>
                                                    <td><strong>&nbsp;</strong></td>
                                                    <td colspan="2">
                                                        <label class="checkbox chk-box">
                                                            <input type="checkbox" id="clickSameHours">
                                                            <?php echo $this->lang->line('time_msg'); ?> </label><br/>
                                                          <span id="alertSpan"  class="alert-timing"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('mon'); ?></strong></td>
                                                    <td>
                                                        <input type="text" class="ophrs" id="monday_open_hours" name="timings[monday][open]" <?php echo (intval(@$business_timings['monday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['monday']['open']; ?>" placeholder="<?php echo $this->lang->line('opening_hours') ?>">
                                                        <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['monday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['monday']['close']; ?>" name="timings[monday][close]" id="monday_close_hours">
                                                    </td>
                                                    <td>
                                                        <label class="checkbox" style="width:100%;"><input type="checkbox" <?php echo (intval(@$business_timings['monday']['off'])) ? '' : 'checked="checked"'; ?> value="monday" class="close_bar_check" id="monday_close" name="timings[monday][off]"><?php echo $this->lang->line('close_msg'); ?> <?php echo $this->lang->line('mon'); ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('tue'); ?></strong></td>
                                                    <td>
                                                        <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>"  <?php echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['tuesday']['open']; ?>" name="timings[tuesday][open]" id="tuesday_open_hours">
                                                        <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['tuesday']['close']; ?>" name="timings[tuesday][close]" id="tuesday_close_hours">
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'checked="checked"'; ?> value="tuesday" class="close_bar_check" id="tuesday_close" name="timings[tuesday][off]"><?php echo $this->lang->line('close_msg'); ?> <?php echo $this->lang->line('tue'); ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('wed'); ?></strong></td>
                                                    <td>
                                                        <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['wednesday']['open']; ?>" name="timings[wednesday][open]" id="wednesday_open_hours">
                                                        <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['wednesday']['close']; ?>" name="timings[wednesday][close]" id="wednesday_close_hours">
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'checked="checked"'; ?> value="wednesday" class="close_bar_check" id="wednesday_close" name="timings[wednesday][off]"><?php echo $this->lang->line('close_msg'); ?> <?php echo $this->lang->line('wed'); ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('thurs'); ?></strong></td>
                                                    <td>
                                                        <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['thursday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['thursday']['open']; ?>" name="timings[thursday][open]" id="thursday_open_hours">
                                                        <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['thursday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['thursday']['close']; ?>" name="timings[thursday][close]" id="thursday_close_hours">
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['thursday']['off'])) ? '' : 'checked="checked"'; ?> value="thursday" class="close_bar_check" id="thursday_close" name="timings[thursday][off]"><?php echo $this->lang->line('close_msg'); ?> <?php echo $this->lang->line('thurs'); ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('fri'); ?></strong></td>
                                                    <td>
                                                        <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['friday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['friday']['open']; ?>" name="timings[friday][open]" id="friday_open_hours">
                                                        <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['friday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['friday']['close']; ?>" name="timings[friday][close]" id="friday_close_hours">
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['friday']['off'])) ? '' : 'checked="checked"'; ?> value="friday" class="close_bar_check" id="friday_close" name="timings[friday][off]"><?php echo $this->lang->line('close_msg'); ?> <?php echo $this->lang->line('fri'); ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('sat'); ?></strong></td>
                                                    <td>
                                                        <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['saturday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['saturday']['open']; ?>" name="timings[saturday][open]" id="saturday_open_hours">
                                                        <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['saturday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['saturday']['close']; ?>" name="timings[saturday][close]" id="saturday_close_hours">
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['saturday']['off'])) ? '' : 'checked="checked"'; ?> value="saturday" class="close_bar_check" id="saturday_close" name="timings[saturday][off]"><?php echo $this->lang->line('close_msg'); ?> <?php echo $this->lang->line('sat'); ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('sun'); ?></strong></td>
                                                    <td>
                                                        <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>"  <?php echo (intval(@$business_timings['sunday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['sunday']['open']; ?>" name="timings[sunday][open]" id="sunday_open_hours">
                                                        <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>"  <?php echo (intval(@$business_timings['sunday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['sunday']['close']; ?>" name="timings[sunday][close]" id="sunday_close_hours">
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['sunday']['off'])) ? '' : 'checked="checked"'; ?> value="sunday" class="close_bar_check" id="sunday_close" name="timings[sunday][off]"><?php echo $this->lang->line('close_msg'); ?> <?php echo $this->lang->line('time_msg'); ?></label>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div> 
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit'); ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('cancel'); ?></a>
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
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo $this->lang->line('lat_long_msg'); ?></h4>
            </div>
            <div class="modal-body">                                               
                <form class="form-inline margin-bottom-10" action="#">
                    <div class="input-group">
                        <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="address...">
                        <span class="input-group-btn">
                            <button class="btn blue" id="gmap_geocoding_btn"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
                <div id="gmap_geocoding" class="gmaps">
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>            
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="http://maps.google.com/maps/api/js?key=<?php echo GMAP_API_KEY ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/gmaps/gmaps.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
$("#basic").on("shown.bs.modal", function () {    
    mapGeocoding(); // init geocoding Maps
});
var mapGeocoding = function () {    
    var map = new GMaps({
        div: '#gmap_geocoding',
        lat: 23.0225,
        lng: 72.5714,
        click: function (e) {           
           placeMarker(e.latLng);
        }       
    }); 
    map.addMarker({
        lat: 21.3891,
        lng: 72.5714,
        title: 'Ahmedabad',
        draggable: true,
        dragend: function(event) {
            $("#latitude").val(event.latLng.lat());
            $("#longitude").val(event.latLng.lng());
        }
    });   
    function placeMarker(location) {                       
        map.removeMarkers();
        $("#latitude").val(location.lat());
        $("#longitude").val(location.lng());
        map.addMarker({
            lat: location.lat(),
            lng: location.lng(),
            draggable: true,
            dragend: function(event) {
                $("#latitude").val(event.latLng.lat());
                $("#longitude").val(event.latLng.lng());
            }    
        })
    }
    var handleAction = function () {
        var text = $.trim($('#gmap_geocoding_address').val());
        GMaps.geocode({
            address: text,
            callback: function (results, status) {
                if (status == 'OK') { 
                    map.removeMarkers();                   
                    var latlng = results[0].geometry.location;                    
                    map.setCenter(latlng.lat(), latlng.lng());
                    map.addMarker({
                        lat: latlng.lat(),
                        lng: latlng.lng(),         
                        draggable: true,
                        dragend: function(event) {
                            $("#latitude").val(event.latLng.lat());
                            $("#longitude").val(event.latLng.lng());
                        }
                    });
                    $("#latitude").val(latlng.lat());
                    $("#longitude").val(latlng.lng());
                }
            }
        });
    }
    $('#gmap_geocoding_btn').click(function (e) {
        e.preventDefault();
        handleAction();
    });
    $("#gmap_geocoding_address").keypress(function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13') {
            e.preventDefault();
            handleAction();
        }
    });
} 
// Markup Radio Button Validation
function markup () {
  if($("input[name=AmountType]:checked").val() == "Percentage" ){
          $("#Amount").hide();
          $("#Percentage").show();     
  }else if($("input[name=AmountType]:checked").val() == "Amount" ){
          $("#Percentage").hide();
          $("#Amount").show();
  }
}
$(document).ready(function(){
   markup();
});
$("input[name=AmountType]:radio").click(function(){
  markup();
  if($("input[name=AmountType]:checked").val() == "Percentage" ){    
    $("#CouponAmount").val('');          
  }else if($("input[name=AmountType]:checked").val() == "Amount" ){
    $("#CouponAmount").val('');           
  }
});
//for company timing
$(function () {
    $('#monday_open_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#monday_close_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#tuesday_open_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#tuesday_close_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#wednesday_open_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#wednesday_close_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#thursday_open_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#thursday_close_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#friday_open_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#friday_close_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#saturday_open_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#saturday_close_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#sunday_open_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});
    $('#sunday_close_hours').timepicker({timeFormat: "hh:mm TT", controlType: 'select', ampm: true, stepMinute: 5});

    $(".close_bar_check").change(function () {
        var dy = this.value;
        if (this.checked) {
            $("#" + dy + "_open_hours").val('');
            $("#" + dy + "_close_hours").val('');
            $("#" + dy + "_open_hours").attr('disabled', 'disabled');
            $("#" + dy + "_close_hours").attr('disabled', 'disabled');
        } else {
            $("#" + dy + "_open_hours").removeAttr('disabled');
            $("#" + dy + "_close_hours").removeAttr('disabled');
        }
        return false;
    });
    $("#clickSameHours").change(function () {
        $('#alertSpan').html('');
        if (this.checked) {
            var ophrs = $('#monday_open_hours').val();
            var clhrs = $('#monday_close_hours').val();
            if (ophrs != '' && clhrs != '') {
                $('#alertSpan').html('');
                $(".close_bar_check").each(function (i) {
                    this.checked = false;
                    var parent = $(this).closest('tr');
                    $(parent).find('input').eq(0).removeAttr('disabled');
                    $(parent).find('input').eq(1).removeAttr('disabled');
                    $(parent).find('input').eq(0).val(ophrs);
                    $(parent).find('input').eq(1).val(clhrs);
                });
            } else {
                $('#alertSpan').html("<?php echo $this->lang->line('open_close_msg'); ?>");
                $(this).removeAttr("checked");
            }
        } else {
            $('#alertSpan').html('');
        }
        return false;
    });
});
$('.company-hours').click(function(){
    if($(this).val() == '0'){
        $('.company-timing').hide();
        $('.hasDatepicker').each(function(){
            var id = $(this).attr('id');
            $('#'+id).val('');
        });
        $('#clickSameHours').prop('checked',false).attr('checked',false);
    }else{
        $('.company-timing').show();
    }
});
function readURL(input) {
    var fileInput = document.getElementById('Image');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    var file_size = fileInput.size;
    if(input.files[0].size <= 5242880){ // 5 MB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview').attr('src', e.target.result).attr('style','display: inline-block;');
                    $("#old").hide();
                    $('#errormsg').html('').hide();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#preview').attr('src', '').attr('style','display: none;');
            $('#errormsg').html('<?php echo $this->lang->line('file_extenstion'); ?>').show();
            $('#Slider_image').val('');
            $("#old").show();
        }
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html('<?php echo $this->lang->line('file_size_msg'); ?>').show();
        $('#Slider_image').val('');
        $("#old").show();
    }
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
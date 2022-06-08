<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $meta_title;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<?php if($this->session->userdata('language_slug')  == 'ar'){?>
    <link href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/>
    
<?php }else{ ?>
    <link href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<?php } ?>
<link href="<?php echo base_url();?>assets/admin/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<?php if($this->session->userdata('language_slug')  == 'ar'){?>
<link href="<?php echo base_url();?>assets/admin/css/components-rtl.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/plugins-rtl.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/layout-rtl.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="<?php echo base_url();?>assets/admin/css/default-rtl.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/layout/css/custom-rtl.css" rel="stylesheet" type="text/css"/>
<?php }else{ ?>
<link href="<?php echo base_url();?>assets/admin/css/components.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="<?php echo base_url();?>assets/admin/layout/css/custom.css" rel="stylesheet">
<?php } ?>
<!-- END THEME STYLES -->
<link rel="shortcut icon"  sizes="40x40" href="<?php echo base_url();?>assets/admin/img/favicon.png"/>
<script>
    var BASEURL = '<?php echo base_url();?>';
</script>
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo base_url();?>assets/admin/plugins/respond.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

</head>
<body class="page-header-fixed">
<!-- BEGIN header -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN header INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="<?php echo base_url().ADMIN_URL;?>">
                <img src="<?php echo base_url();?>assets/admin/img/logo.png" alt="logo" class="logo-default"/>
            </a>
            <div class="menu-toggler sidebar-toggler hide">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <div class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </div>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        
        <div class="top-menu">

            <ul class="nav navbar-nav pull-right">
                <li>
                    <?php $count = $this->common_model->getNotificationCount(); ?>
                    <div class="notification">
                        <a href="<?php echo base_url().ADMIN_URL ?>/order/view" onclick="changeViewStatus();"><span><i class="fa fa-bell"></i><span class="count"><?php echo (!empty($count)) ? (($count->order_count >= 100)?'99+':$count->order_count) :'0' ?></span></span></a>
                    </div>
                </li>
                <li class="dropdown dropdown-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <span class="username">
                    <?php echo $this->session->userdata('adminFirstname')." ".$this->session->userdata('adminLastname');?> </span>
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu">                              
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL;?>/myprofile/getUserProfile">
                            <i class="fa fa-user"></i> My Profile </a>
                        </li>
                        <li class="divider"></li>                 
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL;?>/home/logout">
                            <i class="fa fa-key"></i> <?php echo $this->lang->line('log_out')?> </a>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
                <!-- END USER LOGIN DROPDOWN -->
            </ul>
            <ul class="nav navbar-nav pull-right language-change">                                
                    <?php 
                    $langs = $this->common_model->getLanguages();
                    foreach($langs as $slug => $language)
                    {?>      
                        <li onclick="setLanguage('<?php echo $language->language_slug ?>')"><i class="glyphicon bfh-flag-<?php echo $language->language_slug ?>"></i><?php echo $language->language_name; ?></li>
                    <?php }
                    ?>
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END header INNER -->
</div>
<!-- END header -->
<div class="clearfix">
</div>
<script type="text/javascript">
    function setLanguage(language_slug){
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : '<?php echo base_url().ADMIN_URL ?>/lang_loader/setLanguage',
            data : {'language_slug':language_slug},
            success: function(response) {
                location.reload();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
                alert(errorThrown);
            }
        });
    }
    

    var lang_slug = '<?php echo $this->session->userdata('language_slug'); ?>';
 
    var sProcessing = "<img src='<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif'/><span>&nbsp;&nbsp;Loading...</span>";
    var sLengthMenu = "_MENU_ records";
    var sInfo = "Showing _START_ to _END_ of _TOTAL_ entries";
    var sInfoEmpty = "No records found to show";
    var sGroupActions = "_TOTAL_ records selected:  ";
    var sAjaxRequestGeneralError = "Could not complete request. Please check your internet connection";
    var sEmptyTable = "No data available in table";
    var sZeroRecords = "No matching records found";
    var sPrevious = "Prev";
    var sNext = "Next";
    var sPage = "Page";
    var sPageOf = "of";
    var sFirst = "First";
    var sLast = "Last";
                
    <?php if ($this->session->userdata('language_slug') == "bn"): ?>
        console.log('bn');
        sProcessing = "<img src='<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif'/><span>&nbsp;&nbsp;প্রসেসিং হচ্ছে</span>";
        sLengthMenu = "_MENU_ টা এন্ট্রি দেি দ";
        sInfo = "_TOTAL_ টা এন্ট্রির মধ্যে _START_ থেকে _END_ পর্যন্ত দেখ";
        sInfoEmpty = "কোন এন্ট্রি খুঁজে পাওয়া যায় নাই";
        sGroupActions = "_TOTAL_ রেকর্ড নির্বাচন করা হয়েছে: ";
        sAjaxRequestGeneralError = "অনুরোধ সম্পূর্ণ করতে পারেনি। আপনার ইন্টারনেট সংযোগ পরীক্ষা করুন";
        sEmptyTable = "সারণীতে কোনও ডেটা উপলব্ধ নেই";
        sZeroRecords = "আপনি যা অনুসন্ধান করেছেন তার সাথে মিলে যাওয়া কোন রেকর্ড খুঁজে পাওয়া যা";
        sPrevious = "পূর্ববর্তী";
        sNext = "পরবর্তী";
        sPage = "পৃষ্ঠা";
        sPageOf = "এর";
        sFirst = "প্রথম";
        sLast = "গত";
    <?php endif ?>
    <?php if ($this->session->userdata('language_slug') == "fr"): ?>
        sProcessing = '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Chargement...</span>',
        sLengthMenu = "Afficher _MENU_ &eacute;l&eacute;ments",
        sInfo = "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
        sInfoEmpty = "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
        sGroupActions = "_TOTAL_ records selected:  ",
        sAjaxRequestGeneralError = "Impossible de terminer la demande. S'il vous plait, vérifiez votre connexion internet",
        sEmptyTable =  "Aucune donn&eacute;e disponible dans le tableau",
        sZeroRecords = "Aucun &eacute;l&eacute;ment &agrave; afficher",
        sPrevious = "Pr&eacute;c&eacute;dent",
        sNext = "Suivant",
        sPage = "Page",
        sPageOf = "de",
        sFirst = "Premier",
        sLast = "Dernier"
    <?php endif ?>
    <?php if ($this->session->userdata('language_slug') == "ar"): ?>
        sProcessing = '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;جارٍ التحميل...</span>',
        sLengthMenu = "أظهر _MENU_ مدخلات",
        sInfo = "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
        sInfoEmpty = "لم يتم العثور على أي سجلات",
        sGroupActions = "_TOTAL_ records selected:  ",
        sAjaxRequestGeneralError = "لا يمكن إكمال الطلب. الرجاء التحقق من اتصال الانترنت الخاص بك",
        sEmptyTable =  "لا توجد بيانات متاحة في الجدول",
        sZeroRecords = "لم يتم العثور على سجلات متطابقة",
        sPrevious = "السابق",
        sNext =     "التالي",
        sPage = "صفحة",
        sPageOf = "من",
        sFirst =    "الأول",
        sLast =     "الأخير"
    <?php endif ?>
</script>


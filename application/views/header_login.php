<!DOCTYPE html>

<html lang="en">
	<head>
		<!-- Required meta tags -->
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

	    <title><?php echo $page_title; ?></title>

	    <!-- SEO and SMO meta tags -->
	    <meta name="description" content="">
	    <meta name="keywords" content="">

	    <!-- Required Stylesheet -->
	    <link rel='stylesheet' href='<?php echo base_url(); ?>assets/front/css/animate.min.css'>
	    <link rel='stylesheet' href='<?php echo base_url(); ?>assets/front/css/owl.carousel.min.css'>
	    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/bootstrap.min.css" type="text/css">
	    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/main.css?<?php echo random_string()?>" type="text/css">
	    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/style.css" type="text/css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/responsive.css" type="text/css">

	    <!-- Required jQuery -->
	    <script type="text/javascript" src='<?php echo base_url(); ?>assets/front/js/jquery.min.js'></script>
	    <script type="text/javascript" src='<?php echo base_url(); ?>assets/front/js/wow.min.js'></script>
	    <script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/popper.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/bootstrap.min.js"></script>
	    <script type="text/javascript" src='<?php echo base_url(); ?>assets/front/js/owl.carousel.min.js'></script>
	    <script type="text/javascript" src="<?php echo base_url();?>assets/front/js/jquery.validate.min.js"></script>

	    <!-- Favicons -->
	    <link rel="shortcut icon"  sizes="40x40" href="<?php echo base_url();?>assets/admin/img/favicon.png"/>
	</head>
	<script>
	    var BASEURL = '<?php echo base_url();?>';
	    var USER_ID = '<?php echo $this->session->userdata('UserID'); ?>';
	    var IS_USER_LOGIN = '<?php echo $this->session->userdata('is_user_login'); ?>';
	    var SEARCHED_LAT = '<?php echo ($this->session->userdata('searched_lat'))?$this->session->userdata('searched_lat'):''; ?>';
	    var SEARCHED_LONG = '<?php echo ($this->session->userdata('searched_long'))?$this->session->userdata('searched_long'):''; ?>';
	    var SEARCHED_ADDRESS = '<?php echo ($this->session->userdata('searched_address'))?$this->session->userdata('searched_address'):''; ?>';
	    var ADD = '<?php echo $this->lang->line('add') ?>';
	    var ADDED = '<?php echo $this->lang->line('added') ?>';
        <?php
                if(!empty($store_type_variables)):
        ?>
        var STORE_TYPES = <?php echo json_encode($store_type_variables) ?>;
        <?php
                endif;
        ?>
	</script>
	<?php $lang_class = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') . '-lang' : 'en-lang';?>
	<?php $lang_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
	$cmsPages = $this->common_model->getCmsPages($lang_slug);  ?>
	<body class="<?php echo $lang_class; ?>">
		<?php if ($current_page != "Login" && $current_page != "Registration") { ?>
			<header class="header-area">
				<div class="header-notif"><?php echo $this->lang->line('go-to-guide') ?><a href="<?php echo base_url() ?>how-to-order" style="color: black"><?php echo $this->lang->line('current_lang') == 'en' ? 'here': 'ici'; ?></a><i class="iicon-icon-23" onClick="removeHeader()"></i></div>
				<div class="container">
				<div class="fb-customerchat"
					page_id="<?php echo FB_PAGE_ID?>"
					theme_color="<?php echo FB_CHAT_THEME?>"
					greeting_dialog_display="hide">
				</div>
					<div class="header-inner">
						<div class="logo">
							<a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/front/images/logo.png" alt=""></a>
						</div>
						<nav>
							<ul id="example-one">
								<li class="<?php echo ($current_page == 'HomePage') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('home') ?></a></li>
								<li class="dropdown <?php echo ($current_page == 'OrderFood') ? 'current_page_item' : ''; ?>">
								<span class="dropdown-order"><?php echo $this->lang->line('title_admin_order') ?></span>
							    	<div class="dropdown-content dropdown-inverse-color">
										<?php $stores = $this->common_model->getAllRows('store_type');
    									foreach ($stores as $store) {?>
					                        <div>
											<?php if($store->name_en == 'Telma'){ $store->link = base_url().'telma'; } else { $store->link = base_url().'order/'.$store->entity_id; }?>
											<a href="<?php echo $store->link ?>"><?php echo ($this->lang->line('current_lang') == 'en' ?  $store->name_en : $store->name_fr); ?></a>
											</div>
					                    <?php }?>
					                </div>
								</li>	
								<?php if (!empty($cmsPages)) {
									foreach ($cmsPages as $key => $value) { 
										if($value->CMSSlug == "contact-us") { ?>
											<li class="<?php echo ($current_page == 'ContactUs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'contact-us'; ?>"><?php echo $this->lang->line('contact_us') ?></a></li>
										<?php }
										else if ($value->CMSSlug == "about-us") { ?>
											<li class="<?php echo ($current_page == 'AboutUs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'about-us'; ?>"><?php echo $this->lang->line('about_us') ?></a></li>
										<?php }
									}
								} ?>
							</ul>
							<div class="header-right">
								<div class="noti-cart">
									<ul>
										<?php if ($this->session->userdata('is_user_login') && !empty($this->session->userdata('UserID'))) {
											$userUnreadNotifications = $this->common_model->getUsersNotification($this->session->userdata('UserID'),'unread');
											$notification_count = count($userUnreadNotifications);
											$userNotifications = $this->common_model->getUsersNotification($this->session->userdata('UserID')); ?>
											<li class="notification">
												<div id="notifications_list">
													<?php if (!empty($userNotifications)) { ?>
														<a href="javascript:void(0)" class="notification-btn"><i class="iicon-icon-01"></i><span class="notification_count"><?php echo $notification_count; ?></span></a>
														<div class="noti-popup">
															<div class="noti-title">
																<h5><?php echo $this->lang->line('notification') ?></h5>
																<div class="bell-icon">
																	<i class="iicon-icon-01"></i>
																	<span class="notification_count"><?php echo $notification_count; ?></span>
																</div>
															</div>
															<div class="noti-list">
																<?php if (!empty($userNotifications)) {
																    foreach ($userNotifications as $key => $value) {
																        if (date("Y-m-d", strtotime($value->datetime)) == date("Y-m-d")) {
																            $noti_time = date("H:i:s") - date("H:i:s", strtotime($value->datetime));
																            $noti_time = abs($noti_time) . ' '.$this->lang->line('mins_ago');
																        } else {
																            $d1 = strtotime(date("Y-m-d",strtotime($value->datetime)));
																			$d2 = strtotime(date("Y-m-d"));

																			$noti_time = ($d2 - $d1)/86400;
																			$noti_time = ($noti_time > 1 )?$noti_time.' '.$this->lang->line('days_ago'):$noti_time.' '.$this->lang->line('day_ago');
																        }
																        ?>
																		<div class="noti-list-box">
																			<?php $view_class = ($value->view_status == 0)?'unread':'read'; ?>
																			<div class="noti-list-text <?php echo $view_class; ?>">
																				<h6><?php echo $this->session->userdata('userFirstname') . ' ' . $this->session->userdata('userLastname'); ?></h6>
																				<span class="min"><?php echo $noti_time; ?></span>
																				<h6>OrderID: #<?php echo $value->order_id; ?></h6>
																				<p><?php echo $this->lang->line($value->notification_slug); ?></p>
																			</div>
																		</div>
																	<?php }
																}?>
															</div>
														</div>
													<?php } 
													else { ?>
														<a href="javascript:void(0)" class="notification-btn"><i class="iicon-icon-01"></i><span>0</span></a>
														<div class="noti-popup">
															<div class="noti-title">
																<h5><?php echo $this->lang->line('notification') ?></h5>
																<div class="bell-icon">
																	<i class="iicon-icon-01"></i>
																	<span>0</span>
																</div>
															</div>
															<div class="viewall-btn">
																<a href="javascript:void(0)" class="btn"><?php echo $this->lang->line('no_notifications') ?></a>
															</div>
														</div>
													<?php }?>
												</div>
											</li>
										<?php }?>
										<?php $cart_details = get_cookie('cart_details');
										$cart_shop = get_cookie('cart_shop');
										$cart = $this->common_model->getCartItems($cart_details,$cart_shop);
										$count = count($cart['cart_items']); ?>
										<li class="cart"><a href="<?php echo base_url() . 'cart'; ?>"><i class="iicon-icon-02"></i><span id="cart_count"><?php echo $count; ?></span></a></li>
									</ul>
								</div>
								<div class="dropdown">
									<?php $language = $this->common_model->getLang($this->session->userdata('language_slug'));?>
							    	<button class="dropbtn"><img src="<?php echo base_url(); ?>assets/front/images/translate.png"><?php echo ($language) ? strtoupper($language->language_slug) : 'EN'; ?></button>
							    	<div class="dropdown-content">
										<?php $langs = $this->common_model->getLanguages();
    									foreach ($langs as $slug => $language) {?>
					                        <div onclick="setLanguage('<?php echo $language->language_slug ?>')"><a href="javascript:void(0)"><i class="glyphicon bfh-flag-<?php echo $language->language_slug ?>"></i><?php echo $language->language_name; ?>
					                        </a></div>
					                    <?php }?>
					                </div>
				                </div>
								<?php if ($this->session->userdata('is_user_login')) {?>
									<div class="header-user">
										<div class="user-img">
											<?php $image = ($this->session->userdata('userImage')) ? (strpos($this->session->userdata('userImage'), "https") == 0 ? $this->session->userdata('userImage') : (base_url() . 'uploads/' . $this->session->userdata('userImage'))): (base_url() . 'assets/front/images/user-login.png');?>
                        					<img src="<?php echo $image; ?>">
										</div>
										<span class="user-menu-btn"><?php echo $this->session->userdata('userFirstname'); ?></span>
										<div class="header-user-menu">
											<ul>
												<li class="active"><a href="<?php echo base_url() . 'myprofile'; ?>"><i class="iicon-icon-31"></i><?php echo $this->lang->line('my_profile') ?></a></li>
												<li onclick="logout();"><a href="javascript:void(0)"><i class="iicon-icon-32"></i><?php echo $this->lang->line('logout') ?></a></li>
											</ul>
										</div>
									</div>
								<?php } else {?>
									<div class="signin-btn">
										<a href="<?php echo base_url() . 'home/login'; ?>" class="btn"><?php echo $this->lang->line('sign_in') ?></a>
									</div>
								<?php }?>
								<div class="mobile-icon">
									<button class="" id="nav-icon2"></button>
								</div>
							</div>
						</nav>
					</div>
				</div>
			</header>
		<?php }?>

		<script type="text/javascript">
		function removeHeader() {
			$('.header-notif').addClass('hide');
		}
		</script>

		<script>

  		// store the lat long in session
  	function fb_logon(data, page){
		jQuery.ajax({
			type : "POST",
			dataType :"html",
			url : BASEURL+'home/fb_logon',
			data : {'email':data.email,'first_name':data.first_name,'last_name':data.last_name,'bot_user_id':data.id,'picture':data.picture.data.url},
			success: function(response) {
				if(page == "checkout") {
					window.location.href = BASEURL+"checkout";
					return;
				}
				window.location.href = BASEURL+"myprofile";
				console.log('OK',response);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {           
			alert(errorThrown);
			}
		});
		} 

		function fetchFbData(data, page) {  
            // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
    		// console.log('Welcome!  Fetching your information.... ');
    		// FB.api(`/${data.id}/access_token=`+'<?php echo FB_PAGE_ACCESS_TOKEN ?>', function(response) {
			FB.api('/me?fields=first_name,last_name,email,picture.width(500).height(500)', function(response) {
				  if(response.error) {
					  return;
				  }	
				  // debugger;
				  // console.log('Successful login for: ' + response);
				  fb_logon(response, page);
    		});
		  }
		  
		

		function statusChangeCallback(response, page) {  // Called with the results from FB.getLoginStatus().
			console.log('statusChangeCallback');
			console.log(response);                   // The current login status of the person.
			if (response.status === 'connected') {   // Logged into your webpage and Facebook.
				fetchFbData(response, page);  
			} else {                                 // Not logged into your webpage or we are unable to tell.
			document.getElementById('status').innerHTML = 'Please log ' +
				'into this webpage.';
			}
		}


		function checkLoginState(page) { 
			// Called when a person is finished with the Login Button.
			FB.getLoginStatus(function(response) {   // See the onlogin handler
				statusChangeCallback(response, page);
			});
		}

		window.fbAsyncInit = function() {
			FB.init({
				appId            : <?php echo FB_APP_ID ?>,
				autoLogAppEvents : true,
				xfbml            : true,
				version          : '<?php echo FB_API_VERSION ?>'
			});
			
			FB.AppEvents.logPageView();

			/*FB.getLoginStatus(function(response) {   // Called after the JS SDK has been initialized.
      			statusChangeCallback(response);        // Returns the login status.
			});*/
			
			
			
		};

		(function(d, s, id){
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement(s); js.id = id;
			js.src = "https://connect.facebook.net/en_US/sdk.js";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
		
		</script>

		<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
    
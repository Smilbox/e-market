<div class="wait-loader display-no" id="quotes-main-loader"><img  src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"  ></div>
<footer class="footer-area">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="footer-logo">
					<a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/front/images/logo.png" alt=""></a>
				</div>
			</div>
			<?php //get System Option Data
			$this->db->select('OptionValue');
			$facebook = $this->db->get_where('system_option',array('OptionSlug'=>'facebook'))->first_row();

			$this->db->select('OptionValue');
			$linkedin = $this->db->get_where('system_option',array('OptionSlug'=>'linkedin'))->first_row(); 
			?>
			<div class="col-sm-12 ">
				<div class="social-icon">
					<ul>
						<!-- <?php $lang_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
						$cmsPages = $this->common_model->getCmsPages($lang_slug); 
						if (!empty($cmsPages)) {
							foreach ($cmsPages as $key => $value) { 
								if($value->CMSSlug == "privacy-policy") { ?>
									<li class="<?php echo (isset($current_page) && $current_page == 'PrivacyPolicy') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'privacy-policy'; ?>"><i class="iicon-icon-06"></i></a></li>
								<?php }
							}
						} ?> -->
						<li><a href="<?php echo $facebook->OptionValue; ?>" target="_blank" ><i class="iicon-icon-08"></i></a></li>
						<li><a href="<?php echo $linkedin->OptionValue; ?>" target="_blank" ><i class="iicon-icon-10"></i></a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="footer-links">
					<ul>
						<li class="<?php echo (isset($current_page) && $current_page == 'HomePage') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('home') ?></a></li>

						<?php $lang_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
						$cmsPages = $this->common_model->getCmsPages($lang_slug); 

						if (!empty($cmsPages)) {
							foreach ($cmsPages as $key => $value) { 
								if($value->CMSSlug == "legal-notice") { ?>
									<li class="<?php echo (isset($current_page) && $current_page == 'LegalNotice') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'legal-notice'; ?>"><?php echo $this->lang->line('legal_notice') ?></a></li>
								<?php }
								else if($value->CMSSlug == "terms-and-conditions") { ?>
									<li class="<?php echo (isset($current_page) && $current_page == 'TermsAndConditions') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'terms-and-conditions'; ?>"> <?php echo $this->lang->line('terms_and_conditions')?> </a></li>
								<?php }
								else if($value->CMSSlug == "privacy-policy") { ?>
									<li class="<?php echo (isset($current_page) && $current_page == 'PrivacyPolicy') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'privacy-policy'; ?>"> <?php echo $this->lang->line('privacy_policy')?> </a></li>
								<?php }
								else if ($value->CMSSlug == "about-us") { ?>
									<li class="<?php echo (isset($current_page) && $current_page == 'AboutUs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'about-us'; ?>"><?php echo $this->lang->line('about_us') ?></a></li>
								<?php }
								else if($value->CMSSlug == "contact-us") { ?>
									<li class="<?php echo (isset($current_page) && $current_page == 'ContactUs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'contact-us'; ?>"><?php echo $this->lang->line('contact_us') ?></a></li>
								<?php }
							}
						} ?>
						<!-- <li><a href="<?php //echo base_url() ; ?>">Home</a></li> -->
						<!-- <li><a href="<?php //echo base_url() . 'legal-notice'; ?>">Legal notice</a></li> -->
						<!-- <li><a href="<?php //echo base_url() . 'terms-and-conditions'; ?>">User terms and conditions</a></li> -->
						<!-- <li><a href="<?php //echo base_url() . 'privacy-policy'; ?>">Privacy Policy</a></li> -->
						<!-- <li><a href="<?php //echo base_url() . 'about-us'; ?>">About us</a></li> -->
						<!-- <li><a href="<?php //echo base_url() . 'contact-us'; ?>">Contact us</a></li> -->
					</ul>
				</div>
			</div>
			<hr>
			<div class="col-sm-12">
				<div class="copyright">
					<p><?php echo $this->lang->line('copyright_footer'); ?> <a target="_blank" href="<?php echo base_url(); ?>"><?php echo $this->lang->line('site_footer'); ?></a></p>
				</div>
			</div>
		</div>
	</div>
</footer>

<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/custom_js.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/scripts/front-validations.js?<?php echo random_string() ?>"></script>
</body>
</html>
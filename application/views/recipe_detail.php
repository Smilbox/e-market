<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>

		<section class="inner-banner recipe-detail-banner">
			<div class="container">
				<div class="inner-pages-banner">

				</div>
			</div>
		</section>

		<section class="inner-pages-section recipe-detail-section">
			<div class="rest-detail-main">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="rest-detail">
								<div class="rest-detail-img-main">
									<div class="rest-detail-img">
										<img src="<?php echo ($recipe_details[0]['image'])?$recipe_details[0]['image']:default_img; ?>">
									</div>
								</div>
								<div class="rest-detail-content">
									<h2><?php echo ($recipe_details[0]['name'])?$recipe_details[0]['name']:''; ?></h2>
									<p><?php echo ($recipe_details[0]['menu_detail'])?$recipe_details[0]['menu_detail']:''; ?></p>
									<ul>
										<li><i class="iicon-icon-18"></i><?php echo $this->lang->line('cooking_time') ?> : <?php echo ($recipe_details[0]['recipe_time'])?$recipe_details[0]['recipe_time']:''; ?> <?php echo $this->lang->line('minutes') ?></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="heading-title">
							<h2><?php echo $this->lang->line('recipe_text2') ?></h2>
						</div>
					</div>				
				</div>
				<div class="row recipe-detail-row">
					<div class="col-sm-12 col-md-6 col-lg-8">
						<div class="recipe-detail-list">
							<div class="recipe-detail-title">
								<h4><?php echo $this->lang->line('directions') ?></h4>
							</div>
							<ol class="bullet-style">
								<?php echo ($recipe_details[0]['recipe_detail'])?$recipe_details[0]['recipe_detail']:''; ?>
							</ol>
						</div>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-4">
						<div class="recipe-detail-list">
							<div class="recipe-detail-title">
								<h4 class="ingredients"><i class="iicon-icon-29"></i><?php echo $this->lang->line('ingredients') ?></h4>
							</div>
							<ol class="bullet-style bullet-style-02">
								<?php echo ($recipe_details[0]['ingredients'])?$recipe_details[0]['ingredients']:''; ?>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</section>


<?php $this->load->view('footer'); ?>

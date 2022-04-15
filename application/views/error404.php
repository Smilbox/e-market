<?php $this->load->view('header'); ?>

<section class="page-wrapper contact-us-wrapper" style="min-height: calc(100vh - 370px)">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center" style="margin-top: 5%;">
                	<img src="<?php echo base_url().'assets/front/images/no-delivery.png'?>" alt="404 not found">
                </div>
            </div>           
        </div>
        <div class="text-center" style="margin-top: 5%;">
            <h2><?php echo $this->lang->line('404_not_found') ?></h2>
        </div>
    </div>
</section>

<?php $this->load->view('footer'); ?>

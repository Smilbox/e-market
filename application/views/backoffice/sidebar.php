<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">        
        <ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200">            
            <li class="sidebar-toggler-wrapper">                
                <div class="sidebar-toggler">
                </div>                
            </li>
            <li>&nbsp;</li>
            <li class="start <?php echo ($this->uri->segment(2)=='dashboard')?"active":""; ?>">
                <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                    <i class="fa fa-dashboard"></i>
                    <span class="title"><?php echo $this->lang->line('dashboard'); ?></span>
                    <span class="selected"></span>
                </a>
            </li>
            <?php if($this->session->userdata('UserType') == 'MasterAdmin'){ ?>
                <li class="start <?php echo ($this->uri->segment(2)=='users' || $this->uri->segment(3)=='driver' || $this->uri->segment(3)=='commission')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/users/view">
                        <i class="fa fa-users"></i>
                        <span class="title"><?php echo $this->lang->line('users'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2)=='users' || $this->uri->segment(3) == 'driver' || $this->uri->segment(4)=='driver')?"open":""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="start <?php echo ($this->uri->segment(2)=='users' && $this->uri->segment(3) != 'driver' && $this->uri->segment(4)!='driver' && $this->uri->segment(3)!='commission' && $this->uri->segment(5)!='driver' && $this->uri->segment(3) != 'review')?"active":""; ?>">
                            <a href="<?php echo base_url().ADMIN_URL;?>/users/view">
                                <i class="fa fa-users"></i>
                                <span class="title"><?php echo $this->lang->line('manage_user'); ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3)=='driver' || $this->uri->segment(4)=='driver' ||  $this->uri->segment(3)=='commission' || $this->uri->segment(5)=='driver' || $this->uri->segment(3) == 'review')?"active":""; ?>">
                            <a href="<?php echo base_url().ADMIN_URL;?>/users/driver">
                                <i class="fa fa-motorcycle" aria-hidden="true"></i>
                                <span class="title"><?php echo $this->lang->line('manage_driver'); ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                    </ul>
                </li>
            <?php } ?>
            <li class="start <?php echo ($this->uri->segment(2)=='restaurant' || $this->uri->segment(2)=='branch' || $this->uri->segment(2)=='delivery_charge' || $this->uri->segment(2)=='addons_category')?"active":""; ?>">
                <a href="<?php echo base_url().ADMIN_URL;?>/restaurant/view">
                    <i class="fa fa-file-text"></i>
                    <span class="title"><?php echo $this->lang->line('restaurant'); ?></span>
                    <span class="arrow <?php echo ($this->uri->segment(2)=='restaurant' || $this->uri->segment(2)=='branch' || $this->uri->segment(2)=='delivery_charge' || $this->uri->segment(2)=='addons_category')?"open":""; ?>"></span>
                    <span class="selected"></span>
                </a> 
                <ul class="sub-menu">
                    <li class="start <?php echo ($this->uri->segment(2)=='restaurant' && $this->uri->segment(3) =='view')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/restaurant/view">
                            <i class="fa fa-cutlery"></i>
                            <span class="title"><?php echo $this->lang->line('manage_res'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="start <?php echo ($this->uri->segment(3)=='view_menu' || $this->uri->segment(3) == 'add_menu' || $this->uri->segment(3) == 'edit_menu')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/restaurant/view_menu">
                            <i class="fa fa-bars"></i>
                            <span class="title"><?php echo $this->lang->line('manage_res_menu'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="start <?php echo ($this->uri->segment(2) == 'branch')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/branch/view">
                            <i class="fa fa-building-o"></i>
                            <span class="title"><?php echo $this->lang->line('manage_branch'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="start <?php echo ($this->uri->segment(2) == 'addons_category')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/addons_category/view">
                            <i class="fa fa-list-alt"></i>
                            <span class="title"><?php echo $this->lang->line('addons_category'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="start <?php echo ($this->uri->segment(2) == 'store-type')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/store_type/view">
                            <i class="fa fa-list-alt"></i>
                            <span class="title"><?php echo $this->lang->line('store-type'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <?php //if($this->session->userdata('UserType') == 'MasterAdmin'){ ?>
                    <li class="start <?php echo ($this->uri->segment(2) == 'delivery_charge')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/delivery_charge/view">
                            <i class="fa fa-list-alt"></i>
                            <span class="title"><?php echo $this->lang->line('delivery_charge'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <?php //} ?>
                </ul>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='category')?"active":""; ?>">
                <a href="<?php echo base_url().ADMIN_URL;?>/category/view">
                    <i class="fa fa-list-alt"></i>
                    <span class="title"><?php echo $this->lang->line('menu_category'); ?></span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='order')?"active":""; ?>">
                <a href="<?php echo base_url().ADMIN_URL;?>/order/view">
                    <i class="fa fa-file-text"></i>
                    <span class="title"><?php echo $this->lang->line('orders'); ?></span>
                    <span class="arrow <?php echo ($this->uri->segment(2)=='order')?"open":""; ?>"></span>
                    <span class="selected"></span>
                </a> 
                <ul class="sub-menu">
                    <li class="start <?php echo ($this->uri->segment(2)=='order' && $this->uri->segment(3) != 'pending' && $this->uri->segment(3) != 'delivered' && $this->uri->segment(3) != 'on-going')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/order/view">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="title"><?php echo $this->lang->line('all_orders'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="start <?php echo ($this->uri->segment(3) =='pending')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/order/pending">
                            <i class="fa fa-clock-o"></i>
                            <span class="title"><?php echo $this->lang->line('placed'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="start <?php echo ($this->uri->segment(3) == 'delivered')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/order/delivered">
                            <i class="fa fa-truck"></i>
                            <span class="title"><?php echo $this->lang->line('delivered'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="start <?php echo ($this->uri->segment(3) == 'on-going')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/order/on-going">
                            <i class="fa fa-motorcycle"></i>
                            <span class="title"><?php echo $this->lang->line('onGoing'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="start <?php echo ($this->uri->segment(3) == 'cancel')?"active":""; ?>">
                        <a href="<?php echo base_url().ADMIN_URL;?>/order/cancel">
                            <i class="fa fa-times"></i>
                            <span class="title"><?php echo $this->lang->line('cancel'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php if($this->session->userdata('UserType') == 'MasterAdmin'){ ?>
            <li class="start <?php echo ($this->uri->segment(2)=='coupon')?"active":""; ?>">
                <a href="<?php echo base_url().ADMIN_URL;?>/coupon/view">
                    <i class="fa fa-dollar"></i>
                    <span class="title"><?php echo $this->lang->line('coupons'); ?></span>
                    <span class="selected"></span>
                </a>
            </li>
            <?php } ?>
            <li class="start <?php echo ($this->uri->segment(2)=='review')?"active":""; ?>">
                <a href="<?php echo base_url().ADMIN_URL;?>/review/view">
                    <i class="fa fa-star"></i>
                    <span class="title"><?php echo $this->lang->line('rating_review'); ?></span>
                    <span class="selected"></span>
                </a>
            </li>
            <?php if($this->session->userdata('UserType') == 'MasterAdmin'){ ?>
                <li class="start <?php echo ($this->uri->segment(2)=='notification')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/notification/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('notification'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
                <li class="start <?php echo ($this->uri->segment(2)=='slider-image')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/slider-image/view">
                        <i class="fa fa-image"></i>
                        <span class="title"><?php echo $this->lang->line('slider'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
                <li class="start <?php echo ($this->uri->segment(2)=='cms')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/cms/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('cms'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
                <li class="start <?php echo ($this->uri->segment(2)=='system_option')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/system_option/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('titleadmin_systemoptions'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
                <li class="start <?php echo ($this->uri->segment(2)=="email_template")?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/email_template/view">
                        <i class="fa fa-envelope-o"></i>
                        <span class="title"><?php echo $this->lang->line('email_template'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
                <li class="start <?php echo ($this->uri->segment(2)=="track_drivers")?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/track_drivers/view">
                        <i class="fa fa-map"></i>
                        <span class="title"><?php echo $this->lang->line('track_drivers'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
                <li class="start <?php echo ($this->uri->segment(2)=="promotion_settings")?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/promotion_settings/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('promotion_settings'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
        </ul>        
    </div>
</div>
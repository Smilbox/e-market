<div class="col-xl-3 col-lg-4">
    <div class="sidebar-menu-main">
        <div class="sidebar-menu">
            <div class="ordering-title">
                <h6><?php echo $this->lang->line('ordering') ?></h6>
            </div>
            <ul>
                <li><a href="javascript:void(0)" onclick="myOrderHistory()"><?php echo $this->lang->line('order_history') ?></a></li>
                <li><a href="javascript:void(0)" onclick="myBookings()"><?php echo $this->lang->line('my_bookings') ?></a></li>
                <li class="active"><a href="javascript:void(0)" onclick="myAddresses()"><?php echo $this->lang->line('my_addresses') ?></a></li>
            </ul>
        </div>
    </div>
</div>
<div class="col-xl-9 col-lg-8">
	<div class="profile-content-area">
	<div class="profile-page-title">
		<h5><?php echo $this->lang->line('my_addresses') ?></h5>
		<div class="add-address-btn">
			<button class="btn" data-toggle="modal" data-target="#add-address"><?php echo $this->lang->line('add_address') ?></button>
		</div>
	</div>
	<div class="profile-content-main">
		<div class="row orders-box-row">
			<?php if (!empty($users_address)) {
				foreach ($users_address as $key => $value) { 
					$class = ($value->is_main == 1)?"primary-address":""; ?>
			    	<div class="col-xl-6 col-lg-12">
			    		<div class="my-address-main <?php echo $class; ?>">
			    			<div class="my-address-box">
			    				<div class="my-address-list">
			    					<h6><?php echo $this->lang->line('address') ?> <?php echo $key+1; ?></h6> <?php echo ($value->is_main == 1)?"<span class='default-address'>".$this->lang->line('default')."</span>":""; ?>
			    					<p><?php echo $value->address.','.$value->landmark.','.$value->city.','.$value->zipcode; ?></p>
			    				</div>
			    			</div>
			    			<div class="address-btn">
			    				<button class="btn" data-toggle="modal"  onclick="editAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('edit_address') ?></button>
			    				<button class="btn" data-toggle="modal" onclick="deleteAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('delete_address') ?></button>
			    				<?php if ($value->is_main == 0) { ?>
			    					<button class="btn" data-toggle="modal" onclick="setMainAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('set_as_primary') ?></button>
			    				<?php } ?>
			    			</div>
			    		</div>
			    	</div>	
				<?php }
			} ?>			    	
	    </div>
	</div>
	</div>
</div>
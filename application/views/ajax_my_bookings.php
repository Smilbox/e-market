<div class="col-xl-3 col-lg-4">
    <div class="sidebar-menu-main">
        <div class="sidebar-menu">
            <div class="ordering-title">
                <h6><?php echo $this->lang->line('ordering') ?></h6>
            </div>
            <ul>
                <li><a href="javascript:void(0)" onclick="myOrderHistory()"><?php echo $this->lang->line('order_history') ?></a></li>
                <li class="active"><a href="javascript:void(0)" onclick="myBookings()"><?php echo $this->lang->line('my_bookings') ?></a></li>
                <li><a href="javascript:void(0)" onclick="myAddresses()"><?php echo $this->lang->line('my_addresses') ?></a></li>
            </ul>
        </div>
    </div>
</div>
<div class="col-xl-9 col-lg-8">
	<div class="profile-content-area">
		<div class="profile-page-title">
			<h5><?php echo $this->lang->line('my_addresses') ?></h5>
			<ul class="nav nav-tabs" role="tablist">
			  <li class="nav-item">
			    <a href="#past-bookings" class="nav-link active" data-toggle="tab"><?php echo $this->lang->line('past_bookings') ?></a>
			  </li>
			  <li class="nav-item">
			    <a href="#current-bookings" class="nav-link" data-toggle="tab"><?php echo $this->lang->line('upcoming_bookings') ?></a>
			  </li>
			</ul>
		</div>
		<div class="profile-content-main">
			<div class="tab-content">
				<div id="past-bookings" class="tab-pane show active">
				    <div class="row orders-box-row">
				    	<?php if (!empty($past_events)) {
				    		foreach ($past_events as $key => $value) {
				    			if ($key <= 7) { ?>
							    	<div class="col-xl-6 col-lg-12">
							    		<div class="ordering-box-main">
							    			<div class="ordering-box-top">
								    			<div class="ordering-box-img">
								    				<div class="ordering-img">
                                                        <?php $image = ($value['image'])?($value['image']):(default_img); ?>
                                                        <img src="<?php echo $image;?>"> 
								    				</div>
								    			</div>
								    			<div class="ordering-box-text">
								    				<h6><?php echo $value['name'];?></h6>
								    				<p class="addresse-icon"><?php echo $value['address'];?></p>
								    				<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> 
								    				<!-- <strong>Price : <span>$<?php //echo $value['package_price'];?></span></strong> -->
								    				<strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
								    			</div>
							    			</div>
							    			<div class="ordering-box-bottom">
							    				<ul>
							    					<li><i class="iicon-icon-18"></i><?php echo (date("G:i A",strtotime($value['booking_date'])));?></li>
							    					<li><i class="iicon-icon-26"></i><?php echo (date("d M Y",strtotime($value['booking_date'])));?></li>
							    					<li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> people</li>
							    				</ul>
							    				<div class="ordering-btn">
							    					<button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
							    				</div>
							    			</div>
							    		</div>
							    	</div>
				    			<?php } 
				    		}
				    	} ?>
				    </div>
				    <?php if (count($past_events) > 8) { ?>
                        <div class="row orders-box-row display-no" id="all_past_events">
                            <?php foreach ($past_events as $key => $value) {
                                if ($key > 7) { ?>
							    	<div class="col-xl-6 col-lg-12">
							    		<div class="ordering-box-main">
							    			<div class="ordering-box-top">
								    			<div class="ordering-box-img">
								    				<div class="ordering-img">
                                                        <?php $image = ($value['image'])?($value['image']):(default_img); ?>
                                                        <img src="<?php echo $image;?>"> 
								    				</div>
								    			</div>
								    			<div class="ordering-box-text">
								    				<h6><?php echo $value['name'];?></h6>
								    				<p class="addresse-icon"><?php echo $value['address'];?></p>
								    				<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> 
								    				<!-- <strong>Price : <span>$<?php //echo $value['package_price'];?></span></strong> -->
								    				<strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
								    			</div>
							    			</div>
							    			<div class="ordering-box-bottom">
							    				<ul>
							    					<li><i class="iicon-icon-18"></i><?php echo (date("G:i A",strtotime($value['booking_date'])));?></li>
							    					<li><i class="iicon-icon-26"></i><?php echo (date("d M Y",strtotime($value['booking_date'])));?></li>
							    					<li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> people</li>
							    				</ul>
							    				<div class="ordering-btn">
							    					<button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
							    				</div>
							    			</div>
							    		</div>
							    	</div>
                            	<?php }
                            } ?>
                        </div>
                        <div class="col-lg-12">
                            <div id="more_past_events" class="load-more-btn">
                                <button class="btn" onclick="moreEvents('past')">Load More</button>
                            </div>
                        </div>
                    <?php } ?>
				</div>
				<div id="current-bookings" class="tab-pane">
					<div class="row orders-box-row">
				    	<?php if (!empty($upcoming_events)) {
				    		foreach ($upcoming_events as $key => $value) {
				    			if ($key <= 7) { ?>
							    	<div class="col-xl-6 col-lg-12">
							    		<div class="ordering-box-main">
							    			<div class="ordering-box-top">
								    			<div class="ordering-box-img">
								    				<div class="ordering-img">
                                                        <?php $image = ($value['image'])?($value['image']):(default_img); ?>
                                                        <img src="<?php echo $image;?>"> 
								    				</div>
								    			</div>
								    			<div class="ordering-box-text">
								    				<h6><?php echo $value['name'];?></h6>
								    				<p class="addresse-icon"><?php echo $value['address'];?></p>
								    				<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> 
								    				<!-- <strong>Price : <span>$<?php //echo $value['package_price'];?></span></strong> -->
								    				<strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
								    			</div>
							    			</div>
							    			<div class="ordering-box-bottom">
							    				<ul>
							    					<li><i class="iicon-icon-18"></i><?php echo (date("G:i A",strtotime($value['booking_date'])));?></li>
							    					<li><i class="iicon-icon-26"></i><?php echo (date("d M Y",strtotime($value['booking_date'])));?></li>
							    					<li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> people</li>
							    				</ul>
							    				<div class="ordering-btn">
							    					<button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
							    				</div>
							    			</div>
							    		</div>
							    	</div>
				    			<?php } 
				    		}
				    	} ?>
				    </div>
				    <?php if (count($upcoming_events) > 8) { ?>
                        <div class="row orders-box-row display-no" id="all_upcoming_events">
                            <?php foreach ($upcoming_events as $key => $value) {
                                if ($key > 7) { ?>
							    	<div class="col-xl-6 col-lg-12">
							    		<div class="ordering-box-main">
							    			<div class="ordering-box-top">
								    			<div class="ordering-box-img">
								    				<div class="ordering-img">
                                                        <?php $image = ($value['image'])?($value['image']):(default_img); ?>
                                                        <img src="<?php echo $image;?>"> 
								    				</div>
								    			</div>
								    			<div class="ordering-box-text">
								    				<h6><?php echo $value['name'];?></h6>
								    				<p class="addresse-icon"><?php echo $value['address'];?></p>
								    				<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">NEW</strong>'; ?> 
								    				<!-- <strong>Price : <span>$<?php //echo $value['package_price'];?></span></strong> -->
								    				<strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
								    			</div>
							    			</div>
							    			<div class="ordering-box-bottom">
							    				<ul>
							    					<li><i class="iicon-icon-18"></i><?php echo (date("G:i A",strtotime($value['booking_date'])));?></li>
							    					<li><i class="iicon-icon-26"></i><?php echo (date("d M Y",strtotime($value['booking_date'])));?></li>
							    					<li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> people</li>
							    				</ul>
							    				<div class="ordering-btn">
							    					<button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
							    				</div>
							    			</div>
							    		</div>
							    	</div>
                            	<?php }
                            } ?>
                        </div>
                        <div class="col-lg-12">
                            <div id="more_upcoming_events" class="load-more-btn">
                                <button class="btn" onclick="moreEvents('past')"><?php echo $this->lang->line('load_more') ?></button>
                            </div>
                        </div>
                    <?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
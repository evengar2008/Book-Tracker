		<header class="entry-header">
			<h1 class="entry-title">Book Details - <?php the_title();?></h1>
		</header><!-- .entry-header -->

		<div class="entry-content">
		<?php 
		if (isset($_GET['submit'])) {
			echo '<p class = "form_notification">Book edited successfully!</p>';
			$trd_amazon_tracker->save_book_edit_fields($post_id);
			$seller_tracking_number = get_post_meta($post_id, 'seller_tracking_number', true);
			$user_status = get_post_meta($post_id, 'User status', true);
			$comments = $trd_amazon_tracker->get_book_comment($post_id);
			}?>	
		<div class = "view_book_wrapper">
		<form id="premium_user_edit_book" class="appnitro" enctype="multipart/form-data" method="post" action="<?php echo get_permalink();?>?submit=true">
			<div class = "view_book_wrapper_left">
				<p>
					<span class = "book_meta_label">Shipping status: </span>
					<span class = "book_meta_value"><?php echo $track_status_tag;?> <?php echo $last_checkpoint_city;?></span>
				</p>
				<p>
					<span class = "book_meta_label">Expected delivery: </span>
					<span class = "book_meta_value"><?php echo $expected_delivery;?></span>
				</p>
				<p>
					<span class = "book_meta_label">Shipping courier: </span>
					<span class = "book_meta_value"><?php echo $courier;?></span>
				</p>				
				<p>
					<span class = "book_meta_label">Warehouse status: </span>
					<span class = "book_meta_value"><?php echo $warehouse_status;?></span>
				</p>
				<p>
					<span class = "book_meta_label">User status: </span>
					<select name = "user_status">
						<option selected = "selected" value = "<?php echo $user_status;?>"><?php echo $trd_amazon_tracker->status_dict($user_status);?></option>
						<option value = "payment_received">Payment Received</option>
						<option value = "rejected_by_buyer">Rejected by Buyer</option>
						<option value = "refunded_by_seller">Refunded by seller</option>
						<option value = "donate_or_destroy">Donate or destroy</option>
					</select>
				</p>
				<p>
					<span class = "book_meta_label">Trading-in to: </span>
					<span class = "book_meta_value"><?php echo $trade_in_to;?></span>
				</p>												
				<p>
					<span class = "book_meta_label">Date: </span>
					<span class = "book_meta_value"><?php echo get_the_date();?></span>
				</p>
				<p>
					<span class = "book_meta_label">TRN-ID: </span>
					<span class = "book_meta_value"><?php echo $trn_id;?></span>
				</p>		
				<p>
					<span class = "book_meta_label">Shipped to Name: </span>
					<span class = "book_meta_value"><?php echo $shipped_to_name;?></span>
				</p>	
				<p>
					<span class = "book_meta_label">ISBN13: </span>
					<span class = "book_meta_value"><?php echo $isbn13;?></span>
				</p>
				<p>
					<span class = "book_meta_label">ISBN10: </span>
					<span class = "book_meta_value"><?php echo $isbn10;?></span>
				</p>															
			</div>

			<div class = "view_book_wrapper_right">
				<p>
					<span class = "book_meta_label">Book Title: </span>
					<span class = "book_meta_value"><?php the_title();?></span>
				</p>
				<p>
					<span class = "book_meta_label">Edition: </span>
					<span class = "book_meta_value"><?php echo $edition;?></span>
				</p>
				<p>
					<span class = "book_meta_label">Author: </span>
					<span class = "book_meta_value"><?php echo $author;?></span>
				</p>
				<p>
					<span class = "book_meta_label">Cost: </span>
					<span class = "book_meta_value"><?php echo $book_cost;?></span>
				</p>
				<p>
					<span class = "book_meta_label">Trade-In Value: </span>
					<span class = "book_meta_value"><?php echo $trd_in_value;?></span>
				</p>	
				<p>
					<span class = "book_meta_label">Tracking Number from Pre-Paid Label: </span>
					<span class = "book_meta_value"><?php echo $track_number;?></span>
				</p>
				<p>
					<span class = "book_meta_label">Seller / Rejected tracking number </span>
					<input id="seller_tracking_number" name="seller_tracking_number" class="element text medium" type="text" maxlength="50" value="<?php echo $seller_tracking_number;?>"/> 
				</p>					
				<p>
					<span class = "book_meta_label">Seller: </span>
					<span class = "book_meta_value"><?php echo $seller_name;?></span>
				</p>
				<p>
					<span class = "book_meta_label">Comments: </span>
					<textarea id="comments" name="book_comments" class="element text medium" type="textarea"><?php echo $comments;?></textarea> 
				</p>																											
			</div>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</form>
		</div>

		</div><!-- .entry-content -->
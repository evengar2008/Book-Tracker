<header class="entry-header">
			<h1 class="entry-title">Book Details - <?php the_title();?></h1>
		</header><!-- .entry-header -->

		<div class="entry-content">
		<?php 
		$comments = get_the_content($post_id);
		if (isset($_GET['submit'])) {
				echo '<p class = "form_notification">Book edited successfully!</p>';
				$trd_amazon_tracker->save_book_edit_fields($post_id);
				$seller_email = get_post_meta($post_id, 'seller_email', true);
				$shipped_to_name = get_post_meta($post_id, 'shipped_to_name', true);
				$trade_in_to = get_post_meta($post_id, 'trade_in_to', true);
				$trn_id = get_post_meta($post_id, 'trn_id', true);
				$isbn13 = get_post_meta($post_id, 'isbn13', true);
				$isbn10 = get_post_meta($post_id, 'isbn10', true);
				$edition = get_post_meta($post_id, 'edition', true);
				$book_cost = get_post_meta($post_id, 'book_cost', true);
				$trd_in_value = get_post_meta($post_id, 'trd_in_value', true);
				$track_number = get_post_meta($post_id, 'track_number', true);
				$seller_name = get_post_meta($post_id, 'seller_name', true);
				$author = get_post_meta($post_id, 'author', true);
				//book title check
				$book_title = get_post_meta($post_id, 'book_title', true);
				if (empty(trim($book_title))) {
					$book_title = get_the_title();
				}
				$warehouse_status = get_post_meta($post_id, 'track_status_manual', true);
				$comments = $trd_amazon_tracker->get_book_comment($post_id);	
				$seller_tracking_number = get_post_meta($post_id, 'seller_tracking_number', true);
				$user_status = get_post_meta($post_id, 'User status', true);				
			}
			//print "<pre>";
			//print_r($track_status);
			//print "</pre>";
			?>			
				<div class = "view_book_wrapper">
					<form id="admin_edit_book" class="appnitro" enctype="multipart/form-data" method="post" action="<?php echo get_permalink();?>?submit=true">
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
							<select name = "track_status_manual">
								<option selected = "selected" value = "<?php echo $warehouse_status;?>"><?php echo $trd_amazon_tracker->status_dict($warehouse_status);?></option>
								<option value = "waiting">Waiting</option>
								<option value = "received">Received</option>
								<option value = "sent_out">Sent out</option>
								<option value = "received_with_issues">Received with issues</option>
								<option value = "label_voided">Label voided</option>
							</select>
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
						</p>
						<p>
							<span class = "book_meta_label">Date: </span>
							<span class = "book_meta_value"><?php echo get_the_date();?></span>
						</p>
						<p>
							<span class = "book_meta_label">TRN-ID: </span>
							<input id="isbn10" name="trn_id" class="element text medium" type="text" maxlength="255" value="<?php echo $trn_id;?>"/> 
						</p>		
						<p>
							<span class = "book_meta_label">Shipped to Name: </span>
							<input id="isbn10" name="shipped_to_name" class="element text medium" type="text" maxlength="255" value="<?php echo $shipped_to_name;?>"/> 
						</p>	
						<p>
							<span class = "book_meta_label">ISBN13: </span>
							<input id="isbn10" name="isbn13" class="element text medium" type="text" maxlength="255" value="<?php echo $isbn13;?>"/> 
						</p>
						<p>
							<span class = "book_meta_label">ISBN10: </span>
							<input id="isbn10" name="isbn10" class="element text medium" type="text" maxlength="255" value="<?php echo $isbn10;?>"/> 
						</p>															
					</div>

					<div class = "view_book_wrapper_right">
						<p>
							<span class = "book_meta_label">Book Title: </span>
							<input id="isbn10" name="book_title" class="element text medium" type="text" maxlength="255" value="<?php echo $book_title;?>"/> 
						</p>
						<p>
							<span class = "book_meta_label">Edition: </span>
							<select name = "edition">
								<option selected = "selected" value = "<?php echo $edition;?>"><?php echo $edition;?></option>
								<option value = "1">1</option>
								<option value = "2">2</option>
								<option value = "3">3</option>
								<option value = "4">4</option>
								<option value = "5">5</option>
								<option value = "6">6</option>
								<option value = "7">7</option>
								<option value = "8">8</option>
								<option value = "9">9</option>
								<option value = "other">Other</option>
							</select>
						</p>
						<p>
							<span class = "book_meta_label">Author: </span>
							<input id="isbn10" name="book_author" class="element text medium" type="text" maxlength="255" value="<?php echo $author;?>"/> 
						</p>
						<p>
							<span class = "book_meta_label">Cost: </span>
							<input id="isbn10" name="book_cost" class="element text medium" type="number" maxlength="255" value="<?php echo $book_cost;?>"/> 
						</p>
						<p>
							<span class = "book_meta_label">Trade-In Value: </span>
							<input id="isbn10" name="trd_in_value" class="element text medium" type="number" maxlength="255" value="<?php echo $trd_in_value;?>"/> 
						</p>	
						<p>
							<span class = "book_meta_label">Tracking Number from Pre-Paid Label: </span>
							<input id="isbn10" name="track_number" class="element text medium" type="text" maxlength="255" value="<?php echo $track_number;?>"/> 
						</p>	
						<p>
							<span class = "book_meta_label">Seller / Rejected tracking number </span>
							<input id="seller_tracking_number" name="seller_tracking_number" class="element text medium" type="text" maxlength="50" 
							value="<?php echo $seller_tracking_number;?>"/> 
						</p>						
						<p>
							<span class = "book_meta_label">Seller: </span>
							<input id="isbn10" name="seller_name" class="element text medium" type="text" maxlength="255" value="<?php echo $seller_name;?>"/> 
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
<script type="text/javascript">
	var isbn13 = document.getElementById('isbn13'),
    cleanISBN;

	cleanISBN= function(e) {
    e.preventDefault();
    var pastedText = '';
    if (window.clipboardData && window.clipboardData.getData) { // IE
        pastedText = window.clipboardData.getData('Text');
      } else if (e.clipboardData && e.clipboardData.getData) {
        pastedText = e.clipboardData.getData('text/plain');
      }
    this.value = pastedText.replace(/\D/g, '');
};

isbn13.onpaste = cleanISBN;

var isbn10 = document.getElementById('isbn10'),
cleanISBN;
isbn10.onpaste = cleanISBN;

</script>

<script type="text/javascript">
	
function digits_only(t){
  if(t.value.match(/\D/g)){
    t.value=t.value.replace(/\D/g,'');
  }
}
</script>		
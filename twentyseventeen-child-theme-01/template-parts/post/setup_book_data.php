	<?php
	global $trd_amazon_tracker;
	$user_id = get_current_user_id();
	$book_post_author_user_id = get_the_author_meta('ID'); 
	$post_id = get_the_ID();
	//get tracking registered at aftership and get tracking status
	$tracking_id_aftership = get_post_meta($post_id, 'tracking_id_aftership', true);
	//echo $tracking_id_aftership;
	$book_title = get_post_meta($post_id, 'book_title', true);
	if (empty(trim($book_title))) {
		$book_title = get_the_title();
	}
	$warehouse_status = get_post_meta($post_id, 'track_status_manual', true);

	$track_status = $trd_amazon_tracker->check_tracking_status($tracking_id_aftership);
	//print_r($track_status);
	$track_status_tag = $track_status['data']['tracking']['tag'];	
	$expected_delivery = $track_status['data']['tracking']['expected_delivery'];
	$courier = $track_status['data']['tracking']['slug'];
	if (empty(trim($track_status_tag))) {
		$track_status_tag = "N/A";
	}
	//print_r($track_status);

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
	$user_status = get_post_meta($post_id, 'User status', true);
	$seller_tracking_number = get_post_meta($post_id, 'seller_tracking_number', true);
	$comments = $trd_amazon_tracker->get_book_comment($post_id);
	//check track status
	$is_premium = $user_id == $book_post_author_user_id && current_user_can('premium');

	$last_checkpoint = $trd_amazon_tracker->get_last_checkpoint($tracking_id_aftership);
	$last_checkpoint_city = $last_checkpoint['data']['checkpoint']['city'];

	?>
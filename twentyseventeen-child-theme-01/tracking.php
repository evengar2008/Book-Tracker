<?php
/**
 * Template Name: Tracking
 */
get_header(); ?>
<script type="text/javascript" src="/wp-content/plugins/amazon_tracker/css/responsive-tables.js"></script>

<script src="/wp-content/plugins/amazon_tracker/css/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/amazon_tracker/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/amazon_tracker/css/jquery-ui.structure.css" />
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/amazon_tracker/css/jquery-ui.theme.css" />

<main class="site-main tracking">

	<div class="main-content">

		<!-- check if user is logged in -->

		<?php if (is_user_logged_in() ):?>

			<?php if (current_user_can('administrator') || current_user_can('premium') || current_user_can('warehouse_admin') ) :?>
			<div class="entry-content">
				<?php 
				global $trd_amazon_tracker;
				$user_id = get_current_user_id();
				//check if admin
				$is_admin = current_user_can('administrator') || current_user_can('warehouse_admin');
				$is_premium = current_user_can('premium');

				//update status column
				if (isset($_GET['submit']) && isset($_GET['book_id']) && isset($_GET['user_status']) ) {
					$book_id = $_GET['book_id'];
					$book_status = $_GET['user_status'];
					$update = $trd_amazon_tracker->update_book_status($book_id, $book_status, $is_admin);
					if ($update) {
						echo 'Book status updated!';
					}
					else {
						echo 'Error updating book status';
					}
				}

				if (isset($_GET['date_from']) && isset($_GET['date_to']) ) {
					$date_from = urldecode($_GET['date_from']);
					$date_to = urldecode($_GET['date_to']);

					$dates = array(
						'date_from' => $date_from,
						'date_to'	=> $date_to,
						);
				}

				if ( isset( $_GET['search_books']) ) {
					$keyword = $_GET['search_books'];
				}

				if ( isset( $_GET['search_meta_key']) ) {
					$meta_key = $_GET['search_meta_key'];
				}

				//set up pagination
				//page numbers
				if (isset($_GET['pn'])) {
					$current_page = $_GET['pn'];
				}
				else {
					$current_page = 1;
				}				
				//get books	count	
				$books_count = $trd_amazon_tracker->generate_tracked_books_form($current_page, $dates, $keyword, $count = -1, $meta_key );
				//$books_count = count($books);
				//echo $books_count;

				$nb_elem_per_page = 10;
				$number_of_pages = intval($books_count/$nb_elem_per_page)+1;
				//launch main query
				//echo $keyword;
				$books = $trd_amazon_tracker->generate_tracked_books_form($current_page, $dates, $keyword, $count = 10, $meta_key);				
				//print_r($books);
				?>
				<!-- Datepicker fields -->
				<form id = "datepicker_form" action = "?dates">
					<div class = "datepicker_wrapper">
							<div class = "datepicker_field_wrapper">
								<label for = "date_from" class = "datepicker_label">Date from</label>
								<input class="datepicker" id = "date_from" name ="date_from" data-date-format="yy-mm-dd" value = "<?php echo $date_from;?>"/>
							</div>

							<div class = "datepicker_field_wrapper">
								<label for = "date_to" class = "datepicker_label">Date to</label>
								<input class="datepicker" id = "date_to" name ="date_to" data-date-format="yy-mm-dd" value = "<?php echo $date_to;?>"/>
							</div>
					</div>

					<div class = "search_wrapper">
							
							<?php if (!empty($meta_key)):?>
								<div id = "hidden_meta_key" style = "display:none"><?php echo $meta_key;?></div>
							<?php endif;?>								
							<label for = "search_meta_key">Search By:</label>
								<select name = "search_meta_key" class = "search_meta_key">
									<option value = "">All parameters</option>
									<!-- current search meta key -->
									<option value = "tracking_status_warehouse">Shipping Status</option>
									<option value = "track_status_manual">Warehouse Status</option>
									<option value = "user_status">User Status</option>
									<option value = "book_title">Book Title</option>
									<option value = "isbn13">ISBN13</option>
									<option value = "isbn10">ISBN10</option>
									<option value = "trn_id">TRN-ID</option>
									<option value = "author">Author</option>
									<option value = "book_cost">Cost, $</option>
									<option value = "trd_in_value">Trade-in, $</option>
									<option value = "track_number">Tracking number</option>
									<option value = "seller_tracking_number">Seller Tracking number</option>

								</select>

								<input class="search_books" id = "search_books" name ="search_books"  type = "text" placeholder = "Enter a search keyword" value = "<?php echo $keyword;?>"/>	
								
					</div>
					<div id = "sarch_control_buttons">
						<a href = "javascript:void(0)" id = "reset_search_books_btn">Reset</a>
						<input type = "submit" value = "Search" class = "search_submit_btn"/>	
					</div>				
				</form>	

				<script>
					jQuery( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
					jQuery("#reset_search_books_btn").click(function(){
						event.preventDefault();
						jQuery(".search_meta_key").val('');
						jQuery("#search_books").val('');
						jQuery("#date_from").val('');
						jQuery("#date_to").val('');
					});
				</script>
				<br />

				<table class="compat-table sortable tabelsorter responsive" id = "books_table">
					<thead>
						<tr class = "compat-table head">
							<th>Shipping Status</th>
							<th>Warehouse Status</th>
							<th>User Status</th>
							<th>Date</th>
							<th>Title / ISBN</th>
							<th>TRN-ID</th>
							<th>Author</th>
							<th>Cost, $</th>
							<th>Trade In, $</th>
							<th>Profit, $</th>
							<th>Attachment</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($books as $book) :?>
					<?php
					//get tracking registered at aftership and get tracking status
						$aftership_track_id = $book['tracking_id_aftership'];
						
						if (!empty($aftership_track_id)) {
							$track_status = $trd_amazon_tracker->check_tracking_status($aftership_track_id);
							$track_status_tag = $track_status['data']['tracking']['tag'];
						} 
						else {
							$track_status_tag  = "N/A";
						}
						if (empty($book['track_status_manual'])) {
							$warehouse_status = "N/A";
						}
						else {
							$warehouse_status = $trd_amazon_tracker->status_dict($book['track_status_manual']);
						}
					?>
					
						<tr>
							<td><?php echo $track_status_tag; ?></td>
							<td><?php echo $warehouse_status; ?></td>
							<td><?php echo $trd_amazon_tracker->status_dict($book['user_status']); ?></td>
							<td><?php echo $book['date'];?></td>
							<td><?php echo $book['book_title'];?>
								<span class="isbn"><?php echo $book['isbn10'];?></span>
								<span class="isbn"><?php echo $book['isbn13'];?></span>
							</td>
							<td><?php echo $book['trn_id'];?></td>
							<td><?php echo $book['author'];?></td>
							<td><?php echo $book['book_cost'];?></td>
							<td><?php echo $book['trd_in_value'];?></td>
							<td><?php echo floatval($book['trd_in_value']) - floatval($book['book_cost']) ;?> </td>
							<td><?php if (!empty($book['attachment_url'])):?><a href = "<?php echo $book['attachment_url'];?>">Attachment</a><?php endif;?></td>
							<!-- Premium user actions-->
							<?php if ( $is_premium ):?>	
								<td>
									<a class="book_view_btn" href="<?php echo get_permalink($book['ID']);?>">View/Edit</a>
									<span class="book_actions">
										<a class="book_actions_btn" href= "#">Actions <i class="fa fa-caret-down" aria-hidden="true"></i></a>
										<ul class="actions_dropdown">
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=payment_received">Payment Received</a>
											</li>
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=rejected_by_buyer">Rejected by Buyer</a>
											</li>
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=refunded_by_seller">Refunded By Seller</a>
											</li>
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=donate_or_destroy">Donate or Destroy</a>
											</li>
											<li>
												<a href="<?php echo get_permalink($book['ID']);?>">Enter Seller Tracking Information</a>
											</li>
										</ul>
									</span>
								</td>
							<!-- if admin -->
							<?php else:?>

								<td>
									<a class = "book_view_btn" href = "<?php echo get_permalink($book['ID']);?>">View/Edit</a>
									<span class="book_actions">
										<a class="book_actions_btn" href= "#">Actions <i class="fa fa-caret-down" aria-hidden="true"></i></a>
										<ul class="actions_dropdown">
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=waiting">Waiting</a>
											</li>
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=received">Received</a>
											</li>
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=sent_out">Sent out</a>
											</li>
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=received_with_issues">Received with issues</a>
											</li>
											<li>
												<a href="?submit=true&book_id=<?php echo $book['ID'];?>&user_status=label_voided">Label voided</a>
											</li>											
											<li>
												<a href="<?php echo get_permalink($book['ID']);?>">Enter Seller Tracking Information</a>
											</li>
										</ul>
									</span>									
								</td>		
							<?php endif;?>
						</tr>
					
					<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- .entry-content -->

			<div class = "paginator_wrapper">

				<ul id='paginator'>
					<?php
					wp_reset_query();
					$query_string = "&date_from=$date_from&date_to=$date_to&search_meta_key=$meta_key&search_books=$keyword";
					for ($i=1; $i <= $number_of_pages; $i++): ?>
						<li class = "paginate-links"><a href="<?php echo get_permalink() . '?pn=' . $i . $query_string; ?>"><?php echo $i;?></a></li>
					<?php endfor; ?>
				</ul>
					<span id ="page_status">Page <span class = "active_page_search"><?php echo $current_page; ?></span> of <?php echo $number_of_pages; ?></span>
			</div>

				<?php else:?>
					<div class="entry-content">
						<p>You have to be logged in as a Premium User to access this page</p>
					</div>	
				<?php endif;?>

			<?php else: ?>
				<div class="entry-content">
					<p>You have to be logged in to access this page</p>
					<a href = "<?php echo wp_login_url(); ?> ">Log In/Register</a>
				</div>	
		<?php endif;?>
	</div>
</main>
<script src = "<?php echo get_stylesheet_directory_uri();?>/js/jquery.tablesorter.js"></script>
<script type="text/javascript">
	jQuery("#books_table").tablesorter(); 
</script>
<script>
(function($) {
	$(function() {
		var meta_key = jQuery("#hidden_meta_key").text();
		jQuery(".search_meta_key option").each(
		function() {
			if (jQuery(this).val()==meta_key) {
				jQuery(this).attr('selected','selected');
			}

		}
		)

		$( '.book_actions_btn' ).on( 'click', function(event) {
			event.preventDefault();
			$(this).parent().find('ul').fadeToggle(100);
			$(this).toggleClass( 'active' );
		});

	});
}(jQuery));
</script>
<?php get_footer();

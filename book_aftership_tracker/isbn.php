<?php
/*
  Plugin Name: Amazon Trade-In Book ISBN Tracker
  Plugin URI:
  Description: Monitor second-hand prices on books on Amazon using ISBN
  Version: 1.0
  Author: mechanical-pie
  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  

//new amazon tracker class instance
function amazon_tracker() {
	global $trd_amazon_tracker;
    $trd_amazon_tracker = new amazon_tracker();
}
//load the plugin
add_action('plugins_loaded', 'amazon_tracker', 0);

//Declare our class for amazon trade-in tracker
Class amazon_tracker {
	//class constructor and WP actions hooks
	function __construct() {
		global $wp_roles;
		add_action( 'admin_menu', array($this, 'amzisbn_add_admin_menu') );
		add_action( 'admin_init', array($this, 'amzisbn_settings_init') );
		add_shortcode( 'book_form', array($this,'generate_book_form_shortcode') );
		add_action('init', array($this,'register_book_post_type'));
		add_action('wp_enqueue_scripts', array($this, "add_isbn_css"));
		add_action('init', array($this,'add_roles_on_plugin_activation') );	
		add_action('after_setup_theme', array($this,'remove_admin_bar') );
		add_filter( 'login_redirect', array($this,'acme_login_redirect'), 10, 3 );	
		add_action('admin_init', array($this,'no_mo_dashboard') );
		add_action('init', array($this,'wps_change_role_name' ) );
		add_option( 'tracking_last_run', '' );
		add_action('init', array($this,'register_book_post_type'));
		add_action('init', array($this,'scheduler'));
		add_action('wp_ajax_ajax_book_update', array($this,'ajax_book_update_callback') );
		add_action( 'wp_enqueue_scripts', array($this,'myajax_data'), 99 );
		add_action( 'register_form', array($this,'isbn_register_form' ) ); 
		add_filter( 'registration_errors', array($this,'isbn_registration_errors'), 10, 3 );
		add_action( 'user_register', array($this,'isbn_user_register' ) );
	}
	//check scheduled actions
	function scheduler() {
		$date = date("Y-m-d H:i:s"); 
		//echo $date;
		$last_run = get_option('last_run');
		//if one hour passed since last run
		if (strtotime($last_run)+ 3600 < strtotime($date)) {
			//echo "1 hour passed!";
			update_option('last_run', $date);
			$this->run_scheduled_actions();
			return true;
		}
		else {
			return false;
		}
		
	}

	//scheduled actions
	function run_scheduled_actions() {
		global $wpdb;
		//get all the books from the database
		$table_name = $wpdb->prefix . 'posts';
		$books = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `post_type` = 'book';", ARRAY_A);
		foreach ($books as $book) {
			$book_id = $book['ID'];
			//echo $book_id;
			$user_id = $this->get_book_user_author($book_id);
			//get tracking ids
			$tracking_id_warehouse = get_post_meta($book_id, 'tracking_id_aftership', true);
			$tracking_id_seller = get_post_meta($book_id, 'seller_tracking_number', true);
			//get old valuss
			$tracking_status_warehouse = get_post_meta($book_id, 'tracking_status_warehouse', true);
			$tracking_status_seller = get_post_meta($book_id, 'tracking_status_seller', true);
			//get new values
			if (!empty($tracking_id_warehouse)) {
				$tracking_status_warehouse_new_full = $this->check_tracking_status($tracking_id_warehouse);
				$tracking_status_warehouse_new = $tracking_status_warehouse_new_full['data']['tracking']['tag'];
				//if tracking status changed, update it in the meta. same if it is first-run
				if ($tracking_status_warehouse != $tracking_status_warehouse_new) {
					update_post_meta($book_id, 'tracking_status_warehouse', $tracking_status_warehouse_new);
					$this->mail_on_track_update($user_id, $tracking_status_warehouse_new, $book_id);
				}
			}
			if (!empty($tracking_id_seller)) {
				$tracking_id_aftership_warehouse_query = $this->register_tracking_at_aftership($tracking_id_seller);
				$tracking_id_aftership_warehouse = $tracking_id_aftership_warehouse_query['data']['tracking']['id'];

				$tracking_status_seller_new_full = $this->check_tracking_status($tracking_id_aftership_warehouse);
				$tracking_status_seller_new = $tracking_status_seller_new_full['data']['tracking']['tag'];

				if ($tracking_status_seller != $tracking_status_seller_new) {
					update_post_meta($book_id, 'tracking_status_seller', $tracking_status_seller_new);
					$this->mail_on_track_update($user_id, $tracking_status_seller_new, $book_id);
				}				
			}
			//end with checks
		}
		//end with books loop
	}

	//send notifications
	function mail_on_track_update($user_id, $status, $book_id) {
		$user = new WP_User($user_id);
		$book_title = get_post_meta($book_id, 'book_title', true);
		$user_email = $user->user_email;
		$subject = "Book tracking status updated";
		$message = "Tracking status of book $book_title you have added at the website has been updated. New tracking status is $status";
		$is_sent_ok = wp_mail( $user_email, $subject, $message, $headers = '');
		if ($is_sent_ok) {
			return true;
		}
		else {
			return false;
		}
	}

	//book meta dict
	function book_meta_dict($key) {
		$dict = array(
			'seller_name'=>'Seller name',
			'shipped_to_name'=>'Shipped to name',
			'trn_id'=>'TRN-ID',
			'book_title'=>'Title',
			'comments'=>'Comments',
			'track_number'=>'Track number',
			'author'=>'Author',
			'tracking_status_warehouse'=>'Shipping status',
			'track_status_manual'=>'Warehouse Status',
			'book_cost'=>'Cost, $',
			'user_status'=>'User Status',
			'seller_tracking_number' => 'Seller Tracking number',
			);
		return $dict[$key];		
	}

	//user/warehouse book status dictionary
	function status_dict($key) {
		$dict = array(
			'payment_received'=>'Payment Received',
			'rejected_by_buyer'=>'Rejected by Buyer',
			'refunded_by_seller'=>'Refunded By Seller',
			'donate_or_destroy'=>'Donate or Destroy',
			'waiting'=>'Waiting',
			'received'=>'Received',
			'sent_out'=>'Sent out',
			'received_with_issues'=>'Received with issues',
			'label_voided'=>'Label voided',
			);
		return $dict[$key];
	}
	//Fistname add to the WP reg form
	function isbn_register_form() {
		require_once('templates_shortcodes/register_form.php');
	}

	function get_book_user_author($book_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'posts';
		$book_post_user_author_sql = $wpdb->get_results("SELECT post_author FROM `$table_name` WHERE `ID` = '$book_id';", ARRAY_N);
		$book_post_user_author = $book_post_user_author_sql[0][0];
		return $book_post_user_author;
	}

	//update book status from the table
	function update_book_status($book_id, $user_status, $is_admin) {
		$user_id = get_current_user_id();
		$book_post_author_user_id = $this->get_book_user_author($book_id);
		$is_admin = current_user_can('administrator') || current_user_can('warehouse_admin');

		if ($is_admin) {
			update_post_meta($book_id, 'track_status_manual', $user_status);
			return true;
		}
		else {
			if ($book_post_author_user_id == $user_id ) {
				update_post_meta($book_id, 'user_status', $user_status);
				return true;
			}
			else {
				return false;
			}
		}
	}

	//2. Add validation. In this case, we make sure first_name is required.
	function isbn_registration_errors( $errors, $sanitized_user_login, $user_email ) {

	    if ( empty( $_POST['first_name'] ) || ! empty( $_POST['first_name'] ) && trim( $_POST['first_name'] ) == '' ) {
	    $errors->add( 'first_name_error', __( '<strong>ERROR</strong>: You must include a first name.', 'mydomain' ) );
	    }

	    if ( empty( $_POST['last_name'] ) || ! empty( $_POST['last_name'] ) && trim( $_POST['last_name'] ) == '' ) {
	    $errors->add( 'last_name_error', __( '<strong>ERROR</strong>: You must include a last name.', 'mydomain' ) );
	    }

	    return $errors;
	}

	//3. Finally, save our extra registration user meta.
	function isbn_user_register( $user_id ) {
	    if ( ! empty( $_POST['first_name'] ) ) {
	        update_user_meta( $user_id, 'first_name', trim( $_POST['first_name']  ));
	    }

	    if ( ! empty( $_POST['last_name'] ) ) {
	        update_user_meta( $user_id, 'last_name', trim( $_POST['last_name'] ) );
	    }
	}

	//add ajax object and url
	function myajax_data(){

		// Первый параметр 'twentyfifteen-script' означает, что код будет прикреплен к скрипту с ID 'twentyfifteen-script'
		// 'twentyfifteen-script' должен быть добавлен в очередь на вывод, иначе WP не поймет куда вставлять код локализации
		// Заметка: обычно этот код нужно добавлять в functions.php в том месте где подключаются скрипты, после указанного скрипта
		wp_localize_script('jquery', 'myajax', 
			array(
				'url' => admin_url('admin-ajax.php')
			)
		);  
	}	
	//disable WP Dashboard for non-admins
	function remove_admin_bar() {	
		if (!current_user_can('administrator') && !is_admin()) {
		  show_admin_bar(false);
		}
	}
	//redirect to home page if not admin
	function acme_login_redirect( $redirect_to, $request, $user  ) {
		return ( is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) ? admin_url() : site_url();
	}
	//no admin dashboard for users, only for admins
	function no_mo_dashboard() {
	  if (!current_user_can('manage_options') && $_SERVER['DOING_AJAX'] != '/wp-admin/admin-ajax.php') {
	  wp_redirect(home_url()); exit;
	  }
	}
	//change 'Subscriber' user role name to 'User'
	function wps_change_role_name() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();
		$wp_roles->roles['subscriber']['name'] = 'User';
		$wp_roles->role_names['subscriber'] = 'User';
	}

	//Add Premium User role
    function add_roles_on_plugin_activation() {
       add_role( 'premium', 'Premium User',     array(
        'read'              => false, // Allows a user to read
        'create_posts'      => true, // Allows user to create new posts
        'edit_posts'        => true, // Allows user to edit their own posts
        'edit_others_posts' => false, // Allows user to edit others posts too
        'publish_posts'     => true, // Allows the user to publish posts
        'manage_categories' => false, // Allows user to manage post categories
        ) );

       add_role( 'warehouse_admin', 'Warehouse admin',     array(
        'read'              => true, // Allows a user to read
        'create_posts'      => true, // Allows user to create new posts
        'edit_posts'        => true, // Allows user to edit their own posts
        'edit_others_posts' => true, // Allows user to edit others posts too
        'publish_posts'     => true, // Allows the user to publish posts
        'manage_categories' => false, // Allows user to manage post categories
        ) );       
    }
  
	//add CSS
	function add_isbn_css(){
    	wp_enqueue_style( 'isbn-style', plugins_url('/css/isbn.css', __FILE__), false, '1.0.0', 'all');
    	wp_enqueue_style( 'isbn-style-tables', plugins_url('/css/responsive-tables.css', __FILE__), false, '1.0.0', 'all');
    }	
    //load template
	function generate_book_form() {
		require_once('templates_shortcodes/form.php');
	}
	//shortcode wrapper fpr generate book form function
	function generate_book_form_shortcode( $atts ) {
		echo $this->generate_book_form();
		return;
	}
	//load template
	public function generate_tracked_books_form($page = 1, $dates = array(), $keyword = '', $count = 10, $meta_key = '' ) {
		$user_id = get_current_user_id();
		$is_admin = current_user_can('administrator') || current_user_can('warehouse_admin');
		//check the dates parameters
		$meta_query = array();
		//echo $meta_key;

		if (!empty(trim($meta_key)) && !empty($keyword)) {
			$meta_query = array(
					//'relation' => 'AND',
					array(
						'key' => $meta_key,
						'value' => $keyword,
						'compare' => 'LIKE',
					),
				);	
		}
		$search = '';

		if (empty(trim($meta_key)) && !empty($keyword)) {
			$meta_query = array(
					//'relation' => 'AND',
					array(
						'value' => $keyword,
						'compare' => 'LIKE',
					),
				);
		}

		//'meta_query' => 

		if (!empty($dates)) {
			$date_from = $dates['date_from'];
			$date_to = $dates['date_to'];
		}
		//define WP Query parameters
		if ($is_admin) {
			$query = new WP_Query( array( 'post_type' => 'book', 'posts_per_page' => $count, 'paged' => $page, 
				//'s' => $keyword, 
				//define date query
				'date_query' => array( array(
						'after'     => $date_from,
						'before'    => $date_to,
					)),
				//end date query
				'meta_query' => $meta_query,
			 ) ); 
		}
		else {
			$query = new WP_Query( array( 'post_type' => 'book', 'posts_per_page' => $count, 'author' =>$user_id, 'paged' => $page, 
				//'s' => $keyword, 
				//define date query
				'date_query' => array( array(
						'after'     => $date_from,
						'before'    => $date_to,
					)),
				//end date query
				'meta_query' => $meta_query,
			  ) ); 
		}
		//declare empty books array
		$books = array();
		//check count
		if ($count!= -1) {
			//start WP query
			while ( $query->have_posts() ) 
				{
				$query->the_post();
				$id = get_the_ID();
				$date = get_the_date();
				//try go get book title from meta
				$book_title = get_post_meta($id, 'book_title', true);
				//otherwise, get it frpm the post title
				if (empty($book_title)){
					$book_title = get_the_title(); 
				}
				//get other meta
				$seller_email = get_post_meta($id, 'seller_email', true);
				$shipped_to_name = get_post_meta($id, 'shipped_to_name', true);
				$trade_in_to = get_post_meta($id, 'trade_in_to', true);
				$trn_id = get_post_meta($id, 'trn_id', true);
				$isbn13 = get_post_meta($id, 'isbn13', true);
				$isbn10 = get_post_meta($id, 'isbn10', true);
				$edition = get_post_meta($id, 'edition', true);
				$author = get_post_meta($id, 'author', true);
				$book_cost = get_post_meta($id, 'book_cost', true);
				$trd_in_value = get_post_meta($id, 'trd_in_value', true);
				$track_number = get_post_meta($id, 'track_number', true);
				$seller_name = get_post_meta($id, 'seller_name', true);
				$attachment_url = get_post_meta($id, 'attachment_url', true);
				$comments = get_the_content(); 
				$tracking_id_aftership = get_post_meta($id, 'tracking_id_aftership', true);
				$track_status_manual = get_post_meta($id, 'track_status_manual', true);
				$user_status = get_post_meta($id, 'user_status', true);
				//prepare books array for template output
				$books[]=array(
					'book_title'=>$book_title, 
					'seller_email' =>$seller_email, 
					'date' => $date,
					'shipped_to_name' => $shipped_to_name, 
					'trade_in_to'=> $trade_in_to, 
					'trn_id' => $trn_id, 
					'isbn13' => $isbn13, 
					'isbn10' => $isbn10,
					'edition' => $edition,
					'book_cost' => $book_cost,
					'trd_in_value' => $trd_in_value,
					'track_number' => $track_number,
					'seller_name' => $seller_name,
					'attachment_url' => $attachment_url,
					'comments' => $comments,
					'author' => $author,
					'ID' 	=> $id,
					'tracking_id_aftership' => $tracking_id_aftership,
					'track_status_manual' 	=> $track_status_manual,
					'user_status'			=> $user_status,
					 );
				//end WP Query per one post	
				}
			return $books;
		}
		else {
			return $query->post_count;
		}
	}

	//validate ISBN and other fields
	function validate_book_fields() {
		$errors = "";
		$isbn13 = $this->clean_isbn($_POST['isbn13']);
		$isbn10 = $this->clean_isbn($_POST['isbn10']);
		if ( isset($_POST['submit']) ) {
			if (iconv_strlen($isbn13)!=13) {
				$errors .= "Invalid ISBN13 format <br />";
			}
			if (iconv_strlen($isbn10)!=10) {
				$errors .= "Invalid ISBN10 format <br />";
			}
		}
		return $errors;
	}
	/**
	 * converts a Hebrew string to Latin transliteration
	 */
	function hebrew2latin($hebrew) {
		$hebrew = strtr($hebrew,
			" אבגדהוזחטיכךלםמןסעפףצץקרשת",
			"_ABGDHWZXFYKKLMMNNSEPPCCQRJT");
		return $hebrew;
	}

	//helper function to handle uploaded files
	function upload_user_file( $file = array(), $post_id ) {

	    require_once( ABSPATH . 'wp-admin/includes/admin.php' );

	      $file_return = wp_handle_upload( $file, array('test_form' => false ) );

	      if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
	          return false;
	      } else {

	          $filename = $file_return['file'];

	          $attachment = array(
	              'post_mime_type' => $file_return['type'],
	              'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
	              'post_content' => '',
	              'post_status' => 'inherit',
	              'guid' => $file_return['url']
	          );

	          $attachment_id = wp_insert_attachment( $attachment, $file_return['url'], $post_id );

	          require_once(ABSPATH . 'wp-admin/includes/image.php');
	          $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
	          wp_update_attachment_metadata( $attachment_id, $attachment_data );

	          if( 0 < intval( $attachment_id ) ) {
	            return $attachment_id;
	          }
	      }

	      return false;
	}
	//save book as a post of custom post type
	function save_book_fields() {
		if ( isset($_POST['submit']) ) {
			//print_r($_POST);
			// Create an array for postdata 
			$post_data = array(
			  'post_title'    => wp_strip_all_tags( $_POST['book_title'] ),
			  'post_content'  => wp_strip_all_tags($_POST['comments']),
			  'post_status'   => 'publish',
			  'post_type'   => 'book',
			);

			// Create new books post
			$post_id = wp_insert_post( $post_data );
			if( !empty( $_FILES ) ) 
		       {
		          $file=$_FILES['pdf_uploaded'];
		          $attachment_id = $this->upload_user_file( $file, $post_id );

		       }	
		    //prepate temp variables
			$book_title = sanitize_text_field($_POST['book_title']);
			$seller_email = sanitize_text_field($_POST['seller_email']);
			$seller_name = sanitize_text_field($_POST['seller_name']);
			$shipped_to_name = sanitize_text_field($_POST['shipped_to_name']);
			$trade_in_to = sanitize_text_field($_POST['trade_in_to']);
			$trn_id = sanitize_text_field($this->clean_trn_id($_POST['trn_id']));
			$isbn13 = sanitize_text_field($this->clean_isbn($_POST['isbn13']));
			$isbn10 = sanitize_text_field($this->clean_isbn($_POST['isbn10']));
			$edition = $_POST['edition'];
			$author = sanitize_text_field($_POST['book_author']);
			$book_cost = $_POST['book_cost'];
			$trd_in_value = $_POST['trd_in_value'];
			$track_number = sanitize_text_field($_POST['track_number']);
			$comments = sanitize_text_field($_POST['comments']);
			$seller_tracking_number = sanitize_text_field($_POST['seller_tracking_number']);
			//register tracking at aftership with gived track number
			if (isset($_POST['track_number'])) {
				//register tracking at aftership with a given id
				$tracking_api_response  = $this->register_tracking_at_aftership($_POST['track_number']);
				$tracking_id = $tracking_api_response['data']['tracking']['id'];
				//check tracking status
				$track_status = $this->check_tracking_status($tracking_id);
				$track_status_tag = $track_status['data']['tracking']['tag'];				
			}			
			//save post meta
			//save tracking id and status in meta
			update_post_meta($post_id, 'tracking_id_aftership', $tracking_id);
			//set track status
			update_post_meta($post_id, 'track_status', $track_status_tag);
			//save other meta
			update_post_meta($post_id, 'seller_email', $seller_email);
			update_post_meta($post_id, 'shipped_to_name', $shipped_to_name);
			update_post_meta($post_id, 'trade_in_to', $trade_in_to);
			update_post_meta($post_id, 'trn_id', $trn_id);
			update_post_meta($post_id, 'isbn13', $isbn13);
			update_post_meta($post_id, 'isbn10', $isbn10);
			update_post_meta($post_id, 'edition', $edition);
			update_post_meta($post_id, 'book_title', $book_title);
			update_post_meta($post_id, 'book_cost', $book_cost);
			update_post_meta($post_id, 'trd_in_value', $trd_in_value);
			update_post_meta($post_id, 'track_number', $track_number);
			update_post_meta($post_id, 'seller_name', $seller_name);
			update_post_meta($post_id, 'author', $author);

			if (isset($_POST['seller_tracking_number'])) {
				update_post_meta($post_id, 'seller_tracking_number', $seller_tracking_number);

				$tracking_api_response  = $this->register_tracking_at_aftership($_POST['seller_tracking_number']);
				$tracking_id_seller = $tracking_api_response['data']['tracking']['id'];
				//check tracking status
				$track_status = $this->check_tracking_status($tracking_id_seller);
				$tracking_status_seller = $track_status['data']['tracking']['tag'];

				update_post_meta($post_id, 'tracking_status_seller', $tracking_status_seller);				
			}
			
			//check for attachments
			if (!empty($attachment_id)){
				$attachment_url = wp_get_attachment_url($attachment_id);
				update_post_meta($post_id, 'attachment_url',  $attachment_url);
			}
			//prepare email message text
			$message = "
				Book title: $book_title, \n
				Seller email: $seller_email, \n
				Seller name: $seller_name, \n
				Shipped To Name: $shipped_to_name, \n
				Trading In to: $trade_in_to, \n
				Trade-In Transaction ID (TRN-ID): $trn_id, \n
				13 Digit ISBN: $isbn13, \n
				10 Digit ISBN: $isbn10, \n
				Edition: $edition, \n
				Author: $author, \n
				Cost of book: $book_cost, \n
				Trade-in value: $trd_in_value, \n
				Tracking number: $track_number, \n
				Seller Tracking number: $seller_tracking_number, \n
				Comments: $comments, \n
				Attachment URL: $attachment_url,
			";
			//prepare email attachment
			//echo $attachment_filepath;
			//$attachments = array($attachment_filepath);
			//print_r($attachments);
			$current_user = wp_get_current_user();
			$user_email = $current_user->user_email;
			$admin_email = get_option('admin_email');
			//send email with form data to user
			wp_mail( $user_email, 'A new book was added', $message, $headers = '', $attachments );
			//send email with form data to admin
			wp_mail( $admin_email, 'A new book was added', $message, $headers = '', $attachments );
		}
		return;
	}
	//update a book from the table
	function ajax_book_update_callback() {
		$user_status = $_POST['user_status'];
		echo $user_status;
		echo 'test ajax';
		//update_post_meta($post_id, 'user_status', $user_status);
		wp_die();
	}
	//clean ISBN
	function clean_isbn($isbn) {
		$string = $isbn;
		$pattern = '/(s|\W)/i';
		$replacement = '';
		$cleaned_isbn = preg_replace($pattern, $replacement, $string);
		return $cleaned_isbn;
	}
	//clean TRN ID
	function clean_trn_id($trn_id){
		$string = $trn_id;
		$pattern = '/\D/i';
		$replacement = '';
		$cleaned_trn_id = preg_replace($pattern, $replacement, $string);
		return $cleaned_trn_id;
	}
	//save book fields edited from single book page (if user is admin)
	function save_book_edit_fields($post_id) {
	    //prepate temp variables
	    //print_r($_POST);
		$user_status = $_POST['user_status'];
		$book_title = sanitize_text_field($_POST['book_title']);
		$seller_name = sanitize_text_field($_POST['seller_name']);
		$shipped_to_name = sanitize_text_field($_POST['shipped_to_name']);
		$book_author = sanitize_text_field($_POST['book_author']);
		$trn_id = sanitize_text_field($this->clean_trn_id($_POST['trn_id']));
		$isbn13 = sanitize_text_field($this->clean_isbn($_POST['isbn13']));
		$isbn10 = sanitize_text_field($this->clean_isbn($_POST['isbn10']));
		$edition = $_POST['edition'];
		$book_cost = $_POST['book_cost'];
		$trd_in_value = $_POST['trd_in_value'];
		$track_number = sanitize_text_field($_POST['track_number']);
		$track_status = $_POST['track_status'];
		//track status manual is warehouse status
		$track_status_manual = $_POST['track_status_manual'];
		$seller_tracking_number = sanitize_text_field($_POST['seller_tracking_number']);
		$comments = sanitize_text_field($_POST['book_comments']);
		//save other meta/
		$updated_fields = array();

		if (!empty(trim($book_title))) {
			update_post_meta($post_id, 'book_title', $book_title);
			$updated_fields['Book Title'] = $book_title;
		}
		if (!empty(trim($shipped_to_name))) {
			update_post_meta($post_id, 'shipped_to_name', $shipped_to_name);
			$updated_fields['Shipped to name'] = $shipped_to_name;
		}
		if (!empty(trim($isbn13))) {
			update_post_meta($post_id, 'isbn13', $isbn13);
			$updated_fields['ISBN13'] = $isbn13;
		}
		if (!empty(trim($isbn10))) {
			update_post_meta($post_id, 'isbn10', $isbn10);
			$updated_fields['ISBN10'] = $isbn10;
		}						
		if (!empty(trim($edition))) {
			update_post_meta($post_id, 'edition', $edition);
			$updated_fields['Edition'] = $edition;
		}	
		if (!empty(trim($book_cost))) {
			update_post_meta($post_id, 'book_cost', $book_cost);
			$updated_fields['Book cost'] = $book_cost;
		}
		if (!empty(trim($trd_in_value))) {
			update_post_meta($post_id, 'trd_in_value', $trd_in_value);
			$updated_fields['Trade-in value'] = $trd_in_value;
		}
		if (!empty(trim($track_number))) {
			update_post_meta($post_id, 'track_number', $track_number);
			$updated_fields['Track number'] = $track_number;
		}
		if (!empty(trim($seller_name))) {
			update_post_meta($post_id, 'seller_name', $seller_name);
			$updated_fields['Seller name'] = $seller_name;
		}	
		//echo $track_status_manual;			
		if (!empty(trim($track_status_manual))) {
			update_post_meta($post_id, 'track_status_manual', $track_status_manual);
			$updated_fields['Warehouse status'] = $track_status_manual;
		}
		if (!empty(trim($seller_tracking_number))) {
			update_post_meta($post_id, 'seller_tracking_number', $seller_tracking_number);
			$tracking_api_response  = $this->register_tracking_at_aftership($seller_tracking_number);
			$tracking_id_seller = $tracking_api_response['data']['tracking']['id'];
			//check tracking status
			$track_status = $this->check_tracking_status($tracking_id_seller);
			$tracking_status_seller = $track_status['data']['tracking']['tag'];
			update_post_meta($post_id, 'tracking_status_seller', $tracking_status_seller);	
			$updated_fields['Seller tracking number'] = $seller_tracking_number;			
		}
		if (!empty(trim($user_status))) {
			update_post_meta($post_id, 'User status', $user_status);
		}
		if (!empty(trim($comments))) {
			$comments_update = $this->update_book_comments($post_id, $comments);
		}		

		$message = "";
		//prepare email message text
		foreach ($updated_fields as $key => $value) {
				$message .= $key . ": " . $value . "\n";
			}	

		$current_user = wp_get_current_user();
		$user_email = $current_user->user_email;
		//send email with form data to user
		wp_mail( $user_email, 'Book edited', $message, $headers = '' );		
		//update books comments section
		return;
	}
	//helper function to update book comments
	function update_book_comments($book_id, $comments) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'posts';
		$query = $wpdb->prepare("UPDATE `$table_name` SET `post_content` = %s WHERE `ID` = %d;",  $comments, $book_id);
		//print_r($query);
		$update = $wpdb->query($query);
		return $update;
	}
	//helper function to get book comment
	function get_book_comment($book_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'posts';
		$query = "SELECT `post_content` FROM $table_name WHERE `ID` = $book_id;";
		$result = $wpdb->get_results($query, ARRAY_N);
		if (!empty($result)) {
			return $result[0][0];
		}
		else {
			return '';
		}
	}

	//Register new tracking at Aftership.com using their REST API
	function register_tracking_at_aftership($track_number) {
		$options = get_option( 'amzisbn_settings' );
		$api_key = $options['aftership_api_key'];
		$json_to_send = json_encode(
			array('tracking' => 
					array(
						'tracking_number' => $track_number,
					),
			));
		$aftership_request = curl_init('https://api.aftership.com/v4/trackings');
		curl_setopt($aftership_request, CURLOPT_HTTPHEADER,
			array('aftership-api-key:' . $api_key, 'Content-Type: application/json'));
		curl_setopt($aftership_request, CURLOPT_POST, true);
		curl_setopt($aftership_request, CURLOPT_POSTFIELDS, $json_to_send);
		curl_setopt($aftership_request, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($aftership_request);
		curl_close($aftership_request);
		return json_decode($data, true);

	}
	//Get tracking status using REST API
	function check_tracking_status($id){
		$options = get_option( 'amzisbn_settings' );
		$api_key = $options['aftership_api_key'];
		$aftership_request = curl_init('https://api.aftership.com/v4/trackings/' . $id);
		curl_setopt($aftership_request, CURLOPT_HTTPHEADER,
			array('aftership-api-key:' . $api_key, 'Content-Type: application/json'));
		curl_setopt($aftership_request, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($aftership_request);
		curl_close($aftership_request);		
		return json_decode($data, true);
	}
	//get last checkpoint
	function get_last_checkpoint($id) {
		$options = get_option( 'amzisbn_settings' );
		$api_key = $options['aftership_api_key'];
		$aftership_request = curl_init('https://api.aftership.com/v4/last_checkpoint/' . $id);
		curl_setopt($aftership_request, CURLOPT_HTTPHEADER,
			array('aftership-api-key:' . $api_key, 'Content-Type: application/json'));
		curl_setopt($aftership_request, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($aftership_request);
		curl_close($aftership_request);		
		return json_decode($data, true);		
	}

	//get books in amazon trade-in using REST API
	function get_books_by_isbn() {

		$options = get_option( 'amzisbn_settings' );
		$amazon_api_key = $options['amazon_access_key_id'];
		$amazon_key_secret = $options['amazon_access_key_secret'];


		return;
	}
	//add a new item into WP admin menu
	function amzisbn_add_admin_menu(  ) { 
		add_menu_page( 'Amazon ISBN tracker', 'Amazon ISBN tracker', 'manage_options', 'amazon_isbn_tracker', array($this,'amzisbn_options_page') );
	}

	//init settings for plugin
	function amzisbn_settings_init(  ) { 

		register_setting( 'pluginPage', 'amzisbn_settings' );

		add_settings_section(
			'amzisbn_pluginPage_section', 
			__( 'Fill in your Amazon AWS and Aftership API credentials', 'amzisbn' ), 
			array($this, 'amzisbn_settings_section_callback'), 
			'pluginPage'
		);

		add_settings_field( 
			'amazon_access_key_id', 
			__( 'Amazon Access Key ID', 'amzisbn' ), 
			array($this, 'amazon_key_id_render'), 
			'pluginPage', 
			'amzisbn_pluginPage_section' 
		);

		add_settings_field( 
			'amazon_access_key_secret', 
			__( 'Amazon Access Key Secret', 'amzisbn' ), 
			array($this, 'amazon_key_secret_render'), 
			'pluginPage', 
			'amzisbn_pluginPage_section' 
		);

		add_settings_field( 
			'aftership_api_key', 
			__( 'Aftership API key', 'amzisbn' ), 
			array($this, 'aftership_key_render'), 
			'pluginPage', 
			'amzisbn_pluginPage_section' 
		);

	}

	function aftership_key_render(  ) { 

		$options = get_option( 'amzisbn_settings' );
		?>
		<input type='text' name='amzisbn_settings[aftership_api_key]' value='<?php echo $options['aftership_api_key']; ?>'>
		<?php

	}

	function amazon_key_id_render(  ) { 

		$options = get_option( 'amzisbn_settings' );
		?>
		<input type='text' name='amzisbn_settings[amazon_access_key_id]' value='<?php echo $options['amazon_access_key_id']; ?>'>
		<?php

	}

	function amazon_key_secret_render(  ) { 

		$options = get_option( 'amzisbn_settings' );
		?>
		<input type='text' name='amzisbn_settings[amazon_access_key_secret]' value='<?php echo $options['amazon_access_key_secret']; ?>'>
		<?php

	}

	function amzisbn_settings_section_callback(  ) { 

		echo __( 'Fill in your Amazon AWS API credentials', 'amzisbn' );

	}

	function amzisbn_options_page(  ) { 

		?>
		<form action='options.php' method='post'>

			<h2>Amazon ISBN tracker</h2>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

		</form>
		<?php

	}
	//helper function to get current URL
	function get_current_url($strip = true) {
	    // filter function
	    static $filter;
	    if ($filter == null) {
	        $filter = function($input) use($strip) {
	            $input = str_ireplace(array(
	                "\0", '%00', "\x0a", '%0a', "\x1a", '%1a'), '', urldecode($input));
	            if ($strip) {
	                $input = strip_tags($input);
	            }

	            // or any encoding you use instead of utf-8
	            $input = htmlspecialchars($input, ENT_QUOTES, 'utf-8'); 

	            return trim($input);
	        };
	    }

	    return 'http'. (($_SERVER['SERVER_PORT'] == '443') ? 's' : '')
	        .'://'. $_SERVER['SERVER_NAME'] . $filter(strtok($_SERVER["REQUEST_URI"],'?'));
	}
	//register custom book post type with WP
	function register_book_post_type(){
		register_post_type('book', array(
			'label'  => null,
			'labels' => array(
				'name'               => 'book', // основное название для типа записи
				'singular_name'      => 'Book', // название для одной записи этого типа
				'add_new'            => 'Add a new book', // для добавления новой записи
				'add_new_item'       => 'Add a new Book', // заголовка у вновь создаваемой записи в админ-панели.
				'edit_item'          => 'Edit a book', // для редактирования типа записи
				'new_item'           => 'New Book', // текст новой записи
				'view_item'          => 'Open Book', // для просмотра записи этого типа.
				'search_items'       => 'Find a Book', // для поиска по этим типам записи
				'not_found'          => 'Not Found', // если в результате поиска ничего не было найдено
				'not_found_in_trash' => 'Not Found in trash', // если не было найдено в корзине
				'parent_item_colon'  => '', // для родителей (у древовидных типов)
				'menu_name'          => 'Trade-In Books', // название меню
			),
			'description'         => '',
			'public'              => true,
			'publicly_queryable'  => null,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true, // показывать ли в меню адмнки
			'show_in_admin_bar'   => true, // по умолчанию значение show_in_menu
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false, // добавить в REST API. C WP 4.7
			'rest_base'           => null, // $post_type. C WP 4.7
			'menu_position'       => null,
			'menu_icon'           => null, 
			//'capability_type'   => 'post',
			//'capabilities'      => 'post', // массив дополнительных прав для этого типа записи
			//'map_meta_cap'      => null, // Ставим true чтобы включить дефолтный обработчик специальных прав
			'hierarchical'        => false,
			'supports'            => array('title','editor', 'custom-fields', 'author'), // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
			'taxonomies'          => array(),
			'has_archive'         => false,
			'rewrite'             => true,
			'query_var'           => true,
		) );
	}

}

?>
<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>

<?php if (is_user_logged_in()):?>

	<?php require_once('setup_book_data.php'); ?>

	<?php if ($is_premium ): ?>
	<!-- load premium user template -->
	<?php require_once('content-book_premium.php');?>
	<!-- load admin user template-->
	<?php elseif ( current_user_can('administrator') ):?>
		<?php require_once('content-book_admin.php');?>
	<!-- load warehouse admin user template -->
	<?php elseif ( current_user_can('warehouse_admin') ):?>
		<?php require_once('content-book_warehouse_admin.php');?>
	<?php else: ?>
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
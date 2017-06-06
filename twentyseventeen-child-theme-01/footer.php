<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>


	<footer class="trade-footer">
	
		<div class="prefooter">
			<div class="footer-content">
				<?php wp_nav_menu(array('theme_location'  => 'footer_menu', 'container' => 'nav', 'container_class' => 'footer-menu', 'items_wrap' => '<ul>%3$s</ul>' )); ?>
			</div>
		</div>

		<div class="footer-content">
			<div class="cr">Copyright 2016. All rights reserved.</div>
			<div class="timezone">Time Zone: Asia/Kolkata</div>
		</div>
	</footer>

<?php wp_footer(); ?>
<?php if (is_user_logged_in()==false): ?>
	<script type="text/javascript">
	jQuery( document ).ready(function() {
		jQuery("#menu-item-101").hide();
	});
	</script>
<?php else:?>
	<div id = "username_hidden" style = "display: none">
		<?php 
			$current_user = wp_get_current_user();
			$username = $current_user->user_login;
			$logout_url = wp_logout_url();
		echo $username;?>

	</div>

	<div id = "logout_url" style = "display: none">
		<?php echo $logout_url;?>
	</div>
	<script type="text/javascript">
		var username = jQuery("#username_hidden").text();
		var logout_url = jQuery("#logout_url").text();
		jQuery("#menu-item-104 a").text('Welcome,' + username + ' | Logout');
		jQuery("#menu-item-104 a").attr('href', logout_url);
	</script>	
<?php endif;?>
<!-- only specific user roles can see premium/admin items in the menu -->
<?php
$check = current_user_can('administrator') || current_user_can('premium') || current_user_can('warehouse_admin');
if ( $check == false ) :?>
	<script type="text/javascript">
	jQuery( document ).ready(function() {
		jQuery("#menu-item-105").hide();
		jQuery("#menu-item-103").hide();
	});
	</script>
<?php endif;?>
</body>
</html>

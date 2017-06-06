<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<?php
//this is redirect to Amazon product page with our ref id
if (isset($_GET['amazon_redirect'])) {
	if (isset($_GET['asin'])) {
		$asin = $_GET['asin'];
	}

	$tag =  'atmwidget-20';
	$ref = 'trdrt_s_tradein-aps_0';
	$url_params = array(
		'ie' => 'UTF8',
		'tag' => $tag,
		'ref' => $ref,
		);

	$added_params = http_build_query($url_params);

	if (!empty($asin) ) {
		$url = "http://www.amazon.com/gp/product/PLACEHOLDER/$asin?$added_params";
		wp_redirect($url, 301);
		exit();
	}
	 
/*	
asin=1465420460
Is what you have to get and use to build the link

tag=atmwidget-20
*/

}
?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<header class="trade-header">
	    <div class="header-content">
			<div class="logo">
				<a href="/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" alt=""></a>
			</div>
		
			<?php wp_nav_menu(array('theme_location'  => 'header_menu', 'container' => 'nav', 'container_class' => 'header-menu', 'items_wrap' => '<ul>%3$s</ul>' )); ?>
		</div>

	</header>

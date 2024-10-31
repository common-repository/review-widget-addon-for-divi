<?php
/*
Plugin Name: Review widget addon for Divi
Plugin URI: https://wordpress.org/plugins/review-widget-addon-for-divi/
Description: Display your Reviews for free with our responsive widgets in 2 minutes.
Tags: divi, recommendations, reviews, divi addon, widget
Version: 1.1
Author: Trustindex.io <support@trustindex.io>
Author URI: https://www.trustindex.io/
Contributors: trustindex
License:     GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: review-widget-addon-for-divi
Domain Path: /languages
Donate link: https://www.trustindex.io/prices/
*/

/*
Review widget addon for Divi is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Review widget addon for Divi is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Review widget addon for Divi. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

/*
Copyright 2019 Trustindex Kft (email: support@trustindex.io)
*/

if( ! defined( 'ABSPATH' ) ) exit(); // Exit if accessed directly

if ( ! function_exists('is_plugin_active')) { include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }

class TRNDX_Divi {
	public static $plugins =  array(
		0 => 'free-facebook-reviews-and-recommendations-widgets',
		1 => 'wp-reviews-plugin-for-google',
		2 => 'review-widgets-for-tripadvisor',
		3 => 'reviews-widgets-for-yelp',
		4 => 'review-widgets-for-booking-com',
		5 => 'reviews-widgets',
		6 => 'review-widgets-for-amazon',
		7 => 'review-widgets-for-arukereso',
		8 => 'review-widgets-for-airbnb',
		9 => 'review-widgets-for-hotels-com',
		10 => 'review-widgets-for-opentable',
		11 => 'review-widgets-for-foursquare',
		12 => 'review-widgets-for-capterra',
		13 => 'review-widgets-for-szallas-hu',
		14 => 'widgets-for-thumbtack-reviews',
		15 => 'widgets-for-expedia-reviews',
		16 => 'widgets-for-zillow-reviews',
		17 => 'widgets-for-alibaba-reviews',
		18 => 'widgets-for-aliexpress-reviews',
		19 => 'widgets-for-sourceforge-reviews',
		20 => 'widgets-for-ebay-reviews',
	);
}

// check if trustindex plugin is activated
function trdnx_divi_check_ti_active()
{
	$active_plugins = get_option('active_plugins');

	$is_ti_active = false;

	foreach ($active_plugins as $active_plugin)
	{
		$name = explode('/', $active_plugin)[0];
		if (in_array($name, TRNDX_Divi::$plugins))
		{
			$is_ti_active = true;
			break;
		}
	}

	return $is_ti_active;
}

if ( ! function_exists( 'trndx_initialize_extension' ) )
{
	/**
	 * Creates the extension's main class instance.
	 *
	 * @since 1.0.0
	 */
	function trndx_divi_initialize_extension() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/TrustindexDivi.php';
	}
	add_action( 'divi_extensions_init', 'trndx_divi_initialize_extension' );
}

// Check Plugins is Installed or not
function trndx_divi_is_plugin_active( $pl_file_path = null )
{
	$installed_plugins_list = get_plugins();
	return isset( $installed_plugins_list[$pl_file_path] );
}

// Load Plugins
function trndx_divi_load_plugin() {
	if( !trdnx_divi_check_ti_active() ){
		add_action( 'admin_notices', 'trdnx_divi_check_core_status' );
		return;
	}
}
add_action( 'plugins_loaded', 'trndx_divi_load_plugin' );

function trndx_divi_enqueue_scripts()
{
	if ( !function_exists( 'wp_enqueue_script') )
	{
		include_once( ABSPATH . 'wp-includes/functions.wp-scripts.php' );
	}

	wp_enqueue_script( 'trustindex-js', 'https://cdn.trustindex.io/loader.js', [], false, true);
}
add_action( 'wp_enqueue_scripts', 'trndx_divi_enqueue_scripts' );

// Check Trustindex install or not.
function trdnx_divi_check_core_status(){
	$active = false;
	$installed_plugin = null;
	foreach (TRNDX_Divi::$plugins as $plugin)
	{
		if (trndx_divi_is_plugin_active( "{$plugin}/{$plugin}.php" ))
		{
			$installed_plugin = "{$plugin}/{$plugin}.php";
			$active = true;
			break;
		}
	}
	if( $active ) {
		if( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $installed_plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $installed_plugin );
		?>
		<div class="notice notice-warning is-dismissible"><p>
			<p>For the full functionality of <strong>"Review widget addon for Divi"</strong>, activate one of the core <strong>Trustindex Review Plugins.</strong></p>
			<p> <?php echo sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, 'Activate Now' ); ?> </p>
			</p></div>
		<?php
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wp-reviews-plugin-for-google'), 'install-plugin_wp-reviews-plugin-for-google');
		?>
		<div class="notice notice-warning is-dismissible"><p>
			<p> For the full functionality of <strong>"Review widget addon for Divi"</strong>, download one of the core <strong>Trustindex Review Plugins.</strong></p>
			<p> <?php echo sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, 'Install Now' ) ?> </p>
			</p></div>
		<?php
	}
}
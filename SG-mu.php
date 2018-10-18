<?php // (C) Copyright Bobbing Wide 2018

/*
Plugin Name: SG-mu
Plugin URI: https://github.com/bobbingwide/SG-mu
Description: Must Use plugin for running sgmotorsport.biz in batch
Version: 0.0.0
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
License: GPL2

    Copyright 2018 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

/**
 * Try different methods of preventing W3 Total Cache from setting its output buffer handler
 * 
 * MU plugins are loaded after advanced-cache.php
 * In batch mode...
 * 
 * If W3 Total Cache is activated then W3TC_POWERED_BY should have been defined.
 * We can assign it to $_SERVER['HTTP_USER_AGENT'] which will force W3TC\Generic_Plugin::can_ob() to return false.
 * Ta da! 
 */
if ( PHP_SAPI == "cli" ) {
	//global $w3_late_init;
	//$w3_late_init = true;
	//define( 'SHORTINIT', true );
	//bw_trace2( "SG-mu", PHP_SAPI );
	if ( defined( 'W3TC_POWERED_BY' ) ) {
		$_SERVER['HTTP_USER_AGENT'] = W3TC_POWERED_BY;
	}
	add_action( "after_setup_theme", "sgmu_after_setup_theme", 10 );
}







/**
 * Implements after_setup_theme
 * 
 * Adjusts hooks attached to 'init' when running from the command line 
 * 
 * Logic probably applies to both oik-batch and WP-cli
 * 
 * What we want to change            | What to remove / add to effect this
 * --------------------------------- | -----------------------------------
 * Prevent W3TC\Generic_Plugin::ob_callback from being attached  | 	 See above
 * Prevent ob_gzhandler from being attached | farFutureExpiration::do_init_time_tasks
 * 
 * 
 * 
 *
 * 
 
 * 
 
 
C:\apache\htdocs\sgmotorsport\wp-content\themes\SG-Motorsport\functions.php(18:0) sgm_after_setup_theme(1) 29 2018-09-24T15:42:51+00:00 2.617332 0.331560 cf=after_setup_theme 4 0 44040192/44040192 128M F=618 Init hooks 
: -999   LittleBizzy\DisableXMLRPC\LB_Disable_XML_RPC::init;1
: 0   create_initial_post_types;1 create_initial_taxonomies;1 Storefront_Powerpack::includes;1 WC_AJAX::define_ajax;1 WC_Auth::add_endpoint;1 WC_API::add_endpoint;1 WooCommerce::init;1 WooCommerce::wpdb_table_fix;1
: 1   wp_widgets_init;1 Yoast_Notification_Center::setup_current_notifications;1 WPSEO_Sitemaps_Router::init;1
: 2   WPSEO_Option_Titles::translate_defaults;1 WPSEO_Option_Social::translate_defaults;1
: 5   smilies_init;1 WC_Post_Types::register_taxonomies;1 WC_Post_Types::register_post_types;1 WC_Install::check_version;1 WC_Install::init_background_updater;1
: 9   WC_Post_Types::register_post_status;1 WoocommerceGpfCache::init_workers;1
: 10   wp_cron;1 wp_schedule_delete_old_privacy_export_files;1 _show_post_preview;1 rest_api_init;1 kses_init;1 wp_schedule_update_checks;1 Homepage_Control::load_plugin_textdomain;1 RegenerateThumbnails;1 schema_wp_cpt_init;1 schema_wp_wprs_remove_admin_bar_menu;1 schema_wp_wprs_remove_genesis_search_form;1 schema_wp_remove_genesis_breadcrumbs_attr_markup;1 Storefront_Footer_Bar::sfb_load_plugin_textdomain;1 Storefront_Footer_Bar::sfb_setup;1 Storefront_Hamburger_Menu::shm_load_plugin_textdomain;1 Storefront_Hamburger_Menu::shm_setup;1 Storefront_Mega_Menus::load_plugin_textdomain;1 SMM_Customizer::ajax_actions;1 Storefront_Parallax_Hero::load_plugin_textdomain;1 Storefront_Parallax_Hero::sph_setup;1 Storefront_Powerpack::load_plugin_textdomain;1 Storefront_Site_Logo::woa_sf_load_plugin_textdomain;1 Storefront_Site_Logo::woa_sf_setup;1 ewc_shipping_calculator::load_plugin_textdomain;1 load__ewc_plugin_textdomain;1 WC_Gateway_PPEC_Plugin::load_plugin_textdomain;1 IPQ_Quantity_Rule_Post_Type::quantity_rule_init;1 Incremental_Product_Quantities::get_wc_version;1 woocommerce_gpf_endpoints;1 WC_Seq_Order_Number::load_translation;1 woocommerce_legacy_paypal_ipn;1 WC_Post_Types::support_jetpack_omnisearch;1 WC_Regenerate_Images::init;1 WC_Template_Loader::init;1 WC_Query::add_endpoints;1 WC_Shortcodes::init;1 WC_Emails::init_transactional_emails;1 WooCommerce::add_image_sizes;1 farFutureExpiration::load_settings;1 farFutureExpiration::do_init_time_tasks;1 WordPress_Module::init;1 WordPress_Module::force_https;1 WC_Gateway_PPEC_Checkout_Handler::init;1 WC_Table_Rate_Shipping::load_plugin_textdomain;1 WPSEO_Sitemaps_Cache::init;1 initialize_wpseo_front;1 Storefront_Customizer::default_theme_mod_values;1
: 20   oik_main_init;1
: 99   check_theme_switched;1 WPSEO_Option_Titles::enrich_defaults;1 WPSEO_Taxonomy_Meta::enrich_defaults;1
: 999   WPSEO_Option_Titles::end_of_init;1 WPSEO_Rewrite::flush;1
 */
function sgmu_after_setup_theme() {
	if ( PHP_SAPI == "cli" ) {
		$hooks = bw_trace_get_attached_hooks( "init" );
		bw_trace2( $hooks, "Init hooks", false );
		
		remove_action( "init", array( $GLOBALS['ffe_plugin'], "do_init_time_tasks" ) );
		
		$hooks = bw_trace_get_attached_hooks( "init" );
		bw_trace2( $hooks, "Init hooks", false );
		
		//gob();
	}
}

 

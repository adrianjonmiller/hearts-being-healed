<?php
/*
Plugin Name: GAM Event Manager

Plugin URI: https://gamthemes.com/

Description: Manage event listings from the WordPress admin panel, and allow users to post events directly to your site.

Author: GAM Themes

Author URI: https://gamthemes.com/

Text Domain: gam-event-manager

Domain Path: /languages

Version: 1.0.0

Since: 1.0.0

Requires WordPress Version at least: 4.1

Copyright: 2015 GAM Themes

License: GNU General Public License v3.0

License URI: http://www.gnu.org/licenses/gpl-3.0.html

*/

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
}

/**
 * GAM_Event_Manager class.
 */

class GAM_Event_Manager {

	/**
	 * Constructor - get the plugin hooked in and ready
	 */

	public function __construct() 
	{
		// Define constants

		define( 'EVENT_MANAGER_VERSION', '1.0.0' );
		define( 'EVENT_MANAGER_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'EVENT_MANAGER_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		//Core		
		include( 'core/gam-event-manager-install.php' );
		include( 'core/gam-event-manager-post-types.php' );
		include( 'core/gam-event-manager-ajax.php' );
		include( 'core/gam-event-manager-api.php' );
		include( 'core/gam-event-manager-geocode.php' );
		include( 'core/gam-event-manager-filters.php' );
		include( 'core/gam-event-manager-cache-helper.php' );		

		//shortcodes
		include( 'shortcodes/gam-event-manager-shortcodes.php' );

		//forms
		include( 'forms/gam-event-manager-forms.php' );	

		if ( is_admin() ) {

			include( 'admin/gam-event-manager-admin.php' );

		}

		// Init classes

		$this->forms      = new GAM_Event_Manager_Forms();

		$this->post_types = new GAM_Event_Manager_Post_Types();

		// Activation - works with symlinks

		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'activate' ) );

		// Switch theme

		add_action( 'after_switch_theme', array( 'GAM_Event_Manager_Ajax', 'add_endpoint' ), 10 );

		add_action( 'after_switch_theme', array( $this->post_types, 'register_post_types' ), 11 );

		add_action( 'after_switch_theme', 'flush_rewrite_rules', 15 );

		// Actions

		add_action( 'after_setup_theme', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		add_action( 'admin_init', array( $this, 'updater' ) );
	}

	/**
	 * Called on plugin activation
	 */

	public function activate() {

		GAM_Event_Manager_Ajax::add_endpoint();

		$this->post_types->register_post_types();

		GAM_Event_Manager_Install::install();
		
		flush_rewrite_rules();
	}

	/**
	 * Handle Updates
	 */

	public function updater() {

		if ( version_compare( EVENT_MANAGER_VERSION, get_option( 'gam_event_manager_version' ), '>' ) ) {

			GAM_Event_Manager_Install::install();

			flush_rewrite_rules();
		}
	}

	/**
	 * Localisation
	 */

	public function load_plugin_textdomain() {

		$domain = 'gam-event-manager';       

        	$locale = apply_filters('plugin_locale', get_locale(), $domain);

		load_textdomain( $domain, WP_LANG_DIR . "/gam-event-manager/".$domain."-" .$locale. ".mo" );

		load_plugin_textdomain($domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Load functions
	 */

	public function include_template_functions() {

		include( 'gam-event-manager-functions.php' );

		include( 'gam-event-manager-template.php' );
	}

	/**
	 * Widgets init
	 */

	public function widgets_init() {

		include_once( 'widgets/gam-event-manager-widgets.php' );
	}

	/**
	 * Register and enqueue scripts and css
	 */

	public function frontend_scripts() 
	{
		$ajax_url         = GAM_Event_Manager_Ajax::get_endpoint();
		$ajax_filter_deps = array( 'jquery', 'jquery-deserialize' );

		//jQuery Chosen - vendor
		if ( apply_filters( 'event_manager_chosen_enabled', true ) ) {

			wp_register_script( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
			wp_register_script( 'gam-event-manager-term-multiselect', EVENT_MANAGER_PLUGIN_URL . '/assets/js/term-multiselect.min.js', array( 'jquery', 'chosen' ), EVENT_MANAGER_VERSION, true );
			wp_register_script( 'gam-event-manager-multiselect', EVENT_MANAGER_PLUGIN_URL . '/assets/js/multiselect.min.js', array( 'jquery', 'chosen' ), EVENT_MANAGER_VERSION, true );
			wp_enqueue_style( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/css/chosen.css' );

			$ajax_filter_deps[] = 'chosen';
		}
	
		//file upload - vendor
		if ( apply_filters( 'event_manager_ajax_file_upload_enabled', true ) ) {

			wp_register_script( 'jquery-iframe-transport', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-fileupload/jquery.iframe-transport.js', array( 'jquery' ), '1.8.3', true );
			wp_register_script( 'jquery-fileupload', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-fileupload/jquery.fileupload.js', array( 'jquery', 'jquery-iframe-transport', 'jquery-ui-widget' ), '5.42.3', true );
			wp_register_script( 'gam-event-manager-ajax-file-upload', EVENT_MANAGER_PLUGIN_URL . '/assets/js/ajax-file-upload.min.js', array( 'jquery', 'jquery-fileupload' ), EVENT_MANAGER_VERSION, true );

			ob_start();
			get_event_manager_template( 'form-fields/uploaded-file-html.php', array( 'name' => '', 'value' => '', 'extension' => 'jpg' ) );
			$js_field_html_img = ob_get_clean();

			ob_start();
			get_event_manager_template( 'form-fields/uploaded-file-html.php', array( 'name' => '', 'value' => '', 'extension' => 'zip' ) );
			$js_field_html = ob_get_clean();

			wp_localize_script( 'gam-event-manager-ajax-file-upload', 'event_manager_ajax_file_upload', array(
				'ajax_url'               => $ajax_url,
				'js_field_html_img'      => esc_js( str_replace( "\n", "", $js_field_html_img ) ),
				'js_field_html'          => esc_js( str_replace( "\n", "", $js_field_html ) ),
				'i18n_invalid_file_type' => __( 'Invalid file type. Accepted types:', 'gam-event-manager' )
			) );
		}

		//jQuery Desrialize - vendor
		wp_register_script( 'jquery-deserialize', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-deserialize/jquery.deserialize.js', array( 'jquery' ), '1.2.1', true );						
	
		//main frontend, bootstrap & bootstrap calendar style 	
		wp_register_style( 'bootstrap-main-css', EVENT_MANAGER_PLUGIN_URL . '/assets/js/bootstrap/css/bootstrap.min.css');	
		wp_register_style( 'bootstrap-datetime-picker-css', EVENT_MANAGER_PLUGIN_URL.'/assets/js/bootstrap-datetime-picker/bootstrap-datetimepicker.min.css');

		if (!wp_style_is( 'bootstrap.min.css', 'enqueued' )) 
		{
		    wp_enqueue_style( 'bootstrap-main-css');
		}

		if (!wp_style_is( 'bootstrap-datetimepicker.min.css', 'enqueued' )) 
		{
		    wp_enqueue_style( 'bootstrap-datetime-picker-css');
		}
		wp_enqueue_style( 'gam-event-manager-frontend', EVENT_MANAGER_PLUGIN_URL . '/assets/css/frontend.css');	

		//bootstrap, moment and bootstrap calendar js	
		wp_register_script( 'bootstrap-main-js', EVENT_MANAGER_PLUGIN_URL . '/assets/js/bootstrap/js/bootstrap.min.js', array('jquery'), EVENT_MANAGER_VERSION, true);
		wp_register_script( 'moment', EVENT_MANAGER_PLUGIN_URL . '/assets/js/bootstrap-datetime-picker/moment-with-locales.min.js',array('bootstrap-main-js'), EVENT_MANAGER_VERSION, true);
		wp_register_script( 'bootstrap-datetime-picker-js', EVENT_MANAGER_PLUGIN_URL . '/assets/js/bootstrap-datetime-picker/bootstrap-datetimepicker.min.js',array('moment'), EVENT_MANAGER_VERSION, true);

		if (!wp_script_is( 'bootstrap.min.js', 'enqueued' )) 
		{
		    wp_enqueue_script( 'bootstrap-main-js');
		}

		if (!wp_script_is( 'bootstrap-datetimepicker.min.js', 'enqueued' )) 
		{
		   wp_enqueue_script( 'bootstrap-datetime-picker-js');	
		}	

		//wp_enqueue_script( 'moment');	

		//common js
		wp_register_script('gam-event-manager-common', EVENT_MANAGER_PLUGIN_URL . '/assets/js/common.min.js', array('jquery'), EVENT_MANAGER_VERSION, true);	
		wp_enqueue_script('gam-event-manager-common'); 		

		//event submission forms and validation js
		wp_register_script( 'gam-event-manager-event-submission', EVENT_MANAGER_PLUGIN_URL . '/assets/js/event-submission.min.js', array('jquery','bootstrap-datetime-picker-js') , EVENT_MANAGER_VERSION, true );
        wp_register_script( 'gam-event-manager-content-event-listing', EVENT_MANAGER_PLUGIN_URL . '/assets/js/content-event-listing.min.js', array('jquery','gam-event-manager-common'), EVENT_MANAGER_VERSION, true );					

		//ajax filters js
		wp_register_script( 'gam-event-manager-ajax-filters', EVENT_MANAGER_PLUGIN_URL . '/assets/js/event-ajax-filters.min.js', $ajax_filter_deps, EVENT_MANAGER_VERSION, true );
		wp_localize_script( 'gam-event-manager-ajax-filters', 'event_manager_ajax_filters', array(
			'ajax_url'                => $ajax_url,
			'is_rtl'                  => is_rtl() ? 1 : 0,
			'lang'                    => defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '', // WPML workaround until this is standardized
			'i18n_load_prev_listings' => __( 'Load previous listings', 'gam-event-manager' )

		) );

		//dashboard
		wp_register_script( 'bootstrap-confirmation-js', EVENT_MANAGER_PLUGIN_URL . '/assets/js/bootstrap/bootstrap-confirmation.min.js', array('jquery','bootstrap-main-js'), EVENT_MANAGER_VERSION, true );			
		wp_register_script( 'gam-event-manager-event-dashboard', EVENT_MANAGER_PLUGIN_URL . '/assets/js/event-dashboard.min.js', array( 'jquery','bootstrap-confirmation-js' ), EVENT_MANAGER_VERSION, true );	
		wp_localize_script( 'gam-event-manager-event-dashboard', 'event_manager_event_dashboard', array(

			'i18n_btnOkLabel' => __( 'Delete', 'gam-event-manager' ),

			'i18n_btnCancelLabel' => __( 'Cancel', 'gam-event-manager' ),

			'i18n_confirm_delete' => __( 'Are you sure you want to delete this event?', 'gam-event-manager' )

		) );
		
		//registration
	    wp_register_script( 'gam-event-manager-event-registration', EVENT_MANAGER_PLUGIN_URL . '/assets/js/event-registration.min.js', array( 'jquery' ), EVENT_MANAGER_VERSION, true );

	}
}
$GLOBALS['event_manager'] = new GAM_Event_Manager();
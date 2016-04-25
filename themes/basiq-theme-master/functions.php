<?php
// Require device detection library
require_once 'bower_components/Mobile_Detect/Mobile_Detect.php';
require_once 'vendor/timber-library/timber.php';


if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		} );
	return;
}

show_admin_bar( false );

// Base template route
$template_base = 'templates';

// Base tablet routes and fallbacks
$template_array = array($template_base.'/mobile', $template_base.'/layouts', $template_base.'/partials', 'views');

$detect = new Mobile_Detect;

function device_class() {
	$detect = new Mobile_Detect;

	if( $detect->isTablet() ){
		// If tablet
		return "tablet";
	} else if (!$detect->isMobile()) {
		// If not tablet and not mobile
		return "desktop";
	} else {
		return "mobile";
	}
}

if( $detect->isTablet() ){
	// If tablet
	array_unshift($template_array, $template_base.'/tablet');
} else if (!$detect->isMobile()) {
	// If not tablet and not mobile
	array_unshift($template_array, $template_base.'/desktop', $template_base.'/tablet');
}

Timber::$dirname = $template_array;

class StarterSite extends TimberSite {

	function __construct() {
		load_theme_textdomain( 'nilslindstrom-theme', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		));
		add_theme_support( 'post-formats', array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
		));
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary', 'theme' ),
			'footer' => esc_html__( 'Footer', 'theme' ),
		));
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'rewrite_rules' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'loadScripts') );
		add_action( 'widgets_init', array( $this, 'widgets' ) );
		parent::__construct();
	}

	function register_post_types() {
		//this is where you can register custom post types
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

	function add_to_context( $context ) {
		$context['device'] = device_class();
		$context['theme_url'] = get_stylesheet_directory_uri();
		$context['notes'] = 'These values are available everytime you call Timber::get_context();';
		$context['menu'] = new TimberMenu('primary');
		$context['dynamic_sidebar'] = Timber::get_widgets('sidebar-1');
		$context['site'] = $this;
		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own fuctions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter( 'myfoo', new Twig_Filter_Function( 'myfoo' ) );
		return $twig;
	}

	function loadScripts() {
    wp_enqueue_script( 'behaviors', get_template_directory_uri() . '/js/behaviors.js', array(), '1.0.0', true );
    wp_enqueue_script( 'inview', get_template_directory_uri() . '/js/inview.js', array(), '1.0.0', true );
    wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/scripts.js', array(), '1.0.0', true );

		wp_enqueue_style( 'ionicons', get_template_directory_uri(). '/bower_components/Ionicons/css/ionicons.css', array(), '', false );
  }

	function widgets() {
		register_sidebar( array(
			'name'          => esc_html__( 'Blog Sidebar', 'theme' ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="display--sidebar widget-title collapse">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Event Sidebar', 'theme' ),
			'id'            => 'sidebar-2',
			'description'   => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title" style="display: none;">',
			'after_title'   => '</h2>',
		) );
	}

	function rewrite_rules() {
		add_rewrite_rule('img/', get_template_directory_uri().'/img');
	}
}

new StarterSite();
//
// function device() {
// 	$device = 'mobile';
// 	$detect = new Mobile_Detect;
//
// 	if( $detect->isTablet() ){
// 		// If tablet
// 		$device = 'tablet';
// 	} else if (!$detect->isMobile()) {
// 		// If not tablet and not mobile
// 		$device = 'desktop';
// 	}
// 	return $device;
// }

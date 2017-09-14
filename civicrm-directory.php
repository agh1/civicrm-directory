<?php /*
--------------------------------------------------------------------------------
Plugin Name: CiviCRM Directory
Plugin URI: https://github.com/christianwach/civicrm-directory
Description: Creates a publicly-viewable directory from data submitted to CiviCRM.
Author: Christian Wach
Version: 0.1
Author URI: http://haystack.co.uk
Text Domain: civicrm-directory
Domain Path: /languages
Depends: CiviCRM
--------------------------------------------------------------------------------
*/



// set our version here
define( 'CIVICRM_DIRECTORY_VERSION', '0.1' );

// trigger logging of 'civicrm_pre' and 'civicrm_post'
if ( ! defined( 'CIVICRM_DIRECTORY_DEBUG' ) ) {
	define( 'CIVICRM_DIRECTORY_DEBUG', false );
}

// store reference to this file
if ( ! defined( 'CIVICRM_DIRECTORY_FILE' ) ) {
	define( 'CIVICRM_DIRECTORY_FILE', __FILE__ );
}

// store URL to this plugin's directory
if ( ! defined( 'CIVICRM_DIRECTORY_URL' ) ) {
	define( 'CIVICRM_DIRECTORY_URL', plugin_dir_url( CIVICRM_DIRECTORY_FILE ) );
}

// store PATH to this plugin's directory
if ( ! defined( 'CIVICRM_DIRECTORY_PATH' ) ) {
	define( 'CIVICRM_DIRECTORY_PATH', plugin_dir_path( CIVICRM_DIRECTORY_FILE ) );
}



/**
 * CiviCRM Directory Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 0.1
 */
class CiviCRM_Directory {

	/**
	 * Admin object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $plugin The Admin object.
	 */
	public $admin;

	/**
	 * Custom Post Type object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $plugin The Custom Post Type object.
	 */
	public $cpt;

	/**
	 * Metaboxes object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $metaboxes The Metaboxes object.
	 */
	public $metaboxes;

	/**
	 * Map object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $map The Map object.
	 */
	public $map;



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// initialise
		$this->initialise();

		// use translation files
		add_action( 'plugins_loaded', array( $this, 'enable_translation' ) );

		// set up objects when all plugins are loaded
		add_action( 'plugins_loaded', array( $this, 'setup_objects' ), 20 );

	}



	/**
	 * Do stuff on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// set up objects
		$this->setup_objects();

		// pass to classes that need activation
		$this->admin->activate();
		$this->cpt->activate();

	}



	/**
	 * Do stuff on plugin deactivation.
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// pass to classes that need deactivation
		$this->cpt->deactivate();

	}



	/**
	 * Do stuff on plugin init.
	 *
	 * @since 0.1
	 */
	public function initialise() {

		// include files
		$this->include_files();

		// add actions and filters
		$this->register_hooks();

	}



	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// load our Admin class
		require( CIVICRM_DIRECTORY_PATH . 'includes/class-civicrm-directory-admin.php' );

		// load our CPT class
		require( CIVICRM_DIRECTORY_PATH . 'includes/class-civicrm-directory-cpt.php' );

		// load our Metaboxes class
		require( CIVICRM_DIRECTORY_PATH . 'includes/class-civicrm-directory-metaboxes.php' );

		// load our Map class
		require( CIVICRM_DIRECTORY_PATH . 'includes/class-civicrm-directory-map.php' );

	}



	/**
	 * Set up this plugin's objects.
	 *
	 * @since 0.1
	 */
	public function setup_objects() {

		// init flag
		static $done;

		// only do this once
		if ( isset( $done ) AND $done === true ) return;

		// init objects
		$this->admin = new CiviCRM_Directory_Admin( $this );
		$this->admin->register_hooks();
		$this->cpt = new CiviCRM_Directory_CPT( $this );
		$this->cpt->register_hooks();
		$this->metaboxes = new CiviCRM_Directory_Metaboxes( $this );
		$this->metaboxes->register_hooks();

		// map class needs no hooks
		$this->map = new CiviCRM_Directory_Map( $this );

		// we're done
		$done = true;

	}



	/**
	 * Load translation files.
	 *
	 * @since 0.1
	 */
	public function enable_translation() {

		// load translations
		load_plugin_textdomain(
			'civicrm-directory', // unique name
			false, // deprecated argument
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' // relative path
		);

	}



	//##########################################################################



	/**
	 * Register hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// bail if CiviCRM is not present
		if ( ! function_exists( 'civi_wp' ) ) return;

	}



} // class ends



// init plugin
global $civicrm_directory;
$civicrm_directory = new CiviCRM_Directory;

// activation
register_activation_hook( __FILE__, array( $civicrm_directory, 'activate' ) );

// deactivation
register_deactivation_hook( __FILE__, array( $civicrm_directory, 'deactivate' ) );

// uninstall will use the 'uninstall.php' method when fully built
// see: http://codex.wordpress.org/Function_Reference/register_uninstall_hook



/**
 * Utility to get a reference to this plugin.
 *
 * @since 0.1
 *
 * @return object $civicrm_directory The plugin reference.
 */
function civicrm_directory() {

	// return instance
	global $civicrm_directory;
	return $civicrm_directory;

}



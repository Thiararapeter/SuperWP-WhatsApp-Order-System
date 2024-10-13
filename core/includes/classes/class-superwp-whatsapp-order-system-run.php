<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This class is used to bring your plugin to life. 
 * All the other registered classed bring features which are
 * controlled and managed by this class.
 * 
 * Within the add_hooks() function, you can register all of 
 * your WordPress related actions and filters as followed:
 * 
 * add_action( 'my_action_hook_to_call', array( $this, 'the_action_hook_callback', 10, 1 ) );
 * or
 * add_filter( 'my_filter_hook_to_call', array( $this, 'the_filter_hook_callback', 10, 1 ) );
 * or
 * add_shortcode( 'my_shortcode_tag', array( $this, 'the_shortcode_callback', 10 ) );
 * 
 * Once added, you can create the callback function, within this class, as followed: 
 * 
 * public function the_action_hook_callback( $some_variable ){}
 * or
 * public function the_filter_hook_callback( $some_variable ){}
 * or
 * public function the_shortcode_callback( $attributes = array(), $content = '' ){}
 * 
 * 
 * HELPER COMMENT END
 */

/**
 * Class Superwp_Whatsapp_Order_System_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		SUPERWPWHA
 * @subpackage	Classes/Superwp_Whatsapp_Order_System_Run
 * @author		Thiarara SuperWP
 * @since		1.0.1
 */
class Superwp_Whatsapp_Order_System_Run{

	/**
	 * Our Superwp_Whatsapp_Order_System_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.1
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.1
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_styles' ), 20 );
	
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.1
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		wp_enqueue_script( 'superwpwha-backend-scripts', SUPERWPWHA_PLUGIN_URL . 'core/includes/assets/js/backend-scripts.js', array(), SUPERWPWHA_VERSION, false );
		wp_localize_script( 'superwpwha-backend-scripts', 'superwpwha', array(
			'plugin_name'   	=> __( SUPERWPWHA_NAME, 'superwp-whatsapp-order-system' ),
		));
	}


	/**
	 * Enqueue the frontend related scripts and styles for this plugin.
	 *
	 * @access	public
	 * @since	1.0.1
	 *
	 * @return	void
	 */
	public function enqueue_frontend_scripts_and_styles() {
		wp_enqueue_style( 'superwpwha-frontend-styles', SUPERWPWHA_PLUGIN_URL . 'core/includes/assets/css/frontend-styles.css', array(), SUPERWPWHA_VERSION, 'all' );
		wp_enqueue_script( 'superwpwha-frontend-scripts', SUPERWPWHA_PLUGIN_URL . 'core/includes/assets/js/frontend-scripts.js', array(), SUPERWPWHA_VERSION, false );
		wp_localize_script( 'superwpwha-frontend-scripts', 'superwpwha', array(
			'demo_var'   		=> __( 'This is some demo text coming from the backend through a variable within javascript.', 'superwp-whatsapp-order-system' ),
		));
	}

}

<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Superwp_Whatsapp_Order_System' ) ) :

	/**
	 * Main Superwp_Whatsapp_Order_System Class.
	 *
	 * @package		SUPERWPWHA
	 * @subpackage	Classes/Superwp_Whatsapp_Order_System
	 * @since		1.0.1
	 * @author		Thiarara SuperWP
	 */
	final class Superwp_Whatsapp_Order_System {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.1
		 * @var		object|Superwp_Whatsapp_Order_System
		 */
		private static $instance;

		/**
		 * SUPERWPWHA helpers object.
		 *
		 * @access	public
		 * @since	1.0.1
		 * @var		object|Superwp_Whatsapp_Order_System_Helpers
		 */
		public $helpers;

		/**
		 * SUPERWPWHA settings object.
		 *
		 * @access	public
		 * @since	1.0.1
		 * @var		object|Superwp_Whatsapp_Order_System_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * @access	public
		 * @since	1.0.1
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'superwp-whatsapp-order-system' ), '1.0.1' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.1
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'superwp-whatsapp-order-system' ), '1.0.1' );
		}

		/**
		 * Main Superwp_Whatsapp_Order_System Instance.
		 *
		 * @access		public
		 * @since		1.0.1
		 * @static
		 * @return		object|Superwp_Whatsapp_Order_System	The one true Superwp_Whatsapp_Order_System
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Superwp_Whatsapp_Order_System ) ) {
				self::$instance					= new Superwp_Whatsapp_Order_System;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Superwp_Whatsapp_Order_System_Helpers();
				self::$instance->settings		= new Superwp_Whatsapp_Order_System_Settings();

				// Initialize the plugin
				self::$instance->init();

				//Fire the plugin logic
				new Superwp_Whatsapp_Order_System_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'SUPERWPWHA/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.1
		 * @return  void
		 */
		private function includes() {
			require_once SUPERWPWHA_PLUGIN_DIR . 'core/includes/classes/class-superwp-whatsapp-order-system-helpers.php';
			require_once SUPERWPWHA_PLUGIN_DIR . 'core/includes/classes/class-superwp-whatsapp-order-system-settings.php';
			require_once SUPERWPWHA_PLUGIN_DIR . 'core/includes/classes/class-superwp-whatsapp-order-system-run.php';
		}

		/**
		 * Initialize the plugin
		 *
		 * @access  public
		 * @since   1.0.1
		 * @return  void
		 */
		public function init() {
			add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
		}

		/**
		 * Enqueue frontend styles
		 *
		 * @access  public
		 * @since   1.0.1
		 * @return  void
		 */
		public function enqueue_frontend_styles() {
			wp_enqueue_style(
				'superwp-whatsapp-order-system-frontend-styles',
				SUPERWPWHA_PLUGIN_URL . 'core/includes/assets/css/frontend-styles.css',
				array(),
				SUPERWPWHA_VERSION
			);
		}
		
		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.1
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.1
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'superwp-whatsapp-order-system', FALSE, dirname( plugin_basename( SUPERWPWHA_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.
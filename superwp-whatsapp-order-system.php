<?php
/**
 * SuperWP WhatsApp Order System
 *
 * @package       SUPERWPWHA
 * @author        Thiarara SuperWP
 * @license       gplv2-or-later
 * @version       1.0.1
 *
 * @wordpress-plugin
 * Plugin Name:   SuperWP WhatsApp Order System
 * Plugin URI:    https://github.com/Thiararapeter/SuperWP-WhatsApp-Order-System
 * Description:   A WhatsApp order system for WooCommerce with admin settings to customize the ordering process.
 * Version:       1.0.1
 * Author:        Thiarara SuperWP
 * Author URI:    https://profiles.wordpress.org/thiarara/
 * Text Domain:   superwp-whatsapp-order-system
 * Domain Path:   /languages
 * License:       GPLv2 or later
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with SuperWP WhatsApp Order System. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The comment above contains all information about the plugin 
 * that are used by WordPress to differenciate the plugin and register it properly.
 * It also contains further PHPDocs parameter for a better documentation
 * 
 * The function SUPERWPWHA() is the main function that you will be able to 
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 * 
 * HELPER COMMENT END
 */

// Plugin name
define( 'SUPERWPWHA_NAME',			'SuperWP WhatsApp Order System' );

// Plugin version
define( 'SUPERWPWHA_VERSION',		'1.0.1' );

// Plugin Root File
define( 'SUPERWPWHA_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'SUPERWPWHA_PLUGIN_BASE',	plugin_basename( SUPERWPWHA_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'SUPERWPWHA_PLUGIN_DIR',	plugin_dir_path( SUPERWPWHA_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'SUPERWPWHA_PLUGIN_URL',	plugin_dir_url( SUPERWPWHA_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once SUPERWPWHA_PLUGIN_DIR . 'core/class-superwp-whatsapp-order-system.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Thiarara SuperWP
 * @since   1.0.1
 * @return  object|Superwp_Whatsapp_Order_System
 */
function SUPERWPWHA() {
	return Superwp_Whatsapp_Order_System::instance();
}

SUPERWPWHA();

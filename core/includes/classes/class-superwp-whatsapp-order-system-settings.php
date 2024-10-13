<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This class contains all of the plugin related settings.
 * Everything that is relevant data and used multiple times throughout 
 * the plugin.
 * 
 * To define the actual values, we recommend adding them as shown above
 * within the __construct() function as a class-wide variable. 
 * This variable is then used by the callable functions down below. 
 * These callable functions can be called everywhere within the plugin 
 * as followed using the get_plugin_name() as an example: 
 * 
 * SUPERWPWHA->settings->get_plugin_name();
 * 
 * HELPER COMMENT END
 */


/**
 * Class Superwp_Whatsapp_Order_System_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		SUPERWPWHA
 * @subpackage	Classes/Superwp_Whatsapp_Order_System_Settings
 * @author		Thiarara SuperWP
 * @since		1.0.1
 */
class Superwp_Whatsapp_Order_System_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.1
	 */
	private $plugin_name;

	/**
	 * Our Superwp_Whatsapp_Order_System_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.1
	 */
	function __construct(){

		$this->plugin_name = SUPERWPWHA_NAME;
	}


	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.1
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'SUPERWPWHA/settings/get_plugin_name', $this->plugin_name );
	}
}
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Initialize the session for the WhatsApp cart
if (!function_exists('superwp_session_start_v110')) {
    function superwp_session_start_v110() {
        if (!session_id()) {
            session_start();
        }
    }
}
add_action('init', 'superwp_session_start_v110');

// X default "Add to Cart" button and add WhatsApp order button
if (!function_exists('superwp_modify_add_to_cart_button_v220')) {
    function superwp_modify_add_to_cart_button_v220() {
        // X the default "Add to Cart" button from shop/archive pages
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        
        // X the default "Add to Cart" button from single product pages
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        
        // Add our custom WhatsApp order button to shop/archive pages
        add_action('woocommerce_after_shop_loop_item', 'superwp_add_whatsapp_order_button_v220', 10);
        
        // Add our custom WhatsApp order button to single product pages
        add_action('woocommerce_single_product_summary', 'superwp_add_whatsapp_order_button_v220', 30);
    }
}
add_action('init', 'superwp_modify_add_to_cart_button_v220');

// Add WhatsApp order button
if (!function_exists('superwp_add_whatsapp_order_button_v220')) {
    function superwp_add_whatsapp_order_button_v220() {
        global $product;
        $whatsapp_button_text = get_option('superwp_whatsapp_button_text', 'Order via WhatsApp');
        
        echo '<a href="#" class="button superwp-whatsapp-order-button" data-product-id="' . esc_attr($product->get_id()) . '">' . esc_html($whatsapp_button_text) . '</a>';
        echo '<div class="superwp-added-to-cart" style="display:none;">Added to WhatsApp Cart</div>';
    }
}

// Enqueue custom scripts for cart functionality
if (!function_exists('superwp_enqueue_cart_scripts_v220')) {
    function superwp_enqueue_cart_scripts_v220() {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.superwp-whatsapp-order-button').on('click', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                var $button = $(this);
                var $addedMessage = $button.next('.superwp-added-to-cart');
                
                $.post(ajaxurl, {
                    action: 'superwp_add_to_whatsapp_cart',
                    product_id: productId
                }, function(response) {
                    if (response.success) {
                        $button.hide();
                        $addedMessage.fadeIn().delay(2000).fadeOut(function() {
                            $button.fadeIn();
                        });
                        updateCartCount(response.data.cart_count);
                        loadCartItems();
                    }
                });
            });

            // ... rest of the existing JavaScript code ...
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'superwp_enqueue_cart_scripts_v220');

// Add custom styles
if (!function_exists('superwp_add_custom_styles_v220')) {
    function superwp_add_custom_styles_v220() {
        $icon_url = get_option('superwp_whatsapp_cart_icon', plugins_url('/images/whatsapp-icon.png', __FILE__));
        ?>
        <style type="text/css">
            /* ... existing styles ... */

            .superwp-whatsapp-order-button {
                background-color: #25D366 !important;
                color: white !important;
                border: none !important;
                padding: 10px 20px !important;
                border-radius: 4px !important;
                cursor: pointer !important;
                font-size: 16px !important;
                transition: background-color 0.3s ease !important;
                display: inline-block !important;
                text-decoration: none !important;
                margin-top: 10px !important;
            }

            .superwp-whatsapp-order-button:hover {
                background-color: #128C7E !important;
            }

            .superwp-added-to-cart {
                background-color: #128C7E;
                color: white;
                padding: 10px;
                border-radius: 4px;
                margin-top: 10px;
                text-align: center;
                font-weight: bold;
            }

            /* Hide default Add to Cart button on single product page */
            .single-product .cart button.single_add_to_cart_button {
                display: none !important;
            }

            /* ... rest of the existing styles ... */
        </style>
        <?php
    }
}
add_action('wp_head', 'superwp_add_custom_styles_v220');

// Enqueue custom scripts and styles
if (!function_exists('superwp_enqueue_scripts_v110')) {
    function superwp_enqueue_scripts_v110() {
        wp_enqueue_script('superwp-whatsapp-js', plugins_url('/js/superwp-whatsapp.js', __FILE__), array('jquery'), '1.1.0', true);
        wp_enqueue_style('superwp-whatsapp-css', plugins_url('/css/superwp-whatsapp.css', __FILE__), array(), '1.1.0');
    }
}
add_action('wp_enqueue_scripts', 'superwp_enqueue_scripts_v110');

// Add WhatsApp Cart Icon to header
if (!function_exists('superwp_add_whatsapp_cart_icon_v110')) {
    function superwp_add_whatsapp_cart_icon_v110() {
        $cart_count = isset($_SESSION['whatsapp_cart']) ? count($_SESSION['whatsapp_cart']) : 0;
        $icon_url = get_option('superwp_whatsapp_cart_icon', plugins_url('/images/whatsapp-icon.png', __FILE__));
        echo '<div id="superwp-whatsapp-cart-icon">';
        echo '<img src="' . esc_url($icon_url) . '" alt="WhatsApp Cart">';
        echo '<span class="cart-count">' . esc_html($cart_count) . '</span>';
        echo '</div>';
    }
}
add_action('wp_footer', 'superwp_add_whatsapp_cart_icon_v110');

// WhatsApp Checkout Form (WhatsApp Cart)
if (!function_exists('superwp_whatsapp_checkout_form_v120')) {
    function superwp_whatsapp_checkout_form_v120() {
        ?>
        <div id="superwp-whatsapp-checkout" style="display:none;">
            <h3>WhatsApp Cart</h3>
            <div id="whatsapp-cart-items"></div>
            <form id="superwp-whatsapp-checkout-form">
                <div class="form-row">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your name">
                </div>

                <div class="form-row">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
                </div>

                <div class="form-row">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" required placeholder="Enter your address"></textarea>
                </div>

                <button type="submit" class="button">Submit Order</button>
            </form>
        </div>
        <?php
    }
}
add_action('wp_footer', 'superwp_whatsapp_checkout_form_v120');

// Handle WhatsApp Cart
if (!function_exists('superwp_handle_whatsapp_order_v120')) {
    function superwp_handle_whatsapp_order_v120() {
        if (isset($_POST['action']) && $_POST['action'] === 'superwp_whatsapp_order') {
            $name = sanitize_text_field($_POST['name']);
            $phone = sanitize_text_field($_POST['phone']);
            $address = sanitize_textarea_field($_POST['address']);
            $order_details = superwp_get_cart_details_v120();

            $message_template = get_option('superwp_whatsapp_message_template', "Hello, I would like to place an order:\n\n[order_details]\n\nName: [customer_name]\nPhone: [customer_phone]\nAddress: [customer_address]");

            $message = str_replace(
                ['[order_details]', '[customer_name]', '[customer_phone]', '[customer_address]'],
                [$order_details, $name, $phone, $address],
                $message_template
            );

            $whatsapp_url = "https://wa.me/?text=" . urlencode($message);
            
            wp_send_json_success(array('redirect_url' => $whatsapp_url));
        }
        wp_die();
    }
}
add_action('wp_ajax_superwp_whatsapp_order', 'superwp_handle_whatsapp_order_v120');
add_action('wp_ajax_nopriv_superwp_whatsapp_order', 'superwp_handle_whatsapp_order_v120');

// Generate Cart Details
if (!function_exists('superwp_get_cart_details_v120')) {
    function superwp_get_cart_details_v120() {
        $cart_items = isset($_SESSION['whatsapp_cart']) ? $_SESSION['whatsapp_cart'] : array();
        $order_details = '';
        $total = 0;

        foreach ($cart_items as $product_id => $quantity) {
            $product = wc_get_product($product_id);
            $price = $product->get_price() * $quantity;
            $total += $price;

            $order_details .= $product->get_name() . " x $quantity (" . wc_price($price) . ")\n";
        }

        $order_details .= "\nTotal: " . wc_price($total);
        
        // Strip HTML tags and decode entities
        $order_details = html_entity_decode(strip_tags($order_details), ENT_QUOTES, 'UTF-8');

        return $order_details;
    }
}

// Add Product to WhatsApp Cart via AJAX
if (!function_exists('superwp_add_to_whatsapp_cart_v110')) {
    function superwp_add_to_whatsapp_cart_v110() {
        if (isset($_POST['product_id'])) {
            $product_id = intval($_POST['product_id']);
            
            if (!isset($_SESSION['whatsapp_cart'])) {
                $_SESSION['whatsapp_cart'] = array();
            }

            $_SESSION['whatsapp_cart'][$product_id] = isset($_SESSION['whatsapp_cart'][$product_id]) ? $_SESSION['whatsapp_cart'][$product_id] + 1 : 1;

            $cart_count = count($_SESSION['whatsapp_cart']);

            wp_send_json_success(array(
                'message' => 'Product added to WhatsApp cart.',
                'cart_count' => $cart_count
            ));
        }
        wp_die();
    }
}
add_action('wp_ajax_superwp_add_to_whatsapp_cart', 'superwp_add_to_whatsapp_cart_v110');
add_action('wp_ajax_nopriv_superwp_add_to_whatsapp_cart', 'superwp_add_to_whatsapp_cart_v110');

// Show WhatsApp Cart Items
if (!function_exists('superwp_show_whatsapp_cart_items_v120')) {
    function superwp_show_whatsapp_cart_items_v120() {
        $cart_items = isset($_SESSION['whatsapp_cart']) ? $_SESSION['whatsapp_cart'] : array();
        $output = '<h4>Your WhatsApp Cart</h4>';
        $output .= '<ul>';
        if (!empty($cart_items)) {
            foreach ($cart_items as $product_id => $quantity) {
                $product = wc_get_product($product_id);
                $output .= '<li>';
                $output .= esc_html($product->get_name());
                $output .= ' <input type="number" class="cart-quantity" data-product-id="' . esc_attr($product_id) . '" value="' . esc_attr($quantity) . '" min="1">';
                $output .= ' <button class="remove-from-cart" data-product-id="' . esc_attr($product_id) . '">Remove</button>';
                $output .= '</li>';
            }
        } else {
            $output .= '<li>No items in the cart.</li>';
        }
        $output .= '</ul>';
        echo $output;
        wp_die();
    }
}
add_action('wp_ajax_superwp_show_whatsapp_cart', 'superwp_show_whatsapp_cart_items_v120');
add_action('wp_ajax_nopriv_superwp_show_whatsapp_cart', 'superwp_show_whatsapp_cart_items_v120');

// Update Cart Item Quantity
if (!function_exists('superwp_update_cart_quantity_v120')) {
    function superwp_update_cart_quantity_v120() {
        if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);
            
            if ($quantity > 0) {
                $_SESSION['whatsapp_cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['whatsapp_cart'][$product_id]);
            }
            
            wp_send_json_success(array(
                'message' => 'Cart updated successfully.',
                'cart_count' => count($_SESSION['whatsapp_cart'])
            ));
        }
        wp_die();
    }
}
add_action('wp_ajax_superwp_update_cart_quantity', 'superwp_update_cart_quantity_v120');
add_action('wp_ajax_nopriv_superwp_update_cart_quantity', 'superwp_update_cart_quantity_v120');

// Remove Item from Cart
if (!function_exists('superwp_remove_from_cart_v120')) {
    function superwp_remove_from_cart_v120() {
        if (isset($_POST['product_id'])) {
            $product_id = intval($_POST['product_id']);
            
            if (isset($_SESSION['whatsapp_cart'][$product_id])) {
                unset($_SESSION['whatsapp_cart'][$product_id]);
            }
            
            wp_send_json_success(array(
                'message' => 'Item removed from cart.',
                'cart_count' => count($_SESSION['whatsapp_cart'])
            ));
        }
        wp_die();
    }
}
add_action('wp_ajax_superwp_remove_from_cart', 'superwp_remove_from_cart_v120');
add_action('wp_ajax_nopriv_superwp_remove_from_cart', 'superwp_remove_from_cart_v120');

// Notify Admin on Order
if (!function_exists('superwp_notify_admin_v110')) {
    function superwp_notify_admin_v110($order_id) {
        $order = wc_get_order($order_id);
        $admin_phone = get_option('superwp_admin_phone');

        $message = "New order received:\n";
        foreach ($order->get_items() as $item_id => $item) {
            $message .= $item->get_name() . ' x ' . $item->get_quantity() . "\n";
        }
        $message .= "Total: " . $order->get_total();

        $whatsapp_url = "https://wa.me/$admin_phone?text=" . urlencode($message);
        wp_remote_get($whatsapp_url);
    }
}
add_action('woocommerce_thankyou', 'superwp_notify_admin_v110');

// Admin Menu for Plugin Settings
if (!function_exists('superwp_admin_menu_v110')) {
    function superwp_admin_menu_v110() {
        add_menu_page(
            'SuperWP WhatsApp Delivery',
            'SuperWP WhatsApp',
            'manage_options',
            'superwp-whatsapp-settings',
            'superwp_settings_page_v110',
            'dashicons-whatsapp',
            56
        );
    }
}
add_action('admin_menu', 'superwp_admin_menu_v110');

// Settings Page Content
if (!function_exists('superwp_settings_page_v110')) {
    function superwp_settings_page_v110() {
        ?>
        <div class="wrap">
            <h1>SuperWP WhatsApp Delivery Settings</h1>
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                settings_fields('superwp_whatsapp_settings_group');
                do_settings_sections('superwp-whatsapp-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Register Settings
if (!function_exists('superwp_register_settings_v110')) {
    function superwp_register_settings_v110() {
        register_setting('superwp_whatsapp_settings_group', 'superwp_admin_phone', 'superwp_sanitize_admin_phone');
        register_setting('superwp_whatsapp_settings_group', 'superwp_hide_add_to_cart');
        register_setting('superwp_whatsapp_settings_group', 'superwp_whatsapp_button_text');
        register_setting('superwp_whatsapp_settings_group', 'superwp_whatsapp_message_template');
        register_setting('superwp_whatsapp_settings_group', 'superwp_whatsapp_cart_icon', 'superwp_handle_icon_upload');
        register_setting('superwp_whatsapp_settings_group', 'superwp_whatsapp_button_color');
        register_setting('superwp_whatsapp_settings_group', 'superwp_whatsapp_button_text_color');
        register_setting('superwp_whatsapp_settings_group', 'superwp_whatsapp_enable_cart');

        add_settings_section(
            'superwp_whatsapp_main_section',
            'WhatsApp Settings',
            'superwp_section_callback_v110',
            'superwp-whatsapp-settings'
        );

        add_settings_field(
            'superwp_admin_phone',
            'Admin WhatsApp Number (required)',
            'superwp_admin_phone_callback_v110',
            'superwp-whatsapp-settings',
            'superwp_whatsapp_main_section'
        );

        add_settings_field(
            'superwp_hide_add_to_cart',
            'Hide Add to Cart Button',
            'superwp_hide_add_to_cart_callback_v120',
            'superwp-whatsapp-settings',
            'superwp_whatsapp_main_section'
        );

        add_settings_field(
            'superwp_whatsapp_button_text',
            'WhatsApp Button Text',
            'superwp_whatsapp_button_text_callback_v110',
            'superwp-whatsapp-settings',
            'superwp_whatsapp_main_section'
        );

        add_settings_field(
            'superwp_whatsapp_message_template',
            'WhatsApp Message Template',
            'superwp_whatsapp_message_template_callback_v110',
            'superwp-whatsapp-settings',
            'superwp_whatsapp_main_section'
        );

        add_settings_field(
            'superwp_whatsapp_cart_icon',
            'WhatsApp Cart Icon',
            'superwp_whatsapp_cart_icon_callback_v110',
            'superwp-whatsapp-settings',
            'superwp_whatsapp_main_section'
        );

        add_settings_field(
            'superwp_whatsapp_button_color',
            'WhatsApp Button Color',
            'superwp_whatsapp_button_color_callback_v110',
            'superwp-whatsapp-settings',
            'superwp_whatsapp_main_section'
        );

        add_settings_field(
            'superwp_whatsapp_button_text_color',
            'WhatsApp Button Text Color',
            'superwp_whatsapp_button_text_color_callback_v110',
            'superwp-whatsapp-settings',
            'superwp_whatsapp_main_section'
        );

        add_settings_field(
            'superwp_whatsapp_enable_cart',
            'Enable WhatsApp Cart',
            'superwp_whatsapp_enable_cart_callback_v110',
            'superwp-whatsapp-settings',
            'superwp_whatsapp_main_section'
        );
    }
}
add_action('admin_init', 'superwp_register_settings_v110');

// Sanitize and validate admin phone number
if (!function_exists('superwp_sanitize_admin_phone')) {
    function superwp_sanitize_admin_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($phone)) {
            add_settings_error('superwp_admin_phone', 'superwp_admin_phone_error', 'Admin WhatsApp Number is required.');
            return get_option('superwp_admin_phone'); // Return the old value
        }
        return $phone;
    }
}

// Section Callback
if (!function_exists('superwp_section_callback_v110')) {
    function superwp_section_callback_v110() {
        echo '<p>Configure the settings for the WhatsApp Delivery System.</p>';
    }
}

// Admin Phone Callback
if (!function_exists('superwp_admin_phone_callback_v110')) {
    function superwp_admin_phone_callback_v110() {
        $admin_phone = get_option('superwp_admin_phone', '');
        echo '<input type="text" id="superwp_admin_phone" name="superwp_admin_phone" value="' . esc_attr($admin_phone) . '" required />';
        echo '<p class="description">Enter the WhatsApp number where order notifications will be sent.</p>';
    }
}

// Hide Add to Cart Callback
if (!function_exists('superwp_hide_add_to_cart_callback_v120')) {
    function superwp_hide_add_to_cart_callback_v120() {
        $hide_add_to_cart = get_option('superwp_hide_add_to_cart', 'no');
        echo '<input type="checkbox" id="superwp_hide_add_to_cart" name="superwp_hide_add_to_cart" value="yes"' . checked('yes', $hide_add_to_cart, false) . ' />';
        echo '<label for="superwp_hide_add_to_cart">Hide "Add to Cart" button for all products</label>';
        echo '<p class="description">This will hide all WooCommerce "Add to Cart" buttons throughout the site.</p>';
    }
}

// WhatsApp Button Text Callback
if (!function_exists('superwp_whatsapp_button_text_callback_v110')) {
    function superwp_whatsapp_button_text_callback_v110() {
        $whatsapp_button_text = get_option('superwp_whatsapp_button_text', 'Order via WhatsApp');
        echo '<input type="text" id="superwp_whatsapp_button_text" name="superwp_whatsapp_button_text" value="' . esc_attr($whatsapp_button_text) . '" />';
        echo '<p class="description">Customize the text displayed on the WhatsApp order button.</p>';
    }
}

// WhatsApp Message Template Callback
if (!function_exists('superwp_whatsapp_message_template_callback_v110')) {
    function superwp_whatsapp_message_template_callback_v110() {
        $message_template = get_option('superwp_whatsapp_message_template', "Hello, I would like to place an order:\n\n[order_details]\n\nName: [customer_name]\nPhone: [customer_phone]\nAddress: [customer_address]");
        echo '<textarea id="superwp_whatsapp_message_template" name="superwp_whatsapp_message_template" rows="5" cols="50">' . esc_textarea($message_template) . '</textarea>';
        echo '<p class="description">Customize the WhatsApp message template. Available shortcodes: [order_details], [customer_name], [customer_phone], [customer_address]</p>';
    }
}

// WhatsApp Cart Icon Callback
if (!function_exists('superwp_whatsapp_cart_icon_callback_v110')) {
    function superwp_whatsapp_cart_icon_callback_v110() {
        $icon_url = get_option('superwp_whatsapp_cart_icon', plugins_url('/images/whatsapp-icon.png', __FILE__));
        echo '<input type="file" id="superwp_whatsapp_cart_icon" name="superwp_whatsapp_cart_icon" accept="image/*">';
        echo '<p class="description">Upload a custom icon for the WhatsApp cart (recommended size: 50x50px).</p>';
        echo '<p>Current icon: <img src="' . esc_url($icon_url) . '" alt="WhatsApp Cart Icon" style="max-width: 50px; max-height: 50px;"></p>';
    }
}

// WhatsApp Button Color Callback
if (!function_exists('superwp_whatsapp_button_color_callback_v110')) {
    function superwp_whatsapp_button_color_callback_v110() {
        $button_color = get_option('superwp_whatsapp_button_color', '#25D366');
        echo '<input type="color" id="superwp_whatsapp_button_color" name="superwp_whatsapp_button_color" value="' . esc_attr($button_color) . '" />';
        echo '<p class="description">Choose the background color for the WhatsApp order button.</p>';
    }
}

// WhatsApp Button Text Color Callback
if (!function_exists('superwp_whatsapp_button_text_color_callback_v110')) {
    function superwp_whatsapp_button_text_color_callback_v110() {
        $button_text_color = get_option('superwp_whatsapp_button_text_color', '#FFFFFF');
        echo '<input type="color" id="superwp_whatsapp_button_text_color" name="superwp_whatsapp_button_text_color" value="' . esc_attr($button_text_color) . '" />';
        echo '<p class="description">Choose the text color for the WhatsApp order button.</p>';
    }
}

// Enable WhatsApp Cart Callback
if (!function_exists('superwp_whatsapp_enable_cart_callback_v110')) {
    function superwp_whatsapp_enable_cart_callback_v110() {
        $enable_cart = get_option('superwp_whatsapp_enable_cart', 'yes');
        echo '<input type="checkbox" id="superwp_whatsapp_enable_cart" name="superwp_whatsapp_enable_cart" value="yes"' . checked('yes', $enable_cart, false) . ' />';
        echo '<label for="superwp_whatsapp_enable_cart">Enable WhatsApp Cart</label>';
    }
}

// Handle icon upload
if (!function_exists('superwp_handle_icon_upload')) {
    function superwp_handle_icon_upload($option) {
        if (!empty($_FILES["superwp_whatsapp_cart_icon"]["tmp_name"])) {
            $urls = wp_handle_upload($_FILES["superwp_whatsapp_cart_icon"], array('test_form' => false));
            if ($urls && !isset($urls['error'])) {
                return $urls['url'];
            }
        }
        return get_option('superwp_whatsapp_cart_icon', plugins_url('/images/whatsapp-icon.png', __FILE__));
    }
}

// Enqueue custom scripts for cart functionality
if (!function_exists('superwp_enqueue_cart_scripts_v150')) {
    function superwp_enqueue_cart_scripts_v150() {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
			$('.superwp-whatsapp-order-button').on('click', function(e) {
				e.preventDefault();
				var productId = $(this).data('product-id');
				$.post(ajaxurl, {
					action: 'superwp_add_to_whatsapp_cart',
					product_id: productId
				}, function(response) {
					if (response.success) {
						alert(response.data.message);
						updateCartCount(response.data.cart_count);
						loadCartItems();
					}
				});
			});

			$('#superwp-whatsapp-checkout-form').on('submit', function(e) {
				e.preventDefault();
				var formData = $(this).serialize();
				formData += '&action=superwp_whatsapp_order';

				$.post(ajaxurl, formData, function(orderResponse) {
					if (orderResponse.success) {
						window.location.href = orderResponse.data.redirect_url;
					}
				});
			});

			// Toggle WhatsApp cart visibility and load items
			$('#superwp-whatsapp-cart-icon').on('click', function() {
				$('#superwp-whatsapp-checkout').toggle();
				loadCartItems();
			});

			// Function to load cart items
			function loadCartItems() {
				$.post(ajaxurl, {
					action: 'superwp_show_whatsapp_cart'
				}, function(cartResponse) {
					$('#whatsapp-cart-items').html(cartResponse);
				});
			}

			// Update cart item quantity
			$(document).on('change', '.cart-quantity', function() {
				var productId = $(this).data('product-id');
				var quantity = $(this).val();
				
				$.post(ajaxurl, {
					action: 'superwp_update_cart_quantity',
					product_id: productId,
					quantity: quantity
				}, function(response) {
					if (response.success) {
						updateCartCount(response.data.cart_count);
						loadCartItems();
					}
				});
			});

			// Remove item from cart
			$(document).on('click', '.remove-from-cart', function() {
				var productId = $(this).data('product-id');
				
				$.post(ajaxurl, {
					action: 'superwp_remove_from_cart',
					product_id: productId
				}, function(response) {
					if (response.success) {
						updateCartCount(response.data.cart_count);
						loadCartItems();
					}
				});
			});

			// Function to update cart count
			function updateCartCount(count) {
				$('#superwp-whatsapp-cart-icon .cart-count').text(count);
			}
		});
        </script>
        <?php
    }
}
add_action('wp_footer', 'superwp_enqueue_cart_scripts_v150');

// Add custom styles
if (!function_exists('superwp_add_custom_styles_v120')) {
    function superwp_add_custom_styles_v120() {
        $icon_url = get_option('superwp_whatsapp_cart_icon', plugins_url('/images/whatsapp-icon.png', __FILE__));
        ?>
        <style type="text/css">
            #superwp-whatsapp-cart-icon {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background-color: #25D366;
                border-radius: 50%;
                width: 60px;
                height: 60px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                z-index: 9999;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                transition: all 0.3s ease;
            }

            #superwp-whatsapp-cart-icon:hover {
                transform: scale(1.1);
            }

            #superwp-whatsapp-cart-icon img {
                width: 40px;
                height: 40px;
                content: url('<?php echo esc_url($icon_url); ?>');
            }

            #superwp-whatsapp-cart-icon .cart-count {
                position: absolute;
                top: -5px;
                right: -5px;
                background-color: #FF0000;
                color: #FFFFFF;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 12px;
                font-weight: bold;
            }

            #superwp-whatsapp-checkout {
                position: fixed;
                bottom: 90px;
                right: 20px;
                background-color: #FFFFFF;
                border: none;
                border-radius: 10px;
                padding: 20px;
                width: 350px;
                z-index: 9998;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                font-family: Arial, sans-serif;
            }

            #superwp-whatsapp-checkout h3 {
                color: #25D366;
                margin-top: 0;
                margin-bottom: 20px;
                font-size: 1.5em;
            }

            #whatsapp-cart-items {
                max-height: 200px;
                overflow-y: auto;
                margin-bottom: 20px;
            }

            #whatsapp-cart-items ul {
                list-style-type: none;
                padding: 0;
            }

            #whatsapp-cart-items li {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }

            #superwp-whatsapp-checkout-form .form-row {
                margin-bottom: 15px;
            }

            #superwp-whatsapp-checkout-form label {
                display: block;
                margin-bottom: 5px;
                color: #333;
            }

            #superwp-whatsapp-checkout-form input[type="text"],
            #superwp-whatsapp-checkout-form input[type="tel"],
            #superwp-whatsapp-checkout-form textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }

            #superwp-whatsapp-checkout-form button {
                background-color: #25D366;
                color: white;
                border: none;
                padding: 12px 20px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
                width: 100%;
                transition: background-color 0.3s ease;
            }

            #superwp-whatsapp-checkout-form button:hover {
                background-color: #128C7E;
            }

            .cart-quantity {
                width: 50px;
                margin: 0 10px;
                padding: 5px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            .remove-from-cart {
                background-color: #ff0000;
                color: white;
                border: none;
                padding: 5px 10px;
                cursor: pointer;
                border-radius: 4px;
                font-size: 12px;
                transition: background-color 0.3s ease;
            }

            .remove-from-cart:hover {
                background-color: #cc0000;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'superwp_add_custom_styles_v120');

// Hide "Add to Cart" button for all products if option is enabled
function superwp_maybe_hide_add_to_cart_v120() {
    $hide_add_to_cart = get_option('superwp_hide_add_to_cart', 'no');
    
    // Check if the option to hide "Add to Cart" is enabled
    if ($hide_add_to_cart === 'yes') {
        // Remove default WooCommerce Add to Cart buttons (shop page and single product page)
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

        // Make all products unpurchasable to further disable the "Add to Cart" functionality
        add_filter('woocommerce_is_purchasable', 'superwp_make_products_unpurchasable', 10, 2);

        // Add custom CSS to hide any remaining "Add to Cart" buttons
        add_action('wp_head', 'superwp_hide_add_to_cart_css');
    }
}
add_action('init', 'superwp_maybe_hide_add_to_cart_v120');

// Make products unpurchasable to hide "Add to Cart" button
function superwp_make_products_unpurchasable($is_purchasable, $product) {
    return false;  // This makes the product unpurchasable
}

// Add custom CSS to hide any remaining "Add to Cart" buttons
function superwp_hide_add_to_cart_css() {
    ?>
    <style type="text/css">
        .add_to_cart_button,
        .single_add_to_cart_button,
        .ajax_add_to_cart,
        .product_type_simple,
        form.cart {
            display: none !important;
        }
    </style>
    <?php
}

// Ensure the option is being saved correctly
function superwp_save_hide_add_to_cart_option() {
    if (isset($_POST['superwp_hide_add_to_cart'])) {
        update_option('superwp_hide_add_to_cart', 'yes');
    } else {
        update_option('superwp_hide_add_to_cart', 'no');
    }
}
add_action('admin_init', 'superwp_save_hide_add_to_cart_option');

// Initialize plugin
if (!function_exists('superwp_whatsapp_delivery_init_v110')) {
    function superwp_whatsapp_delivery_init_v110() {
        // Check if WooCommerce is active
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            // Plugin initialization code here
        } else {
            add_action('admin_notices', 'superwp_woocommerce_missing_notice_v110');
        }
    }
}
add_action('plugins_loaded', 'superwp_whatsapp_delivery_init_v110');

// Admin notice if WooCommerce is not active
if (!function_exists('superwp_woocommerce_missing_notice_v110')) {
    function superwp_woocommerce_missing_notice_v110() {
        ?>
        <div class="error">
            <p><?php _e('SuperWP WooCommerce WhatsApp Food Delivery System requires WooCommerce to be installed and active.', 'superwp-whatsapp-delivery'); ?></p>
        </div>
        <?php
    }
}
<?php
/**
 * Plugin Name: Miramedia Event Manager for TEDx
 * Description: Comprehensive event management plugin for TEDx organizers. Manage talks, speakers, and sponsor companies with custom Gutenberg blocks and advanced filtering options.
 * Version: 1.6
 * Author: Dominic Johnson / Miramedia
 * Author URI: https://miramedia.co.uk
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: miramedia-event-manager-for-tedx
 *
 * Changelog:
 * 1.4 - Amends based on an email 8th December 2025. Added ABSPATH security checks to all PHP files.
 * 1.5 - Tested to v 6.9
 * 1.6 - Person page: show social links below the photo and embed linked talks'
 *       YouTube videos at the bottom. Added Settings > TEDx Event Manager page
 *       for editable text labels. Talks can now be linked to Mira Events.
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include CPT file
require_once plugin_dir_path(__FILE__) . 'settings.php';
require_once plugin_dir_path(__FILE__) . 'cpt.php';
require_once plugin_dir_path(__FILE__) . 'api.php';
require_once plugin_dir_path(__FILE__) . 'blocks.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

// Define a constant for development mode.
if (!defined('MMEVMT_DEV_MODE')) {
    define('MMEVMT_DEV_MODE', true); // Set to false in production.
}


function mmevmt_enqueue_styles() {
    // Use time() to ensure cache busting on every page load.
    $version = time();

    // Enqueue the single CSS file.
    wp_enqueue_style(
        'mmevmt-plugin-style',
        plugins_url('/assets/style.css', __FILE__), // Path to your style.css file.
        array(), // Dependencies (none in this case).
        $version // Forces browser to re-fetch the latest file.
    );
}
add_action('wp_enqueue_scripts', 'mmevmt_enqueue_styles');



// Disable comments and trackbacks - need to put this into an option Y/N
function mmevmt_disable_comments() {
    // Disable support for comments and trackbacks in post types
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        remove_post_type_support($post_type, 'comments');
        remove_post_type_support($post_type, 'trackbacks');
    }

    // Close comments on the front-end
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);

    // Hide existing comments
    add_filter('comments_array', '__return_empty_array', 10, 2);

    // Remove comments from the admin menu
    add_action('admin_menu', function() {
        remove_menu_page('edit-comments.php');
    });

    // Redirect any user trying to access comments page in the admin
    add_action('admin_init', function() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (is_admin() && isset($_GET['page'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $page = sanitize_text_field(wp_unslash($_GET['page']));
            if ($page === 'edit-comments.php') {
                wp_safe_redirect(admin_url());
                exit;
            }
        }
    });

    // Remove comments-related widgets
    add_action('widgets_init', function() {
        unregister_widget('WP_Widget_Recent_Comments');
    });
}

// Hook into WordPress initialization
add_action('init', 'mmevmt_disable_comments');

// Disable Gutenberg for specific post types
function mmevmt_disable_gutenberg($use_block_editor, $post_type) {
    if (in_array($post_type, ['mmevmt_talk', 'mmevmt_company', 'mmevmt_person'])) {
        return false; // Disable Gutenberg
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'mmevmt_disable_gutenberg', 10, 2);

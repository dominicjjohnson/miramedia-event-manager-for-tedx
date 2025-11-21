<?php
/*
Plugin Name: TEDx Custom Post Types
Description: The plugin is going to create different custom post types:
- TEDx Talks
- TEDx Companies
- TEDx People
Version: 1.2
Author: Dominic Johnson / Miramedia
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include CPT file
require_once plugin_dir_path(__FILE__) . 'cpt.php';
require_once plugin_dir_path(__FILE__) . 'api.php';
require_once plugin_dir_path(__FILE__) . 'blocks.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

// Define a constant for development mode.
if (!defined('DEV_MODE')) {
    define('DEV_MODE', true); // Set to false in production.
}


function TEDx_enqueue_styles() {
    // Use time() to ensure cache busting on every page load.
    $version = time();

    // Enqueue the single CSS file.
    wp_enqueue_style(
        'TEDx-plugin-style',
        plugins_url('/assets/style.css', __FILE__), // Path to your style.css file.
        array(), // Dependencies (none in this case).
        $version // Forces browser to re-fetch the latest file.
    );
}
add_action('wp_enqueue_scripts', 'TEDx_enqueue_styles');



// Disable comments and trackbacks - need to put this into an option Y/N
function TEDx_disable_comments() {
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
        if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'edit-comments.php') {
            wp_safe_redirect(admin_url());
            exit;
        }
    });

    // Remove comments-related widgets
    add_action('widgets_init', function() {
        unregister_widget('WP_Widget_Recent_Comments');
    });
}

// Hook into WordPress initialization
add_action('init', 'TEDx_disable_comments');

// Disable Gutenberg for specific post types
function TEDx_disable_gutenberg($use_block_editor, $post_type) {
    if (in_array($post_type, ['talk', 'company', 'person'])) {
        return false; // Disable Gutenberg
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'TEDx_disable_gutenberg', 10, 2);


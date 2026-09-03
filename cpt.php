<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Register custom taxonomies
function mmevmt_register_taxonomies() {

// Register post meta for Person Type
register_post_meta('mmevmt_person', 'mmevmt_person_type', array(
    'show_in_rest'  => true, // Enables REST API access
    'single'        => true,  // Stores a single value per post
    'type'          => 'string', // Ensures stored data is a string
));

// Register taxonomy for Person Type
register_taxonomy('mmevmt_person_type', 'mmevmt_person', array(
    'labels' => array(
        'name' => 'Person Types',
        'singular_name' => 'Person Type'
    ),
    'hierarchical' => true,
    'show_in_rest' => true
));

// Register post meta for Company Type
register_post_meta('mmevmt_company', 'mmevmt_company_type', array(
    'show_in_rest'  => true,
    'single'        => true,
    'type'          => 'string',
));

// Register taxonomy for Company Type
register_taxonomy('mmevmt_company_type', 'mmevmt_company', array(
    'labels' => array(
        'name' => 'Company Types',
        'singular_name' => 'Company Type'
    ),
    'hierarchical' => true,
    'public' => true,
    'show_ui' => true,
    'show_in_rest' => true,
    'rest_base' => 'mmevmt_company_type',
    'rest_controller_class' => 'WP_REST_Terms_Controller'
));


    register_post_meta('mmevmt_talk', 'mmevmt_talk_year', array(
    'show_in_rest'  => true, // Enables REST API access
    'single'        => true,  // Stores a single value per post
    'type'          => 'string', // Ensures stored data is a string
    ));

    register_taxonomy('mmevmt_talk_year', 'mmevmt_talk', array(
        'labels' => array(
            'name' => 'Talk Years',
            'singular_name' => 'Talk Year'
        ),
        'hierarchical' => true,
        'show_in_rest' => true
    ));

    // Register post meta for talk_year
    register_post_meta('mmevmt_talk', 'mmevmt_talk_year', array(
        'show_in_rest'  => true, // Enables REST API access
        'single'        => true,  // Stores a single value per post
        'type'          => 'string', // Ensures stored data is a string
    ));


    // Register non-hierarchical taxonomy: Tags (like post tags)
    register_taxonomy('mmevmt_tags', 'mmevmt_talk', array(
        'labels' => array(
            'name' => 'Tags',
            'singular_name' => 'Tag'
        ),
        'hierarchical'  => false, // Acts like tags
        'show_in_rest'  => true
    ));

    // Register hierarchical taxonomy: Topic (acts like categories)
    register_taxonomy('mmevmt_topic', 'mmevmt_talk', array(
        'labels' => array(
            'name' => 'Topics',
            'singular_name' => 'Topic'
        ),
        'hierarchical'  => true, // Acts like categories
        'show_in_rest'  => true
    ));
   


}
add_action('init', 'mmevmt_register_taxonomies');

// Add REST API support for taxonomy filtering
function mmevmt_add_rest_taxonomy_filter() {
    // Register company_type filter for company post type
    add_filter('rest_mmevmt_company_query', function($args, $request) {
        if (isset($request['mmevmt_company_type'])) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'mmevmt_company_type',
                    'field' => 'term_id',
                    'terms' => $request['mmevmt_company_type'],
                )
            );
        }
        return $args;
    }, 10, 2);

    // Register person_type filter for person post type
    add_filter('rest_mmevmt_person_query', function($args, $request) {
        if (isset($request['mmevmt_person_type'])) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'mmevmt_person_type',
                    'field' => 'term_id',
                    'terms' => $request['mmevmt_person_type'],
                )
            );
        }
        return $args;
    }, 10, 2);

    // Register talk_year filter for talk post type
    add_filter('rest_mmevmt_talk_query', function($args, $request) {
        if (isset($request['mmevmt_talk_year'])) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'mmevmt_talk_year',
                    'field' => 'term_id',
                    'terms' => $request['mmevmt_talk_year'],
                )
            );
        }
        return $args;
    }, 10, 2);
}
add_action('rest_api_init', 'mmevmt_add_rest_taxonomy_filter');
// Register custom post types
function mmevmt_register_custom_post_types() {
    mmevmt_register_custom_post_types_people();
    mmevmt_register_custom_post_types_company();
    mmevmt_register_custom_post_types_talks();
}
add_action('init', 'mmevmt_register_custom_post_types');

// Convert a YouTube watch/share/shorts URL (youtube.com/watch?v=, youtu.be/, /shorts/, etc.) into an embeddable URL
function mmevmt_get_youtube_embed_url($url) {
    if (preg_match('#(?:youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})#i', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    return '';
}

function mmevmt_register_custom_post_types_people () {

    // People CPT
    register_post_type('mmevmt_person', array(
        'labels' => array(
            'name' => 'People',
            'singular_name' => 'Person',
            'add_new_item' => 'Add New Person', // Changes "Add New Post" to "Add New Person"
            'edit_item' => 'Edit Person', // Changes "Edit Post" to "Edit Person"
            'new_item' => 'New Person', // Changes "New Post" to "New Person"
            'view_item' => 'View Person', // Changes "View Post" to "View Person"
            'search_items' => 'Search People', // Changes "Search Posts" to "Search People"
            'not_found' => 'No People found', // Changes "No Posts found" to "No People found"
            'not_found_in_trash' => 'No People found in Trash', // Changes "No Posts found in Trash" to "No People found in Trash"
            'parent_item_colon' => 'Parent Person:', // Changes "Parent Post:" to "Parent Person:"
            'all_items' => 'All People', // Changes "All Posts" to "All People"
            'archives' => 'Person Archives', // Changes "Post Archives" to "Person Archives"
            'insert_into_item' => 'Insert into Person', // Changes "Insert into Post" to "Insert into Person"
            'uploaded_to_this_item' => 'Uploaded to this Person', // Changes "Uploaded to this Post" to "Uploaded to this Person"
            'featured_image' => 'Featured Image', // Changes "Featured Image" to "Featured Image"
            'set_featured_image' => 'Set Featured Image', // Changes "Set Featured Image" to "Set Featured Image"
            'remove_featured_image' => 'Remove Featured Image', // Changes "Remove Featured Image" to "Remove Featured Image"
            'use_featured_image' => 'Use as Featured Image', // Changes "Use as Featured Image" to "Use as Featured Image"
            'not_an_image' => 'Not an image', // Changes "Not an image" to "Not an image"
            'no_attachment' => 'No attachment', // Changes "No attachment" to "No attachment"
            'no_featured_image' => 'No featured image', // Changes "No featured image" to "No featured image"
            'no_thumbnail' => 'No thumbnail', // Changes "No thumbnail" to "No thumbnail"
            'no_image' => 'No image', // Changes "No image" to "No image"
            'no_post_thumbnail' => 'No post thumbnail', // Changes "No post thumbnail" to "No post thumbnail"
            'no_post_image' => 'No post image', // Changes "No post image" to "No post image"
            'no_post_thumbnail_image' => 'No post thumbnail image', // Changes "No post thumbnail image" to "No post thumbnail image"
            'no_post_featured_image' => 'No post featured image', // Changes "No post featured image" to "No post featured image"
        ),
        'public' => true,
        'menu_icon' => 'dashicons-admin-users',
        'has_archive' => true,
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'), // 'thumbnail' for featured image
        'taxonomies' => array('mmevmt_person_type'),
        'show_in_rest' => true,
        'rest_base' => 'mmevmt_person',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'register_meta_box_cb' => function() {
            add_meta_box('mmevmt_person_meta_box', 'Person Details', function($post) {
            $meta = get_post_meta($post->ID);
            ?>
            <?php wp_nonce_field('mmevmt_person_meta_nonce_action', 'mmevmt_person_meta_nonce'); ?>
            <p>
                <label for="company_name">Company Name:</label><br>
                <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($meta['company_name'][0] ?? ''); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="job_title">Job Title:</label><br>
                <input type="text" id="job_title" name="job_title" value="<?php echo esc_attr($meta['job_title'][0] ?? ''); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="email_address">Email Address:</label><br>
                <input type="email" id="email_address" name="email_address" value="<?php echo esc_attr($meta['email_address'][0] ?? ''); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="social_links">Social Links (JSON format):</label><br>
                <textarea id="social_links" name="social_links" style="width: 100%;"><?php echo esc_textarea($meta['social_links'][0] ?? '{"instagram": "", "facebook": "", "x": "", "youtube": "", "website": ""}'); ?></textarea>
            </p>
            <p>
                <label for="old_username">Old Username:</label><br>
                <input type="text" id="old_username" name="old_username" value="<?php echo esc_attr($meta['old_username'][0] ?? ''); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="telephone_number">Telephone Number:</label><br>
                <input type="text" id="telephone_number" name="telephone_number" value="<?php echo esc_attr($meta['telephone_number'][0] ?? ''); ?>" style="width: 100%;">
            </p>

            <?php
            }, 'mmevmt_person', 'normal', 'high');
        }
    ));

    // Save custom fields
    add_action('save_post_mmevmt_person', function($post_id) {
        // Verify nonce for security
        if (!isset($_POST['mmevmt_person_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mmevmt_person_meta_nonce'])), 'mmevmt_person_meta_nonce_action')) {
            return;
        }
        
        // Check if current user can edit posts
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (array_key_exists('company_name', $_POST)) {
            update_post_meta($post_id, 'company_name', sanitize_text_field(wp_unslash($_POST['company_name'])));
        }
        if (array_key_exists('job_title', $_POST)) {
            update_post_meta($post_id, 'job_title', sanitize_text_field(wp_unslash($_POST['job_title'])));
        }
        if (array_key_exists('email_address', $_POST)) {
            update_post_meta($post_id, 'email_address', sanitize_email(wp_unslash($_POST['email_address'])));
        }
        if (array_key_exists('social_links', $_POST)) {
            update_post_meta($post_id, 'social_links', sanitize_textarea_field(wp_unslash($_POST['social_links'])));
        }
    });

    // Display social links below the featured image on the single Person page
    add_filter('the_content', function($content) {
        if (is_singular('mmevmt_person') && in_the_loop() && is_main_query()) {
            $social_links = get_post_meta(get_the_ID(), 'social_links', true);
            $social_links = json_decode($social_links, true);

            if (!empty($social_links)) {
                $links_html = '<div class="social-links person-social-links">';
                foreach ($social_links as $platform => $link) {
                    if (!empty($link)) {
                        $links_html .= '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">' . esc_html(ucfirst($platform)) . '</a>';
                    }
                }
                $links_html .= '</div>';

                $content = $links_html . $content;
            }

            // Embed YouTube videos for talks linked to this person at the bottom of the page
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
            $talks = get_posts(array(
                'post_type' => 'mmevmt_talk',
                'posts_per_page' => -1,
                'meta_key' => 'person_link',
                'meta_value' => get_the_ID(),
                'orderby' => 'title',
                'order' => 'ASC',
            ));

            $videos_html = '';
            foreach ($talks as $talk) {
                $youtube_link = get_post_meta($talk->ID, 'youtube_link', true);
                $embed_url = $youtube_link ? mmevmt_get_youtube_embed_url($youtube_link) : '';

                if ($embed_url) {
                    $videos_html .= '<div class="person-talk-video">';
                    $videos_html .= '<div class="person-talk-video-frame">';
                    $videos_html .= '<iframe src="' . esc_url($embed_url) . '" title="' . esc_attr($talk->post_title) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';
                    $videos_html .= '</div>';
                    $videos_html .= '<p class="person-talk-title">' . esc_html($talk->post_title) . '</p>';
                    $videos_html .= '</div>';
                }
            }

            if (!empty($videos_html)) {
                $content .= '<div class="person-talks"><h3>' . esc_html(mmevmt_get_label('talks_heading')) . '</h3><div class="person-talks-list">' . $videos_html . '</div></div>';
            }
        }

        return $content;
    });
}

function mmevmt_register_custom_post_types_company() {

    // Company CPT
    register_post_type('mmevmt_company', array(
        'labels' => array(
            'name' => 'Companies',
            'singular_name' => 'Company',
            'add_new_item' => 'Add New Company',
            'edit_item' => 'Edit Company',
            'new_item' => 'New Company',
            'view_item' => 'View Company',
            'search_items' => 'Search Companies',
            'not_found' => 'No Companies found',
            'not_found_in_trash' => 'No Companies found in Trash',
            'parent_item_colon' => 'Parent Company:',
            'all_items' => 'All Companies',
            'archives' => 'Company Archives',
            'insert_into_item' => 'Insert into Company',
            'uploaded_to_this_item' => 'Uploaded to this Company',
            'featured_image' => 'Featured Image',
            'set_featured_image' => 'Set Featured Image',
            'remove_featured_image' => 'Remove Featured Image',
            'use_featured_image' => 'Use as Featured Image',
        ),
        'public' => true,
        'menu_icon' => 'dashicons-building',
        'has_archive' => true,
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'taxonomies' => array('mmevmt_company_type'), // Updated taxonomy to 'mmevmt_company_type'
        'show_in_rest' => true,
        'rest_base' => 'mmevmt_company',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'register_meta_box_cb' => function() {
            add_meta_box('mmevmt_company_meta_box', 'Company Details', function($post) {
                $meta = get_post_meta($post->ID);
                ?>
                <?php wp_nonce_field('mmevmt_company_meta_nonce_action', 'mmevmt_company_meta_nonce'); ?>
                <p>
                    <label for="company_name">Company Name:</label><br>
                    <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($meta['company_name'][0] ?? ''); ?>" style="width: 100%;">
                </p>
                <p>
                    <label for="industry_type">Industry Type:</label><br>
                    <input type="text" id="industry_type" name="industry_type" value="<?php echo esc_attr($meta['industry_type'][0] ?? ''); ?>" style="width: 100%;">
                </p>
                <p>
                    <label for="company_email">Email Address:</label><br>
                    <input type="email" id="company_email" name="company_email" value="<?php echo esc_attr($meta['company_email'][0] ?? ''); ?>" style="width: 100%;">
                </p>
                <p>
                    <label for="social_links">Social Links (JSON format):</label><br>
                    <textarea id="social_links" name="social_links" style="width: 100%;"><?php echo esc_textarea($meta['social_links'][0] ?? '{"linkedin": "", "facebook": "", "twitter": "", "website": ""}'); ?></textarea>
                </p>
                <p>
                    <label for="telephone_number">Telephone Number:</label><br>
                    <input type="text" id="telephone_number" name="telephone_number" value="<?php echo esc_attr($meta['telephone_number'][0] ?? ''); ?>" style="width: 100%;">
                </p>
                <?php
            }, 'mmevmt_company', 'normal', 'high');
        }
    ));

    // Save custom fields
    add_action('save_post_mmevmt_company', function($post_id) {
        // Verify nonce for security
        if (!isset($_POST['mmevmt_company_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mmevmt_company_meta_nonce'])), 'mmevmt_company_meta_nonce_action')) {
            return;
        }
        
        // Check if current user can edit posts
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (array_key_exists('company_name', $_POST)) {
            update_post_meta($post_id, 'company_name', sanitize_text_field(wp_unslash($_POST['company_name'])));
        }
        if (array_key_exists('industry_type', $_POST)) {
            update_post_meta($post_id, 'industry_type', sanitize_text_field(wp_unslash($_POST['industry_type'])));
        }
        if (array_key_exists('company_email', $_POST)) {
            update_post_meta($post_id, 'company_email', sanitize_email(wp_unslash($_POST['company_email'])));
        }
        if (array_key_exists('social_links', $_POST)) {
            update_post_meta($post_id, 'social_links', sanitize_textarea_field(wp_unslash($_POST['social_links'])));
        }
        if (array_key_exists('telephone_number', $_POST)) {
            update_post_meta($post_id, 'telephone_number', sanitize_text_field(wp_unslash($_POST['telephone_number'])));
        }
    });
}

function mmevmt_register_custom_post_types_talks() {

    // Talks CPT
    register_post_type('mmevmt_talk', array(
        'labels' => array(
            'name' => 'Talks',
            'singular_name' => 'Talk',
            'add_new_item' => 'Add New Talk',
            'edit_item' => 'Edit Talk',
            'new_item' => 'New Talk',
            'view_item' => 'View Talk',
            'search_items' => 'Search Talks',
            'not_found' => 'No Talks found',
            'not_found_in_trash' => 'No Talks found in Trash',
            'parent_item_colon' => 'Parent Talk:',
            'all_items' => 'All Talks',
            'archives' => 'Talk Archives',
            'insert_into_item' => 'Insert into Talk',
            'uploaded_to_this_item' => 'Uploaded to this Talk',
            'featured_image' => 'Featured Image',
            'set_featured_image' => 'Set Featured Image',
            'remove_featured_image' => 'Remove Featured Image',
            'use_featured_image' => 'Use as Featured Image',
        ),
        'public' => true,
        'menu_icon' => 'dashicons-video-alt',
        'has_archive' => true,
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'rewrite'           => array('slug' => 'talk', 'with_front' => false),
        'taxonomies' => array('mmevmt_talk_year'), // Use 'mmevmt_talk_year' taxonomy
        'show_in_rest' => true,
        'rest_base' => 'mmevmt_talk',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'register_meta_box_cb' => function() {
            add_meta_box('mmevmt_talk_meta_box', 'Talk Details', function($post) {
                $meta = get_post_meta($post->ID);
                // Get all people for dropdown
                $people = get_posts(array(
                    'post_type' => 'mmevmt_person',
                    'posts_per_page' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC'
                ));
                $selected_person = $meta['person_link'][0] ?? '';
                ?>
                <?php wp_nonce_field('mmevmt_talk_meta_nonce_action', 'mmevmt_talk_meta_nonce'); ?>
                <p>
                    <label for="youtube_link">YouTube Link:</label>Add here to overwrite call to /talk and link to video<br>
                    <input type="url" id="youtube_link" name="youtube_link" value="<?php echo esc_attr($meta['youtube_link'][0] ?? ''); ?>" style="width: 100%;" placeholder="https://www.youtube.com/">
                </p>
                <p>
                    <label for="person_link">Speaker (Person):</label><br>
                    <select id="person_link" name="person_link" style="width: 100%;">
                        <option value="">-- Select Person --</option>
                        <?php foreach ($people as $person): ?>
                            <option value="<?php echo esc_attr($person->ID); ?>" <?php selected($selected_person, $person->ID); ?>>
                                <?php echo esc_html($person->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <?php
            }, 'mmevmt_talk', 'normal', 'high');
        }
    ));

    // Save custom fields
    add_action('save_post_mmevmt_talk', function($post_id) {
        // Verify nonce for security
        if (!isset($_POST['mmevmt_talk_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mmevmt_talk_meta_nonce'])), 'mmevmt_talk_meta_nonce_action')) {
            return;
        }
        
        // Check if current user can edit posts
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (array_key_exists('youtube_link', $_POST)) {
            update_post_meta($post_id, 'youtube_link', esc_url_raw(wp_unslash($_POST['youtube_link'])));
        }
        if (array_key_exists('person_link', $_POST)) {
            update_post_meta($post_id, 'person_link', intval(wp_unslash($_POST['person_link'])));
        }
    });
}



?>
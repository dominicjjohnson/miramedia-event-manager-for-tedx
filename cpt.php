<?php

// Register custom taxonomies
function miramedia_tedx_register_taxonomies() {

// Register post meta for Person Type
register_post_meta('person', 'person_type', array(
    'show_in_rest'  => true, // Enables REST API access
    'single'        => true,  // Stores a single value per post
    'type'          => 'string', // Ensures stored data is a string
));

// Register taxonomy for Person Type
register_taxonomy('person_type', 'person', array(
    'labels' => array(
        'name' => 'Person Types',
        'singular_name' => 'Person Type'
    ),
    'hierarchical' => true,
    'show_in_rest' => true
));

// Register post meta for Company Type
register_post_meta('company', 'company_type', array(
    'show_in_rest'  => true,
    'single'        => true,
    'type'          => 'string',
));

// Register taxonomy for Company Type
register_taxonomy('company_type', 'company', array(
    'labels' => array(
        'name' => 'Company Types',
        'singular_name' => 'Company Type'
    ),
    'hierarchical' => true,
    'public' => true,
    'show_ui' => true,
    'show_in_rest' => true,
    'rest_base' => 'company_type',
    'rest_controller_class' => 'WP_REST_Terms_Controller'
));


    register_post_meta('talk', 'talk_year', array(
    'show_in_rest'  => true, // Enables REST API access
    'single'        => true,  // Stores a single value per post
    'type'          => 'string', // Ensures stored data is a string
    ));
    
    register_taxonomy('talk_year', 'talk', array(
        'labels' => array(
            'name' => 'Talk Years',
            'singular_name' => 'Talk Year'
        ),
        'hierarchical' => true,
        'show_in_rest' => true
    ));

    // Register post meta for talk_year
    register_post_meta('talk', 'talk_year', array(
        'show_in_rest'  => true, // Enables REST API access
        'single'        => true,  // Stores a single value per post
        'type'          => 'string', // Ensures stored data is a string
    ));


    // Register non-hierarchical taxonomy: Tags (like post tags)
    register_taxonomy('tags', 'talk', array(
        'labels' => array(
            'name' => 'Tags',
            'singular_name' => 'Tag'
        ),
        'hierarchical'  => false, // Acts like tags
        'show_in_rest'  => true
    ));

    // Register hierarchical taxonomy: Topic (acts like categories)
    register_taxonomy('topic', 'talk', array(
        'labels' => array(
            'name' => 'Topics',
            'singular_name' => 'Topic'
        ),
        'hierarchical'  => true, // Acts like categories
        'show_in_rest'  => true
    ));
   


}
add_action('init', 'miramedia_tedx_register_taxonomies');

// Add REST API support for taxonomy filtering
function miramedia_tedx_add_rest_taxonomy_filter() {
    // Register company_type filter for company post type
    add_filter('rest_company_query', function($args, $request) {
        if (isset($request['company_type'])) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'company_type',
                    'field' => 'term_id',
                    'terms' => $request['company_type'],
                )
            );
        }
        return $args;
    }, 10, 2);

    // Register person_type filter for person post type
    add_filter('rest_person_query', function($args, $request) {
        if (isset($request['person_type'])) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'person_type',
                    'field' => 'term_id',
                    'terms' => $request['person_type'],
                )
            );
        }
        return $args;
    }, 10, 2);

    // Register talk_year filter for talk post type
    add_filter('rest_talk_query', function($args, $request) {
        if (isset($request['talk_year'])) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'talk_year',
                    'field' => 'term_id',
                    'terms' => $request['talk_year'],
                )
            );
        }
        return $args;
    }, 10, 2);
}
add_action('rest_api_init', 'miramedia_tedx_add_rest_taxonomy_filter');
// Register custom post types
function miramedia_tedx_register_custom_post_types() {
    miramedia_tedx_register_custom_post_types_people();
    miramedia_tedx_register_custom_post_types_company();
    miramedia_tedx_register_custom_post_types_talks();
}
add_action('init', 'miramedia_tedx_register_custom_post_types');

function miramedia_tedx_register_custom_post_types_people () {

    // People CPT
    register_post_type('person', array(
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
        'taxonomies' => array('person_type'),
        'show_in_rest' => true,
        'rest_base' => 'person',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'register_meta_box_cb' => function() {
            add_meta_box('person_meta_box', 'Person Details', function($post) {
            $meta = get_post_meta($post->ID);
            ?>
            <?php wp_nonce_field('person_meta_nonce_action', 'person_meta_nonce'); ?>
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
            }, 'person', 'normal', 'high');
        }
    ));

    // Save custom fields
    add_action('save_post_person', function($post_id) {
        // Verify nonce for security
        if (!isset($_POST['person_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['person_meta_nonce'])), 'person_meta_nonce_action')) {
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
}

function miramedia_tedx_register_custom_post_types_company() {

    // Company CPT
    register_post_type('company', array(
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
        'taxonomies' => array('company_type'), // Updated taxonomy to 'company_type'
        'show_in_rest' => true,
        'rest_base' => 'company',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'register_meta_box_cb' => function() {
            add_meta_box('company_meta_box', 'Company Details', function($post) {
                $meta = get_post_meta($post->ID);
                ?>
                <?php wp_nonce_field('company_meta_nonce_action', 'company_meta_nonce'); ?>
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
            }, 'company', 'normal', 'high');
        }
    ));

    // Save custom fields
    add_action('save_post_company', function($post_id) {
        // Verify nonce for security
        if (!isset($_POST['company_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['company_meta_nonce'])), 'company_meta_nonce_action')) {
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

function miramedia_tedx_register_custom_post_types_talks() {

    // Talks CPT
    register_post_type('talk', array(
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
        'taxonomies' => array('talk_year'), // Use 'talk_year' taxonomy
        'show_in_rest' => true,
        'rest_base' => 'talk',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'register_meta_box_cb' => function() {
            add_meta_box('talk_meta_box', 'Talk Details', function($post) {
                $meta = get_post_meta($post->ID);
                // Get all people for dropdown
                $people = get_posts(array(
                    'post_type' => 'person',
                    'posts_per_page' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC'
                ));
                $selected_person = $meta['person_link'][0] ?? '';
                ?>
                <?php wp_nonce_field('talk_meta_nonce_action', 'talk_meta_nonce'); ?>
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
            }, 'talk', 'normal', 'high');
        }
    ));

    // Save custom fields
    add_action('save_post_talk', function($post_id) {
        // Verify nonce for security
        if (!isset($_POST['talk_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['talk_meta_nonce'])), 'talk_meta_nonce_action')) {
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
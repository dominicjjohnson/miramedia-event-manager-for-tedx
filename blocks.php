<?php

// Add block Category Miramedia
function add_miramedia_block_category($categories, $post) {
    // Create the Miramedia category
    $miramedia_category = array(
        array(
            'slug' => 'miramedia',
            'title' => 'Miramedia',
            'icon'  => 'admin-site', // Optional Dashicon
        ),
    );

    // Merge the Miramedia category to the top of the existing categories
    return array_merge($miramedia_category, $categories);
}
add_filter('block_categories_all', 'add_miramedia_block_category', 10, 2);

// Block registration
function register_custom_blocks() {
    wp_register_script(
        'custom-blocks',
        plugins_url('blocks/block-registration.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor'),
        file_exists(plugin_dir_path(__FILE__) . 'blocks/block-registration.js') ? filemtime(plugin_dir_path(__FILE__) . 'blocks/block-registration.js') : false
    );

    register_block_type('miramedia/people-showcase', array(
        'editor_script' => 'custom-blocks',
        'render_callback' => 'tedx_render_people_showcase_block', // Server-side rendering for the block.
        'attributes' => array(
            'personType' => array(
                'type' => 'string',
                'default' => '', // Default to an empty string if no type is selected.
            ),
            'numberOfPeople' => array(
                'type' => 'number',
                'default' => 3,
            ),
        ),
        'supports' => array(
            'html' => false, // Disable HTML mode for this block.
        ),
    ));

    register_block_type('miramedia/talks-showcase', array(
        'editor_script' => 'custom-blocks',
        'render_callback' => 'tedx_render_talks_showcase_block', // Server-side rendering for the block.
        'attributes' => array(
            'year' => array(
            'type' => 'string',
        ),
        'numberOfTalks' => array(
            'type' => 'number',
            'default' => 3,
            ),
        ),
        'supports' => array(
            'html' => false, // Disable HTML mode for this block.
        ),
    ));

    register_block_type('miramedia/companies-showcase', array(
        'editor_script' => 'custom-blocks',
        'render_callback' => 'tedx_render_companies_showcase_block', // Server-side rendering for the block.
        'attributes' => array(
            'numberOfCompanies' => array(
                'type' => 'number',
                'default' => 3,
            ),
            'companyType' => array(
                'type' => 'string',
                'default' => '', // Default to an empty string if no type is selected.
            ),
        ),
        'supports' => array(
            'html' => false, // Disable HTML mode for this block.
        ),
    ));
}


add_action('init', 'register_custom_blocks');


// USER SIDE BLOCK CODE - display db calls

function tedx_render_people_showcase_block($attributes) {

    //$person_type = is_front_page() ? 'team' : 'team';
    $person_type = isset($attributes['person_type']) ? esc_html($attributes['person_type']) : 'Not selected';

    $query = new WP_Query([
        'post_type' => 'person',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'tax_query' => array(
            array(
                'taxonomy' => 'person_type',
                'field'    => 'slug',
                'terms'    => $person_type,
            ),
        ),
    ]);

    if (!$query->have_posts()) {
        return '<p>No People found.</p>';
    }

    $output = '<div class="people-showcase">'; // Grid container starts

    while ($query->have_posts()) {
        $query->the_post();
        $company_name = get_post_meta(get_the_ID(), 'company_name', true);
        $job_title = get_post_meta(get_the_ID(), 'job_title', true);
        $email_address = get_post_meta(get_the_ID(), 'email_address', true);
        $social_links = get_post_meta(get_the_ID(), 'social_links', true);
        $social_links = json_decode($social_links, true);
        $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
    
        $output .= '<div class="person">';
        $output .= '<a href="' . esc_url(get_permalink()) . '"  rel="noopener noreferrer">';

        if ($featured_image) {
            $output .= '<img src="' . esc_url($featured_image) . '" alt="' . esc_attr(get_the_title()) . '" class="person-image" />';
        }
    
        $output .= '<h3 class="person-name">' . esc_html(get_the_title()) . '</h3>';
        $output .= '<p class="person-job-title">' . esc_html($job_title) . '</p>';
        $output .= '<p class="person-company-name">' . esc_html($company_name) . '</p>';
        $output .= '<p class="person-email">' . esc_html($email_address) . '</p>';
    
        $output .= '</a>';
        
        $output .= '<div class="social-links">';
        if (!empty($social_links)) {
            foreach ($social_links as $platform => $link) {
                if (!empty($link)) {
                    $output .= '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">' . esc_html(ucfirst($platform)) . '</a>';
                }
            }
        }
        $output .= '</div>'; // Close social links div
        $output .= '</div>'; // Close person div
    }
    
    wp_reset_postdata();
    $output .= '</div>'; // Close grid container
    
    
    return $output;
}

function tedx_render_talks_showcase_block($attributes) {

    // Set a flag for now to switch between linking to YouTube or the talk post
    $link_to_youtube = isset($attributes['linkToYoutube']) ? (bool) $attributes['linkToYoutube'] : false;

    // Get the selected year from the block attributes
    $talk_year = isset($attributes['year']) ? esc_html($attributes['year']) : 'Not selected';

    $query = new WP_Query([
        'post_type' => 'talk',
        'posts_per_page' => -1,
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'talk_year',
                'field'    => 'slug',
                'terms'    => $talk_year,
            ),
        ),
        'meta_query' => array(
            array(
                'key'     => '_thumbnail_id',
                'compare' => 'EXISTS',
            ),
        ),
    ]);
    
    if (!$query->have_posts()) {
        return '<p>No talk found for YEAR: ' . $talk_year . '</p>';
    }

    $output = '<div class="talks-showcase">'; // Grid container starts
    while ($query->have_posts()) {
        $query->the_post();
        $youtube_video_link = get_post_meta(get_the_ID(), 'youtube_link', true);
    
        // Loop through each talk (example single talk structure)
        $output .= '<div class="talk-card">';

        if ($youtube_video_link && $link_to_youtube) {
            $output .= '<a href="' . esc_url($youtube_video_link) . '" target="_blank" rel="noopener noreferrer">';
        }
        else   {
            $output .= '<a href="' . esc_url(get_permalink()) . '"  rel="noopener noreferrer">';
        }

        if (has_post_thumbnail()) {
            $output .= '<img src="' . esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')) . '" alt="' . esc_attr(get_the_title()) . '" class="talk-thumbnail" />';
        }
        if ($youtube_video_link) {
            $output .= '</a>';
        }
        $output .= '<h3 class="talk-title"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';

        $output .= '<p class="talk-description">' . esc_html(get_the_excerpt()) . '</p>';  
        
        // remove date for now as it is using published date which is nonsense 
        //$output .= '<p class="talk-date">' . esc_html(get_the_date()) . '</p>';
        // Get linked people (assuming a relationship field 'linked_people' storing person post IDs)
        $linked_people = get_post_meta(get_the_ID(), 'person_link', true);

        if (!empty($linked_people)) {
            if (!is_array($linked_people)) {
            $linked_people = explode(',', $linked_people);
            }
            $people_links = array();
            foreach ($linked_people as $person_id) {
            $person_id = intval($person_id);
            if ($person_id && get_post_type($person_id) === 'person') {
                $person_name = get_the_title($person_id);
                $person_link = get_permalink($person_id);
                $people_links[] = '<a href="' . esc_url($person_link) . '">' . esc_html($person_name) . '</a>';
            }
            }
            if (!empty($people_links)) {
            $output .= '<p class="talk-speaker">' . implode(', ', $people_links) . '</p>';
            }
        }
        $output .= '</div>'; // Close talk-card div
    }
    wp_reset_postdata();
    $output .= '</div>'; // Close talks-showcase div
    
    
    return $output;
}

function tedx_render_companies_showcase_block($attributes) {

    //$company_type = is_front_page() ? 'volunteer' : 'partners';
        
    $company_type = isset($attributes['company_type']) ? esc_html($attributes['company_type']) : 'Not selected';

    $query = new WP_Query([
        'post_type' => 'company',
        'posts_per_page' => 3,
        'tax_query' => array(
            array(
                'taxonomy' => 'company_type',
                'field'    => 'slug',
                'terms'    => $company_type,
            ),
        ),
    ]);

    
    if (!$query->have_posts()) {
        return '<p>No companies found for type '.$company_type.'.</p>';
    }

    $output = '<div class="company-showcase">'; // Grid container starts
    while ($query->have_posts()) {
        $query->the_post();
        $logo = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        $website_address = get_post_meta(get_the_ID(), 'website_address', true);
    
        // Loop through each company (example single company structure)
        $output .= '<div class="company-card">';
        if (!empty($logo)) {
            $output .= '<a href="' . esc_url($website_address) . '" target="_blank" rel="noopener noreferrer">';
            $output .= '<img src="' . esc_url($logo) . '" alt="' . esc_attr(get_the_title()) . '" class="company-logo" />';
            $output .= '</a>';
        }
        //$output .= '<h3 class="company-name">' . esc_html(get_the_title()) . '</h3>';
        $output .= '</div>'; // Close company-card div
    }
    wp_reset_postdata();
    $output .= '</div>'; // Close company-showcase div
    
    
    return $output;
}
?>
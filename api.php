<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// api.php

// Ensure the 'mmevmt_talk_year' taxonomy is registered and available in REST API
add_action('init', function () {
    if (!taxonomy_exists('mmevmt_talk_year')) {
        register_taxonomy('mmevmt_talk_year', 'post', [
            'label' => 'Talk Year',
            'public' => true,
            'rewrite' => ['slug' => 'mmevmt_talk_year'],
            'hierarchical' => false,
            'show_in_rest' => true,
        ]);
    }
});

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/mmevmt_talk_year', [
        'methods'  => 'GET',
        'callback' => 'mmevmt_get_talk_year_terms',
        'permission_callback' => '__return_true',
        'args'     => [
            'per_page' => [
                'default' => 100,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
});
// Access the endpoint at /wp-json/wp/v2/mmevmt_talk_year?per_page=100

function mmevmt_get_talk_year_terms($request) {
    $per_page = $request->get_param('per_page') ?: 100;

    // Ensure the taxonomy is correctly specified for 'mmevmt_talk'
    $terms = get_terms([
        'taxonomy'   => 'mmevmt_talk_year',
        'hide_empty' => false,
        'number'     => $per_page,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return new WP_Error('no_terms', 'No terms found for mmevmt_talk_year taxonomy.', ['status' => 404]);
    }

    // Structure data for response
    $data = array_map(function ($term) {
        return [
            'id'    => $term->term_id,
            'name'  => $term->name,
            'slug'  => $term->slug,
            'count' => $term->count,
        ];
    }, $terms);

    return rest_ensure_response($data);
}


// Ensure the 'mmevmt_person_type' taxonomy is registered and available in REST API
add_action('init', function () {
    if (!taxonomy_exists('mmevmt_person_type')) {
        register_taxonomy('mmevmt_person_type', 'post', [
            'label' => 'Person Type',
            'public' => true,
            'rewrite' => ['slug' => 'mmevmt_person_type'],
            'hierarchical' => false,
            'show_in_rest' => true,
        ]);
    }
});

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/mmevmt_person_type', [
        'methods'  => 'GET',
        'callback' => 'mmevmt_get_person_type_terms',
        'permission_callback' => '__return_true',
        'args'     => [
            'per_page' => [
                'default' => 100,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
});

function mmevmt_get_person_type_terms($request) {
    $per_page = $request->get_param('per_page') ?: 100;

    // Ensure the taxonomy is correctly specified for 'mmevmt_person_type'
    $terms = get_terms([
        'taxonomy'   => 'mmevmt_person_type',
        'hide_empty' => false,
        'number'     => $per_page,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return new WP_Error('no_terms', 'No terms found for mmevmt_person_type taxonomy.', ['status' => 404]);
    }

    // Structure data for response
    $data = array_map(function ($term) {
        return [
            'id'    => $term->term_id,
            'name'  => $term->name,
            'slug'  => $term->slug,
            'count' => $term->count,
        ];
    }, $terms);

    return rest_ensure_response($data);
}


// Ensure the 'mmevmt_company_type' taxonomy is registered and available in REST API
add_action('init', function () {
    if (!taxonomy_exists('mmevmt_company_type')) {
        register_taxonomy('mmevmt_company_type', 'post', [
            'label' => 'Company Type',
            'public' => true,
            'rewrite' => ['slug' => 'mmevmt_company_type'],
            'hierarchical' => false,
            'show_in_rest' => true,
        ]);
    }
});

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/mmevmt_company_type', [
        'methods'  => 'GET',
        'callback' => 'mmevmt_get_company_type_terms',
        'permission_callback' => '__return_true',
        'args'     => [
            'per_page' => [
                'default' => 100,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
});

function mmevmt_get_company_type_terms($request) {
    $per_page = $request->get_param('per_page') ?: 100;

    // Ensure the taxonomy is correctly specified for 'mmevmt_company_type'
    $terms = get_terms([
        'taxonomy'   => 'mmevmt_company_type',
        'hide_empty' => false,
        'number'     => $per_page,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return new WP_Error('no_terms', 'No terms found for mmevmt_company_type taxonomy.', ['status' => 404]);
    }

    // Structure data for response
    $data = array_map(function ($term) {
        return [
            'id'    => $term->term_id,
            'name'  => $term->name,
            'slug'  => $term->slug,
            'count' => $term->count,
        ];
    }, $terms);

    return rest_ensure_response($data);
}

// Register custom REST routes for post types
add_action('rest_api_init', function () {
    // Register route for companies with mmevmt_company_type filter
    register_rest_route('wp/v2', '/mmevmt-companies-filtered', [
        'methods'  => 'GET',
        'callback' => 'mmevmt_get_companies_by_type',
        'permission_callback' => '__return_true',
        'args'     => [
            'mmevmt_company_type' => [
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'per_page' => [
                'default' => 3,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);

    // Register route for people with mmevmt_person_type filter
    register_rest_route('wp/v2', '/mmevmt-people-filtered', [
        'methods'  => 'GET',
        'callback' => 'mmevmt_get_people_by_type',
        'permission_callback' => '__return_true',
        'args'     => [
            'mmevmt_person_type' => [
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'per_page' => [
                'default' => 3,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);

    // Register route for talks with mmevmt_talk_year filter
    register_rest_route('wp/v2', '/mmevmt-talks-filtered', [
        'methods'  => 'GET',
        'callback' => 'mmevmt_get_talks_by_year',
        'permission_callback' => '__return_true',
        'args'     => [
            'mmevmt_talk_year' => [
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'per_page' => [
                'default' => 3,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
});

function mmevmt_get_companies_by_type($request) {
    $company_type = $request->get_param('mmevmt_company_type');
    $per_page = $request->get_param('per_page') ?: 3;

    // Handle -1 as ALL
    if ($per_page == '-1') {
        $per_page = -1;
    }

    $args = [
        'post_type' => 'mmevmt_company',
        'posts_per_page' => intval($per_page),
        'post_status' => 'publish',
    ];

    // Only add tax_query if a specific company type is provided
    if (!empty($company_type)) {
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        $args['tax_query'] = [
            [
                'taxonomy' => 'mmevmt_company_type',
                'field'    => 'term_id',
                'terms'    => intval($company_type),
            ],
        ];
    }

    $query = new WP_Query($args);

    if (empty($query->posts)) {
        return rest_ensure_response([]);
    }

    // Format the response
    $data = array_map(function ($post) {
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
        return [
            'id'    => $post->ID,
            'title' => [
                'rendered' => $post->post_title,
            ],
            'featured_media_url' => $thumbnail_url ?: null,
        ];
    }, $query->posts);

    return rest_ensure_response($data);
}

function mmevmt_get_people_by_type($request) {
    $person_type = $request->get_param('mmevmt_person_type');
    $per_page = $request->get_param('per_page') ?: 3;

    // Handle -1 as ALL
    if ($per_page == '-1') {
        $per_page = -1;
    }

    $args = [
        'post_type' => 'mmevmt_person',
        'posts_per_page' => intval($per_page),
        'post_status' => 'publish',
    ];

    // Only add tax_query if a specific person type is provided
    if (!empty($person_type)) {
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        $args['tax_query'] = [
            [
                'taxonomy' => 'mmevmt_person_type',
                'field'    => 'term_id',
                'terms'    => intval($person_type),
            ],
        ];
    }

    $query = new WP_Query($args);

    if (empty($query->posts)) {
        return rest_ensure_response([]);
    }

    // Format the response
    $data = array_map(function ($post) {
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
        return [
            'id'    => $post->ID,
            'title' => [
                'rendered' => $post->post_title,
            ],
            'featured_media_url' => $thumbnail_url ?: null,
        ];
    }, $query->posts);

    return rest_ensure_response($data);
}

function mmevmt_get_talks_by_year($request) {
    $talk_year = $request->get_param('mmevmt_talk_year');
    $per_page = $request->get_param('per_page') ?: 3;

    // Handle -1 as ALL
    if ($per_page == '-1') {
        $per_page = -1;
    }

    $args = [
        'post_type' => 'mmevmt_talk',
        'posts_per_page' => intval($per_page),
        'post_status' => 'publish',
    ];

    // Only add tax_query if a specific talk year is provided
    if (!empty($talk_year)) {
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        $args['tax_query'] = [
            [
                'taxonomy' => 'mmevmt_talk_year',
                'field'    => 'term_id',
                'terms'    => intval($talk_year),
            ],
        ];
    }

    $query = new WP_Query($args);

    if (empty($query->posts)) {
        return rest_ensure_response([]);
    }

    // Format the response
    $data = array_map(function ($post) {
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
        return [
            'id'    => $post->ID,
            'title' => [
                'rendered' => $post->post_title,
            ],
            'featured_media_url' => $thumbnail_url ?: null,
        ];
    }, $query->posts);

    return rest_ensure_response($data);
}

<?php
// api.php

// Ensure the 'talk_year' taxonomy is registered and available in REST API
add_action('init', function () {
    if (!taxonomy_exists('talk_year')) {
        register_taxonomy('talk_year', 'post', [
            'label' => 'Talk Year',
            'public' => true,
            'rewrite' => ['slug' => 'talk_year'],
            'hierarchical' => false,
            'show_in_rest' => true,
        ]);
    }
});

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/talk_year', [
        'methods'  => 'GET',
        'callback' => 'get_talk_year_terms',
        'permission_callback' => '__return_true',
        'args'     => [
            'per_page' => [
                'default' => 100,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
});
// Access the endpoint at /wp-json/wp/v2/talk_year?per_page=100

function get_talk_year_terms($request) {
    $per_page = $request->get_param('per_page') ?: 100;

    // Ensure the taxonomy is correctly specified for 'talk'
    $terms = get_terms([
        'taxonomy'   => 'talk_year',
        'hide_empty' => false,
        'number'     => $per_page,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return new WP_Error('no_terms', 'No terms found for talk_year taxonomy.', ['status' => 404]);
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


// Ensure the 'person_type' taxonomy is registered and available in REST API
add_action('init', function () {
    if (!taxonomy_exists('person_type')) {
        register_taxonomy('person_type', 'post', [
            'label' => 'Person Type',
            'public' => true,
            'rewrite' => ['slug' => 'person_type'],
            'hierarchical' => false,
            'show_in_rest' => true,
        ]);
    }
});

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/person_type', [
        'methods'  => 'GET',
        'callback' => 'get_person_type_terms',
        'permission_callback' => '__return_true',
        'args'     => [
            'per_page' => [
                'default' => 100,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
});

function get_person_type_terms($request) {
    $per_page = $request->get_param('per_page') ?: 100;

    // Ensure the taxonomy is correctly specified for 'person_type'
    $terms = get_terms([
        'taxonomy'   => 'person_type',
        'hide_empty' => false,
        'number'     => $per_page,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return new WP_Error('no_terms', 'No terms found for person_type taxonomy.', ['status' => 404]);
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


// Ensure the 'company_type' taxonomy is registered and available in REST API
add_action('init', function () {
    if (!taxonomy_exists('company_type')) {
        register_taxonomy('company_type', 'post', [
            'label' => 'Company Type',
            'public' => true,
            'rewrite' => ['slug' => 'company_type'],
            'hierarchical' => false,
            'show_in_rest' => true,
        ]);
    }
});

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/company_type', [
        'methods'  => 'GET',
        'callback' => 'get_company_type_terms',
        'permission_callback' => '__return_true',
        'args'     => [
            'per_page' => [
                'default' => 100,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
});

function get_company_type_terms($request) {
    $per_page = $request->get_param('per_page') ?: 100;

    // Ensure the taxonomy is correctly specified for 'company_type'
    $terms = get_terms([
        'taxonomy'   => 'company_type',
        'hide_empty' => false,
        'number'     => $per_page,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return new WP_Error('no_terms', 'No terms found for company_type taxonomy.', ['status' => 404]);
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

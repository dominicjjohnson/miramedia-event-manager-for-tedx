<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Default values for editable front-end text labels
function mmevmt_get_label_defaults() {
    return array(
        'talks_heading'        => 'Talks',
        'speaker_heading'      => 'SPEAKER:',
        'no_people_message'    => 'No People found.',
        'no_talks_message'     => 'No talks found.',
        'no_companies_message' => 'No companies found.',
    );
}

// Get a single label value, falling back to its default
function mmevmt_get_label($key) {
    $defaults = mmevmt_get_label_defaults();
    $labels = get_option('mmevmt_labels', array());
    return !empty($labels[$key]) ? $labels[$key] : $defaults[$key];
}

function mmevmt_sanitize_labels($input) {
    $output = array();
    foreach (mmevmt_get_label_defaults() as $key => $default) {
        $output[$key] = isset($input[$key]) ? sanitize_text_field($input[$key]) : $default;
    }
    return $output;
}

add_action('admin_menu', function() {
    add_options_page(
        'Miramedia Event Manager',
        'TEDx Event Manager',
        'manage_options',
        'mmevmt-settings',
        'mmevmt_render_settings_page'
    );
});

add_action('admin_init', function() {
    register_setting('mmevmt_settings_group', 'mmevmt_labels', array(
        'sanitize_callback' => 'mmevmt_sanitize_labels',
        'default'           => mmevmt_get_label_defaults(),
    ));

    add_settings_section(
        'mmevmt_labels_section',
        'Text Labels',
        function() {
            echo '<p>Customize the text shown on the front end.</p>';
        },
        'mmevmt-settings'
    );

    $fields = array(
        'talks_heading'        => 'Talks section heading (Person page)',
        'speaker_heading'      => 'Speaker heading ([tedx_speaker] shortcode)',
        'no_people_message'    => '"No People found" message',
        'no_talks_message'     => '"No talks found" message',
        'no_companies_message' => '"No companies found" message',
    );

    foreach ($fields as $key => $label) {
        add_settings_field(
            $key,
            $label,
            function() use ($key) {
                echo '<input type="text" name="mmevmt_labels[' . esc_attr($key) . ']" value="' . esc_attr(mmevmt_get_label($key)) . '" class="regular-text">';
            },
            'mmevmt-settings',
            'mmevmt_labels_section'
        );
    }
});

function mmevmt_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Miramedia Event Manager Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('mmevmt_settings_group');
            do_settings_sections('mmevmt-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

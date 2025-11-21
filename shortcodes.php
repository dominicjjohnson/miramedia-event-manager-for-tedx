<?php
function tedx_youtube_shortcode($atts) {
    $youtube_link = get_post_meta(get_the_ID(), 'youtube_link', true);
    $output = '<!-- Must be the list page - No YouTube link found. -->';

    if ($youtube_link) {
        // Convert regular YouTube URL to an embed link.
        $youtube_link = preg_replace("/watch\?v=/", "embed/", $youtube_link);

        $output = '<div style="display: flex; justify-content: center; align-items: center; width: 100%;">';
        $output .= '<div style="position: relative; width: 100%; max-width: 854px; aspect-ratio: 16 / 9;">';
        $output .= '<iframe ';
        $output .= 'src="' . esc_url($youtube_link) . '" ';
        $output .= 'title="YouTube video player" ';
        $output .= 'frameborder="0" ';
        $output .= 'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" ';
        $output .= 'referrerpolicy="strict-origin-when-cross-origin" ';
        $output .= 'allowfullscreen ';
        $output .= 'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">';
        $output .= '</iframe>';
        $output .= '</div>';
        $output .= '</div>';
    }

    return $output;    
}
add_shortcode('tedx_youtube', 'tedx_youtube_shortcode');


function tedx_speaker_shortcode($atts) {
    // Get the current talk post ID
    $talk_post_id = get_the_ID();

    // Get the person_link (assumed to be a post ID or URL) from the talk post
    $person_link = get_post_meta($talk_post_id, 'person_link', true);

    // Try to get speaker info from person_link if it's a valid post ID
    if (!empty($person_link) && is_numeric($person_link) && get_post_status($person_link)) {
        $speaker_name = get_the_title($person_link);
        $company_name = get_post_meta($person_link, 'company_name', true);
        $job_title = get_post_meta($person_link, 'job_title', true);
        $speaker_image_id = get_post_thumbnail_id($person_link);
        $speaker_image = $speaker_image_id ? wp_get_attachment_url($speaker_image_id) : '';
    } else {
       //return 'No speaker information found.';
        return "";
    }

    // Prevent wpautop from adding <p> tags
    remove_filter('the_content', 'wpautop');
    remove_filter('the_excerpt', 'wpautop');

    $output = '<div class="tedx-speaker" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 8px; width: 100%;">';

    // Add SPEAKER title
    $output .= '<h3 style="margin-bottom: 8px;">SPEAKER:</h3>';

    // Link to the details page
    $person_url = get_permalink($person_link);

    if (!empty($speaker_image)) {
        $output .= '<a href="' . esc_url($person_url) . '" style="display:inline-block; margin-bottom: 0;"><img class="tedx-speaker-image" src="' . esc_url($speaker_image) . '" alt="' . esc_attr($speaker_name) . '" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom: 0;"></a>';
    }

    $output .= '<div class="tedx-speaker-details" style="display: flex; flex-direction: column; align-items: center;">';

    if (!empty($speaker_name)) {
        $output .= '<a href="' . esc_url($person_url) . '" style="font-weight: bold; font-size: 1.3em; color: inherit; text-decoration: none; margin-top: 8px; margin-bottom: 6px;">' . esc_html($speaker_name) . '</a>';
    }

    if (!empty($company_name)) {
        $output .= '<span style="margin-bottom: 3px;">' . esc_html($company_name) . '</span>';
    }

    if (!empty($job_title)) {
        $output .= '<span style="font-style: italic; color: #555;">' . esc_html($job_title) . '</span>';
    }
    $output .= '</div></div>';

    return $output;
}
add_shortcode('tedx_speaker', 'tedx_speaker_shortcode');


?>
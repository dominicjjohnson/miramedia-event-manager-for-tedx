<?php
function tedx_youtube_shortcode($atts) {
    $youtube_link = get_post_meta(get_the_ID(), 'youtube_link', true);
    $output = 'No YouTube link found.';

    if ($youtube_link) {
        // Convert regular YouTube URL to an embed link.
        $youtube_link = preg_replace("/watch\?v=/", "embed/", $youtube_link);

        $output = <<<HTML
        <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
            <div style="position: relative; width: 100%; max-width: 854px; aspect-ratio: 16 / 9;">
            <iframe 
                src="{$youtube_link}" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
            ></iframe>
            </div>
        </div>
        HTML;
    }

    return $output;    
}
add_shortcode('tedx_youtube', 'tedx_youtube_shortcode');


function tedx_speaker_shortcode($atts) {
    // Get the current talk post ID
    $talk_post_id = get_the_ID();


    $talk_post_id = 1108;

    // Get the person_link (assumed to be a post ID or URL) from the talk post
    $person_link = get_post_meta($talk_post_id, 'person_link', true);

    // Try to get speaker info from person_link if it's a valid post ID
    if (!empty($person_link) && is_numeric($person_link) && get_post_status($person_link)) {
        $speaker_name = get_post_meta($person_link, 'speaker_name', true);
        $speaker_bio = get_post_meta($person_link, 'speaker_bio', true);
        $speaker_image = get_post_meta($person_link, 'speaker_image', true);
    } else {
        // Fallback: use current post meta if person_link is not set or not numeric
        $speaker_name = get_post_meta($talk_post_id, 'speaker_name', true);
        $speaker_bio = get_post_meta($talk_post_id, 'speaker_bio', true);
        $speaker_image = get_post_meta($talk_post_id, 'speaker_image', true);
    }

    // Debugging: Uncomment to check values
     error_log("person_link: " . print_r($person_link, true));
     error_log("speaker_name: " . print_r($speaker_name, true));
     error_log("speaker_bio: " . print_r($speaker_bio, true));
     error_log("speaker_image: " . print_r($speaker_image, true));

    if (empty($speaker_name) && empty($speaker_bio) && empty($speaker_image)) {
        return 'No speaker information found.';
    }

    $output = '<div class="tedx-speaker">';
    
    if (!empty($speaker_image)) {
        $output .= '<img src="' . esc_url($speaker_image) . '" alt="' . esc_attr($speaker_name) . '" style="max-width: 100%; height: auto;">';
    }
    
    if (!empty($speaker_name)) {
        $output .= '<h3>' . esc_html($speaker_name) . '</h3>';
    }
    
    if (!empty($speaker_bio)) {
        $output .= '<p>' . esc_html($speaker_bio) . '</p>';
    }
    
    $output .= '</div>';

    return $output;
}
add_shortcode('tedx_speaker', 'tedx_speaker_shortcode');


?>
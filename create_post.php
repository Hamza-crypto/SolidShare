<?php

// Function to create a post or page.
function create_post_or_page($data)
{
    $title = sanitize_text_field($data['title']);
    $content = sanitize_text_field($data['content']);
    $type = isset($data['post_type']) ? sanitize_text_field($data['post_type']) : 'post'; // Set default to 'page' if not provided
    $meta = isset($data['meta']) ? $data['meta'] : array();

    $token_validation_result = validate_bearer_token();

    if (is_wp_error($token_validation_result)) {
        return $token_validation_result;
    }

    $post_data = array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_type'    => $type,
        'post_status'  => 'publish',
    );

    $post_id = wp_insert_post($post_data, true);

    if (!is_wp_error($post_id)) {
        // Save post meta data if post creation is successful.
        foreach ($meta as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }

        return new WP_REST_Response(array( $type . '_id' => $post_id), 200);
    } else {
        return new WP_Error('post_creation_failed', __('Post creation failed.'), array('status' => 500));
    }
}
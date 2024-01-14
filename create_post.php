<?php

// Function to create a post or page.
function solidshare_post_or_page($data)
{
    $title = sanitize_text_field($data['title']);
    $content = sanitize_text_field($data['content']);
    $type = isset($data['post_type']) ? sanitize_text_field($data['post_type']) : 'post'; // Set default to 'page' if not provided
    $meta = isset($data['meta']) ? $data['meta'] : array();
    $template = isset($data['page_template']) ? sanitize_text_field($data['page_template']) : 'template-parts/viewer.php'; 

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

     // Add page template to the post data if it's provided
    if ($type === 'page' && !empty($template)) {
        $post_data['page_template'] = $template;
    }

    $post_id = wp_insert_post($post_data, true);

    if (!is_wp_error($post_id)) {
        // Save post meta data if post creation is successful.
        foreach ($meta as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }

        $data = [
            $type . '_id' => $post_id,
        ];
        return new WP_REST_Response($data, 200);
    } else {
        $error_message = $post_id->get_error_message();
        // Log or output the error message for debugging
        error_log('Post Creation Error: ' . $error_message);
        return new WP_Error('post_creation_failed', __($error_message), array('status' => 500));
    }
}
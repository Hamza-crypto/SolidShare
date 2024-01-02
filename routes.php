<?php

function register_solidshare_custom_endpoint() {
    register_rest_route('custom/v1', '/authenticate/', array(
        'methods' => 'POST',
        'callback' => 'custom_authenticate_user',
        'permission_callback' => function() { return ''; },
        'args' => array(
            'email' => array(
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
            'password' => array(
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
        ),
    ));

    register_rest_route('custom/v1', '/create-post/', array(
        'methods' => 'POST',
        'callback' => 'create_post_or_page',
        'permission_callback' => function() { return ''; },
        'args' => array(
            'title' => array(
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
            'content' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
            'type' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return in_array($param, array('post', 'page'));
                },
            ),
        ),
    ));

    register_rest_route('custom/v1', '/file-upload/', array(
        'methods' => 'POST',
        'callback' => 'handle_file_upload',
        'permission_callback' => function() { return ''; },
        'args' => array(
            'id' => array(
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                },
            ),
        ),
    ));
}
add_action('rest_api_init', 'register_solidshare_custom_endpoint');
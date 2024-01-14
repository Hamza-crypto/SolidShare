<?php

function register_solidshare_endpoints() {
    register_rest_route('solidshare/v1', '/register/', array(
        'methods' => 'POST',
        'callback' => 'solidshare_register_user',
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
    
    register_rest_route('solidshare/v1', '/authenticate/', array(
        'methods' => 'POST',
        'callback' => 'solidshare_authenticate_user',
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

    register_rest_route('solidshare/v1', '/create-post/', array(
        'methods' => 'POST',
        'callback' => 'solidshare_post_or_page',
        'permission_callback' => 'verify_user_verification_status',
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

    register_rest_route('solidshare/v1', '/file-upload/', array(
        'methods' => 'POST',
        'callback' => 'solidshare_handle_file_upload',
       'permission_callback' => 'verify_user_verification_status',
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
add_action('rest_api_init', 'register_solidshare_endpoints');
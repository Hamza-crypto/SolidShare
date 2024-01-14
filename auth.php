<?php

// Function to handle authentication and generate an Application Password.
function solidshare_authenticate_user($data)
{
    $email = sanitize_text_field($data['email']);
    $password = sanitize_text_field($data['password']);

    $user = wp_authenticate($email, $password);

    if (!is_wp_error($user) && $user->ID > 0) {
        $user_id = $user->ID;
        $app_name = "SOLID_SHARE_DESKTOP_CLIENT";

        WP_Application_Passwords::delete_all_application_passwords($user_id);
        $app_password = WP_Application_Passwords::create_new_application_password($user_id, array( 'name' => $app_name ));

        return new WP_REST_Response(array('app_password' => $app_password[0]), 200);

    } else {
        // Authentication failed.
        return new WP_Error('authentication_failed', __('Authentication failed. Invalid email or password.'), array('status' => 401));
    }
}

function validate_bearer_token() {
    $headers = getallheaders();

    if (isset($headers['Authorization'])) {
        $token_parts = explode(' ', $headers['Authorization']);

        if (count($token_parts) !== 2 || $token_parts[0] !== 'Basic') {
            return new WP_Error('invalid_token', __('Invalid Bearer token.'), array('status' => 401));
        }

        $token = base64_decode($token_parts[1]);
        list($username, $password) = explode(':', $token);
        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            return new WP_Error('invalid_token', __('Invalid Bearer token.'), array('status' => 401));
        }
    } else {
        return new WP_Error('token_missing', __('Bearer token is missing.'), array('status' => 401));
    }
}

function verify_user_verification_status() {
    $user_id = get_current_user_id();

    // Check the user's verification status (adjust meta key as needed)
    $is_verified = get_user_meta($user_id, 'is_verified', true);

    return $is_verified === '1';
}
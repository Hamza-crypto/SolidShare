<?php

function solidshare_register_user($data)
{
    $email = sanitize_email($data['email']);
    $password = $data['password'];

    // Your logic to create a user account here
    $user_id = wp_create_user($email, $password, $email);

    if (!is_wp_error($user_id)) {
        // Generate a verification token and save it
        $verification_token = md5(uniqid());
        update_user_meta($user_id, 'verification_token', $verification_token);

        // Send verification email
        $verification_link = site_url("/verify-account/?token=$verification_token&email=$email");
        $subject = 'Verify Your Account';

        // Load the email template content
        $template_path = get_stylesheet_directory() . '/email-templates/verification-email-template.html';
        $message = file_get_contents($template_path);

        // Replace placeholders in the template
        $message = str_replace('%verification_link%', $verification_link, $message);

        $site_logo_id = get_theme_mod('custom_logo'); // Assuming 'custom_logo' is the setting ID
        $logo = wp_get_attachment_image_src($site_logo_id, 'full');

        if (has_custom_logo()) {
            $logo =  esc_url($logo[0]);
        } else {
            $logo = '<h1>' . get_bloginfo('name') . '</h1>';
        }

        $message = str_replace('%YOUR_LOGO%', $logo, $message);

        // Set the content type to HTML
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($email, $subject, $message, $headers);

        return array('status' => 'success', 'message' => 'User registered successfully. Please check your email for verification.');
    } else {
        return array('status' => 'error', 'message' => $user_id->get_error_message());
    }
}


function solidshare_verify_account()
{
    if (isset($_GET['token']) && isset($_GET['email'])) {
        $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
        $email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';

        // Your logic to verify the token and activate the account
        $saved_token = get_user_meta(get_user_by('email', $email)->ID, 'verification_token', true);

        if ($saved_token === $token) {
            // Activate user account
            $user_id = get_user_by('email', $email)->ID;
            
            update_user_meta($user_id, 'is_verified', '1');
            delete_user_meta($user_id, 'verification_token');

            // Redirect the user to a success page
            wp_redirect(home_url());
            exit;
        }
    }

}
add_action('init', 'solidshare_verify_account');
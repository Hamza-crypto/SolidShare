<?php

function solidshare_handle_file_upload($data)
{
    $post_id = $data['id'];

    if (!empty($_FILES['file'])) {
        $file = $_FILES['file'];

        // Get the maximum allowed file size from admin settings (in megabytes)
        $max_allowed_size = get_option('custom_upload_limits_size', 10); // Adjust the option name as needed

        // Check if the file size exceeds the maximum allowed size
        if ($file['size'] > $max_allowed_size * 1024 * 1024) {
            $data = [
                'status' => 'error',
                'message' => sprintf("File size exceeds the maximum allowed size of %d MB.", $max_allowed_size),
                'code' => 400
            ];
            return new WP_REST_Response($data);
        }
        
        $attachment_id = handle_uploaded_file($file, $post_id);

        if ($attachment_id) {
            $data = [
            'status' => 'success',
            'message' => sprintf("File uploaded successfully."),
            'attachment_id' => $attachment_id
        ];
            return new WP_REST_Response($data);
        } else {
            $data = [
            'status' => 'error',
            'message' => sprintf("File upload failed."),
            'code' => 500
        ];
            return new WP_REST_Response($data);
        }
    } else {
        return new WP_Error('missing_file', __('File is missing in the request.'), array('status' => 400));
    }
}

function handle_uploaded_file($file, $post_id)
{
    $upload_dir = wp_upload_dir();
    $target_dir = trailingslashit($upload_dir['basedir']) . 'custom-files/';

    if (!file_exists($target_dir)) {
        wp_mkdir_p($target_dir);
    }
    //
    $file_name = wp_unique_filename($target_dir, $file['name']);
    $target_path = $target_dir . $file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $attachment = array(
            'post_title' => $file['name'],
            'post_parent' => $post_id,
            'post_mime_type' => $file['type'],
            'guid' => $upload_dir['baseurl'] . '/custom-files/' . $file_name,
            'post_type' => 'attachment',
        );

        $attachment_id = wp_insert_attachment($attachment, $target_path, $post_id);
        if (!is_wp_error($attachment_id)) {
            return $attachment_id;
        }
    }

    return false;
}
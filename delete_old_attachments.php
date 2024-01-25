<?php


//Activation hook
register_activation_hook( __FILE__, 'activate_custom_cron' );
function activate_custom_cron() {
    // Schedule the cron job when the plugin is activated for the first time
    if ( ! wp_next_scheduled( 'delete_old_attachments_event' ) ) {
        // wp_schedule_event( time(), 'daily', 'delete_old_attachments_event' );
        wp_schedule_event( time(), 'every_minute', 'delete_old_attachments_event' );
    }
}

// Deactivation hook
register_deactivation_hook( __FILE__, 'deactivate_custom_cron' );
function deactivate_custom_cron() {
    // Clear the scheduled cron job when the plugin is deactivated
    wp_clear_scheduled_hook( 'delete_old_attachments_event' );
}

// Function to delete old attachments
function delete_old_attachments_cron() {

    global $wpdb;

    $one_minute_ago = date('Y-m-d H:i:s', strtotime('-1 minute'));

    // Construct the SQL query (without preparing it)
    $sql_query = "
        DELETE FROM $wpdb->posts
        WHERE post_type = 'attachment' AND post_date < '$one_minute_ago'
    ";
    
    $log_message = "SQL Query: $sql_query\n";
    $log_file = WP_CONTENT_DIR . '/delete_old_attachments_log.txt';

    // Execute the SQL query
    $res = $wpdb->query($sql_query);
    $log_message .= " " . $res;
    file_put_contents($log_file, $log_message, FILE_APPEND);

}

// Hook the delete_old_attachments_cron function to the scheduled event
add_action( 'delete_old_attachments_event', 'delete_old_attachments_cron' );

// Register custom cron intervals
add_filter( 'cron_schedules', 'custom_cron_intervals' );
function custom_cron_intervals( $schedules ) {
    // Add a custom interval for every minute
    $schedules['every_minute'] = array(
        'interval' => 20, // 60 seconds, so every minute
        'display'  => __( 'Every Minute' ),
    );
    return $schedules;
}
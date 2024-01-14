<?php
/*
Plugin Name: Solid Share API
Description: It handles api authentication
Version: 1.0
Author: Hamza Siddique
*/


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

require "init.php";

require "routes.php";

require "auth.php";

require "create_post.php";

require "file_upload.php";

include_once __DIR__ . "/disable_right_click/disable-right-click-for-wp.php";
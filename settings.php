<?php
    function add_page() {
        add_options_page('StayApp', 'StayApp', 'manage_options', 'stay_settings', 'component_option_page');
    }

    function component_option_page(){
        require_once plugin_dir_path(__FILE__) . "/includes/views/html-admin-page.php";
    }
    add_action('admin_menu', 'add_page');

    function call_someClass()
    {
        require_once plugin_dir_path(__FILE__) . "/includes/class-stay-tabs.php";
        return new someClass();
    }
    add_action( 'load-post.php', 'call_someClass' );
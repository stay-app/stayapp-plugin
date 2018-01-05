<?php
    function add_page() {
        add_options_page('StayApp', 'StayApp', 'manage_options', 'stayapp', 'component_option_page');
    }

    function component_option_page(){
        require_once plugin_dir_path(__FILE__) . "/includes/settings-form.php";
    }

    function register_settings(){
        register_settings('stayapp', 'stay_settings');
    }

    add_action( 'admin_menu', 'add_page' );
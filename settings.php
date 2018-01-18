<?php
    function add_page() {
        add_options_page('StayApp', 'StayApp', 'manage_options', 'stay_settings', 'component_option_page');
    }

    /**
     * REQUIRE TEMPALTE
     */
    function component_option_page(){
        // GET TICKETS
        $integration = new SA_Integration(get_option('stayapp_token'));
        global $tickets;
        $tickets = json_decode($integration->getTickets(), false);

        // GET CONDITIONS
        global $wpdb;
        global $conditions;

        $conditions = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}stayapp_conditions"
        );

        require_once plugin_dir_path(__FILE__) . "/includes/views/html-admin-page.php";
    }
    add_action('admin_menu', 'add_page');

    /**
     * REGISTER JS
     */
    function themeslug_enqueue_script() {
        wp_enqueue_script('maskedinput', plugin_dir_url( __FILE__ ) . 'includes/assets/js/jquery.maskedinput.js', array( 'jquery' ), '1.14.13', false );
        wp_enqueue_script('stayapp', plugin_dir_url( __FILE__ ) . 'includes/assets/js/stayapp.js', false );
    }
    add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_script', 0);
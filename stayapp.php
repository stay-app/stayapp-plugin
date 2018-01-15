<?php
    /*
        Plugin Name: StayApp
        Author: StaypApp
        Description: Plugin para integrar as vendas efetuadas pelo woocommerce no StayApp
        Version: 1.0.0
        Author URI: http://stapapp.com.br
    */

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }

    require_once plugin_dir_path(__FILE__) . "/includes/class-stay-integration.php";
    require_once plugin_dir_path(__FILE__) . "/settings.php";

    /**
     * WOOCOMMERCE PLUGIN NOT EXISTS
     */
    function woocommerce_not_exists_notice() {
        echo '<div class="error"><p>' . sprintf( __( 'Para o bom funcionamento do plugin <b>StayApp</b>, o plugin %s é obrigatório!', '' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>';
    }

    /**
     * WOOCOMMERCE PLUGIN NOT EXISTS
     */
    function token_account_not_exists_notice() {
        echo '<div class="error"><p>' . sprintf( __( 'Para o bom funcionamento do plugin <b>StayApp</b>, é necessário inserir o token da sua conta!', '' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>';
    }

    /**
     * LOAD FUNCTIONS
     */
    add_action( 'plugins_loaded', 'wslwoo_load', 0 );
    function wslwoo_load() {
        // Checks if WooCommerce is installed.
        if ( ! class_exists( 'Woocommerce' ) ) {
            add_action( 'admin_notices', 'woocommerce_not_exists_notice' );
            add_action( 'admin_notices', 'token_account_not_exists_notice' );
            return;
        }
    }

    /**
     * Button Ajax validate Token
     */
    add_action( 'admin_footer', 'action_validate_token' );
    function action_validate_token() { ?>
        <script type="text/javascript" >
            jQuery(document).ready(function($) {



                jQuery("#validationtoken").click(function(e){
                    e.preventDefault();
                    var token = jQuery("#token").val();

                    var data = {
                        'action': 'validate_token',
                        'token': token
                    };

                    jQuery.post(ajaxurl, data, function(tickets) {
                        var ticketsObject = JSON.parse(tickets);
                        console.log('Got this from the server: ', ticketsObject);
                    });
                });
            });
        </script>
        <?php
    }

    /**
     * Validate Token
     */
    add_action( 'wp_ajax_validate_token', 'validate_token' );
    function validate_token() {
        global $wpdb;
        $data = (object) $_POST;
        $integration = new SA_Integration($data->token);
        $tickets = json_decode($integration->getTickets(), false);

        $newStamps = [];
        foreach ($tickets as $key => $ticket){
            //$ticket['ticket_id'] = $key;
            array_push($newStamps, $ticket);
        }

        echo json_encode(["length" => count($newStamps)]);
        die;
    }
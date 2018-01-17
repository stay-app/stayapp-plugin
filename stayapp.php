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
                    var load = $("#load");
                    var button = $(this);
                    var checked = $("#checked");
                    var close = $("#close");

                    load.show();
                    button.attr('disabled', true);

                    var data = {
                        'action': 'validate_token',
                        'token': token
                    };

                    jQuery.post(ajaxurl, data, function(res) {
                        console.log("Response - ", res);
                        var response = JSON.parse(res);
                        if(response.status == true){
                            alert("Token validado com sucesso!");
                            button.removeAttr('disabled');
                            checked.show();
                            close.hide();
                        }else{
                            button.removeAttr('disabled');
                            alert("Erro ao validar token!");
                            checked.hide();
                            close.show();
                        }
                        //
                        load.hide();
                        console.log('Got this from the server: ', response);
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
        $data = (object) $_POST;
        $integration = new SA_Integration($data->token);
        $tickets = json_decode($integration->getTickets(), false);

        if(empty($data->token) || $tickets->error == "invalid-token"){
            delete_option('stayapp_token');
            echo json_encode(["status" => false, "error" => $tickets->error]);
        }else{
            add_option( 'stayapp_token', $data->token, '', 'yes' );
            echo json_encode(["status" => true, "error" => $tickets->error]);
        }
        die;
    }

    /**
     * Order Status Completed
     */
    function order_status_completed( $order_id ) {
        error_log("Order complete for order $order_id \n", 3, plugin_dir_path(__FILE__) . "orders.log");

        $order = wc_get_order($order_id);
        $order_data = $order->get_data();
        $order_total_price = $order_data['total'];
        $items = $order->get_items();

        error_log("=====================================================\n", 3, plugin_dir_path(__FILE__) . "errors.log");
        error_log(print_r( $order_data, true ) ."\n", 3, plugin_dir_path(__FILE__) . "orders.log");
        foreach ( $items as $item ) {
            $product_name = $item->get_name();
            $product_id = $item->get_product_id();
            $price = get_post_meta($product_id , '_price', true);
            error_log("[$order_id] Produto - " . $product_name . " Quantidade - " . $price . "\n", 3, plugin_dir_path(__FILE__) . "orders.log");
        }
        error_log("=====================================================\n", 3, plugin_dir_path(__FILE__) . "orders.log");
    }
    add_action( 'woocommerce_order_status_completed', 'order_status_completed', 10, 1 );
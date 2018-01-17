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
    function wslwoo_load() {
        // Checks if WooCommerce is installed.
        if ( ! class_exists( 'Woocommerce' ) ) {
            add_action( 'admin_notices', 'woocommerce_not_exists_notice' );
            return;
        }
        if(empty(get_option('stayapp_token'))){
            add_action( 'admin_notices', 'token_account_not_exists_notice' );
            return;
        }
    }
    add_action( 'plugins_loaded', 'wslwoo_load', 0 );

    /**
     * BUTTON AJAX VALIDATE TOKEN
     */
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
                            //alert("Token validado com sucesso!");
                            button.removeAttr('disabled');
                            checked.show();
                            close.hide();
                        }else{
                            button.removeAttr('disabled');
                            //alert("Erro ao validar token!");
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
    add_action( 'admin_footer', 'action_validate_token' );

    /**
     * VALIDATE TOKEN
     */
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
    add_action( 'wp_ajax_validate_token', 'validate_token' );

    /**
     * ORDER STATUS COMPLETED
     */
    function order_status_completed( $order_id ) {
        error_log("Order complete for order $order_id \n", 3, plugin_dir_path(__FILE__) . "orders.log");

        if(!function_exists('wc_get_order') || empty(get_option('stayapp_token')))
            return;

        $order = wc_get_order($order_id);
        $order_data = $order->get_data();
        $order_total_price = $order_data['total'];
        $items = $order->get_items();

        $integration = new SA_Integration(get_option('stayapp_token'));

        foreach ( $items as $item ) {
            $product_name = $item->get_name();
            $product_id = $item->get_product_id();
            $price = get_post_meta($product_id , '_price', true);
            error_log("[$order_id] PRODUTO - " . $product_name . " QUANTIDADE - " . $price . "\n", 3, plugin_dir_path(__FILE__) . "orders.log");
        }
        $number_stayapp = get_post_meta( $order_id, 'number_stayapp', true );

        $statusStay = $integration->addStamp([
            "number" => $number_stayapp,
            "amount" => "1",
            "ticket_id" => "-Kv2Y43Py5E5q4Yi1nYJ"
        ]);

        error_log("PHONE - $number_stayapp STATUS - $statusStay \n", 3, plugin_dir_path(__FILE__) . "orders.log");
    }
    add_action( 'woocommerce_order_status_completed', 'order_status_completed', 10, 1 );

    /**
     * ORDER STATUS COMPLETED
     */
    function my_custom_checkout_field( $checkout ) {
        echo '<div id="my_custom_checkout_field">
                <h2 style="color: #0480ff;font-weight: bold;">
                    <img src="' . plugin_dir_url( __FILE__ ) . 'includes/assets/images/stayapp.png" style="display: block;margin: 0px auto;width: 200px;">
                </h2>
                
                <p>Participe do nosso programa de fidelidade, informe seu número abaixo:</p>
        ';
        woocommerce_form_field( 'number_stayapp', array(
            'type'          => 'number',
            'class'         => array('my-field-class form-row-wide'),
            'label'         => __('Celular'),
            'placeholder'   => __('(99) 99999-9999'),
        ), $checkout->get_value( 'number_stayapp' ));

        echo '</div>';

    }
    add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );

    /**
     * CHECK FIELD NOT EMPTY
     */
    function number_stayapp_checkout_field_process() {
        // Check if set, if its not set add an error.
        if ( ! $_POST['number_stayapp'] )
            wc_add_notice( __( 'Preencha com um número de celular, para participar do programa de fidelidade.' ), 'error' );
    }
    add_action('woocommerce_checkout_process', 'number_stayapp_checkout_field_process');

    /**
     * UPDATE POST META
     */
    function number_stayapp_update_order_meta( $order_id ) {
        if ( ! empty( $_POST['number_stayapp'] ) ) {
            update_post_meta( $order_id, 'number_stayapp', sanitize_text_field( $_POST['number_stayapp'] ) );
        }
    }
    add_action( 'woocommerce_checkout_update_order_meta', 'number_stayapp_update_order_meta' );
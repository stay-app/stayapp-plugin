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
    require_once plugin_dir_path(__FILE__) . "/includes/class-stay-install.php";
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
     * REGISTER JS
     */
    function enqueue_script() {
        wp_enqueue_script('maskedinput', plugin_dir_url( __FILE__ ) . 'includes/assets/js/jquery.maskedinput.js', array( 'jquery' ), '1.14.13', false );
        wp_enqueue_script('stayapp', plugin_dir_url( __FILE__ ) . 'includes/assets/js/stayapp.js', false );
    }
    add_action( 'admin_footer', 'enqueue_script', 0);

    /**
     * BUTTON AJAX VALIDATE TOKEN
     */
    function action_validate_token() { ?>
        <script type="text/javascript" >
            jQuery(document).ready(function($) {

                //CKECK TYPE CONDITION
                jQuery("input[name=type_condition]").click(function(e){
                    var $this = $(this);
                    if($this.val() == "product_selected"){
                        $("select[name=products]").attr('disabled',false);
                        $("input[id=stamp_by_item]").attr('disabled',false);
                    }else{
                        $("select[name=products]").attr("disabled", "disabled");
                        $("input[id=stamp_by_item]").attr("disabled", "disabled");
                    }

                    if($this.val() == "quantity_cart"){
                        $("input[name=value]").attr('disabled',false);
                    }else{
                        $("input[name=value]").attr("disabled", "disabled");
                    }
                });

                // VERIFU TYPE PROMO
                $("select[name=promo]").change(function(element) {
                    var type = element.target.selectedOptions["0"].attributes[1].nodeValue;
                    if(type == 'PERCENT'){
                        $("#value_condition").hide();
                        $("#value_condition").find("input").attr("disabled", "disabled");
                    }else{
                        $("#value_condition").show();
                        $("#value_condition").find("input").attr('disabled',false);
                    }

                });


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
                        window.location.reload();
                        //
                        load.hide();
                        console.log('Got this from the server: ', response);
                    });
                });


                jQuery("#sendcondition").submit(function(e){
                    e.preventDefault();

                    var data = {
                        'action': 'add_condition',
                        'values': $(this).serialize()
                    };

                    jQuery.post(ajaxurl, data, function(res) {
                        var data = JSON.parse(res);
                        console.log("Response - ", data);

                        window.location.reload();
                    });
                });


                jQuery(".destroy").click(function(e){
                    e.preventDefault();
                    var id = $(this).data('id');
                    console.log(id);
                    var data = {
                        'action': 'remove_condition',
                        'id': id
                    };

                    jQuery.post(ajaxurl, data, function(res) {
                        var data = JSON.parse(res);
                        console.log("Response - ", data);

                        window.location.reload();
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
     * VALIDATE TOKEN
     */
    function remove_condition() {
        $data = (object) $_POST;
        global $wpdb;
        $wpdb->delete( $wpdb->prefix . 'stayapp_conditions', array( 'id' => $data->id ) );
        echo json_encode($data);
        die;
    }
    add_action( 'wp_ajax_remove_condition', 'remove_condition' );



    /**
     * ADD CONDITION
     */
    function add_condition() {
        parse_str($_POST['values'], $data);
        global $wpdb;
        if(!empty($data['value'])){
            $price = str_replace(",", ".", str_replace(".", "", $data['value']));
        }
        // Verify type condition
        switch ($data['type_condition']){
            case "quantity_cart":
                $results = $wpdb->insert(
                    $wpdb->prefix . 'stayapp_conditions',
                    array(
                        'condition_value' => $data['type_condition'],
                        'stamp_sender' => $data['quantity_stamp'],
                        'buy_value' => (isset($price) ? $price : null),
                        'ticket_id' => $data['promo']
                    ),
                    array(
                        '%s',
                        '%d',
                        '%f',
                        '%s'
                    )
                );
                break;
            case "product_selected":
                $results = $wpdb->insert(
                    $wpdb->prefix . 'stayapp_conditions',
                    array(
                        'condition_value' => $data['type_condition'],
                        'product_id' => $data['products'],
                        'ticket_id' => $data['promo'],
                        'stamp_sender' => $data['quantity_stamp'],
                        'stamp_by_item' => (($data['stamp_by_item'] && !empty($data['stamp_by_item'])) ? true : false)
                    ),
                    array(
                        '%s',
                        '%d',
                        '%s',
                        '%d',
                        '%d'
                    )
                );
                break;
            case "always":
                $results = $wpdb->insert(
                    $wpdb->prefix . 'stayapp_conditions',
                    array(
                        'condition_value' => $data['type_condition'],
                        'ticket_id' => $data['promo'],
                        'stamp_sender' => $data['quantity_stamp']
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d'
                    )
                );
                break;
        }

        echo json_encode($results);
        die;
    }
    add_action( 'wp_ajax_add_condition', 'add_condition' );

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
        $number_stayapp = get_post_meta( $order_id, 'number_stayapp', true );

        //$wpdb->prefix . 'stayapp_conditions'

        global $wpdb;
        global $conditions;

        $conditions = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}stayapp_conditions"
        );

        if(!empty($conditions)){
            error_log("-- Condition exists --\n", 3, plugin_dir_path(__FILE__) . "orders.log");
            $ticket = $integration->getTickets();
            foreach ($conditions as $condition){
                if($condition->condition_value == 'quantity_cart'){
                    if($order_total_price >= $condition->buy_value){
                        if($ticket[$condition->ticket_id]['stamp_type'] == 'PERCENT'){
                            $statusStay = $integration->addStamp([
                                "number" => adjustPhoneNumber($number_stayapp),
                                "amount" => $order_total_price,
                                "buy_value" => $order_total_price,
                                "ticket_id" => $condition->ticket_id
                            ]);
                            error_log("PHONE - $number_stayapp STATUS - $statusStay \n", 3, plugin_dir_path(__FILE__) . "orders.log");
                        }else{
                            $statusStay = $integration->addStamp([
                                "number" => adjustPhoneNumber($number_stayapp),
                                "amount" => $condition->stamp_sender,
                                "ticket_id" => $condition->ticket_id
                            ]);
                            error_log("PHONE - $number_stayapp STATUS - $statusStay \n", 3, plugin_dir_path(__FILE__) . "orders.log");
                        }
                    }
                }elseif($condition->condition_value == 'product_selected'){
                    foreach ( $items as $item ) {
                        $product_name = $item->get_name();
                        $product_id = $item->get_product_id();
                        $price = get_post_meta($product_id , '_price', true);
                        if($condition->product_id == $product_id){
                            if($ticket[$condition->ticket_id]['stamp_type'] == 'PERCENT'){
                                $statusStay = $integration->addStamp([
                                    "number" => adjustPhoneNumber($number_stayapp),
                                    "amount" => $order_total_price,
                                    "buy_value" => $order_total_price,
                                    "ticket_id" => $condition->ticket_id
                                ]);
                                error_log("PHONE - $number_stayapp STATUS - $statusStay \n", 3, plugin_dir_path(__FILE__) . "orders.log");
                            }else{
                                $statusStay = $integration->addStamp([
                                    "number" => adjustPhoneNumber($number_stayapp),
                                    "amount" => $condition->stamp_sender,
                                    "ticket_id" => $condition->ticket_id
                                ]);
                                error_log("PHONE - $number_stayapp STATUS - $statusStay \n", 3, plugin_dir_path(__FILE__) . "orders.log");
                            }
                            if($condition->stamp_by_item && $condition->stamp_by_item == false){
                                break 1;
                            }
                        }
                    }
                }elseif($condition->condition_value == 'always'){
                    if($ticket[$condition->ticket_id]['stamp_type'] == 'PERCENT'){
                        $statusStay = $integration->addStamp([
                            "number" => adjustPhoneNumber($number_stayapp),
                            "amount" => $order_total_price,
                            "buy_value" => $order_total_price,
                            "ticket_id" => $condition->ticket_id
                        ]);
                        error_log("PHONE - $number_stayapp STATUS - $statusStay \n", 3, plugin_dir_path(__FILE__) . "orders.log");
                    }else{
                        $statusStay = $integration->addStamp([
                            "number" => adjustPhoneNumber($number_stayapp),
                            "amount" => $condition->stamp_sender,
                            "ticket_id" => $condition->ticket_id
                        ]);
                        error_log("PHONE - $number_stayapp STATUS - $statusStay \n", 3, plugin_dir_path(__FILE__) . "orders.log");
                    }
                }
            }
        }
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
            'type'          => 'tel',
            'class'         => array('number_stayapp form-row-wide'),
            'label'         => __('Celular <abbr class="required" title="obrigatório">*</abbr>'),
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

    /**
     * ADJUST PHONE NUMBER
     */
    function adjustPhoneNumber($phoneNumber) {
        if($phoneNumber == null) return;

        $phoneNumber = preg_replace('~\D~', "", $phoneNumber);
        if(strlen($phoneNumber) == 10) {
            return "55" . substr($phoneNumber, 0, 2) . "9" . substr($phoneNumber, 2);
        } else {
            return "55" . $phoneNumber;
        }
    }

    function stayapp_activate() {
        error_log("Plugin Ativado - " . date("d/m/Y") . "\n", 3, plugin_dir_path(__FILE__) . "orders.log");
        SA_Install::create_tables();
    }
    register_activation_hook( __FILE__, 'stayapp_activate' );
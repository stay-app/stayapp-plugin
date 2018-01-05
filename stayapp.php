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
            add_action( 'admin_notices', 'token_account_not_exists_notice' );
            return;
        }
    }
    add_action( 'plugins_loaded', 'wslwoo_load', 0 );
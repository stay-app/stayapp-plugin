<?php
    /*
        Plugin Name: StayApp Stamps
        Author: StaypApp
        Description: Plugin para integrar as vendas efetuadas pelo woocommerce no StayApp
        Version: 1.0
        Author URI: http://stapapp.com.br
    */



    function add_page() {
        add_menu_page( 'StayApp', 'StayApp', 'manage_options', 'stayapp', 'component_page', 'dashicons-awards', 6  );
    }
    add_action( 'admin_menu', 'add_page' );

    function component_page(){
        echo "<h1 class='wp-heading-inline'>StayApp Integrações</h1>";
    }
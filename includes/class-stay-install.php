<?php
/**
 * Installation
 *
 * @author   STAYAPP
 * @category Admin
 * @package  STAYAPP/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SA_Install
{
    private static function get_schema() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables = "
            CREATE TABLE IF NOT EXISTS {$wpdb->prefix}stayapp_conditions (
              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              ticket_id char(32),
              product_id BIGINT,
              buy_value DOUBLE,
              stamp_by_item BOOLEAN,
              condition_value char(32),
              stamp_sender BIGINT UNSIGNED,
              PRIMARY KEY  (id)
            ) $collate;
		";
        return $tables;
    }

    public static function create_tables() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $wpdb->hide_errors();
        dbDelta( self::get_schema() );
    }
}
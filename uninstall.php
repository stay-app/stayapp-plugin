<?php
    /**
    * StayApp Uninstall
    *
    * Uninstalling StayApp deletes logs stamps.
    *
    * @author      StayApp
    * @category    Core
    * @package     StayApp/Uninstaller
    * @version     1.0.0
    */

    if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit;
    }

    global $wpdb, $wp_version;
<?php
namespace MciControlShellyDevices\devices\ajax;

use MciControlShellyDevices\devices\models\MciscDevice;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscAjaxDeviceWpActive
{

    public function enqueue_wpactive_scripts()
    {

        wp_enqueue_script( 'mcisc_wpactive', MCISC_PLUGIN_URL . 'devices/js/mcisc_device_wpactive.js', array( 'jquery' ), MCISC_VERSION, false );

        wp_localize_script( 'mcisc_wpactive', 'mcisc_wpactive', [
            'ajax_url_wpactive' => admin_url( 'admin-ajax.php' ),
            'wpactive_nonce'    => wp_create_nonce( 'mcisc_wpactive' ),
        ] );

    }

    public function wp_active_save()
    {

        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'mcisc_wpactive' ) ) {
            wp_send_json( 'Security check failed.', 403 );
        }
        if ( !is_admin() || !current_user_can( 'manage_options' ) ) {
            wp_send_json( 'Not authorized.', 403 );
        }

        $device_id = isset( $_POST['device_id'] ) ? sanitize_text_field( $_POST['device_id'] ) : null;
        $wp_active = isset( $_POST['wp_active'] ) ? sanitize_text_field( $_POST['wp_active'] ) : null;

        $device                   = new MciscDevice();
        $device_data              = $device->get( $device_id );
        $device_data['wp_active'] = $wp_active;

        $save = $device->save( $device_data['account_id'], $device_data );

        wp_send_json( $device_data['wp_active'], 200 );

    }

    public function init()
    {
        if ( is_admin() && current_user_can( 'manage_options' ) ) {

            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_wpactive_scripts'] );
            add_action( 'wp_ajax_mcisc_device_wpactive', [$this, 'wp_active_save'] );

        }
    }

}
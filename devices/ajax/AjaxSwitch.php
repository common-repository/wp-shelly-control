<?php
namespace MciControlShellyDevices\devices\ajax;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\devices\controllers\MciscSwitchDevice;

class MciscAjaxSwitch
{

    public function enqueue_switch_scripts()
    {

        wp_enqueue_script( 'mcisc_switch', MCISC_PLUGIN_URL . 'devices/js/mcisc_switch.js', array( 'jquery' ), MCISC_VERSION, false );

        wp_localize_script( 'mcisc_switch', 'mcisc_switch', [
            'ajax_url'   => admin_url( 'admin-ajax.php' ),
            'nonce'      => wp_create_nonce( 'mcisc_switch' ),
            'plugin_url' => MCISC_PLUGIN_URL,
        ] );

    }

    public function device_switch()
    {
        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'mcisc_switch' ) ) {
            wp_send_json( 'Security check failed.', 403 );
        }

        $device_id  = isset( $_POST['device_id'] ) ? sanitize_text_field( $_POST['device_id'] ) : null;
        $old_status = isset( $_POST['old_status'] ) ? sanitize_text_field( $_POST['old_status'] ) : null;

        $max_attempts  = 20;
        $switch_errors = 0;

        do {

            $switch_device = new MciscSwitchDevice( $device_id, $old_status );
            $switch        = $switch_device->init();

            if ( $switch != true && $switch_errors < $max_attempts ) {
                $switch_errors++;
                sleep( 0.6 );
            } else {
                if ( $switch_errors >= $max_attempts ) {
                    $switch = false;
                }
                break;
            }

        } while ( $switch != true );

        wp_send_json( $switch, 200 );

    }

    public function init()
    {
        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_switch_scripts'] );
            add_action( 'wp_ajax_mcisc_switch', [$this, 'device_switch'] );
        } else {
            add_action( 'wp_enqueue_scripts', [$this, 'enqueue_switch_scripts'] );
            add_action( 'wp_ajax_nopriv_mcisc_switch', [$this, 'device_switch'] );
        }
    }
}
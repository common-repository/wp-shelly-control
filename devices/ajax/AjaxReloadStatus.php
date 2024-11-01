<?php
namespace MciControlShellyDevices\devices\ajax;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\devices\controllers\MciscGetStatusDevice;
use MciControlShellyDevices\devices\models\MciscDevice;
use MciControlShellyDevices\options\models\MciscOption;

class MciscAjaxReloadStatus
{
    private $backpermissions;
    private $frontpermissions;
    private $shelly_control_page;

    public function __construct()
    {
        $this->shelly_control_page = isset( $_GET['page'] ) && $_GET['page'] == 'wp-shelly-control' ? true : false;

        $options                = new MciscOption();
        $this->backpermissions  = sanitize_text_field($options->get( 'backpermissions' ));
        $this->frontpermissions = sanitize_text_field($options->get( 'frontpermissions' ));
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script( 'mcisc_get_status', MCISC_PLUGIN_URL . 'devices/js/mcisc_get_status.js', array( 'jquery' ), MCISC_VERSION, false );

        wp_localize_script( 'mcisc_get_status', 'mcisc_get_status', [
            'ajax_url'            => admin_url( 'admin-ajax.php' ),
            'nonce'               => wp_create_nonce( 'mcisc_nonce_get_status' ),
            'shelly_control_page' => sanitize_text_field( $this->shelly_control_page ),
        ] );
    }

    public function reload_devices_status()
    {

        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'mcisc_nonce_get_status' ) ) {
            wp_send_json( 'Security check failed.', 403 );
        }

        $devices     = new MciscDevice();
        $all_devices = $devices->get_all();

        $all_ids = [];
        foreach ( $all_devices as $device ) {
            $all_ids[] = $device['id'];
        }

        $cloud_status_devices = [];

        foreach ( $all_ids as $id ) {
            $cloud_status_device = new MciscGetStatusDevice( $id );

            $cloud_status_devices[] = [
                'id'        => sanitize_text_field( $id ),
                'status'    => sanitize_text_field( $cloud_status_device->status ),
                'online'    => sanitize_text_field( $cloud_status_device->online ),
                'ison'      => sanitize_text_field( $cloud_status_device->ison ),
                'connected' => sanitize_text_field( $cloud_status_device->connected ),
                'wifi_sta'  => sanitize_text_field( $cloud_status_device->wifi_sta ),
                'power'     => sanitize_text_field( $cloud_status_device->power ),
            ];
        }

        wp_send_json( $cloud_status_devices, 200 );

    }

    public function init()
    {

        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );

        add_action( 'wp_ajax_mcisc_get_status', [$this, 'reload_devices_status'] );
        add_action( 'wp_ajax_nopriv_mcisc_get_status', [$this, 'reload_devices_status'] );

    }
}
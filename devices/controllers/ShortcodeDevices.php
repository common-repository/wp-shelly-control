<?php
namespace MciControlShellyDevices\devices\controllers;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\devices\controllers\MciscDevices;
use MciControlShellyDevices\options\models\MciscOption;

class MciscShortcodeDevices
{

    private $frontpermissions;
    private $backpermissions;
    private $display;

    public function __construct( $display )
    {
        $options                = new MciscOption();
        $this->frontpermissions = sanitize_text_field( $options->get( 'frontpermissions' ) );
        $this->backpermissions  = sanitize_text_field( $options->get( 'backpermissions' ) );
        $this->display          = $display;

        add_shortcode( 'mcisc_devices', [$this, 'print_devices_list'] );
    }

    public function print_devices_list()
    {
        if ( $this->display ) {

            if (  ( is_user_logged_in() && current_user_can( 'manage_options' ) ) ||
                ( is_user_logged_in() && !is_admin() && current_user_can( $this->frontpermissions ) )
            ) {

                $this->send_label_shortcode_page_to_front();

                $devices      = new MciscDevices();
                $devices_list = $devices->print_devices_list();
            } else {
                $devices_list = '';
            }
        } else {
            $devices_list = '';
        }

        return $devices_list;
    }

    private function send_label_shortcode_page_to_front()
    {
        wp_enqueue_script( 'mcisc_shortcode_page', MCISC_PLUGIN_URL . 'devices/js/mcisc_shortcode_page.js', array( 'jquery' ), MCISC_VERSION, false );

        wp_localize_script( 'mcisc_shortcode_page', 'mcisc_shortcode_page', [
            'shortcode_page' => true,
        ] );
    }

}

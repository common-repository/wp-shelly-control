<?php
namespace MciControlShellyDevices\devices\views;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\accounts\models\MciscAccount;
use MciControlShellyDevices\shared\check_premium\MciscGetAuth;

class MciscDevicesView
{

    private $mcisc_auth;

    public function __construct()
    {
        $this->mcisc_auth = MciscGetAuth::get_instance();
    }

    public function init( $devices )
    {
        $accounts = new MciscAccount();
        $accounts = $accounts->get_all();

        if ( $devices == null || empty( $devices ) ) {
            $devices = [];
        }

        $html = '';

        if ( empty( $accounts ) && current_user_can( 'manage_options' ) ) {
            $html .= '<p class="mcisc_control_steps">' . esc_html( __( 'Required action: You must set up your account before importing your devices.', 'wp-shelly-control' ) ) . ' ' . '<a href="?page=mcisc-settings">Clic here</a>' . '</p>';
        }
        if ( !empty( $accounts ) && count( $devices ) == 0 && current_user_can( 'manage_options' ) ) {
            $html .= '<p class="mcisc_control_steps mcisc_arrows"><span class="mcisc_arrows">↑↑↑</span></p>';
            $html .= '<p class="mcisc_control_steps">' . esc_html( __( 'There are no devices: Click on the "Import / Update devices from Shelly Cloud" button', 'wp-shelly-control' ) ) . '</p>';
        }

        if ( $this->mcisc_auth != true ) {
            $limit_number = 6;
        } else {
            $limit_number = 100;
        }
        $disabled_count = 0;

        $html .= '<div class="mcisc_device_list" id="mcisc_device_list">';

        $test_device = !MCISC_REAL_ENVIRONMENT && !MCISC_FORCE_REMOVE_RED ? ' mcisc_test_device' : '';

        foreach ( $devices as $device ) {

            $disabled_count++;
            if ( $disabled_count > $limit_number ) {
                $disabled = 'mcisc_device_disabled ';
            } else {
                $disabled = '';
            }

            $admin_device_active = current_user_can( 'manage_options' ) && $device['wp_active'] != null && $device['wp_active'] == '1' ? ' mcisc_device_active' : '';
            $device_layer        = current_user_can( 'manage_options' ) && $this->mcisc_auth && $device['wp_active'] != '1' ? ' mcisc_device_layer' : '';

            $html .= $this->print_device( $device, $disabled, $test_device, $admin_device_active, $device_layer );
        }
        $html .= '</div>';

        return $html;
    }

    public function print_device( $device, $disabled = '', $test_device = '', $admin_device_active = '', $device_layer = '' )
    {
        $html = '<div class="' . esc_attr( $disabled ) . 'mcisc_device' . esc_attr( $test_device ) . esc_attr( $admin_device_active ) . esc_attr( $device_layer ) . '" id="' . esc_attr( $device['id'] ) . '">';
        $html .= '<div class="mcisc_online" id="mcisc_online_' . esc_attr( $device['id'] ) . '" ></div>';
        if ( $this->mcisc_auth && is_admin() && current_user_can( 'manage_options' ) ) {
            $html .= '<input type="checkbox" class="mcisc_wp_active" id="mcisc_wp_active_' . esc_attr( $device['id'] ) . '"' . esc_attr( $device['wp_active'] == '1' ? ' checked' : '' ) . '>';
        }
        $html .= '<p class="mcisc_device_name"><b>' . esc_html( $device['name'] ) . '</b></p>';
        $html .= '<button class="mcisc_device_button" id="mcisc_ison_btn_' . esc_attr( $device['id'] ) . '">
        <img class="mcisc_power_ico" id="mcisc_power_ico" src="' . esc_url( MCISC_PLUGIN_URL . 'front/assets/img/power_ico.png' ) . '" alt="switch">
        </button>';
        $html .= '<p class="mcisc_device_status" id="mcisc_device_status_' . esc_attr( $device['id'] ) . '"></p>';
        $html .= '<hr>';
        if ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) ) {
            $html .= '<p class="mcisc_device_backpermissions"><b>' . esc_html( __( 'Back permissions: ', 'wp-shelly-control' ) ) . '</b>' . $this->print_device_list_permmisions( $device['backpermissions'] ) . '</p>';
            $html .= '<p class="mcisc_device_frontpermissions"><b>' . esc_html( __( 'Front permissions: ', 'wp-shelly-control' ) ) . '</b>' . $this->print_device_list_permmisions( $device['frontpermissions'] ) . '</p>';
            $html .= '<hr>';
        }
        $html .= '<p class="mcisc_device_power" id="mcisc_power_' . esc_attr( $device['id'] ) . '">' . esc_html( __( 'Power: ', 'wp-shelly-control' ) ) . '<span></span>' . '</p>';
        $html .= '<hr>';
        $html .= '<p class="mcisc_device_id"><b>' . esc_html( __( 'Id: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['id'] ) . '</p>';
        $html .= '<p class="mcisc_device_type"><b>' . esc_html( __( 'Type: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['type'] ) . '</p>';
        $html .= '<p class="mcisc_device_gen"><b>' . esc_html( __( 'Gen: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['gen'] ) . '</p>';

        if ( !MCISC_REAL_ENVIRONMENT && !MCISC_FORCE_REMOVE_RED ) {

            $html .= '<div class="mcisc_devices_test">';
            $html .= '<p class="mcisc_device_wp_active"><b>' . esc_html( __( 'Wp_active: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['wp_active'] ) . '</p>';
            $html .= '<p class="mcisc_device_account_id"><b>' . esc_html( __( 'Account_id: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['account_id'] ) . '</p>';
            $html .= '<p class="mcisc_device_position"><b>' . esc_html( __( 'Position: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['position'] ) . '</p>';
            $html .= '<p class="mcisc_device_channel"><b>' . esc_html( __( 'Channel: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['channel'] ) . '</p>';
            $html .= '<p class="mcisc_device_wp_type"><b>' . esc_html( __( 'Wp_type: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['wp_type'] ) . '</p>';
            $html .= '<p class="mcisc_device_category"><b>' . esc_html( __( 'Category: ', 'wp-shelly-control' ) ) . '</b>' . esc_html( $device['category'] ) . '</p>';
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    public function print_device_list_permmisions( $permissions )
    {
        if ( empty( $permissions ) || $permissions == null ) {$html = '';}

        if ( is_array( $permissions ) ) {
            $html = '<ul>';
            foreach ( $permissions as $permission ) {
                $html .= '<li>' . esc_html( $permission ) . '</li>';
            }
            $html .= '</ul>';
        }
        return $html;

    }

}

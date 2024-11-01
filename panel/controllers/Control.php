<?php
namespace MciControlShellyDevices\panel\controllers;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\accounts\models\MciscAccount;
use MciControlShellyDevices\devices\controllers\MciscSaveCloudList;
use MciControlShellyDevices\devices\models\MciscDevice;
use MciControlShellyDevices\options\models\MciscOption;
use MciControlShellyDevices\panel\views\MciscControlView;
use MciControlShellyDevices\shared\MciscHelpers;

class MciscControl
{
    private $backpermissions;
    private $frontpermissions;

    public function __construct()
    {
        $options                = new MciscOption();
        $this->backpermissions  = sanitize_text_field( $options->get( 'backpermissions' ) );
        $this->frontpermissions = sanitize_text_field( $options->get( 'frontpermissions' ) );

    }

    public function init()
    {
        if ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) ) {

            $this->save_control();
            $this->import_devices_from_shellycloud();
        }

        if (  ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) ) ||
            ( is_user_logged_in() && is_admin() && current_user_can( $this->backpermissions ) ) ) {

            $control_view = new MciscControlView();
            $control_view->init();

        } else {

            MciscHelpers::error_message( __( 'You do not have permission to access this page.', 'wp-shelly-control' ) );
        }
    }

    public function import_devices_from_shellycloud()
    {
        $accounts = new MciscAccount();
        $accounts = $accounts->get_all();

        if ( isset( $_POST['sync_devices'] ) && !empty( $accounts ) ) {

            if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

            if ( !isset( $_POST['mcisc_sync_nonce'] ) || !wp_verify_nonce( $_POST['mcisc_sync_nonce'], 'mcisc_sync_nonce' ) ) {
                MciscHelpers::error_message( 'Security check failed.' );
                return false;
            }

            $save_cloudlist = new MciscSaveCloudList();
            $max_attempts   = 10;

            do {
                $save = $save_cloudlist->init();

                if ( !$save ) {
                    $sync_errors = get_transient( 'mcisc_sync_error' ) != null ? get_transient( 'mcisc_sync_error' ) : 0;
                    set_transient( 'mcisc_sync_error', $sync_errors + 1, 60 );

                    //If there is an error, try again
                    if (  ( get_transient( 'mcisc_sync_error' ) > 0 && get_transient( 'mcisc_sync_error' ) < $max_attempts ) ) {
                        sleep( 2 );
                    } else {
                        MciscHelpers::warning_message( __( 'Could not download devices from Shelly Cloud server. Please Try it after a few seconds.', 'wp-shelly-control' ) . ' ' . sanitize_text_field( get_transient( 'mcisc_sync_error' ) ) . ' ' . __( ' attempt/s)', 'wp-shelly-control' ) );
                        delete_transient( 'mcisc_sync_error' );
                        break;
                    }
                } else {
                    $errors = get_transient( 'mcisc_sync_error' );
                    set_transient( 'mcisc_sync_error', $errors + 1, 60 );

                    MciscHelpers::success_message( __( 'Devices list successfully updated from Shelly Cloud. (Connection OK:', 'wp-shelly-control' ) . ' ' . sanitize_text_field( get_transient( 'mcisc_sync_error' ) ) . ')' );
                    delete_transient( 'mcisc_sync_error' );
                    break;
                }
            } while ( get_transient( 'mcisc_sync_error' ) > 0 && get_transient( 'mcisc_sync_error' ) < $max_attempts );

        } elseif ( isset( $_POST['sync_devices'] ) && empty( $accounts ) ) {

            MciscHelpers::warning_message( __( 'No Shelly accounts created. You must create one before importing the devices.', 'wp-shelly-control' ) );
        }
    }

    private function save_control()
    {
        if ( isset( $_POST['control_save'] ) ) {

            if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

            if ( !isset( $_POST['mcisc_control_nonce'] ) || !wp_verify_nonce( $_POST['mcisc_control_nonce'], 'mcisc_control_nonce' ) ) {
                MciscHelpers::error_message( 'Security check failed.' );
                return false;
            }

            $option = new MciscOption();

            //Save Global option back permissions
            if ( isset( $_POST['backpermissions'] ) && !empty( $_POST['backpermissions'] ) ) {
                $backpermissions      = sanitize_text_field( $_POST['backpermissions'] );
                $save_backpermissions = $option->save( 'backpermissions', $backpermissions );
            } else {
                $save_backpermissions = $option->save( 'backpermissions', '0' );
            }
            //Save Global option front permissions
            if ( isset( $_POST['frontpermissions'] ) && !empty( $_POST['frontpermissions'] ) ) {
                $frontpermissions      = sanitize_text_field( $_POST['frontpermissions'] );
                $save_frontpermissions = $option->save( 'frontpermissions', $frontpermissions );
            } else {
                $save_frontpermissions = $option->save( 'frontpermissions', '0' );
            }

            $save_devices_permissions = $this->save_global_permissions_in_devices( $backpermissions, $frontpermissions );

            if ( $save_backpermissions && $save_frontpermissions && $save_devices_permissions ) {
                MciscHelpers::success_message( __( 'Settings saved successfully.', 'wp-shelly-control' ) );
                return true;
            }

        }
    }

    private function save_global_permissions_in_devices( $backpermissions, $frontpermissions )
    {
        $wp_device  = new MciscDevice();
        $wp_devices = $wp_device->get_all();

        if ( !empty( $wp_devices ) ) {

            foreach ( $wp_devices as $device ) {

                $device['backpermissions']    = [];
                $device['frontpermissions']   = [];
                $device['backpermissions'][]  = sanitize_text_field( $backpermissions );
                $device['frontpermissions'][] = sanitize_text_field( $frontpermissions );

                if ( !in_array( 'administrator', $device['backpermissions'] ) ) {
                    $device['backpermissions'][] = 'administrator';
                }
                if ( !in_array( 'administrator', $device['frontpermissions'] ) ) {
                    $device['frontpermissions'][] = 'administrator';
                }
                $wp_device->save( $device['account_id'], $device );
            }
            return true;
        } else {
            return false;
        }
    }

}

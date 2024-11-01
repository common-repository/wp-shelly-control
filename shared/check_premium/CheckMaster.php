<?php
namespace MciControlShellyDevices\shared\check_premium;

use MciControlShellyDevices\shared\check_premium\MciscCheckLemon;
use MciControlShellyDevices\shared\MciscHelpers;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscCheckMaster
{

    public function check_master()
    {
        global $pagenow;
        $check_lemon = new MciscCheckLemon;

        //Check current page & permissions
        if ( is_admin() && current_user_can( 'manage_options' ) &&
            ( $pagenow == 'plugins.php' ||
                (
                    ( isset( $_GET['page'] ) && $_GET['page'] == 'wp-shelly-control' ) ||
                    ( isset( $_GET['page'] ) && $_GET['page'] == 'mcisc-settings' ) ||
                    ( isset( $_GET['page'] ) && $_GET['page'] == 'mcisc-help' )
                )
            ) ) {

            //Check submit button
            if (
                isset( $_POST['submit_mcisc_activate'] ) && isset( $_POST['mcisc_code_key'] ) && strlen( $_POST['mcisc_code_key'] ) > 18 &&
                !empty( $_POST['mcisc_code_key'] )
            ) {
                if (
                    ( !isset( $_GET['page'] ) || $_GET['page'] !== 'mcisc-settings' ) ||
                    ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) )
                ) {
                    return false;
                }

                if ( !isset( $_POST['mcisc_settings_nonce'] ) || !wp_verify_nonce( $_POST['mcisc_settings_nonce'], 'mcisc_settings_nonce' ) ) {
                    MciscHelpers::error_message( 'Security check failed.', true );
                    return false;
                }

                // Activate in Lemon
                if ( $check_lemon->instance_exists() !== true ) {
                    $check_lemon->activate( sanitize_text_field( $_POST['mcisc_code_key'] ) );
                }

                if ( $check_lemon->validate() ) {
                    update_option( 'mcisc_auth_premium', '1' );
                    MciscHelpers::success_message( __( 'The Premium plugin is activated. ', 'wp-shelly-control' ), true );
                } else {
                    update_option( 'mcisc_auth_premium', '0' );
                    MciscHelpers::error_message( __( 'The Premium plugin is disabled. ', 'wp-shelly-control' ), true );
                }

            }

            $check_lemon = $check_lemon->validate();

            // Check LemonAPI and deactivate 'mcisc_auth_premium' if none are valid
            if ( $check_lemon == true ) {

                update_option( 'mcisc_auth_premium', '1' );

            } else {

                if ( $check_lemon != 'server_error' ) {
                    update_option( 'mcisc_auth_premium', '0' );
                }

            }
        }
    }

    public function deactivate_premium()
    {
        if ( isset( $_POST['mcisc_deactivate'] ) ) {

            if (
                ( !isset( $_GET['page'] ) || $_GET['page'] !== 'mcisc-settings' ) ||
                ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) )
            ) {
                return false;
            }

            if ( !isset( $_POST['mcisc_settings_nonce'] ) || !wp_verify_nonce( $_POST['mcisc_settings_nonce'], 'mcisc_settings_nonce' ) ) {
                MciscHelpers::error_message( 'Security check failed.', true );
                return false;
            }

            $check_lemon = new MciscCheckLemon;

            if ( $check_lemon->instance_exists() == true ) {

                $check_lemon->deactivate();

                MciscHelpers::success_message( __( 'Premium license deactivated successfully.', 'wp-shelly-control' ), true );
                return true;
            } else {
                MciscHelpers::error_message( __( 'Premium license could not be deactivated', 'wp-shelly-control' ), true );
                return false;
            }

        }
    }

    public function init()
    {
        add_action( 'admin_init', [$this, 'check_master'] );
        add_action( 'admin_init', [$this, 'deactivate_premium'] );
    }

}

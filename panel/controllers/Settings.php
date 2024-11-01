<?php
namespace MciControlShellyDevices\panel\controllers;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\accounts\models\MciscAccount;
use MciControlShellyDevices\options\models\MciscOption;
use MciControlShellyDevices\panel\views\MciscSettingsView;
use MciControlShellyDevices\shared\MciscHelpers;

class MciscSettings
{

    public function init()
    {
        $this->delete_account();
        $this->save_account();

        $this->save_settings();

        $settings_view = new MciscSettingsView();
        $settings_view->init();
    }

    private function save_account()
    {
        if ( isset( $_POST['account_save'] ) ) {

            if (
                ( !isset( $_GET['page'] ) || $_GET['page'] !== 'mcisc-settings' ) ||
                ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) )
            ) {
                return false;
            }

            if ( !isset( $_POST['mcisc_account_nonce'] ) || !wp_verify_nonce( $_POST['mcisc_account_nonce'], 'mcisc_account_nonce' ) ) {
                MciscHelpers::error_message( 'Security check failed.' );
                return false;
            }

            if ( isset( $_POST['account_cloudkey'] ) && !empty( $_POST['account_cloudkey'] ) && isset( $_POST['account_cloudserver'] ) && !empty( $_POST['account_cloudserver'] ) ) {

                if ( isset( $_POST['account_name'] ) && !empty( $_POST['account_name'] ) ) {
                    $account_name = sanitize_text_field( $_POST['account_name'] );
                } else {
                    $account_name = '';
                }

                $account_cloudkey    = sanitize_text_field( $_POST['account_cloudkey'] );
                $account_cloudserver = sanitize_text_field( $_POST['account_cloudserver'] );

                $account = new MciscAccount();
                $save    = $account->save( $account_name, $account_cloudkey, $account_cloudserver );

                if ( $save ) {
                    MciscHelpers::success_message( __( 'Account saved successfully.', 'wp-shelly-control' ) );
                    return true;
                }

            } else {
                MciscHelpers::error_message( __( 'Please fill Auth Cloud Key & Cloud server fields. The name field is optional.', 'wp-shelly-control' ) );
                return false;
            }
        }
    }

    private function delete_account()
    {
        if (
            ( !isset( $_GET['page'] ) || $_GET['page'] !== 'mcisc-settings' ) ||
            ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) ||
            ( !isset( $_POST['mcisc_account_nonce'] ) || !wp_verify_nonce( $_POST['mcisc_account_nonce'], 'mcisc_account_nonce' ) )
        ) {
            return false;
        }

        //Check if some value $_POST['delete_account_*'] is set
        $delete_account_id = null;

        if ( isset( $_POST ) && !empty( $_POST ) ) {

            foreach ( $_POST as $key => $value ) {

                if ( strpos( $key, 'delete_account_' ) !== false ) {
                    $id                = str_replace( 'delete_account_', '', $key );
                    $delete_account_id = $id;
                    break;
                }
            }
        }

        if ( $delete_account_id !== null ) {

            if ( isset( $delete_account_id ) && !empty( $delete_account_id ) ) {
                $account = new MciscAccount();
                $delete  = $account->delete( $delete_account_id );

                if ( $delete ) {
                    MciscHelpers::success_message( __( 'Account deleted successfully.', 'wp-shelly-control' ) );
                }
            }
        }
    }

    private function save_settings()
    {
        if ( isset( $_POST['settings_save'] ) ) {

            if (
                ( !isset( $_GET['page'] ) || $_GET['page'] !== 'mcisc-settings' ) ||
                ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) )
            ) {
                return false;
            }

            if ( !isset( $_POST['mcisc_settings_nonce'] ) || !wp_verify_nonce( $_POST['mcisc_settings_nonce'], 'mcisc_settings_nonce' ) ) {
                MciscHelpers::error_message( 'Security check failed.' );
                return false;
            }

            $option = new MciscOption();

            //Save delete all
            if ( isset( $_POST['delete_all'] ) ) {
                $save = $option->save( 'delete_all', '1' );
            } else {
                $save = $option->save( 'delete_all', '0' );
            }

            if ( $save ) {
                MciscHelpers::success_message( __( 'Settings saved successfully.', 'wp-shelly-control' ) );
                return true;
            }

        }
    }

}

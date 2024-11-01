<?php
namespace MciControlShellyDevices\accounts\models;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\options\models\MciscOption;
use MciControlShellyDevices\shared\MciscEncryption;
use MciControlShellyDevices\shared\MciscHelpers;

class MciscAccount
{
    private $id;
    private $name;
    private $cloudkey;
    private $cloudkey_encrypt;
    private $cloudserver;

    private $backpermissions;
    private $frontpermissions;

    public function __construct()
    {
        $options                = new MciscOption();
        $this->backpermissions  = $options->get( 'backpermissions' );
        $this->frontpermissions = $options->get( 'frontpermissions' );
    }

    public function save( $name, $cloudkey, $cloudserver )
    {
        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        $auth_premium = false;

        if ( $auth_premium !== true ) {
            $this->delete_all();
        }

        $this->name             = sanitize_text_field( $name );
        $this->cloudkey         = sanitize_text_field( $cloudkey );
        $this->cloudkey_encrypt = MciscEncryption::encrypt( sanitize_text_field( $cloudkey ) );
        $this->cloudserver      = sanitize_text_field( $cloudserver );

        if ( $this->exists( $this->cloudkey ) ) {

            $update = $this->update( $this->cloudkey );

            if ( $update ) {
                MciscHelpers::success_message( __( 'Account updated successfully.', 'wp-shelly-control' ) );
            }
            return $update;

        } else {

            $add = $this->add();
            return $add;
        }
    }

    private function add()
    {
        $accounts = $this->get_all();

        if ( $accounts == null || empty( $accounts ) ) {
            $account_id = 1;
        } else {
            $last_id    = end( $accounts );
            $account_id = $last_id['id'] + 1;
        }

        $accounts[] = [
            'id'          => sanitize_text_field( $account_id ),
            'name'        => sanitize_text_field( $this->name ),
            'cloudkey'    => sanitize_text_field( $this->cloudkey_encrypt ),
            'cloudserver' => sanitize_text_field( $this->cloudserver ),
        ];

        update_option( 'mcisc_accounts', serialize( $accounts ) );

        return true;
    }

    private function update( $cloudkey )
    {

        $accounts = $this->get_all();

        if ( empty( $accounts ) ) {
            $accounts = [];
            return false;
        }

        foreach ( $accounts as $key => $account ) {

            if ( MciscEncryption::decrypt( $account['cloudkey'] ) == $cloudkey ) {
                $accounts[$key]['name']        = sanitize_text_field( $this->name );
                $accounts[$key]['cloudserver'] = sanitize_text_field( $this->cloudserver );
            }
        }

        update_option( 'mcisc_accounts', serialize( $accounts ) );

        return true;
    }

    private function exists( $cloudkey )
    {
        $accounts = $this->get_all();

        if ( empty( $accounts ) ) {
            return false;
        }

        foreach ( $accounts as $account ) {

            if ( MciscEncryption::decrypt( $account['cloudkey'] ) == $cloudkey ) {
                return true;
            }
        }

        return false;
    }

    public function get_all()
    {

        if ( is_user_logged_in() &&
            ( current_user_can( 'manage_options' ) || current_user_can( $this->backpermissions ) || current_user_can( $this->frontpermissions ) )
        ) {
            if ( get_option( 'mcisc_accounts' ) == null || empty( get_option( 'mcisc_accounts' ) ) ) {return [];}

            $accounts = unserialize( get_option( 'mcisc_accounts' ) );

            return $accounts;
        } else {
            return false;
        }
    }

    public function delete( $id )
    {

        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        $accounts = $this->get_all();

        if ( empty( $accounts ) ) {
            return false;
        }

        foreach ( $accounts as $key => $account ) {
            if ( $account['id'] == $id ) {
                unset( $accounts[$key] );
            }
        }

        update_option( 'mcisc_accounts', serialize( $accounts ) );

        return true;
    }

    private function delete_all()
    {
        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        update_option( 'mcisc_accounts', serialize( [] ) );

        return true;
    }

    public function get_name( $id )
    {

        $accounts = $this->get_all();

        if ( empty( $accounts ) ) {
            return false;
        }

        foreach ( $accounts as $key => $account ) {
            if ( $account['id'] == $id ) {
                return sanitize_text_field( $account['name'] );
            }
        }

        return false;
    }

    public function get_cloudserver( $id )
    {
        $accounts = $this->get_all();

        if ( empty( $accounts ) ) {
            return false;
        }

        foreach ( $accounts as $key => $account ) {
            if ( $account['id'] == $id ) {
                return sanitize_text_field( $account['cloudserver'] );
            }
        }

        return false;
    }

    public function get_cloudkey( $id )
    {
        $accounts = $this->get_all();

        if ( empty( $accounts ) ) {
            return false;
        }

        foreach ( $accounts as $key => $account ) {
            if ( $account['id'] == $id ) {
                return sanitize_text_field( MciscEncryption::decrypt( $account['cloudkey'] ) );
            }
        }
        return false;
    }

}
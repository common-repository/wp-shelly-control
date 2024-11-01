<?php
namespace MciControlShellyDevices\devices\models;

use MciControlShellyDevices\shared\check_premium\MciscGetAuth;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscDevice
{
    private $id;
    private $account_id;
    private $name;
    private $position;
    private $channel;
    private $ison;
    private $wp_type;
    private $wp_active;
    private $type;
    private $gen;
    private $category;
    private $backpermissions;
    private $frontpermissions;

    private $mcisc_auth;

//=====================================================

    public function __construct()
    {
        $this->mcisc_auth = MciscGetAuth::get_instance();
    }

    public function save( $account_id, $device )
    {
        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        $this->id               = sanitize_text_field( $device['id'] );
        $this->account_id       = sanitize_text_field( $account_id );
        $this->name             = sanitize_text_field( $device['name'] );
        $this->position         = sanitize_text_field( $device['position'] );
        $this->channel          = sanitize_text_field( $device['channel'] );
        $this->ison             = sanitize_text_field( $device['ison'] );
        $this->wp_type          = sanitize_text_field( $device['wp_type'] );
        $this->wp_active        = sanitize_text_field( $device['wp_active'] );
        $this->type             = sanitize_text_field( $device['type'] );
        $this->gen              = sanitize_text_field( $device['gen'] );
        $this->category         = sanitize_text_field( $device['category'] );
        $this->backpermissions  = array_map( 'sanitize_text_field', $device['backpermissions'] );
        $this->frontpermissions = array_map( 'sanitize_text_field', $device['frontpermissions'] );

        if ( $this->exists( $this->account_id, $this->id ) ) {

            $update = $this->update( $this->account_id, $this->id );
            return $update;

        } else {

            $add = $this->add( $this->account_id );
            return $add;
        }

        return false;
    }

    //=====================================================

    private function add( $account_id )
    {
        $wp_devices = $this->get_all();

        if ( empty( $wp_devices ) ) {
            $wp_devices = [];
        }

        $wp_devices[] = [
            'id'               => sanitize_text_field( $this->id ),
            'account_id'       => sanitize_text_field( $account_id ),
            'name'             => sanitize_text_field( $this->name ),
            'position'         => sanitize_text_field( $this->position ),
            'channel'          => sanitize_text_field( $this->channel ),
            'ison'             => sanitize_text_field( $this->ison ),
            'wp_type'          => sanitize_text_field( $this->wp_type ),
            'wp_active'        => sanitize_text_field( $this->wp_active ),
            'type'             => sanitize_text_field( $this->type ),
            'gen'              => sanitize_text_field( $this->gen ),
            'category'         => sanitize_text_field( $this->category ),
            'backpermissions'  => array_map( 'sanitize_text_field', $this->backpermissions ),
            'frontpermissions' => array_map( 'sanitize_text_field', $this->frontpermissions ),
        ];

        update_option( 'mcisc_devices', serialize( $wp_devices ) );

        return true;
    }

//=====================================================

    private function update( $account_id, $device_id )
    {
        $wp_devices = $this->get_all( $account_id );

        if ( empty( $wp_devices ) ) {return false;}

        foreach ( $wp_devices as $index => $device ) {

            if ( $device['id'] == $device_id ) {

                $wp_devices[$index]['id']               = sanitize_text_field( $this->id );
                $wp_devices[$index]['account_id']       = sanitize_text_field( $account_id );
                $wp_devices[$index]['name']             = sanitize_text_field( $this->name );
                $wp_devices[$index]['position']         = sanitize_text_field( $this->position );
                $wp_devices[$index]['channel']          = sanitize_text_field( $this->channel );
                $wp_devices[$index]['ison']             = sanitize_text_field( $this->ison );
                $wp_devices[$index]['wp_type']          = sanitize_text_field( $this->wp_type );
                $wp_devices[$index]['wp_active']        = sanitize_text_field( $this->wp_active );
                $wp_devices[$index]['type']             = sanitize_text_field( $this->type );
                $wp_devices[$index]['gen']              = sanitize_text_field( $this->gen );
                $wp_devices[$index]['gen2']             = sanitize_text_field( $this->gen );
                $wp_devices[$index]['category']         = sanitize_text_field( $this->category );
                $wp_devices[$index]['backpermissions']  = array_map( 'sanitize_text_field', $this->backpermissions );
                $wp_devices[$index]['frontpermissions'] = array_map( 'sanitize_text_field', $this->frontpermissions );

                update_option( 'mcisc_devices', serialize( $wp_devices ) );
                return true;
            }
        }
        return false;
    }

//=====================================================

    private function exists( $account_id, $device_id )
    {
        $wp_devices = $this->get_all( $account_id );

        if ( empty( $wp_devices ) ) {return false;}

        foreach ( $wp_devices as $wp_device ) {

            if ( $wp_device['id'] == $device_id ) {
                return true;
            }
        }
        return false;
    }

    //=====================================================

    public function get_all()
    {
        if ( get_option( 'mcisc_devices' ) == null || empty( get_option( 'mcisc_devices' ) ) ) {return false;}

        $devices = unserialize( get_option( 'mcisc_devices' ) );

        if ( current_user_can( 'manage_options' ) ) {

            return $devices;

        } else {

            $active_devices = [];

            if ( $this->mcisc_auth ) {

                foreach ( $devices as $device => $value ) {
                    if ( $value['wp_active'] == '1' ) {
                        $active_devices[] = $value;
                    }
                }
                return $active_devices;

            } else {

                return $devices;
            }
        }
    }

//=====================================================

    public function get( $device_id )
    {
        $devices = $this->get_all();

        if ( empty( $devices ) ) {return false;}

        foreach ( $devices as $device => $value ) {
            if ( $value['id'] == $device_id ) {
                return $value;
            }
        }
        return false;
    }

//=====================================================

    public function delete_all( $account_id )
    {
        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        delete_option( 'mcisc_devices' );
        update_option( 'mcisc_devices', serialize( [] ) );

        return true;
    }

//=====================================================

    public function delete( $account_id, $device_id )
    {
        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        $devices = $this->get_all( $account_id );

        if ( empty( $devices ) ) {return false;}

        foreach ( $devices as $device => $value ) {
            if ( $value['id'] == $device_id ) {
                unset( $devices[$device] );
            }
        }

        update_option( 'mcisc_devices', serialize( $devices ) );

        return true;
    }
}

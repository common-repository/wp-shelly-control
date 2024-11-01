<?php
namespace MciControlShellyDevices\devices\controllers;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\accounts\models\MciscAccount;
use MciControlShellyDevices\api\models\MciscActions;
use MciControlShellyDevices\api\models\MciscApiRequest;
use MciControlShellyDevices\devices\models\MciscDevice;
use MciControlShellyDevices\options\models\MciscOption;
use MciControlShellyDevices\shared\MciscEncryption;

class MciscSaveCloudList
{
    private $actions;
    private $options;
    private $accounts;
    private $mapped_devices = [];
    private $frontpermissions;
    private $backpermissions;

    public function __construct()
    {
        $this->options = new MciscOption();
        $this->prepare_permissions_options();

        $accounts       = new MciscAccount();
        $this->accounts = $accounts->get_all();
        if ( empty( $accounts ) ) {return false;}

        $this->actions = MciscActions::actions();
    }

    public function init()
    {
        foreach ( $this->accounts as $account ) {

            $cloudkey    = sanitize_text_field( MciscEncryption::decrypt( $account['cloudkey'] ) );
            $cloudserver = sanitize_text_field( $account['cloudserver'] );
            $account_id  = sanitize_text_field( $account['id'] );

            $cloud_devices = [];
            $cloud_devices = $this->cloud_get_all( $cloudserver, $cloudkey );

            if ( $cloud_devices ) {

                foreach ( $cloud_devices as $device => $value ) {

                    $this->mapped_devices[] = [
                        'id'               => sanitize_text_field( $value->id ),
                        'account_id'       => sanitize_text_field( $account_id ),
                        'name'             => sanitize_text_field( $value->name ),
                        'position'         => sanitize_text_field( $value->position ),
                        'channel'          => sanitize_text_field( $value->channel ),
                        'ison'             => null,
                        'wp_type'          => 'shelly',
                        'wp_active'        => null,
                        'type'             => sanitize_text_field( $value->type ),
                        'gen'              => sanitize_text_field( $value->gen ),
                        'category'         => sanitize_text_field( $value->category ),
                        'backpermissions'  => array_map( 'sanitize_text_field', $this->backpermissions ),
                        'frontpermissions' => array_map( 'sanitize_text_field', $this->frontpermissions ),
                    ];
                }
            } else {
                return false;
            }

            foreach ( $this->mapped_devices as $index => $sh_device ) {

                $wp_device = new MciscDevice;
                $save      = $wp_device->save( $account_id, $sh_device );

                if ( !$save ) {return false;}
            }
        }

        return $wp_device->get_all();
    }

    public function cloud_get_all( $cloudserver, $cloudkey )
    {
        $api_request = new MciscApiRequest;
        $response    = $api_request->post( $cloudserver, $cloudkey, sanitize_text_field( $this->actions['get_all'] ), null, null, null );

        if ( is_object( $response ) ) {
            return $response->data->devices;
        } else {
            return false;
        }
    }

    public function prepare_permissions_options()
    {
        $global_backpermissions  = sanitize_text_field($this->options->get( 'backpermissions' ));
        $global_frontpermissions = sanitize_text_field($this->options->get( 'frontpermissions' ));
        $this->frontpermissions  = ['administrator'];
        $this->backpermissions   = ['administrator'];

        if ( !empty( $global_backpermissions ) && $global_backpermissions != 'administrator' ) {
            $this->backpermissions[] = $global_backpermissions;
        }
        if ( !empty( $global_frontpermissions && $global_frontpermissions != 'administrator' ) ) {
            $this->frontpermissions[] = $global_frontpermissions;
        }
    }

}
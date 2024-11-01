<?php
namespace MciControlShellyDevices\devices\controllers;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\accounts\models\MciscAccount;
use MciControlShellyDevices\api\models\MciscActions;
use MciControlShellyDevices\api\models\MciscApiRequest;
use MciControlShellyDevices\devices\models\MciscDevice;

class MciscSwitchDevice
{
    public $device;
    private $device_id;
    private $cloudserver;
    private $cloudkey;
    private $turn;
    private $action;
    private $channel;

    public function __construct( $device_id, $old_status )
    {
        $this->device_id = sanitize_text_field( $device_id );

        $device       = new MciscDevice();
        $this->device = $device->get( $device_id );
        $account_id   = sanitize_text_field( $this->device['account_id'] );

        if ( $old_status == 1 ) {
            $this->turn = 'off';
        } elseif ( $old_status == 0 ) {
            $this->turn = 'on';
        }

        $account           = new MciscAccount();
        $this->cloudserver = sanitize_text_field( $account->get_cloudserver( $account_id ) );
        $this->cloudkey    = sanitize_text_field( $account->get_cloudkey( $account_id ) );

        $actions       = MciscActions::actions();
        $this->action  = sanitize_text_field( $actions['switch'] );
        $this->channel = sanitize_text_field( $this->device['channel'] );

    }

    public function init()
    {
        $api_request = new MciscApiRequest();

        $response = $api_request->post( $this->cloudserver, $this->cloudkey, $this->action, $this->device_id, $this->channel, $this->turn );

        if ( $response ) {
            return true;
        } else {
            return false;
        }
    }

}
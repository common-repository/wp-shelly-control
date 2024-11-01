<?php
namespace MciControlShellyDevices\devices\controllers;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\accounts\models\MciscAccount;
use MciControlShellyDevices\api\models\MciscActions;
use MciControlShellyDevices\api\models\MciscApiRequest;
use MciControlShellyDevices\devices\models\MciscDevice;

class MciscGetStatusDevice
{
    private $data; // object or null
    public $status; // object or null
    public $online; // 1 or 0
    public $ison; // 1, 0 or 'Device not supported'
    public $connected;
    public $wifi_sta;
    public $power;

    public function __construct( $device_id )
    {
        $device_id  = sanitize_text_field( $device_id );
        $device     = new MciscDevice();
        $device     = $device->get( $device_id );
        $account_id = $device['account_id'];

        $account     = new MciscAccount();
        $cloudserver = sanitize_text_field( $account->get_cloudserver( $account_id ) );
        $cloudkey    = sanitize_text_field( $account->get_cloudkey( $account_id ) );

        $actions     = MciscActions::actions();
        $api_request = new MciscApiRequest();
        $action      = sanitize_text_field( $actions['get_one'] );
        $channel     = sanitize_text_field( $device['channel'] );

        if ( $device['gen'] == '1' || $device['gen'] == '2' ) {

            $response = $api_request->post( $cloudserver, $cloudkey, $action, $device_id, $channel );

            if ( $response ) {

                //data
                if ( isset( $response->data ) ) {
                    sanitize_text_field($this->data = $response->data);
                } else {
                    $this->data = null;
                }
                //online
                if ( isset( $response->data->online ) ) {
                    sanitize_text_field($this->online = $response->data->online);
                } else {
                    $this->online = null;
                }
                //status
                if ( isset( $response->data->device_status ) ) {
                    sanitize_text_field($this->status = $response->data->device_status);
                } else {
                    $this->status = null;
                }
                //ison
                if ( isset( $this->status->relays ) ) {
                    if ( count( $this->status->relays ) > 1 ) {
                        $this->ison = $this->status->relays[$device['channel']]->ison == '1' ? 1 : 0;
                    } else {
                        $this->ison = $this->status->relays[0]->ison == '1' ? 1 : 0;
                    }
                } else {
                    $this->ison = null;
                }
                //connected
                if ( isset( $this->status->cloud->connected ) ) {
                    $this->connected = sanitize_text_field($this->status->cloud->connected);
                } else {
                    $this->connected = null;
                }
                //wifi_sta
                if ( isset( $this->status->wifi_sta->connected ) ) {
                    $this->wifi_sta = sanitize_text_field($this->status->wifi_sta->connected);
                } else {
                    $this->wifi_sta = null;
                }
                //meter
                if ( isset( $this->status->meters ) ) {
                    if ( count( $this->status->meters ) > 1 ) {
                        $this->power = sanitize_text_field($this->status->meters[$device['channel']]->power);
                    } else {
                        $this->power = sanitize_text_field($this->status->meters[0]->power);
                    }
                } elseif ( isset( $this->status->emeters ) ) {
                    if ( count( $this->status->emeters ) > 1 ) {
                        $this->power = sanitize_text_field($this->status->emeters[$device['channel']]->power);
                    } else {
                        $this->power = sanitize_text_field($this->status->emeters[0]->power);
                    }
                } else {
                    $this->power = null;
                }

            } else {
                return null;
            }

        } else {

            $this->status    = null;
            $this->online    = null;
            $this->ison      = esc_html( __( 'Device not supported', 'wp-shelly-control' ) );
            $this->power     = null;
            $this->wifi_sta  = null;
            $this->connected = null;

        }

    }

}
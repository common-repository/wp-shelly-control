<?php
namespace MciControlShellyDevices\devices\controllers;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\devices\models\MciscDevice;
use MciControlShellyDevices\devices\views\MciscDevicesView;
use MciControlShellyDevices\shared\check_premium\MciscGetAuth;

class MciscDevices
{
    private $mcisc_auth;

    public function __construct()
    {
        $this->mcisc_auth = MciscGetAuth::get_instance();
    }

    public function get_devices_list()
    {
        $devices      = new MciscDevice();
        $devices_list = $devices->get_all();

        return $devices_list;
    }

    public function print_devices_list()
    {
        $devices = $this->get_devices_list();

        $devices_view = new MciscDevicesView();

        return $devices_view->init( $devices );
    }

}

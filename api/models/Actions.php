<?php
namespace MciControlShellyDevices\api\models;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscActions
{

    public function __construct()
    {
        $this->actions();
    }

    public static function actions()
    {
        $actions = [

            'get_all' => '/interface/device/list/',
            'get_one' => '/device/status/',
            'switch'  => '/device/relay/control/',
            'emeter'  => '/device/status/',
        ];

        return $actions;
    }
}

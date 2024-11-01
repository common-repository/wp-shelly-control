<?php
namespace MciControlShellyDevices\shared\check_premium;

if ( !defined( 'ABSPATH' ) ) {exit;}

// Use MciscGetAuth::get_instance(); to get the value of $premium
// Include: use MciControlShellyDevices\shared\check_premium\MciscGetAuth;

class MciscGetAuth
{

    private static $instance;
    public $premium = false;

    private function __construct()
    {
        $this->premium = get_option( 'mcisc_auth_premium' );
    }

    public static function get_instance()
    {
        if ( self::$instance == null ) {
            self::$instance = new MciscGetAuth();
        }

        return self::$instance->premium;
    }

    public static function set_instance( $premium )
    {
        self::$instance->premium = $premium;
    }

}

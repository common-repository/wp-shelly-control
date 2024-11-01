<?php
namespace MciControlShellyDevices\shared;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\options\models\MciscOption;

class MciscUninstall
{
    public function __construct()
    {
        $this->delete_options();
    }

    private function delete_options()
    {
        $option = new MciscOption();

        if ( $option->get( 'delete_all' ) && is_admin() && current_user_can( 'activate_plugins' ) ) {

            delete_option( 'mcisc_auth_premium' );
            delete_option( 'mcisc_devices' );
            delete_option( 'mcisc_encryption_key' );
            delete_option( 'mcisc_accounts' );
            delete_option( 'mcisc_version' );
            delete_option( 'mcisc_options' );

        }
    }
}

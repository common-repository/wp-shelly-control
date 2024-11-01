<?php
namespace MciControlShellyDevices\shared;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\shared\MciscDefaultValues;

class MciscActivate
{
    public function __construct()
    {
        $this->generate_once_encryption_key();
        $this->set_default_values();
        update_option( 'mcisc_version', sanitize_text_field( MCISC_VERSION ) );
    }

    private function set_default_values()
    {
        new MciscDefaultValues();
    }

    private function generate_once_encryption_key()
    {
        $key = get_option( 'mcisc_encryption_key' );

        if ( !$key ) {
            $key = bin2hex( random_bytes( 32 ) );
            add_option( 'mcisc_encryption_key', $key );
        }
    }
}

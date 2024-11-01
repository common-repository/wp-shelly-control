<?php
namespace MciControlShellyDevices\shared;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\options\models\MciscOption;

class MciscDefaultValues
{

    public function set_default_values()
    {
        $option = new MciscOption();

        $option->add_if_not_exists( 'backpermissions', ['administrator'] );
        $option->add_if_not_exists( 'frontpermissions', ['administrator'] );
        $option->add_if_not_exists( 'delete_all', '0' );
    }

    public function __construct()
    {
        add_action( 'admin_init', [$this, 'set_default_values'] );
    }
}

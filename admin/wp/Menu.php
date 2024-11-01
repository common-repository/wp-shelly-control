<?php
namespace MciControlShellyDevices\admin\wp;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\options\models\MciscOption;
use MciControlShellyDevices\panel\controllers\MciscControl;
use MciControlShellyDevices\panel\controllers\MciscHelp;
use MciControlShellyDevices\panel\controllers\MciscSettings;
use MciControlShellyDevices\shared\check_premium\MciscGetAuth;

class MciscMenu
{
    private $backpermissions;
    private $frontpermissions;
    private $mcisc_auth;

    public function __construct()
    {

        $options                = new MciscOption();
        $this->backpermissions  = $options->get( 'backpermissions' );
        $this->frontpermissions = $options->get( 'frontpermissions' );

    }

    public function create_items_menu()
    {
        if (  ( is_user_logged_in() && is_admin() && current_user_can( 'administrator' ) ) ) {
            add_menu_page(
                'WP Shelly Control', // Page title
                __( 'WP Shelly Control', 'wp-shelly-control' ), // Menu title
                'manage_options', // permissions
                'wp-shelly-control', // slug
                [$this, 'control'], // callback function
                'dashicons-cloud', // icon
                56// position
            );
        } else {
            add_menu_page(
                'WP Shelly Control', // Page title
                __( 'WP Shelly Control', 'wp-shelly-control' ), // Menu title
                sanitize_text_field( $this->backpermissions ), // permissions
                'wp-shelly-control', // slug
                [$this, 'control'], // callback function
                'dashicons-cloud', // icon
                56// position
            );
        }

        add_submenu_page(
            'wp-shelly-control', // parent slug
            'WP Shelly Control - Settings', // Page title
            __( 'Settings', 'wp-shelly-control' ), // Menu title
            'manage_options', // permissions
            'mcisc-settings', // slug
            [$this, 'settings'], // callback function
            10// position
        );

        add_submenu_page(
            'wp-shelly-control', // parent slug
            'WP Shelly Control - Help', // Page title
            __( 'Help', 'wp-shelly-control' ), // Menu title
            'manage_options', // permissions
            'mcisc-help', // slug
            [$this, 'help'], // callback function
            10// position
        );

    }

    public function control()
    {
        $control = new MciscControl();
        $control->init();
    }

    public function settings()
    {
        $settings = new MciscSettings();
        $settings->init();
    }

    public function help()
    {
        $help = new MciscHelp();
        $help->init();
    }

    public function reload_mcisc_auth()
    {
        MciscGetAuth::set_instance( get_option( 'mcisc_auth_premium' ) );
    }

    public function init()
    {
        add_action( 'admin_init', [$this, 'reload_mcisc_auth'] );
        add_action( 'admin_menu', [$this, 'create_items_menu'] );
    }

}

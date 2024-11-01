<?php
namespace MciControlShellyDevices\admin;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\admin\wp\MciscMenu;
use MciControlShellyDevices\devices\ajax\MciscAjaxDeviceWpActive;
use MciControlShellyDevices\devices\ajax\MciscAjaxReloadStatus;
use MciControlShellyDevices\devices\ajax\MciscAjaxSwitch;
use MciControlShellyDevices\options\models\MciscOption;

class MciscAdmin
{
    private $mcisc_auth;
    private $backpermissions;
    private $frontpermissions;
    private $current_user_role;

    public function __construct()
    {
        $options                = new MciscOption();
        $this->backpermissions  = $options->get( 'backpermissions' );
        $this->frontpermissions = $options->get( 'frontpermissions' );
    }

    public function enqueue_admin_general_styles()
    {
        wp_enqueue_style( 'mcisc_admin_general', MCISC_PLUGIN_URL . 'admin/css/admin_general.css', array(), MCISC_VERSION, 'all' );
    }

    public function enqueue_panel_control()
    {
        wp_enqueue_style( 'mcisc_admin_panel_control', MCISC_PLUGIN_URL . 'admin/css/admin_panel_control.css', array(), MCISC_VERSION, 'all' );
    }

    public function enqueue_panel_settings()
    {
        wp_enqueue_style( 'mcisc_admin_panel_settings', MCISC_PLUGIN_URL . 'admin/css/admin_panel_settings.css', array(), MCISC_VERSION, 'all' );
    }

    public function enqueue_panel_help()
    {
        wp_enqueue_style( 'mcisc_admin_panel_help', MCISC_PLUGIN_URL . 'admin/css/admin_panel_help.css', array(), MCISC_VERSION, 'all' );
    }

    public function load_ajax_files()
    {
        if (  ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) ) ||
            ( is_user_logged_in() && is_admin() && current_user_can( $this->backpermissions ) )
        ) {
            $ajax_reload_status = new MciscAjaxReloadStatus;
            $ajax_reload_status->init();

            $ajax_switch = new MciscAjaxSwitch;
            $ajax_switch->init();

            $ajax_device_wpactive = new MciscAjaxDeviceWpActive;
            $ajax_device_wpactive->init();
        }
    }

    public function init()
    {
        $mcisc_menu = new MciscMenu();
        $mcisc_menu->init();

        add_action( 'admin_init', [$this, 'load_ajax_files'] );

        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_general_styles'] );

        if ( isset( $_GET['page'] ) ) {

            if ( $_GET['page'] == 'wp-shelly-control' ) {
                add_action( 'admin_enqueue_scripts', [$this, 'enqueue_panel_control'] );
            } elseif ( $_GET['page'] == 'mcisc-settings' ) {
                add_action( 'admin_enqueue_scripts', [$this, 'enqueue_panel_settings'] );
            } elseif ( $_GET['page'] == 'mcisc-help' ) {
                add_action( 'admin_enqueue_scripts', [$this, 'enqueue_panel_help'] );
            }
        }
    }

}
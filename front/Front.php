<?php
namespace MciControlShellyDevices\Front;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\devices\ajax\MciscAjaxReloadStatus;
use MciControlShellyDevices\devices\ajax\MciscAjaxSwitch;
use MciControlShellyDevices\devices\controllers\MciscShortcodeDevices;
use MciControlShellyDevices\devices\models\MciscDevice;
use MciControlShellyDevices\shared\check_premium\MciscGetAuth;

class MciscFront
{
    private $mcisc_auth;

    public function __construct()
    {
        $this->mcisc_auth = MciscGetAuth::get_instance();
    }

    public function enqueue_front_styles()
    {
        wp_enqueue_style( 'mcisc-front', MCISC_PLUGIN_URL . 'front/assets/css/front.css', array(), MCISC_VERSION, 'all' );
    }

    public function load_ajax_files()
    {

        $ajax_reload_status = new MciscAjaxReloadStatus;
        $ajax_reload_status->init();

        $ajax_switch = new MciscAjaxSwitch;
        $ajax_switch->init();

    }

    public function shortcode()
    {

        $devices = new MciscDevice();
        $devices = $devices->get_all();

        if ( !empty( $devices ) ) {

            if ( count( $devices ) == 0 ) {
                new MciscShortcodeDevices( false, $this->mcisc_auth );
            } else {
                new MciscShortcodeDevices( true, $this->mcisc_auth );
            }

        }
    }

    public function init()
    {

        add_action( 'init', [$this, 'load_ajax_files'] );
        add_action( 'init', [$this, 'shortcode'] );

        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_front_styles'] );

    }

}

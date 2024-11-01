<?php
namespace MciControlShellyDevices\panel\views\partials;

use MciControlShellyDevices\shared\check_premium\MciscGetAuth;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscHeaderView
{

    private $mcisc_auth;

    public function __construct()
    {
        $this->mcisc_auth = MciscGetAuth::get_instance();
    }

    public function print_header( $subpage )
    {
        $html = '<div class="mcisc_header_wrap">';
        if ( !MCISC_REAL_ENVIRONMENT && !MCISC_FORCE_REMOVE_RED ) {
            $html .= '<p class="mcisc_test_notice">TEST ENVIROMENT</p>';
        }
        $html .= '<h2 class="mcisc_title">';
        $html .= MCISC_PLUGIN_NAME . ' - ' . esc_html( $subpage );
        $html .= '</h2>';
        if ( current_user_can( 'manage_options' ) ) {
            if ( !$this->mcisc_auth ) {
                $html .= '<p class="mcisc_description">Do you want the Premium version? <a target="_blank" href="https://plugins.mci-desarrollo.es/control-shelly-devices/?lang=en">Get a 30-day free trial here.</a></p>';
            }
            $html .= '<p class="mcisc_description">Do you need changes in the plugin? <a target="_blank" href="https://mci-desarrollo.es/contactar/?lang=en">Tell us what you need</a> and we will send you a quote.</p>';
        }
        $html .= '</div>';

        return $html;
    }

}

<?php
namespace MciControlShellyDevices\panel\views;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\panel\views\partials\MciscHeaderView;

class MciscHelpView
{
    public function init()
    {
        $header_view = new MciscHeaderView();
        echo wp_kses( $header_view->print_header( __( 'Help', 'wp-shelly-control' ) ), [
            'div' => [
                'class' => [],
            ],
            'h2'  => [
                'class' => [],
            ],
            'a'   => [
                'href'   => [],
                'target' => [],
                'class'  => [],
            ],
            'p'   => [
                'class' => [],
            ],
        ] );

        echo wp_kses( $this->help_content(),
            [
                'hr'     => [],
                'div'    => [
                    'class' => [],
                ],
                'h2'     => [],
                'h3'     => [],
                'ol'     => [
                    'class' => [],
                ],
                'li'     => [],
                'b'      => [],
                'a'      => [
                    'href'   => [],
                    'target' => [],
                    'class'  => [],
                    'rel'    => [],
                ],
                'p'      => [],
                'img'    => [
                    'src'   => [],
                    'alt'   => [],
                    'class' => [],
                    'width' => [],
                ],
                'strong' => [],
            ]
        );
    }

    public function help_content()
    {
        $html = '<hr>';
        $html .= '<div class="mcisc_help_content">';

        $html .= '<h2>How to use:</h2>';
        $html .= '<ol class="mcisc_help_ol">';
        $html .= '<li>Use <a href="?page=mcisc-settings">WP Shelly Control / Settings</a> screen to <strong>enter your Shelly data</strong>.</li>';
        $html .= '<li>Use <a href="?page=wp-shelly-control">WP Shelly Control / WP Shelly Control</a> screen to <strong>click on "Import / Update devices from Shelly Cloud" button.</strong></li>';
        $html .= '<li><strong>Assign the permissions</strong> for the user roles for the back and front and press "Save" button.</li>';
        $html .= '<li><b>Use the shortcode [mcisc_devices]</b> on any page if you want to show your devices on the front to your users.</li>';
        $html .= '</ol>';
        $html .= '<hr>';

        $html .= '<div>';
        $html .= '<h3>Where to find my Shelly account details?</h3>';
        $html .= '<p>Go to <a href="https://home.shelly.cloud/" rel="nofollow" target="_blank">https://home.shelly.cloud</a> and login with your Shelly account.</p>';
        $html .= '<img class="mcisc_img_help" src="' . MCISC_PLUGIN_URL . 'front/assets/img/control_shelly_account_1.png" alt="Shelly Cloud">';
        $html .= '<img class="mcisc_img_help" src="' . MCISC_PLUGIN_URL . 'front/assets/img/control_shelly_account_2.png" alt="Shelly Cloud">';
        $html .= '</div>';

        $html .= '<h3>Disclaimer:</h3>';
        $html .= '<p>The access to devices and their control is protected by WordPress authentication.</p>';
        $html .= '<p>The developer company of this plugin is not responsible for the possible changes, updates, errors, bugs, failures and neither of the consequences caused by:</p>';
        $html .= '<ul>';
        $html .= '<li>The Shelly Api.</li>';
        $html .= '<li>The Shelly electronic devices.</li>';
        $html .= '<li>The use of users or admins.</li>';
        $html .= '<li>The Control Shelly Devices plugin</li>';
        $html .= '<li>Malfunction of software, servers and other hardware.</li>';
        $html .= '<li>Malicious user actions.</li>';
        $html .= '</ul>';
        $html .= '<p>The Shelly brand and the Shelly Cloud App are external to this plugin and are the property of Allterco Robotics LTD.<p>';
        $html .= '<p>The Control Shelly Devices plugin is created by MCI Desarrollo to control Shelly devices from a WordPress installation.<p>';

        $html .= '</div>';

        return $html;

    }

}

<?php
namespace MciControlShellyDevices\panel\views;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\accounts\models\MciscAccount;
use MciControlShellyDevices\options\models\MciscOption;
use MciControlShellyDevices\panel\views\partials\MciscAccountView;
use MciControlShellyDevices\panel\views\partials\MciscHeaderView;
use MciControlShellyDevices\shared\check_premium\MciscGetAuth;

class MciscSettingsView
{
    private $mcisc_auth;

    public function __construct()
    {
        $this->mcisc_auth = MciscGetAuth::get_instance();
    }

    public function init()
    {
        $header = new MciscHeaderView();
        echo wp_kses( $header->print_header( __( 'Settings', 'wp-shelly-control' ) ),
            [
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
            ]
        );

        $account = new MciscAccountView();
        echo wp_kses( $account->print_account(), [
            'div'   => [
                'class' => [],
                'id'    => [],
            ],
            'form'  => [
                'method' => [],
                'action' => [],
            ],
            'table' => [
                'class' => [],
            ],
            'tr'    => [
                'class' => [],
            ],
            'th'    => [
                'class' => [],
            ],
            'td'    => [
                'class' => [],
            ],
            'input' => [
                'type'        => [],
                'name'        => [],
                'class'       => [],
                'value'       => [],
                'placeholder' => [],
            ],
            'label' => [
                'for'   => [],
                'class' => [],
            ],
            'span'  => [
                'class' => [],
            ],
            'p'     => [
                'class' => [],
            ],
            'a'     => [
                'href'   => [],
                'target' => [],
                'class'  => [],
            ],
        ] );

        echo wp_kses( $this->print_options(), [
            'div'   => [
                'class' => [],
                'id'    => [],
            ],
            'form'  => [
                'method' => [],
                'action' => [],
            ],
            'table' => [
                'class' => [],
            ],
            'tr'    => [
                'class' => [],
            ],
            'td'    => [
                'class' => [],
            ],
            'hr'    => [
                'class' => [],
            ],
            'input' => [
                'type'      => [],
                'name'      => [],
                'class'     => [],
                'id'        => [],
                'value'     => [],
                'checked'   => [],
                'minlength' => [],
            ],
            'label' => [
                'for'   => [],
                'class' => [],
            ],
            'span'  => [
                'for'   => [],
                'class' => [],
            ],
            'a'     => [
                'href'   => [],
                'target' => [],
                'class'  => [],
            ],
            'p'     => [
                'class' => [],
                'id'    => [],
            ],
        ] );
    }

    public function print_options()
    {
        $account = new MciscAccount();
        $option  = new MciscOption();

        $html = '<div class="mcisc_settings_wrap">';
        $html .= '<form method="post" action="">';
        $html .= '<table class="mcisc_settings">';

        //Delete all data checkbox
        $html .= '<tr class="mcisc_settings_row">';
        $html .= '<td>';
        $delete_all_checked = $option->get( 'delete_all' ) ? 'checked' : '';
        $html .= '<input type="checkbox" name="delete_all" id="delete_all" ' . $delete_all_checked . '>';
        $html .= '<span for="delete_all" class="mcisc_settings_label checkline">' . esc_html( 'Delete all plugin data on uninstall (including Shelly account data in WordPress)', 'wp-shelly-control' ) . '</span></td>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        //Save button
        $html .= '<div class="mcisc_settings_row button_row">';
        $html .= '<input type="submit" name="settings_save" id="settings_save" value="' . esc_html( __( 'Save Settings', 'wp-shelly-control' ) ) . '">';
        $html .= '</div>';

        //License area
        if ( !$this->mcisc_auth ) {
            $html .= '<hr/>';
            $html .= '<div class="mcisc_wrap_license">';
            $html .= '<label for="code_key">' . esc_html( __( 'License key', 'wp-shelly-control' ) ) . '</label>';
            $html .= '<input type="password" name="mcisc_code_key" minlength="20" class="premium-password">';

            $html .= '<input class="mcisc_btn submit_mcisc_activate" type="submit" name="submit_mcisc_activate" value="' . esc_html( __( 'Activate premium', 'supplier-order-email' ) ) . '">';
            $html .= '<a href="https://plugins.mci-desarrollo.es/control-shelly-devices/?lang=en" target="_blank" class="mcisc_btn green">' . esc_html( __( 'Get 30 days free trial Pro', 'wp-shelly-control' ) ) . '</a>';
            $html .= '</div>';

            $html .= '</div>';
        } else {
            $html .= '<hr/>';
            $html .= '<div class="mcisc_wrap_license_active">';
            $html .= '<div><p id="mcisc_premium_active">' . esc_html( __( 'Premium license is active', 'wp-shelly-control' ) ) . '</p></div>';
            $html .= '<p class="success secondary_text deactivate_text">';
            $html .= esc_html( __( 'If you are no longer going to use the Premium options of the plugin in this WooCommerce installation, you can deactivate licenses to reduce the limit of your premium plan so that you can use it on other websites. You can always reactivate it with your License Key.', 'wp-shelly-control' ) );
            $html .= '</p>';
            $html .= '<div class="deactivate_btn_wrap">';
            $html .= '<input class="mcisc_btn" type="submit" name="mcisc_deactivate" id="mcisc_deactivate" value="' . esc_html( __( 'Deactivate premium license on this website', 'wp-shelly-control' ) ) . '">';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= wp_nonce_field( 'mcisc_settings_nonce', 'mcisc_settings_nonce' );
        $html .= '</form>';
        $html .= '</div>';

        return $html;
    }
}

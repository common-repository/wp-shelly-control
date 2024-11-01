<?php
namespace MciControlShellyDevices\panel\views;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\devices\controllers\MciscDevices;
use MciControlShellyDevices\options\models\MciscOption;
use MciControlShellyDevices\panel\views\partials\MciscHeaderView;

class MciscControlView
{
    private $roles;

    public function __construct()
    {
        global $wp_roles;
        $this->roles = $wp_roles->get_names();
    }

    public function init()
    {
        $header = new MciscHeaderView();
        echo wp_kses( $header->print_header( __( 'Control', 'wp-shelly-control' ) ),
            [
                'div' => [
                    'class' => [],
                ],
                'h2'  => [
                    'class' => [],
                ],
                'p'   => [
                    'class' => [],
                ],
                'a'   => [
                    'href'   => [],
                    'target' => [],
                    'class'  => [],
                ],
            ]
        );

        if ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) ) {
            echo wp_kses( $this->print_sync_button(), [
                'hr'    => [],
                'div'   => [
                    'class' => [],
                ],
                'form'  => [
                    'method' => [],
                    'action' => [],
                ],
                'input' => [
                    'type'  => [],
                    'name'  => [],
                    'class' => [],
                    'value' => [],
                ],
            ] );
        }

        $devices = new MciscDevices();
        echo wp_kses( $devices->print_devices_list(), [
            'div'    => [
                'class' => [],
                'id'    => [],
            ],
            'p'      => [
                'class' => [],
                'id'    => [],
            ],
            'b'      => [],
            'hr'     => [],
            'span'   => [],
            'button' => [
                'class' => [],
                'id'    => [],
            ],
            'img'    => [
                'class' => [],
                'id'    => [],
                'src'   => [],
                'alt'   => [],
            ],
            'ul'     => [],
            'li'     => [],
            'a'      => [
                'href'  => [],
                'class' => [],
                'id'    => [],
            ],
            'input'  => [
                'type'    => [],
                'name'    => [],
                'class'   => [],
                'id'      => [],
                'value'   => [],
                'checked' => [],
            ],
        ] );

        if ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) ) {
            echo wp_kses( $this->print_control_permisssions(),
                [
                    'hr'     => [],
                    'div'    => [
                        'class' => [],
                    ],
                    'h3'     => [],
                    'form'   => [
                        'method' => [],
                        'action' => [],
                    ],
                    'table'  => [
                        'class' => [],
                    ],
                    'tr'     => [],
                    'td'     => [],
                    'label'  => [
                        'class' => [],
                    ],
                    'select' => [
                        'name'  => [],
                        'id'    => [],
                        'class' => [],
                    ],
                    'option' => [
                        'value'    => [],
                        'selected' => [],
                    ],
                    'input'  => [
                        'type'  => [],
                        'name'  => [],
                        'class' => [],
                        'value' => [],
                    ],
                    'span'   => [
                        'class' => [],
                    ],
                    'strong' => [],
                    'ul'     => [],
                    'li'     => [],
                    'p'      => [
                        'class' => [],
                    ],
                ]
            );
        }
    }

    public function print_sync_button()
    {
        $html = '<hr>';
        $html .= '<div class="mcisc_sync_btn_wrap">';
        $html .= '<form method="post" action="?page=wp-shelly-control&sync=1">';
        $html .= '<input type="submit" name="sync_devices" class="mcisc_blue_btn" value="' . esc_attr( __( 'Import / Update devices from Shelly Cloud', 'wp-shelly-control' ) ) . '">';
        $html .= wp_nonce_field( 'mcisc_sync_nonce', 'mcisc_sync_nonce', true, false );
        $html .= '</form>';
        $html .= '</div>';

        return $html;
    }

    public function print_device_list_permmisions( $permissions )
    {
        if ( empty( $permissions ) || $permissions == null ) {$html = '';}

        if ( is_array( $permissions ) ) {
            $html = '<ul>';
            foreach ( $permissions as $permission ) {
                $html .= '<li>' . esc_html( $permission ) . '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    public function print_control_permisssions()
    {
        $option = new MciscOption();

        $html = '<hr>';
        $html .= '<div class="mcisc_controlsettings_wrap">';
        $html .= '<h3>' . esc_html( __( 'Access permissions', 'wp-shelly-control' ) ) . '</h3>';
        $html .= '<form method="post" action="">';
        $html .= '<table class="mcisc_settings">';

        //Back permissions
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '<label class="mcisc_description">' . esc_html( __( 'Back permissions', 'wp-shelly-control' ) ) . '</label>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<select name="backpermissions" id="backpermissions" class="mcisc_settings_input">';
        foreach ( $this->roles as $back_key => $back_value ) {
            $backpermissions_selected = $option->get( 'backpermissions' ) == $back_key ? 'selected' : '';
            $html .= '<option value="' . esc_attr( $back_key ) . '" ' . esc_attr( $backpermissions_selected ) . '>' . esc_html( $back_value ) . '</option>';
        }
        $html .= '</select>';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<span class="mcisc_description mcisc_annotation">' . esc_html( __( 'Set who can access to WP Shelly Control in back menu (admins always have access)', 'wp-shelly-control' ) ) . '</span>';
        $html .= '</td>';
        $html .= '</tr>';

        //Front permissions
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '<label class="mcisc_description">' . esc_html( __( 'Front permissions', 'wp-shelly-control' ) ) . '</label>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<select name="frontpermissions" id="frontpermissions" class="mcisc_settings_input">';
        foreach ( $this->roles as $front_key => $front_value ) {
            $frontpermissions_selected = $option->get( 'frontpermissions' ) == $front_key ? 'selected' : '';
            $html .= '<option value="' . esc_attr( $front_key ) . '" ' . esc_attr( $frontpermissions_selected ) . '>' . esc_html( $front_value ) . '</option>';
        }
        $html .= '</select>';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<span class="mcisc_description mcisc_annotation">' . esc_html( __( 'Set who can see the devices on the front via shortcode (admins always have access)', 'wp-shelly-control' ) ) . '</span>';
        $html .= '<span class="mcisc_description mcisc_annotation shortcode"><b>' . esc_html( __( 'Use the shortcode', 'wp-shelly-control' ) ) . '<strong> [mcisc_devices] </strong>' . esc_html( __( 'to display all devices in a page.', 'wp-shelly-control' ) ) . '</b></span>';
        $html .= '</td>';
        $html .= '</tr>';

        //Save button
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<input type="submit" name="control_save" id="control_save" class="mcisc_blue_btn" value="' . esc_html( __( 'Save', 'wp-shelly-control' ) ) . '">';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '</table>';
        $html .= wp_nonce_field( 'mcisc_control_nonce', 'mcisc_control_nonce' );
        $html .= '</form>';

        $html .= '<hr>';
        $html .= "<p class='mcisc_control_permissions_notes'>It's strongly recommended to only authorize only trusted users and roles to control your devices.</p>";
        $html .= '<hr>';

        return $html;
    }

}
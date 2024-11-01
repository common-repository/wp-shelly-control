<?php
namespace MciControlShellyDevices\shared;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscHelpers
{

    public static function error_message( $message, $add_margin = false )
    {
        $add_margin = $add_margin ? 'mcisc_panel_notice_margin' : '';

        echo '<div class="notice notice-error is-dismissible ' . esc_attr( $add_margin ) . '">';
        echo '<p>' . esc_html( sprintf( $message ) ) . '</p>';
        echo '</div>';
    }

    public static function success_message( $message, $add_margin = false )
    {
        $add_margin = $add_margin ? 'mcisc_panel_notice_margin' : '';

        echo '<div class="notice mcisc_panel_notice notice-success is-dismissible ' . esc_attr( $add_margin ) . '">';
        echo '<p>' . esc_html( sprintf( $message ) ) . '</p>';
        echo '</div>';
    }

    public static function warning_message( $message, $add_margin = false )
    {
        $add_margin = $add_margin ? 'mcisc_panel_notice_margin' : '';

        echo '<div class="notice mcisc_panel_notice notice-warning is-dismissible ' . esc_attr( $add_margin ) . '">';
        echo '<p>' . esc_html( sprintf( $message ) ) . '</p>';
        echo '</div>';
    }

}

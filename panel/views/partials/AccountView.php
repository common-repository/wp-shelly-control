<?php
namespace MciControlShellyDevices\panel\views\partials;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\accounts\models\MciscAccount;

class MciscAccountView
{
    public function print_account()
    {
        if ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) ) {

            $account_db = new MciscAccount();
            $accounts   = $account_db->get_all();

            $html = '<div class="mcisc_account_wrap">';
            $html .= '<form method="post" action="">';
            $html .= '<table class="mcisc_account_list">';
            $html .= '<tr class="mcisc_account_row_header">';
            $html .= '<th>' . esc_html( __( 'Name', 'wp-shelly-control' ) ) . '</th>';
            $html .= '<th>' . esc_html( __( 'Cloud server', 'wp-shelly-control' ) ) . '</th>';
            $html .= '<th>' . esc_html( __( 'Authorization Cloud Key', 'wp-shelly-control' ) ) . '</th>';
            $html .= '<th></th>';
            $html .= '<tr />';

            foreach ( $accounts as $account ) {

                $account_name = $account_db->get_name( $account['id'] ) ? $account_db->get_name( $account['id'] ) : 'Shelly Account ' . $account['id'];

                $html .= '<tr class="mcisc_account_item">';
                $html .= '<td class="mcisc_account_name">' . esc_html( $account_name ) . '</td>';
                $html .= '<td class="mcisc_account_cloudserver">' . esc_html( $account_db->get_cloudserver( $account['id'] ) ) . '</th>';
                $html .= '<td class="mcisc_account_cloudkey">****************************</th>';
                $html .= '<td class="mcisc_account_delete"><span class="dashicons dashicons-trash"></span><input type="submit" name="delete_account_' . esc_attr( $account['id'] ) . '" value="Delete account"></td>';
                $html .= '</tr>';
            }
            $html .= '</table>';

            if ( count( $accounts ) <= 0 ) {
                $html .= '<p class="mcisc_first_account">' . esc_html( __( "If you don't know where to find the data in your Shelly account", 'wp-shelly-control' ) ) . ' ' . '<a href="?page=mcisc-help">Click here</a>' . '</p>';
                $html .= '<p class="mcisc_first_account">' . esc_html( __( 'Add your first Shelly account to WordPress. You only need your "Authorization Cloud Key" and the "url from the server"', 'wp-shelly-control' ) ) . '</p>';
                $html .= '<p class="mcisc_first_account"><span class="mcisc_arrows">↓↓↓</span></p>';
            }
            $html .= '<table class="mcisc_account_form">';
            $html .= '<tr>';
            $html .= '<td><label for="account_name" class="mcisc_settings_label">' . esc_html( __( 'Name', 'wp-shelly-control' ) ) . '</label></td>';
            $html .= '<td><input type="text" name="account_name" id="account_name" value="" placeholder="' . esc_html( __( 'Alias name (optional)', 'wp-shelly-control' ) ) . '"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><label for="account_cloudkey" class="mcisc_settings_label">' . esc_html( __( 'Authorization Cloud Key', 'wp-shelly-control' ) ) . '</label></td>';
            $html .= '<td><input type="password" name="account_cloudkey" id="account_cloudkey" value="" placeholder="' . esc_html( __( 'Shelly app: User settings / Authorization Cloud Key', 'wp-shelly-control' ) ) . '" ></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><label for="account_cloudserver" class="mcisc_settings_label">' . esc_html( __( 'Cloud Server', 'wp-shelly-control' ) ) . '</label></td>';
            $html .= '<td><input type="text" name="account_cloudserver" id="account_cloudserver" value="" placeholder="' . esc_html( __( 'Shelly app: User settings / Authorization Cloud Key (i.e: https://shelly-37-eu.shelly.cloud)', 'wp-shelly-control' ) ) . '"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td><input type="submit" name="account_save" id="account_save" value="' . esc_html( __( 'Save Shelly Account', 'wp-shelly-control' ) ) . '"></td>';
            $html .= '</tr>';

            $html .= '</table>';
            $html .= wp_nonce_field( 'mcisc_account_nonce', 'mcisc_account_nonce', true, false );
            $html .= '</form>';
            $html .= '</div>';

            return $html;
        }

    }
}
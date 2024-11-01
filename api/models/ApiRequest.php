<?php
namespace MciControlShellyDevices\api\models;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscApiRequest
{

    public function post( $cloudserver, $cloudkey, $action, $device_id, $channel = 0, $turn = null )
    {
        //If device_id have _1 end, delete this end
        if ( isset( $device_id ) ) {
            if ( substr( $device_id, -2 ) == '_1' ) {
                $device_id = substr( $device_id, 0, -2 );
            }
        }

        $url = sanitize_text_field( $cloudserver ) . sanitize_text_field( $action );
        $url = sanitize_url( $url );

        $args = [
            'method'           => 'POST',
            'timeout'          => 5,
            'redirection'      => 5,
            'httpversion'      => '1.0',
            'blocking'         => true,
            'data_format'      => 'form-data',
            'https_ssl_verify' => true,
            'encrypt'          => true,
            'headers'          => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body'             => [
                'id'       => sanitize_text_field( $device_id ),
                'auth_key' => sanitize_text_field( $cloudkey ),
                'channel'  => sanitize_text_field( $channel ),
                'turn'     => sanitize_text_field( $turn ),
            ],
        ];
        $response              = wp_remote_post( $url, $args );
        $body                  = wp_remote_retrieve_body( $response );
        $http_response         = wp_remote_retrieve_response_code( $response );
        $http_response_message = wp_remote_retrieve_response_message( $response );

        if ( $http_response == 200 ) {

            $data = json_decode( $body );

            return $data;

        } else {

            $fixed_message = __( 'Error: ', 'wp-shelly-control' );
            $error_message = ' (' . $http_response . ' ' . $http_response_message . ')';
            $error_message = !empty( $http_response ) || !empty( $http_response_message ) ? $fixed_message . $error_message : $fixed_message;
            $error_message .= '.Device_id: ' . $device_id . ' Action: ' . $action;

            // MciscHelpers::warning_message( $error_message );

            return false;
        }
    }

}

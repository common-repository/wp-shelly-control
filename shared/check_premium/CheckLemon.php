<?php
namespace MciControlShellyDevices\shared\check_premium;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscCheckLemon
{
    private $plugin_slug;
    private $product_id;
    private $license_key;
    private $instance_id;

    public function __construct()
    {
        if ( MCISC_REAL_ENVIRONMENT == true ) {
            $this->product_id = '102187'; // Real product ID = 102187
        } else {
            $this->product_id = '101900'; // Test product ID = 101900
        }
        $this->plugin_slug = 'sc';
        $this->license_key = sanitize_text_field( get_option( 'mci' . $this->plugin_slug . '_lemon_license_key' ) );
        $this->instance_id = sanitize_text_field( get_option( 'mci' . $this->plugin_slug . '_lemon_instance_id' ) );
    }

    // CHECK IF LICENSE INSTANCE IS ALREADY ACTIVATED OR IF DOMAIN IS DUPLICATED
    public function instance_exists()
    {
        $url  = 'https://api.lemonsqueezy.com/v1/licenses/validate';
        $args = [
            'body' => [
                'license_key' => $this->license_key,
                'instance_id' => $this->instance_id,
            ],
        ];
        $response               = wp_remote_post( $url, $args );
        $retrieve               = wp_remote_retrieve_body( $response );
        $retrieve               = json_decode( $retrieve, true );
        $retrieve_response_code = wp_remote_retrieve_response_code( $response );

        $already_exists = false;

        if ( $retrieve_response_code == '200' && $retrieve['instance'] !== null ) {

            if ( $retrieve['instance']['id'] == $this->instance_id || $retrieve['instance']['name'] == $_SERVER['SERVER_NAME'] ) {

                $already_exists = true;
            }
        }
        return $already_exists;
    }

    // ACTIVATE LICENSE
    public function activate( $lemon_license_key )
    {
        $url  = 'https://api.lemonsqueezy.com/v1/licenses/activate';
        $args = [
            'body' => [
                'license_key'   => sanitize_text_field( $lemon_license_key ),
                'instance_name' => $_SERVER['SERVER_NAME'],
            ],
        ];
        $response = wp_remote_post( $url, $args );

        $retrieve          = wp_remote_retrieve_body( $response );
        $retrieve          = json_decode( $retrieve, true );
        $retrieve_response = wp_remote_retrieve_response_code( $response );

        if ( $retrieve_response == 200 ) {

            $this->license_key = $retrieve['license_key']['key'];
            $this->instance_id = $retrieve['instance']['id'];

            update_option( 'mci' . $this->plugin_slug . '_lemon_license_key', sanitize_text_field( $this->license_key ) );
            update_option( 'mci' . $this->plugin_slug . '_lemon_instance_id', sanitize_text_field( $this->instance_id ) );
            return true;
        } else {

            return false;
        }
    }

// VALIDATE LICENSE
    public function validate()
    {
        $url  = 'https://api.lemonsqueezy.com/v1/licenses/validate';
        $args = [
            "headers" => [
                "Accept" => "application/json",
            ],
            'body'    => [
                'license_key' => $this->license_key,
                'instance_id' => $this->instance_id,
            ],
        ];
        $response               = wp_remote_post( $url, $args );
        $retrieve               = wp_remote_retrieve_body( $response );
        $retrieve               = json_decode( $retrieve, true );
        $retrieve_response_code = wp_remote_retrieve_response_code( $response );
        $lemon_product_id       = !empty( $retrieve['meta'] ) ? $this->validate_product_id( $retrieve['meta']['product_id'] ) : false;

        if ( $retrieve == null ) {
            return "server_error";
        }

        if ( $retrieve != null && !empty( $retrieve['errors'] ) ) {
            return false;
        }
        //Check if license valid==true, status="active" and product_id is correct in LemonSqueezy
        //The license status appears in line with Expires & limit in License details (top left corner)
        if ( !empty( $retrieve['valid'] ) && $retrieve['valid'] && $retrieve['license_key']['status'] == 'active' && $this->validate_product_id( $lemon_product_id ) ) {
            return true;
        } else {
            return false;
        }
    }

    public function validate_product_id( $lemon_product_id )
    {
        if ( $lemon_product_id == $this->product_id ) {

            return true;

        } else {

            $this->deactivate();
            return false;
        }
    }

    public function deactivate()
    {
        $url  = 'https://api.lemonsqueezy.com/v1/licenses/deactivate';
        $args = [
            'body' => [
                'license_key' => sanitize_text_field( $this->license_key ),
                'instance_id' => $this->instance_id,
            ],
        ];
        $response = wp_remote_post( $url, $args );

        $retrieve          = wp_remote_retrieve_body( $response );
        $retrieve          = json_decode( $retrieve, true );
        $retrieve_response = wp_remote_retrieve_response_code( $response );

        $deactivated = false;

        if ( $retrieve_response == 200 ) {
            $deactivated = $retrieve['deactivated'];
            if ( $deactivated ) {

                update_option( 'mcisc_auth_premium', '0' );
                update_option( 'mci' . $this->plugin_slug . '_lemon_license_key', '' );
            }
        }

        return $deactivated;
    }

}

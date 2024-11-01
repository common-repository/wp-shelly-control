<?php
namespace MciControlShellyDevices\shared;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscEncryption
{
    private static $encryption_key;

    public static function init()
    {
        self::$encryption_key = get_option( 'mcisc_encryption_key' );
    }

    private static function generate_iv()
    {
        return openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
    }

    public static function encrypt( $value )
    {
        self::init();
        $iv = self::generate_iv();

        $encrypted = openssl_encrypt( $value, 'aes-256-cbc', self::$encryption_key, OPENSSL_RAW_DATA, $iv );

        $iv_base64        = base64_encode( $iv );
        $encrypted_base64 = base64_encode( $encrypted );

        return $iv_base64 . ':' . $encrypted_base64;
    }

    public static function decrypt( $value )
    {
        self::init();

        $parts = explode( ':', $value );
        if ( count( $parts ) !== 2 ) {
            return false; // Invalid cypher value
        }

        $iv        = base64_decode( $parts[0] );
        $encrypted = base64_decode( $parts[1] );

        $decrypted = openssl_decrypt( $encrypted, 'aes-256-cbc', self::$encryption_key, OPENSSL_RAW_DATA, $iv );

        return $decrypted;
    }
}

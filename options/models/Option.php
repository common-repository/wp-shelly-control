<?php
namespace MciControlShellyDevices\options\models;

if ( !defined( 'ABSPATH' ) ) {exit;}

class MciscOption
{
    private $key;
    private $value;

    public function save( $key, $value )
    {
        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        $this->key   = sanitize_text_field( $key );
        $this->value = sanitize_text_field( $value );

        if ( $this->exists( $this->key ) ) {

            $update = $this->update( $this->key );
            return $update;

        } else {

            $add = $this->add();
            return $add;
        }
    }

    public function add_if_not_exists( $key, $value )
    {
        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        $this->key   = sanitize_text_field( $key );
        $this->value = sanitize_text_field( $value );

        if ( !$this->exists( $this->key ) ) {

            $add = $this->add();
            return $add;
        }
    }

    private function add()
    {
        $options = $this->get_all();

        $options[] = [
            'key'   => sanitize_text_field( $this->key ),
            'value' => sanitize_text_field( $this->value ),
        ];

        update_option( 'mcisc_options', serialize( $options ) );

        return true;
    }

    private function update( $option_key )
    {
        $options = $this->get_all();

        if ( empty( $options ) ) {
            $options = [];
        }

        foreach ( $options as $key => $option ) {
            if ( $option['key'] == $option_key ) {

                $options[$key]['value'] = sanitize_text_field( $this->value );
            }
        }

        update_option( 'mcisc_options', serialize( $options ) );

        return true;
    }

    public function get_all()
    {
        $options = get_option( 'mcisc_options' );

        if ( $options == null || empty( $options ) ) {
            return [];
        }

        return unserialize( $options );
    }

    public function get( $key )
    {
        $options = $this->get_all();

        if ( empty( $options ) ) {return false;}

        foreach ( $options as $option ) {
            if ( $option['key'] == $key ) {
                return sanitize_text_field( $option['value'] );
            }
        }
        return false;
    }

    public function delete( $key )
    {
        if ( !is_user_logged_in() || !is_admin() || !current_user_can( 'manage_options' ) ) {return false;}

        $options = $this->get_all();

        if ( empty( $options ) ) {return false;}

        foreach ( $options as $index => $option ) {

            if ( $option['key'] == $key ) {
                unset( $options[$index] );
            }
        }

        update_option( 'mcisc_options', serialize( $options ) );

        return true;
    }

    private function exists( $key )
    {
        $options = $this->get_all();

        if ( empty( $options ) ) {return false;}

        foreach ( $options as $option ) {
            if ( $option['key'] == $key ) {
                return true;
            }
        }
        return false;
    }

}

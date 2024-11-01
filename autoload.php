<?php
if ( !defined( 'ABSPATH' ) ) {exit;}

function mcisc_autoload( $class_name )
{

    $class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name ); //Replace '\' to '/' to load namespaces classes
    $class_name = str_replace( 'Mcisc', '', $class_name ); //Remove 'Mcisc' prefix to named files without it
    $class_name = str_replace( 'MciControlShellyDevices', '', $class_name );

    if ( file_exists( MCISC_PLUGIN_DIR . $class_name . '.php' ) ) {

        require_once MCISC_PLUGIN_DIR . $class_name . '.php';

    }

}

spl_autoload_register( 'mcisc_autoload' );

<?php
/**
 * Plugin Name: Control Shelly Devices
 * Plugin URI: https://mci-desarrollo.es/control-shelly-devices/?lang=en
 * Author: MCI Desarrollo
 * Author URI: https://mci-desarrollo.es
 * Version: 1.2.2
 * Text Domain: wp-shelly-control
 * Description: Control your Shelly remote devices from your WordPress site
 */
if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\MciscMaster;
use MciControlShellyDevices\shared\MciscActivate;
use MciControlShellyDevices\shared\MciscUninstall;

//======================================================================
define( 'MCISC_VERSION', '1.2.2' );
define( 'MCISC_REAL_ENVIRONMENT', true );
//======================================================================

define( 'MCISC_FORCE_REMOVE_RED', false );

define( 'MCISC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MCISC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MCISC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'MCISC_PLUGIN_SLUG', 'wp-shelly-control' );
define( 'MCISC_PLUGIN_NAME', 'Control Shelly Devices' );

require_once MCISC_PLUGIN_DIR . 'autoload.php';

//======================================================================
// Activate plugin function
function mcisc_activate()
{
    new MciscActivate;
}
register_activation_hook( __FILE__, 'mcisc_activate' );

//======================================================================
// Execute activation if the version is different
if ( MCISC_VERSION != get_option( 'mcisc_version' ) || !get_option( 'mcisc_options' ) ) {
    mcisc_activate();
}

// Load plugin
new MciscMaster;

//======================================================================
// Uninstall plugin function
register_uninstall_hook( __FILE__, 'mcisc_uninstall' );

function mcisc_uninstall()
{
    new MciscUninstall;
}

//======================================================================

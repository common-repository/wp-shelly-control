'use strict';

class MciscDeviceWpactive {

    constructor() {

        if ( typeof mcisc_wpactive !== 'undefined' ) {
            this.send_save_request();
        }
    }

    send_save_request() {


        jQuery( '.mcisc_wp_active' ).click( function () {

            let device_id = this.id.replace( 'mcisc_wp_active_', '' );
            var checkbox = jQuery( '#mcisc_wp_active' + device_id );
            var device_box = document.getElementById( device_id );

            //check if checkbox is checked or not
            if ( jQuery( this ).is( ':checked' ) ) {
                device_box.classList.add( 'mcisc_device_active' );
                device_box.classList.remove( 'mcisc_device_layer' );
                var wp_active = '1';

            } else {
                device_box.classList.remove( 'mcisc_device_active' );
                device_box.classList.add( 'mcisc_device_layer' );
                var wp_active = '0';
            }

            jQuery.ajax( {

                url: mcisc_wpactive.ajax_url_wpactive,
                type: 'POST',
                data: {
                    action: 'mcisc_device_wpactive',
                    nonce: mcisc_wpactive.wpactive_nonce,
                    device_id: device_id,
                    wp_active: wp_active,
                },
                success: function ( response ) {

                },
                error: function ( error ) {
                    console.log( error );
                }
            } );
        } );
    }
}

jQuery( document ).ready( function ( $ ) {

    new MciscDeviceWpactive();

} );
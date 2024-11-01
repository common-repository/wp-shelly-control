'use strict';

class MciscSwitch {

    constructor() {

        if (
            ( typeof mcisc_get_status !== 'undefined' && typeof mcisc_get_status.shelly_control_page !== 'undefined' && mcisc_get_status.shelly_control_page ) ||
            ( typeof mcisc_shortcode_page !== 'undefined' && typeof mcisc_shortcode_page.shortcode_page !== 'undefined' && mcisc_shortcode_page.shortcode_page )
        ) {
            this.switch();
        }
    }

    switch() {

        jQuery( '.mcisc_device_button' ).click( function () {

            // clear intervals from mcisc_control_get_status.js file 
            for ( let i = 1; i < 99999; i++ ) {
                window.clearInterval( i );
            }

            let device_id = this.id.replace( 'mcisc_ison_btn_', '' );
            let old_status = this.classList.contains( 'mcisc_device_ison' ) ? 1 : 0;
            let power_ico = this.querySelector( '#mcisc_power_ico' );

            jQuery.ajax( {
                url: mcisc_switch.ajax_url,
                type: 'POST',
                data: {
                    action: 'mcisc_switch',
                    nonce: mcisc_switch.nonce,
                    device_id: device_id,
                    old_status: old_status,
                },
                beforeSend: function () {
                    power_ico.src = mcisc_switch.plugin_url + 'front/assets/img/spinner.gif';
                },
                success: function ( response ) {

                    let device_status = response;
                    let mcisc_device_button = document.getElementById( 'mcisc_ison_btn_' + device_id );
                    power_ico.src = mcisc_switch.plugin_url + 'front/assets/img/power_ico.png';
                    let mcisc_device_status = document.getElementById( 'mcisc_device_status_' + device_id );

                    if ( device_status == true ) {

                        if ( mcisc_device_button.classList.contains( 'mcisc_device_ison' ) ) {

                            mcisc_device_button.classList.remove( 'mcisc_device_ison' );
                            mcisc_device_button.classList.add( 'mcisc_device_isoff' );
                            mcisc_device_status.innerHTML = 'OFF';

                        } else {
                            mcisc_device_button.classList.remove( 'mcisc_device_isoff' );
                            mcisc_device_button.classList.add( 'mcisc_device_ison' );
                            mcisc_device_status.innerHTML = 'ON';
                        }
                        // reactivate intervals from mcisc_control_get_status.js file after 3 seconds
                        setTimeout( () => {
                            new MciscControlGetStatus();
                        }, 3000 );
                    }
                },
                error: function ( response ) {
                    power_ico.src = mcisc_switch.plugin_url + 'front/assets/img/power_ico.png';
                    console.log( 'Error: ' + response.error );
                }
            } );
        }
        );
    }
}

jQuery( document ).ready( function ( $ ) {

    new MciscSwitch();

} );


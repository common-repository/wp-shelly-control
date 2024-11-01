'use strict';

class MciscControlGetStatus {

    constructor() {

        if (
            ( typeof mcisc_get_status !== 'undefined' && typeof mcisc_get_status.shelly_control_page !== 'undefined' && mcisc_get_status.shelly_control_page ) ||
            ( typeof mcisc_shortcode_page !== 'undefined' && typeof mcisc_shortcode_page.shortcode_page !== 'undefined' && mcisc_shortcode_page.shortcode_page )
        ) {

            this.reload_devices_status();

            let interval_reload = setInterval( () => this.reload_devices_status(), 3000 );

            window.addEventListener( 'beforeunload', function ( e ) {
                clearInterval( interval_reload );
            } );
            window.addEventListener( 'unload', function ( e ) {
                clearInterval( interval_reload );
            } );
            window.addEventListener( 'submit', function ( e ) {
                clearInterval( interval_reload );
            } );
            window.addEventListener( 'visibilitychange', function ( e ) {
                clearInterval( interval_reload );
            } );
            window.addEventListener( 'focus', ( e ) => {
                interval_reload = setInterval( () => this.reload_devices_status(), 3000 );
            } );
        }
    }

    reload_devices_status() {

        jQuery.ajax( {
            url: mcisc_get_status.ajax_url,
            type: 'POST',
            data: {
                action: 'mcisc_get_status',
                nonce: mcisc_get_status.nonce,
            },
            success: function ( response ) {

                let device_status = response;

                for ( let i = 0; i < device_status.length; i++ ) {

                    let id = device_status[i]['id'];
                    let all_status = device_status[i];
                    let ison = device_status[i]['ison'];
                    let online = device_status[i]['online'];
                    let power = device_status[i]['power'];

                    let mcisc_ison_btn = document.getElementById( 'mcisc_ison_btn_' + id );
                    let mcisc_device_status = document.getElementById( 'mcisc_device_status_' + id );
                    let mcisc_device_online = document.getElementById( 'mcisc_online_' + id );
                    let mcisc_device_power = document.getElementById( 'mcisc_power_' + id );

                    if ( ison == 1 ) {
                        mcisc_ison_btn.classList.add( 'mcisc_device_ison' );
                        mcisc_ison_btn.classList.remove( 'mcisc_device_isoff' );
                        mcisc_device_status.innerHTML = 'ON';

                    } else if ( ison == 0 ) {
                        mcisc_ison_btn.classList.add( 'mcisc_device_isoff' );
                        mcisc_ison_btn.classList.remove( 'mcisc_device_ison' );
                        mcisc_device_status.innerHTML = 'OFF';
                    }

                    if ( online == true ) {
                        mcisc_device_online.classList.add( 'mcisc_active' );
                    } else {
                        mcisc_device_online.classList.remove( 'mcisc_active' );
                        mcisc_ison_btn.classList.remove( 'mcisc_device_ison' );
                        mcisc_ison_btn.classList.remove( 'mcisc_device_isoff' );
                    }

                    if ( power != null ) {
                        let span = mcisc_device_power.querySelector( 'span' );
                        span.innerHTML = power + ' W';
                    }
                }

            },
            error: function ( error ) {

                console.log( 'Error: ' + error.message );
            }
        } );
    }
}

jQuery( document ).ready( function ( $ ) {

    new MciscControlGetStatus();

} );
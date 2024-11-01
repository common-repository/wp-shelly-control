<?php
namespace MciControlShellyDevices\panel\controllers;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\panel\views\MciscHelpView;

class MciscHelp
{

    public function init()
    {
        $help_view = new MciscHelpView();
        $help_view = $help_view->init();
    }

}

<?php
namespace MciControlShellyDevices;

if ( !defined( 'ABSPATH' ) ) {exit;}

use MciControlShellyDevices\admin\MciscAdmin;
use MciControlShellyDevices\front\MciscFront;
use MciControlShellyDevices\shared\check_premium\MciscCheckMaster;

class MciscMaster
{

    public function __construct()
    {
        $this->check_premium();

        $this->front();
        $this->admin();
    }

    public function check_premium()
    {
        $check_premium = new MciscCheckMaster();
        $check_premium->init();
    }

    public function front()
    {

        $front = new MciscFront();
        $front->init();

    }

    public function admin()
    {
        $admin = new MciscAdmin();
        $admin->init();

    }

}

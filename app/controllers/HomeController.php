<?php

use \im\Primitive\Container;
use \im\Primitive\String;

class HomeController extends BaseController
{
    public function index()
    {
        $path = public_path('portals') . '/settings/self_registered_guest_portal.json';
        $settings = new \im\Primitive\Container( $path );

        $_ = 0;
    }
}

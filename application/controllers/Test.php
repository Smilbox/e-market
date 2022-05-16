<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test extends CI_Controller
{
    public function index()
    {
        $test[] = 1;
        $test[] = 2;
        print_r($test);
    }
}

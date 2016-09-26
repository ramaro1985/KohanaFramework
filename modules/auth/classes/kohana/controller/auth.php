<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Controller_Auth extends Controller
{
    protected $auth;
    
    public function before()
    {
        $this->auth = Auth::instance();
    }
    
    public function action_index(){}
    
    abstract public function action_login();
    
    abstract public function action_logout();
}
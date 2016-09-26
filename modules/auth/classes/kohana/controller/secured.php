<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Controller_Secured extends Controller
{
    protected $auth;
    
    public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
        
        $this->auth = Auth::instance();
	}
}
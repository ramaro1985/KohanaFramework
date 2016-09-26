<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Secured extends Kohana_Controller_Secured
{
    protected $auth;
    
    public function __construct(Request $request, Response $response)
	{
            parent::__construct($request, $response);

            if(($this->auth->logged_in() !== TRUE) AND !$this->request->is_ajax())
            {
                URL::redirect('auth/login');
            }
            elseif(($this->auth->logged_in() !== TRUE) AND $this->request->is_ajax())
            {
                throw new HTTP_Exception_403();
            }
            else
            {
                if(!$this->auth->is_allowed_to())
                {
                    throw new HTTP_Exception_401('access_denied');
                  
                }
               
            }
	}
}
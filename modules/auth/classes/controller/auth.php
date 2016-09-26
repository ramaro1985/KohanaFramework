<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Auth extends Kohana_Controller_Auth
{

    public function before()
    {
        $this->admin = Model::factory('admin');
        $this->auth = Kohana_Auth::instance();
    }
    
    public function action_index()
    {
        $this->action_login();
    }
    
    public function action_login()
    {
        session_start();
        $logged = FALSE;
        if($this->auth->logged_in() !== TRUE)
        {
            $post = $this->request->post();
            
            if (empty($post) AND !$this->request->is_ajax())
            {
                $this->response->body(View::factory('auth/login'));
            }
            else
            {
                $username = $this->request->post('username');
                $password = $this->request->post('password');
                
                $logged = $this->auth->login($username, $password);

                if($logged === TRUE)
                {
                    $_SESSION['user'] = $username;
                    $this->admin->actualizar_login($username);
                    $title = __('auth_login_successful_title');
                    $msg = __('auth_login_successful');
                }
                else
                {
                    $title = __('auth_login_failed_title');
                    $msg = __('auth_login_failed');
                }
            
                echo json_encode(array(
                    'success' => $logged, 
                    'msg' => array(
                        'title' => $title,
                        'msg'   => $msg
                    )
                ));
            }
        }
        else
        {
            URL::redirect(URL::base());
        }
    }
    
    public function action_logout()
    {
        $this->auth->logout();
        URL::redirect(URL::site('/auth/login'));
    }
}
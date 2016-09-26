<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Extop extends Controller_Secured
{
    private $extop;
    
    public function before()
    {
        $this->extop = Model::factory('extop');
        $this->module = Model::factory('module');
    }
     
    public function action_index()
    {
        $values = $this->extop->initialize();
        $this->response->body(View::factory('extop/main')->bind('extop', $values));
    }
    
    public function action_load_module()
    {
        $jsid = $this->request->post('module_id');
        $response = $this->extop->load_module($jsid);
        $this->response->body($response);

     
    }

} // End Desktop
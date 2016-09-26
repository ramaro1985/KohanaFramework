<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Preferencias extends Controller_Secured
{
    private $wallpaper;
    private $preference;
    
    public function before()
    {
        $this->wallpaper = Model_Database::factory('wallpaper');
        $this->preference = Model_Database::factory('preference');
        $this->theme = Model_Database::factory('theme');
    }
     
    public function action_index()
    {
    }
    
    public function action_save()
    {
        $item = $this->request->post('item');
        
        if(empty($item))
        {
            throw new HTTP_Exception_500('post_data_empty');
        }
        
        $data = json_decode(stripslashes($this->request->post('data')), TRUE);
        
        $preferences = array();
        
        if($item == 'shortcut' OR $item == 'quickstart' OR $item == 'autorun')
        {
            $preferences['launchers'][$item] = $data;
        }
        else
        {
            $preferences[$item] = $data;
        }
        
        $user = Auth::instance()->get_user();
        
        $success = $this->preference->save_definition_data($user->user_id, $preferences);
        
        echo json_encode(array('success' => $success));
    }
    
    public function action_load_wallpapers()
    {
        echo json_encode($this->wallpaper->fill_data_view());
    }
    
    public function action_load_themes()
    {
        echo json_encode($this->theme->fill_data_view());
    }

} // End Controller_Preferences class
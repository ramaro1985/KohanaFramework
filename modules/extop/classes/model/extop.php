<?php defined('SYSPATH') or die('No direct script access.');

class Model_Extop extends Model_Database
{
    private $config;
    private $theme;
    private $module;
    private $preference;
    private $wallpaper;
    
    public function __construct()
    {
        parent::__construct();
        $this->config = Kohana::$config->load('extop');
        $this->theme = parent::factory('theme');
        $this->module = parent::factory('module');
        $this->preference = parent::factory('preference');
        $this->wallpaper = parent::factory('wallpaper');
        $this->admin = parent::factory('admin');
    }
    
    public function initialize()
    {
        $values = new stdClass();
        $values->modules = $this->module->get_all_definition_data();
        $values->member_info = $this->_get_user_info();
        $values->privileges = $this->_get_privileges();
        $values->config = $this->_get_visual_config();
        $values->locale = $this->get_locale();
        $values->list_privileges = $this->get_list_privileges();
        return $values;
    }
    
    public function get_locale()
    {
        return array(
            'common' => $this->get_common_locale(),
            'extop'  => $this->get_extop_locale(),
        );
    }
    
    public function get_extop_locale()
    {
        $filepath = MODPATH.'extop/i18n/'.I18n::lang().EXT;
        return Kohana::load($filepath);
    }
    
    public function get_common_locale()
    {
        $filepath = MODPATH.'common/i18n/'.I18n::lang().EXT;
        return Kohana::load($filepath);
    }
    
    public function load_module($jsid)
    {
        return $this->module->get_module_files($jsid);
    }
    
    public function get_module_name($module_id)
    {
        $this->db->select('em.name');
        $this->db->from('extop_modules AS em');
        $this->db->where('em.jsid', $module_id);
        $this->db->limit(1);
        $result = $this->db->get()->result();
        $name = $result[0]->name;
        return $name;
    }
    
    private function _get_user_info()
    {
        return Auth::instance()->get_user();
    }
    
    public function _get_privileges()
    {
        return Auth::instance()->get_permissions();
    }
    
    
    private function _get_visual_config()
    {
        $user_info = $this->_get_user_info();
        
        $themes_dir = $this->config->get('themes_dir');
        $wallpapers_dir = $this->config->get('wallpapers_dir');
        
        $preference = $this->preference->get_definition_data($user_info->user_id);
        $theme = $this->theme->get_definition_data($preference->appearance->themeId);
        
        $preference->appearance->theme = new stdClass();
        $preference->appearance->theme->id = $preference->appearance->themeId;
        $preference->appearance->theme->name = $theme->name;
        $preference->appearance->theme->file = $themes_dir . $theme->file;
        
        unset($preference->appearance->themeId);
                
        $wallpaper = $this->wallpaper->get_definition_data($preference->background->wallpaperId);
        
        $preference->background->wallpaper = new stdClass(); 
        $preference->background->wallpaper->id = $preference->background->wallpaperId;
        $preference->background->wallpaper->name = $wallpaper->name;
        $preference->background->wallpaper->file = $wallpapers_dir.$wallpaper->file;
        
        unset($preference->appearance->wallpaperId);
        
        return $preference;
    }
    
     public function get_list_privileges()
     {
          $controller = array();
          $modules = array();
          $modulos = $this->admin->obtener_modulos();
          $j = 0;
          foreach($modulos as $modulo)
          {
              $mod = strstr($modulo->jsid, '-', true); 
              $dir = MODPATH . $mod.'\\classes\\controller';
              if(file_exists ($dir))
              {
                  $controller = null;
                  $i = 0;
                  $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
                  foreach($files as $file)
                  {
                      $filename = basename($file, ".php");
                      if(strncmp ($filename, 'extop', 5) > 0 || strncmp ($filename, 'extop', 5) < 0)
                      {
                          $content = file_get_contents($file);
                          $strarray = explode(" ", $content);
                          $actions = array();
                          $l = 0; 
                          for($k = 0; $k < count($strarray); $k++)
                          {
                            if(strncmp($strarray[$k], 'action_', 7) == 0)
                            {
                               $actionname = strstr(substr($strarray[$k], 7), '(', true);
                               if(strncmp($actionname, 'index', 5))
                               {
                                   $actions[$l] = array('text' => $actionname, 'iconCls' => 'auth-admin-group-editrol-icon');
                                   $l++;
                               }
                            }
                          }
                          if(count($actions) > 0)
                          {
                              $controller[$i] = array('text' => $filename, 'iconCls' => 'auth-admin-group-addrol-icon', 'actions' => $actions);
                              $i++;   
                          }
                      }
                   }
                $modules[$j] = array('text' => $modulo->jsid, 'iconCls' => 'auth-admin-group-addrol-icon', 'controller' => $controller);
                $j++;
              }
          }
          return $modules;  
       }
}
<?php defined('SYSPATH') or die('No direct script access.');

class Model_Module extends Model_Database
{
    public function get_by_jsid($jsid)
    {
        $this->db->select('em.*');
        $this->db->from('extop_modules AS em');
        $this->db->where(array('em.jsid' => trim($jsid), 'em.active' => 1));
        $this->db->limit(1);
        $result = $this->db->get()->result();
        
        $module = $result[0];
        $module->data = json_decode($module->data);
        return $module;
    }
    
    public function get_all_definition_data ()
    {
        $result = $this->db->select('em.jsid, em.data')
            ->from('extop_modules as em')
            ->where('em.active', 1)
            ->get();
        $count_result = $result->num_rows();
        $arr_result = $result->result_array();
        
        $response = array();
        
        foreach($arr_result as $k => $record)
        {
            $id = $record['jsid'];
            $m = json_decode($record['data']);
            $response[] = array(
                'id'            => $id,
                'type'          => $m->type,
                'className'     => $m->client->class,
                'locale'        => (isset($m->locale))?$this->get_locale($m->locale):$this->get_locale($m->basedir),
                'launcher'      => $m->client->launcher->config,
                'launcherPaths' => $m->client->launcher->paths
            );
        }
        
        return $response;        
    }

    public function get_locale($locale)
    {
        $table = array();
        
        if(is_string($locale))
        {
            $filepath = MODPATH.$locale.'/i18n/'.I18n::lang().EXT;
            if(is_file($filepath))
            {
                $table = Kohana::load($filepath);
            }
        }
        else
        {
            foreach ($locale AS $directory)
            {
                $filepath = MODPATH.$directory.'/i18n/'.I18n::lang().EXT;
                if(is_file($filepath))
                {
                    $table = array_merge($table, Kohana::load($filepath));
                }
            }
        }
        
        return $table;        
    }
    
    public function get_module_files($jsid)
    {
        $response = array(
            'javascript' => array(),
            'css'        => array(),
        );
        $module = $this->get_by_jsid($jsid);
        
        if(isset($module->data->addons))
        {
            $addons = $this->get_module_addons($module->data->addons);
            $response['javascript'] = array_merge($response['javascript'], 
                $addons['javascript']
            );
            $response['css'] = array_merge($response['css'], $addons['css']);
        }
        if(isset($module->data->client->javascript) AND !empty($module->data->client->javascript))
        {
            $response['javascript'] = array_merge($response['javascript'], 
                $this->get_javascript_files($module->data->client->javascript, 
                                $module->data->basedir)
            );
        }
        $response['css'] = array_merge($response['css'], 
            isset($module->data->client->css) ? $this->get_css_files(
                $module->data->client->css, 
                $module->data->basedir):array()
        );
                
        return json_encode($response);
    }
    
    public function get_module_addons($module_addons)
    {
        $addon_model = parent::factory('addon');
        $addons = array(
            'javascript' => array(),
            'css' => array()
        );
        
        foreach ($module_addons AS $addon)
        {
            $addon_config = $addon_model->get_by_jsid($addon->id);
            $addon_addons = array(
                'javascript' => array(),
                'css' => array()
            );
            
            if(isset($addon_config->data->addons)){
                $addon_addons = $this->get_module_addons($addon_config->data->addons);
                $addons['javascript'] = array_merge($addons['javascript'], 
                    $addon_addons['javascript']);
                $addons['css'] = array_merge($addons['css'],
                    $addon_addons['css']);
            }
            
            if(isset($addon_config->data->client->javascript) AND !empty($addon_config->data->client->javascript))
            {
                $addons['javascript'] = array_merge($addons['javascript'], 
                    $this->get_javascript_files($addon_config->data->client->javascript)
                );
            }
            $addons['css'] = array_merge($addons['css'], 
                isset($addon_config->data->client->css) ? $this->get_css_files(
                $addon_config->data->client->css):array()
            );
        }
        
        return $addons;
    }
    
    public function get_javascript_files($javascript_config, $basedir = 'common')
    {
        $javascript_files = array();
        
        foreach($javascript_config AS $javascript_directory)
        {
            foreach($javascript_directory->files AS $javascript_file)
            {
                if (isset($javascript_directory->directory))
                {
                    $src = URL::site(
                        'modules/' . $basedir . 
                        '/' . $javascript_directory->directory . 
                        '/' . $javascript_file
                    );
                    array_push($javascript_files, $src);
                }
                else
                {
                    $src = URL::site('modules/' . $basedir . '/' . $javascript_file);
                    array_push($javascript_files, $src);
                }
            }
        }
        
        return $javascript_files;        
    }
    
    public function get_css_files($css_config, $basedir = 'common')
    {
        $css_files = array();
        
        foreach($css_config AS $css_directory)
        {
            foreach($css_directory->files AS $css_file)
            {
                if (isset($css_directory->directory))
                {
                    $link = URL::site(
                        'modules/' . $basedir . '/' . $css_directory->directory . 
                        '/resources/css/' . $css_file
                    );
                    array_push($css_files, $link);
                }
                else
                {
                    $link = URL::site('modules/' . $basedir . '/resources/' . $css_file);
                    array_push($css_files, $link); 
                }
            }
        }
        
        return $css_files;
    }
}

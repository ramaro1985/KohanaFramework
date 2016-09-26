<?php defined('SYSPATH') or die('No direct script access.');

class Model_Theme extends Model_Database
{
    public function get_all()
    {
        $this->db->select('et.*');
        $this->db->from('extop_themes as et');
        $result = $this->db->get()->result_array();
        
        return (!empty($result))?$result:FALSE;
    }
    
    public function get_by_id($theme_id)
    {
        $this->db->select('et.*');
        $this->db->from('extop_themes as et');
        $this->db->where(array('et.theme_id' => $theme_id));
        $this->db->limit(1);
        $result = $this->db->get()->result_array();
        
        return (!empty($result))?$result[0]:FALSE;
    }
    
    public function get_definition_data($theme_id)
    {
        $theme = $this->get_by_id($theme_id);
        $definition = json_decode($theme['data']);
        
        return $definition;
    }
    
    public function fill_data_view()
    {
        $themes_dir = Kohana::$config->load('extop')->get('themes_dir');
        
        $items = $this->get_all();
        $data = array();
        
        foreach ($items as $k => $value)
        {
            $jsonData = json_decode($value['data']);
                      
            $data[$k]['theme_id'] = $value['theme_id'];
            $data[$k]['name'] = $jsonData->name;
            $data[$k]['thumbnail'] = $jsonData->thumbnail;
            $data[$k]['file'] = $themes_dir . $jsonData->file;
        }

        return array('data' => $data);
    }
}
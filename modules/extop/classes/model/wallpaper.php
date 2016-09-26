<?php defined('SYSPATH') or die('No direct script access.');

class Model_Wallpaper extends Model_Database
{
    
    public function get_all()
    {
        $this->db->select('ew.*');
        $this->db->from('extop_wallpapers as ew');
        $result = $this->db->get()->result_array();
        
        return (!empty($result))?$result:FALSE;
    }
    
    public function get_definition_data($wallpaper_id)
    {
        $wallpaper = $this->get_by_id($wallpaper_id);
        $definition = json_decode($wallpaper['data']);
        
        return $definition;
    }
    
    public function get_by_id($wallpaper_id)
    {
        $this->db->select('ew.*');
        $this->db->from('extop_wallpapers as ew');
        $this->db->where(array('ew.wallpaper_id' => $wallpaper_id));
        $this->db->limit(1);
        $result = $this->db->get()->result_array();
        
        return (!empty($result))?$result[0]:FALSE;
    }
    
    public function fill_data_view()
    {
        $wallpapers_dir = Kohana::$config->load('extop')->get('wallpapers_dir');
        
        $items = $this->get_all();
        $data = array();
        
        foreach ($items as $k => $value)
        {
            $jsonData = json_decode($value['data']);
                      
            $data[$k]['wallpaper_id'] = $value['wallpaper_id'];
            $data[$k]['name'] = $jsonData->name;
            $data[$k]['thumbnail'] = $wallpapers_dir . $jsonData->thumbnail;
            $data[$k]['file'] = $wallpapers_dir . $jsonData->file;
        }

        return array('data' => $data);
    }
}
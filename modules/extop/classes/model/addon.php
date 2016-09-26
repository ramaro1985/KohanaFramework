<?php defined('SYSPATH') or die('No direct script access.');

class Model_Addon extends Model_Database
{
    public function get_by_jsid($jsid)
    {
        $this->db->select('ca.*');
        $this->db->from('common_addons AS ca');
        $this->db->where(array('ca.jsid' => trim($jsid), 'ca.active' => 1));
        $this->db->limit(1);
        $result = $this->db->get()->result();
        
        $addon = $result[0];
        $addon->data = json_decode($addon->data);
        return $addon;
    }
}
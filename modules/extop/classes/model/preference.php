<?php defined('SYSPATH') or die('No direct script access.');

class Model_Preference extends Model_Database
{   
    public function get_definition_data($user_id, $as_array = FALSE)
    {
        $preferences = Kohana::$config->load('extop')
            ->get('preferences');
        $preferences = json_decode(json_encode($preferences), $as_array);
        
        if ($user_id)
        {
            $query = $this->db->from('extop_preferences AS ep')
                ->where('ep.user_id', $user_id)
                ->limit(1)->get();
            $result = $query->result();
            
            if(isset($result[0]->data) AND !empty($result[0]->data))
            {
                $preferences = json_decode($result[0]->data, $as_array);
            }
        }
        
        return $preferences;
    }
    
    public function save_definition_data($user_id, $data)
    {
        $preferences = $this->get_definition_data($user_id, TRUE);
        if(isset($data['launchers']))
        {
            $preferences['launchers'] = array_merge($preferences['launchers'], 
                $data['launchers']);
        }
        else
        {
            $preferences = array_merge($preferences, $data);
        }

        return $this->db->set('data', json_encode($preferences))
            ->where('user_id', $user_id)->update('extop_preferences');
    }
}
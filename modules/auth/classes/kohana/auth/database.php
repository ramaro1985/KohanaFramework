<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Database Auth driver.
 *
 * @package    Kohana/Auth
 * @author     Rafael Ernesto Espinosa Santiesteban <alvk4r@blackbird.org>
 * @copyright  Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license    http://kohanaframework.org/license
 */
class Kohana_Auth_Database extends Auth {
    
    protected $_db_config;
    
    protected $db;
    
    protected $_connection_group = 'default';
    
    public $users_table = 'auth_users';
    public $roles_table = 'auth_roles';
    public $acl_table = 'auth_acl';
    public $pivot_table = 'auth_users_roles';
    public $login_field = 'username';
    public $password_field = 'password';
    
    /**
     * Loads Session and configuration options.
     * @uses    DB::load
     * @return  void
     */
    public function __construct($config = array())
    {
        $this->_db_config = $config->get('db_config');
        
        if(!class_exists('DB'))
        {
            throw new Auth_Exception('auth_required_db_module');
        }
        
        $this->_connection_group = $this->_db_config['_connection_group'];
        $this->db = Db::load($this->_connection_group, TRUE);

        $this->users_table = $this->_db_config['users_table'];
        $this->roles_table = $this->_db_config['roles_table'];
        $this->acl_table = $this->_db_config['acl_table'];
        $this->pivot_table = $this->_db_config['pivot_table'];
        $this->login_field = $this->_db_config['login_field'];
        $this->password_field = $this->_db_config['password_field'];
        
        parent::__construct($config);
    }
        
    /**
     * Logs a user in.
     *
     * @param   string   username
     * @param   string   password
     * @param   boolean  enable autologin (not supported)
     * @return  boolean
     */
    protected function _login($username, $password, $remember)
    {
        $password = $this->hash((string) $password);
        
        $user = $this->db->select("au.*")
            ->from("{$this->users_table} AS au")
            ->where("au.{$this->login_field}", $username)
            ->limit(1)
            ->get()->result();
            
        if(! empty($user))
        {
            $user = $user[0];
            if($user->banned == 'no')
            {
               if($user->password === $password)
               {
                 unset($user->password);
                
                 $permissions = FALSE;
                
                 if($this->_config['authz'] === TRUE)
                 {
                    $roles = $this->db->select('ar.role_id, UPPER(ar.name) AS name, aa.data AS permissions')
                        ->from("{$this->roles_table} AS ar")
                        ->join("{$this->pivot_table} AS aur", "aur.role_id = ar.role_id", 'inner')
                        ->join("{$this->acl_table} AS aa", "aa.role_id = ar.role_id", 'inner')
                        ->where("aur.user_id = {$user->user_id}")
                        ->get()->result_array();
                    
                    $permissions = array();
                    $controller = array();
                    $module = array();
                    foreach ($roles AS $i => $role)
                    { 
                        $permissions = array_merge_recursive($permissions, json_decode($role['permissions'], true));
                        unset($roles[$i]['permissions']);
                    }

                    $user->roles = $roles;
                 }
                
                 return $this->complete_login($user, $permissions);
             }
             else
             {
                return FALSE;
             }    
          }
            
                     
        }
    }
    
    /**
     * Check if current logged user has permission.
     *
     * @return  boolean
     */
    public function is_allowed_to()
    {
        $actions = Kohana::$config->load('actions');
        $actions_list = $actions->get('actions');
        if($this->logged_in())
        {
            $controller = strtolower(Request::current()->controller());
            
            if($controller === 'extop')
            {
                return TRUE;
            }
            
            $action = strtolower(Request::current()->action());
            
            $permission = $this->get_permissions();
            $permissions = $permission[controller];
                for($i = 0; $i < count($permissions); $i++)
                {
                    if (array_key_exists($controller, $permissions[$i]))
                    {
                        //if($action == 'index' OR empty($action))
                        //{
                            //return TRUE;
                        //}
                        if(in_array($action, $actions_list) OR $action == 'index' OR empty($action))
                        {
                            return TRUE;
                        }

                        if (in_array($action, $permissions[$i][$controller]))
                        {
                            return TRUE;
                        }
                    }
                } 
         }
        return FALSE;
    }
    
    /**
     * Check if current logged user has role.
     *
     * @param   mixed       param   The role id or name.
     * @return  boolean
     */
    public function has_role($param)
    {
        if($this->logged_in())
        {
            $user = $this->get_user();
            $roles = $user->roles;
            
            foreach($roles AS $role)
            {
                if(in_array(strtoupper($param), $role))
                {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    
    /**
	 * Gets the currently logged in user permissions from the session.
	 * Returns NULL if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_permissions($default = NULL)
	{
		return $this->_session->get('auth_permissions', $default);
	}
    
    protected function complete_login($user, $permissions = FALSE)
	{
        
		// Regenerate session_id
		$this->_session->regenerate();

		// Store username in session
		$this->_session->set($this->_config['session_key'], $user);
               
        if($permissions !== FALSE AND is_array($permissions))
        {
            $this->_session->set('auth_permissions', $permissions);
        }

		return TRUE;
	}  
        
       
    
    public function password($username){}

    public function check_password($password){}
}
//End Kohana_Auth Database class
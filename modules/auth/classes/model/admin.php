<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin
 *
 * @author Tony
 */
class Model_Admin extends Model_Database{
    //put your code here
    
    
   
     public function __construct()
    {
        parent::__construct();
       
       
    }
    
   
//-------------------Inicia Roles------------------------------------------   
    public function lista_roles($limit, $start, $editar)
    {
        $data = $this->db->select('ar.*')
            ->from('auth_roles AS ar')
	    ->limit($limit, $start)
            ->order_by("ar.name", "asc")->get();
        
        $total = $data->num_rows();
        $data = $data->result_array(); 
        
        return array('total' => $total, 'data' => $data);
    }
    
    public function lista_roles_tree()
    {
        $roles = $this->db->select('ar.*')
            ->from('auth_roles AS ar')
	    ->get()->result();
      
        $children = array();
        for($i = 0; $i < count($roles); $i++)
        {
           $children[$i] = array('text' => $roles[$i]->name, 'leaf' => true, 'cls' => '', 'checked' => false, 'id' => $roles[$i]->role_id);
        }
        
        return $children;
        
    }
    
     public function lista_roles_tree_actualizar($usuario_id)
    {
        $roles = $this->db->select('ar.*')
            ->from('auth_roles AS ar')
	    ->get()->result();
        
        $role_ids = $this->db->select('aur.*') 
                   ->from('auth_users_roles AS aur')
                   ->where('aur.user_id', $usuario_id)
                   ->get()->result();
        $children = array();
        for($i = 0; $i < count($roles); $i++)
        {
          $children[$i] = array('text' => $roles[$i]->name, 'leaf' => true,
                            'checked' => false, 'cls' => '', 'id' => $roles[$i]->role_id );
        }
        for($i = 0; $i < count($role_ids); $i++)
        {
            for($j = 0; $j < count($children); $j++)
            {
                if($role_ids[$i]->role_id === $children[$j][id])
                {
                    $children[$j][checked] = true;
                } 
            }
             
         }
       
        return $children;
        
    }
    
    public function listar_roles_x_usuario($usuario_id)
    {
        $data = $this->db->select('rol.*') 
                         ->from('auth_users AS user', 'auth_roles AS rol')
                         ->join('auth_users_roles AS aur', 'aur.user_id = user.user_id' , 'inner')
                         ->join('auth_roles AS rol', 'rol.role_id = aur.role_id', 'inner')
                         ->where('aur.user_id', $usuario_id)->get();
        $total = $data->num_rows();
        $data = $data->result_array();
        return array('total' => $total, 'data' => $data);
    }




    public function existe_rol($nomrol)
    {
        $existe_rol = $this->db->select('rol.*')
                        ->from('auth_roles AS rol')
                        ->where("rol.name", $nomrol)
                        ->limit(1)
                        ->get()->result();
        if(empty ($existe_rol))
            return FALSE;
        else
            return TRUE;
    }


    public function insertar_rol($rolname, $descripcion)
    {
        
        $data = array('name' => strtoupper($rolname), 'description' => $descripcion);
        $insertado =  $this->db->insert('auth_roles', $data);
        if($insertado)
            return TRUE;
        else
            return FALSE;
    }
    
     public function editar_rol($idrol, $rolname, $descripcion)
     {
         $data = array(
		'name' => strtoupper($rolname) ,
		'description' => $descripcion ,
		);
          $this->db->where('role_id', $idrol);
          $actualizo = $this->db->update('auth_roles', $data); 
          if($actualizo)
              return TRUE;
          else
              return FALSE;
     }
     
     
     
     public function eliminar_rol($idrol)
     {
         return $this->db->delete('auth_roles', array('role_id' => $idrol));
          
     }
     
      public function existe_usuario_rol($idrol)
      {
          $query = $this->db->select('rol.*')
                  ->from('auth_users_roles AS rol')
                  ->where('rol.role_id', $idrol)
                  ->get()->result();
         if(!empty($query))
             return TRUE;
         else
             return FALSE;
      }
      
      public function existe_user_rol($rol_id, $usuario_id)
      {
           $query = $this->db->select('aur.user_role_id')
                  ->from('auth_users_roles AS aur')
                  ->where('aur.role_id', $rol_id)
                  ->where('aur.user_id', $usuario_id)
                  ->get()->result();
           return  $query[0]->user_role_id;
      }


      //-------------------Fin Roles------------------------------------------
 //
 //-------------------Inicia Usuarios------------------------------------------
 
      
    public function id_usuario($usuario)
    {
        $query = $this->db->select('user.*')
                        ->from('auth_users AS user')
                        ->where("user.username", $usuario)
                        ->limit(1)
                        ->get()->result();
        
        return  $query[0]->user_id;
    }
    
    
    public function nombre_usuario($usuario_id)
    {
        $usuario= $this->db->select('user.username')
                        ->from('auth_users AS user')
                        ->where("user.user_id", $usuario_id)
                        ->limit(1)
                        ->get()->result();
        return $usuario;
    }
    
    
    public function existe_usuario($usuario)
    {
        $query = $this->db->select('user.*')
                        ->from('auth_users AS user')
                        ->where("user.username", $usuario)
                        ->limit(1)
                        ->get()->result();
        if (empty ($query))
            return FALSE;
        else
            return TRUE;
        
    }
      
    public function insertar_usuario($usuario, $correo, $contrasenna, $nombre, $apellidos, $fecha_acceso, $fecha_creacion, $fecha_modificado, $banned)
    {
        
        $data = array('username' => $usuario, 'email' => $correo, 'password' => $contrasenna, 'name' => $nombre,
                      'lastname' => $apellidos, 'lastlogin' => $fecha_acceso, 
                      'created' => $fecha_creacion, 'modified' => $fecha_modificado, 'banned' => $banned);
        $insertado =  $this->db->insert('auth_users', $data);
        if($insertado)
       
            return TRUE;
        else
            return FALSE;
    }
    
    
    public function insertar_rol_usuario($role_id, $user_id)
    {
          $data = array('user_id' => $user_id, 'role_id' => $role_id);
          if($this->db->insert('auth_users_roles', $data))
              return TRUE;
          else
              return FALSE;
    }
    
    
   public function obtener_rol_usuario($usuario_id)
    {
         $data = $this->db->select('aur.user_role_id')
            ->from('auth_users_roles AS aur')
            ->where("aur.user_id", $usuario_id)
	    ->get()->result();
         return $data;
    }
    
    
    public function actualizar_login($usuario)
    {
        $query = $this->db->select('user.*')
                        ->from('auth_users AS user')
                        ->where("user.username", $usuario)
                        ->limit(1)
                        ->get()->result_array();
        $usuario = $query[0];
        $lastlogin = new DateTime();
        $lastlogin = $lastlogin->format('Y-m-d H:i:sP');
        $usuario_id = $usuario[user_id];
        $data = array('lastlogin' => $lastlogin);
                         
        $this->db->where('user_id', $usuario_id);
        $this->db->update('auth_users', $data); 
       
    }
    
    public function actualizar_banned($usuario_id, $banned)
    {
        $data = array('banned' => $banned);
        $this->db->where('user_id', $usuario_id);
        $this->db->update('auth_users', $data); 
    }
    
    public function editar_usuario($usuario_id, $usuario, $correo, $contrasenna, $nombre, $apellidos, $fecha_modificado)
    {
          $data = array('username' => $usuario, 'email' => $correo, 'password' => $contrasenna, 'name' => $nombre,
                      'lastname' => $apellidos, 'modified' => $fecha_modificado);
          $this->db->where('user_id', $usuario_id);
          $actualizo = $this->db->update('auth_users', $data); 
          if($actualizo)
              return TRUE;
          else
              return FALSE;
    }
    
    public function editar_usuario1($usuario_id, $usuario, $correo, $nombre, $apellidos, $fecha_modificado)
    {
          $data = array('username' => $usuario, 'email' => $correo, 'name' => $nombre,
                      'lastname' => $apellidos, 'modified' => $fecha_modificado);
          $this->db->where('user_id', $usuario_id);
          $actualizo = $this->db->update('auth_users', $data); 
          if($actualizo)
              return TRUE;
          else
              return FALSE;
    }
    
    public function editar_rol_usuario($user_rol_id, $role_id, $user_id)
    {
         $data = array('user_id' => $user_id, 'role_id' => $role_id);
         $this->db->where('user_role_id', $user_rol_id);
         $actualizo = $this->db->update('auth_users_roles', $data); 
          if($actualizo)
              return TRUE;
          else
              return FALSE;
    }
    
    public function eliminar_usuario($usuario_id)
    {
         if($this->db->delete('auth_users', array('user_id' => $usuario_id)))
             return TRUE;
         else
             return FALSE;
    }
    
    public function eliminar_usuario_rol($usuario_rol_id)
    {
       
         if($this->db->delete('auth_users_roles', array('user_role_id' => $usuario_rol_id)))
             return TRUE;
         else
             return FALSE;
    }

    public function obtener_usuarios($limit, $start, $fname)
    {
        $data = $this->db->select('user.*')->distinct()
                         ->from('auth_users AS user', 'auth_roles AS rol')
                         ->join('auth_users_roles AS aur', 'aur.user_id = user.user_id' , 'inner')
                         ->join('auth_roles AS rol', 'rol.role_id = aur.role_id', 'inner')
                         ->where("user.username LIKE '%$fname%'or user.email LIKE '%$fname%' or rol.name LIKE '%$fname%' or
                                 user.name LIKE '%$fname%' or user.lastname LIKE '%$fname%'")
                         ->order_by("user.username", "asc")
	                 ->limit($limit, $start)->get();
        
        $total = $data->num_rows();
        $data = $data->result_array();
        
        $usuarios = array();
        for($i = 0; $i < count($data); $i++)
        {
            if($data[$i][banned] == 'yes')
                $checked = true;
                else
                $checked = false;
            $user = Kohana_Auth::instance()->get_user()->username;
            if($user == $data[$i][username])
                    $activo = 'activo';
            else
                    $activo = 'no activo';
            $usuarios[$i] = array("user_id" => $data[$i][user_id], 'activo' => $activo,
                              'username' => $data[$i][username],
                              'email' => $data[$i][email],'checked' => $checked,
                              'lastlogin' => $data[$i][lastlogin], 'created' => $data[$i][created],
                              'modified' => $data[$i][modified], 'name' => $data[$i][name], 'lastname' => $data[$i][lastname], 
                              'password_bd' => $data[$i][password]);
        }
        return array('total' => $total, 'data' => $usuarios);
    }
    
    public function eliminar_preferences($preferences_id)
    {
        if($this->db->delete('extop_preferences', array('preference_id' => $preferences_id)))
             return TRUE;
         else
             return FALSE;
    }
    
    public function obtener_preferences($usuario_id)
    {
         $data = $this->db->select('pref.*')
                        ->from('extop_preferences AS pref')
                        ->where("pref.user_id", $usuario_id)
                        ->get()->result();
         return $data[0];
         
    }

    //-------------------Fin Usuarios------------------------------------------
    //-------------------Inicio Privilegios------------------------------------------
    //
        public function insertar_privilegios($role_id, $privilegios)
        {
           $privilegios = strtolower($privilegios);
           $data = array('role_id' => $role_id,
                         'data' =>  $privilegios); 
           if($this->db->insert('auth_acl', $data))
               return true;
           else
               return false;
        }
        
        
        public function acl_id($role_id)
        {
            $data = $this->db->select('acl.*')
                            ->from('auth_acl AS acl')
                            ->where("acl.role_id", $role_id)
                            ->get()->result();
           return $data[0];
        }
        
        public function eliminar_acl($acl_id)
        {
           $this->db->delete('auth_acl', array('acl_id' => $acl_id));
        }
        
        
        public function actualizar_acl($acl_id, $role_id, $privilegios)
        {
            $privilegios = strtolower($privilegios);
            $data = array('role_id' => $role_id, 'data' =>  $privilegios); 
            $this->db->where('acl_id', $acl_id);
            if($this->db->update('auth_acl', $data))
                return true;
            else
                return false;
        }
        
        public function obtener_modulos()
        {
            $this->db->select('em.*');
            $this->db->from('extop_modules AS em');
            $modulos = $this->db->get()->result();
            return $modulos;
        }
        
        public function obtener_modulos_id($modname)
        {
            $modulos = $this->db->select('em.jsid')
                          ->from('extop_modules AS em')
                          ->where('em.name', $modname)
                          ->get()->result();
            return $modulos[0]->jsid;
        }

        //
    //-------------------Fin Privilegios------------------------------------------
}

?>

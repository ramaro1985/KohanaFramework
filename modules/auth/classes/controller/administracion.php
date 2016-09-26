<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of members
 *
 * @author Tony
 */
class Controller_Administracion extends Controller_Secured{
    //put your code here

    public function before() {
        $this->admin = Model::factory('admin');
        $this->auth = Kohana_Auth::instance();
        $this->config_a = Kohana::$config->load('actions');
        $this->config_c = Kohana::$config->load('controller');
    }
 //-------------------------------- Inicia Roles------------------------------------   
    public function action_insertar_rol()
    {
        $rolname = $this->request->post('name');
        $descripcion = $this->request->post('descripcion');
        $existe = $this->admin->existe_rol($rolname);
        if($existe === TRUE)
        {
            
            $existerol = array('success' => FALSE, 'msg' => 'Ya existe el rol.');
            echo json_encode($existerol);
           
        }
        else
        {
            $addRol = $this->admin->insertar_rol($rolname, $descripcion);
            if($addRol === TRUE)
            {
               $insert_ok = array('success' => TRUE, 'msg' => 'Rol insertado correctamente.');
               echo json_encode($insert_ok);
            }
            else 
            {
               $insert_error = array('success' => FALSE, 'msg' => 'Ya existe el rol.');
               echo json_encode($insert_error);
            }
        }
        
    }
    
     public function action_editar_rol()
     {
         $idrol = $this->request->post('rol_id');
         $rolname = $this->request->post('name');
         $descripcion = $this->request->post('descripcion');
         $edito = $this->admin->editar_rol($idrol, $rolname, $descripcion);
         if($edito === TRUE)
            {
                $edito_ok = array('success' => TRUE, 'msg' => 'Rol actualizado correctamente.');
                echo json_encode($edito_ok);
            }
            else 
            {
               $edito_error = array('success' => FALSE, 'msg' => 'No se pudo actualizar el rol.');
               echo json_encode($edito_error);
            }
     }
     
     public function action_eliminar_rol()
     {
          $array = array();
          $roles_ids = json_decode($this->request->post('roles_ids'));
          $i = 0;
          foreach ($roles_ids as $role_id)
          {
              
              $existe = $this->admin->existe_usuario_rol($role_id);
              if(!$existe)
              {
                 $acl_id = $this->admin->acl_id($role_id);
                 if(!empty ($acl_id))
                 {
                     $this->admin->eliminar_acl($acl_id->acl_id);
                 }
                 $this->admin->eliminar_rol($role_id);
              }
              else
              {
                  $array[$i] = 'error';
                  $i++;
              }
              
          }
          if(count($array) > 0)
          {
               $elimino_error = array('success' => FALSE);
               echo json_encode($elimino_error);
          }
          else
          {
              if(count($array) == 0)
              {
                 $elimino_ok = array('success' => TRUE);
                 echo json_encode($elimino_ok); 
              }
          }
     }
     
     
    
     public function action_listar_roles()
     {
        $start = $this->request->post('start');
        $limit = $this->request->post('limit');
        if (isset($start) && isset($limit)) 
        {
            $start = $this->request->post('start');
            $limit = $this->request->post('limit');
        } 
        else
        {
            
            $start = 0;
            $limit = 20;
        }
        echo json_encode($this->admin->lista_roles($limit, $start, $editar));
        
         
     }
     
     public function action_listar_roles_editar()
     {
         $usuario_id = $this->request->post('user_id');
         echo json_encode($this->admin->listar_roles_x_usuario($usuario_id));
     }
     
      public function action_mostrar_roles()
      {
           echo json_encode($this->admin->lista_roles_tree());
     

      }
      
      public function action_mostrar_roles_actualizar()
      {
           $usuario_id = $_GET['user_id'];
           if (isset($usuario_id)) 
            echo json_encode($this->admin->lista_roles_tree_actualizar($usuario_id));
       }
      
     

      //-------------------------------- Fin Roles------------------------------------     
 //-------------------------------- Inicia Usuarios------------------------------------ 
 
     public function action_insertar_usuario()
     {
        $nombre = $this->request->post('name');
        $apellidos = $this->request->post('lastname');
        $usuario = $this->request->post('username');
        $correo = $this->request->post('email');
        $contrasenna = $this->request->post('password');
        $contrasenna = $this->auth->hash_password($contrasenna);
        $roles_id = array();
        $roles_id = json_decode($this->request->post('checkeds'));
        $fecha_creacion = new DateTime();
        $fecha_creacion = $fecha_creacion->format('Y-m-d');
        $fecha_modificado = '';
        $fecha_acceso = '';
        $banned = 'no';
        $existe = $this->admin->existe_usuario($usuario);
        if($existe === TRUE)
        {
            $existeusuario = array('success' => FALSE, 'msg' => 'Ya existe el usuario.');
            echo json_encode($existeusuario);
        }
        else
        {
            $insertUsuario = $this->admin->insertar_usuario($usuario, $correo, $contrasenna, $nombre, $apellidos, $fecha_acceso, $fecha_creacion, $fecha_modificado, $banned);
            if($insertUsuario === TRUE)
            {
                $user_id = $this->admin->id_usuario($usuario);
               
                    foreach ($roles_id as $role_id)
                    {
                        $this->admin->insertar_rol_usuario($role_id, $user_id);
                    }
                
                    $insert_ok = array('success' => TRUE, 'msg' => 'Usuario insertado correctamente.');
                    echo json_encode($insert_ok); 
            }
            else 
            {
               $insert_error = array('success' => FALSE, 'msg' => 'No se inserto el nuevo usuario .');
               echo json_encode($insert_error);
            }
        }
     }
     
     public function action_listar_usuarios()
     {
        $start = $this->request->post('start');
        $limit = $this->request->post('limit');
        $fname = $this->request->post('fname');
        if (isset($start) && isset($limit)) 
        {
            $start = $this->request->post('start');
            $limit = $this->request->post('limit');
            $fname = $this->request->post('fname');
        } 
        else
        {
            
            $start = 0;
            $limit = 20;
         }
         echo json_encode($this->admin->obtener_usuarios($limit, $start, $fname));
     }
     
     
     public function action_editar_usuario()
     {
         $usuario_id = $this->request->post('user_id');
         $nombre = $this->request->post('name');
         $apellidos= $this->request->post('lastname');
         $usuario= $this->request->post('username');
         $correo= $this->request->post('email');
         $pwd = $this->request->post('epassword');
         //$contrasenna_bd = $this->request->post('password_bd');
        // $contrasenna_anterior = $this->request->post('eopassword');
         //$contrasenna_ant_insert = $this->auth->hash_password($contrasenna_anterior);
         $contrasenna = $this->auth->hash_password($pwd);
         $roles_ids = array();
         $roles_ids = json_decode($this->request->post('checkeds'));
         $fecha_modificado = new DateTime();
         $fecha_modificado = $fecha_modificado->format('Y-m-d H:i:sP');
         if($pwd != '')
         {
                      $edito = $this->admin->editar_usuario($usuario_id, $usuario, $correo, $contrasenna, $nombre, $apellidos, $fecha_modificado);
                      if($edito === TRUE)
                      {
                                $users_rols = $this-> admin->obtener_rol_usuario($usuario_id);
                                foreach ($users_rols as $user_rol) 
                                {
                                  $this->admin->eliminar_usuario_rol($user_rol->user_role_id);
                                }
                                foreach ($roles_ids as $rol_id)
                                {
                                   $this->admin->insertar_rol_usuario($rol_id, $usuario_id);
                                }
                                  $edito_ok = array('success' => TRUE, 'msg' => 'El usuario ha sido modificado correctamente.');
                                  echo json_encode($edito_ok);
                      }  
                      else
                      {
                           $edito_error = array('success' => FALSE, 'msg' => 'No se pudo actualizar los datos del  usuario.');
                           echo json_encode($edito_error);
                      }
                
         }
         else 
         {
                 $edito = $this->admin->editar_usuario1($usuario_id, $usuario, $correo, $nombre, $apellidos, $fecha_modificado);
                 if($edito === TRUE)
                 {
                       $users_rols = $this-> admin->obtener_rol_usuario($usuario_id);
                       foreach ($users_rols as $user_rol) 
                       {
                           $this->admin->eliminar_usuario_rol($user_rol->user_role_id);
                       }
                       foreach ($roles_ids as $rol_id)
                       {
                           $this->admin->insertar_rol_usuario($rol_id, $usuario_id);

                       }
                           $edito_ok = array('success' => TRUE, 'msg' => 'El usuario ha sido modificado correctamente.');
                           echo json_encode($edito_ok);
                   }  
                   else
                   {
                           $edito_error = array('success' => FALSE, 'msg' => 'No se pudo actualizar los datos del  usuario.');
                           echo json_encode($edito_error);
                   }
                
         }
      }
     
     public function action_eliminar_usuario()
     {
          
              $usuario_ids = $this->request->post('user_id');
              $usuario_ids = json_decode($usuario_ids);
              $error = array();
              $e = 0;
              foreach ($usuario_ids as $usuario_id)
              {
                    $usuario_rol_ids = $this->admin->obtener_rol_usuario($usuario_id);
                    foreach ($usuario_rol_ids as $usuario_rol_id)
                    {
                           $this->admin->eliminar_usuario_rol($usuario_rol_id->user_role_id);
                    } 
                    $preferences = $this->admin->obtener_preferences($usuario_id); 
                    if(!empty($preferences))
                    {
                         $this->admin->eliminar_preferences($preferences->preference_id);
                    }
                    $success = $this->admin->eliminar_usuario($usuario_id);
                    if(!$success)
                    {
                      $error[$e] = 'error';  
                      $e++;
                    }
             } 
             if(count($error) == 0)
             {
                 $elimino_ok = array('success' => TRUE);
                 echo json_encode($elimino_ok);
             }
             else
             {
                 if(count($error) > 0)
                 {
                    $elimino_error = array('success' => FALSE);
                    echo json_encode($elimino_error); 
                 }
             }
     }
     
     public function action_banear()
     {
         $usuario_id = $this->request->post('iduser');
         $banned = $this->request->post('banned');
         
         if($banned == 'true')
         {
             $this->admin->actualizar_banned($usuario_id, 'yes');
             $elimino_ok = array('success' => TRUE, 'msg' => 'yes');
             echo json_encode($elimino_ok);
         }
         if($banned == 'false')
         {
              $this->admin->actualizar_banned($usuario_id, 'no');
              $elimino_ok = array('success' => TRUE, 'msg' => 'no');
              echo json_encode($elimino_ok);
         }
        
        
           
         
     }

 //-------------------------------- Fin Usuarios------------------------------------  
    
 //-------------------------------- Inicia Privilegios------------------------------------ 
 
     
     
      public function action_insertar_privilegios()
      {
              $controller = array();
              $privilegios = $this->request->post('privilegios');
              $modulespriv = $this->request->post('modules');
              $modulespriv = json_decode($modulespriv);
               
              $jsids = array();
              for($j = 0; $j < count($modulespriv); $j++) 
              {
                  $jsid = $this->admin->obtener_modulos_id($modulespriv[$j]);
                  $jsids[$j] = $jsid;
                  
              }
               
              $aa = json_decode($privilegios);
              for($i = 0; $i < count($aa); $i++)
              {
                 $controller[$i] = $aa[$i][0]; 
                 $permissions[$i] = array($controller[$i] => $aa[$i][1]);
              }
              $modules = array('modules' => $jsids);
              $control = array('controller' => $permissions);
              $priv = array_merge($control, $modules);
              $permiss = json_encode($priv);
              $role_id = $this->request->post('role_id');
              $acl = $this->admin->acl_id($role_id);
              if(empty ($acl))
              {
                  if($this->admin->insertar_privilegios($role_id, $permiss))
                  {
                     $elimino_ok = array('success' => TRUE, 'msg' => 'Asignado los privilegios correctamente');
                      echo json_encode($elimino_ok);     
                  }
                  else
                  {
                      $elimino_ok = array('success' => FALSE, 'msg' => 'No se asignaron los privilegios correctamente');
                      echo json_encode($elimino_ok);
                  }
              }
              else
              {
                     if($this->admin->actualizar_acl($acl->acl_id, $role_id, $permiss))
                     {
                           $elimino_ok = array('success' => TRUE, 'msg' => 'Asignado los privilegios correctamente');
                           echo json_encode($elimino_ok);

                     }
                     else
                     {
                          $elimino_error = array('success' => FALSE, 'msg' => 'No se asignaron los privilegios correctamente');
                          echo json_encode($elimino_error);
                     }        
              }
          
      }
      

      
    public function action_listar_privilegios()
    {
          $actions_list = $this->config_a->get('actions');
          $controller_list = $this->config_c->get('controller');
          $role_id = $_GET['role_id'];
          $controller = array();
          $modules = array();
          $modulos = $this->admin->obtener_modulos();
          $acl = $this->admin->acl_id($role_id);
          if($acl != null)
             $permissions = json_decode($acl->data, true);
          $j = 0;
          foreach($modulos as $modulo)
          {
              $checkedmod = false;
              if($acl != null)
                 if(in_array(strtolower ($modulo->jsid), $permissions[modules]))
                    $checkedmod = true;
            
              $mod = strstr($modulo->jsid, '-', true); 
              $dir = MODPATH . $mod.'\\classes\\controller';
              if(file_exists ($dir))
              {
                  $controller = null;
                  $i = 0;
                  $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
                  foreach($files as $file)
                  {
                      $filename = strtolower(basename($file, ".php"));
                      if($acl != null)
                      {
                         $checkedcontrl = false;
                        if(!in_array($filename, $controller_list))
                         {
                             foreach($permissions[controller] as $perm)
                             {
                                 if(array_key_exists($filename, $perm))
                                 {
                                     $checkedcontrl = true; 
                                     $content = file_get_contents($file);
                                     $strarray = explode(" ", $content);
                                     $actions = array();
                                     $l = 0; 
                                     for($k = 0; $k < count($strarray); $k++)
                                     {
                                         if(strncmp($strarray[$k], 'action_', 7) == 0)
                                         {
                                            $actionname = strstr(substr($strarray[$k], 7), '(', true);
                                            if(!in_array(strtolower($actionname), $actions_list))
                                            {
                                                $checkedaction = false;
                                                if(in_array(strtolower($actionname), $perm[$filename]))
                                                   $checkedaction = true;
                                                $actions[$l] = array('text' => $actionname, 'leaf' => true, 'checked' => $checkedaction);
                                                $l++;
                                            }
                                         }
                                     }
                                 }
                             }
                             if($checkedcontrl == false)
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
                                          if(!in_array($actionname, $actions_list))
                                          {
                                             $checkedaction = false;
                                             $actions[$l] = array('text' => $actionname, 'leaf' => true, 'checked' => $checkedaction);
                                             $l++;
                                           }
                                       }
                                  }
                             }
                             if(count($actions) > 0)
                             {
                                  $controller[$i] = array('text' => 'Paquete'.' '.$filename, 'checked' => $checkedcontrl, 'children' => $actions);
                                  $i++;  
                             }
                         }
                         
                      }
                      else
                      {
                              if(!in_array($filename, $controller_list))
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
                                         if(!in_array($actionname, $actions_list))
                                         {
                                             $checkedaction = false;
                                             $actions[$l] = array('text' => $actionname, 'leaf' => true, 'checked' => $checkedaction);
                                             $l++;
                                         }
                                       }
                                    }
                                    if(count($actions) > 0)
                                    {     $checkedcontrl = false;
                                          $controller[$i] = array('text' => 'Paquete'.' '.$filename, 'checked' => $checkedcontrl, 'children' => $actions);
                                          $i++;   
                                    }
                              }
                      }
                  }
                  $modules[$j] = array('text' => 'Módulo'.' '.$modulo->name, 'checked' => $checkedmod, 'children' => $controller);
                  $j++;
              }
          }
          echo json_encode($modules);   
       }
     
      //-------------------------------- Fin Privilegios------------------------------------ 
     
    
}


?>

<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

    'driver'        => 'database',
    'authz'         => TRUE,
    'audit'         => FALSE,
    'hash_method'   => 'sha256',
    'hash_key'      => 'h4shk3y',
    'lifetime'      => 1209600,
    'session_type'  => 'database',
    'session_key'   => 'auth_info',
    'db_config'     => array(
        '_connection_group' => 'default',
        'users_table'       => 'auth_users',
        'roles_table'       => 'auth_roles',
        'pivot_table'       => 'auth_users_roles',
        'acl_table'         => 'auth_acl',
        'audit_table'       => 'auth_audit',
        'login_field'       => 'username',
        'password_field'    => 'password',
    ),
);

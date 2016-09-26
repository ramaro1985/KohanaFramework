<?php

defined('SYSPATH') or die('No direct access allowed.');

return array(
    'active_group' => 'gestion',
    'active_record' => TRUE,
    'gestion' => array(
        'dbdriver' => 'pgsql',
        'hostname' => 'localhost',
        'database' => 'SistemaAlertas',
        'username' => 'postgres',
        'password' => 'postgres',
        'pconnect' => FALSE,
        'dbprefix' => '',
        'char_set' => 'utf8',
        'dbcollat' => 'utf8_general_ci',
        'cache_on' => FALSE,
        'cachedir' => '',
        'autoinit' => TRUE,
        'stricton' => FALSE,
        'db_debug' => TRUE,
        'swap_pre' => '',
        'port' => '5432',
    ),
);
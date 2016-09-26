<?php defined('SYSPATH') or die('No direct script access.');

require Kohana::find_file('classes', 'vendor/Doctrine');
spl_autoload_register(array('Doctrine', 'autoload'));
spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$db = Kohana::$config->load('database')->as_array();
try
{
    $manager = Doctrine_Manager::getInstance();
    $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_PEAR);

    foreach ($db as $connection_name => $db_values)
    {
        if($db[$connection_name]['type'] != 'pdo') {
            $dsn = $db[$connection_name]['type'] . '://' . 
                $db[$connection_name]['connection']['username'] .
                ':' . $db[$connection_name]['connection']['password'] . 
                '@' . $db[$connection_name]['connection']['hostname'] .
                '/' . $db[$connection_name]['connection']['database'] .
                '?' . 'charset=' . $db[$connection_name]['charset'];
                
            $manager::connection($dsn, $connection_name);
        }
    }
    
    $model_directories = array();
    
    foreach(Kohana::modules() AS $name => $modpath)
    {
        if(is_dir($modpath . 'classes/model'))
        {
            $model_directories[] = $modpath . 'classes';
        }   
    }
    
        Doctrine::loadModels($model_directories);
} 
catch(Doctrine_Exception $e)
{
    throw new Kohana_Exception($e->getMessage(), array(), 500);
}
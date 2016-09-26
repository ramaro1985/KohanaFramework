<?php

include_once('../classes/vendor/Doctrine.php');
spl_autoload_register ( array ('Doctrine', 'autoload' ) );

$pear_style = TRUE;
$base_classes_directory = FALSE;
$base_class_prefix = 'Base_';
$class_prefix = 'Model_';
$generate_table_classes =  FALSE;
$class_prefix_files = FALSE;

$generate_models_options = array(
    'pearStyle' => $pear_style,
    'generateTableClasses' => $generate_table_classes,
    'baseClassesDirectory' => $base_classes_directory,
    'baseClassPrefix' => $base_class_prefix,
    'classPrefix' => $class_prefix,
    'classPrefixFiles' => $class_prefix_files
);

$data_fixtures_path = '../fixtures/data';
$yaml_schema_path = '../fixtures/schema';
$migrations_path = '../migrations';
$models_path = '../classes/model';
$sql_path = '../sql';

$config = array(
    'data_fixtures_path'    => $data_fixtures_path,
    'yaml_schema_path'      => $yaml_schema_path,
    'migrations_path'       => $migrations_path,
    'models_path'           => $models_path,
    'sql_path'              => $sql_path,
    'generate_models_options' => $generate_models_options
);

$manager = Doctrine_Manager::getInstance ();
$manager->setAttribute ( Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_AGGRESSIVE);
//$manager->setAttribute ( Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, TRUE );

$db = get_database_config();

foreach ($db as $connection_name => $db_values)
{
    $dsn = $db[$connection_name]['type'] . '://' . 
        $db[$connection_name]['connection']['username'] .
        ':' . $db[$connection_name]['connection']['password'] . 
        '@' . $db[$connection_name]['connection']['hostname'] .
        '/' . $db[$connection_name]['connection']['database']; // .
        //'?' . 'charset=' . $db[$connection_name]['charset'];
        
    $manager::connection($dsn, $connection_name);
}

spl_autoload_register(array('Doctrine', 'modelsAutoload'));
Doctrine::loadModels($models_path);

function get_database_config()
{
    return array
    (
    	'default' => array
    	(
    		'type'       => 'pgsql',
    		'connection' => array(
    			/**
    			 * The following options are available for MySQL:
    			 *
    			 * string   hostname     server hostname, or socket
    			 * string   database     database name
    			 * string   username     database username
    			 * string   password     database password
    			 * boolean  persistent   use persistent connections?
    			 *
    			 * Ports and sockets may be appended to the hostname.
    			 */
    			'hostname'   => 'localhost',
    			'database'   => 'kodext',
    			'username'   => 'postgres',
    			'password'   => 'admin123',
    			'persistent' => FALSE,
    		),
    		'table_prefix' => '',
    		'charset'      => 'utf8',
            'collate'      => 'utf8_general_ci',
    		'caching'      => FALSE,
    		'profiling'    => TRUE,
    	)
    );
}
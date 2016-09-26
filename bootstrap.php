<?php

defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------
// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT)) {
    // Application extends the core
    require APPPATH . 'classes/kohana' . EXT;
} else {
    // Load empty core extension
    require SYSPATH . 'classes/kohana' . EXT;
}

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('America/Havana');

/**
 * Set the default locale.
 * 
 * Note: Use 65001 codepage instead utf-8 if you are running
 * on Windows. 
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
// setlocale(LC_ALL, 'es_Mx.UTF-8'); 
setlocale(LC_ALL, 'Spanish_Mexico.65001');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('es');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV'])) {
    Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
    'base_url' => ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") .
    "://" . $_SERVER['HTTP_HOST'] .
    str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']),
    'index_file' => FALSE,
    'cache_dir' => KODEXTROOT . 'cache')
);

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(KODEXTROOT . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    'userguide' => MODPATH . 'userguide', // User guide and API documentation
    'cidb' => MODPATH . 'cidb', // Database access (Port from CodeIgniter)
    'auth' => MODPATH . 'auth', // Basic authentication
    'extop' => MODPATH . 'extop', // Extop Module
    'codebench' => MODPATH . 'codebench', // Benchmarking tool
    'phpexcel' => MODPATH . 'phpexcel', // Handle spreadsheets
    'mpdf' => MODPATH . 'mpdf', // Create PDF from HTML and CSS.
    'ioc' => MODPATH . 'ioc', // Dependency Injection
    'balanceMaterial' => MODPATH . 'balanceMaterial', // Balance Material
    'reportes' => MODPATH . 'reportes', // Reportes
        //'database'      => MODPATH.'database',      // Database access (Port from CodeIgniter)
        //'doctrine'      => MODPATH.'doctrine',      // Doctrine 1.2.x ORM Library
        //'cache'         => MODPATH.'cache',         // Caching with multiple backends
        //'image'         => MODPATH.'image',         // Image manipulation
        //'unittest'      => MODPATH.'unittest',      // Unit testing
));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'extop',
            'action' => 'index',
        ));

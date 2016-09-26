<?php

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * NOTICE OF LICENSE
 * 
 * Licensed under the Open Software License version 3.0
 * 
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		Kohana/Database
 * @category	Base
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc. (http://ellislab.com/)
 * @author      Rafael Ernesto Espinosa Santiesteban
 * @copyright   Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

class Kohana_Db
{

    static $database;
    static $util;
    static $forge;
    static $config;
    
    static $active_group;
    static $active_record;

    public function __construct($config = FALSE)
    {
        
    }

    /**
     * simple autoload function
     * returns true if the class was loaded, otherwise false
     *
     * @param string $className
     * @return boolean
     */
    public static function autoload($className)
    {

        if (0 !== stripos($className, 'Db') OR class_exists($className, false) OR interface_exists($className, false))
        {
            return false;
        }

        $class = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR .
            str_replace('_', DIRECTORY_SEPARATOR, str_replace('Db_', '', $className)) . '.php';

        if (file_exists($class))
        {
            require $class;

            return true;
        }

        return false;
    }

    /**
     * Initialize the database.
     *
     * @param	mixed	the DB credentials or config array
     * @param	bool	whether to enable active record (this allows us to override the config setting)
     * @return	object
     */
    public static function &initialize($params = '', $active_record_override = NULL)
    {
        if (!defined('SYSPATH') AND !class_exists('Kohana'))
        {
            spl_autoload_register(array('Db', 'autoload'));
        }
        elseif (class_exists('Kohana'))
        {
            self::$config = Kohana::$config->load('database');
            self::$active_group = self::$config->get('active_group'); 
            self::$active_record = self::$config->get('active_record');
        }
        else
        {
            self::$config = $config;
        }
        // Load the DB config file if a DSN string wasn't passed
        if ((is_string($params) AND strpos($params, '://') === FALSE))
        {

            if (!isset(self::$config) OR count(self::$config) == 0)
            {
                throw new Db_Exception('db_must_specify_conn_sets');
            }

            if ($params != '')
            {
                self::$active_group = $params;
            }

            if (!isset(self::$active_group))
            {
                throw new Db_Exception('db_invalid_conn_group');
            }

            $params = self::$config->get(self::$active_group);
        }
        elseif (is_string($params))
        {

            /* parse the URL from the DSN string
             *  Database settings can be passed as discreet
             *  parameters or as a data source name in the first
             *  parameter. DSNs must have this prototype:
             *  $dsn = 'driver://username:password@hostname/database';
             */

            if (($dsn = @parse_url($params)) === FALSE)
            {
                throw new Db_Exception('db_invalid_dsn_conn_str');
            }

            $params = array(
                'dbdriver' => $dsn['scheme'],
                'hostname' => (isset($dns['host'])) ? rawurldecode($dsn['host']) : '',
                'username' => (isset($dsn['user'])) ? rawurldecode($dsn['user']) : '',
                'password' => (isset($dsn['pass'])) ? rawurldecode($dsn['pass']) : '',
                'database' => (isset($dsn['path'])) ? rawurldecode(substr($dsn['path'], 1)) : ''
            );

            // were additional config items set?
            if (isset($dsn['query']))
            {
                parse_str($dsn['query'], $extra);

                foreach ($extra as $key => $val)
                {
                    // booleans please
                    if (strtoupper($val) == "TRUE")
                    {
                        $val = TRUE;
                    }
                    elseif (strtoupper($val) == "FALSE")
                    {
                        $val = FALSE;
                    }

                    $params[$key] = $val;
                }
            }
        }

        // No DB specified yet?  Beat them senseless...
        if (!isset($params['dbdriver']) OR $params['dbdriver'] == '')
        {
            throw new Db_Exception('db_no_database_type');
        }

        // Load the DB classes.  Note: Since the active record class is optional
        // we need to dynamically create a class that extends proper parent class
        // based on whether we're using the active record class or not.
        // Kudos to Paul for discovering this clever use of eval()

        if ($active_record_override !== NULL)
        {
            self::$active_record = $active_record_override;
        }

        if (self::$active_record == TRUE)
        {
            if (!class_exists('Database'))
            {
                eval('class Database extends Db_Active_Record { }');
            }
        }
        else
        {
            if (!class_exists('Database'))
            {
                eval('class Database extends Db_Driver { }');
            }
        }

        // Instantiate the DB adapter
        $driver = 'Db_' . ucfirst($params['dbdriver']) . '_Driver';
        $DB = new $driver($params);

        if ($DB->autoinit == TRUE)
        {
            $DB->initialize();
        }

        if (isset($params['stricton']) && $params['stricton'] == TRUE)
        {
            $DB->query('SET SESSION sql_mode="STRICT_ALL_TABLES"');
        }

        return $DB;
    }

    /**
     * Load database instance.
     *
     * @param	string	the DB credentials
     * @param	bool	whether to return the DB object
     * @param	bool	whether to enable active record (this allows us to override the config setting)
     * @return	object
     */
    public static function &load($params = '', $return = FALSE, $active_record = NULL)
    {
        // Do we even need to load the database class?
        if (class_exists('Database') AND $return == FALSE AND $active_record == NULL AND isset(self::$database) AND is_object(self::$database))
        {
            return self::$database;
        }

        // Initialize the db variable.  Needed to prevent
        // reference errors with some configurations
        $database = '';

        // Load the DB class
        $database = self::initialize($params, $active_record);

        if ($return === TRUE)
        {
            return $database;
        }
        else
        {
            self::$database = $database;
            $return = !$return;
            return $return;
        }
    }

    /**
     * Load the Utilities Class
     *
     * @access	public
     * @return	string
     */
    public static function &dbutil()
    {
        if (!class_exists('Database'))
        {
            self::load();
        }

        $class = 'Db_' . ucfirst(self::$database->dbdriver) . '_Utility';

        self::$util = new $class();

        return self::$util;
    }

    /**
     * Load the Database Forge Class
     *
     * @access	public
     * @return	string
     */
    public static function &dbforge()
    {
        if (!class_exists('Database'))
        {
            self::load();
        }

        $class = 'Db_' . ucfirst(self::$database->dbdriver) . '_Forge';

        self::$forge = new $class();

        return self::$forge;
    }

}

/* End of file db.php */
/* Location: ./db.php */
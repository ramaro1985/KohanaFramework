<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Database Model base class.
 *
 * @package		Kohana/Database
 * @category	Models
 * @author      Kohana Team
 * @author      Rafael Ernesto Espinosa Santiesteban <alvk4r@blackbird.org>
 * @copyright   (c) 2011 Kohana Team
 * @copyright   Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license     http://kohanaphp.com/license
 */
abstract class Kohana_Model_Database extends Kohana_Model
{

    protected $db = FALSE;
    protected $dbutil = FALSE;
    protected $dbforge = FALSE;

    /**
     * Create a new model instance. A [Database] instance or configuration
     * group name can be passed to the model. If no database is defined, the
     * "default" database group will be used.
     *
     *     $model = Model::factory($name, $db);
     *
     * @param   string   model name
     * @param   string
     * @return  Model
     */
    public static function factory($name, $db = FALSE)
    {
        // Add the model prefix
        $class = 'Model_' . ucfirst($name);

        return new $class($db);
    }

    /**
     * Loads the database.
     *
     *     $model = new Foo_Model($db);
     *
     * @param   string
     * @return  void
     */
    public function __construct($db = FALSE)
    {
        if ($this->db != FALSE)
        {
            $this->db = Db::$database;
        }
        else{
            $this->db = Db::load($db);
        }
        $this->dbutil = Db::dbutil();
        $this->dbforge = Db::dbforge();
    }

    /**
     * Returns the last query that was executed
     *
     * @access	public
     * @return	void
     */
    public function last_query()
    {
        return $this->db->last_query();
    }

}

// End Model

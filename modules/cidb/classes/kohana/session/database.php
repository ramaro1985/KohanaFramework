<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database-based session class.
 *
 * Sample schema:
 *
 *     CREATE TABLE  `sessions` (
 *         `session_id` VARCHAR( 24 ) NOT NULL,
 *         `last_active` INT UNSIGNED NOT NULL,
 *         `contents` TEXT NOT NULL,
 *         PRIMARY KEY ( `session_id` ),
 *         INDEX ( `last_active` )
 *     ) ENGINE = MYISAM ;
 *
 * @package    Kohana/Database
 * @category   Session
 * @author     Kohana Team
 * @author     Rafael Ernesto Espinosa Santiesteban <alvk4r@blackbird.org>
 * @copyright  (c) 2011 Kohana Team
 * @copyright  Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license    http://kohanaphp.com/license
 */
class Kohana_Session_Database extends Session {

	// Database instance
	protected $_db;

	// Database table name
	protected $_table = 'sessions';

	// Database column names
	protected $_columns = array(
		'session_id'  => 'session_id',
		'last_active' => 'last_active',
		'contents'    => 'contents'
	);

	// Garbage collection requests
	protected $_gc = 500;

	// The current session id
	protected $_session_id;

	// The old session id
	protected $_update_id;

	public function __construct(array $config = NULL, $id = NULL)
	{
		if ( ! isset($config['group']))
		{
			// Use the default group
			$config['group'] = 'default';
		}

		// Load the database
		$this->_db = Db::load($config['group'], TRUE);
        
        // Check for Active Record
        if(!is_subclass_of($this->_db,'Db_Active_Record'))
        {
            throw new Db_Exception('db_active_record_disabled');
        }

		if (isset($config['table']))
		{
			// Set the table name
			$this->_table = (string) $config['table'];
		}

		if (isset($config['gc']))
		{
			// Set the gc chance
			$this->_gc = (int) $config['gc'];
		}

		if (isset($config['columns']))
		{
			// Overload column names
			$this->_columns = $config['columns'];
		}

		parent::__construct($config, $id);

		if (mt_rand(0, $this->_gc) === $this->_gc)
		{
			// Run garbage collection
			// This will average out to run once every X requests
			$this->_gc();
		}
	}

	public function id()
	{
		return $this->_session_id;
	}

	protected function _read($id = NULL)
	{
		if ($id OR $id = Cookie::get($this->_name))
		{
            $query = $this->_db
                ->select($this->_columns['contents'])
                ->from($this->_table)
                ->where($this->_columns['session_id'], $id)
                ->limit(1)->get();
            
            $result = $query->row();
                

			if ($query->num_rows())
			{
				// Set the current session id
				$this->_session_id = $this->_update_id = $id;

				// Return the contents
				return $result->contents;
			}
		}

		// Create a new session id
		$this->_regenerate();

		return NULL;
	}

	protected function _regenerate()
	{
		do
		{
			// Create a new session id
			$id = str_replace('.', '-', uniqid(NULL, TRUE));
            
            // Create the query to find an ID
   		   $query = $this->_db
                ->select($this->_columns['contents'])
                ->from($this->_table)
                ->where($this->_columns['session_id'], $id)
                ->limit(1)->get();                        
		}
		while ($query->num_rows());

	    return $this->_session_id = $id;
	}

	protected function _write()
	{
        // Define row values
        $sets = array(
            $this->_columns['last_active'] => $this->_data['last_active'],
            $this->_columns['contents'] => $this->__toString()
        );                 
            
		if ($this->_update_id === NULL)
		{
            $new = TRUE;
            $sets['session_id'] = $this->_session_id;
		}
		else
		{
			// Update the row
            $new = FALSE;
            $sets['session_id'] = $this->_update_id;
            
			if ($this->_update_id !== $this->_session_id)
			{
				// Also update the session id
				$sets['session_id'] = $this->_session_id;
			}
		}

        $this->_db->set($sets);
        
        if($new === TRUE)
        {
            $this->_db->insert($this->_table);
        }
        else
        {
            $this->_db->where($this->_columns['session_id'], $this->_update_id)->update($this->_table);
        }

		// The update and the session id are now the same
		$this->_update_id = $this->_session_id;

		// Update the cookie with the new session id
		Cookie::set($this->_name, $this->_session_id, $this->_lifetime);

		return TRUE;
	}

	/**
	 * @return  bool
	 */
	protected function _restart()
	{
		$this->_regenerate();

		return TRUE;
	}

	protected function _destroy()
	{
		if ($this->_update_id === NULL)
		{
			// Session has not been created yet
			return TRUE;
		}

		try
		{
			// Delete the current session
            $this->_db->where($this->_columns['session_id'], $this->_update_id)->delete($this->_table);
			// Delete the cookie
			Cookie::delete($this->_name);
		}
		catch (Exception $e)
		{
			// An error occurred, the session has not been deleted
			return FALSE;
		}

		return TRUE;
	}

	protected function _gc()
	{
		if ($this->_lifetime)
		{
			// Expire sessions when their lifetime is up
			$expires = $this->_lifetime;
		}
		else
		{
			// Expire sessions after one month
			$expires = Date::MONTH;
		}

		// Delete all sessions that have expired
        $this->_db->delete($this->_table, $this->_columns['last_active'].' < '.(time() - $expires));
	}

} // End Session_Database

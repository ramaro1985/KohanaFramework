<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Database writer for the config system
 *
 * @package     Kohana
 * @category    Configuration
 * @author      Kohana Team
 * @author      Rafael Ernesto Espinosa Santiesteban <alvk4r@blackbird.org>
 * @copyright   (c) 2011 Kohana Team
 * @copyright   Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license     http://kohanaphp.com/license
 */
class Kohana_Config_Database_Writer extends Config_Database_Reader implements Kohana_Config_Writer
{
	protected $_loaded_keys = array();

	/**
	 * Tries to load the specificed configuration group
	 *
	 * Returns FALSE if group does not exist or an array if it does
	 *
	 * @param  string $group Configuration group
	 * @return boolean|array
	 */
	public function load($group)
	{
		$config = parent::load($group);

		if ($config !== FALSE)
		{
			$this->_loaded_keys[$group] = array_combine(array_keys($config), array_keys($config));
		}

		return $config;
	}

	/**
	 * Writes the passed config for $group
	 *
	 * Returns chainable instance on success or throws 
	 * Kohana_Config_Exception on failure
	 *
	 * @param string      $group  The config group
	 * @param string      $key    The config key to write to
	 * @param array       $config The configuration to write
	 * @return boolean
	 */
	public function write($group, $key, $config)
	{
		$config = serialize($config);

		// Check to see if we've loaded the config from the table already
		if (isset($this->_loaded_keys[$group][$key]))
		{
			$this->_update($group, $key, $config);
		}
		else
		{
			// Attempt to run an insert query
			// This may fail if the config key already exists in the table
			// and we don't know about it
			try
			{
				$this->_insert($group, $key, $config);
			}
			catch (Database_Exception $e)
			{
				// Attempt to run an update instead
				$this->_update($group, $key, $config);
			}
		}

		return TRUE;
	}

	/**
	 * Insert the config values into the table
	 *
	 * @param string      $group  The config group
	 * @param string      $key    The config key to write to
	 * @param array       $config The serialized configuration to write
	 * @return boolean
	 */
	protected function _insert($group, $key, $config)
	{            
        $this->_db->set(array('group_name' => $group, 'config_key' => $key, 'config_value' => $config));
        $this->_db->insert($this->_table_name);

		return $this;
	}

	/**
	 * Update the config values in the table
	 *
	 * @param string      $group  The config group
	 * @param string      $key    The config key to write to
	 * @param array       $config The serialized configuration to write
	 * @return boolean
	 */
	protected function _update($group, $key, $config)
	{   
        $this->_db->set(array('config_value' => $config));
        $this->_db->where(array('group_name' => $group, 'config_key' => $key))
            ->update($this->_table_name);

		return $this;
	}
}

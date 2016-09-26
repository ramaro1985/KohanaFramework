<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Database reader for the kohana config system
 *
 * @package     Kohana/Database
 * @category    Configuration
 * @author      Kohana Team
 * @author      Rafael Ernesto Espinosa Santiesteban <alvk4r@blackbird.org>
 * @copyright   (c) 2011 Kohana Team
 * @copyright   Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license     http://kohanaphp.com/license
 */
class Kohana_Config_Database_Reader implements Kohana_Config_Reader
{
	protected $_db;

	protected $_table_name  = 'config';

	/**
	 * Constructs the database reader object
	 *
	 * @param array Configuration for the reader
	 */
	public function __construct(array $config = NULL)
	{
		if (isset($config['group']))
		{
			$this->_db = Db::load($config['group'], TRUE);
		}

		if (isset($config['table_name']))
		{
			$this->_table_name = $config['table_name'];
		}
	}

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
	    $query = $this->_db->select('config_key, config_value')
            ->from($this->_table_name)
            ->where('group_name', $group)
            ->get();

		return ($query->num_rows() > 0) ? array_map('unserialize', $query->result_array()) : FALSE;
	}
}

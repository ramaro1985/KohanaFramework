<?php

/**
 * MySQLi Utility Class
 *
 * @package		Kohana/Database
 * @subpackage	Drivers
 * @category	Mysqli
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc. (http://ellislab.com/)
 * @author      Rafael Ernesto Espinosa Santiesteban
 * @copyright   Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com/user_guide/database/
 */
class Kohana_Db_Mysqli_Utility extends Kohana_Db_Utility
{

    /**
     * List databases
     *
     * @access	private
     * @return	bool
     */
    function _list_databases()
    {
        return "SHOW DATABASES";
    }

    // --------------------------------------------------------------------

    /**
     * Optimize table query
     *
     * Generates a platform-specific query so that a table can be optimized
     *
     * @access	private
     * @param	string	the table name
     * @return	object
     */
    function _optimize_table($table)
    {
        return "OPTIMIZE TABLE " . $this->db->_escape_identifiers($table);
    }

    // --------------------------------------------------------------------

    /**
     * Repair table query
     *
     * Generates a platform-specific query so that a table can be repaired
     *
     * @access	private
     * @param	string	the table name
     * @return	object
     */
    function _repair_table($table)
    {
        return "REPAIR TABLE " . $this->db->_escape_identifiers($table);
    }

    // --------------------------------------------------------------------

    /**
     * MySQLi Export
     *
     * @access	private
     * @param	array	Preferences
     * @return	mixed
     */
    function _backup($params = array())
    {
        // Currently unsupported
        return $this->db->display_error('db_unsuported_feature');
    }

}

/* End of file mysqli_utility.php */
/* Location: ./system/database/drivers/mysqli/mysqli_utility.php */
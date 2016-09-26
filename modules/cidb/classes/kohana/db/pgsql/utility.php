<?php

/**
 * Pgsql Utility Class
 *
 * @package		Kohana/Database
 * @subpackage	Drivers
 * @category	Pgsql
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc. (http://ellislab.com/)
 * @author      Rafael Ernesto Espinosa Santiesteban
 * @copyright   Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com/user_guide/database/
 */
class Kohana_Db_Pgsql_Utility extends Kohana_Db_Utility
{

    /**
     * List databases
     *
     * @access	private
     * @return	bool
     */
    function _list_databases()
    {
        return "SELECT datname FROM pg_database";
    }

    // --------------------------------------------------------------------

    /**
     * Optimize table query
     *
     * Is table optimization supported in Postgre?
     *
     * @access	private
     * @param	string	the table name
     * @return	object
     */
    function _optimize_table($table)
    {
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * Repair table query
     *
     * Are table repairs supported in Postgre?
     *
     * @access	private
     * @param	string	the table name
     * @return	object
     */
    function _repair_table($table)
    {
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * Postgre Export
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

/* End of file postgre_utility.php */
/* Location: ./system/database/drivers/postgre/postgre_utility.php */
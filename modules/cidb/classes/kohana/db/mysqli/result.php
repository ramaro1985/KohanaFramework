<?php

/**
 * MySQLi Result Class
 *
 * This class extends the parent result class: CI_DB_result
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
class Kohana_Db_Mysqli_Result extends Kohana_Db_Result
{

    /**
     * Number of rows in the result set
     *
     * @access	public
     * @return	integer
     */
    function num_rows()
    {
        return @mysqli_num_rows($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * Number of fields in the result set
     *
     * @access	public
     * @return	integer
     */
    function num_fields()
    {
        return @mysqli_num_fields($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch Field Names
     *
     * Generates an array of column names
     *
     * @access	public
     * @return	array
     */
    function list_fields()
    {
        $field_names = array();
        while ($field = mysqli_fetch_field($this->result_id))
        {
            $field_names[] = $field->name;
        }

        return $field_names;
    }

    // --------------------------------------------------------------------

    /**
     * Field data
     *
     * Generates an array of objects containing field meta-data
     *
     * @access	public
     * @return	array
     */
    function field_data()
    {
        $retval = array();
        while ($field = mysqli_fetch_object($this->result_id))
        {
            preg_match('/([a-zA-Z]+)(\((\d+)\))?/i', $field->Type, $matches);

            $type = $matches[1];
            $length = isset($matches[3]) ? (int) $matches[3] : NULL;

            $F = new stdClass();
            $F->name = $field->Field;
            $F->type = $type;
            $F->default = $field->Default;
            $F->max_length = $length;
            $F->primary_key = ( $field->Key == 'PRI' ? 1 : 0 );

            $retval[] = $F;
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * Free the result
     *
     * @return	null
     */
    function free_result()
    {
        if (is_object($this->result_id))
        {
            mysqli_free_result($this->result_id);
            $this->result_id = FALSE;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Data Seek
     *
     * Moves the internal pointer to the desired offset.  We call
     * this internally before fetching results to make sure the
     * result set starts at zero
     *
     * @access	private
     * @return	array
     */
    function _data_seek($n = 0)
    {
        return mysqli_data_seek($this->result_id, $n);
    }

    // --------------------------------------------------------------------

    /**
     * Result - associative array
     *
     * Returns the result set as an array
     *
     * @access	private
     * @return	array
     */
    function _fetch_assoc()
    {
        return mysqli_fetch_assoc($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * Result - object
     *
     * Returns the result set as an object
     *
     * @access	private
     * @return	object
     */
    function _fetch_object()
    {
        return mysqli_fetch_object($this->result_id);
    }

}

/* End of file mysqli_result.php */
/* Location: ./system/database/drivers/mysqli/mysqli_result.php */
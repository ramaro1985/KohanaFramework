<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Download Helper Class
 * Used to set headers on download file process.
 *
 * @package		Kohana
 * @category	Helpers
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @copyright   Copyright (c) 2008 - 2011, Rafael Ernesto Espinosa Santiesteban
 * @license		http://codeigniter.com/user_guide/license.html
 * @author		ExpressionEngine Dev Team
 * @author      Rafael Ernesto Espinosa Santiesteban <alvk4r@blackbird.org>
 * @link		http://codeigniter.com/user_guide/libraries/zip.html
 */
class Kohana_Download
{
    
    /**
     * Force Download
     *
     * Generates headers that force a download to happen
     *
     * @access	public
     * @param	string	filename
     * @param	mixed	the data to be downloaded
     * @return	void
     */
    public static function force_download($filename = '', $data = '')
	{
		if ($filename == '' OR $data == '')
		{
			return FALSE;
		}

		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (FALSE === strpos($filename, '.'))
		{
			return FALSE;
		}

		// Grab the file extension
		$x = explode('.', $filename);
		$extension = end($x);
        $mimes = Kohana::$config->load('mimes');
		// Set a default mime if we can't find it
		if ( ! isset($mimes[$extension]))
		{
			$mime = 'application/octet-stream';
		}
		else
		{
			$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}

		// Generate the server headers
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		}
		else
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data));
		}

		exit($data);
	}
}
<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Xml Helper Class
 *
 * This class is based on a library I found at Zend:
 * http://www.zend.com/codex.php?id=696&single=1
 *
 * The original library is a little rough around the edges so I
 * refactored it and added several additional methods -- Rick Ellis
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
class Kohana_Xml
{
    /**
     * Convert Reserved XML characters to Entities
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public static function convert($str, $protect_all = FALSE)
	{
		$temp = '__TEMP_AMPERSANDS__';

		// Replace entities to temporary markers so that
		// ampersands won't get messed up
		$str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);

		if ($protect_all === TRUE)
		{
			$str = preg_replace("/&(\w+);/",  "$temp\\1;", $str);
		}

		$str = str_replace(array("&","<",">","\"", "'", "-"),
							array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;", "&#45;"),
							$str);

		// Decode the temp markers back to entities
		$str = preg_replace("/$temp(\d+);/","&#\\1;",$str);

		if ($protect_all === TRUE)
		{
			$str = preg_replace("/$temp(\w+);/","&\\1;", $str);
		}

		return $str;
	}
}
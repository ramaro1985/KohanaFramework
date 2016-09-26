<?php defined('SYSPATH') or die('No direct script access.');

class Kodoc extends Kohana_Kodoc 
{
   /**
	 * Returns an array of all the classes available, built by listing all files in the classes folder and then trying to create that class.
	 *
	 * This means any empty class files (as in complety empty) will cause an exception
	 *
	 * @param   array   array of files, obtained using Kohana::list_files
	 * @return  array   an array of all the class names
	 */
	public static function classes(array $list = NULL)
	{
		if ($list === NULL)
		{
			$list = Kohana::list_files('classes');
		}

		$classes = array();

		foreach ($list as $name => $path)
		{
			if (is_array($path))
			{
				$classes += Kodoc::classes($path);
			}
			elseif (strpos($path, 'vendor') === FALSE)
			{
				// Remove "classes/" and the extension
				$class = substr($name, 8, -(strlen(EXT)));

				// Convert slashes to underscores
				$class = str_replace(DIRECTORY_SEPARATOR, '_', strtolower($class));

				$classes[$class] = $class;
			}
		}

		return $classes;
	}
}

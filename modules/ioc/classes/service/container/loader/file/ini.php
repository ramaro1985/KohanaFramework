<?php

/*
 * This file is part of the symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Service_Container_Loader_File_Ini loads parameters from INI files.
 *
 * @package    Kohana/IOC
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @modified   Rafael E. Espinosa Santiesteban <alvk4r@blackbird.org> 
 * @version    SVN: $Id$
 */
class Service_Container_Loader_File_Ini extends Service_Container_Loader_File
{
  public function doLoad($files)
  {
    $parameters = array();
    foreach ($files as $file)
    {
      $path = $this->getAbsolutePath($file);
      if (!file_exists($path))
      {
        throw new InvalidArgumentException(sprintf('The %s file does not exist.', $file));
      }

      $result = parse_ini_file($path, true);
      if (false === $result || array() === $result)
      {
        throw new InvalidArgumentException(sprintf('The %s file is not valid.', $file));
      }

      if (isset($result['parameters']) && is_array($result['parameters']))
      {
        foreach ($result['parameters'] as $key => $value)
        {
          $parameters[strtolower($key)] = $value;
        }
      }
    }

    return array(array(), $parameters);
  }
}

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
 * Service_Parameter represents a parameter reference.
 *
 * @package    Kohana/IOC
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @modified   Rafael E. Espinosa Santiesteban <alvk4r@blackbird.org> 
 * @version    SVN: $Id: Service_Reference.php 267 2009-03-26 19:56:18Z fabien $
 */
class Service_Parameter
{
  protected
    $id = null;

  /**
   * Constructor.
   *
   * @param string $id The parameter key
   */
  public function __construct($id)
  {
    $this->id = $id;
  }

  /**
   * __toString.
   *
   * @return string The parameter key
   */
  public function __toString()
  {
    return (string) $this->id;
  }
}

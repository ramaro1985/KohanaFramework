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
 * Service_Container_Dumper is the abstract class for all built-in dumpers.
 *
 * @package    Kohana/IOC
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @modified   Rafael E. Espinosa Santiesteban <alvk4r@blackbird.org> 
 * @version    SVN: $Id$
 */
abstract class Service_Container_Dumper implements Service_Container_Dumper_Interface
{
  protected $container;

  /**
   * Constructor.
   *
   * @param Service_Container_Builder $container The service container to dump
   */
  public function __construct(Service_Container_Builder $container)
  {
    $this->container = $container;
  }

  /**
   * Dumps the service container.
   *
   * @param  array  $options An array of options
   *
   * @return string The representation of the service container
   */
  public function dump(array $options = array())
  {
    throw new LogicException('You must extend this abstract class and implement the dump() method.');
  }
}

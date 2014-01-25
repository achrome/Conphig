<?php

/**
 *
 * @author    Ashwin Mukhija
 * @license   MIT
 * @link      https://github.com/Achrome/Conphig
 */
namespace Conphig\Builders;

use Conphig\Interfaces\Buildable;

abstract class AbstractBuilder implements Buildable {

  protected $_classname;

  protected $_processedArgs = [];

  protected $_mandatoryAttrs = [];

  protected $_optionalAttrs = [];

  abstract protected function processArgs($argArray);
}
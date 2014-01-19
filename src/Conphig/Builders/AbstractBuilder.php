<?php

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 */
namespace Conphig\Builders;

use Conphig\Interfaces\Buildable;

abstract class AbstractBuilder implements Buildable {

  protected $classname;

  protected $processedArgs = [ ];

  protected $mandatoryAttrs = [ ];

  protected $optionalAttrs = [ ];

  abstract protected function processArgs( $argArray );
}
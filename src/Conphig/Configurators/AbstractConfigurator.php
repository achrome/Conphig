<?php

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 */
namespace Conphig\Configurators;

use Conphig\Interfaces\Configurable;

abstract class AbstractConfigurator implements Configurable {

  /**
   *
   * @var string
   */
  protected $filePath;

  /**
   *
   * @var Configuration
   */
  protected $configuration;

  /**
   *
   * @var mixed
   */
  protected $intermediateConf;

  /**
   *
   * @var array
   */
  protected $extraParams;

  public function __construct($filePath, $extraParams = []) {
    $this->filePath = $filePath;
    $this->extraParams = $extraParams;
  }

  public function getConfiguration() {
    return $this->configuration;
  }
}
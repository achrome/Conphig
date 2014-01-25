<?php

/**
 *
 * @author    Ashwin Mukhija
 * @license   MIT
 * @link      https://github.com/Achrome/Conphig
 */
namespace Conphig\Configurators;

use Conphig\Interfaces\Configurable;

abstract class AbstractConfigurator implements Configurable {

  /**
   *
   * @var   string
   */
  protected $_filePath;

  /**
   *
   * @var   Configuration
   */
  protected $_configuration;

  /**
   *
   * @var   mixed
   */
  protected $_intermediateConf;

  /**
   *
   * @var   array
   */
  protected $_extraParams;

  public function __construct($filePath, $extraParams = []) {
    $this->_filePath = $filePath;
    $this->_extraParams = $extraParams;
  }

  public function getConfiguration() {
    return $this->_configuration;
  }
}
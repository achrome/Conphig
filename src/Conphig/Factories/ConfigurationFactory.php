<?php

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 */
namespace Conphig\Factories;

use Conphig\Interfaces\Configurable;
use \ReflectionClass;
use Conphig\Exceptions\ConfigurationException;
use Conphig\Configuration\Configuration;
use \InvalidArgumentException;
use Conphig\Configurators\AbstractConfigurator;

class ConfigurationFactory {

  /**
   *
   * @var string
   */
  private $_configPath;

  /**
   *
   * @var string
   */
  private $_configType = 'ini';

  /**
   *
   * @var array
   */
  private $_supportedTypes = [ 
      'ini' => 'Conphig\\Configurators\\IniConfigurator', 
      'xml' => 'Conphig\\Configurators\\XmlConfigurator', 
      'json' => 'Conphig\\Configurators\\JsonConfigurator'
  ];

  /**
   *
   * @var string
   */
  private $_configFileName = 'config';

  /**
   *
   * @var array
   */
  private $_configParams = [];

  public function __construct($configPath = '') {
    if (!is_string($configPath)) {
      throw new InvalidArgumentException(
          "Invalid type given. Expected string, got " . gettype($configPath)
      );
    }
    
    $this->setConfigPath($configPath);
  }

  public function setConfigPath($configPath) {
    $this->_configPath = $configPath;
    return $this;
  }

  public function setConfigType($configType) {
    $this->_configType = $configType;
    return $this;
  }

  public function setConfigFileName($fileName) {
    $this->_configFileName = $fileName;
    return $this;
  }

  public function setConfigParams($configParams) {
    $this->_configParams = $configParams;
    return $this;
  }

  public function registerConfigHandler($configType, $configClass) {
    if (!class_exists($configClass)) {
      throw new ConfigurationException(
          "Class $configClass not found. Please check that the class exists"
      );
    }
    
    if (!is_subclass_of($configClass, AbstractConfigurator::class)) {
      throw new ConfigurationException(
          "Class $configClass does not extend AbstractConfigurator"
      );
    }
    
    $this->_supportedTypes[$configType] = $configClass;
    $this->_configType = $configType;
    
    return $this;
  }

  public function getSupportedTypes() {
    return $this->_supportedTypes;
  }

  /**
   *
   * @return Configuration
   */
  public function create($fullPath = '') {
    
    $this->_assignCorrectPath($fullPath);
    
    $filePath = 
        $this->_configPath . DIRECTORY_SEPARATOR . $this->_configFileName .
        '.' . $this->_configType;
    
    if (!file_exists($filePath)) {
      throw new ConfigurationException(
          "Unable to find file at $filePath"
      );
    }
    
    if (!array_key_exists($this->_configType, $this->_supportedTypes)) {
      throw new ConfigurationException(
          "Invalid configuration type used"
      );
    }
    
    $reflectionClass = 
        new ReflectionClass($this->_supportedTypes[$this->_configType]);
    $configurator = 
        $reflectionClass->newInstanceArgs([$filePath, $this->_configParams]);
    $configurator->parseConfig();
    
    return $configurator->getConfiguration();
  }

  protected function _parseFullPath($path) {
    if (empty($path)) {
      throw new ConfigurationException(
          "Cannot parse empty paths"
      );
    }
    
    $tokens = preg_split('/\/|\\\/', $path);
    $ctokens = count($tokens); 
    $filename = $tokens[$ctokens - 1];
    
    // Check whether the path terminates with a file name or not.
    if (strpos($filename, '.') !== false ) {
      list ($name, $ext) = explode('.', $filename);
      unset($tokens[$ctokens - 1]);
    }
    
    $this->_configPath = implode(DIRECTORY_SEPARATOR, $tokens);
    if (isset($name) && $name !== '' && isset($ext) && $ext !== null) {
      $this->_configFileName = $name;
      $this->_configType = $ext;
    }
  }
  
  protected function _assignCorrectPath($fullPath = '') {
    if ($fullPath !== '') {
      $this->_parseFullPath($fullPath);
    } else if ($this->_configPath !== '') {
      $this->_parseFullPath($this->_configPath);
    } else {
      $this->_configPath = getcwd();
    }
  }
}
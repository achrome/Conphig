<?php

namespace Conphig\Factories;

use Conphig\Interfaces\Configurable;
use \ReflectionClass;
use Conphig\Exceptions\ConfigurationException;

class ConfigurationFactory {
	
	/**
	 * @var string
	 */
	private $configPath;
	
	/**
	 * @var string
	 * Allowed values = ini, 
	 */
	private $configType = 'ini';
	
	/**
	 * @var array
	 */
	private $supportedTypes = ['ini' => 'Conphig\\Configurators\\IniConfigurator',  
		'xml' => 'Conphig\\Configurators\\XmlConfigurator'];
	
	private $configFileName = 'config';
	
	/**
	 * 
	 * @var ConfigurationFactory
	 */
	private static $_instance = null;
	
	private function __construct($configPath = '') {
		$this->setConfigPath($configPath);
	}
	
	public static function getInstance($configPath = '') {
		return self::$_instance === null ? new self($configPath) : self::$_instance; 
	}
	
	public function setConfigPath($configPath) {
		$this->configPath = $configPath;
		return $this;
	}
	
	public function setConfigType($configType) {
		$this->configType = $configType;
		return $this;
	}
	
	public function setConfigFileName($fileName) {
		$this->configFileName = $fileName;
		return $this;
	}
	
	private function __clone() {}
	
	public function registerConfigHandler($configType, $configClass) {
		if(!class_exists($configClass)) {
			throw new ConfigurationException("Class $configClass not found. Please check that the class exists");
		}
		$this->supportedTypes[$configType] = $configClass;
		$this->configType = $configType;
		return $this;
	}
	
	public function getSupportedTypes() {
		return $this->supportedTypes;
	}
	
	/**
	 * @return Configurable
	 */
	public function create() {
		$filePath = $this->configPath . DIRECTORY_SEPARATOR . $this->configFileName . '.' . $this->configType;
		if(!file_exists($filePath)) {
			throw new ConfigurationException("Unable to find file: $filePath");
		}
		$reflectionClass = new ReflectionClass($this->supportedTypes[$this->configType]);
		$configurator = $reflectionClass->newInstanceArgs(array($filePath));
		$configurator->parseConfig();
		return $configurator->getConfiguration();
	}
}
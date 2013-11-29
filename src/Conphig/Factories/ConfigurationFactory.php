<?php

namespace Conphig\Factories;

use Conphig\Interfaces\Configurable;
use \ReflectionClass;
use Conphig\Exceptions\ConfigurationException;
use Conphig\Configuration\Configuration;
use \InvalidArgumentException;

class ConfigurationFactory {

	/**
	 *
	 * @var string
	 */
	private $configPath;

	/**
	 *
	 * @var string
	 */
	private $configType = 'ini';

	/**
	 *
	 * @var array
	 */
	private $supportedTypes = [ 'ini' => 'Conphig\\Configurators\\IniConfigurator', 'xml' => 'Conphig\\Configurators\\XmlConfigurator', 'json' => 'Conphig\\Configurators\\JsonConfigurator'];

	/**
	 *
	 * @var string
	 */
	private $configFileName = 'config';

	/**
	 *
	 * @var array
	 */
	private $configParams = [ ];

	public function __construct($configPath = '') {
		if (!is_string($configPath))
			throw new InvalidArgumentException("Invalid type given. Expected string, got " . gettype($configPath));
		
		$this->setConfigPath($configPath);
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

	public function setConfigParams($configParams) {
		$this->setConfigParams($configParams);
		return $this;
	}

	public function registerConfigHandler($configType, $configClass) {
		if (!class_exists($configClass))
			throw new ConfigurationException("Class $configClass not found. Please check that the class exists");
		
		if (!is_a($configClass, 'Conphig\\Interfaces\\Configurable'))
			throw new ConfigurationException("Class $configClass does not implement the Configurable interface");
		
		$this->supportedTypes[$configType] = $configClass;
		$this->configType = $configType;
		
		return $this;
	}

	public function getSupportedTypes() {
		return $this->supportedTypes;
	}

	/**
	 *
	 * @return Configuration
	 */
	public function create() {
		if ($this->configPath === '')
			throw new ConfigurationException("The configuration path is not set");
		
		$filePath = $this->configPath . DIRECTORY_SEPARATOR . $this->configFileName . '.' . $this->configType;
		if (!file_exists($filePath))
			throw new ConfigurationException("Unable to find file: $filePath");
		
		if(!array_key_exists($this->configType, $this->supportedTypes))
			throw new ConfigurationException("Invalid configuration type used");
			
		$reflectionClass = new ReflectionClass($this->supportedTypes[$this->configType]);
		$configurator = $reflectionClass->newInstanceArgs(array ($filePath, $this->configParams));
		$configurator->parseConfig();
		
		return $configurator->getConfiguration();
	}
}
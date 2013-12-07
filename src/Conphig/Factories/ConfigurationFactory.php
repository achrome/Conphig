<?php

namespace Conphig\Factories;

use Conphig\Interfaces\Configurable;
use \ReflectionClass;
use Conphig\Exceptions\ConfigurationException;
use Conphig\Configuration\Configuration;
use \InvalidArgumentException;

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 *
 */
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
	public function create($fullPath = '') {
		if ($fullPath !== '') {
			$this->parseFullPath($fullPath);
		}
		if($this->configPath === '')
			$this->configPath = getcwd();
		
		$filePath = $this->configPath . DIRECTORY_SEPARATOR . $this->configFileName . '.' . $this->configType;
		if (!file_exists($filePath))
			throw new ConfigurationException("Unable to find file at $filePath");
		
		if (!array_key_exists($this->configType, $this->supportedTypes))
			throw new ConfigurationException("Invalid configuration type used");
		
		$reflectionClass = new ReflectionClass($this->supportedTypes[$this->configType]);
		$configurator = $reflectionClass->newInstanceArgs(array ($filePath, $this->configParams));
		$configurator->parseConfig();
		
		return $configurator->getConfiguration();
	}

	protected function parseFullPath($path) {
		$tokens = preg_split('/\/|\\\/', $path);
		$filename = $tokens[count($tokens) - 1];
		
		//Check whether the path terminates with a file name or not.
		if(strpos($filename, '.') !== FALSE) {
			list($name, $ext) = explode('.', $filename);
			unset($tokens[count($tokens) - 1]);
		}
		
		$this->configPath = implode(DIRECTORY_SEPARATOR, $tokens);
		if(isset($name) && $name !== '' && isset($ext) && $ext !== NULL) {
			$this->configFileName = $name;
			$this->configType = $ext;
		}
	}
}
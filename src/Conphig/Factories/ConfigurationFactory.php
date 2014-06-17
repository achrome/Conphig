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

class ConfigurationFactory
{
  
    const CONFIGURATOR_PATH = "Conphig\\Configurators\\";

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
    private $supportedTypes = [
        'ini' => "IniConfigurator",
        'xml' => "XmlConfigurator",
        'json' => "JsonConfigurator"
    ];

    /**
     *
     * @var string
     */
    private $configFileName = 'config';

    /**
     *
     * @var array
     */
    private $configParams = [];

    public function __construct($configPath = '')
    {
        if (!is_string($configPath)) {
            $type = gettype($configPath);
            $errMsg = "Invalid type given. Expected string, got {$type}";
            throw new InvalidArgumentException($errMsg);
        }
    
        $this->supportedTypes = array_map(function ($type) {
            return self::CONFIGURATOR_PATH . $type;
        }, $this->supportedTypes);
        $this->setConfigPath($configPath);
    }

    public function setConfigPath($configPath)
    {
        $this->configPath = $configPath;
        return $this;
    }

    public function setConfigType($configType)
    {
        $this->configType = $configType;
        return $this;
    }

    public function setConfigFileName($fileName)
    {
        $this->configFileName = $fileName;
        return $this;
    }

    public function setConfigParams($configParams)
    {
        $this->configParams = $configParams;
        return $this;
    }

    public function registerConfigHandler($configType, $configClass)
    {
        if (!class_exists($configClass)) {
            $errMsg = "Class {$configClass} not found. Please check that the class exists.";
            throw new ConfigurationException($errMsg);
        }
    
        if (!is_subclass_of($configClass, AbstractConfigurator::class)) {
            $errMsg = "Class {$configClass} does not extend Conphig\Configurators\AbstractConfigurator";
            throw new ConfigurationException($errMsg);
        }
    
        $this->supportedTypes[$configType] = $configClass;
        $this->configType = $configType;
        return $this;
    }

    public function getSupportedTypes()
    {
        return $this->supportedTypes;
    }

    /**
     *
     * @return Configuration
     */
    public function create($fullPath = '')
    {
        $this->assignCorrectPath($fullPath);
        $filePath = $this->configPath . DIRECTORY_SEPARATOR . $this->configFileName .
            '.' . $this->configType;
    
        if (!file_exists($filePath)) {
            throw new ConfigurationException("Unable to find file at {$filePath}");
        }
    
        if (!array_key_exists($this->configType, $this->supportedTypes)) {
            throw new ConfigurationException("Invalid configuration type used");
        }
    
        $reflectorType = $this->supportedTypes[$this->configType];
        $reflectionClass = new ReflectionClass($reflectorType);
        $configurator = $reflectionClass->newInstanceArgs([$filePath, $this->configParams]);
        $configurator->parseConfig();
    
        return $configurator->getConfiguration();
    }

    protected function parseFullPath($path)
    {
        if (empty($path)) {
            throw new ConfigurationException("Cannot parse empty paths");
        }
    
        $tokens = preg_split('/\/|\\\/', $path);
        $tokenCount = count($tokens);
        $filename = $tokens[$tokenCount - 1];
    
        // Check whether the path terminates with a file name or not.
        if (strpos($filename, '.') !== false) {
            list($name, $ext) = explode('.', $filename);
            unset($tokens[$tokenCount - 1]);
        }
    
        $this->configPath = implode(DIRECTORY_SEPARATOR, $tokens);
        if (isset($name) && $name !== '' && isset($ext) && $ext !== null) {
            $this->configFileName = $name;
            $this->configType = $ext;
        }
    }

    protected function assignCorrectPath($fullPath = '')
    {
        if ($fullPath !== '') {
            $this->parseFullPath($fullPath);
        } elseif ($this->configPath !== '') {
            $this->parseFullPath($this->configPath);
        } else {
            $this->configPath = getcwd();
        }
    }
}

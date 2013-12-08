<?php

namespace Conphig\Configuration;

use \ReflectionClass;
use \ReflectionException;
use Conphig\Exceptions\ConfigurationException;

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 */
class Configuration {

	private $registry = [ ];

	public function __get($key) {
		return $this->registry[$key];
	}

	public function __set($key, $value) {
		$this->registry[$key] = $value;
	}

	public function build() {
		$builtReg = [ ];
		foreach ($this->registry as $key => $config) {
			$builtReg[$key] = $this->recursivelyBuildClasses($config);
		}
		$this->registry = $builtReg;
		return $this;
	}

	protected function getRegistry() {
		return $this->registry;
	}

	protected function recursivelyBuildClasses(Configuration $config) {
		if (isset($config->getRegistry()['class'])) {
			$classname = str_replace('\\', '\\\\', $config->class);
			if (class_exists($classname)) {
				$reflector = new ReflectionClass($classname);
			} else if (class_exists('\\\\' . $classname)) {
				$classname = '\\\\' . $classname;
				$reflector = new ReflectionClass($classname);
			} else {
				throw new ConfigurationException("Unable to resolve class name $classname");
			}
			
			if (method_exists($classname, '__construct')) {
				$objArgs = $config->getRegistry();
				unset($objArgs['class']);
				$obj = $reflector->newInstanceArgs($objArgs);
			} else {
				$obj = $reflector->newInstanceWithoutConstructor();
			}
			return $obj;
		} else {
			return $config;
		}
	}
}
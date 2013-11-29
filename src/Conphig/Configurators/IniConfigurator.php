<?php

namespace Conphig\Configurators;

use Conphig\Configuration\Configuration;
class IniConfigurator extends AbstractConfigurator {
	
	public function parseConfig() {
		$this->intermediateConf = parse_ini_file($this->filePath, true);
		//var_dump($this->intermediateConf);
		$this->createConfig();
		return $this->configuration;
	}
	
	function createConfig() {
		$this->configuration = $this->recursivelyCreateConfig($this->intermediateConf);
		return $this->configuration;
	}
	
	private function recursivelyCreateConfig($confArray) {
		$configuration = new Configuration;
		foreach($confArray as $key => $value) {
			$configuration->$key = is_array($value) ? $this->recursivelyCreateConfig($value) : $value;
		}
		return $configuration;
	}
}
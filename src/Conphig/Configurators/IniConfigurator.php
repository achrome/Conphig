<?php

namespace Conphig\Configurators;

use Conphig\Configuration\Configuration;
class IniConfigurator extends AbstractConfigurator {
	
	public function parseConfig() {
		$this->intermediateConf = parse_ini_file($this->filePath, true);
		$this->configuration = $this->recursivelyCreateConfig($this->intermediateConf);
	}
	
	private function recursivelyCreateConfig($confArray) {
		$configuration = new Configuration;
		foreach($confArray as $key => $value) {
			$configuration->$key = is_array($value) ? $this->recursivelyCreateConfig($value) : $value;
		}
		return $configuration;
	}
}
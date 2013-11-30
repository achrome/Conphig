<?php
namespace Conphig\Configurators;

use Conphig\Exceptions\ConfigurationException;
use Conphig\Helpers\ConfiguratorHelper;
class JsonConfigurator extends AbstractConfigurator {
	
	public function parseConfig() {
		$json = file_get_contents($this->filePath);
		if($json === FALSE)
			throw new ConfigurationException("Unable to read config file");
		
		$this->intermediateConf = json_decode($json, true);
		$this->configuration = (new ConfiguratorHelper())->createObjFromArray($this->intermediateConf);
	}
}
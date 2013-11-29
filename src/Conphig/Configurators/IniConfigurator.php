<?php

namespace Conphig\Configurators;

use Conphig\Configuration\Configuration;
use Conphig\Helpers\ConfiguratorHelper;

class IniConfigurator extends AbstractConfigurator {

	public function parseConfig() {
		$this->intermediateConf = parse_ini_file($this->filePath, true);
		$this->configuration = (new ConfiguratorHelper())->createObjFromArray($this->intermediateConf);
	}
}
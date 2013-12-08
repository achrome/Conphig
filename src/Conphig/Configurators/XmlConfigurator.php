<?php

namespace Conphig\Configurators;

use Conphig\Exceptions\ConfigurationException;
use Conphig\Configuration\Configuration;

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 *      
 */
class XmlConfigurator extends AbstractConfigurator {

	public function parseConfig() {
		if (!extension_loaded('libxml'))
			throw new ConfigurationException("libxml is not loaded");
		
		$this->intermediateConf = simplexml_load_file($this->filePath);
		if ($this->intermediateConf->getName() !== 'config') {
			throw new ConfigurationException("All configuration must be wrapped inside <config></config>");
		}
		
		$this->configuration = $this->createConfigFromXmlElements($this->intermediateConf);
	}

	protected function createConfigFromXmlElements($node) {
		$configuration = new Configuration();
		foreach ($node->children() as $childNode) {
			$configuration->{$childNode->getName()} = ($childNode->count() > 0) ? $this->createConfigFromXmlElements($childNode) : (string) $childNode;
		}
		return $configuration;
	}
}
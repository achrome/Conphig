<?php

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 */
namespace Conphig\Configurators;

use Conphig\Exceptions\ConfigurationException;
use Conphig\Configuration\Configuration;

class XmlConfigurator extends AbstractConfigurator {
  
  public function parseConfig() {
    if (!$this->_isLibxmlLoaded()) {
      throw new ConfigurationException(
          "libxml is not loaded"
      );
    }
    
    $this->_intermediateConf = simplexml_load_file($this->_filePath);
    if ($this->_intermediateConf->getName() !== 'config') {
      throw new ConfigurationException(
          "All configuration must be wrapped inside <config></config>"
      );
    }
    
    $this->_configuration = 
        $this->_createConfigFromXmlElements($this->_intermediateConf);
  }

  protected function _createConfigFromXmlElements($node) {
    $configuration = new Configuration;
    foreach($node->children() as $childNode) {
      $nodeName = $childNode->getName();
      $configuration->{$nodeName} = 
          ($childNode->count() > 0 ) ? 
          $this->_createConfigFromXmlElements($childNode) : 
          (string) $childNode;
    }
    
    return $configuration;
  }
  
  protected function _isLibxmlLoaded() {
    return extension_loaded('libxml');
  }
}
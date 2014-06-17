<?php

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 */
namespace Conphig\Configurators;

use Conphig\Exceptions\ConfigurationException;
use Conphig\Helpers\ConfiguratorHelper;

class JsonConfigurator extends AbstractConfigurator
{

    public function parseConfig()
    {
        $jsonConf = file_get_contents($this->filePath);
        if ($jsonConf === false) {
            throw new ConfigurationException("Unable to read config file");
        }
    
        $this->intermediateConf = json_decode($jsonConf, true);
        $this->configuration = (new ConfiguratorHelper)->createObjFromArray($this->intermediateConf);
    }
}

<?php

namespace Conphig\Configurators;

use Conphig\Interfaces\Configurable;

abstract class AbstractConfigurator implements Configurable {
	
	/**
	 * @var string
	 */
	protected $filePath;
	
	/**
	 * @var Configuration
	 */
	protected $configuration;
	
	/**
	 * @var mixed
	 */
	protected $intermediateConf;
	
	public function __construct($filePath) {
		$this->filePath = $filePath;
	}
	
}
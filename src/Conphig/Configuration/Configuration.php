<?php

namespace Conphig\Configuration;

/**
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 */
class Configuration {
	
	private $registry = [];
	
	public function __get($key) {
		return $this->registry[$key];
	}
	
	public function __set($key, $value) {
		$this->registry[$key] = $value;
	}
}
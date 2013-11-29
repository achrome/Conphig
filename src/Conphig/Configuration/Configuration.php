<?php

namespace Conphig\Configuration;

class Configuration {
	
	private $registry = [];
	
	public function __get($key) {
		return $this->registry[$key];
	}
	
	public function __set($key, $value) {
		$this->registry[$key] = $value;
	}
}
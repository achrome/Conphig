<?php

namespace Conphig\Interfaces;

interface Configurable {
	
	public function parseConfig();
	
 	function createConfig();
}
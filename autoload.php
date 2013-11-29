<?php

/**
 * Author - Ashwin Mukhija (http://github.com/achrome)
 * Path - Conphig/autoload.php
 * Description - If not using Composer, this file will support autoloading.
 * Usage - require 'Conphig/autoload.php'
 */

/**
 * The autoload function will pick up all the classes from the Conphig namespace, including classes from the sub-namespaces
 */
spl_autoload_register(function ($className) {
	// Only load the class under the Conphig
	if (strpos($className, 'Conphig\\') !== 0) return;
	
	// Compute the file path and make it OS agnostic
	$className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
	$fileName = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . "$className.php";
	
	// Add a safety check to avoid unforeseen errors
	if (file_exists($fileName)) require $fileName;
});


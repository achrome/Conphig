<?php

/**
 * @author - Ashwin Mukhija (http://github.com/achrome)
 * If not using Composer, this file will support autoloading.
 */

/**
 * The autoload function will pick up all the classes from the Conphig namespace, 
 * including classes from the sub-namespaces
 */

define('DS', DIRECTORY_SEPARATOR);

spl_autoload_register(function($className) {
  // Only load the class under the Conphig
  if(strpos($className, "Conphig\\") !== 0) { 
    return false;
  }
	
  // Compute the file path and make it OS agnostic
  $className = str_replace("\\", DS, $className);
  $fileName = __DIR__ . DS . 'src' . DS . "$className.php";
  
  // Add a safety check to avoid unforeseen errors
  if(file_exists($fileName)) {
    require $fileName;
    return true;
  }
  return false;
});
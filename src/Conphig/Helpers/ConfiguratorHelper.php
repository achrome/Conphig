<?php

/**
 *
 * @author Ashwin Mukhija
 * @license MIT
 * @link https://github.com/Achrome/Conphig
 */
namespace Conphig\Helpers;

use \InvalidArgumentException;
use \ReflectionClass;
use \Exception;

class ConfiguratorHelper
{

    const DEFAULT_OBJ_TYPE = 'Conphig\\Configuration\\Configuration';
  
    public function createObjFromArray($inArray)
    {
    
        $outObj = (new ReflectionClass(self::DEFAULT_OBJ_TYPE))->newInstanceWithoutConstructor();
    
        foreach ($inArray as $key => $value) {
            if (is_array($value)) {
                $outObj->$key = $this->createObjFromArray($value);
            } else {
                $outObj->$key = $value;
            }
        }
        return $outObj;
    }
}

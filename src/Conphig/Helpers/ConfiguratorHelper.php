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

class ConfiguratorHelper {

  public function createObjFromArray( $inArray, 
      $objType = 'Conphig\\Configuration\\Configuration', $objArgs = [] ) {
    if ( !is_array( $inArray ) ) throw new InvalidArgumentException( 
        "Expected an array, got a " .
             gettype( $inArray ) );
    
    $reflector = new ReflectionClass( $objType );
    $outObj = method_exists( $objType, '__construct' ) ? $reflector->newInstance( 
        $objArgs ) : $reflector->newInstanceWithoutConstructor( );
    
    if ( !method_exists( $outObj, '__set' ) ||
         !method_exists( $outObj, '__get' ) ) throw new Exception( 
        "$objType does not implement methods __get and __set" );
    
    foreach ( $inArray as $key => $value )
      $outObj->$key = is_array( $value ) ? $this->createObjFromArray( $value, 
          $objType, $objArgs ) : $value;
    
    return $outObj;
  }
}
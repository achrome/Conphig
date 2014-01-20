<?php
/**
 *
 * @author Ashwin Mukhija
 * @package Conphig
 *         The test file for ConfigurationFactory
 */
use \PHPUnit_Framework_TestCase;
use \ReflectionClass;
use \Exception;
use Conphig\Factories\ConfigurationFactory;
use Conphig\Exceptions\ConfigurationException;
use Conphig\Configuration\Configuration;

/**
 * 
 * @coversDefaultClass Conphig\Factories\ConfigurationFactory
 *
 */
class ConfigurationFactoryTest extends PHPUnit_Framework_TestCase {

  /**
   * @covers ::getSupportedTypes
   * @test
   */
  public function defaultSupportedTypesShouldBe3( ) {
    $factory = new ConfigurationFactory( );
    $this->assertEquals( count( $factory->getSupportedTypes( ) ), 3 );
  }

  /**
   * @covers ::__construct
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Invalid type given. Expected string, got array
   * @test
   */
  public function newInstanceShouldStrictlyEnforceString( ) {
    $factory = new ConfigurationFactory( [ ] );
  }

  /**
   * @covers ::setConfigFileName
   * @covers ::setConfigType
   * @covers ::setConfigPath
   * @test
   * @requires PHP 5.5.0
   */
  public function mutatorFunctionsShouldNotChangeTypeOfFactory( ) {
    $factory = new ConfigurationFactory( );
    $test = $factory->setConfigFileName( 'foo' );
    $this->assertEquals( $test, $factory );
    $this->assertInstanceOf( ConfigurationFactory::class, $test );
    $test = $factory->setConfigPath( 'bar' );
    $this->assertInstanceOf( ConfigurationFactory::class, $test );
    $this->assertEquals( $test, $factory );
    $test = $factory->setConfigType( 'baz' );
    $this->assertInstanceOf( ConfigurationFactory::class, $test );
    $this->assertEquals( $test, $factory );
  }

  /**
   * @covers ::create
   * @expectedException Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage Unable to find file at foobar
   * @test
   */
  public function creationWithInvalidPathShouldFail( ) {
    $factory = new ConfigurationFactory( );
    $factory->create( 'foobar' );
  }

  /**
   * @covers ::create
   * @test
   * @requires PHP 5.5.0
   */
  public function creationWithValidPathShouldPass( ) {
    $factory = new ConfigurationFactory( FIXTURES_PATH );
    $conf = $factory->create( );
    $this->assertInstanceOf( Configuration::class, $conf );
  }

  /**
   * @covers ::registerConfigHandler
   * @expectedException Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage Class bar not found. Please check that the class exists
   * @test
   */
  public function registerNonexistentHandlerShouldFail( ) {
    $factory = new ConfigurationFactory( );
    $factory->registerConfigHandler( 'foo', 'bar' );
  }

  /**
   * @covers ::registerConfigHandler
   * @expectedException Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage Class stdClass does not implement the Configurable interface
   * @test
   */
  public function registerInvalidHandlerShouldFail( ) {
    $factory = new ConfigurationFactory( );
    $factory->registerConfigHandler( 'foo', \stdClass::class );
  }
  
  /**
   * @covers ::setConfigPath
   * @test
   * @requires PHP 5.5.0
   * @requires ReflectionProperty::setAccessible
   */
  public function privateConfigPathShouldBeCorrectlySet( ) {
    $factory = new ConfigurationFactory( );
    $refl = new ReflectionClass( ConfigurationFactory::class );
    $prop = $refl->getProperty( 'configPath' );
    $prop->setAccessible( TRUE );
    $method = $refl->getMethod( 'setConfigPath' );
    $val = $prop->getValue( $factory );
    $this->assertEmpty( $val );
    $method->invoke( $factory, 'foo' );
    $val = $prop->getValue( $factory );
    $this->assertEquals( $val, 'foo' );
  }
  
  /**
   * @covers ::setConfigType
   * @test
   * @requires PHP 5.5.0
   * @requires ReflectionProperty::setAccessible
   */
  public function privateConfigTypeShouldBeCorrectlySet( ) {
    $factory = new ConfigurationFactory( );
    $refl = new ReflectionClass( ConfigurationFactory::class );
    $prop = $refl->getProperty( 'configType' );
    $prop->setAccessible( TRUE );
    $val = $prop->getValue( $factory );
    $this->assertEquals( $val, 'ini' );
    $method = $refl->getMethod( 'setConfigType' );
    $method->invoke( $factory, 'bar' );
    $val = $prop->getValue( $factory );
    $this->assertEquals( $val, 'bar' );
  }
  
  /**
   * @covers ::setConfigFileName
   * @test
   * @requires PHP 5.5.0
   * @requires ReflectionProperty::setAccessible
   */
  public function privateConfigFileNameShouldBeCorrectlySet( ) {
    $factory = new ConfigurationFactory( );
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $prop = $refl->getProperty( 'configFileName' );
    $prop->setAccessible( TRUE );
    $val = $prop->getValue( $factory );
    $this->assertEquals( $val, 'config' );
    $method = $refl->getMethod( 'setConfigFileName' );
    $method->invoke( $factory, 'baz' );
    $val = $prop->getValue( $factory );
    $this->assertEquals( $val, 'baz' );
  }
  
  /**
   * @covers ::parseFullPath
   * @test
   * @requires PHP 5.5.0
   * @requires ReflectionMethod::setAccessible
   */
  public function protectedParseFullPathShouldWork( ) {
    $factory = new ConfigurationFactory( );
    $refl = new ReflectionClass( ConfigurationFactory::class );
    $rConfigType = $refl->getProperty( 'configType' );
    $rConfigPath = $refl->getProperty( 'configPath' );
    $rConfigFileName = $refl->getProperty( 'configFileName' );
    $rConfigType->setAccessible( TRUE );
    $rConfigPath->setAccessible( TRUE );
    $rConfigFileName->setAccessible( TRUE );
    $method = $refl->getMethod( 'parseFullPath' );
    $method->setAccessible( TRUE );
    
    $path = DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar';
    $method->invoke( $factory, $path );
    $this->assertEquals( $rConfigPath->getValue( $factory ), $path );
    $this->assertEquals( $rConfigFileName->getValue( $factory ), 'config' );
    $this->assertEquals( $rConfigType->getValue( $factory ), 'ini' );
    
    $fullpath = $path . DIRECTORY_SEPARATOR . 'baz.quux';
    $method->invoke( $factory, $fullpath );
    $this->assertEquals( $rConfigPath->getValue( $factory ), $path );
    $this->assertEquals( $rConfigFileName->getValue( $factory ), 'baz' );
    $this->assertEquals( $rConfigType->getValue( $factory ), 'quux' );
  }
  
  /**
   * @covers ::create
   * @test
   * @expectedException Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage Invalid configuration type used 
   */
  public function createWithInvalidTypeShouldFail( ) {
    $factory = new ConfigurationFactory( FIXTURES_PATH . DIRECTORY_SEPARATOR . 'foo.bar' );
    $factory->create( );
  }
  
  /**
   * @covers ::parseFullPath
   * @test
   * @expectedException Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage Cannot parse empty path
   * @requires PHP 5.5.0
   * @requires ReflectionMethod::setAccessible
   */
  public function parseFullPathWithEmptyPathShouldFail( ) {
    $factory = new ConfigurationFactory( );
    $refl = new ReflectionClass( ConfigurationFactory::class );
    $method = $refl->getMethod( 'parseFullPath' );
    $method->setAccessible( TRUE );
    $method->invoke( $factory, '' );
  }
  
  /**
   * @covers ::parseFullPath
   * @test
   * @expectedException Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage Cannot parse empty path
   * @requires PHP 5.5.0
   * @requires ReflectionMethod::setAccessible
   */
  public function parseFullPathWithNullPathShouldFail( ) {
    $factory = new ConfigurationFactory( );
    $refl = new ReflectionClass( ConfigurationFactory::class );
    $method = $refl->getMethod( 'parseFullPath' );
    $method->setAccessible( TRUE );
    $method->invoke( $factory, NULL );
  }
}
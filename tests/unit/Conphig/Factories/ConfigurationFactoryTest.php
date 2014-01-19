<?php
/**
 *
 * @author Ashwin Mukhija
 *         The test file for ConfigurationFactory
 */
use Conphig\Factories\ConfigurationFactory;
use Conphig\Exceptions\ConfigurationException;
use Conphig\Configuration\Configuration;

class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Conphig\Factories\ConfigurationFactory::getSupportedTypes
   */
  public function test_Default_Supported_Types( ) {
    $factory = new ConfigurationFactory( );
    $this->assertEquals( count( $factory->getSupportedTypes( ) ), 3 );
  }

  /**
   * @covers Conphig\Factories\ConfigurationFactory::__construct
   * @expectedException InvalidArgumentException
   */
  public function test_String_Checking_For_New( ) {
    $factory = new ConfigurationFactory( [ ] );
  }

  /**
   * @covers Conphig\Factories\ConfigurationFactory::setConfigFileName
   * @covers Conphig\Factories\ConfigurationFactory::setConfigType
   * @covers Conphig\Factories\ConfigurationFactory::setConfigPath
   */
  public function test_Mutator_Functions( ) {
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
   * @covers Conphig\Factories\ConfigurationFactory::create
   * @expectedException Conphig\Exceptions\ConfigurationException
   */
  public function test_Configuration_Creation_With_Invalid_Path( ) {
  	$factory = new ConfigurationFactory();
  	$factory->create('foobar');
  }
  
  /**
   * @covers Conphig\Factories\ConfigurationFactory::create
   */
  public function test_Configuration_Creation_With_Valid_Path( ) {
    $factory = new ConfigurationFactory( FIXTURES_PATH );
    $conf = $factory->create( );
    $this->assertInstanceOf( Configuration::class, $conf );
  }
}
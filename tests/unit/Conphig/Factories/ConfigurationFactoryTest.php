<?php
/**
 *
 * @author    Ashwin Mukhija
 * @package   Conphig
 *
 */
use \PHPUnit_Framework_TestCase;
use \ReflectionClass;
use \Exception;
use \stdClass;
use Conphig\Factories\ConfigurationFactory;
use Conphig\Exceptions\ConfigurationException;
use Conphig\Configuration\Configuration;
use Conphig\Configurators\IniConfigurator;
use Tests\FooConfigurator;

/**
 * @coversDefaultClass Conphig\Factories\ConfigurationFactory
 */
class ConfigurationFactoryTest extends PHPUnit_Framework_TestCase {

  /**
   * @covers                    ::getSupportedTypes
   * @test
   */
  public function defaultSupportedTypesShouldBe3() {
    $factory = new ConfigurationFactory;
    $this->assertEquals(count($factory->getSupportedTypes()), 3);
  }

  /**
   * @covers                    ::__construct
   * @expectedException         InvalidArgumentException
   * @expectedExceptionMessage  Invalid type given. Expected string, got array
   * @test
   */
  public function newInstanceShouldStrictlyEnforceString() {
    $factory = new ConfigurationFactory([]);
  }

  /**
   * @covers                    ::__construct
   * @test
   */
  public function newInstanceWithoutArgsShouldPass() {
    $factory = new ConfigurationFactory;
    $this->assertInstanceOf(ConfigurationFactory::class, $factory);
  }

  /**
   * @covers                    ::__construct
   * @requires                  ReflectionProperty::setAccessible
   * @test
   */
  public function newInstanceShouldCorrectlySetPrivateVars() {
    $factory = new ConfigurationFactory(__DIR__);
    $refl = new ReflectionClass(ConfigurationFactory::class);
    
    $rConfigPath = $refl->getProperty('configPath');
    $rConfigFileName = $refl->getProperty('configFileName');
    $rConfigType = $refl->getProperty('configType');
    $rConfigParams = $refl->getProperty('configParams');
    
    $rConfigFileName->setAccessible(true);
    $rConfigPath->setAccessible(true);
    $rConfigType->setAccessible(true);
    $rConfigParams->setAccessible(true);
    
    $this->assertEquals($rConfigFileName->getValue($factory), 'config');
    $this->assertEquals($rConfigPath->getValue($factory), __DIR__);
    $this->assertEquals($rConfigType->getValue($factory), 'ini');
    $this->assertEquals($rConfigParams->getValue($factory), []);
    
    $path = FIXTURES_PATH . DIRECTORY_SEPARATOR . 'foo.bar';
    $factory = new ConfigurationFactory($path);
    $this->assertEquals($rConfigPath->getValue($factory), $path);
  }

  /**
   * @covers                    ::setConfigFileName
   * @covers                    ::setConfigType
   * @covers                    ::setConfigPath
   * @covers                    ::setConfigParams
   * @test
   */
  public function mutatorFunctionsShouldNotChangeTypeOfFactory() {
    $factory = new ConfigurationFactory;
    $test = $factory->setConfigFileName('foo');
    $this->assertEquals($test, $factory);
    $this->assertInstanceOf(ConfigurationFactory::class, $test);
    
    $test = $factory->setConfigPath('bar');
    $this->assertInstanceOf( ConfigurationFactory::class, $test);
    $this->assertEquals($test, $factory);
    
    $test = $factory->setConfigType('baz');
    $this->assertInstanceOf(ConfigurationFactory::class, $test);
    $this->assertEquals($test, $factory);
    
    $test = $factory->setConfigParams('quux');
    $this->assertInstanceOf(ConfigurationFactory::class, $test);
    $this->assertEquals($test, $factory);
  }

  /**
   * @covers                    ::create
   * @covers                    ::assignCorrectPath
   * @expectedException         Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage  Unable to find file at foobar
   * @test
   */
  public function creationWithInvalidPathShouldFail() {
    $factory = new ConfigurationFactory;
    $factory->create('foobar');
  }

  /**
   * @covers                    ::create
   * @covers                    ::assignCorrectPath
   * @test
   */
  public function creationWithValidPathShouldPass() {
    $factory = new ConfigurationFactory(FIXTURES_PATH);
    $conf = $factory->create();
    $this->assertInstanceOf(Configuration::class, $conf);
  }

  /**
   * @covers                    ::registerConfigHandler
   * @expectedException         Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage  Class bar not found. Please check that the class exists
   * @test
   */
  public function registerNonexistentHandlerShouldFail() {
    $factory = new ConfigurationFactory;
    $factory->registerConfigHandler('foo', 'bar');
  }

  /**
   * @covers                    ::registerConfigHandler
   * @expectedException         Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage  Class stdClass does not extend Conphig\Configurators\AbstractConfigurator
   * @test
   */
  public function registerInvalidHandlerShouldFail() {
    $factory = new ConfigurationFactory;
    $factory->registerConfigHandler('foo', stdClass::class);
  }
  
  /**
   * @covers                    ::registerConfigHandler
   * @test
   */
  public function registerValidHandlerShouldPass() {
    $factory = new ConfigurationFactory;
    require FIXTURES_PATH . DIRECTORY_SEPARATOR . 'FooConfigurator.php';
    $factory->registerConfigHandler('foo', 'FooConfigurator');
    $this->assertEquals(count($factory->getSupportedTypes()), 4);
    $this->assertContains('FooConfigurator', $factory->getSupportedTypes());
    $this->assertEquals($factory->getSupportedTypes()['foo'], 'FooConfigurator');
    
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $rConfigType = $refl->getProperty('configType');
    $rConfigType->setAccessible(true);
    $value = $rConfigType->getValue($factory);
    $this->assertEquals($value, 'foo');
  }

  /**
   * @covers                    ::setConfigPath
   * @test
   */
  public function privateConfigPathShouldBeCorrectlySet() {
    $factory = new ConfigurationFactory;
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $prop = $refl->getProperty('configPath');
    $prop->setAccessible(true);
    
    $val = $prop->getValue($factory);
    $this->assertEmpty($val);
    
    $factory->setConfigPath('foo');
    $val = $prop->getValue($factory);
    $this->assertEquals($val, 'foo');
  }

  /**
   * @covers                    ::setConfigType
   * @test
   */
  public function privateConfigTypeShouldBeCorrectlySet() {
    $factory = new ConfigurationFactory;
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $prop = $refl->getProperty('configType');
    $prop->setAccessible(true);
    $val = $prop->getValue($factory);
    $this->assertEquals($val, 'ini');
    
    $factory->setConfigType('bar');
    $val = $prop->getValue($factory);
    $this->assertEquals($val, 'bar');
  }

  /**
   * @covers                      ::setConfigFileName
   * @test
   */
  public function privateConfigFileNameShouldBeCorrectlySet() {
    $factory = new ConfigurationFactory;
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $prop = $refl->getProperty('configFileName');
    $prop->setAccessible(true);
    $val = $prop->getValue($factory);
    $this->assertEquals($val, 'config');
    
    $factory->setConfigFileName('baz');
    $val = $prop->getValue($factory);
    $this->assertEquals($val, 'baz');
  }

  /**
   * @covers                    ::setConfigParams
   * @test
   */
  public function privateConfigParamsShouldBeCorrectlySet() {
    $factory = new ConfigurationFactory;
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $prop = $refl->getProperty('configParams');
    $prop->setAccessible(true);
    $val = $prop->getValue($factory);
    $this->assertEquals($val, []);
    $this->assertEmpty($val);
    
    $factory->setConfigParams(['foo', 'bar']);
    $val = $prop->getValue($factory);
    $this->assertEquals($val, ['foo', 'bar']);
  }

  /**
   * @covers                    ::parseFullPath
   * @test
   */
  public function protectedParseFullPathShouldWork() {
    $factory = new ConfigurationFactory;
    $refl = new ReflectionClass(ConfigurationFactory::class);
    
    $rConfigType = $refl->getProperty('configType');
    $rConfigPath = $refl->getProperty('configPath');
    $rConfigFileName = $refl->getProperty('configFileName');
    
    $rConfigType->setAccessible(true);
    $rConfigPath->setAccessible(true);
    $rConfigFileName->setAccessible(true);
    $method = $refl->getMethod('parseFullPath');
    $method->setAccessible(true);
    
    $path = DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar';
    $method->invoke($factory, $path);
    $this->assertEquals($rConfigPath->getValue($factory), $path);
    $this->assertEquals($rConfigFileName->getValue($factory), 'config');
    $this->assertEquals($rConfigType->getValue($factory), 'ini');
    
    $fullpath = $path . DIRECTORY_SEPARATOR . 'baz.quux';
    $method->invoke($factory, $fullpath);
    $this->assertEquals($rConfigPath->getValue($factory), $path);
    $this->assertEquals($rConfigFileName->getValue($factory), 'baz');
    $this->assertEquals($rConfigType->getValue($factory), 'quux');
  }

  /**
   * @covers                    ::create
   * @covers                    ::assignCorrectPath
   * @expectedException         Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage  Invalid configuration type used
   * @test
   */
  public function createWithInvalidTypeShouldFail() {
    $factory = new ConfigurationFactory(
        FIXTURES_PATH . DIRECTORY_SEPARATOR . 'foo.bar'
    );
    $factory->create();
  }

  /**
   * @covers                    ::parseFullPath
   * @expectedException         Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage  Cannot parse empty path
   * @test
   */
  public function parseFullPathWithEmptyPathShouldFail() {
    $factory = new ConfigurationFactory;
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $method = $refl->getMethod('parseFullPath');
    $method->setAccessible(true);
    $method->invoke($factory, '');
  }

  /**
   * @covers                    ::parseFullPath
   * @expectedException         Conphig\Exceptions\ConfigurationException
   * @expectedExceptionMessage  Cannot parse empty path
   * @test
   */
  public function parseFullPathWithNullPathShouldFail() {
    $factory = new ConfigurationFactory;
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $method = $refl->getMethod('parseFullPath');
    $method->setAccessible(true);
    $method->invoke($factory, null);
  }

  /**
   * @covers                    ::create
   * @covers                    ::__construct
   * @covers                    ::assignCorrectPath
   * @covers                    Conphig\Configurators\AbstractConfigurator::__construct
   * @covers                    Conphig\Configurators\IniConfigurator::parseConfig
   * @covers                    Conphig\Configurators\AbstractConfigurator::getConfiguration
   * @covers                    Conphig\Helpers\ConfiguratorHelper::createObjFromArray
   * @test
   */
  public function newConfigurationFromIniShouldWork() {
    $factory = new ConfigurationFactory(FIXTURES_PATH);
    $config = $factory->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
    
    $factory = new ConfigurationFactory;
    $config = $factory->create(FIXTURES_PATH);
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
    
    $factory = new ConfigurationFactory;
    $config = $factory->setConfigPath(FIXTURES_PATH)->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
  }

  /**
   * @covers                    ::create
   * @covers                    ::assignCorrectPath
   * @covers                    ::__construct
   * @covers                    Conphig\Configurators\IniConfigurator::parseConfig
   * @covers                    Conphig\Configurators\AbstractConfigurator::getConfiguration
   * @covers                    Conphig\Helpers\ConfiguratorHelper::createObjFromArray
   * @covers                    Conphig\Configurators\AbstractConfigurator::__construct
   * @test
   */
  public function newConfigurationFromAnotherIniShouldWork() {
    $path = FIXTURES_PATH . DIRECTORY_SEPARATOR . 'foo.ini';
    $factory = new ConfigurationFactory($path);
    $config = $factory->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->logging);
    $this->assertEquals($config->logging->engine, 'Monolog');
    
    $factory = new ConfigurationFactory;
    $config = $factory->create($path);
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->logging);
    $this->assertEquals($config->logging->engine, 'Monolog');
    
    $factory = new ConfigurationFactory;
    $config = $factory->setConfigPath($path)->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->logging);
    $this->assertEquals($config->logging->engine, 'Monolog');
    
    $factory = new ConfigurationFactory(FIXTURES_PATH);
    $config = $factory->setConfigFileName('foo')->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->logging);
    $this->assertEquals($config->logging->engine, 'Monolog');
  }

  /**
   * @covers                    ::create
   * @covers                    ::assignCorrectPath
   * @covers                    ::__construct
   * @covers                    Conphig\Configurators\XmlConfigurator::parseConfig
   * @covers                    Conphig\Configurators\AbstractConfigurator::getConfiguration
   * @covers                    Conphig\Configurators\XmlConfigurator::createConfigFromXmlElements
   * @covers                    Conphig\Configurators\AbstractConfigurator::__construct
   * @test
   */
  public function newConfigurationFromXmlShouldWork() {
    $path = FIXTURES_PATH . DIRECTORY_SEPARATOR . 'config.xml';
    $factory = new ConfigurationFactory($path);
    $config = $factory->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
    
    $factory = new ConfigurationFactory(FIXTURES_PATH);
    $config = $factory->setConfigType('xml')->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
    
    $factory = new ConfigurationFactory;
    $config = $factory->create($path);
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
  }

  /**
   * @covers                    ::create
   * @covers                    ::assignCorrectPath
   * @covers                    ::__construct
   * @covers                    Conphig\Configurators\XmlConfigurator::parseConfig
   * @covers                    Conphig\Configurators\AbstractConfigurator::getConfiguration
   * @covers                    Conphig\Configurators\XmlConfigurator::createConfigFromXmlElements
   * @covers                    Conphig\Configurators\AbstractConfigurator::__construct
   * @requires                  PHP 5.5.0
   * @test
   */
  public function newConfigurationFromAnotherXmlShouldWork() {
    $path = FIXTURES_PATH . DIRECTORY_SEPARATOR . 'foo.xml';
    $factory = new ConfigurationFactory($path);
    $config = $factory->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
    
    $factory = new ConfigurationFactory(FIXTURES_PATH);
    $config = $factory->setConfigFileName('foo')->setConfigType('xml')->create();
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
    
    $factory = new ConfigurationFactory;
    $config = $factory->create($path);
    $this->assertInstanceOf(Configuration::class, $config);
    $this->assertNotEmpty($config->database);
    $this->assertEquals($config->database->engine, 'mysql');
  }
  
  /**
   * @covers                    ::assignCorrectPath
   * @test
   */
  public function checkPathCreationWithNoGivenPath() {
    $refl = new ReflectionClass(ConfigurationFactory::class);
    $factory = $refl->newInstanceArgs([]);
    $rAssignCorrectPath = $refl->getMethod('assignCorrectPath');
    $rAssignCorrectPath->setAccessible(true);
    $rAssignCorrectPath->invoke($factory);
    
    $rConfigPath = $refl->getProperty('configPath');
    $rConfigPath->setAccessible(true);
    
    $this->assertEquals($rConfigPath->getValue($factory), getcwd());
  }
}
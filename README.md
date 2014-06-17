# Welcome to Conphig

It's a simple configuration generator that parses different types of configuration files into an object.
The idea behind it is to standardize configuration objects and allowing you to mix and match configuration files as per need.

Currently the only supported file types are INI, XML and JSON. YAML support can be added through custom configuration

[![Build Status](https://travis-ci.org/Achrome/Conphig.png?branch=master)](https://travis-ci.org/Achrome/Conphig)

### Installation

#### With Composer

Just add this to your `composer.json`

```json
{
	"require": {
		"achrome/conphig": "*"
	}
}
```

Alternatively, you can use the composer CLI like this

```shell
$ php composer.phar require 'achrome/conphig=*'
```

Or

```shell
$ composer require 'achrome/conphig=*'
```

Then, in the application bootstrap, just `require 'vendor/autoload.php'`.

#### Without Composer (Honestly, why?)

You can clone this repo directly and use it as well, like this

```shell
$ mkdir vendor && cd vendor
$ git clone https://github.com/Achrome/Conphig.git
```

Then, you just have to `require 'Conphig/autoload.php'` in the bootstrap file.

### Usage

In the application bootstrap, `require 'Conphig/autoload.php'` to set up the autoloader, or if you are using Composer, just
`require 'vendor/autoload.php'`

```php
use Conphig\Factories\ConfigurationFactory;

$configCreator = new ConfigurationFactory;
$configuration = $configCreator->setConfigPath('/path/to/config/dir')
					       ->setConfigFileName('configFileName')
					       ->setConfigType('filetype')
					       ->create();
```

Or, if you prefer to go through a simpler route

```php

use Conphig\Factories\ConfigurationFactory;

$configCreator = new ConfigurationFactory;
$configuration = $configCreator->create('/path/to/config/file.ext');

```

By default, it will take the file name `config` and type `ini`, so the only thing it needs is the path.

If this is the case, you could just do this.

```php
use Conphig\Factories\ConfigurationFactory;

$configCreator = new ConfigurationFactory('/path/to/config/dir');
$configuration = $configCreator->create();
```

For example, if a config.ini looks like this,

```ini
[database]
engine = mysql
host = localhost
dbname = test
user = root
password = FooBar1234
```

When parsed through Conphig

```php
use Conphig\Factories\ConfigurationFactory;

$configCreator = new ConfigurationFactory('/path/to/config/dir');
$configuration = $configCreator->create();

echo $configuration->database->engine; //Will output mysql
```

### Custom configurators

You can register your own configuration system by extending `Conphig\Configurators\AbstractConfigurator` like this

```php
namespace Foo;

use Conphig\Configurators\AbstractConfigurator;

class BarConfigurator extends AbstractConfigurator {

	public function parseConfig() {
		//The file name is saved in AbstractConfigurator::filePath and can be used here to write your own logic to parse the file.
		//Save the configuration in AbstractConfigurator::configuration for the factory to be able to return it.
	}
}
```

Then, you need to register the custom handler and it will be set as the configurator that will be used

```php
use Conphig\Factories\ConfigurationFactory;

$configCreator = new ConfigurationFactory('/path/to/custom/config/dir');
$configuration = $configCreator->registerConfigHandler('custom', 'Foo\\BarConfigurator')->create();
```

### License
[MIT](https://github.com/Achrome/Conphig/LICENSE)

### Contributing
Just add new features as you want and send out a pull request! If you see an issue, just open it in [issues](https://github.com/Achrome/Conphig/issues)
**Note**: Please run `grunt` before committing to make sure code style and unit tests pass

### Contact
[Github](https://github.com/Achrome)
[Mail](mailto:achromatic.rainbow@gmail.com)

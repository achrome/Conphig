# Welcome to Conphig

It's a simple configuration generator that parses different types of configuration files into an object

Currently the only supported file type is INI

### Usage

In the application bootstrap, `require 'Conphig/autoload.php'` to set up the autoloader, or if you are using Composer, just 
`require 'vendor/autoload.php'`

```php
use Conphig\Factories\ConfigurationFactory;

$configCreator = new ConfigurationFactory();
$configuration = $configCreator->setConfigPath('/path/to/config/dir')
					->setConfigFileName('configFileName')
					->setConfigType('filetype')
					->create();
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

You can register your own configuration system by implementing the `Conphig\Interfaces\Configurable` interface,
or even better, by extending `Conphig\Configurators\AbstractConfigurator` like this

```php
namespace Foo;

use Conphig\Configurators\AbstractConfigurator;

class BarConfigurator extends AbstractConfigurator {

	public function parseConfig() {
		//The file name is saved in AbstractConfigurator::filePath and can be used here to write your own logic to parse the file.
	}
}
```

Then, you need to register the custom handler and it will be set as the configurator that will be used

```php
use Conphig\Factories\ConfigurationFactory;

$configCreator = new ConfigurationFactory('/path/to/custom/config/dir');
$configuration = $configCreator->registerConfigHandler('custom', 'Foo\\BarConfigurator')->create();
```
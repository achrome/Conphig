### Welcome to Conphig

It's a simple configuration generator that parses different types of configuration files into an object

Currently the only supported file type is INI

### Usage

In the application bootstrap, `require 'Conphig/autoload.php'` to set up the autoloader, or if you are using Composer, just 
`require 'vendor/autoload.php'`

```
$configuration = Conphig\Factories\ConfigurationFactory::getInstance()
					->setConfigPath('/path/to/your/config/directory')
					->setConfigFileName('configFileName')
					->setConfigType('filetype')
					->create();
```

By default, it will take the file name `config` and type `ini`, so the only thing it needs is the path.

If this is the case, you could just do this.

```
$configuration = Conphig\Factories\ConfigurationFactory::getInstance('/path/to/config/directory')->create();
```

For example, if a config.ini looks like this,

```
[database]
engine = mysql
host = localhost
dbname = test
user = root
password = FooBar1234
```

When parsed through Conphig

```
$configuration = ConfigurationFactory::getInstance('path/to/config/ini')->create();

echo $configuration->database->engine; //Will output mysql
```

### Custom configurators

You can register your own configuration system by implementing the `Conphig\Interfaces\Configurable` interface,
or even better, by extending `Conphig\Configurators\AbstractConfigurator` like this

```
namespace Foo;

class BarConfigurator extends AbstractConfigurator {

	public function parseConfig() {
		//The file name is saved in AbstractConfigurator::filePath and can be used here to write your own logic to parse the file.
		return $this->configuration; //This line is needed for the factory to return the configuration object
	}
}
```
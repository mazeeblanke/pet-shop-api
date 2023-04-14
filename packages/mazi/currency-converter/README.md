# Currency Converter

This package provides an endpoint that returns the exchange rate for the given amount in the same standard format as the other API endpoints.

The package fetches the exchange rate of the day from the European Central Bank daily reference.


## Requirements
---------------
 - PHP == 8.2

## Installation
-------------------

The package has been added as a local dependency to the pets shop project.
To install run 

``` composer require mazi/currency-converter ``` or ``` composer install ``` to install all dependencies.

For Laravel >=5.5, no need to manually add `ServiceProvider` into the config. It uses a package auto-discovery feature. Skip this if you are on >=5.5, if not:

Open your `AppServiceProvider` (located in `app/Providers`) and add this line in `register` function
```php
$this->app->register(\Mazi\CurrencyConverter\ServiceProvider::class);
```
or open your `config/app.php` and add this line in `providers` section
```php
\Mazi\CurrencyConverter\ServiceProvider::class,
```

## Configuration
--------
```ini
'cache-key' => 'CUR-CONVERTER-RATES',
'cache-time' => 50,
```
These configurations are used by the drivers to cache rate results to improve performance.

To publish the config file of the package to your project for customization, RUN 
```
php artisan vendor:publish --provider="Mazi\CurrencyConverter\ServiceProvider
```

 ## Usage
 ------
 You can register the `Converter`, `Parser` and `Driver` in the `AppServiceProvider`.
 At the moment, only one Driver is implemented, which is the `EuroBankDriver`.
 Only one Parser is implemented as well, the `XMLParser`.

 ```
         $this->app->bind(
            Parser::class,
            XMLParser::class
        );

        $this->app->singleton(ConverterDriver::class, function () {
            return new EuroBankDriver(
                new \GuzzleHttp\Client(),
                $this->app->make(Parser::class),
                $this->app->make(Repository::class),
                $this->app->make(ConfigRepository::class)
            );
        });

        $this->app->singleton(ContractsConverter::class, function() {
            return new Converter(
                $this->app->make(ConverterDriver::class
            ));
        });
```

As shown in the example above, it is better to depend on the contracts rather than tight coopling to the actual classes, while registering to the container.

The `Symbol` class contains a list of all currencies/Symbols and the `Exception` Directory contains Exceptions used in the package.

The package also ships with a factory, for convienient purposes

```
 ConverterFactory::make('eurobankDriver', $app)
```

The package ships with a `Request` class i.e `packages/mazi/currency-converter/src/Http/Requests/ConversionRequest.php`


## Tests
-------------
Tests are found in the test directory.

Steps to run tests:

1. cd in to `mazi/currency-converter` directory 
2. RUN `composer install`
3. RUN `./vendor/bin/phpunit`



## Swagger Docs
-----------------
The ```convert-currency``` has swagger documentations that you can include in your application.

It has a dependency of ```https://github.com/DarkaOnLine/L5-Swagge``` for swagger documetation. So to include the docs in the generated docs of your app, include it in the configuration.

l5-swagger.php config
```
...

    'annotations' => [
        base_path('app'),
        base_path('packages/mazi/currency-converter/src') // ADD THIS
    ],

...

```
now when you RUN `php artisan l5-swagger:generate` it will add the docs to the generated docs

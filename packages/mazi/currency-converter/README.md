# Currency Converter

This package provides an endpoint that returns the exchange rate for the given amount in the same standard format as the other API endpoints.

The package fetches the exchange rate of the day from the European Central Bank daily reference.



## Requirements
---------------
 - PHP == 8.2
 - Laravel >= 6.0




#### Tests
-------------
Tests are found in the test directory.

To run tests, cd in to `mazi/currency-converter` directory and run `./vendor/bin/phpunit`



#### Swagger Docs
-----------------
The ```convert-currency/{amount}/{currency}``` has swagger documentations that you can include in your application.

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

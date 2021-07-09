<p align="center">
    <img src="https://raw.githubusercontent.com/nunomaduro/larastan/master/docs/logo.png" alt="Larastan Logo" width="300">
    <br><br>
    <img src="https://raw.githubusercontent.com/nunomaduro/larastan/master/docs/example.png" alt="Larastan Example" height="300">
</p>

<p align="center">
  <a href="https://github.com/nunomaduro/larastan/actions"><img src="https://github.com/nunomaduro/larastan/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/nunomaduro/larastan/stats"><img src="https://poser.pugx.org/nunomaduro/larastan/d/total.svg" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/nunomaduro/larastan"><img src="https://poser.pugx.org/nunomaduro/larastan/v/stable.svg" alt="Latest Version"></a>
  <a href="https://github.com/nunomaduro/larastan/blob/master/LICENSE.md"><img src="https://poser.pugx.org/nunomaduro/larastan/license.svg" alt="License"></a>
</p>

------

## ⚗️ About Larastan

Larastan was created by [Can Vural](https://github.com/canvural) and [Nuno Maduro](https://github.com/nunomaduro), got artwork designed by [@Caneco](http://github.com/caneco), is maintained by [Can Vural](https://github.com/canvural), [Nuno Maduro](https://github.com/nunomaduro), and [Viktor Szépe](https://github.com/szepeviktor), and is a [PHPStan](https://phpstan.org/) wrapper for Laravel. Larastan focuses on **finding errors in your code** without actually running it. It catches whole classes of bugs even **before you write tests** for the code.

- Adds static typing to Laravel to improve developer productivity and **code quality**
- Supports most of [Laravel](https://laravel.com)'s **beautiful magic**
- Discovers bugs in your code without running it

## ✨ Getting Started In 3 Steps

> **Requires:**
- **[PHP 7.2+](https://php.net/releases/)**
- **[Laravel 6.0+](https://github.com/laravel/laravel)**

**1**: First, you may use [Composer](https://getcomposer.org) to install Larastan as a development dependency into your Laravel project:
```bash
composer require --dev nunomaduro/larastan
```

> Using Larastan for analysing Laravel packages? You may need to install `orchestra/testbench`.

**2**: Then, create a `phpstan.neon` or `phpstan.neon.dist` file in the root of your application. It might look like this:

```
includes:
    - ./vendor/nunomaduro/larastan/extension.neon

parameters:

    paths:
        - app

    # The level 8 is the highest level
    level: 5

    ignoreErrors:
        - '#Unsafe usage of new static#'

    excludePaths:
        - ./*/*/FileToBeExcluded.php

    checkMissingIterableValueType: false
```

For all available options, please take a look at the PHPStan documentation: **https://phpstan.org/config-reference**

**3**: Finally, you may start analyzing your code using the phpstan console command:

```bash
./vendor/bin/phpstan analyse
```

If you are getting the error `Allowed memory size exhausted`, then you can use the `--memory-limit` option fix the problem:

```bash
./vendor/bin/phpstan analyse --memory-limit=2G
```

## Ignoring errors
Ignoring a specific error can be done either with a php comment or in the configuration file.  


```php
/** @phpstan-ignore-next-line **/
$test->badMethod();
```

When ignoring errors in PHPStans configuration file, they are ignored by writing a regex based on error messages.  
This can be scoped to a path and amount of times it happens also. ([PHPStan Docs](https://phpstan.org/user-guide/ignoring-errors))

```yaml
parameters:
    ignoreErrors:
        - '#Call to an undefined method .*badMethod\(\)#'
```

## Baseline file
In older codebases it might be hard to spend the time fixing all the code to pass a high PHPStan Level.  
To get around this a baseline file can be generated. The baseline file will create a configuration file with all of the current errors, so new code can be written following a higher standard than the old code. ([PHPStan Docs](https://phpstan.org/user-guide/baseline))

```bash
./vendor/bin/phpstan analyse --generate-baseline
```


## Rules

A list of configurable rules specific to Laravel can be found [here](docs/rules.md).

## Custom PHPDoc types

A list of PHPDoc types specific to Larastan can be found [here](docs/custom-types.md).

## Custom PHPStan config parameters
A list of custom config parameters that you can use in your PHPStan config file can be found [here](docs/custom-config-paramaters.md).

## Errors To Ignore

Some parts of Laravel are currently too magical for Larastan/PHPStan to understand.
We listed common [errors to ignore](docs/errors-to-ignore.md), add them as needed.

## 👊🏻 Contributing

Thank you for considering contributing to Larastan. All the contribution guidelines are mentioned [here](CONTRIBUTING.md).

You can have a look at the [CHANGELOG](CHANGELOG.md) for constant updates & detailed information about the changes. You can also follow the Twitter account for the latest announcements or just come say hi!: [@enunomaduro](https://twitter.com/enunomaduro), [@can__vural](https://twitter.com/can__vural).

## ❤️ Support The Development

**Do you like this project? Support it by donating**

- PayPal: [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=66BYDWAT92N6L)
- Patreon: [Donate](https://www.patreon.com/nunomaduro)

## 📖 License

Larastan is an open-sourced software licensed under the [MIT license](LICENSE.md).

# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kregel/exception-probe.svg?style=flat-square)](https://packagist.org/packages/kregel/exception-probe)
[![Total Downloads](https://img.shields.io/packagist/dt/kregel/exception-probe.svg?style=flat-square)](https://packagist.org/packages/kregel/exception-probe)

## Installation

You can install the package via composer:

```bash
composer require kregel/exception-probe
```

## Usage

``` php
$stacktrace = new Kregel\ExceptionProbe\Stacktrace();
echo $stacktrace->parse('ErrorException: The thing that was suppose to do stuff broke
#0 /usr/share/php/test/index.php(34): Kernel->run()
#1 /usr/share/php/test/Framework/file-thing.php(143): SomeClass->doTheThing()
#2 /usr/share/php/PHPUnit/Framework/TestCase.php(626): SeriesHelperTest->setUp()
#3 /usr/share/php/PHPUnit/Framework/TestResult.php(666): PHPUnit_Framework_TestCase->runBare()
#4 /usr/share/php/PHPUnit/Framework/TestCase.php(576): PHPUnit_Framework_TestResult->run(Object(SeriesHelperTest))
#5 /usr/share/php/PHPUnit/Framework/TestSuite.php(757): PHPUnit_Framework_TestCase->run(Object(PHPUnit_Framework_TestResult))
#6 /usr/share/php/PHPUnit/Framework/TestSuite.php(733): PHPUnit_Framework_TestSuite->runTest(Object(SeriesHelperTest), Object(PHPUnit_Framework_TestResult))
#7 /usr/share/php/PHPUnit/TextUI/TestRunner.php(305): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#8 /usr/share/php/PHPUnit/TextUI/Command.php(188): PHPUnit_TextUI_TestRunner->doRun(Object(PHPUnit_Framework_TestSuite), Array)
#9 /usr/share/php/PHPUnit/TextUI/Command.php(129): PHPUnit_TextUI_Command->run(Array, true)
#10 /usr/bin/phpunit(53): PHPUnit_TextUI_Command::main()
#11 {main}"');
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email security@kbcomp.co instead of using the issue tracker.

## Credits

- [Austin Kregel](https://github.com/austinkregel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support on Beerpay
Hey dude! Help me out for a couple of :beers:!

[![Beerpay](https://beerpay.io/austinkregel/php-exception-probe/badge.svg?style=beer-square)](https://beerpay.io/austinkregel/php-exception-probe)  [![Beerpay](https://beerpay.io/austinkregel/php-exception-probe/make-wish.svg?style=flat-square)](https://beerpay.io/austinkregel/php-exception-probe?focus=wish)
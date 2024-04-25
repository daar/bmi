# A BMI calculator based on WHO data and definitions.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/daar/bmi.svg?style=flat-square)](https://packagist.org/packages/daar/bmi)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/daar/bmi/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/daar/bmi/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/daar/bmi/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/daar/bmi/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/daar/bmi.svg?style=flat-square)](https://packagist.org/packages/daar/bmi)

## Installation

You can install the package via composer:

```bash
composer require daar/bmi
```

## Usage

```php
use Daar\Bmi\BMI;

// Calculate the BMI
$bmi = BMI::calculate($weight, $length);

// Calculate the BMI category according to the WHO standard
$category = BMI::category($weight, $length, $gender, $ageInMonths);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [daar](https://github.com/daar)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

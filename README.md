# Laravel AWS Textract

[![Latest Version on Packagist](https://img.shields.io/packagist/v/franckitho/laravel-aws-textract.svg?style=flat-square)](https://packagist.org/packages/franckitho/laravel-aws-textract)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/franckitho/laravel-aws-textract/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/franckitho/laravel-aws-textract/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/franckitho/laravel-aws-textract/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/franckitho/laravel-aws-textract/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/franckitho/laravel-aws-textract.svg?style=flat-square)](https://packagist.org/packages/franckitho/laravel-aws-textract)

Simple [AWS Textract](https://aws.amazon.com/fr/textract/) (OCR Software, Data Extraction Tool) wrapper for Laravel

## Features
- [x] Analyze Document
- [ ] Analyze ID card
- [ ] Analyze Invoice
- [ ] Query-based extraction
- [ ] Signature detection  

## Installation

You can install the package via composer:

```bash
composer require franckitho/laravel-aws-textract
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-aws-textract-config"
```

This is the contents of the published config file:

```php
return  [
    'region' => env('AWS_REGION', 'us-east-1'),
    'version' => env('AWS_TEXTRACT_VERSION', 'latest'),
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
];
```

## Usage

##### With local file or url
```php
use franckitho\Textract\AnalyseDocument;

$document = AnalyseDocument::features('LAYOUT')->file('path/to/file')->analyze();
```

##### With S3 Bucket (need S3 permission)
```php
use franckitho\Textract\AnalyseDocument;

$document = AnalyseDocument::features('LAYOUT')->s3('bucket', 'file')->analyze();
```

##### For showing metadata
```php
use franckitho\Textract\AnalyseDocument;

$document = AnalyseDocument::features('LAYOUT')->file('path/to/file')->withMetaData()->analyze();
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

- [franckitho](https://github.com/franckitho)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

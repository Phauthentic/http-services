# HTTP Services

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/Phauthentic/http-services/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/Phauthentic/http-services/)
[![Code Quality](https://img.shields.io/scrutinizer/g/Phauthentic/http-services/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/Phauthentic/http-services/)

This library provides a set of service classes that all operate on the PSR request and / or response objects. 

**This is not yet ready for prodction! Still in development!**

## Installation

You can install this library using [composer](http://getcomposer.org):

```
composer require Phauthentic/http-services
```

## Requirements

Your application **must** use the [PSR 7 HTTP Message interfaces](https://github.com/php-fig/http-message) for your request and response objects. The whole library is build to be framework agnostic but uses these interfaces as the common API. Every modern and well written framework and application should fulfill this requirement.

 * php >= 7.1
 * [psr/http-message](https://github.com/php-fig/http-message)
 * [psr/http-factory](https://github.com/php-fig/http-factory)

## Documentation

 * [Download Service](docs/Download-Service.md) 
 * [Redirect Service](docs/Redirect-Service.md)
 * [Request Handler Service](docs/Request-Handler-Service.md)
 
## Copyright & License

Licensed under the [MIT license](LICENSE.txt).

Copyright (c) [Phauthentic](https://github.com/Phauthentic)

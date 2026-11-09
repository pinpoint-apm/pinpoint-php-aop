[![Build](https://github.com/pinpoint-apm/pinpoint-php-aop/actions/workflows/main.yml/badge.svg)](https://github.com/pinpoint-apm/pinpoint-php-aop/actions/workflows/main.yml) [![LICENSE](https://img.shields.io/github/license/pinpoint-apm/pinpoint-php-aop)](LICENSE)

## Issues

https://github.com/pinpoint-apm/pinpoint-c-agent/labels/php-aop

## Requirements

- PHP runtime: 8.2, 8.3, 8.4, or 8.5 (CI matrix)
- ext-pinpoint_php: `^0.5.2 || ^0.6.0` ( [ Guide 📑](https://github.com/pinpoint-apm/pinpoint-c-agent/blob/dev/DOC/PHP/Readme.md#steps) )

The extension requirement accepts versions `>=0.5.2 <0.6.0` or `>=0.6.0 <0.7.0`, including `0.6.2`. Install and enable the extension before installing this library with Composer.

### PHP-Parser compatibility

This library requires `nikic/php-parser:^4.19.5`. Version 4.19.5 includes PHP 8.5 runtime deprecation fixes while retaining the 4.x parser API used by this project.

Runtime compatibility does not imply support for parsing every syntax feature of that PHP version. PHP-Parser 4.x parses syntax through PHP 8.3; monitored source files using PHP 8.4 property hooks or PHP 8.5 pipe operators are not supported. PHP-Parser 5.x requires an API migration before it can be enabled here.

See the [PHP-Parser 4.x changelog](https://github.com/nikic/PHP-Parser/blob/4.x/CHANGELOG.md) and [5.x migration guide](https://github.com/nikic/PHP-Parser/blob/master/UPGRADE-5.0.md).

##  How to use 

### 1. Import from packagist

> composer require  pinpoint-apm/pinpoint-php-aop

### 2. Add pinpoint entry into your entry file(eg: index.php)
``` php
<?php
require_once __DIR__."/../vendor/autoload.php";

// A writable path for caching AOP code, default is /tmp
// define('AOP_CACHE_DIR',__DIR__.'/../Cache/');  // optional 
// API for register your own plugins eg:
define('PP_REQ_PLUGINS', Pinpoint\Plugins\DefaultRequestPlugin::class);                    
require_once __DIR__. '/../vendor/pinpoint-apm/pinpoint-php-aop/auto_pinpointed.php';
```

## Write your own plugins

<details> <summary>Only for developers</summary>

#### Steps 

1. Write your own plugins(if needs). Here are some plugins template.

- [pRedisCall.php](lib/Pinpoint/Plugins/autoload/_predis/pRedisCall.php)
- [GuzzlePlugin.php](lib/Pinpoint/Plugins/autoload/_GuzzleHttp/GuzzlePlugin.php)


2. Use `AspectClassHandle` to combine target class with plugin class.
```php
    $classHandler = new AspectClassHandle(\yii\web\UrlManager::class);
    $classHandler->addJoinPoint('parseRequest', \Pinpoint\Plugins\yii2\UrlRule::class);
    $cls[] = $classHandler;
```

3. Extend `DefaultRequestPlugin` and implement `joinedClassSet`.

Examples:
- [Yii2PerRequestPlugins](lib/Pinpoint/Plugins/Yii2PerRequestPlugins.php)
- [DefaultRequestPlugin](lib/Pinpoint/Plugins/DefaultRequestPlugin.php)

</details>

## Our test project 

- For yii2, [Yii2PerRequestPlugins example](lib/Pinpoint/Plugins/Yii2PerRequestPlugins.php)
- [pinpoint-c-agent/SimplePHP](https://github.com/pinpoint-apm/pinpoint-c-agent/tree/dev/testapps/SimplePHP)
- [pinpoint-c-agent/cachethq](https://github.com/pinpoint-apm/pinpoint-c-agent/tree/dev/testapps/cachethq)
- [pinpoint-c-agent/flarum](https://github.com/pinpoint-apm/pinpoint-c-agent/tree/dev/testapps/flarum)
- [pinpoint-c-agent/php_phpmyadmin](https://github.com/pinpoint-apm/pinpoint-c-agent/tree/dev/testapps/php_phpmyadmin)
- [pinpoint-c-agent/php_wordpress](https://github.com/pinpoint-apm/pinpoint-c-agent/tree/dev/testapps/php_wordpress)

### How it works

* Use `nikic/PHP-Parser` generating glue layer code
* Use namespace replace to reuse plugins or hook build-in class/function
* Intercept php classloader to redirect origin class to new class
  
> pinpoint-php-aop wrappers your class with an onBefore/onEnd/onException suite.

#### Blog 

- [Intro pinpoint php aop](https://github.com/pinpoint-apm/pinpoint-php-aop/wiki/Intro-pinpoint-php-aop)


#### Supported libraries/framework

<details> <summary>Libraries</summary>

Type|Name|Version
---|---|---
Built-in | curl| 
||memcached|`3.2.0`
||mysqli|
||pdo|
||phpredis|`6.0.2`
||apcu|
|User |guzzlehttp| `8.0.x-dev`
||predis|`3.0`
||mongodb|`v1.19.x`
</details> 

<details> <summary>Frameworks</summary>
 
Name|Version|
|---|---|
|Yii2||
|wordpress||
|thinkphp8||
|laravel||

</details>

#### Data Chart Map

![how it works](https://raw.githubusercontent.com/pinpoint-apm/pinpoint-c-agent/master/images/principle_v0.2.x.png)

There are some examples into [lib/pinpoint/test/Comparison/pinpoint/test](https://github.com/pinpoint-apm/pinpoint-php-aop/blob/dev/lib/Pinpoint/test/Comparison/Pinpoint/test/Bear.php)


#### Needs Help/Issues

[create an issue](https://github.com/pinpoint-apm/pinpoint-c-agent/issues/new?assignees=eeliu&labels=PHP-AGENT&projects=&template=-php--custom-issue-template.md&title=%5BFeat%5D+I+need+a+feature+...)


## Copyright

```
Copyright 2024-present NAVER Corp.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```

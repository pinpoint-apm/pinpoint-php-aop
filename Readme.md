[![Build](https://github.com/pinpoint-apm/pinpoint-php-aop/workflows/Build/badge.svg?branch=master)](https://github.com/pinpoint-apm/pinpoint-php-aop/actions) [![LICENSE](https://img.shields.io/github/license/pinpoint-apm/pinpoint-php-aop)](LICENSE)

## Issues

https://github.com/pinpoint-apm/pinpoint-c-agent/labels/php-aop

## Requirements

- php 7.0 ~ php 8.3
- ext-pinpoint_php: "^0.5.2" ( [ Guide 📑](https://github.com/pinpoint-apm/pinpoint-c-agent/blob/dev/DOC/PHP/Readme.md#steps) )

##  How to use 

### 1. Import from packagist

> composer require  pinpoint-apm/pinpoint-php-aop:v3.0.1

### 2. Add pinpoint entry into your entry file(eg: index.php)
``` php
<?php
require_once __DIR__."/../vendor/autoload.php";

// A writable path for caching AOP code, default is /tmp
// define('AOP_CACHE_DIR',__DIR__.'/../Cache/');  // optional 
// API for register your own plugins eg:
define('PP_REQ_PLUGINS', Pinpoint\Plugins\DefaultRequestPlugin::class);                    
require_once __DIR__. '/vendor/pinpoint-apm/pinpoint-php-aop/auto_pinpointed.php';
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

[![Build](https://github.com/pinpoint-apm/pinpoint-php-aop/workflows/Build/badge.svg?branch=master)](https://github.com/pinpoint-apm/pinpoint-php-aop/actions) [![LICENSE](https://img.shields.io/github/license/pinpoint-apm/pinpoint-php-aop)](LICENSE)

## Issues

https://github.com/pinpoint-apm/pinpoint-c-agent/labels/php-aop

##  How to Use 

### 1. Import from packagist

Add requirement into composer.json

```Json
    "require": {
        "pinpoint-apm/pinpoint-php-aop": "v2.1.0"
    }
```

### 2. Write your plugins

> UserPlugin

```php
use Pinpoint\Common\AbstractMonitor;
class UserPlugin extends AbstractMonitor
{
    public function onBefore(){

    }
    public function onEnd(&$ret){

    }
    public function onException($e){
    }
}
```
>eg: [OutputMonitor example](lib/Pinpoint/test/OutputMonitor.php)

### 3. Register UserPlugin on target class

```php
    use Pinpoint\Common\AspectClassHandle;
    ...
    $classHandler = new AspectClassHandle(\namespace\Abc::class);
    $classHandler->addJoinPoint('parseRequest', \UserPlugin::class);
```

### Here is the example for yii framework

For yii2, [Yii2PerRequestPlugins example](lib/Pinpoint/Plugins/Yii2PerRequestPlugins.php)


``` php
<?php

require_once __DIR__."/../vendor/autoload.php";

// A writable path for caching AOP code
define('AOP_CACHE_DIR',__DIR__.'/../Cache/');   
// API for register your own plugins eg: \Pinpoint\Plugins\Yii2PerRequestPlugins::class
define('PP_REQ_PLUGINS', \Pinpoint\Plugins\Yii2PerRequestPlugins::class);                    
// require auto_pinpointed, it must located after other loads
require_once __DIR__. '/vendor/pinpoint-apm/pinpoint-php-aop/auto_pinpointed.php';

```


### How it works

* Use `nikic/PHP-Parser` generating glue layer code
* Use namespace replace to reuse plugins or hook build-in class/function
* Intercept php classloader to redirect origin class to new class


### 



> pinpoint-php-aop wrappers your class with an onBefore/onEnd/onException suite.

#### Data Chart Map

![how it works](https://raw.githubusercontent.com/pinpoint-apm/pinpoint-c-agent/master/images/principle_v0.2.x.png)

More details please go to lib/pinpoint/test/Comparison/pinpoint/test


#### Needs Help/Issues

[create an issue](https://github.com/pinpoint-apm/pinpoint-c-agent/issues/new?assignees=eeliu&labels=PHP-AGENT&projects=&template=-php--custom-issue-template.md&title=%5BFeat%5D+I+need+a+feature+...)


## Copyright

```
Copyright 2020-present NAVER Corp.

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

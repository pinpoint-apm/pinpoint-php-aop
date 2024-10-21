## Changes

### - v4.0.1 async invocation API

- rdkafka plugins

### v4.0.0
> https://github.com/pinpoint-apm/pinpoint-php-aop/releases/tag/v4.0.0
* support nikic/php-parser v5.1.0

### - v3.0.8 async invocation API

> https://github.com/pinpoint-apm/pinpoint-php-aop/releases/tag/v3.0.8

* fix a bug in https://github.com/pinpoint-apm/pinpoint-c-agent/issues/672

### v0.3.3

* fix dsn on pdo8

### v0.3.0 

* add autoloader plugins

### v2.1.0

* remove setting.ini
* AspectClassHandle api
* remove `PINPOINT_USE_CACHE`

### v2.0.2
* support thinkphp5.0.x
* support all-in-one 
  * pack autoGen plugins

### v2.0.1 
* support "use xx as xxx;"
* support `use A/B/C`, while the np is A/B/C/Ds

### v2.0.0 Rename from v1.1 
* support setting.ini
* new framework Plugins tree

### v1.1 support EasySwoole framework
* namespace hack
* rename `PINPOINT_USE_CACHE`

### v1.0.1 migrate to naver/repo
### v0.2.4 Support references in parameters
1. Support pinpoint_get_func_ref_args
2. Add debug_backtrac when pinpoint_get_func_ref_args not find
3. Support PHP 7.4

### 0.2.3 Support user filter

1. User can filter plugins related class loader by extending `Pinpoint\Common\AopClassMap`.

``` php
namespace Pinpoint\Plugins;
use Pinpoint\Common\AopClassMap;

class ClassMapInFile extends AopClassMap
{
    public  function findFile($classFullName)
    {
        $file = parent::findFile($classFullName);
        if($file){
            if (some condition not allow)
            {
                reject reload this file from "__DIR__.'/Cache/'",use the origin file
                return null;
            }else{
                return $file;
            }
        }
        return $file;
    }
}

```

2020/3/19
1. Add user defined class loader ( Currently try to support yii)
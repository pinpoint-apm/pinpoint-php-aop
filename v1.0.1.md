## Changes

### v1.0.1 migrate to naver/repo
### v0.2.4 Support references in parameters
1. Support pinpoint_get_func_ref_args
2. Add debug_backtrac when pinpoint_get_func_ref_args not find
3. Support PHP 7.4

### 0.2.3 Support user filter

1. User can filter plugins related class loader by extending `Pinpoint\Common\AopClassMap`.

``` php
namespace Plugins;
use pinpoint\Common\AopClassMap;

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
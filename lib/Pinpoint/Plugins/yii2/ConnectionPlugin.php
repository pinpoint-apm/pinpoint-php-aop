<?php

declare(strict_types=1);
#-------------------------------------------------------------------------------
# Copyright 2020 NAVER Corp
#
# Licensed under the Apache License, Version 2.0 (the "License"); you may not
# use this file except in compliance with the License.  You may obtain a copy
# of the License at
#
#   http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
# WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
# License for the specific language governing permissions and limitations under
# the License.
#-------------------------------------------------------------------------------

namespace Pinpoint\Plugins\Yii2;

use Pinpoint\Plugins\Common\Trace;
use Pinpoint\Plugins\Sys\PDO\PDO;
use Yii;

class ConnectionPlugin extends Trace
{
    function onEnd(&$ret)
    {
        // god bless, the dsn,username,password,attributes is private
        if (!$ret instanceof PDO) {
            // after Aop rendering, only $pdoClass is PDO
            // https://github.com/yiisoft/yii2/blob/08da35e511e83b2184e6dfa46ec8232058ff4b2d/framework/db/Connection.php#L718-L723
            $con = $this->who;
            $dsn = $con->dsn;
            if (strncmp('sqlite:@', $dsn, 8) === 0) {
                $dsn = 'sqlite:' . Yii::getAlias(substr($dsn, 7));
            }
            $ret = new PDO($dsn, $con->username, $con->password, $con->attributes);
        }
    }
}

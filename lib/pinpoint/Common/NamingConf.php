<?php
/**
 * Copyright 2020-present NAVER Corp.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace pinpoint\Common;


class NamingConf
{
    private $conf = [];
    public function __construct(string $fullname)
    {
        $this->ini2Conf($fullname);
    }

    private function ini2Conf(string $fullname)
    {
        $ar = parse_ini_file($fullname,true);
        $this->conf['classCall'] = isset($ar['nm_class'])?($ar['nm_class']):([]);
        $this->conf['funCall'] = isset($ar['nm_func'])?($ar['nm_func']):([]);
        $this->conf['ignoreFiles'] = isset($ar['nm_ignore_class']['class_name'])?($ar['nm_ignore_class']['class_name']):([]);
        $this->conf['appendFiles'] = isset($ar['nm_add_class']['class_name'])?($ar['nm_add_class']['class_name']):([]);
    }

    public function getConf()
    {
        return $this->conf;
    }
}
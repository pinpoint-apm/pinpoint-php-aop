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

/**
 * User: eeliu
 * Date: 4/1/19
 * Time: 3:27 PM
 */

namespace pinpoint\test;

require_once 'bootstrap.php';

use pinpoint\Common\GenRequiredBIFileHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class GenRequiredBIFileTest
 * Test built-in class /Function
 * @package pinpoint\test
 */
class GenRequiredBIFileTest extends TestCase
{

    public function testLoadToFile()
    {
        $bi = new GenRequiredBIFileHelper("app\Foo");

        $classNameAr=["Exception","PDOStatement","PDO"];
        foreach ($classNameAr as $className) {
            $pdo = new  \ReflectionClass($className);

            foreach ($pdo->getMethods() as $method) {
                if(!$method->isFinal())
                    $bi->extendsMethod($className, $method->getName(), [7, 'pinpoint', 'commPlugins']);
            }
        }

        $moduleName =['curl'];

        foreach ($moduleName as $mName) {
            $funs = get_extension_funcs($mName);

            foreach ($funs as $func) {
                $bi->extendsFunc($func, [7, 'pinpoint', 'commPlugins']);
            }

        }
        if(file_exists("required_test.php")){
            unlink( "required_test.php");
        }
        $bi->loadToFile("required_test.php");
        self::assertFileExists("required_test.php");
        require "required_test.php";
    }
}


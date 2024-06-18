<?php

declare(strict_types=1);
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

namespace Pinpoint\Common;

use PhpParser\Node;
use PhpParser\PrettyPrinter;

class GenClassIndexMap
{

    private $cls_map_;
    private $ast_nodes = [];
    private $var_ar_name = 'pinpoint_class_map';
    public function __construct(array $cls_map)
    {
        $this->cls_map_ = $cls_map;
        $this->genIndexScript();
        $this->ast_nodes[] = $this->genIndexScript();
        $this->ast_nodes[] = $this->genReturnExp();
    }

    private function genReturnExp()
    {
        return new Node\Stmt\Return_(new Node\Expr\Variable($this->var_ar_name));
    }

    private function genIndexScript()
    {
        $items = [];
        foreach ($this->cls_map_ as $cls => $path) {
            $value = new Node\Scalar\String_($path);
            $key = new Node\Scalar\String_($cls);
            $items[] =
                new Node\Expr\ArrayItem($value, $key);
        }
        $array_exp = new Node\Expr\Array_($items);
        return new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable($this->var_ar_name), $array_exp));
    }

    public function save(string $full_path)
    {
        $astPrinter_ = new PrettyPrinter\Standard();
        $context = $astPrinter_->prettyPrintFile($this->ast_nodes);
        Utils::saveObj($context, $full_path);
    }
}

// author: eeliu
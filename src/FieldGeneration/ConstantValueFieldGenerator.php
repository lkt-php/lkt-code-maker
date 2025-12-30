<?php

namespace Lkt\CodeMaker\FieldGeneration;

class ConstantValueFieldGenerator extends AbstractFieldGenerator
{
    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function get{$this->data->methodName}():{$this->data->getterReturnType} { return \$this->_getConstantValueVal('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        return '';
    }

    public function getCheckers(): string
    {
        return '';
    }

    public function parse(): string
    {
        return $this->getGetters();
    }
}
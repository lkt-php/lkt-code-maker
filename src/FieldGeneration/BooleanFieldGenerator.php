<?php

namespace Lkt\CodeMaker\FieldGeneration;

class BooleanFieldGenerator extends AbstractFieldGenerator
{
    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function {$this->data->fieldName}():bool { return \$this->_getBooleanVal('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(bool \${$this->data->fieldName}):static { return \$this->_setBooleanVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        return '';
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
        ]);
    }
}
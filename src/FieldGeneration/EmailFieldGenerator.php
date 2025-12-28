<?php

namespace Lkt\CodeMaker\FieldGeneration;

class EmailFieldGenerator extends AbstractFieldGenerator
{
    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function get{$this->data->methodName}():string { return \$this->_getEmailVal('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(string \${$this->data->fieldName}):static { return \$this->_setEmailVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName}():bool { return \$this->_hasEmailVal('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
            $this->getCheckers(),
        ]);
    }
}
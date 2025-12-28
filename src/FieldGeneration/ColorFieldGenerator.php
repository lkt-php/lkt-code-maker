<?php

namespace Lkt\CodeMaker\FieldGeneration;

class ColorFieldGenerator extends AbstractFieldGenerator
{
    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function get{$this->data->methodName}():string { return \$this->_getColorVal('{$this->data->fieldName}'); }";
        $r[] = "public function get{$this->data->methodName}Rgb(float \$opacity = null):array { return \$this->_getColorRgbVal('{$this->data->fieldName}', \$opacity); }";
        $r[] = "public function get{$this->data->methodName}RgbFormatted(float \$opacity = null):array { return \$this->_getColorRgbStringVal('{$this->data->fieldName}', \$opacity); }";

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(string \${$this->data->fieldName}):static { return \$this->_setColorVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName}():bool { return \$this->_hasColorVal('{$this->data->fieldName}'); }";

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
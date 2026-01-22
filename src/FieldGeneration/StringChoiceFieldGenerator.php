<?php

namespace Lkt\CodeMaker\FieldGeneration;

class StringChoiceFieldGenerator extends AbstractFieldGenerator
{
    public function getGetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return string[] */";
            $r[] = "public function get{$this->data->methodName}():array { return \$this->_getStringChoiceVal('{$this->data->fieldName}'); }";

        } else {
            $r[] = "public function get{$this->data->methodName}():string { return \$this->_getStringChoiceVal('{$this->data->fieldName}'); }";
        }


        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        if ($this->data->isMultiple) {
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { return \$this->_setStringChoiceVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function set{$this->data->methodName}(string{$enumClass} \${$this->data->fieldName}):static { return \$this->_setStringChoiceVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $lowerFieldMethod = lcfirst($this->data->methodName);

        if ($this->data->isMultiple) {
            $r[] = "public function {$lowerFieldMethod}Is(array \$value):bool { return \$this->_stringChoiceEqual('{$this->data->fieldName}', \$value); }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function {$lowerFieldMethod}Is(string{$enumClass} \$value):bool { return \$this->_stringChoiceEqual('{$this->data->fieldName}', \$value); }";
        }
        $r[] = "public function has{$this->data->methodName}():bool { return \$this->_hasStringChoiceVal('{$this->data->fieldName}'); }";
        $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->_stringChoiceIn('{$this->data->fieldName}', \$values); }";

        return implode(' ', $r);
    }

    public function getOptionsMethods(): string
    {
        $r = [];
        $optionsMethods = $this->getAllowedOptionsMethods();
        if (count($optionsMethods) > 0) {
            $lowerFieldMethod = lcfirst($this->data->methodName);
            foreach ($optionsMethods as $i => $option) {

                $optionVal = $this->data->options[$i];

                $r[] = "public function {$lowerFieldMethod}Is{$option}(): bool { return \$this->_getStringChoiceVal('{$this->data->fieldName}') === '{$optionVal}'; }";

                $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
                $r[] = "public function set{$this->data->methodName}{$option}(): static { return \$this->_setStringChoiceVal('{$this->data->fieldName}', '{$optionVal}'); }";


                if ($this->data->enabledEmptyPreset) {
                    $r[] = "public static function emptyWith{$this->data->methodName}EqualTo{$option}(): static { return static::getInstance()->{$this->data->methodName}{$option}(); }";
                }
            }
        }
        return implode(' ', $r);
    }

    public function getComparatorsInMethods(): string
    {
        $r = [];
        if (count($this->data->comparatorsIn) > 0) {
            $lowerFieldMethod = lcfirst($this->data->methodName);
            foreach ($this->data->comparatorsIn as $comparatorName => $options) {
                $upperComparatorName = ucfirst($comparatorName);

                $c = count($options);
                if ($c === 0) continue;

                $singleMode = false;
                if ($c === 1) {
                    $singleMode = true;
                    $optionsText = "'" .implode("','", $options) . "'";

                } else {
                    $optionsText = "['" .implode("','", $options) . "']";
                }

                $comparatorFunctionContent = $singleMode ? "_stringChoiceEqual" : '_stringChoiceIn';

                $r[] = "public function {$lowerFieldMethod}Is{$upperComparatorName}(): bool { return \$this->{$comparatorFunctionContent}('{$this->data->fieldName}', {$optionsText}); }";

            }
        }
        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
            $this->getCheckers(),
            $this->getOptionsMethods(),
            $this->getComparatorsInMethods(),
        ]);
    }
}
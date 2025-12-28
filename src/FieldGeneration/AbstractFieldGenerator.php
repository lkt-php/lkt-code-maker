<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\DTO\FieldGeneratorData;

abstract class AbstractFieldGenerator
{
    public FieldGeneratorData $data;

    public function __construct(FieldGeneratorData $data)
    {
        $this->data = $data;
    }


    abstract public function getGetters(): string;
    abstract public function getSetters(): string;
    abstract public function getCheckers(): string;
    abstract public function parse(): string;

    protected function getRelatedReturnTypeFormatted(): string
    {
        if ($this->data->relatedReturnType !== '') return ":?\\{$this->data->relatedReturnType}";
        return '';
    }

    protected function getRelatedReturnAnnotationFormatted(): string
    {
        if ($this->data->relatedReturnAnnotation !== '') {
            if ($this->data->isMultiple) {
                return "@return \\{$this->data->relatedReturnAnnotation}[]";
            }
            return "@return \\{$this->data->relatedReturnAnnotation}";
        }
        return '';
    }
}
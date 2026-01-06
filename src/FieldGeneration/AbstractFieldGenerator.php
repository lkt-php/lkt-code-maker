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

    public static function generateCode(FieldGeneratorData $data): string
    {
        return (new static($data))->parse();
    }

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

    public function getAllowedOptionsMethods(): array
    {
        $r = [];
        if ($this instanceof IntegerChoiceFieldGenerator) {
            foreach ($this->data->options as $key => $value) {
                $d = is_numeric($key) ? trim($value) : trim($key);
                $d = str_replace(' ', '', ucwords(str_replace('-', ' ', $d)));
                $r[$key] = $d;
            }
        } else {
            $r = array_map(function ($option) {
                return str_replace(' ', '', ucwords(str_replace('-', ' ', $option)));
            }, $this->data->options);
        }
        return $r;
    }

    public function getEnumChoiceClass(): string
    {
        $r = $this->data->enumChoiceClass;
        if ($r !== '') $r = "|\\{$r}";
        return $r;
    }
}
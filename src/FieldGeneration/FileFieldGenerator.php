<?php

namespace Lkt\CodeMaker\FieldGeneration;

class FileFieldGenerator extends AbstractFieldGenerator
{
    public function getGetters(): string
    {
        $r = [];
        $r[] = "public function get{$this->data->methodName}() { return \$this->_getFileVal('{$this->data->fieldName}'); }";

        if ($this->data->isMultiple) {
            $r[] = "public function get{$this->data->methodName}InternalPath(): array { return \$this->_getInternalPath('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}PublicPath(): array { return \$this->_getPublicPath('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Name(int \$index = 0): string { return \$this->_getFileName('{$this->data->fieldName}', \$index); }";
            $r[] = "public function get{$this->data->methodName}Extension(int \$index = 0): string { return \$this->_getFileExtension('{$this->data->fieldName}', \$index); }";
            $r[] = "public function get{$this->data->methodName}Content(int \$index = 0): string { return \$this->_getFileContent('{$this->data->fieldName}', \$index); }";
            $r[] = "public function get{$this->data->methodName}LastModified(int \$index = 0): int|false { return \$this->_getFileLastModified('{$this->data->fieldName}', \$index); }";
            $r[] = "public function get{$this->data->methodName}Size(int \$index = 0): int|false { return \$this->_getFileSize('{$this->data->fieldName}', \$index); }";

        } else {
            $r[] = "public function get{$this->data->methodName}InternalPath(): string { return \$this->_getInternalPath('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}PublicPath(): string { return \$this->_getPublicPath('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Name(): string { return \$this->_getFileName('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Extension(): string { return \$this->_getFileExtension('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Content(): string { return \$this->_getFileContent('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}LastModified(): int|false { return \$this->_getFileLastModified('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Size(): int|false { return \$this->_getFileSize('{$this->data->fieldName}'); }";

        }

        $r[] = "public function get{$this->data->methodName}FieldConfig() { return \$this->_getFileFieldConfig('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { return \$this->_setFileVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";

        } else {
            $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
            $r[] = "public function set{$this->data->methodName}(string \${$this->data->fieldName}):static { return \$this->_setFileVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";
        }

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}InternalPath(string \${$this->data->fieldName}):static { return \$this->_setInternalPath('{$this->data->fieldName}', \${$this->data->fieldName}); }";

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}WithHttpFile(array \$value = null):static { return \$this->_setFileValWithHttpFile('{$this->data->fieldName}', \$value); }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName}():bool { return \$this->_hasFileVal('{$this->data->fieldName}'); }";

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
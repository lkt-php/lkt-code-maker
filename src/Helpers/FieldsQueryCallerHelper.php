<?php

namespace Lkt\CodeMaker\Helpers;

use Lkt\Factory\Schemas\ComputedFields\StringEqualComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringInComputedField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;

class FieldsQueryCallerHelper
{
    public static function makeFieldsCode(Schema $schema, bool $includeStatic = false)
    {
        $instanceSettings = $schema->getInstanceSettings();

        $className = $instanceSettings->getQueryCallerFQDN();
        if ($includeStatic) {
            $className = $instanceSettings->getWhereFQDN();
        }
        $returnSelf = '\\' . $className;

        $methods = [];

        foreach ($schema->getAllFields() as $field) {
            
            $fieldMethod = ucfirst($field->getName());

            $templateData = [
                'column' => $field->getColumn(),
                'fieldMethod' => $fieldMethod,
                'returnSelf' => $returnSelf,
                'canBeNull' => false,
            ];

            if ($field instanceof ForeignKeyField || $field instanceof IntegerField) {
                $templateData['canBeNull'] =  $field->canBeNull();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/integer-builder.phtml')
                    ->setData($templateData)
                    ->parse();

                if ($includeStatic) {
                    $templateData['fieldMethod'] = $field->getName();
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/integer-builder-static.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }

            if ($field instanceof StringField || $field instanceof HTMLField || $field instanceof EmailField) {
                $templateData['canBeNull'] =  $field->canBeNull();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/string-builder.phtml')
                    ->setData($templateData)
                    ->parse();

                if ($includeStatic) {
                    $templateData['fieldMethod'] = $field->getName();
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/string-builder-static.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }


            if ($field instanceof FloatField) {
                $templateData['canBeNull'] =  $field->canBeNull();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/float-builder.phtml')
                    ->setData($templateData)
                    ->parse();

                if ($includeStatic) {
                    $templateData['fieldMethod'] = $field->getName();
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/float-builder-static.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }


            if ($field instanceof BooleanField) {
                $templateData['canBeNull'] =  $field->canBeNull();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/boolean-builder.phtml')
                    ->setData($templateData)
                    ->parse();

                if ($includeStatic) {
                    $templateData['fieldMethod'] = $field->getName();
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/boolean-builder-static.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }


            if ($field instanceof ForeignKeysField) {
                $templateData['canBeNull'] =  $field->canBeNull();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/foreign-keys-builder.phtml')
                    ->setData($templateData)
                    ->parse();

                if ($includeStatic) {
                    $templateData['fieldMethod'] = $field->getName();
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/foreign-builder-static.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }


            if ($field instanceof DateTimeField) {
                $templateData['canBeNull'] =  $field->canBeNull();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/datetime-builder.phtml')
                    ->setData($templateData)
                    ->parse();

                if ($includeStatic) {
                    $templateData['fieldMethod'] = $field->getName();
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/datetime-builder-static.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }

            if ($field instanceof StringEqualComputedField) {
                $templateData['canBeNull'] =  false;
                $relatedField = $schema->getField($field->getField());
                $templateData['column'] = $relatedField->getColumn();
                $templateData['value'] = $field->getValue();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder-computed-fields/string-equal-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();

                if ($includeStatic) {
                    $templateData['fieldMethod'] = $field->getName();
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder-computed-fields/string-equal-computed-field-static.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }

            if ($field instanceof StringInComputedField) {
                $templateData['canBeNull'] =  false;
                $relatedField = $schema->getField($field->getField());
                $templateData['column'] = $relatedField->getColumn();
                $templateData['value'] = "'".implode("','", $field->getValue())."'";
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder-computed-fields/string-in-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();

                if ($includeStatic) {
                    $templateData['fieldMethod'] = $field->getName();
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder-computed-fields/string-in-computed-field-static.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }
        }

        return implode("\n", $methods);
    }
}
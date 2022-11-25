<?php

namespace Lkt\CodeMaker\Helpers;

use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;

class FieldsQueryCallerHelper
{
    public static function makeFieldsCode(Schema $schema)
    {
        $instanceSettings = $schema->getInstanceSettings();

        $className = $instanceSettings->getAppClass();
        $returnSelf = '\\' . $className;

        $methods = [];

        foreach ($schema->getAllFields() as $field) {
            
            $fieldMethod = ucfirst($field->getName());

            $templateData = [
                'column' => $field->getColumn(),
                'fieldMethod' => $fieldMethod,
                'returnSelf' => $returnSelf,
            ];

            if ($field instanceof ForeignKeyField || $field instanceof IntegerField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/integer-builder.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringField || $field instanceof HTMLField || $field instanceof EmailField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/string-builder.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }


            if ($field instanceof FloatField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/float-builder.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }


            if ($field instanceof BooleanField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/boolean-builder.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }


            if ($field instanceof ForeignKeysField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/foreign-keys-builder.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }


            if ($field instanceof DateTimeField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/query-builder/datetime-builder.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }
//
//            if ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
//                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/datetime-field.phtml')
//                    ->setData($templateData)
//                    ->parse();
//                continue;
//            }
        }

        return implode("\n", $methods);
    }
}
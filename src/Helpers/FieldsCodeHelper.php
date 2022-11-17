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

class FieldsCodeHelper
{
    public static function makeFieldsCode(Schema $schema)
    {
        $instanceSettings = $schema->getInstanceSettings();

        $className = $instanceSettings->getAppClass();
        $returnSelf = '\\' . $className;

        $methods = [];

        foreach ($schema->getAllFields() as $field) {
            
            $fieldMethod = ucfirst($field->getName());
            $fieldName = $field->getName();

            $templateData = [
                'fieldName' => $fieldName,
                'fieldMethod' => $fieldMethod,
                'returnSelf' => $returnSelf,
            ];

            if ($field instanceof ForeignKeyField) {

                $relatedComponent = $field->getComponent();
                $relatedSchema = Schema::get($relatedComponent);

                $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();
                $templateData['component'] = $relatedComponent;
                $templateData['relatedClassName'] = ':?\\' . $relatedClassName;
                $templateData['relatedReturnClass'] = '@return \\'. $relatedClassName;

                if ($field->isSoftTyped()) {
                    $templateData['relatedClassName'] = '';
                    $templateData['relatedReturnClass'] = '';
                }

                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/foreign-key-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof IntegerField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/integer-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringField || $field instanceof HTMLField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/string-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof EmailField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/email-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof BooleanField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/boolean-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof FloatField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/float-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/datetime-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof ForeignKeysField || $field instanceof RelatedField || $field instanceof RelatedKeysField) {

                $relatedComponent = $field->getComponent();
                if (Schema::exists($relatedComponent)) {
                    $relatedSchema = Schema::get($relatedComponent);
                    $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();
                    $templateData['component'] = $relatedComponent;
                    $templateData['relatedClassName'] = ':?\\' . $relatedClassName;
                    $templateData['relatedReturnClass'] = '@return \\'. $relatedClassName. '[]';
                }

                if ($field instanceof RelatedField) {
                    $templateData['isSingleMode'] = $field->isSingleMode();
                }

                if ($field->isSoftTyped()) {
                    $templateData['relatedClassName'] = '';
                    $templateData['relatedReturnClass'] = '';
                }

                if ($field instanceof ForeignKeysField) {
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/foreign-keys-field.phtml')
                        ->setData($templateData)
                        ->parse();

                } elseif ($field instanceof RelatedField) {
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/related-field.phtml')
                        ->setData($templateData)
                        ->parse();
                } elseif ($field instanceof RelatedKeysField) {
                    $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/related-keys-field.phtml')
                        ->setData($templateData)
                        ->parse();
                }
                continue;
            }

            if ($field instanceof PivotField) {

                $relatedComponent = $field->getComponent();
                $relatedSchema = Schema::get($relatedComponent);

                $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();
                $templateData['component'] = $relatedComponent;
                $templateData['relatedClassName'] = ':?\\' . $relatedClassName;
                $templateData['relatedReturnClass'] = '@return \\'. $relatedClassName. '[]';

                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/pivot-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof FileField) {
                $templateData['isPublic'] = $field->getPublicPath() !== '';
                $templateData['publicPath'] = $field->getPublicPath();

                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/file-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof ColorField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/color-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof JSONField) {
                $templateData['isAssoc'] = $field->isAssoc();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/json-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }
        }

        return implode("\n", $methods);
    }
}
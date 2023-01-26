<?php

namespace Lkt\CodeMaker\Helpers;

use Lkt\Factory\Schemas\ComputedFields\BooleansComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringAboveMinLengthComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringBelowMaxLengthComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringBetweenMinAndMaxLengthComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringEqualComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringInComputedField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\RelatedKeysMergeField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;

class FieldsCodeHelper
{
    public static function makeFieldsCode(Schema $schema): string
    {
        $instanceSettings = $schema->getInstanceSettings();

        $className = $instanceSettings->getAppClass();
        $returnSelf = '\\' . $className;

        $methods = [];

        foreach ($schema->getFields() as $field) {
            
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

            if ($field instanceof IntegerChoiceField) {
                $templateData['comparatorsIn'] = $field->getComparatorsIn();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/integer-choice-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }
            elseif ($field instanceof IntegerField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/integer-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringChoiceField) {

                $options = $field->getAllowedOptions();

                $optionsMethods = array_map(function ($option) {
                    return str_replace(' ', '', ucwords(str_replace('-', ' ', $option)));
                }, $options);

                $templateData['options'] = $options;
                $templateData['optionsMethods'] = $optionsMethods;
                $templateData['comparatorsIn'] = $field->getComparatorsIn();

                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/string-choice-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }
            elseif ($field instanceof StringField || $field instanceof HTMLField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/string-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof EncryptField) {
                $templateData['hashMode'] = $field->isHashMode();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/encrypt-field.phtml')
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

                $templateData['formats'] = $field->getFormats();
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
                    $relatedQueryCaller = $relatedSchema->getInstanceSettings()->getQueryCallerFQDN();

                    $templateData['component'] = $relatedComponent;
                    $templateData['relatedClassName'] = ':?\\' . $relatedClassName;
                    $templateData['relatedReturnClass'] = '@return \\'. $relatedClassName. '[]';
                    $templateData['relatedQueryCaller'] = '\Lkt\QueryCaller\QueryCaller';
                    $templateData['singleReturnType'] = '';

                    if ($relatedQueryCaller) {
                        $templateData['relatedQueryCaller'] = '\\' . $relatedQueryCaller;
                    }
                }

                if ($field instanceof RelatedField) {
                    $templateData['isSingleMode'] = $field->isSingleMode();
                    if ($field->isSingleMode()) {
                        $templateData['relatedReturnClass'] = '@return \\'. $relatedClassName . '|null';
                        $templateData['singleReturnType'] = ': ?\\'. $relatedClassName;
                    }
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

            if ($field instanceof RelatedKeysMergeField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/related-keys-merge-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof BooleansComputedField) {
                $templateData['allRequired'] = BooleansComputedField::getAllConditionRequiredString($field, $schema);
                if ($templateData['allRequired'] === '') continue;
                $templateData['allRequiredSetter'] = BooleansComputedField::getAllConditionRequiredSetterString($field, $schema);

                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/computed-fields/booleans-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringEqualComputedField) {
                $relatedField = $schema->getField($field->getField());
                $templateData['getter'] = $relatedField->getGetterForComputed();
                $templateData['value'] = $field->getValue();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/computed-fields/string-equal-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringInComputedField) {
                $relatedField = $schema->getField($field->getField());
                $templateData['getter'] = $relatedField->getGetterForComputed();
                $templateData['value'] = "'".implode("','", $field->getValue())."'";
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/computed-fields/string-in-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringAboveMinLengthComputedField) {
                $relatedField = $schema->getField($field->getField());
                $templateData['getter'] = $relatedField->getGetterForComputed();
                $templateData['value'] = $field->getValue();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/computed-fields/string-above-min-length-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringBelowMaxLengthComputedField) {
                $relatedField = $schema->getField($field->getField());
                $templateData['getter'] = $relatedField->getGetterForComputed();
                $templateData['value'] = $field->getValue();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/computed-fields/string-below-max-length-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringBetweenMinAndMaxLengthComputedField) {
                $relatedField = $schema->getField($field->getField());
                $templateData['getter'] = $relatedField->getGetterForComputed();
                $value = $field->getValue();
                $templateData['min'] = $value[0];
                $templateData['max'] = $value[1];
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/computed-fields/string-between-min-and-max-length-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }
        }

        return implode("\n", $methods);
    }
}
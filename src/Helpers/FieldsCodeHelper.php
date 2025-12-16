<?php

namespace Lkt\CodeMaker\Helpers;

use Lkt\Factory\Schemas\CompositionSchema;
use Lkt\Factory\Schemas\ComputedFields\BooleansComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringAboveMinLengthComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringBelowMaxLengthComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringBetweenMinAndMaxLengthComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringEqualComputedField;
use Lkt\Factory\Schemas\ComputedFields\StringInComputedField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\ConcatField;
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
use Lkt\Factory\Schemas\Fields\ValueListField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;
use function PHPUnit\Framework\isInstanceOf;

class FieldsCodeHelper
{
    public static function makeFieldsCode(Schema $schema): string
    {
        $instanceSettings = $schema->getInstanceSettings();

        $className = $instanceSettings?->getAppClass();
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
                $relatedClassName = '';
                if ($relatedComponent) {
                    $relatedSchema = Schema::get($relatedComponent);
                    $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();
                }
                $templateData['component'] = $relatedComponent;
                $templateData['relatedClassName'] = '';
                $templateData['relatedReturnClass'] = '';

                if ($relatedClassName !== '') {
                    $templateData['relatedClassName'] = ':?\\' . $relatedClassName;
                    $templateData['relatedReturnClass'] = '@return \\' . $relatedClassName;
                }

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

                $options = $field->getAllowedOptions();

                $optionsMethods = [];
                foreach ($options as $key => $value) {
                    $d = is_numeric($key) ? trim($value) : trim($key);
                    $d = str_replace(' ', '', ucwords(str_replace('-', ' ', $d)));
                    $optionsMethods[$key] = $d;
                }

                $templateData['enabledEmptyPreset'] = $field->hasEnabledEmptyPreset();
                $templateData['options'] = $options;
                $templateData['optionsMethods'] = $optionsMethods;
                $templateData['comparatorsIn'] = $field->getComparatorsIn();
                $templateData['isMultiple'] = $field->isMultiple();
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/integer-choice-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            } elseif ($field instanceof IntegerField) {
                $templateData['isMultiple'] = $field->isMultiple();
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

                $templateData['enabledEmptyPreset'] = $field->hasEnabledEmptyPreset();
                $templateData['options'] = $options;
                $templateData['optionsMethods'] = $optionsMethods;
                $templateData['comparatorsIn'] = $field->getComparatorsIn();

                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/string-choice-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;

            } elseif ($field instanceof ValueListField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/value-list-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            } elseif ($field instanceof StringField || $field instanceof HTMLField) {
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
                    $templateData['relatedReturnClass'] = '@return \\' . $relatedClassName . '[]';
                    $templateData['relatedQueryCaller'] = '\Lkt\QueryCaller\QueryCaller';
                    $templateData['singleReturnType'] = '';

                    if ($relatedQueryCaller) {
                        $templateData['relatedQueryCaller'] = '\\' . $relatedQueryCaller;
                    }

                    if ($relatedSchema->hasComplexPrimaryKey()) {
                        $relatedIdentifiers = $relatedSchema->getIdentifiers();
                        $additionalInput = [];
                        $additionalInputDetection = [];
                        foreach ($relatedIdentifiers  as $relatedIdentifier) {
                            if ($relatedIdentifier->getColumn() === $field->getColumn()) continue;


                            $relatedIdentifierSchema = Schema::get($relatedIdentifier->getComponent());
                            $relatedIdentifierClassName = $relatedIdentifierSchema->getInstanceSettings()->getAppClass();

                            $additionalInput[] = "\\{$relatedIdentifierClassName}|int \${$relatedIdentifier->getName()}";
                            $additionalInputDetection[] = "'{$relatedIdentifier->getName()}' => \${$relatedIdentifier->getName()} instanceOf AbstractInstance ? (int)\${$relatedIdentifier->getName()}?->getIdColumnValue() : \${$relatedIdentifier->getName()},";
                        }

                        $templateData['additionalInput'] = implode(', ', $additionalInput);
                        $templateData['additionalInputDetection'] = implode(', ', $additionalInputDetection);
                    }
                }

                if ($field instanceof RelatedField) {
                    $templateData['isSingleMode'] = $field->isSingleMode();
                    if ($field->isSingleMode()) {
                        $templateData['relatedReturnClass'] = '@return \\' . $relatedClassName . '|null';
                        $templateData['singleReturnType'] = ': ?\\' . $relatedClassName;
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
                $templateData['relatedReturnClass'] = '@return \\' . $relatedClassName . '[]';

                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/pivot-field.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof FileField) {
                $templateData['isPublic'] = $field->getPublicPath() !== '';
                $templateData['publicPath'] = $field->getPublicPath();
                $templateData['isMultiple'] = $field->isMultiple();

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

            if ($field instanceof ConcatField) {
                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/concat-field.phtml')
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
                $templateData['value'] = "'" . implode("','", $field->getValue()) . "'";
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

        foreach ($schema->getCompositionFields() as $compositionField) {
            $compositionFieldName = $compositionField->getName();
            $composedComponent = $compositionField->getComponent();
            $composedSchema = Schema::get($composedComponent);
            $nestedComposedSchema = Schema::get($composedComponent);
            $compositionValues = $compositionField->getCompositionValues();
            dump('====');

            foreach ($compositionField->getCompositionContent() as $fieldName => $composedFieldName) {

                $composedPrimitiveInputType = 'mixed';
                $composedPrimitiveReturnType = 'mixed';
                $composedInstanceReturnType = '';
                $composedDocReturn = '';
                $additionalFields = '';
                $additionalInput = '';
                $additionalInputDetection = '';

                $nestedCompositionLevel = 1;

                $fieldMethod = ucfirst($fieldName);

                $composedField = $composedSchema->getField($composedFieldName);
                dump($composedField);

                if (!$composedField) {
                    $composedSchemaCompositionSchema = Schema::get($composedSchema->getComponent());
                    if (!$composedSchemaCompositionSchema->hasField($composedFieldName)) {
                        continue;
                    }

                    $nestedCompositionField = $composedSchemaCompositionSchema->getRelatedFieldHandlingThisField($composedFieldName);

                    $composedField = $composedSchemaCompositionSchema->getField($composedFieldName);

                    if (!$composedField) {
                        continue;
                    }
                    ++$nestedCompositionLevel;
                    $nestedComposedSchema = Schema::get($nestedCompositionField->getComponent());
                }


                if ($nestedComposedSchema->hasComplexPrimaryKey()) {
                    $relatedIdentifiers = $nestedComposedSchema->getIdentifiers();
                    $_additionalInput = [];
                    $_additionalInputDetection = [];
                    foreach ($relatedIdentifiers  as $relatedIdentifier) {
                        if ($nestedCompositionLevel === 1 && $relatedIdentifier->getColumn() === $compositionField->getColumn()) continue;

                        $relatedIdentifierSchema = Schema::get($relatedIdentifier->getComponent());
                        $relatedIdentifierClassName = $relatedIdentifierSchema->getInstanceSettings()->getAppClass();

                        $tmpAdditionalInput = "\\{$relatedIdentifierClassName}|int \${$relatedIdentifier->getName()}";
                        $compositionValue = $compositionValues[$relatedIdentifier->getName()];
                        if ($compositionValue !== null) {
                            $tmpAdditionalInput .= ' = null';
                        }

                        $_additionalInput[] = $tmpAdditionalInput;
                        $_additionalInputDetection[] = "'{$relatedIdentifier->getName()}' => \${$relatedIdentifier->getName()} instanceOf AbstractInstance ? (int)\${$relatedIdentifier->getName()}?->getIdColumnValue() : \${$relatedIdentifier->getName()}";
                    }

                    $_additionalInput = array_filter($_additionalInput, function ($d) { return trim($d) !== ''; });
                    $_additionalInputDetection = array_filter($_additionalInputDetection, function ($d) { return trim($d) !== ''; });

                    $additionalInput = implode(', ', $_additionalInput);
                    $additionalInputDetection = implode(', ', $_additionalInputDetection);
                }

                if ($composedField instanceof ForeignKeyField) {

                } elseif ($composedField instanceof IntegerField) {
                    if ($composedField->isMultiple()) {
                        $composedInstanceReturnType = '@return int[]';
                        $composedPrimitiveReturnType = '?array';
                        $composedPrimitiveInputType = 'array';
                    } else {
                        $composedPrimitiveReturnType = '?int';
                        $composedPrimitiveInputType = 'int';
                    }

                } elseif ($composedField instanceof StringField || $composedField instanceof HTMLField || $composedField instanceof EncryptField || $composedField instanceof ColorField || $composedField instanceof ConcatField) {
                    $composedPrimitiveReturnType = '?string';
                    $composedPrimitiveInputType = 'string';

                } elseif ($composedField instanceof BooleanField || $composedField instanceof BooleansComputedField || $composedField instanceof StringEqualComputedField || $composedField instanceof StringInComputedField || $field instanceof StringAboveMinLengthComputedField || $field instanceof StringBelowMaxLengthComputedField || $field instanceof StringBetweenMinAndMaxLengthComputedField) {
                    $composedPrimitiveReturnType = '?bool';
                    $composedPrimitiveInputType = 'bool';

                } elseif ($composedField instanceof FloatField) {
                    $composedPrimitiveReturnType = '?float';
                    $composedPrimitiveInputType = 'float';

                } elseif ($composedField instanceof DateTimeField || $field instanceof UnixTimeStampField) {
                    $composedPrimitiveReturnType = '?\Carbon\Carbon';
                    $composedPrimitiveInputType = '\Carbon\Carbon|\DateTime|string|int|null';

                } elseif ($composedField instanceof FileField) {
                    $additionalFields = $composedField->isMultiple() ? 'files' : 'file';

                } elseif ($composedField instanceof ForeignKeysField || $field instanceof RelatedField || $field instanceof RelatedKeysField || $field instanceof PivotField) {
                    $relatedSchema = Schema::get($composedField->getComponent());
                    $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();

                    if (method_exists($composedField, 'isSingleMode') && $composedField->isSingleMode()) {
                        $composedInstanceReturnType = ':?\\' . $relatedClassName;
                        $composedDocReturn = '@return \\' . $relatedClassName . '|null';
                    } else {
                        $composedInstanceReturnType = ':?\\' . $relatedClassName;
                        $composedDocReturn = '@return \\' . $relatedClassName . '[]';
                    }

                    //@TODO $composedPrimitiveInputType

                } elseif ($composedField instanceof JSONField) {
                    if ($composedField->isAssoc()) {
                        $composedPrimitiveReturnType = '?array';
                        $composedPrimitiveInputType = 'array';
                    } else {
                        $composedPrimitiveReturnType = '?\StdClass';
                        $composedPrimitiveInputType = '\StdClass';
                    }

                } elseif ($composedField instanceof RelatedKeysMergeField) {
                    $composedPrimitiveReturnType = 'array';
                    $composedPrimitiveInputType = 'array';
                }

                if ($composedPrimitiveReturnType !== '') $composedPrimitiveReturnType = ":{$composedPrimitiveReturnType}";

                $templateData = [
                    'fieldName' => $fieldName,
                    'fieldMethod' => $fieldMethod,
                    'composedComponent' => $composedComponent,
                    'composedFieldName' => $composedFieldName,
                    'compositionFieldName' => $compositionFieldName,
                    'composedInstanceReturnType' => $composedInstanceReturnType,
                    'composedDocReturn' => $composedDocReturn,
                    'composedPrimitiveReturnType' => $composedPrimitiveReturnType,
                    'composedPrimitiveInputType' => $composedPrimitiveInputType,
                    'returnSelf' => $returnSelf,
                    'additionalFields' => $additionalFields,
                    'additionalInput' => $additionalInput,
                    'additionalInputDetection' => $additionalInputDetection,
                ];


                $methods[] = Template::file(__DIR__ . '/../../assets/phtml/fields/composed-field.phtml')
                    ->setData($templateData)
                    ->parse();
            }
        }

//        $compositionSchema = CompositionSchema::get($schema->getComponent());
//        if ($compositionSchema) {
//            foreach ($compositionSchema->getAllCompositionContent() as $compositionContent) {
//
//                $compositionField = $compositionContent->getRelatedField();
//
//            }
//        }

        return implode("\n", $methods);
    }
}
<?php

namespace Lkt\CodeMaker;

use Lkt\CodeMaker\Helpers\FieldsCodeHelper;
use Lkt\CodeMaker\Helpers\FieldsQueryCallerHelper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;
use function Lkt\Tools\Strings\removeDuplicatedWhiteSpaces;

class WhereMaker
{
    public static function generate(): void
    {
        $stack = Schema::getStack();
        echo "Generating where...\n";
        $n = count($stack);
        echo "There are ({$n}) schemas \n";
        echo "\n";

        $registeredSchemas = array_keys($stack);
        echo "All registered schemas: ";
        foreach ($registeredSchemas as $schema) {
            echo "-> {$schema} \n";
        }
        echo "\n";
        echo "\n";

        foreach ($stack as $schema) {

            $component = $schema->getComponent();
            echo "Generating where for: {$component}...\n";

            $instanceSettings = $schema->getInstanceSettings();

            $className = $instanceSettings->getWhereClassName();
            if ($className === '') {
                echo "Component without Where: {$component}...\n";
                continue;
            }
            $returnSelf = '\\' . $className;

            $extends = $instanceSettings->hasLegalExtendClass()
                ? $instanceSettings->getClassToBeExtended()
                : AbstractInstance::class;

            $extends = '\\'. $extends;

            $namespace = $instanceSettings->getNamespaceForGeneratedClass();

            $relatedQueryCaller = $schema->getInstanceSettings()->getWhereFQDN();

            $templateData['relatedQueryCaller'] = '\Lkt\QueryBuilding\Where';

            if (!$relatedQueryCaller) {
                $relatedQueryCaller = 'Lkt\QueryBuilding\Where';
            }
            $relatedQueryCaller = '\\' . $relatedQueryCaller;

            $methods = FieldsQueryCallerHelper::makeFieldsCode($schema, true);
            $code = Template::file(__DIR__ . '/../assets/phtml/where-template.phtml')->setData([
                'component' => $component,
                'className' => $className,
                'namespace' => $namespace,
                'methods' => $methods,
                'returnSelf' => $returnSelf,
                'queryCaller' => $relatedQueryCaller,
            ])->parse();
            $code = str_replace("\n", ' ', $code);
            $code = removeDuplicatedWhiteSpaces($code);
            $code = '<?php ' .$code;

            $filePath = $instanceSettings->getWhereFullPath();
            $status = file_put_contents($filePath, $code);
            if ($status === false) {
                echo "Could't store {$filePath}\n";
                echo "Maybe an invalid path or not enough permissions\n";
            } else {
                echo "Successful storage at {$filePath}\n";
            }

            echo "\n";
        }
    }
}
<?php

namespace Lkt\CodeMaker;

use Lkt\CodeMaker\Helpers\FieldsCodeHelper;
use Lkt\CodeMaker\Helpers\FieldsOrderByHelper;
use Lkt\CodeMaker\Helpers\FieldsQueryCallerHelper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\Values\StringValue;
use Lkt\Templates\Template;
use function Lkt\Tools\Strings\removeDuplicatedWhiteSpaces;

class OrderByMaker
{
    public static function generate(): void
    {
        $stack = Schema::getStack();
        echo "Generating order by...\n";
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
            echo "Generating order by for: {$component}...\n";

            $instanceSettings = $schema->getInstanceSettings();

            $className = $instanceSettings->getAppClass();
            if ($className === '') {
                echo "Component without Order By: {$component}...\n";
                continue;
            }
            $className = explode('\\', $className);
            $className = $className[count($className) - 1];
            $className .= 'OrderBy';
            $returnSelf = '\\' . $className;

            $extends = $instanceSettings->hasLegalExtendClass()
                ? $instanceSettings->getClassToBeExtended()
                : AbstractInstance::class;

            $extends = '\\'. $extends;

            $namespace = $instanceSettings->getNamespaceForGeneratedClass();


            $relatedQueryCaller = [$instanceSettings->getNamespaceForGeneratedClass(), $className];
            $relatedQueryCaller = implode('\\', $relatedQueryCaller);

            $templateData['relatedQueryCaller'] = '\Lkt\QueryBuilding\OrderBy';

            if (!$relatedQueryCaller) {
                $relatedQueryCaller = 'Lkt\QueryBuilding\OrderBy';
            }
            $relatedQueryCaller = '\\' . $relatedQueryCaller;

            $methods = FieldsOrderByHelper::makeFieldsCode($schema, true);
            $code = Template::file(__DIR__ . '/../assets/phtml/order-by-template.phtml')->setData([
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


            $filePath = '';
            if ($instanceSettings->hasWhereStoreGeneratedClass()) {
                $filePath .= $instanceSettings->getWhereStoreGeneratedClass() . '/';
            }

            $filePath .=  "{$className}.php";
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
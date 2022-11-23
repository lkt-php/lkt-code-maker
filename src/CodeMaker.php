<?php

namespace Lkt\CodeMaker;

use Lkt\CodeMaker\Helpers\FieldsCodeHelper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;
use function Lkt\Tools\Strings\removeDuplicatedWhiteSpaces;

class CodeMaker
{
    public static function generate()
    {
        $stack = Schema::getStack();
        echo "Generating code...\n";
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
            echo "Generating code for: {$component}...\n";

            $instanceSettings = $schema->getInstanceSettings();

            $className = $instanceSettings->getAppClass();
            $returnSelf = '\\' . $className;

            $extends = $instanceSettings->hasLegalExtendClass()
                ? $instanceSettings->getClassToBeExtended()
                : AbstractInstance::class;

            $extends = '\\'. $extends;

            $implements = $instanceSettings->getImplementedInterfacesAsString();
            if ($implements !== ''){
                $implements = "implements {$implements};";
            }

            $traits = $instanceSettings->getUsedTraitsAsString();
            if ($traits !== ''){
                $traits = "use {$traits};";
            }

            $namespace = $instanceSettings->getNamespaceForGeneratedClass();


            $methods = FieldsCodeHelper::makeFieldsCode($schema);


            $relatedQueryCaller = $schema->getInstanceSettings()->getQueryCallerFQDN();

            $templateData['relatedQueryCaller'] = '\Lkt\QueryCaller\QueryCaller';

            if (!$relatedQueryCaller) {
                $relatedQueryCaller = 'Lkt\QueryCaller\QueryCaller';
            }
            $relatedQueryCaller = '\\' . $relatedQueryCaller;

            $code = Template::file(__DIR__ . '/../assets/phtml/class-template.phtml')->setData([
                'component' => $component,
                'className' => $instanceSettings->getClassNameForGeneratedClass(),
                'extends' => $extends,
                'implements' => $implements,
                'traits' => $traits,
                'namespace' => $namespace,
                'methods' => $methods,
                'returnSelf' => $returnSelf,
                'queryCaller' => $relatedQueryCaller,
            ])->parse();
            $code = str_replace("\n", ' ', $code);
            $code = removeDuplicatedWhiteSpaces($code);
            $code = '<?php ' .$code;

            $filePath = $instanceSettings->getGeneratedClassFullPath();
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
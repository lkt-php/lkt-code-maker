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
        foreach (Schema::getStack() as $schema) {

            $component = $schema->getComponent();
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
            $code = Template::file(__DIR__ . '/../assets/phtml/class-template.phtml')->setData([
                'component' => $component,
                'className' => $instanceSettings->getClassNameForGeneratedClass(),
                'extends' => $extends,
                'implements' => $implements,
                'traits' => $traits,
                'namespace' => $namespace,
                'methods' => $methods,
                'returnSelf' => $returnSelf,
            ])->parse();
            $code = str_replace("\n", ' ', $code);
            $code = removeDuplicatedWhiteSpaces($code);
            $code = '<?php ' .$code;

            $filePath = $instanceSettings->getGeneratedClassFullPath();
            $status = file_put_contents($filePath, $code);
            if ($status === false) {
                echo "Could't store {$filePath}";
                echo "Maybe an invalid path or not enough permissions";
            }
            dump($status);
        }
    }
}
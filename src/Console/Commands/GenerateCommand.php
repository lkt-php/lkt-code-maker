<?php

namespace Lkt\CodeMaker\Console\Commands;

use Lkt\CodeMaker\CodeMaker;
use Lkt\CodeMaker\GroupByMaker;
use Lkt\CodeMaker\OrderByMaker;
use Lkt\CodeMaker\QueryCallerMaker;
use Lkt\CodeMaker\SelectBuilderMaker;
use Lkt\CodeMaker\WhereMaker;
use Lkt\Factory\Schemas\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateCommand extends Command
{
    protected static $defaultName = 'lkt:make:code';

    public function execute(InputInterface $input, OutputInterface $output)
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


        WhereMaker::generate();
        QueryCallerMaker::generate();
        CodeMaker::generate();
        OrderByMaker::generate();
        GroupByMaker::generate();
        SelectBuilderMaker::generate();
        return 1;
    }
}
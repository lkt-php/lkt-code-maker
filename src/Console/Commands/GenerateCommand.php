<?php

namespace Lkt\CodeMaker\Console\Commands;

use Lkt\CodeMaker\CodeMaker;
use Lkt\CodeMaker\OrderByMaker;
use Lkt\CodeMaker\QueryCallerMaker;
use Lkt\CodeMaker\WhereMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateCommand extends Command
{
    protected static $defaultName = 'lkt:make:code';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        WhereMaker::generate();
        QueryCallerMaker::generate();
        CodeMaker::generate();
        OrderByMaker::generate();
        return 1;
    }
}
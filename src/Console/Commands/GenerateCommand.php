<?php

namespace Lkt\CodeMaker\Console\Commands;

use Lkt\CodeMaker\CodeMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateCommand extends Command
{
    protected static $defaultName = 'lkt:make:code';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        CodeMaker::generate();
        return 1;
    }
}
<?php

namespace Lkt\CodeMaker;

use Lkt\CodeMaker\Console\Commands\GenerateCommand;
use Lkt\Commander\Commander;

if (php_sapi_name() === 'cli') {
    Commander::register(new GenerateCommand());
}
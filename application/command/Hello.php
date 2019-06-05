<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Request;

class Hello extends Command {//继承think\console\Command


    /**
           * 重写configure
           * {@inheritdoc}
            */

    protected function configure() {
        $this->setName('hello')
                ->setDescription('hello word !');
    }

    /**
           * 重写execute
           * {@inheritdoc}
            */
    protected function execute(Input $input, Output $output) {
        $output->writeln('hello word !');
    }

}

?>
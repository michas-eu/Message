<?php
namespace Jasuwienas\MessageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SendMessagesCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this
            ->setName('messages:send')
            ->setDescription('Sends messages from queue.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $container->get('message.queue_manager.process')->run();
    }

}
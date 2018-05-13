<?php
namespace Jasuwienas\MessageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class SendMessagesCommand
 * @package Jasuwienas\MessageBundle\Command
 */
class SendMessagesCommand extends ContainerAwareCommand
{


    /**
     * Send messages
     */
    protected function configure()
    {
        $this
            ->setName('messages:send')
            ->addOption(
                'timeout',
                null,
                InputOption::VALUE_REQUIRED,
                'How long one script execution should run (in seconds)?',
                55
            )
            ->setDescription('Sends messages from queue.')
        ;
    }

    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $timeout = $input->getOption('timeout');
        $container->get('message.queue_manager.process')->setMaxExecutionTime($timeout)->run();
    }

}
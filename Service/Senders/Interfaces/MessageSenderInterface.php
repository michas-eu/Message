<?php
namespace Jasuwienas\MessageBundle\Service\Senders\Interfaces;

use Jasuwienas\MessageBundle\Model\MessageQueueInterface as MessageQueue;
use Jasuwienas\MessageBundle\Component\Response;

/**
 * Interface MessageSenderInterface
 * @package Jasuwienas\MessageBundle\Service\Senders\Interfaces
 */
interface MessageSenderInterface {

    /**
     * @param MessageQueue $messageQueue
     * @return Response
     */
    public function send($messageQueue);


}
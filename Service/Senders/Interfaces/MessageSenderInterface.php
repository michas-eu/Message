<?php
namespace Jasuwienas\MessageBundle\Service\Senders\Interfaces;

use Jasuwienas\MessageBundle\Model\MessageQueueInterface as MessageQueue;
use Jasuwienas\MessageBundle\Component\Response;

interface MessageSenderInterface {

    /**
     * @param MessageQueue $messageQueue
     * @return Response
     */
    public function send($messageQueue);


}
<?php
namespace Jasuwienas\MessageBundle\Service\Senders;

use Jasuwienas\MessageBundle\Component\Response;
use Jasuwienas\MessageBundle\Entity\MessageQueueInterface as MessageQueue;
use Jasuwienas\MessageBundle\Service\Senders\Interfaces\MessageSenderInterface;
use Swift_Mailer as Mailer;
use Swift_Message as Message;
use Exception;

class SmtpMailSenderService implements MessageSenderInterface {


    /**
     * @param Mailer $mailer
     * @param string $from
     */
    public function __construct(Mailer $mailer, $from) {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    /**
     * @param MessageQueue $messageQueue
     * @return Response
     */
    public function send($messageQueue)
    {
        try {
            $message = Message::newInstance()
                ->setSubject($messageQueue->getTitle())
                ->setFrom($this->from)
                ->setTo($messageQueue->getRecipient())
                ->setBody($messageQueue->getBody(), 'text/html')
                ->addPart($messageQueue->getPlainBody(), 'text/plain')
            ;
            $this->mailer->send($message);
            return new Response(true);
        } catch(Exception $exception) {
            return new Response(false, $exception->getMessage());
        }
    }
}
<?php
namespace Jasuwienas\MessageBundle\Service\Senders;

use Jasuwienas\MessageBundle\Component\Response;
use Jasuwienas\MessageBundle\Entity\MessageQueueInterface as MessageQueue;
use Jasuwienas\MessageBundle\Service\Senders\Interfaces\MessageSenderInterface;
use Swift_Mailer as Mailer;
use Swift_Message as Message;
use Swift_Attachment as Attachment;
use Exception;

/**
 * Class SmtpMailSenderService
 * @package Jasuwienas\MessageBundle\Service\Senders
 */
class SmtpMailSenderService implements MessageSenderInterface {

    /** @var Mailer */
    private $mailer;

    /** @var string */
    private $from;

    /** @var string */
    private $senderName;

    /**
     * SmtpMailSenderService constructor.
     * @param Mailer $mailer
     * @param string $from
     * @param string $senderName
     */
    public function __construct(Mailer $mailer, $from, $senderName) {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->senderName = $senderName;
    }

    /**
     * Send
     *
     * @param MessageQueue $messageQueue
     * @return Response
     */
    public function send($messageQueue)
    {
        try {
            $message = method_exists(Message::class, 'newInstance')
                ? Message::newInstance()->setSubject($messageQueue->getTitle())
                : new Message($messageQueue->getTitle())
            ;
            $message
                ->setFrom($this->from, $this->senderName)
                ->setTo($messageQueue->getRecipient())
                ->setBody($messageQueue->getBody(), 'text/html')
                ->addPart($messageQueue->getPlainBody(), 'text/plain')
            ;
            $attachments = $messageQueue->getAttachments();
            if($attachments && is_array($attachments)) {
                foreach($attachments as $attachment) {
                    if(!file_exists($attachment)) {
                        continue;
                    }
                    $attachmentPath = explode(DIRECTORY_SEPARATOR, $attachment);
                    $message->attach(
                        Attachment::fromPath($attachment)->setFilename($attachmentPath[count($attachmentPath) - 1])
                    );
                }
            }
            $this->mailer->send($message);
            return new Response(true);
        } catch(Exception $exception) {
            return new Response(false, $exception->getMessage());
        }
    }
}
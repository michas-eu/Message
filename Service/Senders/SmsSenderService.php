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
 * Class SmsSenderService
 * @package Jasuwienas\MessageBundle\Service\Senders
 */
class SmsSenderService implements MessageSenderInterface {

    /** @var string */
    private $host = 'https://api.smsapi.pl/sms.do';

    /** @var string */
    private $token;

    /** @var string */
    private $senderName;

    /**
     * SmsSenderService constructor.
     * @param string $host
     * @param string $token
     * @param string $senderName
     */
    public function __construct($host, $token, $senderName) {
        $this->host = $host;
        $this->token = $token;
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
            $header = 'Authorization Bearer ' . $this->token;
            $url = 'https://api.smsapi.pl/sms.do';
            $requestData = [
                'from' => $this->senderName,
                'to' => $messageQueue->getRecipient(),
                'message' => $messageQueue->getPlainBody() ? : $messageQueue->getBody() ,
                'format' => 'json'
            ];
            $handler = curl_init();
            curl_setopt($handler, CURLOPT_URL,$url);
            curl_setopt($handler, CURLOPT_POST, 1);
            curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($requestData));
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handler, CURLOPT_HTTPHEADER, [$header]);

            $result = curl_exec ($handler);
            curl_close ($handler);
            if ($result !== "OK") {
                return new Response(false, $result);
            }
            return new Response(true);
        } catch(Exception $exception) {
            return new Response(false, $exception->getMessage());
        }
    }
}
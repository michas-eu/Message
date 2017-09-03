<?php

namespace Jasuwienas\MessageBundle\Model;

use DateTime;
use Jasuwienas\MessageBundle\Service\QueueManagerService;

/**
 * MessageQueue
 * Storage of message from queue
 */
abstract class MessageQueue implements MessageQueueInterface
{

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $recipient;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $plainBody;

    /**
     * @var int
     */
    protected $status = 0;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var DateTime
     */
    protected $sendAt;

    /**
     * @var int
     */
    protected $attempts = 0;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var int
     */
    protected $contentId;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set adapter
     *
     * @param string $adapter
     *
     * @return MessageQueue
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Get adapter
     *
     * @return string
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set recipient
     *
     * @param string $recipient
     *
     * @return MessageQueue
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return MessageQueue
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return MessageQueue
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }


    /**
     * Set plain body
     *
     * @param string $plainBody
     *
     * @return MessageQueue
     */
    public function setPlainBody($plainBody)
    {
        $this->plainBody = $plainBody;
        return $this;
    }

    /**
     * Get plain body
     *
     * @return string
     */
    public function getPlainBody()
    {
        return $this->plainBody;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return MessageQueue
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set error
     *
     * @param string $error
     *
     * @return MessageQueue
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set send at
     *
     * @param DateTime $sendAt
     * @return MessageQueue
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    /**
     * Get send at
     *
     * @return DateTime
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * Set attempts
     *
     * @param int $attempts
     * @return MessageQueue
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * Get attempts
     *
     * @return int
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * Get attempts left
     *
     * @return int
     */
    public function getAttemptsLeft() {
        return QueueManagerService::MAX_SENDING_ATTEMPTS - $this->attempts;
    }

    /**
     * Set content type
     *
     * @param string $contentType
     *
     * @return MessageQueue
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set content id
     *
     * @param string $contentId
     *
     * @return MessageQueue
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * Get content id
     *
     * @return integer
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * Request next sending attempts (with 5 minutes interval)
     */
    public function requestNextSendingAttempt() {
        $this->attempts++;
        $this->sendAt = new DateTime('+5 minutes');
        $this->status = MessageQueueInterface::STATUS_TRY_AGAIN;
    }
}

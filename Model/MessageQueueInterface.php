<?php

namespace Jasuwienas\MessageBundle\Model;

use DateTime;

/**
 * MessageQueue
 * Storage of message from queue
 */
interface MessageQueueInterface
{

    const STATUS_NEW = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_TRY_AGAIN = 3;
    const STATUS_ERROR = 4;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set adapter
     *
     * @param string $adapter
     *
     * @return MessageQueueInterface
     */
    public function setAdapter($adapter);

    /**
     * Get adapter
     *
     * @return string
     */
    public function getAdapter();

    /**
     * Set recipient
     *
     * @param string $recipient
     *
     * @return MessageQueueInterface
     */
    public function setRecipient($recipient);

    /**
     * Get recipient
     *
     * @return string
     */
    public function getRecipient();

    /**
     * Set title
     *
     * @param string $title
     *
     * @return MessageQueueInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set body
     *
     * @param string $body
     *
     * @return MessageQueueInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string
     */
    public function getBody();


    /**
     * Set plain body
     *
     * @param string $plainBody
     *
     * @return MessageQueueInterface
     */
    public function setPlainBody($plainBody);

    /**
     * Get plain body
     *
     * @return string
     */
    public function getPlainBody();

    /**
     * Set status
     *
     * @param int $status
     * @return MessageQueueInterface
     */
    public function setStatus($status);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set error
     *
     * @param string $error
     *
     * @return MessageQueueInterface
     */
    public function setError($error);

    /**
     * Get error
     *
     * @return string
     */
    public function getError();

    /**
     * Set created at
     *
     * @param DateTime $createdAt
     * @return MessageQueueInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get created at
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Set updated at
     *
     * @param DateTime $updatedAt
     * @return MessageQueueInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get updated at
     *
     * @return DateTime
     */
    public function getUpdatedAt();

    /**
     * Set send at
     *
     * @param DateTime $sendAt
     * @return MessageQueueInterface
     */
    public function setSendAt($sendAt);

    /**
     * Get send at
     *
     * @return DateTime
     */
    public function getSendAt();

    /**
     * Set attempts
     *
     * @param int $attempts
     * @return MessageQueueInterface
     */
    public function setAttempts($attempts);

    /**
     * Get attempts
     *
     * @return int
     */
    public function getAttempts();

    /**
     * Get attempts left
     *
     * @return int
     */
    public function getAttemptsLeft();

    /**
     * Set content type
     *
     * @param string $contentType
     *
     * @return MessageQueueInterface
     */
    public function setContentType($contentType);

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType();

    /**
     * Set content id
     *
     * @param string $contentId
     *
     * @return MessageQueueInterface
     */
    public function setContentId($contentId);

    /**
     * Get content id
     *
     * @return integer
     */
    public function getContentId();

    /**
     * Request next sending attempts (with 5 minutes interval)
     */
    public function requestNextSendingAttempt();
}

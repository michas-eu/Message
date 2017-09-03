# Symfony2 Message
Integrates messages (mailer, sms api) into Symfony2 project.

## Installation

1. Add as composer dependency:

  ```bash
  composer require jasuwienas/message
  ```
2. Add in application kernel:

  ```php
  class AppKernel extends Kernel
  {
      public function registerBundles()
      {
      //...
      $bundles[] = new \Jasuwienas\MessageBundle\MessageBundle();
      return $bundles;
      }
  }
  ```
3. Create message queue object entity, it should extend class \Jasuwienas\MessageBundle\Model\MessageQueue. For example:

```
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Jasuwienas\MessageBundle\Service\QueueManagerService;
use Jasuwienas\MessageBundle\Model\MessageQueue as BaseMessageQueue;

/**
 * MessageQueue
 *
 * @ORM\Table("message_queue")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class MessageQueue extends BaseMessageQueue
{

    const STATUS_NEW = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_TRY_AGAIN = 3;
    const STATUS_ERROR = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="adapter", type="string", length=32, nullable=true)
     */
    protected $adapter;

    /**
     * @var string
     *
     * @ORM\Column(name="recipient", type="string", length=255, nullable=true)
     */
    protected $recipient;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    protected $body;

    /**
     * @var string
     *
     * @ORM\Column(name="plain_body", type="text", nullable=true)
     */
    protected $plainBody;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", length=1, options={"comment" = "0 - new, 1 - processed, 2 - success, 3 - try again, 4 - error"}, nullable=false)
     */
    protected $status = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="string", length=255, nullable=true)
     */
    protected $error;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="send_at", type="datetime", nullable=true)
     */
    protected $sendAt;

    /**
     * @var int
     *
     * @ORM\Column(name="attempts", type="smallint", length=1, options={"comment" = "counts number of sending attempts"}, nullable=false)
     */
    protected $attempts = 0;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist()
    {
        $now = new Datetime;
        if(!$this->getCreatedAt()) {
            $this->setCreatedAt($now);
        }
        if(!$this->getSendAt()) {
            $this->setSendAt($now);
        }
        $this->setUpdatedAt($now);
    }

    /**
     * Set created at
     *
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get created at
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated at
     *
     * @param DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updated at
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}
```
4. Set entity class name in your application config.yml file. For example:

```
message:
    queue_object_class: AppBundle\Entity\MessageQueue
```
5. Add message to queue:

```
    $manager = $container->get('message.queue_manager')->push('your@email', 'Message title', 'Message body');
```
6. Message sending command:
php app/console messages:send
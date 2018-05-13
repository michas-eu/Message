# Symfony Message
Integrates messages (mailer, sms api) into Symfony project.

## Installation

### Add as composer dependency:

  ```bash
  composer require jasuwienas/message
  ```
### Add in application kernel:

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
### Create message queue object entity

it should extend class \Jasuwienas\MessageBundle\Model\MessageQueue. For example:

```
<?php

namespace App\Entity;

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
     * @ORM\Column(name="adapter", type="string", nullable=false, length=32, options={"comment": "Name of the sender which will process this message"})
     */
    protected $adapter;

    /**
     * @var string
     *
     * @ORM\Column(name="recipient", type="text", nullable=false, options={"comment": "Recipient of the message (email for MailSender and phone number for SMSSender)"})
     */
    protected $recipient;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=true, options={"comment": "Message title"})
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true, options={"comment": "Message body"})
     */
    protected $body;

    /**
     * @var string
     *
     * @ORM\Column(name="plan_body", type="text", nullable=true, options={"comment": "Body of the message with special characters removed"})
     */
    protected $plainBody;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default":0, "comment": "Message sending status. 0 - new, awaiting, 1 - processed, 2 - sent, 3 - sending failed, waiting for next attempt, 4 - error"})
     */
    protected $status = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="text", nullable=true, options={"comment": "Error message"})
     */
    protected $error;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="send_at", type="datetime", nullable=true, options={"comment": "Date time of sending this message"})
     */
    protected $sendAt;

    /**
     * @var int
     *
     * @ORM\Column(name="attempts", type="integer", nullable=true, options={"default": 0, "comment": "Number of attempts to send this message"})
     */
    protected $attempts = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="content_type", type="text", nullable=true, options={"comment": "Entity connect with this message - name"})
     */
    protected $contentType;

    /**
     * @var int
     *
     * @ORM\Column(name="content_id", type="integer", nullable=true, options={"comment": "Entity connect with this message - id"})
     */
    protected $contentId;

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
     * @var array
     *
     * @ORM\Column(name="attachments", type="json_array", nullable=true, options={"comments": "List of attachments (paths)"})
     */
    protected $attachments = [];

    /**
     * Priority - higher priority messages will be send sooner
     *
     * @var int
     * @ORM\Column(name="priority", type="integer", nullable=false, options={"default": 0, "comments": "Message sending priority - the biggest priority the sooner mail will be send"})
     */
    protected $priority = 0;

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
### Set entity class name in your application config.yml file.

For example:

```
message:
    queue_object_class: App\Entity\MessageQueue
```

### Configuring Smtp mail sender.
	
Configure base smtp connection (https://symfony.com/doc/current/email.html)
	
<b>IMPORTANT</b> Remove spool from your config (https://symfony.com/doc/current/email/spool.html). You should remove line

```
    spool: { type: 'memory' }
```

if it exists in your configuration

Add
```
message:
    smtp_mailer_user: test@test.com
```
to your config.yml (config/packages/message.yaml in symfony 4).

Replace test@test.com with message sender user

### Configuring Freshmail
add
```
message:
    freshmail_api_host:         'https://api.freshmail.com/'
    freshmail_api_prefix:       'rest/'
    freshmail_api_api_key:      API_KEY
    freshmail_api_secret_key:   SECRET_KEY
```
to your config.yml (config/packages/message.yaml in symfony 4).

Replace API_KEY and SECRET_KEY with your frashmail keys.

### Configuring SMSApi

Add
```
message:
    sms_api_host: 'https://api.smsapi.pl/sms.do'
    sms_api_access_token: API_TOKEN
```
to your config.yml (config/packages/message.yaml in symfony 4).
 
 Replace API_TOKEN  with your sms_api access token.

### Adding messages to queue:
 $this->get('message.queue_manager')->push(
             'jasuwienas@gmail.com',
            'Test title',
            'Test content',
            new DateTime(),
            'smtp'
 );
### Message sending command:
Symfony < 3.4
```
php app/console messages:send
```
Symfony 4+
```
bin/console messages:send
```
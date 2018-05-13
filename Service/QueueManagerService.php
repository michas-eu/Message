<?php
namespace Jasuwienas\MessageBundle\Service;

use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\ORM\EntityRepository;
use Jasuwienas\MessageBundle\Model\MessageQueueInterface as MessageQueue;
use DateTime;
use Exception;


class QueueManagerService {

    const MAX_SENDING_ATTEMPTS = 5;

    /** @var EntityManager $entityManager */
    private $entityManager;

    /** @var string */
    private $messageQueueClass;

    /** @var string */
    private $defaultAdapter = 'smtp';

    /**
     * QueueManagerService constructor.
     * @param EntityManager $entityManager
     * @param $messageQueueClass
     * @param string $defaultAdapter
     */
    public function __construct(EntityManager $entityManager, $messageQueueClass, $defaultAdapter = 'smtp') {
        $this->entityManager = $entityManager;
        $this->messageQueueClass = $messageQueueClass;
        $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * @param string $recipient - fe. email / phone number
     * @param string $title
     * @param string $body
     * @param DateTime|null $sendAt
     * @param string $adapter
     * @param array $attachments
     */
    public function push($recipient, $title, $body, $sendAt = null, $adapter = null, $attachments = []) {
        $queueElement = $this->create($recipient, $title, $body, $sendAt, $adapter, $attachments);
        $this->save($queueElement);
    }

    /**
     * @param string $recipient - fe. email / phone number
     * @param string $title
     * @param string $body
     * @param DateTime|null $sendAt
     * @param string $adapter
     * @param array $attachments
     *
     * @return MessageQueue
     */
    public function create($recipient, $title, $body, $sendAt = null, $adapter = null, $attachments = []) {
        if($adapter === null) {
            $adapter = $this->defaultAdapter;
        }
        if(!$sendAt) {
            $sendAt = new DateTime();
        }
        $class = $this->messageQueueClass;
        /** @var MessageQueue $queueElement */
        $queueElement = new $class();
        $queueElement
            ->setAdapter(strtolower($adapter))
            ->setRecipient($recipient)
            ->setTitle($title)
            ->setBody($body)
            ->setPlainBody(strip_tags($body))
            ->setSendAt($sendAt)
            ->setAttachments($attachments)
        ;
        return $queueElement;
    }

    /**
     * @return MessageQueue|null
     */
    public function pop() {
        if($queueElement = $this->getFirstReadyToProcess()) {
            $queueElement->setStatus(MessageQueue::STATUS_PROCESSED);
            $this->save($queueElement);
        }
        return $queueElement;
    }

    /**
     * Get first ready to process
     *
     * @return MessageQueue|null
     */
    public function getFirstReadyToProcess() {
        try {

            return $this->createReadyToProcessQueryBuilder('queue')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch(Exception $exception) {
            return null;
        }
    }

    /**
     * @param $queryName
     * @param null $indexBy
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createReadyToProcessQueryBuilder($queryName, $indexBy = null) {
        /** @var EntityRepository $repository  */
        $repository = $this->entityManager->getRepository($this->messageQueueClass);
        $queryBuilder = $repository->createQueryBuilder($queryName, $indexBy)
            ->andWhere($queryName . '.status in (:status)')
            ->andWhere($queryName . '.sendAt < :sendAt')
            ->setParameter('status', [MessageQueue::STATUS_NEW, MessageQueue::STATUS_TRY_AGAIN])
            ->setParameter('sendAt', new DateTime())
            ;
        if($this->entityManager->getClassMetadata($this->messageQueueClass)->hasField('priority')) {
            $queryBuilder->orderBy($queryName.'.priority', 'desc');
        }
        return $queryBuilder;
    }

    /**
     * @param MessageQueue $queueElement
     * @param string $errorMessage
     */
    public function handleError($queueElement, $errorMessage) {
        $queueElement
            ->setStatus(MessageQueue::STATUS_ERROR)
            ->setError($errorMessage)
        ;
        $this->save($queueElement);
    }

    /**
     * @param MessageQueue $queueElement
     */
    public function handleSuccess($queueElement) {
        $queueElement->setStatus(MessageQueue::STATUS_SUCCESS);
        $this->save($queueElement);
    }

    /**
     * @param MessageQueue $queueElement
     * @param string $attemptReason
     */
    public function handleNextAttempt($queueElement, $attemptReason = '') {
        $queueElement->setError($attemptReason)->requestNextSendingAttempt();
        if($queueElement->getAttempts() >= self::MAX_SENDING_ATTEMPTS) {
            $this->handleError($queueElement, $attemptReason);
            return;
        }
        $this->save($queueElement);
    }

    /**
     * @param MessageQueue $queueElement
     */
    public function save(&$queueElement) {
        $this->entityManager->persist($queueElement);
        $this->entityManager->flush();
    }

}
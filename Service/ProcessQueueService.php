<?php
namespace Jasuwienas\MessageBundle\Service;

use Jasuwienas\MessageBundle\Component\Response;
use Jasuwienas\MessageBundle\Model\MessageQueueInterface as MessageQueue;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use Jasuwienas\MessageBundle\Service\QueueManagerService as QueueManager;
use Jasuwienas\MessageBundle\Service\Senders\Interfaces\MessageSenderInterface as MessageSender;
use Exception;

/**
 * Class ProcessQueueService
 * @package Jasuwienas\MessageBundle\Service
 */
class ProcessQueueService {

    /** @var int */
    private $maxExecutionTime = 55;

    /**
     * @var QueueManager
     */
    private $queueManager;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Translator
     */
    private $translator;

    /** @var int */
    private $startTime;

    /**
     * Currently processed message
     *
     * @var MessageQueue
     */
    private $processedMessage;

    /**
     * ProcessQueueService constructor.
     * @param QueueManagerService $queueManager
     * @param Container $container
     * @param Translator $translator
     */
    public function __construct(QueueManager $queueManager, Container $container, Translator $translator) {
        $this->queueManager = $queueManager;
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
     * Set max execution time (in seconds)
     *
     * @param int $maxExecutionTime
     * @return ProcessQueueService
     */
    public function setMaxExecutionTime($maxExecutionTime) {
        $this->maxExecutionTime = $maxExecutionTime;
        return $this;
    }

    /**
     * Run
     *
     * @throws Exception
     */
    public function run() {
        $this->initialize();
        while($this->getExecutionTime() < $this->maxExecutionTime) {
            $this->sendOneMessage();
        }
    }

    /**
     * Initialize
     */
    private function initialize() {
        $this->startTime = microtime(true);
    }

    /**
     * Sends one message from queue. Returns true for success and false for failure.
     *
     * @throws Exception
     * @return bool
     */
    private function sendOneMessage() {
        $this->processedMessage = $this->queueManager->pop();
        try {
            if (!$this->processedMessage || !$this->processedMessage instanceof MessageQueue) {
                return $this->waitForMessages();
            }
            $serviceName = 'message.sender.' . $this->processedMessage->getAdapter();
            if (!$this->container->has($serviceName)) {
                return $this->handleNotExistingAdapter();
            }
            /** @var MessageSender $messageSender */
            $messageSender = $this->container->get($serviceName);
            if(!$messageSender instanceof MessageSender) {
                return $this->handleWrongAdapterInterface();
            }
            $response = $messageSender->send($this->processedMessage);
            if(!$response instanceof Response) {
                return $this->handleWrongAdapterResponse();
            }
            if (!$response->getResult()) {
                throw new Exception($response->getError());
            }
            $this->queueManager->handleSuccess($this->processedMessage);
        } catch(Exception $exception) {
            if($this->processedMessage instanceof MessageQueue) {
                $this->queueManager->handleNextAttempt($this->processedMessage, $exception->getMessage());
            }
            return false;
        }
        return true;
    }

    /**
     * This method is executed when there are no messages ready to send.
     * It forces application to wait for new messages.
     *
     * @return bool
     */
    private function waitForMessages() {
        sleep(5);
        return false;
    }

    /**
     * Adapter does not exist. So we are not even waiting for next attempt, and setting error now.
     *
     * @return bool
     */
    private function handleNotExistingAdapter() {
        $errorMessage = $this->translator->trans('jswns.message.error.adapter');
        $this->queueManager->handleError($this->processedMessage, $errorMessage);
        return false;
    }

    /**
     * Proper adapter does not exist. So we are not even waiting for next attempt, and setting error now.
     * Adapter exists but does not extend requested interface
     *
     * @return bool
     */
    private function handleWrongAdapterInterface() {
        $errorMessage = $this->translator->trans('jswns.message.error.adapter.interface');
        $this->queueManager->handleError($this->processedMessage, $errorMessage);
        return false;
    }

    /**
     * Proper adapter does not exist. So we are not even waiting for next attempt, and setting error now.
     * Adapter exists but does not return proper response.
     *
     * @return bool
     */
    private function handleWrongAdapterResponse() {
        $errorMessage = $this->translator->trans('jswns.message.error.adapter.response');
        $this->queueManager->handleError($this->processedMessage, $errorMessage);
        return false;
    }

    private function getExecutionTime() {
        return (microtime(true) - $this->startTime);
    }

}
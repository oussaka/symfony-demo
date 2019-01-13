<?php
/**
 * Created by PhpStorm.
 * User: oussaka
 * Date: 13/01/19
 * Time: 18:30
 */

namespace App\Mailer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Swift_ConfigurableSpool;
use Swift_Mime_SimpleMessage;
use Swift_Transport;

class RabbitMqSpool extends Swift_ConfigurableSpool
{
    /** @var ProducerInterface $producer */
    protected $producer;

    /** @var ConsumerInterface $producer */
    protected $consumer;

    public function __construct($producer, $consumer)
    {
        $this->producer = $producer;
        $this->consumer = $consumer;
    }

    /**
     * Starts this Spool mechanism.
     */
    public function start()
    {
        // TODO: Implement start() method.
    }

    /**
     * Stops this Spool mechanism.
     */
    public function stop()
    {
        // TODO: Implement stop() method.
    }

    /**
     * Tests if this Spool mechanism has started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * Queues a message.
     *
     * @param Swift_Mime_SimpleMessage $message The message to store
     *
     * @return bool Whether the operation has succeeded
     */
    public function queueMessage(Swift_Mime_SimpleMessage $message)
    {
        $serialized = serialize($message);
        $this->getMailProducer()->publish($serialized);
    }

    /**
     * Sends messages using the given transport instance.
     *
     * @param Swift_Transport $transport A transport instance
     * @param string[] $failedRecipients An array of failures by-reference
     *
     * @return int The number of sent emails
     */
    public function flushQueue(Swift_Transport $transport, &$failedRecipients = null)
    {
        return $this->getConsumer()->consume($this->getMessageLimit());
    }

    protected function getConsumer() {
        return $this->consumer;
    }

    protected function getMailProducer() {
        return $this->producer;
    }
}
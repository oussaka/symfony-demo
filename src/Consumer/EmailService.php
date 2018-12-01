<?php
/**
 * Created by PhpStorm.
 * User: oussaka
 * Date: 18/11/2018
 * Time: 09:23
 */

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class EmailService implements ConsumerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    private $delayedProducer;

    private $logger;

    public function __construct(\Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        return $this->processMessage($msg);
    }

    public function processMessage(AMQPMessage $msg)
    {
        $message = unserialize($msg->getBody());
        if (false == $this->mailer->send($message)) {
            $this->logger->error('Corrupt message goes into Dead Letter Exchange.');

            return ConsumerInterface::MSG_REJECT;
        }

        $this->logger->info('Message consumed.');
    }
}
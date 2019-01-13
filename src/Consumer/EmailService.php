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
    private $logger;
    private $swiftTransport;

    public function __construct(\Swift_Mailer $mailer, \Swift_Transport $swiftTransport, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->swiftTransport = $swiftTransport;
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
        $transport = $this->getTransport();

        if (false == $transport->send($message)) {
            $this->logger->error('Corrupt message goes into Dead Letter Exchange.');
            $transport->stop();

            return ConsumerInterface::MSG_REJECT;
        }

        $transport->stop();
        $this->logger->info('Message consumed.');

        return ConsumerInterface::MSG_ACK;
    }

    /** @return \Swift_Transport  */
    protected function getTransport()
    {
        /** @var \Swift_Transport $swiftTransport */

        if (!$this->swiftTransport->isStarted()) {
            $this->swiftTransport->start();
        }

        return $this->swiftTransport;
    }
}
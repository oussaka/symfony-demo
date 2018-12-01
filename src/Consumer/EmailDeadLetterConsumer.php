<?php
/**
 * Created by PhpStorm.
 * User: oussaka
 * Date: 13/01/19
 * Time: 11:44
 */
namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class EmailDeadLetterConsumer implements ConsumerInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function execute(AMQPMessage $message)
    {
        $message = json_decode($message->body);

        $this->logger->info('Do something about "dead-lettered" message.');
    }
}
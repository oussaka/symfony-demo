<?php
/**
 * Created by PhpStorm.
 * User: oussaka
 * Date: 18/11/2018
 * Time: 09:23
 */

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class EmailService implements ConsumerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
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
        $this->mailer->send($message);
    }
}
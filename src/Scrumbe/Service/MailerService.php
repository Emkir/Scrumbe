<?php
namespace Scrumbe\Service;

class MailerService {

    protected $container;
    protected $sendFrom;

    public function __construct($container, $sendFrom)
    {
        $this->container = $container;
        $this->sendFrom = $sendFrom;
    }

    public function sendConfirmEmail($subject = '', $data = array(), $sendTo = '', $template = null)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->sendFrom)
            ->setTo($sendTo)
            ->setContentType('text/html');
        if (!is_null($template))
            $message->setBody($this->container->get('templating')->render($template, $data));

        $this->container->get('mailer')->send($message);
    }

} 
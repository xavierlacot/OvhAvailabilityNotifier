<?php

namespace AppBundle\Notifier;

use Symfony\Component\Templating\EngineInterface;
use Hip\MandrillBundle\Dispatcher;
use Hip\MandrillBundle\Message;

class MailNotifier implements NotifierInterface
{
    protected $templating;
    protected $dispatcher;
    protected $notificationStorage;
    protected $recipients;

    public function __construct(EngineInterface $templating, Dispatcher $dispatcher, NotificationStorage $notificationStorage, array $recipients)
    {
        $this->templating = $templating;
        $this->dispatcher = $dispatcher;
        $this->notificationStorage = $notificationStorage;
        $this->recipients = $recipients;
    }

    public function notify($diff)
    {
        $html = $this->templating->render(
            'AppBundle::notification_mail.html.twig',
            array('diff' => $diff)
        );

        foreach ($this->recipients as $recipient) {
            $mandrillMessage = new Message();
            $mandrillMessage
                ->setFromEmail('xlacot+ovh-notify@jolicode.com')
                ->setFromName('OvhBot')
                ->addTo($recipient)
                ->setSubject('[OVH BOT] server availability change')
                ->setHtml($html);
            $return = $this->dispatcher->send($mandrillMessage);
            $status = ($return[0]['status'] == 'sent');
            $this->notificationStorage->log($recipient, 'mail', $status);
        }
    }
}

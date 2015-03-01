<?php

namespace AppBundle\Notifier;

use CL\Slack\Transport\ApiClient;
use CL\Slack\Payload\ChatPostMessagePayload;

class SlackNotifier implements NotifierInterface
{
    protected $slack;
    protected $notificationStorage;
    protected $recipients;

    public function __construct(ApiClient $slack, NotificationStorage $notificationStorage, array $recipients)
    {
        $this->slack = $slack;
        $this->notificationStorage = $notificationStorage;
        $this->recipients = $recipients;
    }

    protected function computeText($availability, $type)
    {
        if ('added' === $type) {
            return sprintf(
                ':loudspeaker: Le serveur "%s" est disponible dans le datacenter "%s". Commander : %s',
                $availability->getReference(),
                $availability->getZone(),
                'https://eu.soyoustart.com/fr/commande/soYouStart.xml?reference='.$availability->getReference().'&amp;quantity=1'
            );
        } else {
            return sprintf(
                ':weary: Le serveur "%s" n\'est plus disponible dans le datacenter "%s"',
                $availability->getReference(),
                $availability->getZone()
            );
        }
    }

    protected function sendAvailability($availability, $recipient, $type)
    {
        $payload  = new ChatPostMessagePayload();
        $payload->setChannel($recipient);
        $payload->setText($this->computeText($availability, $type));
        $payload->setUsername('OvhBot');
        $payload->setIconEmoji('computer');
        $response = $this->slack->send($payload);

        $this->notificationStorage->log($recipient, 'slack', $response->isOk());
    }

    public function notify($diff)
    {
        foreach ($this->recipients as $recipient) {
            foreach ($diff['added'] as $availability) {
                $this->sendAvailability($availability, $recipient, 'added');
            }

            foreach ($diff['removed'] as $availability) {
                $this->sendAvailability($availability, $recipient, 'removed');
            }
        }
    }
}

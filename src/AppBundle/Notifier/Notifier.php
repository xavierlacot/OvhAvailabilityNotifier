<?php

namespace AppBundle\Notifier;

use Symfony\Component\Templating\EngineInterface;

class Notifier
{
    protected $notifiers = array();

    public function addNotifier(NotifierInterface $notifier)
    {
        $this->notifiers[] = $notifier;
    }

    public function notify($diff)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->notify($diff);
        }
    }
}

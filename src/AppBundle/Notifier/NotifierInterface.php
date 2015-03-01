<?php

namespace AppBundle\Notifier;

interface NotifierInterface
{
    public function notify($diff);
}

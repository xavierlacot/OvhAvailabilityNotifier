<?php

namespace AppBundle\Notifier;

class NotificationStorage
{
    protected $storageFile;

    public function __construct($storageFile)
    {
        $this->storageFile = $storageFile;
    }

    public function log($recipient, $type, $status)
    {
        $text = sprintf(
            "%s - %s - %s - %s\n",
            date('Y-m-d-H-i-s'),
            $recipient,
            $type,
            $status ? 'success' : 'fail'
        );
        file_put_contents($this->storageFile, $text, FILE_APPEND);
    }
}

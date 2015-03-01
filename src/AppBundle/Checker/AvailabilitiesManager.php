<?php

namespace AppBundle\Checker;

class AvailabilitiesManager
{
    protected $workspace;

    public function __construct($workspace)
    {
        $this->workspace = $workspace;
    }

    protected function computeKey($references, $locations)
    {
        return sprintf(
            '%s-%s',
            md5(implode(',', $references)),
            md5(implode(',', $locations))
        );
    }

    public function getCurrent($references, $locations)
    {
        $filename = $this->getCurrentFilePath($references, $locations);

        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            return unserialize($content);
        } else {
            return array();
        }
    }

    public function getCurrentFilePath($references, $locations)
    {
        return $this->workspace.DIRECTORY_SEPARATOR.'current-'.$this->computeKey($references, $locations);
    }

    public function getHistoryFilePath($references, $locations)
    {
        return $this->workspace.DIRECTORY_SEPARATOR.'history-'.$this->computeKey($references, $locations);
    }

    public function logDiff($references, $locations, $diff)
    {
        $date = date('Y-m-d-H-i-s');
        $filename = $this->getHistoryFilePath($references, $locations);
        $diffText = "\n";

        foreach ($diff['added'] as $added) {
            $diffText .= sprintf(
                "%s - The server \"%s\" becomes available in zone \"%s\".\n",
                $date,
                $added->getReference(),
                $added->getZone()
            );
        }

        foreach ($diff['removed'] as $removed) {
            $diffText .= sprintf(
                "%s - The server \"%s\" is no longer available in zone \"%s\".\n",
                $date,
                $added->getReference(),
                $added->getZone()
            );
        }

        file_put_contents($filename, $diffText, FILE_APPEND);
    }

    public function setCurrent($references, $locations, $availaibilites)
    {
        $filename = $this->getCurrentFilePath($references, $locations);
        file_put_contents($filename, serialize($availaibilites));
    }
}

<?php

namespace AppBundle\Checker;

use Psr\Log\LoggerInterface;
use AppBundle\Notifier\Notifier;

class OvhChecker
{
    protected static $defaultLocations = array(
        'bhs',
        'gra',
        'rbx',
        'sbg',
    );

    protected $client;
    protected $manager;
    protected $notifier;

    public function __construct(Client $client, AvailabilitiesManager $manager, Notifier $notifier)
    {
        $this->client = $client;
        $this->availaibilitiesManager = $manager;
        $this->notifier = $notifier;
    }

    public function check(array $references, array $locations)
    {
        $availabilites = $this->getAvailabilities($references, $locations);
        $diff = $this->computeDiff($references, $locations, $availabilites);
        $this->availaibilitiesManager->logDiff($references, $locations, $diff);

        if (count($diff['added']) + count($diff['removed']) > 0) {
            $this->notifier->notify($diff);
            $this->availaibilitiesManager->setCurrent($references, $locations, $availabilites);
        }
    }

    protected function computeDiff(array $references, array $locations, array $availabilites)
    {
        $current = $this->availaibilitiesManager->getCurrent($references, $locations);

        foreach ($availabilites as $key => $availability) {
            foreach ($current as $currentKey => $currentAvailability) {
                if ($availability->getZone() === $currentAvailability->getZone()
                    && $availability->getReference() === $currentAvailability->getReference()
                ) {
                    unset($availabilites[$key]);
                    unset($current[$currentKey]);
                }
            }
        }

        return array(
            'added' => $availabilites,
            'removed' => $current
        );
    }

    /**
     * checks the availability of certain server references in certain datacenters
     *
     * @param  array  $references server references to check
     * @param  array  $locations  datacenters to check
     */
    protected function getAvailabilities(array $references, array $locations)
    {
        if (count($locations) == 0) {
            $locations = self::$defaultLocations;
        }

        $return = array();
        $availabilites = $this->client->getAvailabilities();

        foreach($availabilites as $availability) {
            if (in_array($availability['reference'], $references)) {
                $zones = $availability['zones'];

                foreach ($zones as $zone) {
                    if ($zone['availability'] !== 'unavailable' && in_array(strtolower($zone['zone']), $locations)) {
                        $return[] = new Availability($availability['reference'], strtolower($zone['zone']));
                    }
                }
            }
        }

        return $return;
    }
}

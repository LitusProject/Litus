<?php

namespace TicketBundle\Component\Document\Generator\Consumptions;

class Csv extends \CommonBundle\Component\Document\Generator\Csv
{
    public function __construct($consumptions)
    {
        $headers = array('r-number', 'amount');

        $result = array();
        foreach ($consumptions as $consumption) {
            $result[] = array(
                $consumption->getUsername(),
                $consumption->getConsumptions(),
            );
        }

        $result[] = array(' ');

        parent::__construct($headers, $result);
    }
}

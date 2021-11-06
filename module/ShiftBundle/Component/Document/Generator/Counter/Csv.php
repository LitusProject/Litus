<?php

namespace ShiftBundle\Component\Document\Generator\Counter;

/**
 * Csv
 *
 */
class Csv extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param array $volunteers
     */
    public function __construct($volunteers)
    {
        $headers = array('First Name', 'Last Name');

        $result = array();
        foreach ($volunteers as $volunteer) {
            $result[] = array(
                $volunteer['firstName'],
                $volunteer['lastName'],
            );
        }

        $result[] = array(' ');

        parent::__construct($headers, $result);
    }
}

<?php

namespace ShopBundle\Form\Admin\Session\OpeningHour;

use DateInterval;
use DatePeriod;
use DateTime;

/**
 * Add multiple opening hours at once
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Schedule extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShopBundle\Hydrator\Session\OpeningHour';

    public function init()
    {
        parent::init();

        $days = $this->createDaysArray();

        foreach ($days as $day) {
            $this->add(
                array(
                    'type'       => 'checkbox',
                    'name'       => 'interval_10:30-18:00_' . $day->format('d/m/Y'),
                    'label'      => $day->format('l') . ' 10:30-18:00',
                    'required'   => true,
                    'attributes' => array(
                        'id'    => 'interval_10:30-18:00_' . $day->format('d/m/Y'),
                        'value' => 1,
                        'class' => 'interval select' . $day->format('d'),
                    ),
                )
            );

            $this->add(
                array(
                    'type'       => 'checkbox',
                    'name'       => 'shift_' . $day->format('d/m/Y'),
                    'label'      => 'Shifts needed',
                    'required'   => false,
                    'attributes' => array(
                        'id'    => 'shift_' . $day->format('d/m/Y'),
                        'value' => 1,
                        'class' => 'select' . $day->format('d'),
                    ),
                )
            );
        }

        $this->addSubmit('Add', 'clock_add');
    }

    /**
     * @return array
     */
    private function createDaysArray()
    {
        $dt = new DateTime();                                                           // create DateTime object with current time
        $dt->setISODate($dt->format('o'), $dt->format('W') + 1);     // set object to Monday on next week
        $periods = new DatePeriod($dt, new DateInterval('P1D'), 4);   // get all 1day periods from Monday to +6 days
        return iterator_to_array($periods);
    }
}

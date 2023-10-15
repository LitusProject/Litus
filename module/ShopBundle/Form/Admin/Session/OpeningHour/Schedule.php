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
                    'name'       => 'interval_12:30-14:00_' . $day->format('d/m/Y'),
                    'label'      => $day->format('l') . ' 12:30 - 14:00',
                    'required' => true,
                    'attributes' => array(
                        'id' => 'interval_12:30-14:00_' . $day->format('d/m/Y'),
                    ),
                )
            );

            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'volunteers_12:30-14:00_' . $day->format('d/m/Y'),
                    'label'    => 'Volunteers',
                    'attributes' => array(
                        'value' => '0',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'Int'),
                            ),
                        ),
                    ),
                )
            );

            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'volunteers-min_12:30-14:00_' . $day->format('d/m/Y'),
                    'label'    => 'Min. Volunteers',
                    'attributes' => array(
                        'value' => '0',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'Int'),
                            ),
                        ),
                    ),
                )
            );

            $this->add(
                array(
                    'type'       => 'checkbox',
                    'name'       => 'interval_18:00-19:00_' . $day->format('d/m/Y'),
                    'label'      => '18:00 - 19:00',
                    'required' => true,
                    'attributes' => array(
                        'id' => 'interval_18:00-19:00_' . $day->format('d/m/Y'),
                    ),
                )
            );

            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'volunteers_18:00-19:00_' . $day->format('d/m/Y'),
                    'label'    => 'Volunteers',
                    'attributes' => array(
                        'value' => '0',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'Int'),
                            ),
                        ),
                    ),
                )
            );

            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'volunteers-min_18:00-19:00_' . $day->format('d/m/Y'),
                    'label'    => 'Min. Volunteers',
                    'attributes' => array(
                        'value' => '0',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'Int'),
                            ),
                        ),
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
        $periods = new DatePeriod($dt, new DateInterval('P1D'), 3);   // get all 1day periods from Monday to +6 days
        $days = iterator_to_array($periods);                                           // convert DatePeriod object to array
        // $days[0] is Monday, ..., $days[3] is Thursday
        // to format selected date do: $days[1]->format('Y-m-d');

        return $days;
    }
}


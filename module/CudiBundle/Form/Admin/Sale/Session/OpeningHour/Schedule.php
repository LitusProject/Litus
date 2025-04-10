<?php

namespace CudiBundle\Form\Admin\Sale\Session\OpeningHour;

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
    protected $hydrator = 'CudiBundle\Hydrator\Sale\Session\OpeningHour';

    public function init()
    {
        parent::init();

        $days = $this->createDaysArray();

        foreach ($days as $day) {
            $this->add(
                array(
                    'type'       => 'checkbox',
                    'name'       => 'interval_noon_' . $day->format('d/m/Y'),
                    'label'      => $day->format('l') . ' 12:05 - 13:55',
                    'required'   => true,
                    'attributes' => array(
                        'class' => 'interval select1' . $day->format('d'),
                        'value' => 1,
                    ),
                )
            );

            $this->add(
                array(
                    'type'       => 'text',
                    'name'       => 'volunteers_noon_' . $day->format('d/m/Y'),
                    'label'      => 'Volunteers',
                    'attributes' => array(
                        'value' => '4',
                        'class' => 'volunteers select1' . $day->format('d'),
                    ),
                    'options'    => array(
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
                    'type'       => 'text',
                    'name'       => 'volunteers-min_noon_' . $day->format('d/m/Y'),
                    'label'      => 'Min. Volunteers',
                    'attributes' => array(
                        'value' => '3',
                        'class' => 'volunteers-min select1' . $day->format('d'),
                    ),
                    'options'    => array(
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
                    'type'       => 'text',
                    'name'       => 'nb-registered_noon_' . $day->format('d/m/Y'),
                    'label'      => 'Registries',
                    'attributes' => array(
                        'value' => '50',
                        'class' => 'registries select1' . $day->format('d'),
                    ),
                    'options'    => array(
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
                    'name'       => 'interval_evening_' . $day->format('d/m/Y'),
                    'label'      => '18:05 - 19:00',
                    'required'   => true,
                    'attributes' => array(
                        'class' => 'interval select2' . $day->format('d'),
                        'value' => 1,
                    ),
                )
            );

            $this->add(
                array(
                    'type'       => 'text',
                    'name'       => 'volunteers_evening_' . $day->format('d/m/Y'),
                    'label'      => 'Volunteers',
                    'attributes' => array(
                        'value' => '4',
                        'class' => 'volunteers select2' . $day->format('d'),
                    ),
                    'options'    => array(
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
                    'type'       => 'text',
                    'name'       => 'volunteers-min_evening_' . $day->format('d/m/Y'),
                    'label'      => 'Min. Volunteers',
                    'attributes' => array(
                        'value' => '3',
                        'class' => 'volunteers-min select2' . $day->format('d'),
                    ),
                    'options'    => array(
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
                    'type'       => 'text',
                    'name'       => 'nb-registered_evening_' . $day->format('d/m/Y'),
                    'label'      => 'Registries',
                    'attributes' => array(
                        'value' => '50',
                        'class' => 'registries select2' . $day->format('d'),
                    ),
                    'options'    => array(
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
        return iterator_to_array($periods);
    }
}

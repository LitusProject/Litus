<?php

namespace CalendarBundle\Form\Admin\Event;

use CalendarBundle\Entity\Node\Event as EventEntity;
use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add an event.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'CalendarBundle\Hydrator\Node\Event';

    /**
     * @var \CalendarBundle\Entity\Node\Event
     */
    private $event;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'datetime',
                'name'       => 'start_date',
                'label'      => 'Start Date',
                'required'   => true,
                'attributes' => array(
                    'placeholder' => 'dd/mm/yyyy hh:mm',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'datetime',
                'name'       => 'end_date',
                'label'      => 'End Date',
                'required'   => false,
                'attributes' => array(
                    'placeholder' => 'dd/mm/yyyy hh:mm',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'is_hidden',
                'label' => 'Is Hidden',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'is_career',
                'label' => 'Is Career',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'is_eerstejaars',
                'label' => 'Is Eerstejaars',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'is_international',
                'label' => 'Is International',
            )
        );

        $this->addSubmit('Add', 'calendar_add');

        if ($this->getEvent() !== null) {
            $this->bind($this->getEvent());
        }
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'title',
                'label'    => 'Title',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'EventName',
                                'options' => array(
                                    'event' => $this->getEvent(),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'location',
                'label'    => 'Location',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $container->add(
            array(
                'type'     => 'textarea',
                'name'     => 'content',
                'label'    => 'Content',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * @param \CalendarBundle\Entity\Node\Event
     * @return \CalendarBundle\Form\Admin\Event\Add
     */
    public function setEvent(EventEntity $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return \CalendarBundle\Entity\Node\Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}

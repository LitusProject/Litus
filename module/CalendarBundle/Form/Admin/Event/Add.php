<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Form\Admin\Event;

use CalendarBundle\Component\Validator\Name as EventNameValidator,
    CalendarBundle\Entity\Node\Event as EventEntity,
    CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CommonBundle\Entity\General\Language,
    Zend\Form\FormInterface;

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
    private $_event;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'start_date',
            'label'      => 'Start Date',
            'required'   => true,
            'attributes'  => array(
                'placeholder'     => 'dd/mm/yyyy hh:mm',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'end_date',
            'label'      => 'End Date',
            'required'   => true,
            'attributes'  => array(
                'placeholder'     => 'dd/mm/yyyy hh:mm',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new DateCompareValidator('start_date', 'd/m/Y H:i'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'calendar_add');

        if (null !== $this->getEvent())
            $this->bind($this->getEvent());
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
            'type'       => 'text',
            'name'       => 'title',
            'label'      => 'Title',
            'required'   => $isDefault,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new EventNameValidator($this->getEntityManager(), $language, $this->getEvent()),
                    ),
                ),
            ),
        ));

        $container->add(array(
            'type'       => 'text',
            'name'       => 'location',
            'label'      => 'Location',
            'required'   => $isDefault,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $container->add(array(
            'type'       => 'textarea',
            'name'       => 'content',
            'label'      => 'Content',
            'required'   => $isDefault,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }

    /**
     * @param \CalendarBundle\Entity\Node\Event
     * @return \CalendarBundle\Form\Admin\Event\Add
     */
    public function setEvent(EventEntity $event)
    {
        $this->_event = $event;

        return $this;
    }

    /**
     * @return \CalendarBundle\Entity\Node\Event
     */
    public function getEvent()
    {
        return $this->_event;
    }
}

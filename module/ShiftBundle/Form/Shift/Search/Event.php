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

namespace ShiftBundle\Form\Shift\Search;

use CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Entity\General\Language,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Search Event
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Event extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\Language $language The language
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Language $language, $name = null)
    {
        parent::__construct($name, false, false);

        $this->_entityManager = $entityManager;

        $this->setAttribute('class', 'form-inline');

        $field = new Select('event');
        $field->setAttribute('options', $this->_createEventsArray($language));
        $this->add($field);
    }

    private function _createEventsArray(Language $language)
    {
        $events = $this->_entityManager
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $eventsArray = array(
            '' => ''
        );
        foreach ($events as $event)
            $eventsArray[$event->getId()] = $event->getTitle($language);

        return $eventsArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'event',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}

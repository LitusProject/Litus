<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\QueryBuilder,
    CalendarBundle\Component\Validator\Name as EventNameValidator,
    CalendarBundle\Entity\Nodes\Event,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit an event.
 */
class Edit extends Add
{
    /**
     * @var \CalendarBundle\Entity\Nodes\Event
     */
    protected $_event;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Event $event, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $this->_event = $event;

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'calendar_edit');
        $this->add($field);

        $this->_populateFromEvent($event);
    }

    private function _populateFromEvent(Event $event)
    {
        $data = array(
            'start_date' => $event->getStartDate()->format('d/m/Y H:i'),
        );
        if ($event->getEndDate())
            $data['end_date'] = $event->getEndDate()->format('d/m/Y H:i');

        foreach($this->getLanguages() as $language) {
            $data['location_' . $language->getAbbrev()] = $event->getLocation($language, false);
            $data['title_' . $language->getAbbrev()] = $event->getTitle($language, false);
            $data['content_' . $language->getAbbrev()] = $event->getContent($language, false);
        }
        $this->setData($data);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = parent::getInputFilter();
            $factory = new InputFactory();

            foreach($this->getLanguages() as $language) {
                $inputFilter->remove('title_' . $language->getAbbrev());
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'title_' . $language->getAbbrev(),
                            'required' => $language->getAbbrev() == \Locale::getDefault(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new EventNameValidator($this->_entityManager, $language, $this->_event),
                            ),
                        )
                    )
                );
            }
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}

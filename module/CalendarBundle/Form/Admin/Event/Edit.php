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

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\QueryBuilder,
    CalendarBundle\Component\Validator\Name as EventNameValidator,
    CalendarBundle\Entity\Node\Event,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit an event.
 */
class Edit extends Add
{
    /**
     * @var \CalendarBundle\Entity\Node\Event
     */
    protected $_event;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Event $event, $name = null)
    {
        parent::__construct($entityManager, $name);

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

        return $inputFilter;
    }
}

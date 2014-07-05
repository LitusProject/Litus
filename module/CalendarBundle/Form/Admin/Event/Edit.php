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
    CalendarBundle\Entity\Node\Event;

/**
 * Edit an event.
 */
class Edit extends Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit');
        $this->addSubmit('Save', 'calendar_edit');
    }

    /*public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        foreach ($this->getLanguages() as $language) {
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
    }*/
}

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

namespace SyllabusBundle\Form\Admin\Group\Study;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    Zend\Form\Element\Submit,
    Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

/**
 * Add Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($studies, $name = null)
    {
        parent::__construct($name);

        $studyNames = array();
        foreach ($studies as $study) {
            $studyNames[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getFullTitle();
        }

        if (0 != count($studyNames)) {
            $field = new Select('studies');
            $field->setLabel('Studies')
                ->setAttribute('multiple', true)
                ->setAttribute('style', 'max-width: 100%;')
                ->setAttribute('options', $studyNames);
            $this->add($field);
        }

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        return $inputFilter;
    }
}

<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Admin\Section;

use BrBundle\Entity\Contract\Section,
    BrBundle\Component\Validator\SectionName as SectionNameValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit a section.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends Add
{

    /**
     * @var \BrBundle\Entity\Contract\Section
     */
    private $_section;

    public function __construct(EntityManager $entityManager, Section $section, $options = null)
    {
        parent::__construct($entityManager, $options);

        $this->_section = $section;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'sections_edit');
        $this->add($field);

        $this->populateFromSection($section);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('name');
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new SectionNameValidator($this->_entityManager, $this->_section),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}

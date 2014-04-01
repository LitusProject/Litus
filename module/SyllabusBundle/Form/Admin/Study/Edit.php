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

namespace SyllabusBundle\Form\Admin\Study;

use Doctrine\ORM\EntityManager,
    SyllabusBundle\Component\Validator\Study\KulId as KulIdValidator,
    SyllabusBundle\Component\Validator\Study\Recursion as RecursionValidator,
    SyllabusBundle\Entity\Study,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var Study
     */
    private $_study = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Study           $study         The study we're going to modify
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Study $study, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_study = $study;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromStudy($study);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('kul_id');
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'kul_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new KulIdValidator($this->_entityManager, $this->_study),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'parent',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new RecursionValidator($this->_entityManager, $this->_study),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}

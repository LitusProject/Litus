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

use CommonBundle\Component\OldForm\Bootstrap\Element\Text,
    CommonBundle\Component\OldForm\Bootstrap\Element\Submit,
    DateTime,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Search Date
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Date extends \CommonBundle\Component\OldForm\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name, false, false);

        $this->_entityManager = $entityManager;

        $this->setAttribute('class', 'form-inline');

        $today = new DateTime();
        $field = new Text('date');
        $field->setAttribute('placeholder', 'dd/mm/yyyy')
            ->setValue($today->format('d/m/Y'));
        $this->add($field);
    }

    private function _createUnitsArray()
    {
        $units = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActive();

        $unitsArray = array(
            '' => ''
        );
        foreach ($units as $unit)
            $unitsArray[$unit->getId()] = $unit->getName();

        return $unitsArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $factory->createInput(
            array(
                'name'     => 'date',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'date',
                        'options' => array(
                            'format' => 'd/m/Y',
                        ),
                    ),
                ),
            )
        );

        return $inputFilter;
    }
}

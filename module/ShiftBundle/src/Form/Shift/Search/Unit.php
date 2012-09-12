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

namespace ShiftBundle\Form\Shift\Search;

use CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Search Unit
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Unit extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name, false, false);

        $this->_entityManager = $entityManager;

        $field = new Select('unit');
        $field->setAttribute('options', $this->_createUnitsArray());
        $this->add($field);

        $field = new Submit('search');
        $field->setValue('Search')
            ->setAttribute('class', 'btn');
        $this->add($field);
    }

    private function _createUnitsArray()
    {
        $units = $this->_entityManager
            ->getRepository('ShiftBundle\Entity\Unit')
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
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}

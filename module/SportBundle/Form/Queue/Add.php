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

namespace SportBundle\Form\Queue;

use CommonBundle\Component\OldForm\Bootstrap\Element\Collection,
    CommonBundle\Component\OldForm\Bootstrap\Element\Text,
    CommonBundle\Component\OldForm\Bootstrap\Element\Select,
    CommonBundle\Component\OldForm\Bootstrap\Element\Submit,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add a runner to the queue.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Bootstrap\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param EntityManager   $entityManager
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $information = new Collection('information');
        $information->setLabel('Information')
            ->setAttribute('id', 'information');
        $this->add($information);

        $field = new Text('university_identification');
        $field->setLabel('University Identification')
            ->setAttribute('autocomplete', 'off');
        $information->add($field);

        $field = new Text('first_name');
        $field->setLabel('First Name')
            ->setRequired()
            ->setAttribute('autocomplete', 'off');
        $information->add($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
            ->setRequired()
            ->setAttribute('autocomplete', 'off');
        $information->add($field);

        $field = new Select('department');
        $field->setLabel('Department')
            ->setRequired(true)
            ->setAttribute('options', $this->_getDepartments());
        $information->add($field);

        $field = new Submit('queue');
        $field->setValue('Queue');
        $this->add($field);
    }

    private function _getDepartments()
    {
        $departments = $this->_entityManager
            ->getRepository('SportBundle\Entity\Department')
            ->findAll();

        $array = array('0' => '');
        foreach($departments as $department)
            $array[$department->getId()] = $department->getName();

        return $array;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'university_identification',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'first_name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'last_name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}

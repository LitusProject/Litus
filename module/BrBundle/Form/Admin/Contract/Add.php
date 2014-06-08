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

namespace BrBundle\Form\Admin\Contract;

use BrBundle\Entity\Contract\Section,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit,
    Zend\Validator\Float as FloatValidator;

/**
 * Add Contract
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $options = null)
    {
        parent::__construct($options);

        $this->_entityManager = $entityManager;

        $field = new Select('company');
        $field->setLabel('Company')
            ->setRequired(true)
            ->setAttribute('options', $this->_getCompanies());
        $this->add($field);

        $field = new Text('discount');
        $field->setLabel('Discount Percentage')
            ->setRequired(true)
            ->setValue('0');
        $this->add($field);

        $field = new Text('title');
        $field->setLabel('Contract Title')
            ->setRequired(true);
        $this->add($field);

        $field = new Select('sections');
        $field->setLabel('Sections')
            ->setRequired(true)
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->_getSections());
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'contracts_add');
        $this->add($field);
    }

    private function populateFromContract(Contract $contract)
    {
        $formData = array(
            'title' => $title,
        );

        $this->setData($formData);
    }

    private function _getCompanies()
    {
        $companies = $this->_entityManager
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        $companiesArray = array();
        foreach ($companies as $company)
            $companiesArray[$company->getId()] = $company->getName();

        return $companiesArray;
    }

    private function _getSections()
    {
        $sections = $this->_entityManager
            ->getRepository('BrBundle\Entity\Contract\Section')
            ->findAll();

        $sectionsArray = array();
        foreach ($sections as $section)
            $sectionsArray[$section->getId()] = $section->getName();

        return $sectionsArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'company',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'discount',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                        array(
                            'name' => 'between',
                            'options' => array(
                                'min' => 0,
                                'max' => 100,
                            )
                        )
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'title',
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
                    'name'     => 'sections',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}

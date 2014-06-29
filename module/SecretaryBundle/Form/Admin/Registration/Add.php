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

namespace SecretaryBundle\Form\Admin\Registration;

use CommonBundle\Component\OldForm\Admin\Element\Checkbox,
    CommonBundle\Component\OldForm\Admin\Element\Hidden,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Component\OldForm\Admin\Element\Select,
    Doctrine\ORM\EntityManager,
    SecretaryBundle\Entity\Registration,
    SecretaryBundle\Entity\Organization\MetaData,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Hidden('person_id');
        $field->setAttribute('id', 'personId');
        $this->add($field);

        $field = new Text('person');
        $field->setLabel('Person')
            ->setAttribute('style', 'width: 400px;')
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('payed');
        $field->setLabel('Has Payed');
        $this->add($field);

        $field = new Checkbox('irreeel');
        $field->setLabel('Ir.Reëel at CuDi');
        $this->add($field);

        $field = new Checkbox('bakske');
        $field->setLabel('Bakske by E-mail');
        $this->add($field);

        $field = new Select('tshirt_size');
        $field->setLabel('T-shirt Size')
            ->setAttribute(
                'options',
                MetaData::$possibleSizes
            );
        $this->add($field);

        $field = new Select('organization');
        $field->setLabel('Organization')
            ->setAttribute('options', $this->_getOrganizations());
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'secretary_edit');
        $this->add($field);
    }

    private function _getOrganizations()
    {
        $organizations = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationOptions = array();
        foreach($organizations as $organization)
            $organizationOptions[$organization->getId()] = $organization->getName();

        return $organizationOptions;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'payed',
                    'required' => false,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'person_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'person',
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
                    'name'     => 'organization',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}

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

namespace CommonBundle\Form\Admin\Person;

use CommonBundle\Component\Form\Admin\Element\Password,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\PhoneNumber as PhonenumberValidator,
    CommonBundle\Component\Validator\Username as UsernameValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Person
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('username');
        $field->setLabel('Username')
            ->setRequired();
        $this->add($field);

        $field = new Text('first_name');
        $field->setLabel('First Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('email');
        $field->setLabel('E-mail')
            ->setRequired();
        $this->add($field);

        $field = new Text('phone_number');
        $field->setLabel('Phone Number')
            ->setAttribute('placeholder', '+CCAAANNNNNN');
        $this->add($field);

        $field = new Select('sex');
        $field->setLabel('Sex')
            ->setRequired()
            ->setAttribute(
                'options',
                array(
                    'm' => 'M',
                    'f' => 'F'
                )
            );
        $this->add($field);

        $field = new Select('roles');
        $field->setLabel('Groups')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->createRolesArray());
        $this->add($field);
    }

    /**
     * Returns an array that has all the roles, so that they are available in the
     * roles multiselect.
     *
     * @return array
     */
    protected function createRolesArray()
    {
        $roles = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem())
                continue;

            $rolesArray[$role->getName()] = $role->getName();
        }

        if (empty($rolesArray))
            throw new \RuntimeException('There needs to be at least one role before you can add a person');

        return $rolesArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'username',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'alnum'
                        ),
                        new UsernameValidator($this->_entityManager),
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

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'email',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'EmailAddress',
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'phone_number',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PhoneNumberValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'sex',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}

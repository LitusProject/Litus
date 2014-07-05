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

namespace SyllabusBundle\Form\Admin\Group;

use CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Component\OldForm\Admin\Element\Textarea,
    CommonBundle\Component\OldForm\Admin\Element\Checkbox,
    Doctrine\ORM\EntityManager,
    MailBundle\Component\Validator\MultiMail as MultiMailValidator,
    SyllabusBundle\Component\Validator\Group\Name as NameValidator,
    SyllabusBundle\Entity\Group,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setAttribute('size', 70)
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('cvbook');
        $field->setLabel('Show in CV Book');
        $this->add($field);

        $field = new Textarea('extra_members');
        $field->setLabel('Extra Members');
        $this->add($field);

        $field = new Textarea('excluded_members');
        $field->setLabel('Excluded Members');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    protected function populateFromGroup(Group $group)
    {
        $extraMembers = unserialize($group->getExtraMembers());
        $excludedMembers = unserialize($group->getExcludedMembers());

        $this->setData(
            array(
                'name' => $group->getName(),
                'cvbook' => $group->getCvBook(),
                'extra_members' => $extraMembers ? implode(',', $extraMembers) : '',
                'excluded_members' => $excludedMembers ? implode(',', $excludedMembers) : '',
            )
        );
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new NameValidator($this->_entityManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'extra_members',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new MultiMailValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'excluded_members',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new MultiMailValidator(),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}

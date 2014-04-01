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

namespace FormBundle\Form\Admin\Group;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Node\Group,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Group           $group         The group we're going to edit
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Group $group, $name = null)
    {
        parent::__construct($entityManager, $name);

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('active');
        $field->setLabel('Active')
            ->setAttribute('class', 'form doodle');
        $this->add($field);

        $field = new Text('max');
        $field->setLabel('Total Max Entries')
            ->setAttribute('class', 'form');
        $this->add($field);

        $field = new Checkbox('non_members');
        $field->setLabel('Allow Entry Without Login')
            ->setAttribute('class', 'form doodle');
        $this->add($field);

        $field = new Checkbox('editable_by_user');
        $field->setLabel('Allow Users To Edit Their Info')
            ->setAttribute('class', 'form doodle');
        $this->add($field);

        $this->remove('start_form');

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'form_edit');
        $this->add($field);

        $this->_populateFromGroup($group);
    }

    private function _populateFromGroup(Group $group)
    {
        $data = array(
            'start_date'       => $group->getStartDate()->format('d/m/Y H:i'),
            'end_date'         => $group->getEndDate()->format('d/m/Y H:i'),
            'active'           => $group->isActive(),
            'max'              => $group->getMax(),
            'editable_by_user' => $group->isEditableByUser(),
            'non_members'      => $group->isNonMember(),
        );

        foreach ($this->getLanguages() as $language) {
            $data['title_' . $language->getAbbrev()] = $group->getTitle($language, false);
            $data['introduction_' . $language->getAbbrev()] = $group->getIntroduction($language, false);
        }

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('start_form');

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'start_date',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'end_date',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        new DateCompareValidator('start_date', 'd/m/Y H:i'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'max',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits',
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}

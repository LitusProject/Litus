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

namespace CommonBundle\Form\Admin\Academic;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Entity\Users\Statuses\University,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Checkbox,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Validator\Alnum as AlnumValidator;

/**
 * Add Academic
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Form\Admin\Person\Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The form's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($entityManager, $opts);

        $field = new Checkbox('activation_code');
        $field->setLabel('Activation Code')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('university_identification');
        $field->setLabel('Identification')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new AlnumValidator());
        $this->addElement($field);

        $field = new Select('university_status');
        $field->setLabel('Status')
            ->setRequired()
            ->setMultiOptions(University::$possibleStatuses)
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $this->addDisplayGroup(
            array(
                'university_identification',
                'university_status'
            ),
            'academic_form'
        );
        $this->getDisplayGroup('academic_form')
               ->setLegend('University')
            ->setAttrib('id', 'academic_form')
            ->removeDecorator('DtDdWrapper');

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'academic_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}

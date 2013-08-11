<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace TicketBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Price as PriceValidator,
    Doctrine\ORM\EntityManager,
    Ticketbundle\Entity\Event,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilterProviderInterface,
    Zend\Form\Fieldset,
    Zend\Form\Element\Submit;

/**
 * Add Option
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Option extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('option');

        $this->setLabel('Option');

        $field = new Hidden('option_id');
        $this->add($field);

        $field = new Text('option');
        $field->setLabel('Name')
            ->setRequired(true);
        $this->add($field);

        $field = new Text('price_members');
        $field->setLabel('Price Members');
        $this->add($field);

        $field = new Text('price_non_members');
        $field->setLabel('Price Non Members')
            ->setAttribute('class', $field->getAttribute('class') . ' price_non_members');
        $this->add($field);
    }

    public function getInputFilterSpecification()
    {
        $required = isset($_POST['options'][$this->getName()]['option']) && strlen($_POST['options'][$this->getName()]['option']) > 0 ? true : false;

        return array(
            array(
                'name'     => 'option_id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'int',
                    )
                ),
            ),
            array(
                'name'     => 'option',
                'required' => $required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            array(
                'name'     => 'price_members',
                'required' => $required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    new PriceValidator()
                ),
            ),
            array(
                'name'     => 'price_non_members',
                'required' => isset($_POST['only_members']) && $_POST['only_members'] ? false : $required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    new PriceValidator()
                ),
            ),
        );
    }
}

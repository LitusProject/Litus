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

namespace TicketBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Fieldset,
    CommonBundle\Component\Validator\Price as PriceValidator,
    Zend\InputFilter\InputFilterProviderInterface;

/**
 * Add Option
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Option extends Fieldset implements InputFilterProviderInterface
{
    public function init()
    {
        parent::init();

        $this->setLabel('Option');

        $this->add(array(
            'type' => 'hidden',
            'name' => 'option_id',
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'option',
            'label'      => 'Name',
            'required'   => true,
        ));

        $this->add(array(
            'type'  => 'text',
            'name'  => 'price_members',
            'label' => 'Price Members',
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'price_non_members',
            'label'      => 'Price Non Members',
            'attributes' => array(
                'class', 'price_non_members'
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        $required = $this->get('option')->getValue() && strlen($this->get('option')->getValue()) > 0 ? true : false;

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

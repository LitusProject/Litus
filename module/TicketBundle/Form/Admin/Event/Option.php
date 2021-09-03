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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace TicketBundle\Form\Admin\Event;

use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Add Option
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Option extends \CommonBundle\Component\Form\Fieldset implements InputFilterProviderInterface
{
    public function init()
    {
        parent::init();

        $this->setLabel('Option');

        $this->add(
            array(
                'type'     => 'hidden',
                'name'     => 'option_id',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'option',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'price_members',
                'label'    => 'Price Members',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'price_non_members',
                'label'      => 'Price Non Members',
                'required'   => true,
                'attributes' => array(
                    'class' => 'price_non_members',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'maximum',
                'label'      => 'Maximum amount of tickets',
                'required'   => true,
                'attributes' => array(
                    'class' => 'maximum',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                            array('name' => 'PositiveNumber'),
                        ),
                    ),
                ),
            )
        );
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $required = $this->get('option')->getValue() && strlen($this->get('option')->getValue()) > 0;

        $specs['option']['required'] = $required;
        $specs['price_members']['required'] = $required;
        $specs['price_non_members']['required'] = isset($_POST['only_members']) && $_POST['only_members'] ? false : $required;

        return $specs;
    }
}

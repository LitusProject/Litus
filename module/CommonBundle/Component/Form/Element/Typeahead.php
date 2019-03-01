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

namespace CommonBundle\Component\Form\Element;

/**
 * Typeahead form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Typeahead extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        $this->add(
            array(
                'type'     => 'hidden',
                'name'     => 'id',
                'required' => true,
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
                'type'       => 'text',
                'name'       => 'value',
                'required'   => true,
                'attributes' => array(
                    'autocomplete' => 'off',
                    'data-provide' => 'typeahead',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    public function setAttribute($name, $value)
    {
        if ($name == 'name') {
            parent::setAttribute($name, $value);
        } else {
            if ($this->has('value')) {
                $this->get('value')->setAttribute($name, $value);
            }
        }

        return $this;
    }

    public function setLabel($label)
    {
        $this->get('value')->setLabel($label);

        return $this;
    }

    public function getValue()
    {
        return $this->get('id')->getValue();
    }
}

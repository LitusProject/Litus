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

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\Fieldset;

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
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'value',
                'required'   => false,
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

    public function getValue()
    {
        return $this->get('id')->getValue();
    }

    public function setLabel($label)
    {
        $this->get('value')->setLabel($label);

        return $this;
    }

    public function setAttribute($name, $value)
    {
        if ($name == 'name') {
            parent::setAttribute($name, $value);
        } else {
            $this->get('value')->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Specifies whether this element is a required field.
     *
     * Also sets the HTML5 'required' attribute.
     *
     * @param  boolean $flag
     * @return self
     */
    public function setRequired($flag = true)
    {
        parent::setRequired($flag);

        $field = $this->get('value');
        if (!($field instanceof Fieldset)) {
            return $this;
        }

        $labelAttributes = $field->getLabelAttributes() ?: array();
        if (isset($labelAttributes['class'])) {
            $labelAttributes['class'] .= ' ' . ($flag ? 'required' : 'optional');
        } else {
            $labelAttributes['class'] = ($flag ? 'required' : 'optional');
        }
        $field->setLabelAttributes($labelAttributes);

        return $this;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs['id']['required'] = $this->isRequired();
        $specs['value']['required'] = $this->isRequired();

        if (isset($this->options['input'])) {
            $specs['value'] = array_merge($this->options['input'], $specs['value']);
            unset($this->options['input']);
        }

        return $specs;
    }
}

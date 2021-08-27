<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

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
                'required' => true,
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
            if ($this->has('value')) {
                $this->get('value')->setAttribute($name, $value);
            }
        }

        return $this;
    }

    public function setRequired($flag = true)
    {
        parent::setRequired($flag);

        $field = $this->get('value');
        if (!($field instanceof Fieldset)) {
            return $this;
        }

        $labelAttributes = $field->getLabelAttributes() ?: array();
        if (isset($labelAttributes['class'])) {
            if (strpos($labelAttributes['class'], 'required') === false) {
                $labelAttributes['class'] .= ' ' . ($flag ? 'required' : 'optional');
            }
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

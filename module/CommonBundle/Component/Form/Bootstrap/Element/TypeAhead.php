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

namespace CommonBundle\Component\Form\Bootstrap\Element;

use Traversable,
    Zend\InputFilter\InputFilterProviderInterface,
    Zend\Stdlib\ArrayUtils;

class TypeAhead extends \CommonBundle\Component\Form\Fieldset implements InputFilterProviderInterface
{
    public function init()
    {
        $this->add(array(
            'type'       => 'hidden',
            'name'       => $this->getName() . '_id',
            'required'   => true,
            'attributes' => array(
                'id' => $this->getName() . 'Id',
            ),
            'options'    => array(
                'input' => array(
                    'filters'    => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => $this->getName(),
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'data-provide' => 'typeahead',
                'id'           => $this->getName() . 'Search',
            ),
            'options'    => array(
                'input' => array(
                    'filters'    => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }

    public function setName($name)
    {
        if (empty(parent::getName())) {
            $field = $this->get($this->getName() . '_id')->setName($name . '_id')
                ->setAttribute('id', $name . 'Id');
            unset($this->byName[$this->getName() . '_id']);
            $this->byName[$name . '_id'] = $field;

            $field = $this->get($this->getName())->setName($name)
                ->setAttribute('id', $name . 'Search');
            unset($this->byName[$this->getName()]);
            $this->byName[$name ] = $field;
        }

        $this->setAttribute('name', $name);

        return $this;
    }

    public function getValue()
    {
        return $this->get($this->getName() . '_id')->getValue();
    }

    public function getName()
    {
        $name = parent::getName();

        if (empty($name)) {
            return 'typeahead';
        }

        return $name;
    }

    public function setLabel($label)
    {
        $this->get($this->getName())->setLabel($label);

        return $this;
    }

    public function setAttribute($name, $value)
    {
        if ('name' == $name) {
            parent::setAttribute($name, $value);
        } else {
            $this->get($this->getName())->setAttribute($name, $value);
        }

        return $this;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs[$this->getName() . '_id']['required'] = $this->isRequired();
        $specs[$this->getName()]['required'] = $this->isRequired();

        if (isset($this->options['input'])) {
            $specs[$this->getName()] = array_merge($this->options['input'], $specs[$this->getName()]);
            unset($this->options['input']);
        }

        return $specs;
    }

    /*public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }

        if (isset($options['input'])) {
            $this->get($this->getName())
            unset($options['input']);
        }

        return parent::setOptions($options);
    }*/
}

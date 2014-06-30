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

namespace CommonBundle\Component\Form\Admin\Element;

use RuntimeException,
    Zend\InputFilter\InputFilterProviderInterface;

class TypeAhead extends \CommonBundle\Component\Form\Fieldset implements InputFilterProviderInterface
{
    /**
     * @var string The name of the typeahead
     */
    private $name;

    public function init()
    {
        if (null === $this->name)
            throw new RuntimeException('Cannot create typeahead without name');

        $this->add(array(
            'type'       => 'hidden',
            'name'       => $this->name . '_id',
            'attributes' => array(
                'id' => $this->name . 'Id',
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => $this->name,
            'attributes' => array(
                'autocomplete' => 'off',
                'data-provide' => 'typeahead',
                'id'           => $this->name . 'Search',
            ),
        ));

        $this->setRequired();
    }

    /**
     * @param  string $name The name of the typeahead
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAttribute($name, $value)
    {
        $this->get($this->name)->setAttribute($name, $value);

        return $this;
    }

    public function setTypeAheadAttribute($name, $value)
    {
        return parent::setAttribute($name, $value);
    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name'       => $this->name . '_id',
                'required'   => $this->isRequired(),
                'filters'    => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'int'),
                ),
            ),
            array(
                'name'     => $this->name,
                'required' => $this->isRequired(),
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ),
        );
    }

    public function populateValues($data)
    {
        if (is_string($data))
            return;

        if (array_key_exists($this->name, $data))
            $this->get($this->name)
                ->setValue($data[$this->name]);

        if (array_key_exists($this->name . '_id', $data))
            $this->get($this->name . '_id')
                ->setValue($data[$this->name . '_id']);
    }
}

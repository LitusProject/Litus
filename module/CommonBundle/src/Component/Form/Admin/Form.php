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

namespace CommonBundle\Component\Form\Admin;

use Zend\InputFilter\InputFilterAwareInterface;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Form extends \Zend\Form\Form implements InputFilterAwareInterface
{
    /**
     * @var \Zend\InputFilter\InputFilter
     */
    protected $_inputFilter;

    /**
     * @param mixed $options The form's options
     */
    public function __construct($options)
    {
        parent::__construct($options = null);

        $this->setAttribute('method', 'post')
            ->setAttribute('class', 'form')
            ->setAttribute('novalidate', true);
    }

    /**
     * Set data to validate and/or populate elements
     *
     * Typically, also passes data on to the composed input filter.
     *
     * @param  array|\ArrayAccess|\Traversable $data
     * @return Form|FormInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setData($data)
    {
        parent::setData($data);

        $this->setInputFilter($this->getInputFilter());
    }

    /**
     * Set a hash of element names/messages to use when validation fails
     *
     * @param  array|Traversable $messages
     * @return Element|ElementInterface|FieldsetInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setMessages($messages)
    {
        parent::setMessages($messages);

        $fieldsets = $this->getFieldsets();
        foreach($fieldsets as $fieldset) {
            $fieldset->setMessages($messages);
        }

        return $this;
    }

    /**
     * Recursively populate values of attached elements and fieldsets
     *
     * @param  array|Traversable $data
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function populateValues($data)
    {
        parent::populateValues($data);

        $fieldsets = $this->getFieldsets();
        foreach($fieldsets as $fieldset) {
            $fieldset->populateValues($data);
        }

        return $this;
    }
}

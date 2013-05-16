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

namespace CommonBundle\Component\Form\Bootstrap;

use Zend\Form\Element\Csrf,
    Zend\InputFilter\InputFilterAwareInterface;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Form extends \Zend\Form\Form implements InputFilterAwareInterface
{
    /**
     * @var boolean Whether or not to show the form-actions div
     */
    private $_displayFormActions;

    /**
     * @param null|string|int $name Optional name for the element
     * @param boolean $horizontal Whether to display the form horizontically or vertically
     * @param boolean $displayFormActions Whether or not to show the form-actions div
     */
    public function __construct($name = null, $horizontal = true, $displayFormActions = true)
    {
        parent::__construct($name);

        $this->_displayFormActions = $displayFormActions;

        $this->setAttribute('method', 'post')
            ->setAttribute('novalidate', true);

        if ($horizontal)
            $this->setAttribute('class', 'form-horizontal');

        $this->add(
            new Csrf('csrf')
        );
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

    /**
     * Whether or not to show the form-actions div
     *
     * @return boolean
     */
    public function getDisplayFormActions()
    {
        return $this->_displayFormActions;
    }

    /**
     * Return the form validated data, combined with post data
     *
     * @return object
     */
    public function getFormData($formData)
    {
        foreach($this->getData() as $key => $value) {
            if (null !== $value)
                $formData->{$key} = $value;
        }
        return $formData;
    }
}

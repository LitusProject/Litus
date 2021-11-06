<?php

namespace CommonBundle\Component\Form;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Laminas\Form\FormInterface;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

class Collection extends \Laminas\Form\Element\Collection implements FieldsetInterface, ServiceLocatorAwareInterface
{
    use ElementTrait {
        ElementTrait::setRequired as setElementRequired;
    }
    use FieldsetTrait {
        FieldsetTrait::setRequired insteadof ElementTrait;
    }

    use ServiceLocatorAwareTrait;

    public function showAs()
    {
        return 'fieldset';
    }

    public function setName($name)
    {
        $this->setAttribute('id', $name);

        return parent::setName($name);
    }

    /**
     * Set a hash of element names/messages to use when validation fails
     *
     * @param  array|\Traversable $messages
     * @return Collection
     * @throws Exception\InvalidArgumentException
     */
    public function setMessages($messages)
    {
        parent::setMessages($messages);

        $fieldsets = $this->getFieldsets();
        foreach ($fieldsets as $fieldset) {
            $fieldset->setMessages($messages);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function extract()
    {
        if ($this->object instanceof Traversable) {
            $this->object = (object) ArrayUtils::iteratorToArray($this->object, false);
        }

        if (!is_array($this->object)) {
            return array();
        }

        return $this->object;
    }

    /**
     * Replaces the default template object of a sub element with the corresponding
     * real entity so that all properties are preserved.
     *
     * @return void
     */
    protected function replaceTemplateObjects()
    {
    }

    /**
     * Checks if this fieldset can bind data
     *
     * @return boolean
     */
    public function allowValueBinding()
    {
        return false;
    }

    /**
     * Ensures state is ready for use. Here, we append the name of the fieldsets to every elements in order to avoid
     * name clashes if the same fieldset is used multiple times
     *
     * @param  FormInterface $form
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
        parent::prepareElement($form);
    }
}

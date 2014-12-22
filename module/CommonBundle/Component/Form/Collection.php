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

namespace CommonBundle\Component\Form;



use Traversable,
    Zend\Form\FormInterface,
    Zend\Stdlib\ArrayUtils;

class Collection extends \Zend\Form\Element\Collection implements FieldsetInterface, \CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface
{
    use ElementTrait, FieldsetTrait {
        FieldsetTrait::setRequired insteadof ElementTrait;
        ElementTrait::setRequired as setElementRequired;
    }

    use \CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

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
     * @param  array|\Traversable                 $messages
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
            $this->object = ArrayUtils::iteratorToArray($this->object, false);
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
     * @return bool
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

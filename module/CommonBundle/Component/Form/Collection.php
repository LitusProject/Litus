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

class Collection extends \Zend\Form\Element\Collection implements FieldsetInterface, \CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface
{
    use ElementTrait;
    use FieldsetTrait;

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
     * @param  array|\Traversable                         $messages
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
}

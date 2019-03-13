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

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Component\Form\FieldsetTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Collection form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Collection extends \Zend\Form\Element\Collection implements FieldsetInterface, ServiceLocatorAwareInterface
{
    use FieldsetTrait;
    use ServiceLocatorAwareTrait;

    /**
     * Set a hash of element names/messages to use when validation fails
     *
     * @param  array|\Traversable $messages
     * @return Collection
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
     * @return boolean
     */
    public function allowValueBinding()
    {
        return false;
    }
}

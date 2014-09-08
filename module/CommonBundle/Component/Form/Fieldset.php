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

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Extending Zend's fieldset component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Fieldset extends \Zend\Form\Fieldset implements FieldsetInterface, \CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface
{
    use ElementTrait, FieldsetTrait {
        FieldsetTrait::setRequired insteadof ElementTrait;
        ElementTrait::setRequired as setElementRequired;
    }

    use \CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    /**
     * @param string $name
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setHydrator(new ClassMethodsHydrator());
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->setAttribute('id', $name);

        return parent::setName($name);
    }

    public function showAs()
    {
        return 'fieldset';
    }
}

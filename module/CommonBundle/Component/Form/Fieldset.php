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

namespace CommonBundle\Component\Form;

use Zend\Form\FormInterface,
    Zend\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Extending Zend's fieldset component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @method \CommonBundle\Component\Form\FieldsetTrait setRequired(boolean $flag = true)
 * @method \CommonBundle\Component\Form\ElementTrait setElementRequired(boolean $flag = true)
 */
class Fieldset extends \Zend\Form\Fieldset implements FieldsetInterface
{
    use ElementTrait, FieldsetTrait {
        FieldsetTrait::setRequired insteadof ElementTrait;
        ElementTrait::setRequired as setElementRequired;
    }

    /**
     * @param string|null $name
     * @param array       $options
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

    /**
     * @return string
     */
    public function showAs()
    {
        if (null !== $this->getOption('showAs')) {
            return $this->getOption('showAs');
        }

        return 'fieldset';
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

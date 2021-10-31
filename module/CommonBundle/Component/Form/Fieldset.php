<?php

namespace CommonBundle\Component\Form;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\CacheTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Laminas\Form\FormInterface;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * Extending Laminas's fieldset component, so that our forms look the way we
 * want them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Fieldset extends \Laminas\Form\Fieldset implements FieldsetInterface, ServiceLocatorAwareInterface
{
    use ElementTrait {
        ElementTrait::setRequired as setElementRequired;
    }

    use FieldsetTrait {
        FieldsetTrait::setRequired insteadof ElementTrait;
    }

    use ServiceLocatorAwareTrait;

    use CacheTrait;
    use DoctrineTrait;

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
        if ($this->getOption('showAs') !== null) {
            return $this->getOption('showAs');
        }

        return 'fieldset';
    }

    /**
     * Ensures state is ready for use. Here, we append the name of the fieldsets
     * to every elements in order to avoid name clashes if the same fieldset is
     * used multiple times
     *
     * @param  FormInterface $form
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
        parent::prepareElement($form);
    }
}

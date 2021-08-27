<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

use CommonBundle\Component\Form\Factory;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait FormFactoryTrait
{
    /**
     * @return Factory
     */
    protected function getFormFactory()
    {
        return $this->getServiceLocator()->build(
            Factory::class,
            array(
                'isAdmin' => false,
            )
        );
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}

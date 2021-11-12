<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait TranslatorTrait
{
    /**
     * @return \Laminas\I18n\Translator\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->getServiceLocator()->get('translator');
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}

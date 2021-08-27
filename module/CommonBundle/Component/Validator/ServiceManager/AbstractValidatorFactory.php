<?php

namespace CommonBundle\Component\Validator\ServiceManager;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\Validator\AbstractValidator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Validator\AbstractValidator as ZendAbstractValidator;

/**
 * Abstract factory instantiating an installer.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AbstractValidatorFactory implements AbstractFactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @return boolean
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (class_exists($requestedName)) {
            if (in_array(AbstractValidator::class, class_parents($requestedName), true)) {
                return true;
            }

            return in_array(ZendAbstractValidator::class, class_parents($requestedName), true);
        }

        return false;
    }

    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return AbstractValidator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $validator = new $requestedName($options);
        if ($validator instanceof ServiceLocatorAwareInterface) {
            $validator->setServiceLocator($container);
        }

        return $validator;
    }
}

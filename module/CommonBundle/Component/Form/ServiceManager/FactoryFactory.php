<?php

namespace CommonBundle\Component\Form\ServiceManager;

use CommonBundle\Component\Form\Factory;
use CommonBundle\Component\Form\FormElementManager;
use Interop\Container\ContainerInterface;
use Laminas\Filter\FilterChain;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Validator\ValidatorChain;

/**
 * A factory class for form factories.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FactoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = $config['litus']['forms'][$options['isAdmin'] ? 'admin' : 'bootstrap'];

        $factory = new Factory(
            new FormElementManager($options['isAdmin'], $container, $config)
        );

        $filterChain = new FilterChain();
        $filterChain->setPluginManager($container->get('FilterManager'));

        $validatorChain = new ValidatorChain();
        $validatorChain->setPluginManager($container->get('ValidatorManager'));

        $factory->getInputFilterFactory()
            ->setDefaultFilterChain($filterChain)
            ->setDefaultValidatorChain($validatorChain);

        return $factory;
    }
}

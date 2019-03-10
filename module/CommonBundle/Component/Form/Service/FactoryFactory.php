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

namespace CommonBundle\Component\Form\Service;

use CommonBundle\Component\Form\Factory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create a form factory instance.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FactoryFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Factory
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($options !== null
            && isset($options['form_view_helpers'])
            && $container->has('ViewHelperManager')
        ) {
            $container->get('ViewHelperManager')->configure($options['form_view_helpers']);
        }

        $formFactory = new Factory(
            $container->get('FormElementManager')
        );

        $inputFilterFactory = $formFactory->getInputFilterFactory();
        $inputFilterFactory->getDefaultFilterChain()->setPluginManager($container->get('FilterManager'));
        $inputFilterFactory->getDefaultValidatorChain()->setPluginManager($container->get('ValidatorManager'));

        return $formFactory;
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return Factory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'CommonBundle\Component\Form\Factory');
    }
}

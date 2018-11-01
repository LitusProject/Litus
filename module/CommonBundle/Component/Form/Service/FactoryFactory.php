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
use CommonBundle\Component\Form\FormElementManager;
use Interop\Container\ContainerInterface;
use Zend\Filter\FilterChain;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Validator\ValidatorChain;

/**
 * A factory class for form factories.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FactoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
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

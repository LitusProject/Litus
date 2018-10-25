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

namespace CommonBundle\Component\Validator\Service;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\Validator\AbstractValidator;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Validator\AbstractValidator as ZendAbstractValidator;

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
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (class_exists($requestedName)) {
            if (in_array(AbstractValidator::class, class_parents($requestedName), true)) {
                return true;
            }

            if (in_array(ZendAbstractValidator::class, class_parents($requestedName), true)) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
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

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

namespace CommonBundle\Component\Validator;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface,
    CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait,
    Zend\Form\ElementInterface,
    Zend\ServiceManager\ServiceLocatorAwareTrait as ZendServiceLocatorAwareTrait,
    Zend\Validator\ValidatorPluginManager;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class AbstractValidator extends \Zend\Validator\AbstractValidator implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait, ZendServiceLocatorAwareTrait {
        ZendServiceLocatorAwareTrait::getServiceLocator as traitGetServiceLocator;
    }

    /**
     * @param  array|ElementInterface|null $context
     * @param  string|array                $path
     * @return string
     */
    protected static function getFormValue($context = null, $path)
    {
        if (null === $context || !(is_array($context) || $context instanceof ElementInterface)) {
            return null;
        }
        if (is_array($path)) {
            if (empty($path)) {
                return $context instanceof ElementInterface
                    ? $context->getValue()
                    : $context;
            }

            $step = array_shift($path);

            return self::getFormValue(self::takeStep($context, $step), $path);
        } else {
            $context = self::takeStep($context, $path);

            return $context instanceof ElementInterface
                ? $context->getValue()
                : $context;
        }
    }

    private static function takeStep($context, $step)
    {
        if ($context instanceof ElementInterface) {
            if (!$context->has($step)) {
                return null;
            }

            return $context->get($step);
        }

        if (!array_key_exists($step, $context)) {
            return null;
        }

        return $context[$step];
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        if ($this->serviceLocator instanceof ValidatorPluginManager) {
            return $this->serviceLocator->getServiceLocator();
        }

        return $this->serviceLocator;
    }
}

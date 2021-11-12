<?php

namespace CommonBundle\Component\Validator;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Component\Util\AcademicYear;
use Laminas\Form\ElementInterface;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class AbstractValidator extends \Laminas\Validator\AbstractValidator implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use DoctrineTrait;

    /**
     * @param  array|ElementInterface|null $context
     * @param  string|array                $path
     * @return string
     */
    protected static function getFormValue($context = null, $path = '')
    {
        if ($context === null || !(is_array($context) || $context instanceof ElementInterface)) {
            return null;
        }
        if (is_array($path)) {
            if (count($path) == 0) {
                return $context instanceof ElementInterface ? $context->getValue() : $context;
            }

            $step = array_shift($path);

            return self::getFormValue(self::takeStep($context, $step), $path);
        } else {
            $context = self::takeStep($context, $path);

            return $context instanceof ElementInterface ? $context->getValue() : $context;
        }
    }

    /**
     * @param  array|ElementInterface $context
     * @param  string                 $step
     * @return array|ElementInterface
     */
    private static function takeStep($context, $step)
    {
        if ($context instanceof \Laminas\Form\Fieldset) {
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
     * Get the current academic year.
     *
     * @param  boolean $organization
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getCurrentAcademicYear($organization = false)
    {
        if ($organization) {
            return AcademicYear::getOrganizationYear($this->getEntityManager());
        }

        return AcademicYear::getUniversityYear($this->getEntityManager());
    }
}

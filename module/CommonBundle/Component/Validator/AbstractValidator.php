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

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class AbstractValidator extends \Zend\Validator\AbstractValidator
{
    /**
     * @param array|null $context
     * @param string|array $path
     * @return mixed|false
     */
    protected static function getFormValue($context = null, $path)
    {
        if (null === $context || !is_array($context)) {
            return null;
        }

        if (is_array($value)) {
            if (empty($path) || !array_key_exists($path[0], $context)) {
                return null;
            }

            $step = array_shift($path);

            return self::getFormValue($context[$step], $path);
        } else {
            if (!array_key_exists($path, $context)) {
                return null;
            }

            return $context[$path];
        }
    }
}

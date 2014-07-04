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

namespace FormBundle\Component\Validator;

/**
 * Checks whether a field may be required or not (visibility)
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Required extends \Zend\Validator\AbstractValidator
{
    const MAY_NOT_BE_REQUIRED = 'mayNotBeRequired';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::MAY_NOT_BE_REQUIRED => 'The field may not be required because it can be invisible',
    );

    /**
     * Returns true if the required field is unchecked or checked if it is allowed
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ('1' == $value && isset($context['visible_if']) && '0' != $context['visible_if']) {
            $this->error(self::MAY_NOT_BE_REQUIRED);

            return false;
        }

        return true;
    }
}

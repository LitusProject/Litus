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

namespace CommonBundle\Component\Validator;

/**
 * Checks whether a checkbox is true is it is required
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */
class RequiredCheckbox extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_CHECKED = 'notChecked';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_CHECKED => 'The checkbox is required to be selected to submit the form.',
    );

    /**
     * Returns true if the required field is unchecked or checked if it is allowed
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);


        if ($value == '0') {
            $this->error(self::NOT_CHECKED);

            return false;
        }

        return true;
    }
}

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
 * Verifies whether the given number is a positive number.
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class PositiveNumber extends AbstractValidator
{
    const NOT_POSITIVE = 'notPositive';
    const NOT_STRICT_POSITIVE = 'notStrictPositive';

    protected $options = array(
        'strict' => true,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_POSITIVE => 'The value may not be negative',
        self::NOT_STRICT_POSITIVE => 'The value may not be negative or zero',
    );

    /**
     * Sets validator options
     *
     * @param int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['strict'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if the value is a positive number.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $intVal = intval(trim($value), 10);
        if ($intVal > 0) {
            return true;
        }

        if ($this->options['strict'] && $intVal == 0) {
            $this->error (self::NOT_STRICT_POSITIVE);
        } else {
            $this->error (self::NOT_POSITIVE);
        }

        return false;
    }
}

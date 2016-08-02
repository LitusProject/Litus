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
 * Checks whether the string field was specified correctly.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class StringField extends \CommonBundle\Component\Validator\AbstractValidator
{
    const ML_BOTH = 'mlboth';
    const NON_ML_LINES = 'nonmllines';

    protected $options = array(
        'multiline' => false,
        'lines' => 0,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::ML_BOTH => 'Multiline fields must specify either both a line limit and a character limit or none',
        self::NON_ML_LINES => 'Non multiline fields should not specify a maximum number of lines',
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
            $options['multiline'] = array_shift($args);
            $options['lines'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if a person exists for this value, but no driver exists for that person.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($this->options['multiline']) {
            if (($this->isSpecified($this->options['lines']) && !$this->isSpecified($value)) ||
                (!$this->isSpecified($this->options['lines']) && $this->isSpecified($value))) {
                $this->error(self::ML_BOTH);

                return false;
            }
        } else {
            if ($this->isSpecified($this->options['lines'])) {
                $this->error(self::NON_ML_LINES);

                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether the given value is a non-zero character count or line count specification.
     *
     * @param mixed $value The value to check.
     */
    private function isSpecified($value)
    {
        return $value !== null && $value != 0 && $value != '';
    }
}

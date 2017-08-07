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

namespace FormBundle\Component\Validator;

/**
 * Matches the timeslot for occupation of user
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class MaxTimeSlot extends \CommonBundle\Component\Validator\AbstractValidator
{
    /**
     * @var string The error codes
     */
    const TO_MANY = 'to_many';

    protected $options = array(
        'form' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TO_MANY => 'Too many time slots were selected',
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
            $options['form'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if the end date is after the start date
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $valid = true;

        $num = 0;

        foreach ($this->options['form']->getFields() as $field) {
            $num += isset($context['field-' . $field->getId()]) && $context['field-' . $field->getId()];
        }

        if ($num > 1 && !$this->options['form']->isMultiple()) {
            $this->error(self::TO_MANY);
            $valid = false;
        }

        return $valid;
    }
}

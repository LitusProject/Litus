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

use FormBundle\Entity\Node\Form\Doodle as Doodle;

/**
* Matches the timeslot for occupation of user
*
* @author Kristof Mariën <kristof.marien@litus.cc>
*/
class MaxTimeSlot extends \Zend\Validator\AbstractValidator
{
    /**
    * @var string The error codes
    */
    const TO_MANY = 'to_many';

    /**
    * @var array The error messages
    */
    protected $messageTemplates = array(
        self::TO_MANY         => 'Too many time slots were selected',
    );

    /**
    * @var Doodle
    */
    private $_form;

    /**
    * Sets validator options
    *
    * @param Doodle     $form
    * @param array|null $opts
    */
    public function __construct(Doodle $form, $opts = null)
    {
        parent::__construct($opts);

        $this->_form = $form;
    }

    /**
    * Returns true if and only if the end date is after the start date
    *
    * @param  mixed   $value
    * @param  array   $context
    * @return boolean
    */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $valid = true;

        $num = 0;

        foreach ($this->_form->getFields() as $field) {
            $num += isset($context['field-' . $field->getId()]) && $context['field-' . $field->getId()];
        }

        if ($num > 1 && !$this->_form->isMultiple()) {
            $this->error(self::TO_MANY);
            $valid = false;
        }

        return $valid;
    }
}

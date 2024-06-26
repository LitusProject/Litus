<?php

namespace TicketBundle\Component\Validator;

use CommonBundle\Component\Form\Form;
use CommonBundle\Component\Validator\AbstractValidator;
use CommonBundle\Component\Validator\FormAwareInterface;

class NumberTicketsGuest extends AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';
    const EXCEEDS_MAX_PERSON = 'exceedsMaxPerson';
    const EXCEEDS_MAX = 'exceedsMax';

    protected $options = array(
        'maximum' => '',
        'event'   => null,
    );

    /**
     * @var Form
     */
    private $form;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID          => 'The number of tickets is not valid',
        self::EXCEEDS_MAX_PERSON => 'The number of tickets exceeds the maximum per person (%maximum%)',
        self::EXCEEDS_MAX        => 'The number of tickets exceeds the maximum',
    );

    /**
     * @var array The message variables
     */
    protected $messageVariables = array(
        'maximum' => array('options' => 'maximum'),
    );

    /**
     * Sets validator options
     *
     * @param integer|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['event'] = array_shift($args);
            $options['maximum'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if these do not exceed max
     *
     * @param string     $value   The value of the field that will be validated
     * @param array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $optionsForm = $this->form->has('options_form') ? $this->form->get('options_form') : $this->form;

        $number = 0;
        if ($this->options['event']->getOptions()->isEmpty()) {
            $number += $optionsForm->get('number_member')->getValue();
            if (!$this->options['event']->isOnlyMembers()) {
                $number += $optionsForm->get('number_non_member')->getValue();
            }
        } else {
            $options = $this->options['event']->getOptions();
            foreach ($options as $option) {
                if ($option->isVisible()) {
                    $number += $optionsForm->get('option_' . $option->getId() . '_number_member')->getValue();
                    if (!$this->options['event']->isOnlyMembers() && $option->getPriceNonMembers() != 0) {
                        $number += $optionsForm->get('option_' . $option->getId() . '_number_non_member')->getValue();
                    }
                }
            }
        }

        if ($number == 0) {
            $this->error(self::NOT_VALID);

            return false;
        }

        if ($number > $this->options['event']->getLimitPerPerson() && $this->options['event']->getLimitPerPerson() != 0) {
            $this->error(self::EXCEEDS_MAX_PERSON);

            return false;
        }

        if ($number > $this->options['event']->getNumberFree() && $this->options['event']->getNumberOfTickets() != 0) {
            $this->error(self::EXCEEDS_MAX);

            return false;
        }

        return true;
    }

    /**
     * @param Form $form
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }
}

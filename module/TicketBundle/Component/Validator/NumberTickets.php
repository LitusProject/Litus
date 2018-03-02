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

namespace TicketBundle\Component\Validator;

use CommonBundle\Component\Form\Form,
    CommonBundle\Component\Validator\FormAwareInterface;

/**
 * Check whether number of member + number of non member does not exceed max
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NumberTickets extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';
    const EXCEEDS_MAX_PERSON = 'exceedsMaxPerson';
    const EXCEEDS_MAX = 'exceedsMax';

    protected $options = array(
        'event'  => null,
        'person' => null,
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
        self::EXCEEDS_MAX_PERSON => 'The number of tickets exceeds the maximum per person',
        self::EXCEEDS_MAX        => 'The number of tickets exceeds the maximum',
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
            $options['event'] = array_shift($args);
            $options['person'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if these does not exceed max
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        /** @var \CommonBundle\Component\Form\Fieldset $optionsForm */
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
                $number += $optionsForm->get('option_' . $option->getId() . '_number_member')->getValue();
                if (!$this->options['event']->isOnlyMembers()) {
                    $number += $optionsForm->get('option_' . $option->getId() . '_number_non_member')->getValue();
                }
            }
        }

        if ($number == 0) {
            $this->error(self::NOT_VALID);

            return false;
        }

        /** @var \CommonBundle\Component\Form\Fieldset $personFieldset */
        $personFieldset = $this->form->get('person_form');
        if ($this->options['person'] == null && is_numeric($personFieldset->get('person')->getValue())) {
            $person = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person')
                ->findOneById($personFieldset->get('person')->getValue());
        } else {
            $person = $this->options['person'];
        }

        if (null === $person && !$this->form->get('is_guest')->getValue()) {
            $this->error(self::NOT_VALID);

            return false;
        }

        if (null !== $person) {
            $tickets = $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findAllByEventAndPerson($this->options['event'], $person);

            if ($number + sizeof($tickets) > $this->options['event']->getLimitPerPerson() && $this->options['event']->getLimitPerPerson() != 0) {
                $this->error(self::EXCEEDS_MAX_PERSON);

                return false;
            }
        }

        if ($number > $this->options['event']->getNumberFree() && $this->options['event']->getNumberOfTickets() != 0) {
            $this->error(self::EXCEEDS_MAX);

            return false;
        }

        return true;
    }

    /**
     * @param  Form $form
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }
}

<?php

namespace CalendarBundle\Component\Validator;

use CommonBundle\Component\Form\Form;
use CommonBundle\Component\Util\Url;
use CommonBundle\Component\Validator\FormAwareInterface;
use DateTime;

/**
 * Matches the given event title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EventName extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'event' => null,
    );

    /**
     * @var Form
     */
    private $form;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This event title already exists',
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
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $date = DateTime::createFromFormat('d#m#Y H#i', $this->form->get('start_date')->getValue());

        if ($date) {
            $title = $date->format('Ymd') . '_' . Url::createSlug($value);

            $event = $this->getEntityManager()
                ->getRepository('CalendarBundle\Entity\Node\Event')
                ->findOneByName($title);

            if ($event === null || ($this->options['event'] && $event->getEvent() == $this->options['event'])) {
                return true;
            }

            $this->error(self::NOT_VALID);
        }

        return false;
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

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

namespace CalendarBundle\Component\Validator;

use CommonBundle\Component\Form\Form,
    CommonBundle\Component\Util\Url,
    CommonBundle\Component\Validator\FormAwareInterface;

/**
 * Matches the given event title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Name extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'event' => null,
    );

    /**
     * @var Form
     */
    private $_form;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This event title already exists',
    );

    /**
     * Sets validator options
     *
     * @param int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options     = func_get_args();
            $temp['event'] = array_shift($options);
            $options = $temp;
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $date = \DateTime::createFromFormat('d#m#Y H#i', $this->_form->get('start_date')->getValue());

        if ($date) {
            $title = $date->format('Ymd') . '_' . Url::createSlug($value);

            $event = $this->getEntityManager()
                ->getRepository('CalendarBundle\Entity\Node\Event')
                ->findOneByName($title);

            if (null === $event || ($this->options['event'] && $event->getEvent() == $this->options['event'])) {
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
        $this->_form = $form;

        return $this;
    }
}

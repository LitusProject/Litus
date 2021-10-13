<?php

namespace MailBundle\Component\Validator;

/**
 * Checks whether a mailing list name is unique or not.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ListName extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'list' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'A list with this name already exists',
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
            $options['list'] = array_shift($args);
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

        $list = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\Named')
            ->findOneByName($value);

        if ($list === null || ($this->options['list'] && $list == $this->options['list'])) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

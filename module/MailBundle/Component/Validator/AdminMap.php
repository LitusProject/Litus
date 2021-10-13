<?php

namespace MailBundle\Component\Validator;

/**
 * Checks whether a mailing admin map is unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AdminMap extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'list' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This member already has admin rights on this list',
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

        $adminMap = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\AdminMap')
            ->findOneBy(
                array(
                    'list'     => $this->options['list'],
                    'academic' => $context['id'],
                )
            );

        if ($adminMap === null) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

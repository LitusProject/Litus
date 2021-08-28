<?php

namespace PageBundle\Component\Validator;

/**
 * Checks whether an FAQ is already subscribed to a page
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FAQ extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'page' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This faq already has been subscribed to this Page',
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
            $options['page'] = array_shift($args);
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

        $faq = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQ')
            ->findOneById(intval($context['id']));

        if (!in_array($faq, $this->options['page']->getFAQs()->toArray())) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

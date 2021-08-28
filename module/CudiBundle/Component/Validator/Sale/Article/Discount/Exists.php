<?php

namespace CudiBundle\Component\Validator\Sale\Article\Discount;

use CudiBundle\Entity\Sale\Article;
/**
 * Matches the given discount against the database to check whether it already exists or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Exists extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'article' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The discount already exist',
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
            $options['article'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($context['organization'] != '0') {
            $organization = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOneById($context['organization']);
        } else {
            $organization = null;
        }

        $discount = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Discount')
            ->findOneByArticleAndTypeAndOrganization($this->options['article'], $value, $organization);

        if ($discount === null) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

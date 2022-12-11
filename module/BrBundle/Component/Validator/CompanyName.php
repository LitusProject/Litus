<?php

namespace BrBundle\Component\Validator;

use CommonBundle\Component\Util\Url;

/**
 * Matches the given company name against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CompanyName extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The company name already exists',
    );

    protected $options = array(
        'company' => null,
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
            $options['company'] = array_shift($args);
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

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneBySlug(Url::createSlug($value));

        if ($company === null || ($this->options['company'] && ($company == $this->options['company'] || !$company->isActive()))) {
            return true;
        }

        if (!$company->isActive()) {
            return true;
        }

        $this->error(self::NOT_VALID);
        return false;
    }
}

<?php

namespace BrBundle\Component\Validator;

/**
 * Matches the given company against the database to check whether the logo type
 * already exists or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class LogoType extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The logo type already exists for this company',
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

        $logo = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Logo')
            ->findOneByTypeAndCompany($value, $this->options['company']);

        if ($logo === null) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

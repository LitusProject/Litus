<?php

namespace SyllabusBundle\Component\Validator\Study\ModuleGroup;

/**
 * Matches the given module group code against the database to check whether it exists or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ExternalId extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'exclude' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The module group already exists',
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
            $options['exclude'] = array_shift($args);
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

        $moduleGroup = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
            ->findOneByExternalId($value);

        if ($moduleGroup === null || ($this->options['exclude'] !== null && $moduleGroup->getId() == $this->options['exclude']->getId())) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

<?php

namespace SyllabusBundle\Component\Validator;

/**
 * Matches the given group name against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupName extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'exclude' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'There already exists a group with this name',
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
     * Returns true if no matching record is found in the database.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $group = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findOneByName($value);

        if ($group === null || ($this->options['exclude'] !== null && $group->getId() == $this->options['exclude']->getId())) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

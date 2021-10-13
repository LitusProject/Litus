<?php

namespace CommonBundle\Component\Validator;

abstract class Typeahead extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'entity' => '',
    );

    /**
     * @var mixed The found entity
     */
    protected $entityObject;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This entity does not exits',
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
            $options['entity'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field unique and valid.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($context['id'] == null) {
            $this->error(self::NOT_VALID);

            return false;
        }

        $this->entityObject = $this->getEntityManager()
            ->getRepository($this->options['entity'])
            ->findOneById($context['id']);

        if ($this->entityObject === null) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getEntityObject()
    {
        return $this->entityObject;
    }
}

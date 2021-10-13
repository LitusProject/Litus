<?php

namespace CommonBundle\Component\Validator;

class PersonBarcode extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The academic barcode already exists',
    );

    protected $options = array(
        'person' => null,
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
            $options['person'] = array_shift($args);
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

        if ($this->options['person'] === null) {
            return true;
        }

        $barcode = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Barcode')
            ->findOneByBarcode($value);

        if ($barcode === null || ($this->options['person'] && $barcode->getPerson() == $this->options['person'])) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

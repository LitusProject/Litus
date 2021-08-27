<?php

namespace BrBundle\Component\Validator;

/**
 * Matches the given product name against the database to check whether it is
 * unique or not.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ProductName extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The product name already exists',
    );

    protected $options = array(
        'product' => null,
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
            $options['product'] = array_shift($args);
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

        $product = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product')
            ->findProductByNameNotOld($value);

        if ($product === null || ($this->options['product'] && ($product == $this->options['product']))) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

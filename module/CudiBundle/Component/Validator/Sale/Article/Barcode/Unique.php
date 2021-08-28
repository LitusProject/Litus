<?php

namespace CudiBundle\Component\Validator\Sale\Article\Barcode;

use CudiBundle\Entity\Sale\Article;
/**
 * Matches the given article barcode against the database to check whether it is unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Unique extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'sale_article' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The article barcode already exists',
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
            $options['sale_article'] = array_shift($args);
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

        if (! is_numeric($value)) {
            $this->error(self::NOT_VALID);

            return false;
        }

        $barcode = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Barcode')
            ->findOneByBarcode($value);

        if ($barcode === null || $barcode->getArticle() == $this->options['sale_article']) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

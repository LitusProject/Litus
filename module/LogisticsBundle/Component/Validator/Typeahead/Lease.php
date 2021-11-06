<?php

namespace LogisticsBundle\Component\Validator\Typeahead;

/**
 * Checks if a barcode belongs to a lease item, and it is not yet leased
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Lease extends \CommonBundle\Component\Validator\Typeahead
{
    /**
     * @const string The error codes
     */
    const ITEM_LEASED = 'itemLeased';
    const ITEM_RETURNED = 'itemReturned';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID     => 'No lease item with this barcode exists',
        self::ITEM_LEASED   => 'This item is already leased',
        self::ITEM_RETURNED => 'This item is already returned',
    );

    protected $options = array(
        'entity'         => '',
        'must_be_leased' => false,
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
            $options['must_be_leased'] = array_shift($args);
        }

        $options['entity'] = 'LogisticsBundle\Entity\Lease\Item';

        parent::__construct($options);
    }

    /**
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (!parent::isValid($value, $context)) {
            return false;
        }

        $unreturned = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Lease')
            ->findUnreturnedByItem($this->entityObject);

        switch (count($unreturned)) {
            case 0:
                if ($this->options['must_be_leased']) {
                    $this->error(self::ITEM_RETURNED);

                    return false;
                }

                return true;
            default:
                if (!$this->options['must_be_leased']) {
                    $this->error(self::ITEM_LEASED);

                    return false;
                }

                return true;
        }
    }
}

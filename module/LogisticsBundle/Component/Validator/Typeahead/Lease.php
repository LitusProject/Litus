<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
        self::NOT_VALID => 'No lease item with this barcode exists',
        self::ITEM_LEASED => 'This item is already leased',
        self::ITEM_RETURNED => 'This item is already returned',
    );

    protected $options = array(
        'entity' => '',
        'must_be_leased' => false,
    );

    /**
     * Sets validator options
     *
     * @param int|array|\Traversable $options
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
            ->getRepository('LogisticsBundle\Entity\Lease\Lease')
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

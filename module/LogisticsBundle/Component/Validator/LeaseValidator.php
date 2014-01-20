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
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Component\Validator;

use Doctrine\ORM\EntityManager;

/**
 * Checks if a barcode belongs to a lease item, and it is not yet leased
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class LeaseValidator extends \Zend\Validator\AbstractValidator
{
    /**
     * @const string The error codes
     */
    const NO_LEASE_ITEM = 'noLeaseItem';
    const ITEM_LEASED = 'itemLeased';
    const ITEM_RETURNED = 'itemReturned';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NO_LEASE_ITEM => 'No lease item with this barcode exists',
        self::ITEM_LEASED => 'This item is already leased',
        self::ITEM_RETURNED => 'This item is already returned',
    );

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * True if the item has to be leased for the validator to be valid.
     * @var boolean
     */
    private $_mustBeLeased;

    /**
     * Sets validator options
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param boolean $mustBeLeased If true, the item must be leased to pass the validation. Else it shouldn't.
     * @return void
     */
    public function __construct(EntityManager $entityManager, $mustBeLeased = false)
    {
        parent::__construct(null);

        $this->_entityManager = $entityManager;
        $this->_mustBeLeased = !!$mustBeLeased;
    }

    /**
     *
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $item = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Lease\Item')
            ->findOneByBarcode($value);

        if(!$item) {
            $this->error(self::NO_LEASE_ITEM);
            return false;
        }

        $unreturned = $this->_entityManager
                ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                ->findUnreturnedByItem($item);

        switch(count($unreturned)) {
            case 0:
                if($this->_mustBeLeased) {
                    $this->error(self::ITEM_RETURNED);

                    return false;
                }

                return true;
            default:
                if(!$this->_mustBeLeased) {
                    $this->error(self::ITEM_LEASED);

                    return false;
                }

                return true;
        }

    }
}

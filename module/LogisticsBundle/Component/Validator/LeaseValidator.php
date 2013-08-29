<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Component\Validator;

use Doctrine\ORM\EntityManager;

/**
 * Checks if a barcode belongs to a lease item, and it is not yet leased
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class LeaseValidator extends \Zend\Validator\AbstractValidator
{
    /**
     * @const string The error codes
     */
    const NO_LEASE_ITEM = 'noLeaseItem';
    const ITEM_LEASED = 'itemLeased';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NO_LEASE_ITEM => 'No lease item with this barcode exists',
        self::ITEM_LEASED => 'This item is already leased to someone',
    );

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * Sets validator options
     *
     * @param mixed $token
     * @param string $format
     * @return void
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct(null);

        $this->_entityManager = $entityManager;
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

        if(count($unreturned) > 0) {
            $this->error(self::ITEM_LEASED);
            return false;
        }

        return true;
    }
}

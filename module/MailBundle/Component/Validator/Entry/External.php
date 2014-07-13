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

namespace MailBundle\Component\Validator\Entry;

use Doctrine\ORM\EntityManager,
    MailBundle\Entity\MailingList as MailingListEntity;

/**
 * Checks whether a mailing admin map is unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class External extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \MailBundle\Entity\MailingList The list for this check
     */
    private $_list;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This external address already has been subscribed to this list!'
    );

    /**
     * @param EntityManager                  $entityManager The EntityManager instance
     * @param \MailBundle\Entity\MailingList $list          The list exluded from this check
     * @param mixed                          $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, MailingListEntity $list, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_list = $list;
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $entry = $this->_entityManager
            ->getRepository('MailBundle\Entity\MailingList\Entry\Person\External')
            ->findOneBy(
                array(
                    'list' => $this->_list,
                    'email' => $value
                )
            );

        if (null === $entry)
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

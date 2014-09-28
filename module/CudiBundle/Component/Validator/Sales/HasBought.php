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

namespace CudiBundle\Component\Validator\Sales;

use Doctrine\ORM\EntityManager;

/**
 * Check if user has bought an aritcle
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class HasBought extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The article was never bought by this user'
    );

    /**
     * Create a new HasBought validator.
     *
     * @param EntityManager $entityManager The EntityManager instance
     * @param mixed         $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
    }


    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $person = $this->_entityManager
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($context['person_id']);

        $article = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($context['article_id']);

        $booking = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findOneSoldByArticleAndPerson($article, $person, false);

        if (null !== $booking)
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

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

namespace GalleryBundle\Component\Validator;

use CommonBundle\Component\Util\Url,
    Doctrine\ORM\EntityManager,
    GalleryBundle\Entity\Album\Album;

/**
 * Matches the given page title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Name extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \GalleryBundle\Entity\Album\Album The album exluded from this check
     */
    private $_album;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The album title already exists'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \GalleryBundle\Entity\Adlum\Album The album exluded from this check
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Album $album = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_album = $album;
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $date = \DateTime::createFromFormat('d#m#Y', $context['date']);

        if ($date) {
            $title = $date->format('Ymd') . '_' . Url::createSlug($value);

            $album = $this->_entityManager
                ->getRepository('GalleryBundle\Entity\Album\Album')
                ->findOneByName($title);

            if (null === $album || ($this->_album && $album == $this->_album))
                return true;

            $this->error(self::NOT_VALID);
        }

        return false;
    }
}

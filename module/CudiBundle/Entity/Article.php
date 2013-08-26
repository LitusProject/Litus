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

namespace CudiBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Article")
 * @ORM\Table(name="cudi.articles")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "external"="CudiBundle\Entity\Article\External",
 *      "internal"="CudiBundle\Entity\Article\Internal"
 * })
 */
abstract class Article
{
    /**
     * @var integer The ID of this article
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The title of this article
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string The authors of the article
     *
     * @ORM\Column(type="string")
     */
    private $authors;

    /**
     * @var string The publishers of the article
     *
     * @ORM\Column(type="string")
     */
    private $publishers;

    /**
     * @var integer The year the article was published
     *
     * @ORM\Column(name="year_published", type="integer", length=4, nullable=true)
     */
    private $yearPublished;

    /**
     * @var \DateTime The time the article was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var integer The version number of this article
     *
     * @ORM\Column(name="version_number", type="smallint", nullable=true)
     */
    private $versionNumber;

    /**
     * @var integer The ISBN number of this article
     *
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $isbn;

    /**
     * @var string The url with a link to extra information of this article
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $url;

    /**
     * @var boolean The flag whether the article is old or not
     *
     * @ORM\Column(name="is_history", type="boolean")
     */
    private $isHistory;

    /**
     * @var boolean The flag whether the article is just created by a prof
     *
     * @ORM\Column(name="is_prof", type="boolean")
     */
    private $isProf;

    /**
     * @var boolean Flag whether this action is a draft
     *
     * @ORM\Column(name="is_draft", type="boolean")
     */
    private $isDraft;

    /**
     * @var boolean The flag whether the article is downloadable
     *
     * @ORM\Column(type="boolean")
     */
    private $downloadable;

    /**
     * @var boolean The flag whether the article is the same as previous year
     *
     * @ORM\Column(name="same_as_previous_year", type="boolean")
     */
    private $sameAsPreviousYear;

    /**
     * @var string The article type
     *
     * @ORM\Column(type="string")
     */
    private $type;
    
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager;

    /**
     * @var array The possible types of an article
     */
    public static $POSSIBLE_TYPES = array(
        'common' => 'Common',
        'other' => 'Other',
        'exercises' => 'Exercises',
        'notes' => 'Notes',
        'slides' => 'Slides',
        'student' => 'Student',
        'textbook' => 'Textbook',
    );

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $title The title of the article
     * @param string $authors The authors of the article
     * @param string $publishers The publishers of the article
     * @param integer $yearPublished The year the article was published
     * @param integer $isbn The isbn of the article
     * @param string|null $url The url of the article
     * @param string $type The article type
     * @param boolean $downloadable The flag whether the article is downloadable
     * @param boolean $sameAsPreviousYear The flag whether the article is the same as previous year
     */
    public function __construct($title, $authors, $publishers, $yearPublished, $isbn = null, $url = null, $type, $downloadable, $sameAsPreviousYear)
    {
        $this->setTitle($title)
            ->setAuthors($authors)
            ->setPublishers($publishers)
            ->setYearPublished($yearPublished)
            ->setVersionNumber(1)
            ->setISBN($isbn)
            ->setURL($url)
            ->setIsHistory(false)
            ->setIsProf(false)
            ->setType($type)
            ->setIsDownloadable($downloadable)
            ->setIsSameAsPreviousYear($sameAsPreviousYear);
        $this->timestamp = new DateTime();
        $this->isDraft = false;
    }

    /**
     * @return boolean
     */
    public static function isValidArticleType($type)
    {
        return array_key_exists($type, self::$POSSIBLE_TYPES);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setTitle($title)
    {
        $title = trim($title);

        if (strlen($title) == 0)
            throw new \InvalidArgumentException('The article title is not valid.');

        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param string $authors
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setAuthors($authors)
    {
        $this->authors = $authors;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @param string $publishers
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setPublishers($publishers)
    {
        $this->publishers = $publishers;
        return $this;
    }

    /**
     * @return integer
     */
    public function getYearPublished()
    {
        return $this->yearPublished;
    }

    /**
     * @param string $yearPublished
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setYearPublished($yearPublished)
    {
        if (empty($yearPublished))
            $yearPublished = null;
        $this->yearPublished = $yearPublished;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setTimestamp(DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return integer
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }

    /**
     * @param integer $versionNumber
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setVersionNumber($versionNumber)
    {
        $this->versionNumber = $versionNumber;
        return $this;
    }

    /**
     * @return integer
     */
    public function getISBN()
    {
        return $this->isbn;
    }

    /**
     * @param integer $isbn
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setISBN($isbn)
    {
        if (strlen($isbn) == 0)
            $this->isbn = null;
        else
            $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setURL($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHistory()
    {
        return $this->isHistory;
    }

    /**
     * @param boolean $isHistory
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setIsHistory($isHistory)
    {
        $this->isHistory = $isHistory;

        if ($saleArticle = $this->getSaleArticle() && $isHistory == true)
            $saleArticle->setIsHistory(true);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isProf()
    {
        return $this->isProf;
    }

    /**
     * @param boolean $isProf
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setIsProf($isProf)
    {
        $this->isProf = $isProf;
        return $this;
    }

    /**
     * @param boolean $isDraft
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setIsDraft($isDraft)
    {
        if ($isDraft)
            $this->setIsProf(true);

        $this->isDraft = $isDraft;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * @return boolean
     */
    public function isDownloadable()
    {
        return $this->downloadable;
    }

    /**
     * @param boolean $downloadable
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setIsDownloadable($downloadable)
    {
        $this->downloadable = $downloadable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSameAsPreviousYear()
    {
        return $this->sameAsPreviousYear;
    }

    /**
     * @param boolean $sameAsPreviousYear
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setIsSameAsPreviousYear($sameAsPreviousYear)
    {
        $this->sameAsPreviousYear = $sameAsPreviousYear;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setType($type)
    {
        if (!self::isValidArticleType($type))
            throw new \InvalidArgumentException('The article type is not valid.');
        $this->type = $type;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Sale\Article
     */
    public function getSaleArticle()
    {
        if (null == $this->_entityManager)
            return null;

        return $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneByArticle($this);
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Article
     */
    abstract public function duplicate();

    /**
     * @return boolean
     */
    abstract public function isExternal();

    /**
     * @return boolean
     */
    abstract public function isInternal();
}

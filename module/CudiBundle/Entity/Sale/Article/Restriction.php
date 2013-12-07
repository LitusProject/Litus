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

namespace CudiBundle\Entity\Sale\Article;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article as Article,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Restriction")
 * @ORM\Table(name="cudi.sales_articles_restrictions")
 */
class Restriction
{
    /**
     * @var integer The ID of the restriction
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CudiBundle\Entity\Sale\Article The article of the restriction
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article", inversedBy="barcodes")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var string The type of restriction
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string The value of the restriction
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $value;

    /**
     * @var array The possible types of a discount
     */
    public static $POSSIBLE_TYPES = array(
        'member' => 'Member',
        'amount' => 'Amount',
    );

    /**
     * @var array The possible types of a discount
     */
    public static $VALUE_TYPES = array(
        'member' => 'boolean',
        'amount' => 'integer',
    );

    /**
     * @param \CudiBundle\Entity\Sale\Article The article of the restriction
     * @param string $type The type of the restriction
     * @param string|null $value The value of the restriction
     */
    public function __construct(Article $article, $type, $value = null)
    {
        if (!self::isValidRestrictionType($type))
            throw new \InvalidArgumentException('The restriction type is not valid.');

        $this->article = $article;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public static function isValidRestrictionType($type)
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
     * @return \CudiBundle\Entity\Sale\Article\Barcode
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::$POSSIBLE_TYPES[$this->type];
    }

    /**
     * @return string
     */
    public function getRawType()
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return boolean
     */
    public function canBook(Person $person, EntityManager $entityManager)
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $academicYear = $entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        if ('member' == $this->type) {
            return ('1' == $this->value && $person->isMember($academicYear) || '0' == $this->value && !$person->isMember($academicYear));
        } elseif ('amount' == $this->type) {
            $amount = sizeof($entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneSoldOrAssignedOrBookedByArticleAndPerson($this->article, $person));
            return $amount < $this->value;
        } else {
            return false;
        }
    }
}

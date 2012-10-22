<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an event.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Logo")
 * @ORM\Table(name="br.companies_logos")
 */
class Logo
{
    /**
     * @var string The company logo's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The type of the logo
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string The url of the company
     *
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @var string The path to the logo
     *
     * @ORM\Column(type="string")
     */
    private $path;

    /**
     * @var \BrBundle\Entity\Company The company of the logo
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @var integer The width of the logo
     *
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @var integer The height of the logo
     *
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @var array The possible types of a logo
     */
    public static $POSSIBLE_TYPES = array(
        'homepage' => 'Homepage',
        'cudi' => 'Cudi',
    );

    /**
     * @param \BrBundle\Entity\Company $company The event's company
     * @param string $type The type to the logo
     * @param string $path The path to the logo
     * @param string $url The url to the website
     * @param integer $width The width of the logo
     * @param integer $height The height of the logo
     */
    public function __construct(Company $company, $type, $path, $url, $width, $height)
    {
        $this->company = $company;
        $this->path = $path;
        $this->width = $width;
        $this->height = $height;

        if (strpos($url, 'http://') !== 0)
            $url = 'http://' . $url;

        $this->url = $url;

        if (!self::isValidLogoType($type))
            throw new \InvalidArgumentException('The logo type is not valid.');
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public static function isValidLogoType($type)
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
    public function getType()
    {
        return self::$POSSIBLE_TYPES[$this->type];
    }

    /**
     * @return string
     */
    public function getTypeCode()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }
}

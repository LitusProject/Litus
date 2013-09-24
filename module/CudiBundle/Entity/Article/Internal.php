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

namespace CudiBundle\Entity\Article;

use CudiBundle\Entity\Article\Option\Binding,
    CudiBundle\Entity\Article\Option\Color,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Article\Internal")
 * @ORM\Table(name="cudi.articles_internal")
 */
class Internal extends \CudiBundle\Entity\Article
{
    /**
     * @var integer The number of black and white pages
     *
     * @ORM\Column(name="nb_black_and_white", type="smallint")
     */
    private $nbBlackAndWhite;

    /**
     * @var integer The number of colored pages
     *
     * @ORM\Column(name="nb_colored", type="smallint")
     */
    private $nbColored;

    /**
     * @var \CudiBundle\Entity\Article\Option\Binding The binding of this article
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article\Option\Binding")
     * @ORM\JoinColumn(name="binding", referencedColumnName="id")
     */
    private $binding;

    /**
     * @var boolean Whether the aricle is an official one.
     *
     * @ORM\Column(type="boolean")
     */
    private $official;

    /**
     * @var boolean Flag whether the article is rectoverso or not.
     *
     * @ORM\Column(name="recto_verso", type="boolean")
     */
    private $rectoVerso;

    /**
     * @var \CudiBundle\Entity\Article\Option\Color The color of the front page.
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article\Option\Color")
     * @ORM\JoinColumn(name="front_page_color", referencedColumnName="id")
     */
    private $frontPageColor;

    /**
     * @var boolean Whether the aricle is perforated or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $isPerforated;

    /**
     * @var boolean Flag whether the article pages are colored
     *
     * @ORM\Column(type="boolean")
     */
    private $colored;
    
    /**
     * @var boolean Flag whether the article has a hardcover
     *
     * @ORM\Column(type="boolean")
     */
    private $_hardcovered;

    /**
     * @var string The file name of the cached front page
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $frontPage;

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
     * @param integer $nbBlackAndWhite The number of blach and white pages of the article
     * @param integer $nbColored The number of colored pages of the article
     * @param \CudiBundle\Entity\Article\Option\Binding $binding The binding of the article
     * @param boolean $official Whether the article is an official one or not
     * @param boolean $rectoverso Whether the article is recto-verso or not
     * @param \CudiBundle\Entity\Article\Option\Color $frontPageColor The front page color of the article
     * @param boolean $isPerforated Whether the article is perforated or not
     * @param boolean $isPerforated Whether the article pages are colored or not
     */
    public function __construct(
        $title, $authors, $publishers, $yearPublished, $isbn, $url = null, $type, $downloadable, $sameAsPreviousYear, $nbBlackAndWhite, $nbColored, Binding $binding, $official, $rectoverso, Color $frontPageColor = null, $isPerforated, $colored,$hardcovered = false
    ) {
        parent::__construct($title, $authors, $publishers, $yearPublished, $isbn, $url, $type, $downloadable, $sameAsPreviousYear);

        $this->setNbBlackAndWhite($nbBlackAndWhite)
            ->setNbColored($nbColored)
            ->setBinding($binding)
            ->setIsOfficial($official)
            ->setIsRectoVerso($rectoverso)
            ->setFrontColor($frontPageColor)
            ->setIsPerforated($isPerforated)
            ->setIsColored($colored)
			->setIsHardCovered($hardcovered);
    }

    /**
     * @return int
     */
    public function getNbBlackAndWhite()
    {
        return $this->nbBlackAndWhite;
    }

    /**
     * @param integer $nbBlackAndWhite
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setNbBlackAndWhite($nbBlackAndWhite)
    {
        $this->nbBlackAndWhite = $nbBlackAndWhite;
        return $this;
    }

    /**
     * @return int
     */
    public function getNbColored()
    {
        return $this->nbColored;
    }

    /**
     * @param integer $nbColored
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setNbColored($nbColored)
    {
        $this->nbColored = $nbColored;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Article\Option\Binding
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * @param \CudiBundle\Entity\Article\Option\Binding $binding
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setBinding(Binding $binding)
    {
        $this->binding = $binding;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOfficial()
    {
        return $this->official;
    }

    /**
     * @param boolean $official
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setIsOfficial($official)
    {
        $this->official = $official;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRectoVerso()
    {
        return $this->rectoVerso;
    }

    /**
     * @param boolean $rectoVerso
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setIsRectoVerso($rectoVerso)
    {
        $this->rectoVerso = $rectoVerso;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Article\Option\Color
     */
    public function getFrontColor()
    {
        return $this->frontPageColor;
    }

    /**
     * @param \CudiBundle\Entity\Article\Option\Color $frontPageColor
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setFrontColor(Color $frontPageColor = null)
    {
        $this->frontPageColor = $frontPageColor;
        return $this;
    }

    /**
     * @return integer
     */
    public function getNbPages()
    {
        return $this->getNbBlackAndWhite() + $this->getNbColored();
    }

    /**
     * @return boolean
     */
    public function isPerforated()
    {
        return $this->isPerforated;
    }

    /**
     * @param boolean $isPerforated
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setIsPerforated($isPerforated)
    {
        $this->isPerforated = $isPerforated;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isColored()
    {
        return $this->colored;
    }

    /**
     * @param boolean $colored
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setIsColored($colored)
    {
        $this->colored = $colored;
        return $this;
    }
	
	/**
     * @return boolean
     */
    public function isHardCovered()
    {
        return $this->_hardcovered;
    }

    /**
     * @param boolean $hardcovered
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setIsHardCovered($hardcovered)
    {
        $this->_hardcovered = $hardcovered;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrontPage()
    {
        return $this->frontPage;
    }

    /**
     * @param string|null $frontPage
     *
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function setFrontPage($frontPage = null)
    {
        $this->frontPage = $frontPage;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Article
     */
    public function duplicate()
    {
        return new Internal(
            $this->getTitle(),
            $this->getAuthors(),
            $this->getPublishers(),
            $this->getYearPublished(),
            $this->getISBN(),
            $this->getURL(),
            $this->getType(),
            $this->isDownloadable(),
            $this->isSameAsPreviousYear(),
            $this->getNbBlackAndWhite(),
            $this->getNbColored(),
            $this->getBinding(),
            $this->isOfficial(),
            $this->isRectoVerso(),
            $this->getFrontColor(),
            $this->isPerforated(),
            $this->isColored()
        );
    }

    /**
     * @return boolean
     */
    public function isExternal()
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isInternal()
    {
        return true;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return integer
     */
    public function precalculateSellPrice(EntityManager $entityManager)
    {
        $prices = unserialize(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.sell_prices')
        );

        $total = 0;
        switch ($this->binding->getCode()) {
            case 'glued':
                $total += $prices['binding_glued'];
                break;
            case 'stapled':
                $total += $prices['binding_stapled'];
                break;
            default:
                $total += $prices['binding_none'];
                break;
        }
        if ($this->rectoVerso) {
            if ($this->nbColored > 0)
                $total += $prices['recto_verso_color'] * ($this->nbColored + $this->nbBlackAndWhite);
            else
                $total += $prices['recto_verso_bw'] * ($this->nbColored + $this->nbBlackAndWhite);
        } else {
            if ($this->nbColored > 0)
                $total += $prices['recto_color'] * ($this->nbColored + $this->nbBlackAndWhite);
            else
                $total += $prices['recto_bw'] * ($this->nbColored + $this->nbBlackAndWhite);
        }
        return $total;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return integer
     */
    public function precalculatePurchasePrice(EntityManager $entityManager)
    {
        $prices = unserialize(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.purchase_prices')
        );

        $total = 0;
        switch ($this->binding->getCode()) {
            case 'glued':
                $total += $prices['binding_glued'];
                break;
            case 'stapled':
                $total += $prices['binding_stapled'];
                break;
            default:
                $total += $prices['binding_none'];
                break;
        }
        if ($this->rectoVerso) {
            if ($this->nbColored > 0)
                $total += $prices['recto_verso_color'] * ($this->nbColored + $this->nbBlackAndWhite);
            else
                $total += $prices['recto_verso_bw'] * ($this->nbColored + $this->nbBlackAndWhite);
        } else {
            if ($this->nbColored > 0)
                $total += $prices['recto_color'] * ($this->nbColored + $this->nbBlackAndWhite);
            else
                $total += $prices['recto_bw'] * ($this->nbColored + $this->nbBlackAndWhite);
        }
        return $total / 1000;
    }
}

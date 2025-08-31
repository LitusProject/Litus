<?php

namespace CudiBundle\Entity\Article;

use CudiBundle\Entity\Article\Option\Binding;
use CudiBundle\Entity\Article\Option\Color;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Article\Internal")
 * @ORM\Table(name="cudi_articles_internal")
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
     * @var Binding The binding of this article
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
     * @var Color The color of the front page.
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
    private $hardcovered;

    /**
     * @var string The file name of the cached front page
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $frontPage;

    public function __construct()
    {
        parent::__construct();

        $this->nbBlackAndWhite = 0;
        $this->nbColored = 0;
    }

    /**
     * @return integer
     */
    public function getNbBlackAndWhite()
    {
        return $this->nbBlackAndWhite;
    }

    /**
     * @param integer $nbBlackAndWhite
     *
     * @return self
     */
    public function setNbBlackAndWhite($nbBlackAndWhite)
    {
        $this->nbBlackAndWhite = $nbBlackAndWhite;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNbColored()
    {
        return $this->nbColored;
    }

    /**
     * @param integer $nbColored
     *
     * @return self
     */
    public function setNbColored($nbColored)
    {
        $this->nbColored = $nbColored;

        return $this;
    }

    /**
     * @return Binding
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * @param Binding $binding
     *
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setIsRectoVerso($rectoVerso)
    {
        $this->rectoVerso = $rectoVerso;

        return $this;
    }

    /**
     * @return Color
     */
    public function getFrontColor()
    {
        return $this->frontPageColor;
    }

    /**
     * @param Color|null $frontPageColor
     *
     * @return self
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
     * @return self
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
        return $this->colored || $this->getNbColored() > 0;
    }

    /**
     * @param boolean $colored
     *
     * @return self
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
        return $this->hardcovered;
    }

    /**
     * @param boolean $hardcovered
     *
     * @return self
     */
    public function setIsHardCovered($hardcovered)
    {
        $this->hardcovered = $hardcovered;

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
     * @return self
     */
    public function setFrontPage($frontPage = null)
    {
        $this->frontPage = $frontPage;

        return $this;
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
     * @param EntityManager $entityManager
     *
     * @return float
     */
    public function precalculateSellPrice(EntityManager $entityManager)
    {
        // Get purchase price in euros
        $purchasePrice = $this->precalculatePurchasePrice($entityManager);

        // Apply 3% markup
        $sellPrice = $purchasePrice * 1.03;

        // Round UP to the next 10 cents
        return ceil($sellPrice * 10) / 10;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return float
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
            if ($this->nbColored > 0) {
                $total += $prices['recto_verso_color'] * ($this->nbColored + $this->nbBlackAndWhite);
            } else {
                $total += $prices['recto_verso_bw'] * ($this->nbColored + $this->nbBlackAndWhite);
            }
        } else {
            if ($this->nbColored > 0) {
                $total += $prices['recto_color'] * ($this->nbColored + $this->nbBlackAndWhite);
            } else {
                $total += $prices['recto_bw'] * ($this->nbColored + $this->nbBlackAndWhite);
            }
        }

        if ($this->hardcovered) {
            $total += $prices['hardcover'];
        }

        return $total / 1000;
    }
}

<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Articles;

use CudiBundle\Entity\Articles\Options\Binding,
    CudiBundle\Entity\Articles\Options\Color;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\Internal")
 * @Table(name="cudi.articles_internal")
 */
class Internal extends \CudiBundle\Entity\Article
{
    /**
     * @var integer The number of black and white pages
     *
     * @Column(name="nb_black_and_white", type="smallint")
     */
    private $nbBlackAndWhite;

    /**
     * @var integer The number of colored pages
     *
     * @Column(name="nb_colored", type="smallint")
     */
    private $nbColored;

    /**
     * @var \CudiBundle\Entity\Articles\Options\Binding The binding of this article
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Articles\Options\Binding")
     * @JoinColumn(name="binding", referencedColumnName="id")
     */
    private $binding;

    /**
     * @var boolean Whether the aricle is an official one.
     *
     * @Column(type="boolean")
     */
    private $official;

    /**
     * @var boolean Flag whether the article is rectoverso or not.
     *
     * @Column(name="recto_verso", type="boolean")
     */
    private $rectoVerso;

    /**
     * @var \CudiBundle\Entity\Articles\Options\Color The color of the front page.
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Articles\Options\Color")
     * @JoinColumn(name="front_page_color", referencedColumnName="id")
     */
    private $frontPageColor;

    /**
     * @var boolean Whether the aricle is perforated or not.
     *
     * @Column(type="boolean")
     */
    private $isPerforated;

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
     * @param integer $nbBlackAndWhite The number of blach and white pages of the article
     * @param integer $nbColored The number of colored pages of the article
     * @param \CudiBundle\Entity\Articles\Options\Binding $binding The binding of the article
     * @param boolean $official Whether the article is an official one or not
     * @param boolean $rectoverso Whether the article is recto-verso or not
     * @param \CudiBundle\Entity\Articles\Options\Color $frontPageColor The front page color of the article
     * @param boolean $isPerforated Whether the article is perforated or not
     */
    public function __construct(
        $title, $authors, $publishers, $yearPublished, $isbn, $url = null, $type, $downloadable, $nbBlackAndWhite, $nbColored, Binding $binding, $official, $rectoverso, Color $frontPageColor = null, $isPerforated
    ) {
        parent::__construct($title, $authors, $publishers, $yearPublished, $isbn, $url, $type, $downloadable);

        $this->setNbBlackAndWhite($nbBlackAndWhite)
            ->setNbColored($nbColored)
            ->setBinding($binding)
            ->setIsOfficial($official)
            ->setIsRectoVerso($rectoverso)
            ->setFrontColor($frontPageColor)
            ->setIsPerforated($isPerforated);
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
     * @return \CudiBundle\Entity\Articles\Internal
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
     * @return \CudiBundle\Entity\Articles\Internal
     */
    public function setNbColored($nbColored)
    {
        $this->nbColored = $nbColored;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Articles\Options\Binding
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * @param \CudiBundle\Entity\Articles\Options\Binding $binding
     *
     * @return \CudiBundle\Entity\Articles\Internal
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
     * @return \CudiBundle\Entity\Articles\Internal
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
     * @return \CudiBundle\Entity\Articles\Internal
     */
    public function setIsRectoVerso($rectoVerso)
    {
        $this->rectoVerso = $rectoVerso;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Articles\Options\Color
     */
    public function getFrontColor()
    {
        return $this->frontPageColor;
    }

    /**
     * @param \CudiBundle\Entity\Articles\Options\Color $frontPageColor
     *
     * @return \CudiBundle\Entity\Articles\Internal
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
     * @return \CudiBundle\Entity\Articles\Internal
     */
    public function setIsPerforated($isPerforated)
    {
        $this->isPerforated = $isPerforated;
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
            $this->getNbBlackAndWhite(),
            $this->getNbColored(),
            $this->getBinding(),
            $this->isOfficial(),
            $this->isRectoVerso(),
            $this->getFrontColor(),
            $this->isPerforated()
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
}

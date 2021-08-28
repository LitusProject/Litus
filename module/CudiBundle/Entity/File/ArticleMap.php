<?php

namespace CudiBundle\Entity\File;

use CudiBundle\Entity\Article\Internal as InternalArticle;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\File\ArticleMap")
 * @ORM\Table(name="cudi_files_articles_map")
 */
class ArticleMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var InternalArticle The article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article\Internal")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var File The file of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\File\File")
     * @ORM\JoinColumn(name="file", referencedColumnName="id")
     */
    private $file;

    /**
     * @var boolean Flag whether the file is the printable one or not
     *
     * @ORM\Column(type="boolean")
     */
    private $printable;

    /**
     * @var boolean The flag whether the file is just created by a prof
     *
     * @ORM\Column(type="boolean")
     */
    private $isProf;

    /**
     * @var boolean The flag whether the file is removed
     *
     * @ORM\Column(type="boolean")
     */
    private $removed;

    /**
     * @param InternalArticle $article   The article of the mapping
     * @param File            $file      The file of the mapping
     * @param boolean         $printable Flag whether the file is the printable one or not
     */
    public function __construct(InternalArticle $article, File $file, $printable)
    {
        $this->article = $article;
        $this->file = $file;
        $this->setPrintable($printable)
            ->setIsProf(false);
        $this->removed = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return InternalArticle
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return boolean
     */
    public function isPrintable()
    {
        return $this->printable;
    }

    /**
     * @param boolean $printable Flag whether the file is the printable one or not
     *
     * @return self
     */
    public function setPrintable($printable)
    {
        $this->printable = $printable;

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
     * @return self
     */
    public function setIsProf($isProf)
    {
        $this->isProf = $isProf;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @return self
     */
    public function remove()
    {
        $this->removed = true;

        return $this;
    }
}

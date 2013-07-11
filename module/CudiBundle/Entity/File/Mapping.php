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

namespace CudiBundle\Entity\File;

use CudiBundle\Entity\Article\Internal as InternalArticle,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\File\Mapping")
 * @ORM\Table(name="cudi.files_articles_map")
 */
class Mapping
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
     * @var \CudiBundle\Entity\Article\Internal The article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article\Internal")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var \CudiBundle\Entity\File\File The file of the mapping
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
     * @param \CudiBundle\Entity\Article\Internal $article The article of the mapping
     * @param \CudiBundle\Entity\File\File $file The file of the mapping
     * @param boolean $printable Flag whether the file is the printable one or not
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
     * @return \CudiBundle\Entity\Article\Internal
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return \CudiBundle\Entity\File\File
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
     * @return \CudiBundle\Entity\File\Mapping
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
     * @return \CudiBundle\Entity\File\Mapping
     */
    public function setIsProf($isProf)
    {
        $this->isProf = $isProf;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\File\Mapping
     */
    public function setRemoved()
    {
        $this->removed = true;
        return $this;
    }
}

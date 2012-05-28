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
 
namespace CudiBundle\Entity\Files;

use CudiBundle\Entity\Articles\Internal as InternalArticle;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Files\Mapping")
 * @Table(name="cudi.files_mapping")
 */
class Mapping
{
    /**
     * @var integer The ID of the mapping
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

    /**
     * @var \CudiBundle\Entity\Articles\Internal The article of the mapping
     *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Articles\Internal")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;

	/**
	 * @var \CudiBundle\Entity\Files\File The file of the mapping
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Files\File")
	 * @JoinColumn(name="file", referencedColumnName="id")
	 */
	private $file;

    /**
     * @var boolean Flag whether the file is the printable one or not
     *
     * @Column(type="boolean")
     */
    private $printable;
    
    /**
     * @var boolean The flag whether the file is just created by a prof
     *
     * @Column(type="boolean")
     */
    private $isProf;
    
    /**
     * @param \CudiBundle\Entity\Articles\Internal $article The article of the mapping
     * @param \CudiBundle\Entity\Files\File $file The file of the mapping
     * @param boolean $printable Flag whether the file is the printable one or not
     */
    public function __construct(InternalArticle $article, File $file, $printable)
    {
        $this->article = $article;
        $this->file = $file;
        $this->setPrintable($printable)
            ->setIsProf(false);
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \CudiBundle\Entity\Articles\Internal
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    /**
     * @return \CudiBundle\Entity\Files\File
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
     * @return \CudiBundle\Entity\Files\Mapping
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
     * @return \CudiBundle\Entity\Files\Mapping
     */
    public function setIsProf($isProf)
    {
        $this->isProf = $isProf;
        return $this;
    }
}

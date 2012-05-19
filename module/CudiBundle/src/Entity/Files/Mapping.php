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

use \CudiBundle\Entity\Articles\Internal;

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
     * @param \CudiBundle\Entity\Articles\Internal $article
     * @param \CudiBundle\Entity\Files\File $file
     * @param boolean $printable
     */
    public function __construct(Article $article, File $file, $printable)
    {
        $this->article = $article;
        $this->file = $file;
        $this->setPrintable($printable);
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
    public function printable()
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
    	$this->removed = $removed;
    	return $this;
    }
}

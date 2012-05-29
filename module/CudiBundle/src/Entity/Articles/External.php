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

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\External")
 * @Table(name="cudi.articles_external")
 */
class External extends \CudiBundle\Entity\Article
{
    /**
     * @return \CudiBundle\Entity\Article
     */
    public function duplicate()
    {
        return new External(
            $this->getTitle(),
            $this->getAuthors(),
            $this->getPublishers(),
            $this->getYearPublished(),
            $this->getISBN(),
            $this->getURL()
        );
    }
    
    /**
     * @return boolean
     */
    public function isStub()
    {
        return false;
    }
    
    /**
     * @return boolean
     */
    public function isStock()
    {
        return true;
    }
    
    /**
     * @return boolean
     */
    public function isExternal()
    {
        return true;
    }
    
    /**
     * @return boolean
     */
    public function isInternal()
    {
        return false;
    }
}
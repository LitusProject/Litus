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

namespace CudiBundle\Entity\Articles;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Articles\External")
 * @ORM\Table(name="cudi.articles_external")
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
            $this->getURL(),
            $this->getType(),
            $this->isDownloadable(),
            $this->isSameAsPreviousYear()
        );
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

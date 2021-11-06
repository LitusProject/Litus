<?php

namespace CudiBundle\Entity\Article;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Article\External")
 * @ORM\Table(name="cudi_articles_external")
 */
class External extends \CudiBundle\Entity\Article
{
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

<?php

namespace CudiBundle\Entity\Log\Sale;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Sale\ProfVersion")
 * @ORM\Table(name="cudi_log_sales_prof_versions")
 */
class ProfVersion extends \CudiBundle\Entity\Log
{
    /**
     * @param Person  $person
     * @param Article $article
     */
    public function __construct(Person $person, Article $article)
    {
        parent::__construct($person, $article->getId());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'prof version';
    }
}

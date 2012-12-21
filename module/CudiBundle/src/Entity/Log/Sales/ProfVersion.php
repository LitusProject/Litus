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

namespace CudiBundle\Entity\Log\Sales;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Sales\Article,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Sales\ProfVersion")
 * @ORM\Table(name="cudi.log_sales_prof_version")
 */
class ProfVersion extends \CudiBundle\Entity\Log
{
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \CudiBundle\Entity\Sales\Article $article
     */
    public function __construct(Person $person, Article $article)
    {
        parent::__construct($person, $article->getId());
    }
}

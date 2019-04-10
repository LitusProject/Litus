<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sale\Article\Restriction;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Restriction\Available")
 * @ORM\Table(name="cudi_sales_articles_restrictions_available")
 */
class Available extends \CudiBundle\Entity\Sale\Article\Restriction
{
    /**
     * @param Article $article The article of the restriction
     * @param integer $value   The value of the restriction
     */
    public function __construct(Article $article)
    {
        parent::__construct($article);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'available';
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return 0;
    }

    /**
     * @param Person        $person
     * @param EntityManager $entityManager
     *
     * @return boolean
     */
    public function canBook(Person $person, EntityManager $entityManager)
    {
        $currentPeriod = $entityManager->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        $currentPeriod->setEntityManager($entityManager);

        $available = $this->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($this->getArticle());

        return $available > 0;
    }
}

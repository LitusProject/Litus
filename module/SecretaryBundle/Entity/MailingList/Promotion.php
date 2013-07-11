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

namespace SecretaryBundle\Entity\MailingList;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    SecretaryBundle\Entity\Promotion as PromotionEntity;

/**
 * This is the entity for a promotion list.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\MailingList\Promotion")
 * @ORM\Table(name="mail.lists_promotion")
 */
class Promotion extends \MailBundle\Entity\MailingList
{

    /**
     * @var \SecretaryBundle\Entity\Promotion The promotion of this list
     *
     * @ORM\OneToOne(targetEntity="SecretaryBundle\Entity\Promotion")
     * @ORM\JoinColumn(name="promotion", referencedColumnName="id")
     */
    private $promotion;

    /**
     * Creates a new list for the given promotion year.
     *
     * @param $promotion \SecretaryBundle\Entity\Promotion The promotion year
     */
    public function __construct(PromotionEntity $promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return \SecretaryBundle\Entity\Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'prom_' . $this->getPromotion()->getAcademicYear()->getCode(true);
    }
}

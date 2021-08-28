<?php

namespace PublicationBundle\Entity\Edition;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Edition\Pdf")
 * @ORM\Table(name="publications_editions_pdf")
 */
class Pdf extends \PublicationBundle\Entity\Edition
{
}

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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Log\Sale;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Sale\Cancellations")
 * @ORM\Table(name="cudi.log_sales_cancellations")
 */
class Cancellations extends \CudiBundle\Entity\Log
{
    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param array                            $cancellations
     */
    public function __construct(Person $person, $cancellations)
    {
        parent::__construct($person, serialize($cancellations));
    }

    /**
     * @return array
     */
    public function getCancellations()
    {
        return unserialize($this->getText());
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return count(unserialize($this->getText()));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'cancellations';
    }
}

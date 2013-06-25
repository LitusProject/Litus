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

namespace CudiBundle\Entity\Log\Sales;

use CommonBundle\Entity\Users\Person,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Sales\Assignments")
 * @ORM\Table(name="cudi.log_sales_assignments")
 */
class Assignments extends \CudiBundle\Entity\Log
{
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param array $assignments
     */
    public function __construct(Person $person, $assignments)
    {
        parent::__construct($person, serialize($assignments));
    }

    /**
     * @return array
     */
    public function getAssigments()
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
}

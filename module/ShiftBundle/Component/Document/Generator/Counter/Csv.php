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

namespace ShiftBundle\Component\Document\Generator\Counter;

use Doctrine\ORM\EntityManager;

/**
 * Csv
 *
 */
class Csv extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param array $volunteers
     */
    public function __construct($volunteers)
    {
        $headers = array('First Name', 'Last Name');

        $result = array();
        foreach ($volunteers as $volunteer) {
            $result[] = array(
                $volunteer['firstName'],
                $volunteer['lastName'],
            );
        }

        $result[] = array(' ');

        parent::__construct($headers, $result);
    }
}

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

namespace SportBundle\Component\Validator;

/**
 * Verifies whether the given value is in a valid university identification format.
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class UniversityIdentification extends \Zend\Validator\Regex
{
    public function __construct()
    {
        parent::__construct('/[rsu][0-9]{7}$/');

        $this->setMessage(
            'The given university identification is not in the correct format of an r-, s-, or u-number'
        );
    }
}

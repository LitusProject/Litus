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

namespace CommonBundle\Component\Hydrator\NamingStrategy;

/**
 * Make method names like <i>is</i>Something() result in 'something' rather than 'is_something'.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class RemoveIs extends \Zend\Stdlib\Hydrator\NamingStrategy\UnderscoreNamingStrategy
{
    public function extract($name)
    {
        $extracted = parent::extract($name);

        if (0 !== strpos($extracted, 'is_')) {
            return $extracted;
        }

        return substr($extracted, 3);
    }
}

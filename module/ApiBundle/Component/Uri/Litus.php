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

namespace ApiBundle\Component\Uri;

/**
 * Litus URI handler.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Litus extends \Zend\Uri\Uri
{
    protected static $validSchemes = array('litus');

    /**
     * User Info part is not used in Litus URIs.
     *
     * @see    Uri::setUserInfo()
     * @param  string                         $userInfo
     * @return \ApiBundle\Component\Uri\Litus
     */
    public function setUserInfo($userInfo)
    {
        return $this;
    }

    /**
     * Fragment part is not used in Litus URIs.
     *
     * @see    Uri::setFragment()
     * @param  string                         $fragment
     * @return \ApiBundle\Component\Uri\Litus
     */
    public function setFragment($fragment)
    {
        return $this;
    }
}

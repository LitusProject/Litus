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

namespace CommonBundle\Component\Controller;

use CommonBundle\Entity\Users\Person;

/**
 * Interface that specifies controller authentication methods.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
interface AuthenticationAware
{
    /**
     * Returns the Authentication instance.
     *
     * @return \CommonBundle\Component\Authentication\Authentication
     */
    public function getAuthentication();

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler();
}

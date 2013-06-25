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

namespace CommonBundle\Component\Authentication;

/**
 * Abstract class extending the basic Zend authentication to make sure a
 * person object can always be requested.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Result extends \Zend\Authentication\Result
{
    /**
     * Return the user object.
     *
     * @return \CommonBundle\Entity\Users\Person
     */
    public abstract function getPersonObject();

    /**
     * Return the session object.
     *
     * @return \CommonBundle\Entity\Users\Session
     */
    public abstract function getSessionObject();
}

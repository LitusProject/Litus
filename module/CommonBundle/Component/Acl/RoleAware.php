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

namespace CommonBundle\Component\Acl;

/**
 * All entities that support roles should implement this.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
interface RoleAware
{
    /**
     * Return an array with all the entity's roles.
     *
     * @return array
     */
    public function getRoles();
}

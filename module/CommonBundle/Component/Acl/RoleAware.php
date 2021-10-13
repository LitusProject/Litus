<?php

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

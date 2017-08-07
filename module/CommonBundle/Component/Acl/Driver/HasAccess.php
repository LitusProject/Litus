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

namespace CommonBundle\Component\Acl\Driver;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Acl\RoleAware;

/**
 * A view helper that allows us to easily verify whether or not the authenticated user
 * has access to a resource. This is particularly useful for creating navigation items.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class HasAccess
{
    /**
     * @var Acl The ACL object
     */
    private $acl = null;

    /**
     * @var RoleAware The authentication entity
     */
    private $entity = null;

    /**
     * @var boolean Whether the person is authenticated
     */
    private $authenticated = false;

    /**
     * @param Acl            $acl           The ACL object
     * @param boolean        $authenticated Whether the person is authenticated
     * @param RoleAware|null $entity        The authenticated entity
     */
    public function __construct(Acl $acl, $authenticated, RoleAware $entity = null)
    {
        $this->acl = $acl;
        $this->authenticated = $authenticated;
        $this->entity = $entity;
    }

    /**
     * @param  string       $resource The resource that should be verified
     * @param  string       $action   The module that should be verified
     * @return boolean|null
     */
    public function __invoke($resource, $action)
    {
        if (null === $this->acl) {
            throw new Exception\RuntimeException('No ACL object was provided');
        }

        if ($this->authenticated && null === $this->entity) {
            throw new Exception\RuntimeException('No entity was provided');
        }

        // Making it easier to develop new actions and controllers, without all the ACL hassle
        if ('development' == getenv('APPLICATION_ENV')) {
            return true;
        }

        if (!$this->acl->hasResource($resource)) {
            return false;
        }

        if ($this->authenticated && null !== $this->entity) {
            foreach ($this->entity->getRoles() as $role) {
                if (
                    $role->isAllowed(
                        $this->acl, $resource, $action
                    )
                ) {
                    return true;
                }
            }

            return false;
        } else {
            return $this->acl->isAllowed(
                'guest', $resource, $action
            );
        }
    }
}

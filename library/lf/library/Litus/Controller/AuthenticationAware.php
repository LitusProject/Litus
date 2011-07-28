<?php

namespace Litus\Controller;

use \Litus\Entity\Users\Person;

interface AuthenticationAware
{
    /**
     * Implementing the Singleton pattern for the Authentication object.
     *
     * @return \Litus\Authentication\Authentication
     */
    public function getAuthentication();

    /**
     * This method verifies whether or not a given user is allowed access to the
     * resource.
     *
     * @return bool
     */
    public function hasAccess();
}
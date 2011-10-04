<?php

namespace Litus\View\Helper;

use \Litus\Acl\Acl;
use \Litus\Authentication\Adapter\Doctrine as DoctrineAdapter;
use \Litus\Authentication\Authentication;
use \Litus\Authentication\Service\Doctrine as DoctrineService;

class HasAccess extends \Zend\View\Helper\AbstractHelper
{

    public function __invoke($module, $controller, $action)
    {
        // Making it easier to develop new actions and controllers, without all the ACL hassle
        if ('development' == getenv('APPLICATION_ENV'))
            return true;

        $acl = new Acl();
        $authentication = new Authentication(
            new DoctrineAdapter('Litus\Entity\Users\Person', 'username', 'can_login'),
            new DoctrineService('Litus\Entity\Users\Session', 2678400)
        );

        $authentication->authenticate();
        if ($authentication->isAuthenticated()) {
            foreach ($authentication->getPersonObject()->getRoles() as $role) {
                if (
                    $acl->getAcl()->isAllowed(
                        $role->getName(),
                        $module . '.' . $controller,
                        $action
                    )
                ) {
                    return true;
                }
            }

            return false;
        } else {
            return $acl->getAcl()->isAllowed(
                'guest',
                $module . '.' . $controller,
                $action
            );
        }
    }
}
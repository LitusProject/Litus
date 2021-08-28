<?php

namespace ApiBundle\Hydrator;

use ApiBundle\Entity\Key as KeyEntity;

/**
 * This hydrator hydrates/extracts API keys.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Key extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('host', 'check_host');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            do {
                $code = md5(uniqid(rand(), true));
                $found = $this->getEntityManager()
                    ->getRepository('ApiBundle\Entity\Key')
                    ->findOneByCode($code);
            } while (isset($found));

            $object = new KeyEntity($code);
        }

        $roles = array();
        $roles[] = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findOneByName('student');

        if (isset($data['roles'])) {
            foreach ($data['roles'] as $role) {
                if ($role == 'student') {
                    continue;
                }
                $roles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName($role);
            }
        }

        $object->setRoles($roles);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, array(self::$stdKeys, 'code'));

        $roles = array();
        foreach ($object->getRoles() as $role) {
            if ($role->getSystem()) {
                continue;
            }

            $roles[] = $role->getName();
        }
        $data['roles'] = $roles;

        return $data;
    }
}

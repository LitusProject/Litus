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
    private static $std_keys = array('host', 'check_host');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
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
                if ('student' == $role) continue;
                $roles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName($role);
            }
        }

        $object->setRoles($roles);

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, array(self::$std_keys, 'code'));

        $roles = array();
        foreach ($object->getRoles() as $role) {
            if ($role->getSystem())
                continue;

            $roles[] = $role->getName();
        }
        $data['roles'] = $roles;

        return $data;
    }
}

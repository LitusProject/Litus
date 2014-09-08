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

namespace CommonBundle\Hydrator\User;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

abstract class Person extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array(
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'sex',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, array(self::$std_keys, 'username'));

        $data['roles'] = $this->rolesToData($object->getRoles());

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create a person');
        }

        if (isset($data['roles'])) {
            $object->setRoles($this->dataToRoles($data['roles']));
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function dataToRoles($rolesData)
    {
        // TODO

        return array();
    }

    protected function rolesToData($roles)
    {
        // TODO

        return array();
    }
}

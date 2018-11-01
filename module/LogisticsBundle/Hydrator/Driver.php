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

namespace LogisticsBundle\Hydrator;

use LogisticsBundle\Entity\Driver as DriverEntity;

class Driver extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('color');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $years = array();
        foreach ($object->getYears() as $year) {
            $years[] = $year->getId();
        }
        $data['years'] = $years;

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneByPerson($data['person']['id']);

            if ($object === null) {
                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($data['person']['id']);

                $object = new DriverEntity($person);
            }
        }

        $object->setRemoved(false);

        $years = array();
        $repository = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear');
        foreach ($data['years'] as $year) {
            $years[] = $repository->findOneById($year);
        }
        $object->setYears($years);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

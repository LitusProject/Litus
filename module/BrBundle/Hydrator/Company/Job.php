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

namespace BrBundle\Hydrator\Company;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Job data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Job extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('name', 'description', 'benefits', 'profile', 'email', 'city');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a job');
        }

        $object->setSector($data['sector']);
        $object->setLocation($data['location']);
        $object->setMaster($data['master']);
        $object->updateDate();
        $object->setStartDate(self::loadDate($data['start_date']))
            ->setEndDate(self::loadDate($data['end_date']));

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        echo '<script>console.log(' . json_encode($data) . ')</script>';

        $data['sector'] = $object->getSectorCode();
        $data['location'] = $object->getLocationCode();
        $data['master'] = $object->getMasterCode();
        $data['start_date'] = $object->getStartDate()->format('d/m/Y');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y');

        return $data;
    }
}

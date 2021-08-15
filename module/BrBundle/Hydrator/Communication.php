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
namespace BrBundle\Hydrator;

use BrBundle\Entity\Communication as CommunicationEntity;
use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Communication data.
 *
 * @autho Stan Cardinaels <stan.cardinaels@vtk.be>
 */
class Communication extends \CommonBundle\Component\Hydrator\Hydrator {

    private static $stdKeys = array('option', 'audience');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new CommunicationEntity( $this->getPersonEntity());
        }

        if (isset($data['companyId'])) {
            $object->setCompany(
                $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($data['companyId'])
            );
        }

        if (isset($data['date'])) {
            $object->setDate(self::loadDate($data['date']));
        }


        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if (object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['company'] = $object->getCompany() !== null ? $object->getCompany()->getName() : -1;

        $data['date'] = $object->getDate()->format('d/m/Y H:i');

        return $data;
    }


}

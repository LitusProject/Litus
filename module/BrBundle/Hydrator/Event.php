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

use BrBundle\Entity\Event as EventEntity;

/**
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Event extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('title',
        'description_for_students',
        'description_for_companies',
        'view_information_nl',
        'view_information_en',        
//        'nb_companies',
//        'nb_students',
        'visible_for_companies',
        'visible_for_students',
        'location',
        'audience'
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $date = $object->getSubscriptionDate();
        if ($date != null){
            $data['subscription_date'] = $date->format('d/m/Y H:i');
        }
        $date = $object->getMapviewDate();
        if ($date != null){
            $data['mapview_date'] = $object->getMapviewDate()->format('d/m/Y H:i');
        }

        $data['nb_students'] = ($object->getNbStudents() ? $object->getNbStudents() : '');
        $data['nb_companies'] = ($object->getNbCompanies() ? $object->getNbCompanies() : '');
        $data['food'] = implode(',', $object->getFood());


        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new EventEntity($this->getPersonEntity());
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        if (isset($data['start_date'])) {
            $object->setStartDate(self::loadDateTime($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $object->setEndDate(self::loadDateTime($data['end_date']));
        }

        if (isset($data['subscription_date'])) {
            $object->setSubscriptionDate(self::loadDateTime($data['subscription_date']));
        }

        if (isset($data['mapview_date'])) {
            $object->setMapviewDate(self::loadDateTime($data['mapview_date']));
        }

        if(!($data['nb_students'] == "")) {
            $object->setNbStudents($data['nb_students']);
        }

        if(!($data['nb_companies'] == "")) {
            $object->setNbCompanies($data['nb_companies']);
        }

        if (!($data['food'] == "")) {
            $object->setFood(explode(',', $data['food']));
        }
        return $object;
    }
}

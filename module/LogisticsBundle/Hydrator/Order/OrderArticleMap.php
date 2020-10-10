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

namespace LogisticsBundle\Hydrator\Order;

use LogisticsBundle\Entity\Order\OrderArticleMap as MapEntity;

class OrderArticleMap extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('amount');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }


        $data = $this->stdExtract($object, self::$stdKeys);
        $data['status'] = $object->getStatusCode();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new MapEntity(
                $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order')
                ->findOneById($data['order']['id']),
                $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findOneById($data['article']['id']),
                $data['amount']);
        }

        $object->setStatus($data['status']);
        print_r($data);die();

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

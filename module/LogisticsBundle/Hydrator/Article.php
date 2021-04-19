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

use LogisticsBundle\Entity\Article as ArticleEntity;

class Article extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'additional_info', 'spot', 'amount_owned', 'amount_available', 'internal_comment');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['location'] = $object->getLocation()->getId();
        $data['warranty'] = $object->getWarranty() / 100;
        $data['rent'] = $object->getRent() / 100;
        $data['visibility'] = $object->getVisibilityCode();
        $data['status'] = $object->getStatusCode();
        $data['category'] = $object->getCategoryCode();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new ArticleEntity();
        }


        $object->setWarranty($data['warranty'] !== null ? $data['warranty'] * 100 : 0);
        $object->setRent($data['rent'] !== null ? $data['rent'] * 100 : 0);

        $object->setVisibility($data['visibility']);
        $object->setStatus($data['status']);
        $object->setCategory($data['category']);
        $object->setLocation(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Location')
                ->findOneById($data['location'])
        );

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

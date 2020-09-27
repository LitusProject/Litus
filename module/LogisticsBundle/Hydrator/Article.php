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
//    TODO: fix dit want dit ken ik nog niet
    private static $stdKeys = array('name', 'additional_info', 'spot', 'warranty', 'rent', 'amount_owned', 'visibility', 'status', 'category', 'location', );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new ArticleEntity();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

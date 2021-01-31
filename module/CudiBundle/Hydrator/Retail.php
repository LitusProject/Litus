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

namespace CudiBundle\Hydrator;

use CudiBundle\Entity\Retail as RetailEntity;

class Retail extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('comment', 'anonymous', );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['price'] = $object->getPrice();
        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        $maxRelPrice = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_maximal_relative_price');

        if ($object === null) {
            $article = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findOneByTitle($data['article']['value']);

            $owner = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['owner']['id']);

            $object = new RetailEntity($article, $owner);
        }

        $article = $object->getArticle();
        if ($article->getSaleArticle()) {
            if ($data['price'] > $maxRelPrice * $article->getSaleArticle()->getSellPrice()) {
                $object->setPrice($maxRelPrice * $article->getSaleArticle()->getSellPrice());
            } else {
                $object->setPrice($data['price']);
            }
        } else {
            $object->setPrice($data['price']);
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

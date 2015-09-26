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

namespace ShopBundle\Hydrator;

use ShopBundle\Entity\Product as ProductEntity;

/**
 * Class Product
 * @author Floris Kint <floris.kint@litus.cc>
 */

class Product extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'name',
        'available',
    );

    /**
	 * @param Product|null $object
	 * @return array
	 */
    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['sell_price'] = $object->getSellPrice();

        return $data;
    }

    /**
	 * @param array $data
	 * @param ProductEntity | $object
	 * @return ProductEntity
	 */
    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new ProductEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);
        $object->setSellPrice(floatval(str_replace(',','.', $data['sell_price'])));

        return $object;
    }
}

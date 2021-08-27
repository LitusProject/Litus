<?php

namespace ShopBundle\Hydrator;

use ShopBundle\Entity\Product as ProductEntity;

class Product extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'available');

    /**
     * @param  Product|null $object
     * @return array
     */
    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['sell_price'] = $object->getSellPrice();
        $data['name_en'] = $object->getName('en');

        return $data;
    }

    /**
     * @param  array              $data
     * @param  ProductEntity|null $object
     * @return ProductEntity
     */
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new ProductEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);
        $object->setSellPrice(floatval(str_replace(',', '.', $data['sell_price'])));
        $object->setNameEN($data['name_en']);

        return $object;
    }
}

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

namespace CudiBundle\Hydrator\Sale;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Article extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        static $stdKeys = array(
            'bookable', 'unbookable', 'sellable',
        );

        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, $stdKeys);

        $data['can_expire'] = $object->canExpire();

        $data['purchase_price'] = number_format($object->getPurchasePrice() / 100, 2);
        $data['sell_price'] = number_format($object->getSellPrice() / 100, 2);

        $data['barcode'] = $object->getBarcode() != '' ? str_pad($object->getBarcode(), 12, '0', STR_PAD_LEFT) : '';
        $data['supplier'] = $object->getSupplier()->getId();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        static $stdKeys = array(
            'sell_price', 'purchase_price', 'can_expire',
        );

        if ($object === null) {
            throw new InvalidObjectException('Cannot create a sale article');
        }

        $this->stdHydrate($data, $object, $stdKeys);

        $supplier = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findOneById($data['supplier']);

        $object->setIsBookable(isset($data['bookable']) && $data['bookable'])
            ->setIsUnbookable(isset($data['unbookable']) && $data['unbookable'])
            ->setIsSellable($data['sellable'])
            ->setSupplier($supplier);

        $barcodeCheck = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_sale_article_barcode_check');

        if ($data['barcode'] == '' && !$barcodeCheck) {
            $object->clearBarcode();
        } else {
            $object->setBarcode($data['barcode']);
        }

        return $object;
    }
}

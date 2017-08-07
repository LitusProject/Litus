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

namespace CudiBundle\Hydrator\Sale\Article\Discount;

use CudiBundle\Entity\Sale\Article\Discount\Template as TemplateEntity;

class Template extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'name', 'method', 'rounding', 'type',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['organization'] = null === $object->getOrganization()
            ? '0'
            : $object->getOrganization()->getId();
        $data['apply_once'] = $object->applyOnce();
        $data['value'] = number_format($object->getValue() / 100.0, 2);

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new TemplateEntity();
        }

        $organization = null;
        if (isset($data['organization']) && 0 != $data['organization']) {
            $organization = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOneById($data['organization']);
        }

        $object->setOrganization($organization);

        return $this->stdHydrate($data, $object, array(self::$stdKeys, array('apply_once', 'value')));
    }
}

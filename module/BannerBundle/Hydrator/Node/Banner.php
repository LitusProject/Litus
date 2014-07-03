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

namespace BannerBundle\Hydrator\Node;

use BannerBundle\Entity\Node\Banner as BannerEntity,
    InvalidArgumentException;

class Banner extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array(
        'name', 'start_date', 'end_date', 'active', 'url',
    );

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidArgumentException('The Banner hydrator doesn\'t support creating a new banner');
        }

        if (array_key_exists('start_date', $data)) {
            $data['start_date'] = DateTime::createFromFormat('d#m#Y H#i', $data['start_date']) ?: null;
        }

        if (array_key_exists('end_date', $data)) {
            $data['end_date'] = DateTime::createFromFormat('d#m#Y H#i', $data['end_date']) ?: null;
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        if (array_key_exists('start_date', $data) && !empty($data['start_date'])) {
            $data['start_date'] = $data['start_date']->format('d/m/Y H:i');
        }

        if (array_key_exists('end_date', $data) && !empty($data['end_date'])) {
            $data['end_date'] = $data['end_date']->format('d/m/Y H:i');
        }

        return $data;
    }
}

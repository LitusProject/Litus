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

namespace OnBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts slug data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Slug extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'OnBundle\Document\Slug';

    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $std_keys = array('name', 'url');

    protected function doHydrate(array $data, $object = null)
    {
        // SlugDocument requires the Person that created it, so
        // we cannot create an object here.
        if (null === $object)
            throw new InvalidObjectException();

        if ('' == $data['name']) {
            do {
                $name = $this->_createRandomName();
                $found = $this->getDocumentManager()
                    ->getRepository('OnBundle\Document\Slug')
                    ->findOneByName($name);
            } while (isset($found));
        } else {
            $name = strtolower($data['name']);
        }

        $object->setName(strtolower($name))
            ->setUrl($data['url']);

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        return $data;
    }

    private function _createRandomName()
    {
        $characters = 'abcdefghijklmnopqrstuwxyz0123456789';

        $name = array();
        for ($i = 0; $i < 8; $i++)
            $name[$i] = $characters[rand(0, strlen($characters) - 1)];

        return implode('', $name);
    }
}

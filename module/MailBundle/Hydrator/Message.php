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

namespace MailBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts message data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Message extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'MailBundle\Document\Message';

    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $std_keys = array('subject', 'body');

    protected function doHydrate(array $data, $object = null)
    {
        // SlugDocument requires the Person that created it, so
        // we cannot create an object here.
        if (null === $object)
            throw new InvalidObjectException();

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
}

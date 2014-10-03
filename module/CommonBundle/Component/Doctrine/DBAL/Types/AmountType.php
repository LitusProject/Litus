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

namespace CommonBundle\Component\Doctrine\DBAL\Types;

use CommonBundle\Component\Util\Types\Amount,
    Doctrine\DBAL\Platforms\AbstractPlatform;

class AmountType extends \Doctrine\DBAL\Types\IntegerType
{
    const AMOUNT = 'amount';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::AMOUNT;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $parentValue = parent::convertToPHPValue($value, $platform);

        return Amount::fromInteger($parentValue);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return parent::convertToDatabaseValue($value, $platform);
        } else {
            return parent::convertToDatabaseValue($value->asInteger(), $platform);
        }
    }
}

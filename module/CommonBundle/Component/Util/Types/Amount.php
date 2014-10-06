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

namespace CommonBundle\Component\Util\Types;

class Amount
{
    /**
     * @var int The amount times the multiplier
     */
    private $amount;

    private function __construct($amount)
    {
        $this->amount = $amount;
    }

    public function asFloat($multiplier = 1)
    {
        return $this->amount / $multiplier;
    }

    /**
     * Returns the value as integer.
     *
     * Returns the value times $multiplier, if the current value is stored times
     * $storedMultiplier.
     *
     * @param int      $multiplier
     * @param int|null $storedMultiplier
     *
     * @return int
     */
    public function asInteger($multiplier = 1, $storedMultiplier = null)
    {
        if ($storedMultiplier !== null && $multiplier === $storedMultiplier) {
            $multiplier /= $storedMultiplier;
        }

        return (int) ($this->amount * $multiplier);
    }

    public static function fromFloat($amount, $multiplier = 1)
    {
        $float = (int) str_replace(',', '.', $amount);

        return new Amount((int) ($float * $multiplier));
    }

    /**
     * Creates an amount for the given integer.
     *
     * The amount will represent the amount times $multiplier, where the amount
     * is given times $givenMultiplier as $amount.
     *
     * @param int      $amount
     * @param int      $multiplier
     * @param int|null $givenMultiplier
     *
     * @return self
     */
    public static function fromInteger($amount, $multiplier = 1, $givenMultiplier = null)
    {
        if ($givenMultiplier !== null && $multiplier !== $givenMultiplier) {
            $multiplier /= $givenMultiplier;
        }

        return new Amount((int) ($amount * $multiplier));
    }

    public static function zero()
    {
        return new Amount(0);
    }
}

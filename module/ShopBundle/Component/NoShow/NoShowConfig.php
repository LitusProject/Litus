<?php

namespace ShopBundle\Component\NoShow;

use CommonBundle\Entity\User\Person;
use Nette\NotImplementedException;

class NoShowConfig {
    /**
     * @var array Stores the email related to each warning
     */
    private array $emailDictionary;

    /**
     * @var array Stores the amount of ban days related to each warning
     */
    private array $banDaysDictionary;

    public function __construct($configJson) {
        $decodedConfig = json_decode($configJson, true);

        $this->emailDictionary = [];
        $this->banDaysDictionary = [];

        foreach ($decodedConfig['warnings'] as $index => $warning) {
            $this->emailDictionary[$index] = $warning['email_message'];
            $this->banDaysDictionary[$index] = $warning['ban_days'];
        }
    }

    public function getBanInterval(int $banCount) {
        throw new NotImplementedException();
    }

    public function getEmail(Person $person, int $banCount) {
        throw new NotImplementedException();
    }
}

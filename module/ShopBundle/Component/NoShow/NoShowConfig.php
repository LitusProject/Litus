<?php

namespace ShopBundle\Component\NoShow;

use CommonBundle\Entity\User\Person;

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

    /**
     * @return array Returns a dictionary with the emails for each warning
     */
    public function getEmailDictionary() {
        return $this->emailDictionary;
    }

    /**
     * @return array Returns a dictionary with the amount of ban days for each warning
     */
    public function getBanDaysDictionary() {
        return $this->banDaysDictionary;
    }

    public function createBan(Person $person, int $warningCount) {

    }
}

<?php

namespace ShopBundle\Component\NoShow;

use CommonBundle\Entity\User\Person;

/**
 * This class is used to interpret the configuration value that defines the configuration of the no-show system.
 * The no-show system is used to notify users and create bans when users reserve items and don't pick them up.
 */
class NoShowConfig extends \CommonBundle\Component\Controller\ActionController\AdminController
{

    /**
     * @var array Stores the email related to each warning
     */
    private array $emailDictionary;

    /**
     * @var array Stores the amount of ban days related to each warning
     */
    private array $banDaysDictionary;

    public function __construct($configData)
    {
        $this->emailDictionary = array();
        $this->banDaysDictionary = array();

        foreach ($configData as $index => $data) {
            $index = (int)$index; // This conversion may not be necessary

            $this->emailDictionary[$index]['subject'] = $data['mail_subject'];
            $this->emailDictionary[$index]['content'] = $data['mail_content'];
            $this->banDaysDictionary[$index] = $data['ban_days'];
        }
    }

    /**
     * Returns the amount of days of ban for the warningCount.
     *
     * @param integer $warningCount
     * @return mixed
     */
    public function getBanInterval(int $warningCount)
    {
        if ($warningCount >= count($this->banDaysDictionary)) {
            $warningCount = count($this->banDaysDictionary) - 1;
        }
        return $this->banDaysDictionary[$warningCount];
    }

    /**
     * Returns the warning email content for the warningCount.
     *
     * @param Person  $person
     * @param integer $warningCount
     * @return array|string|string[]
     */
    public function getEmailContent(Person $person, int $warningCount)
    {
        if ($warningCount >= count($this->banDaysDictionary)) {
            $warningCount = count($this->banDaysDictionary) - 1;
        }

        $mailContent = $this->emailDictionary[$warningCount]['content'];

        $name = $person->getFirstName();
        $mailContent = str_replace('{{ name }}', $name, $mailContent);

        return $mailContent;
    }

    /**
     * Returns the warning email subject for the warningCount.
     *
     * @param integer $warningCount
     * @return mixed
     */
    public function getEmailSubject(int $warningCount)
    {
        if ($warningCount >= count($this->banDaysDictionary)) {
            $warningCount = count($this->banDaysDictionary) - 1;
        }

        return $this->emailDictionary[$warningCount]['subject'];
    }
}
